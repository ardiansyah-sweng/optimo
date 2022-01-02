<?php

interface LocalParameter
{
    function getLocalParameter();
}

class GA implements LocalParameter
{
    function getLocalParameter()
    {
        return [
            'parameterName' => 'gaParameter',
            'populationSize' => 50,
            'cr'=>0.9,
            'mr'=>0.01
        ];
    }
}

class PSO implements LocalParameter
{
    function getLocalParameter()
    {
        return [
            'parameterName' => 'psoParameter',
            'maxIteration' => 1000,
            'populationSize' => 50,
            'c1' => 2,
            'c2' => 2,
            'inertiaMax' => 0.9,
            'inertiaMin' => 0.4,
        ];
    }
}

class UCPSO implements LocalParameter
{
    function getLocalParameter()
    {
        $pso = new PSO;
        return [
            'parameterName' => 'cpsoParameter',
            'chaotic1' => 'singer',
            'chaotic2' => 'sine',
            'populationSize' => $pso->getLocalParameter()['populationSize'],
            'maxIteration' => $pso->getLocalParameter()['maxIteration'],
            'c1' => $pso->getLocalParameter()['c1'],
            'c2' => $pso->getLocalParameter()['c2'],
            'inertiaMax' => $pso->getLocalParameter()['inertiaMax'],
            'inertiaMin' => $pso->getLocalParameter()['inertiaMin']
        ];
    }
}

class KMA implements LocalParameter
{
    function __construct($numOfVariabe)
    {
        $this->numOfVariable = $numOfVariabe;    
    }

    function getLocalParameter()
    {
        return [
            'maxIteration' => 1000,
            'populationSize' => 5,
            'n2' => 200,
            'n2Min' => 20,
            'n2Max' => 200,
            'p1' => 0.5,
            'p2' => 0.5,
            'd1' => ($this->numOfVariable - 1) / $this->numOfVariable,
            'd2' => 0.5
        ];
    }
}

class LocalParameterFactory
{
    function initializingLocalParameter($optimizerType, $numOfVariable)
    {
        $optimizerTypes = [
            ['optimizer' => 'ga', 'select' => new GA],
            ['optimizer' => 'pso', 'select' => new PSO],
            ['optimizer' => 'ucpso', 'select' => new UCPSO],
            ['optimizer' => 'mypso1', 'select' => new UCPSO],
            ['optimizer' => 'komodo', 'select' => new KMA($numOfVariable)]
        ];
        $index = array_search($optimizerType, array_column($optimizerTypes, 'optimizer'));
        return $optimizerTypes[$index]['select'];
    }
}