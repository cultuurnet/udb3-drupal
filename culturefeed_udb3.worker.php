#!/usr/bin/php
<?php

/**
 * @file
 * Contains culturefeed_udb3.worker.php.
 */

use Drupal\Core\DrupalKernel;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\Request;
use CultuurNet\UDB3\CommandHandling\QueueJob;

$options = getopt('', array('drupal_root:', 'uri:'));
$root = (isset($options['drupal_root'])) ? $options['drupal_root'] : '';

$base_url = $options['uri'] ?: NULL;
if ($base_url) {
  $GLOBALS['base_url'] = $base_url;
}

if (!$root) {
  print "No Drupal root provided.\n";
  exit();
}

$autoloader_path = $root . '/autoload.php';
if (!file_exists($autoloader_path)) {
  print "Drupal root is invalid\n";
  print "No vendor autoload found at $autoloader_path\n";
  exit();
}

chdir($root);
$autoloader = require $autoloader_path;

$request = Request::createFromGlobals();
$kernel = DrupalKernel::createFromRequest($request, $autoloader, 'prod');
$kernel->prepareLegacyRequest($request);

$queue_name = $kernel->getContainer()->getParameter('command_bus.queue_name');

// We need to close the database connection here, otherwise
// the worker child process will kill it when the process finishes, and the
// next worker child process won't be able to use the database.
Database::closeConnection();

// Bootstrap drupal after the parent forks its process and is ready to perform.
Resque_Event::listen('afterFork', function(Resque_Job $job) use ($autoloader) {

  try {

    $request = Request::createFromGlobals();
    $kernel = DrupalKernel::createFromRequest($request, $autoloader, 'prod');
    $kernel->prepareLegacyRequest($request);

    $args = $job->getArguments();
    $context = unserialize(base64_decode($args['context']));

    /* @var \Drupal\culturefeed_udb3\Impersonator $impersonator */
    $impersonator = $kernel->getContainer()->get('culturefeed_udb3.impersonator');
    $impersonator->impersonate($context);

    /* @var \CultuurNet\UDB3\CommandHandling\ResqueCommandBus $command_bus */
    $command_bus = $kernel->getContainer()->get('culturefeed_udb3.event_command_bus');
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
$worker->work(1);
