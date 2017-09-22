<?php

/**
 * This script shows how to generate a signing request link for a signer that
 * signs one or more documents
 *
 * Usage:
 * php run.php \
 *  --endpoint="https://sandbox.penneo.com/api/v1/" \
 *  --key=key \
 *  --secret=secret \
 *  --files=document.pdf,contract.pdf
 */

namespace Penneo\SDK;

require(__DIR__ . '/vendor/autoload.php');

use Psr\Log\AbstractLogger;

// Create a custom logger

// class MyLogger extends AbstractLogger
// {
//     public function log($level, $message, array $context = array())
//     {
//         $message = (string) $message;
//         echo "$level : $message : " . print_r($context, true);
//     }
// }

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
        'files:',
        // Optional
        //
        'debug::',
    ]
);

$endpoint   = $opts['endpoint'];
$key        = $opts['key'];
$secret     = $opts['secret'];
$files       = explode(',', $opts['files']);
$debug      = @$opts['debug'];

// Initialize the connection to the API
//
ApiConnector::initialize($key, $secret, $endpoint);
// ApiConnector::setLogger(new MyLogger());

if ($debug) {
	ApiConnector::enableDebug($debug);
	ApiConnector::throwExceptions($debug);
}

// Make sure all files exist
foreach ($files as $file) {
    if (!file_exists($file)) {
        echo 'File does not exist. Exitting..' . PHP_EOL;
        exit();
    }
}

// create a new case file
$cf = new CaseFile();
$cf->setTitle('Sample Case File');
CaseFile::persist($cf);

// Create a new signer that can sign documents in the case file
$signer = new Signer($cf);
$signer->setName('John Doe');
$signer->setOnBehalfOf('Acme Inc');
Signer::persist($signer);

// Documents for signing
foreach ($files as $file) {
    // Document
    $doc = new Document($cf);
    $doc->setTitle('Sample Document');
    $doc->setPdfFile($file);
    $doc->makeSignable();
    Document::persist($doc);

    // Create a new signature line on the document
    $sigLine = new SignatureLine($doc);
    $sigLine->setRole('Signer');
    SignatureLine::persist($sigLine);

    // Link the signer to the signature line
    $sigLine->setSigner($signer);
}



// Get the signing request
$request = $signer->getSigningRequest();
$request->setEnableInsecureSigning(true);
SigningRequest::persist($request);

// Activate
$cf->send();

// Generate the signing request link
echo $request->getLink();
echo PHP_EOL;
