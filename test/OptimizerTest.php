<?php
require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class OptimizerTest extends TestCase
{
    function test_updating()
    {
        $optimizer = new Optimizers;
        $optimizer->experimentType = 'normal';
        $optimizer->algorithm = 'pso';
        $optimizer->updating();
        die;
    }
}