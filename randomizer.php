<?php

require 'vendor/autoload.php';

class Randomizers
{
    static function randomZeroToOneFraction()
    {
        return (float) rand() / (float) getrandmax();
    }

    static function randomVariableValueByRange($variableRanges)
    {
        return mt_rand($variableRanges['lowerBound'] * 100, $variableRanges['upperBound'] * 100) / 100;
    }

    static function getCutPointIndex($lengthOfChromosome)
    {
        return rand(0, $lengthOfChromosome - 1);
    }

    static function getRandomIndexOfIndividu($popSize)
    {
        return rand (0, $popSize-1);
    }
}
