<?php
require 'vendor/autoload.php';

use DataProcessor\DataprocessorFactory;
use DataProcessor\SeedsProcessor;
use PHPUnit\Framework\TestCase;

class DaraProcessorTest extends TestCase
{
    function test_getSeedFiles()
    {
        $scan = new DirectoryScanner;
        $scan->pathToDirectory = 'Dataset/EffortEstimation/Seeds/agile/';
        $seedFiles = $scan->getFileNames();
        // print_r($seedFiles);
        // die;

        $dataProcessor = new DataprocessorFactory;
        $result = $dataProcessor->initializeDataprocessor('seeds', 35);
        print_r($result->processingData($seedFiles[0]));
    }

    function test_getSeedsData()
    {
        $dataProcessor = new DataprocessorFactory;
        $result = $dataProcessor->initializeDataprocessor('seeds', 35);
        print_r($result->processingData('C:\xampp\htdocs\optimo\Dataset\EffortEstimation\Seeds\agile\seeds0.txt'));
    }
}