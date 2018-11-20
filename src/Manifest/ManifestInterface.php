<?php

namespace surangapg\Haunt\Manifest;

/**
 * Interface ManifestInterface
 *
 * A manifest is a collection of items that need to have a screenshot taken.
 * It acts as the base point between a set of config about the items that are
 * relevant and the various sources / output handlers.
 *
 * @package surangapg\Haunt\Manifest
 */
interface ManifestInterface {

  /**
   * ManifestInterface constructor.
   *
   * @param array $config
   *   Needed config for this manifest item.
   */
  public function __construct(array $config);

  /**
   * List all the items in the manifest.
   *
   * @return \surangapg\Haunt\Manifest\Item\ManifestItemInterface[]
   *   Full list of all the manifest items.
   */
  public function listManifestItems();

  /**
   * Check or the config is valid.
   *
   * @throws \surangapg\Haunt\Exception\InvalidManifestConfigException
   *   If the configuration was not valid.
   */
  public function checkConfig();
}
