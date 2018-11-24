<?php
/**
 * @file Contains the command that makes all the actual comparisons.
 */

namespace surangapg\Haunt\Command;

use surangapg\Haunt\Generator\SnapshotGenerator;
use surangapg\Haunt\Manifest\YamlFileManifest;
use surangapg\Haunt\Output\Structure\DefaultFolderOutputStructure;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SnapshotSeleniumCommand extends Command {

  /**
   * The base url for the domain to check.
   *
   * @var string
   *   The domain to check.
   */
  protected $domain;

  /**
   * The directory location for the output.
   *
   * @var string
   *   Absolute path for the output dir.
   */
  protected $outputDir;

  /**
   * Configuration for the snapshot run.
   *
   * @var string
   *   The location for the manifest file.
   */
  protected $manifest;

  /**
   * Browser to use.
   *
   * @var string
   *   The name for the browser to use.
   */
  protected $browser = 'firefox';

  /**
   * The name for this dataset.
   *
   * @var string
   *   The name for this comparison.
   */
  protected $metaName;

  /**
   * @inheritdoc
   */
  protected function configure() {
    $this->setName('snapshots:selenium')
      ->addOption('manifest', NULL, InputOption::VALUE_REQUIRED, 'The manifest file to use.')
      ->addOption('domain', NULL, InputOption::VALUE_REQUIRED, 'The domain to take the snapshots from.')
      ->addOption('meta-name', NULL, InputOption::VALUE_REQUIRED, 'The name for this comparison.')
      ->addOption('output-dir', NULL, InputOption::VALUE_REQUIRED, 'The base location for the generated snapshots.', getcwd() . '/haunt/new_snapshots')
      ->addOption('browser', NULL, InputOption::VALUE_REQUIRED, 'The browser to use for the snapshots' ,'firefox')
      ->setDescription('Use a selenium browser to produce a set of snapshots based on a yml config file.');
  }

  /**
   * @inheritdoc
   */
  public function initialize(InputInterface $input, OutputInterface $output) {
    parent::initialize($input, $output);

    if (!$this->isBrowserDriverActive()) {
      throw new \Exception("Selenium driver is not active. Try are you sure it was installed and is running properly? Run 'selenium-server -p 4444' if installed via brew. This is best done in a different terminal window since it's a java application that runs in the background.");
    }

    $domain = $input->getOption('domain');
    if (!isset($domain)) {
      throw new \Exception('Domain option is required.');
    }

    $outputDir = $input->getOption('output-dir');
    $browser = $input->getOption('browser');
    $manifestFile = $input->getOption('manifest');
    $metaName =  $input->getOption('meta-name');

    $this->setManifest($manifestFile);
    $outputDir = rtrim($outputDir, '/');

    $this->setMetaName($metaName);
    $this->setOutputDir($outputDir);
    $this->setDomain($domain);
    $this->setBrowser($browser);
  }

  /**
   * @inheritdoc
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    // Generate a manifest.
    $manifest = new YamlFileManifest(['file' => $this->getManifest()]);
    $outputStructure = new DefaultFolderOutputStructure($this->getOutputDir() . '/' . $this->metaName);

    $metaInfo = [
      'name' => $this->metaName,
      'timeStamp' => time(),
      'time' => date('d/m/Y - H:i'),
    ];

    $snapshotGenerator = new SnapshotGenerator($manifest, $outputStructure, $output);

    $snapshotGenerator->generate($this->getDomain(), $metaInfo);
  }

  /**
   * @return bool
   */
  protected function isBrowserDriverActive() {
    // From http://stackoverflow.com/questions/3657803/how-to-check-whether-selenium-server-is-running
    // we might want to change this to a cleaner application via curl request to http://localhost:4444/wd/hub/status
    $fp = @fsockopen('localhost', 4444);
    $active = ($fp !== false);

    if ($active) {
      fclose($fp);
    }

    return $active;
  }

  /**
   * @param $session
   */
  public function setSession($session) {
    $this->session = $session;
  }

  /**
   * @return string
   */
  public function getDomain() {
    return $this->domain;
  }

  /**
   * @param string $domain
   */
  public function setDomain(string $domain) {
    $this->domain = rtrim($domain, '/');
  }

  /**
   * @return string
   */
  public function getOutputDir() {
    return $this->outputDir;
  }

  /**
   * @return string
   */
  public function getMetaName() {
    return $this->metaName;
  }

  /**
   * @param string $outputDir
   */
  public function setOutputDir(string $outputDir) {
    $this->outputDir = rtrim($outputDir, '/') . '/';
  }

  /**
   * @return string
   */
  public function getManifest() {
    return $this->manifest;
  }

  /**
   * @param string $manifest
   */
  public function setManifest(string $manifest) {
    $this->manifest = $manifest;
  }

  /**
   * @param string $metaName
   */
  public function setMetaName(string $metaName) {
    $this->metaName = $metaName;
  }


  /**
   * @return string
   */
  public function getBrowser() {
    return $this->browser;
  }

  /**
   * @param $browser
   */
  public function setBrowser($browser) {
    $this->browser = $browser;
  }
}