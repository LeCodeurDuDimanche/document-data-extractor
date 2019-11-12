<?php
namespace lecodeurdudimanche\DocumentDataExtractor\Tests\EndToEnd;

use PHPUnit\Framework\TestCase;

use lecodeurdudimanche\DocumentDataExtractor\Extractor;

final class DocumentDataExtractorTest extends TestCase
{
    private $expectedData = [
        ['label' => 'Name of the company', 'type' => 'text', 'data' => 'Company Limited'],
        ['label' => 'Total', 'type' => 'integer', 'data' => '55']
    ];
    private $imagePath = __DIR__ . "/data/image.png";
    private $documentPath = __DIR__ . "/data/document.pdf";
    private $configPath = __DIR__ . "/data/example-config.json";
    private $pdfConfigPath = __DIR__ . "/data/pdf-config.json";


    public function testRunWithGDImage()
    {
        $image = imagecreatefrompng($this->imagePath);
        $actualData = (new Extractor())
                    ->loadConfig($this->configPath)
                    ->setImage($image)
                    ->run();
        $this->assertEquals($this->expectedData, $actualData);
    }

    public function testRunWithPDF()
    {
        $actualData = (new Extractor())
                    ->loadConfig($this->pdfConfigPath)
                    ->loadPDF($this->documentPath)
                    ->run();
        $this->assertEquals($this->expectedData, $actualData);
    }

    public function testRunWithRawImageData()
    {
        $imageData = file_get_contents($this->imagePath);
        $actualData = (new Extractor())
                    ->loadConfig($this->configPath)
                    ->setImage($imageData)
                    ->run();
        $this->assertEquals($this->expectedData, $actualData);
    }

    public function testRunWithImagickImage()
    {
        $image = new \Imagick($this->imagePath);
        $actualData = (new Extractor())
                    ->loadConfig($this->configPath)
                    ->setImage($image)
                    ->run();
        $this->assertEquals($this->expectedData, $actualData);
    }

    public function testRunLoadingImage()
    {
        $actualData = (new Extractor())
                    ->loadConfig($this->configPath)
                    ->loadImage($this->imagePath)
                    ->run();
        $this->assertEquals($this->expectedData, $actualData);
    }
}
