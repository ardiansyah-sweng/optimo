<?php

class Paths
{
    function initializePath($type)
    {
        if ($type === 'ga'){
            return 'results/ga.txt';
        }
        if ($type === 'pso') {
            return 'results/pso.txt';
        }
        if ($type === 'ucpso') {
            return 'results/ucpso.txt';
        }
        if ($type === 'mypso1') {
            return 'results/mypso1.txt';
        }
        if ($type === 'mypso2') {
            return 'results/mypso2.txt';
        }
        if ($type === 'mypso3') {
            return 'results/mypso3.txt';
        }
        if ($type === 'komodo') {
            return 'results/komodo.txt';
        }

    }
}