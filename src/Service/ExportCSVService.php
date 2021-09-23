<?php

namespace App\Service;

use App\Helper\FunctionHelper;
use App\Helper\LogHelper;
use DateTime;

class ExportCSVService
{

    private $filePath = "export";
    private $fileName = "exportCSVService";
    private $fileExtension = ".csv";
    private $delimiter = ";";
    private $enclosure = '"';
    private $escape_char = "\\";

    private $logHelper;

    private $lineParLigne = true;
    private $emptyValue = "NA";
    private $dateFormat = "Y-m-d";

    private $headers = null;
    private $data = [];

    public function __construct(LogHelper $logHelper)
    {
        $this->logHelper = $logHelper;
    }

    public function generateCsv($delete_file_before = true)
    {
        $isStoredFile = !empty($this->filePath) && !empty($this->fileName);

        if ($isStoredFile) {
            if (!file_exists($this->filePath)) {
                mkdir($this->filePath, 0777);
            }

            $file = $this->filePath . DIRECTORY_SEPARATOR . $this->fileName . $this->fileExtension;

            if ($delete_file_before && file_exists($file)) {
                unlink($file);
            }

            $handle = fopen($file, 'w');
        } else {
            $handle = fopen('php://output', 'w');
        }

        //add BOM to fix UTF-8 in Excel
        fputs($handle, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        $data = $this->data;
        $headers = $this->headers;

        if ($this->lineParLigne) {
            //écrit ligne par ligne le contenu du csv
            $this->writeLine($handle, $headers);
            foreach ($data as $line) {
                $this->writeLine($handle, $line);
            }
        } else {
            // $headers = array_keys($data);

            //Compte la max d'index (data)
            $maxIndex = 0;
            foreach ($headers as $header) {
                if (isset($data[$header]) && count($data[$header]) > $maxIndex) {
                    $maxIndex = count($data[$header]);
                }
            }

            //écrit ligne par ligne le contenu du csv
            $this->writeLine($handle, $this->headers);
            for ($index = 0; $index < $maxIndex; $index++) {
                $line = [];
                foreach ($headers as $header) {
                    $value = isset($data[$header][$index]) ? $data[$header][$index] : $this->emptyValue;

                    if ($value instanceof DateTime) {
                        $value = $this->getFormattedDate($value);
                    }

                    if (is_null($value) || $value == "") {
                        $value = $this->emptyValue;
                    }

                    $line[$header] = $value;
                }

                $this->writeLine($handle, $line);
            }
        }

        if ($isStoredFile) {
            fclose($handle);
        }

        $this->reset();

        if ($isStoredFile) {
            return $file;
        } else {
            $content = stream_get_contents($handle);
            fclose($handle);
            return $content;
        }
    }

    private function getFormattedDate($date)
    {
        if ($date instanceof DateTime && checkdate($date->format("m"), $date->format("d"), $date->format("Y")) && $date->format("Y") >= 1900) {
            return $date->format($this->dateFormat);
        } else {
            return null;
        }
    }

    private function writeLine($handle, $line)
    {
        if (!is_array($line)) {
            $this->logHelper->warning("Chaque ligne du csv doit être un tableau " . $line);
        } else {
            fputs($handle, implode($this->delimiter, array_map(function ($value) {
                    return "$this->enclosure$value$this->enclosure";
                }, $line)) . "\r\n");
        }
    }

    private function reset()
    {
        $this->data = [];
    }

    public function setConfig(array $headers, string $filePath, string $fileName, bool $lineParLigne = false, string $emptyValue = "NA")
    {
        $this->headers = $headers;
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->lineParLigne = $lineParLigne;
        $this->emptyValue = $emptyValue;
    }

    public function addLine(array $data)
    {
        if (!$this->lineParLigne) {
            $this->logHelper->warning("Vous ne pouvez pas utiliser cette fonction car le mode CSV n'est pas en ligne par ligne");
        }

        $this->data[] = $data;
    }

    public function addData($header, $value, $index)
    {
        if ($this->lineParLigne) {
            $this->logHelper->warning("Vous ne pouvez pas utiliser cette fonction car le mode CSV est en ligne par ligne");
        }

        if (is_string($value)) {
            //Validation de maximum 255 caractères (requis dans un celulle excel)
            $value = FunctionHelper::validationStringLength($value, 255, "...", false);
        }

        $this->data[$header][$index] = $value;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath(string $filePath): void
    {
        $this->filePath = $filePath;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    /**
     * @param string $fileExtension
     */
    public function setFileExtension(string $fileExtension): void
    {
        $this->fileExtension = $fileExtension;
    }

    /**
     * @param string $dateFormat
     */
    public function setDateFormat(string $dateFormat): void
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter(string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }

    public function test()
    {
        $this->setConfig(["nom", "prenom", "test", "asfd"], "test_tmp", false, "vide");

        $this->addData("nom", "riva1", 0);
        $this->addData("prenom", "fabrice1", 0);
        $this->addData("test", "ok1", 0);
        $this->addData("nom", "riva2", 1);
        $this->addData("prenom", "fabrice2", 1);

        $this->generateCsv();
    }

}
