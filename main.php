<?php
require 'vendor/autoload.php';

$optimizerAlgorithm = 'pso';
$optimizerAlgorithms = ['pso', 'ga', 'ucpso'];
$functionToOptimize = 'ucp';
$functionsToOptimize = ['f1', 'f2', 'f3', 'f4', 'f5', 'f6', 'f7', 'f8', 'f9', 'f10', 'f11', 'f12', 'f13', 'ucp', 'cocomo', 'agile'];

$optimizer = new Optimizers($optimizerAlgorithm, $functionsToOptimize);
print_r($optimizer->getVariables());
