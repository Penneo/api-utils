<?php

/**
 * This script shows how to enable logging for the php sdk
 *
 * Works with version >= 1.6.0
 *
 * Usage:
 * php run.php \
 *  --endpoint="https://sandbox.penneo.com/api/v1/" \
 *  --key=key \
 *  --secret=secret
 */

namespace Penneo\SDK;

require(__DIR__ . '/vendor/autoload.php');

use Psr\Log\AbstractLogger;

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
    ]
);

$endpoint   = $opts['endpoint'];
$key        = $opts['key'];
$secret     = $opts['secret'];

// Create a custom logger

class MyLogger extends AbstractLogger
{
    public function log($level, $message, array $context = array())
    {
        $message = (string) $message;
        echo "$level : $message : " . print_r($context, true);
    }
}

// Initialize the connection to the API
//
ApiConnector::initialize($key, $secret, $endpoint, null, ['Accept' => 'application/json']);
ApiConnector::setLogger(new MyLogger());

// Request and response bodies will be logged when calling a function that
// generates an API request:
//
CaseFile::find(999999999);
