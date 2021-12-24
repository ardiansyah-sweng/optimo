<?php

namespace Dataset;

class Data
{
    public $path;

    function getFileNames()
    {
        $files = array_diff(scandir($this->path), array('.', '..'));
        foreach ($files as $fileName) {
            $ret[] = $this->path . '' . $fileName;
        }
        return $ret;
    }
}
