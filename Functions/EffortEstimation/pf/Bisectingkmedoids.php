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
            //if ($cacah != $key) {
                $ret[] = $val;
            //}
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

                // $C1 = $this->pairingDistanceAndProjectID($val, $arrDistanceC1);
                // $C2 = $this->pairingDistanceAndProjectID($val, $arrDistanceC2);
                // Jika MAX($variance C1, $variance C2) < $parent Variance, maka array $NextLevel diisi 2 cluster C1, C2 tersebut
                // Sehingga size count array $V bertambah, dan tetap > 0, sehingga while terus berulang
                if (max($varianceC1, $varianceC2) < $parentVariance) {
                    foreach ($twoClusterC1C2 as $vals) {
                        //echo '<br>' . $key . '<br>';
                        foreach ($vals as $subval) {
                            $temp[] = $val[$subval];
                        }
                        $NextLevel[] = $temp;
                        $temp = [];
                    }
                } else {
                    $S[] = $val;
                    $arrMedoidForAllClusters[] = $arrRandomMedoidTupleParent;
                }
            }
            $V = $NextLevel;
            $NextLevel = [];
        }
        $ret['medoids'] = $arrMedoidForAllClusters;
        $ret['clusters'] = $S;
        return $ret;
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

class BisectingKMedoidsGenerator
{
    function clusterGenerator()
    {
        $cacah = 0;
        $bisecting = new BisectingKMedoids();

        $numOfData = count($bisecting->readDataset(0));

        for ($j = 0; $j < $numOfData; $j++) {
            $klasters = $bisecting->bisectingKMedoidsClustering($j);
            while ($cacah < 1) {
                if (count($klasters['clusters']) < 2) {
                    $klasters = $bisecting->bisectingKMedoidsClustering($j);
                    $cacah = 0;
                } else {
                    break;
                }
            }
            $klasterSets[] = $klasters;
        }

        return $klasterSets;
    }
}
