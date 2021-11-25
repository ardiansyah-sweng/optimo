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
        foreach ($variableRanges as $range){
            $ret[] = mt_rand($range['lowerBound'] * 100, $range['upperBound'] * 100) / 100;
        }
        return $ret;
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
