<?php

class SeedsGenerator
{
    public $sizeOfPopulation;
    public $numOfVariable;
    public $numOfSeeds;
    public $pathToSeeds;
    public $variableRanges;

    function generateRandomVariable($var)
    {
        return mt_rand($var['lowerBound'] * 100, $var['upperBound'] * 100) / 100;
    }

    public function writeToTXTFile($seedsIteration)
    {
        for ($i = 0; $i < $this->sizeOfPopulation; $i++) {
            for ($j = 0; $j < $this->numOfVariable; $j++) {
                $variables[] = $this->generateRandomVariable($this->variableRanges[$j]);
            }
            $fp = fopen('seeds_master.txt', 'a');
            fputcsv($fp, $variables);
            fclose($fp);
            $variables = [];
        }

        $file_content = file_get_contents('seeds_master.txt');
        file_put_contents($this->pathToSeeds . 'seeds' . $seedsIteration . '.txt', $file_content);
    }

    function createSeedsFile()
    {
        for ($i = 0; $i < $this->numOfSeeds; $i++) {
            $this->writeToTXTFile($i);
            $f = @fopen("seeds_master.txt", "r+");
            if ($f !== false) {
                ftruncate($f, 0);
                fclose($f);
            }
        }
    }
}

$variableRanges = [
    [
        'lowerBound' => 0.01,
        'upperBound' => 100
    ],
    [
        'lowerBound' => 0.01,
        'upperBound' => 50
    ],

];

$seedsGenerator = new SeedsGenerator;
$seedsGenerator->sizeOfPopulation = 2500;
$seedsGenerator->numOfVariable = 2;
$seedsGenerator->numOfSeeds = 30;
$seedsGenerator->variableRanges = $variableRanges;
$seedsGenerator->pathToSeeds = 'EffortEstimation/Seeds/svm_zhou';
$seedsGenerator->createSeedsFile();
