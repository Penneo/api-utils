<?php

/**
 * This script shows how to create case files in bulk and distribute emails from Penneo
 *
 * Usage:
 * php run.php \
 *  --endpoint="https://sandbox.penneo.com/api/v1/" \
 *  --key=key \
 *  --secret=secret \
 *  --files=document.pdf,contract.pdf \
 *  --template=email.txt \
 *  --recipients=recipients.csv \
 *  --title="Sample Case File" \
 *  --folder-id=0 \
 *  --expire-interval=30 \
 *  --remind-interval=5
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
        'title:',
        // Optional
        //
        'folder-id::',
        'expire-interval::',
        'remind-interval::',
        'dry::',
        'debug::',
    ]
);

$endpoint   = $opts['endpoint'];
$key        = $opts['key'];
$secret     = $opts['secret'];
$files      = explode(',', $opts['files']);
$recipients = $opts['recipients'];
$template   = $opts['template'];
$title      = $opts['title'];
//optional
$folderId       = @$opts['folder-id'];
$expireInterval = @$opts['expire-interval'] ?: 30;
$reminderInterval = @$opts['remind-interval'] ?: 2;
$dry            = isset($opts['dry']);
$debug          = @$opts['debug'];

// Check if files exist

// Make sure all document files exist
foreach ($files as $file) {
  if (!file_exists($file)) {
    echo 'File does not exist.' . PHP_EOL;
    exit();
  }
}

// Email template
if (!file_exists($template)) {
  echo 'Email template file does not exist.' . PHP_EOL;
  exit();
}
$template = file_get_contents($template);

// Recipients
if (!file_exists($recipients)) {
  echo "Recipients csv file not found: $recipients." . PHP_EOL;
  exit();
}

// Expire Interval
echo "Expire interval: $expireInterval" . PHP_EOL;
echo "Remind interval: $reminderInterval" . PHP_EOL;

// Initialize the connection to the API
//
ApiConnector::initialize($key, $secret, $endpoint);

if ($debug) {
	ApiConnector::enableDebug($debug);
	ApiConnector::throwExceptions($debug);
}

// Get the folder
$folder = null;
if ($folderId) {
  $message = '';
  try {
    $folder = Folder::find($folderId);
  } catch (\Exception $ex) {
    echo $ex->getMessage() . PHP_EOL;
  }
  if (!$folder) {
    echo "Folder with Id: $folderId not found." . PHP_EOL;
    exit();
  }
}

$count = 0;
$lines = file($recipients, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {

  list ($name, $email) = explode(',', $line);
  $name = trim($name);
  $email = trim($email);

  echo "- $name ($email)" . PHP_EOL;

  if ($dry || !$name || !$email) {
    echo "  skipped!" . PHP_EOL;
    continue;
  }

  $expireAt = new \DateTime();
  $expireAt->modify('+30 day');

  // create a new case file
  $cf = new CaseFile();
  $cf->setTitle("$title : $name");
  $cf->setExpireAt($expireAt);
  CaseFile::persist($cf);

  $folder->addCaseFile($cf);

  // Create a new signer that can sign documents in the case file
  $signer = new Signer($cf);
  $signer->setName($name);
  // $signer->setOnBehalfOf('Acme Inc');
  Signer::persist($signer);

  // Documents for signing
  foreach ($files as $file) {
    // Document
    $doc = new Document($cf);
    $doc->setTitle($file);
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
  $request->setReminderInterval($reminderInterval);
  SigningRequest::persist($request);

  // Activate
  $cf->send();

  echo "  done!" . PHP_EOL;
  $count++;
}

// Generate the signing request link
echo "Total case files created: $count" . PHP_EOL;
