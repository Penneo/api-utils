<?php

/**
 * This script shows how to generate a signing request link for a signer that
 * signs multiple documents
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
        'files:',
        // Optional
        //
        'debug::',
    ]
);

$endpoint   = $opts['endpoint'];
$key        = $opts['key'];
$secret     = $opts['secret'];
$files      = explode(',', $opts['files']);
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
$cf->setLanguage('da');
// $cf->setSensitiveData(true);
CaseFile::persist($cf);

echo "Case File Id : " . $cf->getId();
echo PHP_EOL;


// Create a new signer that can sign documents in the case file
$signer = new Signer($cf);
$signer->setName('John Doe');
$signer->setOnBehalfOf('Acme Inc');
Signer::persist($signer);

// Documents for signing
foreach ($files as $index => $file) {
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
// Optionally, set the the email information if you want to send the mail
// through Penneo
//
$request->setEmail('fo@penneo.com');
$request->setEmailSubject('Test subject');
$request->setEmailText('Test text');
// $request->setAccessControl(true);
// $request->setEnableInsecureSigning(true);
SigningRequest::persist($request);

// Activate
$cf->send();

// Generate the signing request link
echo $request->getLink();
echo PHP_EOL;
