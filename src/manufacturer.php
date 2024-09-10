<?php

class Manufacturer
{
    private $oui_path;
    function __construct()
    {
        $this->oui_path = dirname(__FILE__, 2) . '/data/oui.csv';
        $this->updateOuiData();

    }

    public function updateOuiData()
    {
        $localFile = $this->oui_path;
        $remoteFile = 'https://standards-oui.ieee.org/oui/oui.csv';
        $localFileTimestamp = file_exists($localFile) ? filemtime($localFile) : 0;

        $remoteFileHeaders = @get_headers($remoteFile, 1);
        if ($remoteFileHeaders === false) {
            return;
        }

        $remoteFileLastModified = isset($remoteFileHeaders['Last-Modified']) ? strtotime($remoteFileHeaders['Last-Modified']) : 0;

        if ($remoteFileLastModified - 604800 > $localFileTimestamp) { // Checar apenas uma vez por semana
            $fileContent = @file_get_contents($remoteFile);
            if ($fileContent !== false) {
                file_put_contents($localFile, $fileContent);
            }
        }
    }


    public function getManufacturer($mac)
    {
        $manufacturerData = null;

        if ($manufacturerData === null) {
            $localFile = $this->oui_path;
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
}