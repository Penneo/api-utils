<?php

// namespace Penneo\ApiUtils;

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
        // Optional
        //
        'folder-id::',
        'case-file-id::',
        'directory::',
        'debug::',
        'dry-run::',
    ]
);

if (!$opts) {
    echo "Usage: php {$argv[0]} --endpoint='xxx' --key='xxx' --secret='xxx'" . PHP_EOL;
    exit(-1);
}


$endpoint   = $opts['endpoint'];
$key        = $opts['key'];
$secret     = $opts['secret'];
$folderId   = @$opts['folder-id'];
$caseFileId = @$opts['case-file-id'];
$directory  = @$opts['directory'] ?: '.';
$debug      = @$opts['debug'];
$dryRun     = @$opts['dry-run'] === 'true';


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
ApiConnector::enableDebug(!is_null($debug));
ApiConnector::initialize($key, $secret, $endpoint);
ApiConnector::setLogger(new MyLogger());

// Find Case Files
//
$caseFiles = [];
if ($caseFileId) {
    $caseFile = CaseFile::find($caseFileId);
    $caseFiles = [$caseFile];
} else {
    $criteria = [
        'status' => 5,
    ];
    if ($folderId) {
        $criteria['folderIds'] = $folderId;
    }
    $caseFiles = CaseFile::findBy(
        $criteria,
        null,            // order
        10               // limit
    );
}

$count = 0;
foreach ($caseFiles as $caseFile) {
    $documents = $caseFile->getDocuments();
    foreach ($documents as $document) {
        if ($document->getStatus() !== 'completed') {
            break;
        }
        $filename = "$directory/{$caseFile->getTitle()} - {$document->getTitle()}.pdf";

        $cfId     = $caseFile->getId();
        $cfTitle  = $caseFile->getTitle();
        $docId    = $document->getId();
        $docTitle = $document->getTitle();

        $message = "$cfId : $cfTitle | $docId : $docTitle";
        if (file_exists($filename) && filesize($filename) > 0 ) {
            echo "$message \t [skipped]\n";
            continue;
        }

        echo "$message \n";

        if (!$dryRun) {
            // Save file
            $file = fopen($filename,'w');
            fwrite($file,$document->getPdf());
            fclose($file);
        }

        $count++;
    }
}

echo "Total files downloaded: $count \n";
