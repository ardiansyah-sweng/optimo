<?php
require 'vendor/autoload.php';

$optimizerAlgorithm = 'cpso';
$optimizerAlgorithms = ['pso', 'cpso', 'ga', 'rao'];
$functionToOptimize = 'ucp';
$functionsToOptimize = ['f1', 'f2', 'f3', 'f4', 'f5', 'f6', 'f7', 'f8', 'f9', 'f10', 'f11', 'f12', 'f13', 'ucp', 'cocomo', 'agile'];
$experimentType = ['evaluation','convergence','normal'];
$variableType = ['random','seeds'];

$optimizer = new Optimizers($optimizerAlgorithms, $functionsToOptimize);
$optimizer->createIndividu();
