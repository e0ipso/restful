<?php

/**
 * @file
 * Contains RequestCacheabilityDependency.php.
 */

namespace Drupal\restful;

use Drupal\Core\Cache\CacheableDependencyInterface;

/**
 * Class RequestCacheabilityDependency.
 */
class RequestCacheabilityDependency implements CacheableDependencyInterface {

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return ['url.query_args:fields'];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return -1;
  }


}
