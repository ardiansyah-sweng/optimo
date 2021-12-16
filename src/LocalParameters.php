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
            'populationSize' => 30,
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
            'populationSize' => 100,
            'c1' => 2,
            'c2' => 2,
            'inertiaMax' => 0.9,
            'inertiaMin' => 0.4,
        ];
    }
}

class CPSO extends PSO implements LocalParameter
{
    function getLocalParameter()
    {
        $pso = new PSO;
        return [
            'parameterName' => 'cpsoParameter',
            // 'psoParameter' => $pso->getLocalParameter(),
            'chaotic1' => 'singer',
            'chaotic2' => 'sine',
            'populationSize' => $pso->getLocalParameter()['populationSize'] 
        ];
    }
}

class Rao implements LocalParameter
{
    function getLocalParameter()
    {
        return [
            'parameterName' => 'raoParameter',
            'maxIteration' => 1000,
            'populationSize' => 100
        ];
    }
}

class LocalParameterFactory
{
    function initializingLocalParameter($optimizerType)
    {
        $optimizerTypes = [
            ['optimizer' => 'ga', 'select' => new GA],
            ['optimizer' => 'pso', 'select' => new PSO],
            ['optimizer' => 'cpso', 'select' => new CPSO],
            ['optimizer' => 'rao', 'select' => new Rao]
        ];
        $index = array_search($optimizerType, array_column($optimizerTypes, 'optimizer'));
        return $optimizerTypes[$index]['select'];
    }
}