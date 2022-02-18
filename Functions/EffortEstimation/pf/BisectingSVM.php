<?php

class BisectingSVM
{
    function runBisectingSVM($cValue, $gamma, $klasterSets)
    {
        $bisecting = new BisectingKMedoids();
        $saveFile = new FileSaver;

        $jumCluster = 100;
        $temp = 0;
        for ($i = 0; $i < $jumCluster; $i++) {
            
            $rawTestData = $bisecting->getTestData($i);
            $testData = $bisecting->convertingECF($rawTestData);

            foreach ($klasterSets[$i]['clusters'] as $key => $cluster) {
                $rawClusters[] = $bisecting->convertingRaw($cluster, 0);
            }

            foreach ($klasterSets[$i]['clusters'] as $key => $cluster) {
                $rawClustersNN[] = $bisecting->convertingRaw($cluster, 1);
            }

            foreach ($klasterSets[$i]['clusters'] as $cluster) {
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

            $kernel = "Sigmoid";

            $predictedClusterNo = (new API())->getAPI($kernel, $ret);
            $rawClusters = [];
            $counter = 0;
            
            echo 'Predicted cluster No.- '.$predictedClusterNo .' Jum cluster: '. count($klasterSets[$i]['medoids'])."\n";

            while ($counter < 1){
                if ($predictedClusterNo >= count($klasterSets[$i]['medoids'])){
                    die;
                    $klasterSets = [];
                    $ret = [];
                    $rawClusters = [];
                    $klasterSets = (new BisectingKMedoidsGenerator())->clusterGenerator();
                    foreach ($klasterSets[$i]['clusters'] as $key => $cluster) {
                        $rawClusters[] = $bisecting->convertingRaw($cluster, 0);
                    }

                    foreach ($rawClusters as $key => $klasters) {
                        foreach ($klasters as $tupel) {
                            $labels[] = $key;
                            $data[] = $tupel;
                        }
                    }
                    $ret['dataTrain'] = $data;
                    $ret['dataLabel'] = $labels;
                    $ret['gamma'] = $gamma;
                    $ret['dataTest'] = $testData;
                    $ret['cVal'] = $cValue;

                    $predictedClusterNo = (new API())->getAPI($kernel, $ret);
                    $counter = 0;

                    $saveFile->saveToFile('results\normalSVM.txt', array($i));
 

                } else {
                    $medoid = $klasterSets[$i]['medoids'][$predictedClusterNo];
                    $counter = 1;
                }
            }
            $dataInputs[] = [
                $rawTestData['actual'],
                $medoid['actualPF'],
                $rawTestData['size']
            ];
            
            $predictAccuracy = (new EvaluationMeasure($rawTestData, $klasterSets[$i]['clusters'][$predictedClusterNo]));
            $temp += $predictAccuracy->accuracyCalc();

            // $transposedDataNN = $util->transpose($dataNN);
            // $weights = $util->matrixMultiplication($actualY, $transposedDataNN);

            // foreach ($dataInput as $input) {
            //     $estimatedEffort = $weights[0] + ($weights[1] * $input[2]) + ($weights[2] * $input[1]);
            //     $ae[$i][$input[0]] = abs($estimatedEffort - $rawTestData['actual']);
            // }

            //$dataNN = [];
            $rawClustersNN = [];
            $labelsNN = [];
        }
        $accuracy = $temp / $jumCluster;
        $saveFile->saveToFile('results\normalSVM.txt', array($accuracy));
        return $accuracy;

        $dataNormalization = new MinMaxScaler;
        $dataNormalization->dataset = $dataInputs;
        $normalDataInputs = $dataNormalization->normalization();

        $sgdOptimizer = new StochasticGD;
        $sgdOptimizer->normalizedDataset = $normalDataInputs;
        $normalRes = $sgdOptimizer->dataProcessing();
        $originalResults = $dataNormalization->denormalizing($normalDataInputs, $normalRes, $dataInputs);
        return $originalResults;
    }
}

