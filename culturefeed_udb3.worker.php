#!/usr/bin/php
<?php

/**
 * @file
 * Contains culturefeed_udb3.worker.php.
 */

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;
use CultuurNet\UDB3\CommandHandling\QueueJob;

$options = getopt('', array('drupal_root:'));
$root = (isset($options['drupal_root'])) ? $options['drupal_root'] : '';

if (!$root) {
  print "No Drupal root provided.\n";
  exit();
}

$autoloader_path = $root . '/core/vendor/autoload.php';
if (!file_exists($autoloader_path)) {
  print "Drupal root is invalid\n";
  print "No vendor autoload found at $autoloader_path\n";
  exit();
}

chdir($root);
$autoloader = require $autoloader_path;
require $root . '/sites/all/vendor/autoload.php';

$request = Request::createFromGlobals();
$kernel = DrupalKernel::createFromRequest($request, $autoloader, 'prod');
$kernel->prepareLegacyRequest($request);

$queue_name = $kernel->getContainer()->getParameter('command_bus.queue_name');

// We need to close the database connection here, otherwise
// the worker child process will kill it when the process finishes, and the
// next worker child process won't be able to use the database.
\Drupal\Core\Database\Database::closeConnection();

// Bootstrap drupal after the parent forks its process and is ready to perform.
Resque_Event::listen('afterFork', function() use ($autoloader) {

  try {

    $request = Request::createFromGlobals();
    $kernel = DrupalKernel::createFromRequest($request, $autoloader, 'prod');
    $kernel->prepareLegacyRequest($request);

    $command_bus = $kernel->getContainer()->get(
      'culturefeed_udb3.event_command_bus'
    );
    QueueJob::setCommandBus($command_bus);

  }
  catch (Exception $e) {

    $message = 'Error';
    print $e->getTraceAsString();
    print $message;
    throw $e;

  }

});

$worker = new Resque_Worker(array($queue_name));
$worker->logLevel = Resque_Worker::LOG_VERBOSE;
fwrite(STDOUT, '*** Starting worker ' . $worker . "\n");
$worker->work(5);
