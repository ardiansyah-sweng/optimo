<?php

class SeedsGenerator
{
    public $sizeOfPopulation;
    public $numOfVariable;
    public $numOfSeeds;
    public $pathToSeeds;
    public $variableRanges;

    function generateRandomVariable()
    {
        return mt_rand($this->variableRanges['lowerBound'] * 100, $this->variableRanges['upperBound'] * 100) / 100;
    }

    public function writeToTXTFile($seedsIteration)
    {
        for ($i=0; $i < $this->sizeOfPopulation; $i++) {
            for ($j = 0; $j < $this->numOfVariable; $j++){
                $variables[] = $this->generateRandomVariable();
            }
            $fp = fopen('seeds_master.txt', 'a');
            fputcsv($fp, $variables);
            fclose($fp);
            $variables = [];
        }

        $file_content = file_get_contents('seeds_master.txt');
        file_put_contents($this->pathToSeeds.'seeds'.$seedsIteration.'.txt', $file_content);
    }

    function createSeedsFile()
    {
        for ($i = 0; $i < $this->numOfSeeds; $i++){
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
        'lowerBound' => -1.28,
        'upperBound' => 1.28
    ];

$seedsGenerator = new SeedsGenerator;
$seedsGenerator->sizeOfPopulation = 2500;
$seedsGenerator->numOfVariable = 30;
$seedsGenerator->numOfSeeds = 30;
$seedsGenerator->variableRanges = $variableRanges;
$seedsGenerator->pathToSeeds = 'TestFunctions/Range1Koma28/';
$seedsGenerator->createSeedsFile();

