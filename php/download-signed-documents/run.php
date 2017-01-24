<?php

// namespace Penneo\ApiUtils;

namespace Penneo\SDK;

require(__DIR__ . '/vendor/autoload.php');

// Inputs
//
list($script, $endpoint, $username, $key, $debug) = $argv;

// Initialize the connection to the API
//
ApiConnector::enableDebug(!is_null($debug));
ApiConnector::initialize($username, $key, $endpoint);

$caseFiles = CaseFile::findBy(
    ['status' => 5], // criteria
    null,            // order
    10               // limit
);

$count = 0;
foreach ($caseFiles as $caseFile) {
    $documents = $caseFile->getDocuments();
    foreach ($documents as $document) {
        if ($document->getStatus() !== 'completed') {
            break;
        }
        $filename = "{$caseFile->getTitle()} - {$document->getTitle()}.pdf";

        $cfId     = $caseFile->getId();
        $cfTitle  = $caseFile->getTitle();
        $docId    = $document->getId();
        $docTitle = $document->getTitle();

        if (file_exists($filename) && filesize($filename) > 0 ) {
            echo "Skipping $docTitle [$docId] for $cfTitle [$cfId] \n";
            continue;
        }

        echo "Downloading $docTitle [$docId] for $cfTitle [$cfId] \n";

        // Save file
        $file = fopen($filename,'w');
        fwrite($file,$document->getPdf());
        fclose($file);

        $count++;
    }
}

echo "Total files downloaded: $count \n";



