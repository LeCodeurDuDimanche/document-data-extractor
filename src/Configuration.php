<?php
namespace lecodeurdudimanche\DocumentDataExtractor;

use thiagoalessio\TesseractOCR\TesseractOCR;

class Configuration {

    private $tesseractConfiguration = [];
    private $regionsOfInterest = [];

    public function __construct()
    {

    }

    //TODO: Make this more user firendly (errors, hints on possible configuration names/values)
    public function addTesseractConfiguration(string $name, string $value) : Configuration
    {
        $this->tesseractConfiguration[$name] = $value;
        return $this;
    }

    public function addROI(ROI $roi) : Configuration
    {
        $this->regionsOfInterest[] = $roi;
    }

    public function configureTesseract(TesseractOCR $tesseract, string $dataType) : TesseractOCR
    {
        foreach($this->tesseractConfiguration as $key => $value)
            $tesseract->$key($value);

        switch ($dataType) {
            case 'integer':
            case 'float':
                // TODO: add post-processing function
                $tesseract->digits();
                break;
            case 'devise':
                //TODO: Add more missing values
                $tesseract->whitelist(["€", "$", "£"]);
                break;
            case 'address':
                throw Exception("Not implemented");
                break;
            case 'text':
                $tesseract->lang("Latin");
                break;
            default:
                $tesseract->lang($dataType);
                break;
        }
        $tesseract->withoutTempFiles();
        return $tesseract;
    }

    public function getRegionsOfInterest() : array
    {
        // TODO: Deep or shallow copy
        return $this->regionsOfInterest;
    }

    public static function fromArray(array $data) : Configuration
    {
        $config = new Configuration();
        $config->tesseractConfiguration = $data["tesseractConfiguration"];
        $config->regionsOfInterest = $data["regionsOfInterest"];
        return $config;
    }

    public static function fromFile(string $path) : Configuration
    {
        $data = json_decode(file_get_contents($path), true);
        $data['regionsOfInterest'] = array_map(ROI::class . '::fromArray', $data['regionsOfInterest']);
        return self::fromArray($data);
    }

    public function toArray() : array
    {
        return ["tesseractConfiguration" => $this->tesseractConfiguration, "regionsOfInterest" => $this->regionsOfInterest];
    }

    public function toFile(string $path) : bool
    {
        return file_put_contents($path, json_encode($this->toArray(), JSON_PRETTY_PRINT)) !== FALSE;
    }
}
