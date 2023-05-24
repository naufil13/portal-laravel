<?php

class _Image extends Gregwar\Image\Image
{

    protected $cacheDir = 'assets/cache';

    function __construct($originalFile = null, $width = null, $height = null)
    {
        $this->setCacheDir('assets/cache');

        $filename = explode('.', urlencode(end(explode('/', $originalFile))))[0];
        $this->setPrettyName($filename);

        parent::__construct($originalFile, $width, $height);
    }



    public function show()
    {
            return asset_url($this->cacheFile('png', 100));
    }

}

