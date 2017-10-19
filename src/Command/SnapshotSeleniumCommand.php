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
   * @inheritdoc
   */
  protected function configure() {
    $this->setName('snapshots:selenium')
      ->addOption('domain', NULL, InputOption::VALUE_REQUIRED, 'The domain to take the snapshots from.')
      ->addOption('target', NULL, InputOption::VALUE_REQUIRED, 'The type of snapshots to make (either baseline or new).', 'new')
      ->addOption('output', NULL, InputOption::VALUE_REQUIRED, 'The base location for the generated snapshots.', getcwd() . '/haunt/snapshots')
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

    $domain = rtrim($input->getOption('domain'), '/');
    $this->setDomain($domain);

  }

  /**
   * @inheritdoc
   */
  public function execute(InputInterface $input, OutputInterface $output) {

    $info = [
      'name' => 'testing-123',
      'sizes' => [
        'mobile' => [
          'width' => 600,
          'height' => 1200,
        ]
      ],
      'paths' => [
        '/'
      ]
    ];

    $fullPaths = $info['paths'];
    $domain = $this->getDomain();

    array_walk($fullPaths, function (&$path) use ($domain) {
      $path = $domain . '/' . ltrim($path, '/');
    });

    /** @var Snapshot[] $snapshotSets */
    $snapshotSets = [];

    if (isset($info['sizes'])) {
      foreach ($info['sizes'] as $variant => $resolution) {
        $outputDir = getcwd() . '/testing/' . $info['name'] . '--' . $variant;
        $snapshotSets[] = new Snapshot($fullPaths, 'new.png', $outputDir, $output, $resolution);
      }
    }
    else {
      $outputDir = getcwd() . '/testing/' . $info['name'];
      $snapshotSets[] = new Snapshot($fullPaths, 'new.png', $outputDir, $output);
    }

    foreach ($snapshotSets as $snapshotSet) {
      $snapshotSet->snap();
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
    $this->domain = $domain;
  }
}