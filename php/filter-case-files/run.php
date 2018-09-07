<?php

/**
 * Filtering the case files / paging
 *
 * Usage:
 * php run.php \
 *  --endpoint="https://sandbox.penneo.com/api/v1/" \
 *  --key=key \
 *  --secret=secret \
 */

namespace Penneo\SDK;

require(__DIR__ . '/vendor/autoload.php');

use Psr\Log\AbstractLogger;

// Create a custom logger

class MyLogger extends AbstractLogger
{
    public function log($level, $message, array $context = array())
    {
        $message = (string) $message;
        echo "$level : $message : " . print_r($context, true);
    }
}

// Inputs
//
$opts = getopt(
    "",
    [
        // Required
        //
        'endpoint:',
        'key:',
        'secret:',
        'folderId:',
        // Optional
        //
        'debug::',
    ]
);

$endpoint   = $opts['endpoint'];
$key        = $opts['key'];
$secret     = $opts['secret'];
$folderId   = $opts['folderId'];
$debug      = @$opts['debug'];

// Initialize the connection to the API
//
ApiConnector::initialize($key, $secret, $endpoint);
// ApiConnector::setLogger(new MyLogger());

if ($debug) {
	ApiConnector::enableDebug($debug);
	ApiConnector::throwExceptions($debug);
}

$limit  = 10;
$offset = 0;

while ($caseFiles = CaseFile::findBy(['folderIds' => $folderId], [], $limit, $offset)) {
    foreach ($caseFiles as $caseFile) {
        echo $caseFile->getId() . PHP_EOL;
    }
    $offset += $limit;
}
