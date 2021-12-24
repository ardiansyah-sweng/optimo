<?php

class DirectoryScanner
{
    public $pathToDirectory;

    function getFileNames()
    {
        $files = array_diff(scandir($this->pathToDirectory), array('.', '..'));
        foreach ($files as $fileName) {
            $ret[] = $this->pathToDirectory . '/' . $fileName;
        }
        return $ret;
    }
}