<?php

namespace Drupal\culturefeed_udb3\Factory;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Event\Listeners\SQLSessionInit;
use Drupal\Core\Database\Connection;

/**
 * Class DBALConnectionFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class DBALConnectionFactory {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * VariationSearchRepository constructor.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(
    Connection $database
  ) {
    $this->database = $database;
  }


  /**
   * Get the dbal connection.
   *
   * @return \Doctrine\DBAL\Connection
   *   The dbal connection.
   *
   * @throws \Doctrine\DBAL\DBALException
   */
  public function get() {

    $event_manager = new EventManager();
    $sql_mode = 'NO_ENGINE_SUBSTITUTION,STRICT_ALL_TABLES';
    $query = "SET SESSION sql_mode = '{$sql_mode}'";
    $event_manager->addEventSubscriber(
      new SQLSessionInit($query)
    );

    // @TODO
    // Improve !!! (more generic, ...).
    $drupal_options = $this->database->getConnectionOptions();
    $dbal_options = array(
      'dbname' => 'udb3-drupal',
      'driver' => 'pdo_mysql',
      'password' => $drupal_options['password'],
      'user' => $drupal_options['username'],
    );

    $connection = DriverManager::getConnection(
      $dbal_options,
      NULL,
      $event_manager
    );

    return $connection;

  }

}
