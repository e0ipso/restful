<?php

/**
 * @file
 * Contains \Drupal\restful\Plugin\resource\DataProvider\DataProviderResource.
 */

namespace Drupal\restful\Plugin\resource\DataProvider;

use Drupal\restful\Plugin\resource\ResourceInterface;
use Drupal\restful\Http\RequestInterface;
use Drupal\restful\Plugin\resource\Field\ResourceFieldCollectionInterface;
use Drupal\restful\Plugin\ResourcePluginManager;

/**
 * This data provider creates a resource and uses it to access the data.
 *
 * Class DataProviderResource
 * @package Drupal\restful\Plugin\resource\DataProvider
 */
class DataProviderResource extends DataProvider implements DataProviderResourceInterface {

  /**
   * The referenced resource.
   *
   * @var ResourceInterface
   */
  protected $resource;

  /**
   * The referenced data provider.
   *
   * @var DataProviderInterface
   */
  protected $referencedDataProvider;

  /**
   * Constructor.
   *
   * @param RequestInterface $request
   *   The request.
   * @param ResourceFieldCollectionInterface $field_definitions
   *   The field definitions.
   * @param object $account
   *   The authenticated account.
   * @param array $options
   *   The plugin options for the data provider.
   * @param string $langcode
   *   (Optional) The entity language code.
   * @param ResourceInterface $resource
   *   The referenced resource.
   */
  public function __construct(RequestInterface $request, ResourceFieldCollectionInterface $field_definitions, $account, array $options, $langcode = NULL, ResourceInterface $resource = NULL) {
    $this->resource = $resource;
    $this->referencedDataProvider = $resource->getDataProvider();
    parent::__construct($request, $field_definitions, $account, $options, $langcode);
  }

  /**
   * {@inheritdoc}
   */
  public static function init(RequestInterface $request, $resource_name, array $version) {
    $plugin_manager = ResourcePluginManager::create();
    /** @var ResourceInterface $resource */
    $resource = $plugin_manager->createInstance($resource_name . '::' . $version[0] . '.' . $version[1]);
    $plugin_definition = $resource->getPluginDefinition();
    return new static($request, $resource->getFieldDefinitions(), $resource->getAccount(), $plugin_definition['dataProvider'], static::getLanguage(), $resource);
  }

  /**
   * {@inheritdoc}
   */
  public function index() {
    return $this->referencedDataProvider->index();
  }

  /**
   * {@inheritdoc}
   */
  public function create($object) {
    return $this->referencedDataProvider->create($object);
  }

  /**
   * {@inheritdoc}
   */
  public function view($identifier) {
    return $this->referencedDataProvider->view($identifier);
  }

  /**
   * {@inheritdoc}
   */
  public function viewMultiple(array $identifiers) {
    return $this->referencedDataProvider->viewMultiple($identifiers);
  }

  /**
   * {@inheritdoc}
   */
  public function update($identifier, $object, $replace = FALSE) {
    return $this->referencedDataProvider->update($identifier, $object, $replace);
  }

  /**
   * {@inheritdoc}
   */
  public function remove($identifier) {
    $this->referencedDataProvider->remove($identifier);
  }

}