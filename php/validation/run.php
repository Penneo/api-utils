<?php

/**
 * This script shows how to generate a validation request link
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
        // Optional
        //
        'debug::',
    ]
);

$endpoint   = $opts['endpoint'];
$key        = $opts['key'];
$secret     = $opts['secret'];
$debug      = @$opts['debug'];

// Initialize the connection to the API
//
ApiConnector::initialize($key, $secret, $endpoint);
ApiConnector::setLogger(new MyLogger());

if ($debug) {
	ApiConnector::enableDebug($debug);
	ApiConnector::throwExceptions($debug);
}

// Create a new validation
$myValidation = new Validation();
$myValidation->setTitle('Validation for John Døe');
$myValidation->setName('John Døe');
Validation::persist($myValidation);

// Output the validation link.
print('<a href="'.$myValidation->getLink().'">Validate now</a>');
