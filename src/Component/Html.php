<?php

/**
 * @file Component that writes out the report to a static html item.
 */
namespace surangapg\Haunt\Component;

use surangapg\Haunt\Output\HtmlGeneratorInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class Html
 *
 * @TODO Make the passing of the report data less primitive.
 *
 * @package surangapg\Haunt\Component
 */
class Html {

  /**
   * @var array
   */
  protected $reportData = [];

  /**
   * @var HtmlGeneratorInterface
   */
  protected $outputGenerator;

  /**
   * @var OutputInterface
   */
  protected $output;

  /**
   * Html constructor.
   *
   * @param array $reportData
   * @param \surangapg\Haunt\Output\HtmlGeneratorInterface $outputGenerator
   * @param \Symfony\Component\Console\Output\OutputInterface|NULL $output
   */
  public function __construct(array $reportData, HtmlGeneratorInterface $outputGenerator, OutputInterface $output = NULL) {
    $this->reportData = $reportData;
    $this->outputGenerator = $outputGenerator;

    if (empty($output)) {
      $output = new BufferedOutput();
    }
    $this->output = $output;
  }

  /**
   * Start spitting out html.
   *
   * @param string $outputDir
   */
  public function writeHtml(string $outputDir) {

    $outputDir = rtrim($outputDir, '/') . '/';
    $fs = new Filesystem();

    $this->output->writeln(sprintf('Generating output in %s', $outputDir));

    // Clean up the report data by adding a relative path between the output dir
    // and the folder dir since html doesn't accept absolute file paths.
    foreach ($this->reportData['records'] as &$group) {
      foreach ($group['paths'] as &$pathInfo) {
        $pathInfo['relativePath'] = $this->findRelativePath($outputDir, $pathInfo['folder']);
      }
    }

    // @TODO Yield this?
    foreach ($this->outputGenerator->generateHtml($this->reportData, $outputDir) as $location => $data) {
      $this->output->writeln(sprintf('  Writing to %s', $location . '.html'));
      $fs->dumpFile($outputDir . $location . '.html', $data);
    }
  }

  /**
   * Find the relative file system path between two file system paths.
   *
   * Pro verbatim copy from: https://gist.github.com/ohaal/2936041
   *
   * @param  string  $frompath  Path to start from
   * @param  string  $topath    Path we want to end up in
   *
   * @return string             Path leading from $frompath to $topath
   */
  protected function findRelativePath ($frompath, $topath) {
    $from = explode( DIRECTORY_SEPARATOR, $frompath ); // Folders/File
    $to = explode( DIRECTORY_SEPARATOR, $topath ); // Folders/File
    $relpath = '';

    $i = 0;
    // Find how far the path is the same
    while ( isset($from[$i]) && isset($to[$i]) ) {
      if ( $from[$i] != $to[$i] ) break;
      $i++;
    }
    $j = count( $from ) - 1;
    // Add '..' until the path is the same
    while ( $i <= $j ) {
      if ( !empty($from[$j]) ) $relpath .= '..'.DIRECTORY_SEPARATOR;
      $j--;
    }
    // Go to folder from where it starts differing
    // We make an extra allowance here for folders named "0".
    while ( isset($to[$i]) ) {
      if ( !empty($to[$i]) || $to[$i] == 0 ) $relpath .= $to[$i].DIRECTORY_SEPARATOR;
      $i++;
    }

    // Strip last separator
    return substr($relpath, 0, -1);
  }
}
