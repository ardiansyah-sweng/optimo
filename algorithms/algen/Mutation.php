<?php

class Mutation
{
    public $numOfGen;

    function calculateMutationRate(): float
    {
        return 1 / $this->numOfGen;
    }

    function calculateNumOfMutation($popSize): int
    {
        return round($this->calculateMutationRate() * $popSize);
    }

    function isContains($numOfMutation)
    {
        if ($numOfMutation > 0) {
            return true;
        }
    }

    function changeGen($valueOfGen, $varType, string $function)
    {
        if ($varType === 'binary') {
            if ($valueOfGen === 0) {
                return 1;
            } else {
                return 0;
            }
        }

        if ($varType === 'real') {
            $var = (new Variables())
                ->initializeVariableFactory($function)
                ->getVariables('');
            $genValue = mt_rand($var['ranges'][0]['lowerBound'] * 100, $var['ranges'][0]['upperBound'] * 100) / 100;
            return $genValue;
        }
    }

    function runMutation(array $population, int $popSize, string $function): array
    {
        $numOfMutation = $this->calculateNumOfMutation($popSize);

        $ret = [];
        if ($this->isContains($numOfMutation)) {
            for ($i = 0; $i < $numOfMutation; $i++) {
                $indexOfChromosomes = (new Randomizers())->getRandomIndexOfIndividu($popSize);
                $indexOfGen = Randomizers::getRandomIndexOfIndividu($this->numOfGen);
                $selectedChromosomes = $population[$indexOfChromosomes];
                $valueOfGen = $selectedChromosomes['individu'][$indexOfGen];
                $mutatedGen = $this->changeGen($valueOfGen, 'real', $function);
                $selectedChromosomes['individu'][$indexOfGen] = $mutatedGen;
                $ret[] = $selectedChromosomes;
            }
        }
        return $ret;
    }
}
