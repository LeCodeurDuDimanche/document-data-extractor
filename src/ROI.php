<?php
namespace lecodeurdudimanche\DocumentDataExtractor;

class ROI implements \JsonSerializable
{

    /**
     * The type of the data located in this region of interest.
     * Can be 'integer', 'float', 'devise', 'address' (?), 'text' or a three character language code for generic text in this language
     **/
    private $dataType;

    /**
     * A unique identifier for the data
     **/
    private $label;

    /**
     * The area of this ROI
     **/
    private $rect;


    public function __construct(string $label, string $dataType = 'text')
    {
        $this->label = $label;
        $this->dataType = $dataType;
        $this->rect = array(0, 0, 0, 0);
    }

    public static function fromArray(array $array): ROI
    {
        list($x, $y, $w, $h) = $array["rect"];
        return (new ROI($array["label"], $array["dataType"]))->setRect($x, $y, $w, $h);
    }

    public function jsonSerialize() : array
    {
        return ["label" => $this->label, "dataType" => $this->dataType, "rect" => $this->rect];
    }

    public function setRect(float $x, float $y, float $w, float $h) : ROI
    {
        $this->rect = [$x, $y, $w, $h];
        return $this;
    }

    public function getLabel() : string
    {
        return $this->label;
    }

    public function getDataType() : string
    {
        return $this->dataType;
    }

    public function extractROI(\Imagick $image) : \Imagick
    {
        return $image->getImageRegion($this->rect[2], $this->rect[3], $this->rect[0], $this->rect[1]);
    }

}
