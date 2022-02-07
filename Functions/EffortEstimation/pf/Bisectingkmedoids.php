<?php
# read data set
require_once "dataset.php";

/**
 * Bisecting k-Medoids Clustering
 * 
 * @2020 Ardiansyah - ardiansyah@tif.uad.ac.id
 * 
 * The class will return N clusters S = {C1, C2, C3, C4, .., CN} along with Medoid tuple for each cluster.
 * The medoid using random approach
 */

class BisectingKMedoids
{
    private $EXPONENT = 2;

    /**
     * Membaca dataset dari file dataset.php
     * Return berupa array dataset utama
     */
    function readDataset($cacah)
    {
        $dataset = new Dataset();
        $temp = $dataset->dataset();
        // Leave One Out - LOO
        foreach ($temp as $key => $val) {
            if ($cacah != $key) {
                $ret[] = $val;
            }
        }
        return $ret;
    }

    /**
     * Membaca dataset dari file dataset.php
     * Return berupa array tupel test data
     */
    function getTestData($cacah): array
    {
        $dataset = new Dataset();
        $temp = $dataset->dataset();
        // Leave One Out - LOO
        foreach ($temp as $key => $val) {
            if ($cacah === $key) {
                return $val;
            }
        }
    }


    /**
     * Membangkitkan medoid secara random
     * Return array Tupel medoid yang terpilih secara acak
     */
    function randomMedoidTuple($arrDataset)
    {
        $randomIndex = array_rand($arrDataset);
        foreach ($arrDataset as $key => $val) {
            if ($key == $randomIndex) {
                return $val;
            }
        }
    }

    # Calculate distance between tuple and data set
    # Return array sum of all tuple in dataset
    # Calculate variance for each cluster
    # variance = 1/n sum of ||x(i)th data object subsctract v(i)th center of C(i)-th cluster||^2
    # Parameter(s) = array of cluster, center of the cluster
    # Return array variance value for each cluster
    function variance($arrDistance)
    {
        return array_sum($arrDistance) / count($arrDistance);
    }

    /**
     * Sementara khusus hitung distance, nanti direfaktor
     */
    function distance($arrDataset, $arrTupleOFMedoids)
    {
        foreach ($arrDataset as $val) {
            $f1 = POW($val['f1'] - $arrTupleOFMedoids['f1'], $this->EXPONENT);
            $f2 = POW($val['f2'] - $arrTupleOFMedoids['f2'], $this->EXPONENT);
            $f3 = POW($val['f3'] - $arrTupleOFMedoids['f3'], $this->EXPONENT);
            $f4 = POW($val['f4'] - $arrTupleOFMedoids['f4'], $this->EXPONENT);
            $f5 = POW($val['f5'] - $arrTupleOFMedoids['f5'], $this->EXPONENT);
            $f6 = POW($val['f6'] - $arrTupleOFMedoids['f6'], $this->EXPONENT);
            $f7 = POW($val['f7'] - $arrTupleOFMedoids['f7'], $this->EXPONENT);
            $f8 = POW($val['f8'] - $arrTupleOFMedoids['f8'], $this->EXPONENT);
            $retDistance[] = $f1 + $f2 + $f3 + $f4 + $f5 + $f6 + $f7 + $f8;
        }
        return $retDistance;
    }

    # Pairing between Project ID and Distance from medoid tuples
    function pairingDistanceAndProjectID($arrDataset, $arrDistance)
    {
        foreach ($arrDataset as $key => $val) {
            $pairing[$key]['projectID'] = $val['project_id'];
            $pairing[$key]['distance'] = $arrDistance[$key];
        }
        return $pairing;
    }

    # Calculate child variance
    # Return array variance of child clusters
    function childVariance($cluster)
    {
        $cacah = 0;
        foreach ($cluster as $key => $val) {
            foreach ($val as $subkey => $subval) {
                $dataPoint = count($subval);
                foreach ($subval as $subsubval) {
                    $cacah = $cacah + $subsubval['distance'];
                }
                $variance = $cacah / $dataPoint;
                $ret[$key][$subkey] = $variance;
                $cacah = 0;
            }
        }
        return $ret;
    }

