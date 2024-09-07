<?php


//Não esquecer de adicionar no cronjob

function updateFilexx()
{
    $localFile = '../src/oui_local.txt';
    $remoteFile = 'https://standards-oui.ieee.org/oui/oui.csv';
    $localFileTimestamp = file_exists($localFile) ? filemtime($localFile) : 0;

    $remoteFileHeaders = @get_headers($remoteFile, 1);
    if ($remoteFileHeaders === false) {
        return;
    }

    $remoteFileLastModified = isset($remoteFileHeaders['Last-Modified']) ? strtotime($remoteFileHeaders['Last-Modified']) : 0;

    if ($remoteFileLastModified > $localFileTimestamp) {
        $fileContent = @file_get_contents($remoteFile);
        if ($fileContent !== false) {
            file_put_contents($localFile, $fileContent);
        }
    }
}


function getFabr($mac)
{
    static $manufacturerData = null; //reaprovitar 

    if ($manufacturerData === null) {
        $localFile = '../src/oui_local.txt';
        $manufacturerData = [];

        if (file_exists($localFile)) {
            $fileContents = file($localFile);

            foreach ($fileContents as $line) {
                $data = str_getcsv($line);
                if (isset($data[1]) && isset($data[2])) {
                    $prefix = $data[1];
                    $organizationName = trim($data[2]);
                    $manufacturerData[$prefix] = $organizationName;
                }
            }
        }
    }

    $mac = strtoupper($mac);
    $mac = preg_replace('/[^A-F0-9]/', '', $mac);

    if (strlen($mac) < 6) {
        return "Desconhecido";
    }

    $macPrefix = substr($mac, 0, 6);
    return $manufacturerData[$macPrefix] ?? "Desconhecido";
}

//updateFilexx();