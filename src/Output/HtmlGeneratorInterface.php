<?php

/**
 * @file Component that writes out the report to a static html item.
 */
namespace surangapg\Haunt\Output;

/**
 * Interface OutputTypeInterface
 *
 * Contains an interface for common classes to generate a set of static html
 * of a given type.
 *
 * @TODO Make the passing of the report data less primitive.
 *
 * @package surangapg\Haunt\Output
 */
interface HtmlGeneratorInterface {

  /**
   * Location on the filesytem where the twig files can be found.
   *
   * @param array $reportData
   *
   * @return string[]
   */
  public function generateHtml(array $reportData);

}