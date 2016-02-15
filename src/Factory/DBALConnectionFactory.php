<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\Factory\DBALConnectionFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Event\Listeners\SQLSessionInit;
use Drupal\Core\Database\Connection;

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


  public function get() {

    $eventManager = new EventManager();
    $sqlMode = 'NO_ENGINE_SUBSTITUTION,STRICT_ALL_TABLES';
    $query = "SET SESSION sql_mode = '{$sqlMode}'";
    $eventManager->addEventSubscriber(
      new SQLSessionInit($query)
    );

    $options = $this->database->getConnectionOptions();
    $options['driver'] = 'mysqli';
    unset($options['pdo']);

    $connection = DriverManager::getConnection(
      $options,
      null,
      $eventManager
    );

    return $connection;

  }

}
