<?php

/**
 * @file Component that writes out the report to a static html item.
 */
namespace surangapg\Haunt\Output;

/**
 * Class ComparisonOutput
 *
 * Contains that writes out a fully fledged static comparison ui.
 *
 * @package surangapg\Haunt\Output
 */
class ComparisonOutput implements HtmlGeneratorInterface {

  /**
   * @inheritdoc
   */
  public function generateHtml(array $reportData) {

    // @TODO Yield this?
    $loader = new \Twig_Loader_Filesystem(dirname(dirname(__DIR__)) . '/tpl/comparison');
    $twig = new \Twig_Environment($loader);

    $files = [
      'index' => $twig->render('overview.html.twig', $reportData),
    ];

    foreach ($reportData['records'] as $id => $group) {
      $files['ch-' . $id] = $twig->render('changes.html.twig', ['id' => $id, 'group' => $group]);
    }

    return $files;
  }
}