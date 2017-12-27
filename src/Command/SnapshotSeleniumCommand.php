<?php
/**
 * @file Contains the command that makes all the actual comparisons.
 */

namespace surangapg\Haunt\Command;

use Behat\Mink\Session;
use surangapg\Haunt\Component\Snapshot;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class SnapshotSeleniumCommand extends Command {

  /**
   * @var Session
   */
  protected $session;

  /**
   * @var string
   */
  protected $domain;

  /**
   * @var string
   */
  protected $targetFile;

  /**
   * @var string
   */
  protected $outputDir;

  /**
   * Configuration for the snapshot run.
   *
   * @var array
   */
  protected $config = [];

  /**
   * Browser to use.
   *
   * @var string
   */
  protected $browser = 'firefox';

  /**
   * @inheritdoc
   */
  protected function configure() {
    $this->setName('snapshots:selenium')
      ->addOption('config', NULL, InputOption::VALUE_REQUIRED, 'The configuration file to use.')
      ->addOption('domain', NULL, InputOption::VALUE_REQUIRED, 'The domain to take the snapshots from.')
      ->addOption('target', NULL, InputOption::VALUE_REQUIRED, 'The type of snapshots to make (either baseline or new).', 'new')
      ->addOption('output-dir', NULL, InputOption::VALUE_REQUIRED, 'The base location for the generated snapshots.', getcwd() . '/haunt/snapshots')
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

    $target = $input->getOption('target');
    if (!isset($target)) {
      throw new \Exception('Target option is required.');
    }

    $outputDir = $input->getOption('output-dir');
    $browser = $input->getOption('browser');
    $configFile = $input->getOption('config');

    // Add loading of the file here.
    $config = Yaml::parse(file_get_contents($configFile));

    if (!isset($config['name'])) {
      $config['name'] = base64_encode($configFile);
    }

    if (!isset($config['paths'])) {
      throw new \Exception('No "paths" key found in the configuration file. This should be a set of key/value pairs name => path');
    }

    $this->setConfig($config);
    $this->setOutputDir($outputDir);
    $this->setTargetFile($target . '.png');
    $this->setDomain($domain);
    $this->setBrowser($browser);
  }

  /**
   * @inheritdoc
   */
  public function execute(InputInterface $input, OutputInterface $output) {

    $info = $this->getConfig();

    $fullPaths = $info['paths'];
    $domain = $this->getDomain();

    array_walk($fullPaths, function (&$path) use ($domain) {
      $path = $domain . '/' . ltrim($path, '/');
    });

    /** @var Snapshot[] $snapshotSets */
    $snapshotSets = [];

    if (isset($info['sizes'])) {
      foreach ($info['sizes'] as $variant => $resolution) {
        $groupInfo = [
          'id' => $info['name'] . '--' . $variant,
        ];
        $outputDir = $this->getOutputDir() . $info['name'] . '--' . $variant;
        $snapshotSets[] = new Snapshot($fullPaths, $this->getTargetFile(), $outputDir, $output, $groupInfo, $resolution);
      }
    }
    else {
      $outputDir = $this->getOutputDir() . $info['name'];
      $groupInfo = [
        'id' => $info['name'],
      ];
      $snapshotSets[] = new Snapshot($fullPaths, $this->getTargetFile(), $outputDir, $output, $groupInfo);
    }

    foreach ($snapshotSets as $snapshotSet) {
      $snapshotSet->snap($this->getBrowser());
    }
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
   * @return Session
   */
  public function getSession() {
    return $this->session;
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
  public function getTargetFile() {
    return $this->targetFile;
  }

  /**
   * @param string $targetFile
   */
  public function setTargetFile(string $targetFile) {
    $this->targetFile = $targetFile;
  }

  /**
   * @return string
   */
  public function getOutputDir() {
    return $this->outputDir;
  }

  /**
   * @param string $outputDir
   */
  public function setOutputDir(string $outputDir) {
    $this->outputDir = rtrim($outputDir, '/') . '/';
  }

  /**
   * @return array
   */
  public function getConfig() {
    return $this->config;
  }

  /**
   * @param array $config
   */
  public function setConfig(array $config) {
    $this->config = $config;
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