    # Compare variance of two child cluster with variance of parent cluster
    # Return boolean [split or stop]
    function compareVariance($arrParentVariance, $arrChildVariance)
    {
        foreach ($arrParentVariance as $key => $val) {
            foreach ($arrChildVariance as $subkey => $subval) {
                if ($key == $subkey) {
                    foreach ($subval as $subsubkey => $subsubval) {
                        if ($val < $subsubval) {
                            $ret[$key][$subsubkey] = 'stop';
                        }
                        if ($val > $subsubval) {
                            $ret[$key][$subsubkey] =  'split';
                        }
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * Membuat dua cluster
     * Return dua cluster dengan elemen berisi index
     */
    function createTwoCluster($arrDistanceC1, $arrDistanceC2)
    {
        foreach ($arrDistanceC1 as $key => $val) {
            if ($val < $arrDistanceC2[$key]) {
                $ret['C1'][] = $key;
            } else {
                $ret['C2'][] = $key;
            }
        }
        return $ret;
    }

    function convertingECF($tuples)
    {
        foreach ($tuples as $key => $val) {
            if ($key !== 'project_id' && $key !== 'actual' && $key !== 'size' && $key !== 'actualPF') {
                $ret[] = $val;
            }
        }
        return $ret;
    }

    function convertingECFToNN($tuples, $flag)
    {
        foreach ($tuples as $key => $val) {
            if (($key === 'size' || $key === 'actualPF') && $flag === 0) {
                $ret[] = $val;
            }
            if ($key === 'actual' && $flag === 1) {
                $ret[] = $val;
            }
        }
        return $ret;
    }

    function convertingRaw($cluster, $nn)
    {
        foreach ($cluster as $key => $tuples) {
            if ($nn === 0) {
                $convertedRaws[] = $this->convertingECF($tuples);
            }
            if ($nn === 1) {
                $convertedRaws[] = $this->convertingECFToNN($tuples, 0);
            }
        }
        return $convertedRaws;
    }

    /**
     * Program utama Bisecting K-Medoids
     */
    function bisectingKMedoidsClustering($cacah)
    {
        $X[] = $this->readDataset($cacah);
        $NextLevel = array();
        $V = array(); // Inisialisasi V untuk menampung seluruh array cluster
        $V = array_merge($V, $X); // Assign $V untuk menampung dataset utama. count($V) harusnya > 0
        $S = array();

        // echo 'Initial dataset (belum masuk while):<br>';
        // print_r($V);
        // echo '<p>';

        // Ulangi selama $V > 0
        while (count($V) > 0) {
            // Untuk setiap Cluster dalam array $V, lakukan 
            foreach ($V as $val) {
                // Hitung varians tiap Cluster
                // 1. Ambil medoid acak terlebih dahulu dari dataset
                // 2. Hitung varians
                $arrRandomMedoidTupleParent = $this->randomMedoidTuple($val);
                $parentDistance = $this->distance($val, $arrRandomMedoidTupleParent);
                $parentVariance = $this->variance($parentDistance);
                // Split menjadi dua Cluster (C1, C2)
                $arrRandomMedoidTupleC1 = $this->randomMedoidTuple($val);
                $arrRandomMedoidTupleC2 = $this->randomMedoidTuple($val);
                $arrDistanceC1 = $this->distance($val, $arrRandomMedoidTupleC1);
                $arrDistanceC2 = $this->distance($val, $arrRandomMedoidTupleC2);
                $varianceC1 = $this->variance($arrDistanceC1);
                $varianceC2 = $this->variance($arrDistanceC2);
                $twoClusterC1C2 = $this->createTwoCluster($arrDistanceC1, $arrDistanceC2);
                // print_r($twoClusterC1C2);

                // $C1 = $this->pairingDistanceAndProjectID($val, $arrDistanceC1);
                // $C2 = $this->pairingDistanceAndProjectID($val, $arrDistanceC2);
                // Jika MAX($variance C1, $variance C2) < $parent Variance, maka array $NextLevel diisi 2 cluster C1, C2 tersebut
                // Sehingga size count array $V bertambah, dan tetap > 0, sehingga while terus berulang
                if (max($varianceC1, $varianceC2) < $parentVariance) {
                    foreach ($twoClusterC1C2 as $vals) {
                        //echo '<br>' . $key . '<br>';
                        foreach ($vals as $subval) {
                            //echo $subval . '&nbsp;';
                            // print_r($val[$subval]);
                            // echo '<br>';
                            $temp[] = $val[$subval];
                        }
                        //echo '<br>';
                        // echo '<p>';
                        // print_r($temp);
                        $NextLevel[] = $temp;
                        $temp = [];
                    }
                    //echo 'Split<br>';
                } else {
                    // print_r($arrRandomMedoidTupleParent);
                    $S[] = $val;
                    //print_r($S);
                    //echo '<p>';
                    $arrMedoidForAllClusters[] = $arrRandomMedoidTupleParent;
                    //echo 'Stop<br>';
                }
            }

            $V = $NextLevel;
            // echo 'Next level<br>';
            // print_r($NextLevel);

            //$V = []; // Mengosongkan array $V supaya count($V) == 0. Sehingga While berhentin
            $NextLevel = [];
        }
        // echo '<p>';
        // echo '<h4>Data ke-' . $cacah . '. Final clusters = ' . count($S) . '</h4>';
        // echo 'Medoids:<br>';
        // echo '<p></p>';

        // foreach ($arrMedoidForAllClusters as $vals){
        //     print_r($vals);
        //     echo '<br>';
        // }

        //echo '<p>';
        $ret['medoids'] = $arrMedoidForAllClusters;
        $ret['clusters'] = $S;
        return $ret;

        //return $klasters;
        //print_r($S);
        //echo '<br>';
        // echo '<p>';
        // echo '<h4>Data ke-' . $cacah . '. Final clusters = ' . count($S) . '</h4>';
        // echo 'Medoids:';
        // echo "<br>";
        // print_r($arrMedoidForAllClusters[0]);
        // echo "<p>";
        // print_r($S[0]);
        // echo "<br>";

        // foreach ($arrMedoidForAllClusters as $key => $medoid){
        // echo 'Predicted PF '. $medoid['actualPF'];
        // echo "<br>";
        // $S[$key]['predictedPF'] = $medoid['actualPF'];
        // print_r($S[$key]);
        // echo "<p>";
        // }
        // return $S;
        // echo "<p>";
        // print_r($S);
        // foreach ($S as $key => $val){
        //     print_r($val);
        //     echo '<p>';
        // }
    }
}

class Utils
{
    function transpose($dataNN)
    {
        foreach ($dataNN as $vals) {
            $ret[] = $vals[0];
            $rett[] = $vals[1];
            $rettt[] = $vals[2];
        }
        $rets[] = $ret;
        $rets[] = $rett;
        $rets[] = $rettt;

        return $rets;
    }

    function sumKuadrat($columns)
    {
        foreach ($columns as $val) {
            $kuadrat[] = pow($val, 2);
        }
        return array_sum($kuadrat);
    }

    function multiplication($column1, $column2)
    {
        foreach ($column1 as $key => $val) {
            $ret[] = $val * $column2[$key];
        }
        return array_sum($ret);
    }

    function matrixMultiplication($actualY, $transposedDataNN)
    {
        $r0c0 = array_sum($transposedDataNN[0]);
        $r0c1 = array_sum($transposedDataNN[1]);
        $r0c2 = array_sum($transposedDataNN[2]);

        $r1c0 = $r0c1;
        $r1c1 = $this->sumKuadrat($transposedDataNN[1]);
        $r1c2 = $this->multiplication($transposedDataNN[1], $transposedDataNN[2]);

        $r2c0 = $r0c2;
        $r2c1 = $r1c2;
        $r2c2 = $this->sumKuadrat($transposedDataNN[2]);

        $XtransposeX = [
            [$r0c0, $r0c1, $r0c2],
            [$r1c0, $r1c1, $r1c2],
            [$r2c0, $r2c1, $r2c2]
        ];

        $XtransposeY0 = array_sum($actualY);
        $XtransposeY1 = $this->multiplication($actualY, $transposedDataNN[1]);
        $XtransposeY2 = $this->multiplication($actualY, $transposedDataNN[2]);

        $XtransposeY = [$XtransposeY0, $XtransposeY1, $XtransposeY2];

        $matrixLibrary = new MatrixLibrary();
        $XtransposeXinverse = $matrixLibrary->inverseMatrix($XtransposeX);

        foreach ($XtransposeXinverse as $vals) {
            $w[] = $this->multiplication($XtransposeY, $vals);
        }

        return $w;
    }
}

class MatrixLibrary
{
    //Gauss-Jordan elimination method for matrix inverse
    public function inverseMatrix(array $matrix)
    {
        //TODO $matrix validation

        $matrixCount = count($matrix);

        $identityMatrix = $this->identityMatrix($matrixCount);
        $augmentedMatrix = $this->appendIdentityMatrixToMatrix($matrix, $identityMatrix);
        $inverseMatrixWithIdentity = $this->createInverseMatrix($augmentedMatrix);
        $inverseMatrix = $this->removeIdentityMatrix($inverseMatrixWithIdentity);

        return $inverseMatrix;
    }

    private function createInverseMatrix(array $matrix)
    {
        $numberOfRows = count($matrix);

        for ($i = 0; $i < $numberOfRows; $i++) {
            $matrix = $this->oneOperation($matrix, $i, $i);

            for ($j = 0; $j < $numberOfRows; $j++) {
                if ($i !== $j) {
                    $matrix = $this->zeroOperation($matrix, $j, $i, $i);
                }
            }
        }
        $inverseMatrixWithIdentity = $matrix;

        return $inverseMatrixWithIdentity;
    }

    private function oneOperation(array $matrix, $rowPosition, $zeroPosition)
    {
        if ($matrix[$rowPosition][$zeroPosition] !== 1) {
            $numberOfCols = count($matrix[$rowPosition]);

            if ($matrix[$rowPosition][$zeroPosition] === 0) {
                $divisor = 0.0000000001;
                $matrix[$rowPosition][$zeroPosition] = 0.0000000001;
            } else {
                $divisor = $matrix[$rowPosition][$zeroPosition];
            }

            for ($i = 0; $i < $numberOfCols; $i++) {
                $matrix[$rowPosition][$i] = $matrix[$rowPosition][$i] / $divisor;
            }
        }

        return $matrix;
    }

    private function zeroOperation(array $matrix, $rowPosition, $zeroPosition, $subjectRow)
    {
        $numberOfCols = count($matrix[$rowPosition]);

        if ($matrix[$rowPosition][$zeroPosition] !== 0) {
            $numberToSubtract = $matrix[$rowPosition][$zeroPosition];

            for ($i = 0; $i < $numberOfCols; $i++) {
                $matrix[$rowPosition][$i] = $matrix[$rowPosition][$i] - $numberToSubtract * $matrix[$subjectRow][$i];
            }
        }

        return $matrix;
    }

    private function removeIdentityMatrix(array $matrix)
    {
        $inverseMatrix = array();
        $matrixCount = count($matrix);

        for ($i = 0; $i < $matrixCount; $i++) {
            $inverseMatrix[$i] = array_slice($matrix[$i], $matrixCount);
        }

        return $inverseMatrix;
    }

    private function appendIdentityMatrixToMatrix(array $matrix, array $identityMatrix)
    {
        //TODO $matrix & $identityMatrix compliance validation (same number of rows/columns, etc)

        $augmentedMatrix = array();

        for ($i = 0; $i < count($matrix); $i++) {
            $augmentedMatrix[$i] = array_merge($matrix[$i], $identityMatrix[$i]);
        }

        return $augmentedMatrix;
    }

    public function identityMatrix(int $size)
    {
        //TODO validate $size

        $identityMatrix = array();

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                if ($i == $j) {
                    $identityMatrix[$i][$j] = 1;
                } else {
                    $identityMatrix[$i][$j] = 0;
                }
            }
        }

        return $identityMatrix;
    }
}


$bisecting = new BisectingKMedoids();
$util = new Utils;

$cacah = 0;
for ($i = 0; $i < 120; $i++) {
    $rawTestData = $bisecting->getTestData($i);
    $testData = $bisecting->convertingECF($rawTestData);

    $klaster = $bisecting->bisectingKMedoidsClustering($i);

    while ($cacah < 1) {
        if (count($klaster['clusters']) < 2) {
            $klaster = $bisecting->bisectingKMedoidsClustering($i);
            $cacah = 0;
        } else {
            break;
        }
    }

    //echo $i . "<br>";
    foreach ($klaster['clusters'] as $key => $cluster) {
        $rawClusters[] = $bisecting->convertingRaw($cluster, 0);
    }
    foreach ($klaster['clusters'] as $key => $cluster) {
        $rawClustersNN[] = $bisecting->convertingRaw($cluster, 1);
    }
    foreach ($klaster['clusters'] as $cluster) {
        foreach ($cluster as $tupel) {
            $actualY[] = $tupel['actual'];
        }
    }

    foreach ($rawClusters as $key => $klasters) {
        foreach ($klasters as $tupel) {
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

    $dataJson = json_encode($data, 0);
    $labelJson = json_encode($labels, 0);

    $ret['dataTrain'] = $data;
    $ret['dataLabel'] = $labels;
    $ret['gamma'] = 0.72;
    $ret['dataTest'] = $testData;
    $ret['cVal'] = 0.52;

    $rawClusters = [];
    $labels = [];

    $url = 'http://localhost:8000/count';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($ret, 0));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response  = curl_exec($ch);
    curl_close($ch);
    $result = array_values(json_decode($response, true));
    $results = json_decode($response, true);

    foreach ($klaster['medoids'] as $key => $medoid) {
        foreach ($results['values'] as $key1 => $val) {
            if ($key === $val) {
                $dataInput[] = [
                    $key1,
                    $medoid['actualPF'],
                    $rawTestData['size']
                ];
            }
            // $dataTrain = $bisecting->convertingECFToNN()

            $rets['dataTrain'] = $dataNN;
            $rets['dataInput'] = $dataInput;
        }
    }
    //print_r(json_encode($rets,0));

    //print_r($dataNN);
    $transposedDataNN = $util->transpose($dataNN);
    $weights = $util->matrixMultiplication($actualY, $transposedDataNN);
    // print_r($dataInput);die;

    foreach ($dataInput as $input) {
        $estimatedEffort = $weights[0] + ($weights[1] * $input[2]) + ($weights[2] * $input[1]);
        //echo 'Kernel: '. $input[0].' Estimated: ' . $estimatedEffort . ' Actual: ' . $rawTestData['actual'];

        $ae[$i][$input[0]] = abs($estimatedEffort - $rawTestData['actual']);

        //echo '<br>';
    }


    $klaster = [];
    $data = [];
    $dataInput = [];
    $rawClustersNN = [];
    $rets = [];
    $dataNN = [];
    $labelsNN = [];
    $actualY = [];
    //echo "<br> <br>";
}

//echo '<p></p>';
foreach ($ae as $key => $error) {
    $rb[] = $error['Radial Basis'];
    $poly[] = $error['Polynomial'];
    $sig[] = $error['Sigmoid'];
    $linear[] = $error['Linear'];
}

echo "MAE \n";
echo "Radial Basis: " . array_sum($rb) / 120;
echo "\n Polynomial: " . array_sum($poly) / 120;
echo "\n Sigmoid: " . array_sum($sig) / 120;
echo "\n Linear: " . array_sum($linear) / 120;
echo "\n \n";

$rb = [];
$poly = [];
$sig = [];
$linear = [];
