<?php

interface VariablesInterface
{
    function getVariables($experimentType);
}

class F_1_3_4_6 implements VariablesInterface
{
    function getVariables($experimentType)
    {
        if ($experimentType){
            return 'Dataset\TestFunctions\Range100';
        }

        return [
            'numOfVariables' => 30,
            'ranges' => [
                ['lowerBound' => -100, 'upperBound' => 100]
            ]
        ];
    }
}

class F2 implements VariablesInterface
{
    function getVariables($experimentType)
    {
        if ($experimentType){
            return 'Dataset\TestFunctions\Range10';
        }

        return [
            'numOfVariables' => 30,
            'ranges' => [
                ['lowerBound' => -10, 'upperBound' => 10]
            ]
        ];
    }
}

class F5 implements VariablesInterface
{
    function getVariables($experimentType)
    {
        if ($experimentType){
            return 'Dataset\TestFunctions\Range30';
        }

        return [
            'numOfVariables' => 30,
            'ranges' => [
                ['lowerBound' => -30, 'upperBound' => 30]
            ]
        ];
    }
}

class F7 implements VariablesInterface
{
    function getVariables($experimentType)
    {
        if ($experimentType){
            return 'Dataset\TestFunctions\Range1Koma28';
        }

        return [
            'numOfVariables' => 30,
            'ranges' => [
                ['lowerBound' => -1.28, 'upperBound' => 1.28]
            ]
        ];
    }
}

class F8 implements VariablesInterface
{
    function getVariables($experimentType)
    {
        if ($experimentType){
            return 'Dataset\TestFunctions\Range500';
        }

        return [
            'numOfVariables' => 30,
            'ranges' => [
                ['lowerBound' => -500, 'upperBound' => 500]
            ]
        ];
    }
}

class F9 implements VariablesInterface
{
    function getVariables($experimentType)
    {
        if ($experimentType){
            return 'Dataset\TestFunctions\Range5Koma12';
        }

        return [
            'numOfVariables' => 30,
            'ranges' => [
                ['lowerBound' => -5.12, 'upperBound' => 5.12]
            ]
        ];
    }
}

class F10 implements VariablesInterface
{
    function getVariables($experimentType)
    {
        if ($experimentType){
            return 'Dataset\TestFunctions\Range32';
        }

        return [
            'numOfVariables' => 30,
            'ranges' => [
                ['lowerBound' => -32, 'upperBound' => 32]
            ]
        ];
    }
}

class F11 implements VariablesInterface
{
    function getVariables($experimentType)
    {
        if ($experimentType){
            return 'Dataset\TestFunctions\Range600';
        }

        return [
            'numOfVariables' => 30,
            'ranges' => [
                ['lowerBound' => -600, 'upperBound' => 600]
            ]
        ];
    }
}

class F_12_13 implements VariablesInterface
{
    function getVariables($experimentType)
    {
        if ($experimentType){
            return 'Dataset\TestFunctions\Range50';
        }

        return [
            'numOfVariables' => 30,
            'ranges' => [
                ['lowerBound' => -50, 'upperBound' => 50]
            ]
        ];
    }
}

class UCP implements VariablesInterface
{
    function getVariables($experimentType)
    {
        if ($experimentType){
            return 'Dataset\EffortEstimation\Seeds\ucp';
        }

        return [
            'numOfVariables' => 3,
            'ranges' => [
                ['lowerBound' => 5, 'upperBound' => 7.49],
                ['lowerBound' => 7.5, 'upperBound' => 12.49],
                ['lowerBound' => 12.5, 'upperBound' => 15]
            ]
        ];
    }
}

class COCOMO implements VariablesInterface
{
    function getVariables($experimentType)
    {
        if ($experimentType){
            return 'Dataset\EffortEstimation\Seeds\cocomo';
        }

        return [
            'numOfVariables' => 2,
            'ranges' => [
                ['lowerBound' => 0, 'upperBound' => 10],
                ['lowerBound' => 0.3, 'upperBound' => 2],
            ]
        ];
    }
}

class Agile implements VariablesInterface
{
    function getVariables($experimentType)
    {
        if ($experimentType){
            return 'Dataset\EffortEstimation\Seeds\agile';
        }

        return [
            'numOfVariables' => 13,
            'ranges' => [
                ['lowerBound' => 0.91, 'upperBound' => 1],
                ['lowerBound' => 0.89, 'upperBound' => 1],
                ['lowerBound' => 0.96, 'upperBound' => 1],
                ['lowerBound' => 0.85, 'upperBound' => 1],
                ['lowerBound' => 0.91, 'upperBound' => 1],
                ['lowerBound' => 0.96, 'upperBound' => 1],
                ['lowerBound' => 0.90, 'upperBound' => 1],
                ['lowerBound' => 0.98, 'upperBound' => 1],
                ['lowerBound' => 0.98, 'upperBound' => 1],
                ['lowerBound' => 0.96, 'upperBound' => 1],
                ['lowerBound' => 0.95, 'upperBound' => 1],
                ['lowerBound' => 0.97, 'upperBound' => 1],
                ['lowerBound' => 0.98, 'upperBound' => 1]
            ]
        ];
    }
}


class Variables
{
    function initializeVariableFactory($functionToOptimized)
    {
        $functionsToOptimized = [
            ['function' => 'f1', 'select' => new F_1_3_4_6],
            ['function' => 'f2', 'select' => new F2],
            ['function' => 'f3', 'select' => new F_1_3_4_6],
            ['function' => 'f4', 'select' => new F_1_3_4_6],
            ['function' => 'f5', 'select' => new F5],
            ['function' => 'f6', 'select' => new F_1_3_4_6],
            ['function' => 'f7', 'select' => new F7],
            ['function' => 'f8', 'select' => new F8],
            ['function' => 'f9', 'select' => new F9],
            ['function' => 'f10', 'select' => new F10],
            ['function' => 'f11', 'select' => new F11],
            ['function' => 'f12', 'select' => new F_12_13],
            ['function' => 'f13', 'select' => new F_12_13],
            ['function' => 'ucp', 'select' => new UCP],
            ['function' => 'cocomo', 'select' => new COCOMO],
            ['function' => 'agile', 'select' => new Agile]
        ];
        $index = array_search($functionToOptimized, array_column($functionsToOptimized, 'function'));
        return $functionsToOptimized[$index]['select'];
    }
}