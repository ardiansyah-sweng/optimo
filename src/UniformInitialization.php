<?php

interface UniformInitialization
{
    function initializingSwarm();
}

class UCPSO implements UniformInitialization
{
    function initializingSwarm()
    {
        return 'hai';
    }
}

class UniformFactory
{
    function initializingUniform($type)
    {
        if ($type === 'ucpso' || 'mucpso'){
            return (new UCPSO())->initializingSwarm();
        }
    }
}