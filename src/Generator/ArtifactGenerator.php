<?php

/**
 * @file Build a fully html based artifact.
 */
namespace surangapg\Haunt\Generator;

use surangapg\Haunt\Analysis\ComparisonAnalysisInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class ArtifactGenerator {

  /**
   * All the items from the manifest.
   *
   * @var \surangapg\Haunt\Manifest\ManifestInterface
   *   The manifest to get the files for.
   */
  protected $manifest;

  /**
   * The set of files for the reference.
   *
   * @var \surangapg\Haunt\Output\Structure\OutputStructureInterface
   *   The reference item set with the screenshots.
   */
  protected $reference;

  /**
   * The set of files for the comparison.
   *
   * @var \surangapg\Haunt\Output\Structure\OutputStructureInterface
   *   The new data set for the comparison.
   */
  protected $new;

  /**
   * The output handler.
   *
   * @var \surangapg\Haunt\Generator\BufferedOutput|\Symfony\Component\Console\Output\OutputInterface
   *   Output handler for all the items.
   */
  protected $output;

  /**
   * The comparison output directory.
   *
   * @var \surangapg\Haunt\Generator\BufferedOutput|\Symfony\Component\Console\Output\OutputInterface
   *   The directory to output the comparison files to.
   */
  protected $comparison;

  /**
   * File system helper.
   *
   * @var \Symfony\Component\Filesystem\Filesystem
   *   File system helper.
   */
  protected $fs;

  /**
   * The directory for the artifact.
   *
   * @var string
   *   The artifact dir.
   */
  protected $artifactDir;

  /**
   * Get the analysis for all the data.
   *
   * @var \surangapg\Haunt\Analysis\ComparisonAnalysisInterface
   *   The analysis for all the data.
   */
  protected $analysis;

  /**
   * ArtifactGenerator constructor.
   *
   * @param \surangapg\Haunt\Analysis\ComparisonAnalysisInterface $analysis
   *   The analysis for the different source dirs.
   * @param string $artifactDir
   *   The location where the artifact has to be placed.
   * @param \Symfony\Component\Console\Output\OutputInterface|NULL $output
   *   Output interface for the feedback.
   */
  public function __construct(ComparisonAnalysisInterface $analysis, string $artifactDir, OutputInterface $output = NULL) {
    if (!isset($output)) {
      $output = new BufferedOutput();
    }

    $this->output = $output;
    $this->comparison = $analysis->getComparison();
    $this->artifactDir = $artifactDir;
    $this->reference = $analysis->getReference();
    $this->new = $analysis->getNew();
    $this->analysis = $analysis;
  }

  /**
   * Write out an artifact to the filesystem.
   */
  public function generate() {

    $this->fs = new Filesystem();

    $this->getOutput()->writeln('<fg=yellow>Generating artifact</>');

    $this->moveAssets();

    // Prepare all the data for the output.
    $outputData = [
      'analysis' => $this->analysis,
    ];

    // @TODO Make this pluggable by allowing a different render item.
    $loader = new \Twig_Loader_Filesystem(dirname(dirname(__DIR__)) . '/tpl/simple-comparison');
    $twig = new \Twig_Environment($loader);

    $this->fs->dumpFile($this->artifactDir . '/index.html', $twig->render('index.html.twig', $outputData));
  }

  /**
   * Get the output interface.
   *
   * @return \Symfony\Component\Console\Output\BufferedOutput|\Symfony\Component\Console\Output\OutputInterface
   *   Output interface being used.
   */
  public function getOutput() {
    return $this->output;
  }

  /**
   * Move over all the required files into the artifact dir.
   */
  protected function moveAssets() {
    $this->getOutput()->writeln('Moving assets to the artifact');

    $sources = ['reference', 'new', 'comparison'];
    foreach ($sources as $source) {
      $this->fs->mirror($this->{$source}->getFolderRoot(), $this->artifactDir . '/assets/img/' . $source);
      // Reset the property to make it easier to generate links etc later.
      $this->{$source}->setFolderRoot($this->artifactDir . '/assets/img/' . $source);
    }
  }
}
