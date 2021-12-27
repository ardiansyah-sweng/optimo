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
        return 'results/pso.txt';
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
    }
}