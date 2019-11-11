<?php
namespace lecodeurdudimanche\DocumentDataExtractor;

use thiagoalessio\TesseractOCR\TesseractOCR;

class Extractor {

    private $config;
    private $image;
    private $imageIsFile;

    public function __construct()
    {
        $this->config = null;
        $this->image = null;
        $this->imageSize = 0;
        $this->imageIsFile = false;
    }

    public function loadConfig(string $configPath) : Extractor
    {
        $config = Configuration::fromFile($configPath);
        $this->setConfig($config);

        return $this;
    }

    public function setConfig($config) : Extractor
    {
        if (is_array($config))
            $config = Configuration::fromArray($config);

        //TODO: check config ?
        $this->config = $config;

        return $this;
    }

    public function loadPDF(string $pdfPath, int $page = 0, ?int $resolution = null) : Extractor
    {
        $imagick = new \Imagick();
        $imagick->readImage("${pdfPath}[${page}]");
        if ($resolution)
            $imagick->setImageResolution($resolution, $resolution);

        $imagick->setImageFormat("png");
        return $this->setImage($imagick);
    }

    public function loadImage(string $imagePath) : Extractor
    {
        //TODO: check path ?
        $this->image = $imagePath;
        $this->imageIsFile = true;

        return $this;
    }

    /**
    * Set the document to use
    * @param image a Imagick or GD object, or raw image data of a format supported by Imagick
    **/
    public function setImage($image) : Extractor
    {
        if ($image instanceof \Imagick)
            $this->image = $image;
        else
        {
            if (is_resource($image) && get_resource_type($image) == "gd")
            {
                ob_start();
                imagepng($img, null, 0);
                $image = ob_get_clean();
            }

            $image = new \Imagick();
            $this->image->readFromBlob($image);
        }

        return $this;
    }

    public function run() : array
    {
        $data = [];
        foreach($this->config->getRegionsOfInterest() as $roi)
        {
            $imageROI = $roi->extractROI($this->image);
            $tesseract = new TesseractOCR();
            $tesseract->imageData($imageROI->getImageBlob(), $imageROI->getImageLength());
            $this->config->configureTesseract($tesseract, $roi->getDataType());
            $text = $tesseract->run();

            $data[] = ["label" => $roi->getLabel(), "type" => $roi->getDataType(), "data" => $text];
        }
        return $data;
    }
}
