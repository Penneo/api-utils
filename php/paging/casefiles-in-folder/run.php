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
        'folder-id:',
        // Optional
        //
        'debug::',
    ]
);

$endpoint   = $opts['endpoint'];
$key        = $opts['key'];
$secret     = $opts['secret'];
$folderId   = $opts['folder-id'];

// Initialize the connection to the API
//
ApiConnector::initialize($key, $secret, $endpoint);
// ApiConnector::setLogger(new MyLogger());

$page    = 1;
$perPage = 5;

$folder = Folder::find($folderId);

while ($caseFiles = $folder->getCaseFiles($page, $perPage)) {
    echo "Page: $page" . PHP_EOL;
    foreach ($caseFiles as $caseFile) {
        echo " - {$caseFile->getId()}" . PHP_EOL;
    }
    $page++;
}
