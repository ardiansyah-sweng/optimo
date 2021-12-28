<?php

interface PathToResultInterface
{
    function getPathToResult();
}

class GAPath implements PathToResultInterface
{
    function getPathToResult()
    {
        return 'results/ga.txt';
    }
}

class PSOPath implements PathToResultInterface
{
    function getPathToResult()
    {
        return 'results/pso.txt';
    }
}

class UCPSOPath implements PathToResultInterface
{
    function getPathToResult()
    {
        return 'results/ucpso.txt';
    }
}

class MyPSO1Path implements PathToResultInterface
{
    function getPathToResult()
    {
        return 'results/mypso1.txt';
    }
}

class Paths
{
    function initializePath($type)
    {
        if ($type === 'ga'){
            return (new GAPath())->getPathToResult();
        }
        if ($type === 'pso') {
            return (new PSOPath())->getPathToResult();
        }
        if ($type === 'ucpso') {
            return (new UCPSOPath())->getPathToResult();
        }
        if ($type === 'mypso1') {
            return (new MyPSO1Path())->getPathToResult();
        }
    }
}