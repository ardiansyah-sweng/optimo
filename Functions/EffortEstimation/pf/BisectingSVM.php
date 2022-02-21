<?php

class BisectingSVM
{
    function runBisectingSVM($cValue, $gamma, $klasterSets)
    {
        $bisecting = new BisectingKMedoids();
        $saveFile = new FileSaver;
        $util = new Utils;

        $jumCluster = count($klasterSets);
        for ($i = 0; $i < $jumCluster; $i++) {
            $rawTestData = $bisecting->getTestData($i);
            $testData = $bisecting->convertingECF($rawTestData);

            foreach ($klasterSets[0]['clusters'] as $key => $cluster) {
                $rawClusters[] = $bisecting->convertingRaw($cluster, 0);
            }

            foreach ($klasterSets[0]['clusters'] as $key => $cluster) {
                $rawClustersNN[] = $bisecting->convertingRaw($cluster, 1);
            }

            foreach ($klasterSets[0]['clusters'] as $cluster) {
                foreach ($cluster as $tupel) {
                    $actualY[] = $tupel['actual'];
                }
            }

            foreach ($rawClusters as $key => $klasters) {
                foreach ($klasters as $key1 => $tupel) {
                    $labels[] = $key;
                    $data[] = $tupel;
                }
            }

            foreach ($rawClustersNN as $key => $klasters) {
                foreach ($klasters as $tupel) {
                    $labelsNN[] = $key;
                    array_unshift($tupel, 1); // jika mau pakai regression matrix
                    $dataNN[] = $tupel;
                }
            }

            //$dataJson = json_encode($data, 0);
            //$labelJson = json_encode($labels, 0);

            $ret['dataTrain'] = $data;
            $ret['dataLabel'] = $labels;
            $ret['gamma'] = $gamma;
            $ret['dataTest'] = $testData;
            $ret['cVal'] = $cValue;

            $kernel = "rbf";
            $predictedClusterNo = (new API())->getAPI($kernel, $ret);
            $rawClusters = [];
            $counter = 0;

            while ($counter < 1) {
                if ($predictedClusterNo >= count($klasterSets[0]['medoids'])) {
                    // $klasterSets = [];
                    // $ret = [];
                    // $rawClusters = [];
                    // $klasterSets = (new BisectingKMedoidsGenerator())->clusterGenerator();
                    // foreach ($klasterSets[0]['clusters'] as $key => $cluster) {
                    //     $rawClusters[] = $bisecting->convertingRaw($cluster, 0);
                    // }

                    // foreach ($rawClusters as $key => $klasters) {
                    //     foreach ($klasters as $tupel) {
                    //         $labels[] = $key;
                    //         $data[] = $tupel;
                    //     }
                    // }
                    // $ret['dataTrain'] = $data;
                    // $ret['dataLabel'] = $labels;
                    // $ret['gamma'] = $gamma;
                    // $ret['dataTest'] = $testData;
                    // $ret['cVal'] = $cValue;

                    $predictedClusterNo = (new API())->getAPI($kernel, $ret);
                    $counter = 0;
                } else {
                    $medoid = $klasterSets[0]['medoids'][$predictedClusterNo];
                    $counter = 1;
                }
            }

            // $predictAccuracy = (new EvaluationMeasure($rawTestData, $klasterSets[$i]['clusters'][$predictedClusterNo]));

            $dataInputs = [
                $rawTestData['actual'],
                $medoid['actualPF'],
                $rawTestData['size']
            ];

            $transposedDataNN = $util->transpose($dataNN);
            $weights = $util->matrixMultiplication($actualY, $transposedDataNN);

            $estimatedEffort = $weights[0] + ($weights[1] * $dataInputs[2]) + ($weights[2] * $dataInputs[1]);
            $ae = abs($estimatedEffort - $rawTestData['actual']);
            $errors[] = $ae;
            //$saveFile->saveToFile('results\normalSVM.txt', array($ae));

            //$dataNN = [];
            $dataInputs = []; //comment untuk SGD
            $rawClustersNN = [];
            $labelsNN = [];
        }
        $mae = array_sum($errors) / $jumCluster;
        //$saveFile->saveToFile('results\normalSVM.txt', array($mae));
        return $mae;

        // $accuracy = $temp / $jumCluster;
        // $saveFile->saveToFile('results\normalSVM.txt', array($accuracy));
        // return $accuracy;

        // $dataNormalization = new MinMaxScaler;
        // $dataNormalization->dataset = $dataInputs;
        // $normalDataInputs = $dataNormalization->normalization();

        // $sgdOptimizer = new StochasticGD;
        // $sgdOptimizer->normalizedDataset = $normalDataInputs;
        // $normalRes = $sgdOptimizer->dataProcessing();
        // $originalResults = $dataNormalization->denormalizing($normalDataInputs, $normalRes, $dataInputs);
        // return $originalResults;
    }
}
