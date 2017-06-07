<?php

/**
 * This script shows how to create a case file using a template
 *
 * Usage:
 * php run.php \
 *  --endpoint="https://sandbox.penneo.com/api/v1/" \
 *  --key=key \
 *  --secret=secret \
 *  --file=document.pdf
 */

namespace Penneo\SDK;

require(__DIR__ . '/vendor/autoload.php');

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
        'file:',
        // Optional
        //
        'debug::',
    ]
);

$endpoint   = $opts['endpoint'];
$key        = $opts['key'];
$secret     = $opts['secret'];
$file       = $opts['file'];
$debug      = @$opts['debug'];

// Initialize the connection to the API
//
ApiConnector::initialize($key, $secret, $endpoint);

if ($debug) {
	ApiConnector::enableDebug($debug);
	ApiConnector::throwExceptions($debug);
}

// Make sure file exists
if (!file_exists($file)) {
    echo 'File does not exist. Exitting..' . PHP_EOL;
    exit();
}

// Create a new case file
//
$cf = new CaseFile();
$cf->setTitle('Sample Case File');
//
// Set template
//
$templates = $cf->getCaseFileTemplates();
$cf->setCaseFileTemplate($templates[0]);
//
CaseFile::persist($cf);


// Get available document types
$documentTypes = $cf->getDocumentTypes();

// Document for signing
//
$doc = new Document($cf);
$doc->setTitle('Sample Document');
$doc->setPdfFile($file);
$doc->makeSignable();
$doc->setDocumentType($documentTypes[0]);
Document::persist($doc);

// Get the signer types
//
// Create a new signer that can sign documents in the case file
$signer = new Signer($cf);
$signer->setName('John Doe');
$signer->setOnBehalfOf('Acme Inc');
Signer::persist($signer);

// Add signer type
//
$signerTypes = $cf->getSignerTypes();
$signer->addSignerType($signerTypes[0]);


// Check case file for validation errors
//
$errors = $cf->getErrors();
if (count($errors) > 0) {
	echo 'The case file configuration has the following problems:' . PHP_EOL;
	foreach ($errors as $error) {
		print($error);
	}
    exit();
}

// Activate
$cf->send();

echo 'Case file created: ' . $cf->getId() . PHP_EOL;
