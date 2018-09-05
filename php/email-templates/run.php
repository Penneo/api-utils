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
 *  --file=document.pdf
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
// ApiConnector::setLogger(new MyLogger());

if ($debug) {
	ApiConnector::enableDebug($debug);
	ApiConnector::throwExceptions($debug);
}

// Make sure that the file exists
if (!file_exists($file)) {
    echo 'File does not exist. Exitting..' . PHP_EOL;
    exit();
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

// Get the signing request
$request = $signer->getSigningRequest();

$request->setEmail("INSERT EMAIL HERE");

// Note: Email customization should be enabled for your account. Please get in
// touch with support@penneo.com if you need assistance
$request->setEmailFormat('html');


// Note: you can dynamically add information into the email body by using merge
// fields e.g. {{recipient.name}}, {{sender.name}} etc. For more details, have a
// look here:
// https://app.penneo.com/api/docs#put--api-v1-signingrequests-{requestId}

$request->setEmailSubject('Request to sign {{casefile.name}}');
$request->setEmailText('Dear {{recipient.name}}, Please sign using the link: {{link}}');

$request->setReminderEmailSubject("You still haven't signed {{casefile.name}}");
$request->setReminderEmailText("Dear {{recipient.name}}, This is just to remind you that you still haven't signed");

$request->setCompletedEmailSubject('All signers have signed {{casefile.name}}');
$request->setCompletedEmailText('Dear {{recipient.name}}, All signers have signed {casefile.name}}.');

$request->setEnableInsecureSigning(true);

SigningRequest::persist($request);

// Send
$cf->send();

echo PHP_EOL;
