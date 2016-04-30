<?php

/**
 * @file
 * Contains \Drupal\Tests\restful\Kernel\Routing\ResourceRoutesTest.
 */

namespace Drupal\Tests\restful\Kernel\Routing;

use Drupal\restful\Routing\ResourceRoutes;
use Drupal\Tests\restful\Kernel\RestfulDrupalTestBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class ResourceRoutesTest.
 *
 * @package Drupal\Tests\restful\Unit\Routing
 *
 * @coversDefaultClass \Drupal\restful\Routing\ResourceRoutes
 *
 * @group RESTful
 */
class ResourceRoutesTest extends RestfulDrupalTestBase {

  /**
   * Modules to enable.
   *
   * @var string[]
   */
  public static $modules = [
    'node',
    'restful',
    'restful_examples',
    'rest',
    'serialization',
    'system',
    'user',
  ];

  /**
   * Tests the route generation based on the existing resource configs.
   *
   * It's not a testing best practice to test a protected method. But in this
   * case we don't want to go through the pain of testing the public ::routes
   * since it's already tested by the parent class in core.
   *
   * @covers ::alterRoutes
   */
  public function testAlterRoutes() {
    // Add a resource config object.
    $base_path = $this->getRandomGenerator()->name();
    $entity_type = 'node';
    $bundle = $this->getRandomGenerator()->name();
    $version = 'v' . (int) mt_rand(1, 10) . '.' . (int) mt_rand(1, 10);
    $this->entityTypeManager->getStorage('resource_config')->create([
      'id' => 'articles.' . $version,
      'contentEntityTypeId' => $entity_type,
      'version' => $version,
      'contentBundleId' => $bundle,
      'path' => $base_path,
    ])->save();
    $resource_routes = new ResourceRoutes($this->manager, $this->entityTypeManager, $this->logger, $this->container->get('restful.version_manager'));
    $route_collection = new RouteCollection();

    $reflection = new \ReflectionObject($resource_routes);
    $method = $reflection->getMethod('alterRoutes');
    $method->setAccessible(TRUE);

    $method->invokeArgs($resource_routes, [$route_collection]);
    $route_iterator = $route_collection->getIterator();
    while ($route = $route_iterator->current()) {
      // Check the altered routes.
      foreach ($route->getMethods() as $method) {
        if ($method == 'POST') {
          $this->assertEquals('/entity/' . $entity_type, $route->getPath());
        }
        else {
          // Make sure that the path is either prfixed with the route or the
          // latest version.
          $valid_path = '/' . $base_path . '/{' . $entity_type . '}' == $route->getPath() || '/' . $version . '/' . $base_path . '/{' . $entity_type . '}' == $route->getPath();
          $this->assertTrue($valid_path);
          $this->assertEquals($entity_type, $route->getRequirement('_entity_type'));
          $this->assertEquals($bundle, $route->getRequirement('_bundle'));
        }
      }
      $route_iterator->next();
    }
  }

}
