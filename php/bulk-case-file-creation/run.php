<?php

/**
 * This script shows how to generate a signing request link for a signer that
 * signs one document
 *
 * Usage:
 * php run.php \
 *  --endpoint="https://sandbox.penneo.com/api/v1/" \
 *  --key=key \
 *  --secret=secret \
 *  --files=document.pdf,contract.pdf \
 *  --template=template.txt \
 *  --recipients=recipients.csv
 *
 * TODO: Case file title
 * TODO: Document title
 * TODO: Folder Id should be optional
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
        'files:',
        'recipients:',
        'template:',
        // Optional
        //
        'debug::',
    ]
);

$endpoint   = $opts['endpoint'];
$key        = $opts['key'];
$secret     = $opts['secret'];
$files      = explode(',', $opts['files']);
$recipients = $opts['recipients'];
$template   = $opts['template'];
$debug      = @$opts['debug'];

// Check if files exist

// Make sure all document files exist
foreach ($files as $file) {
  if (!file_exists($file)) {
    echo 'File does not exist. Exitting..' . PHP_EOL;
    exit();
  }
}

// Email template
if (!file_exists($template)) {
  echo 'Email template file does not exist. Exitting..' . PHP_EOL;
  exit();
}
$template = file_get_contents($template);

// Recipients
if (!file_exists($recipients)) {
  echo 'Email template file does not exist. Exitting..' . PHP_EOL;
  exit();
}

// Initialize the connection to the API
//
ApiConnector::initialize($key, $secret, $endpoint);

if ($debug) {
	ApiConnector::enableDebug($debug);
	ApiConnector::throwExceptions($debug);
}

$count = 0;
$lines = file($recipients, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {

  list ($name, $email) = explode(',', $line);

  if (!$name || !$email) {
    echo "Skipping creation of case file for Name: $name, Email: $email \n";
    continue;
  }

  // create a new case file
  $cf = new CaseFile();
  $cf->setTitle('Sample Case File');
  CaseFile::persist($cf);

  // Create a new signer that can sign documents in the case file
  $signer = new Signer($cf);
  $signer->setName($name);
  // $signer->setOnBehalfOf('Acme Inc');
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
  $request->setEmail($email);
  $request->setEmailText($template);

  // Activate
  $cf->send();

  echo "Case file created for $name ($email) \n";
  $count++;
}

// Generate the signing request link
echo "Total case files created: $count \n";
