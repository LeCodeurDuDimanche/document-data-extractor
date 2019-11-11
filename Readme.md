# Document Data Extractor
A simple PHP library to automate data extraction from documents with known formats.

## Requirements

This library uses Tesseract to read text from documents and Imagick to manipulate the images.

It relies on GhostScript (`gs`) to convert pdf files to images.

## Usage

First, you'll need to define what data you want to extract and where it is on the image :
```php
    $extractor = new Extractor();
    $regionsOfInterest = [
        // The name of the company is in the rectangle with the top left corner (150, 30) and a size of (100, 20)
        new ROI('Name of the company')->setRect(150, 30, 100, 20),
        new ROI('Total', 'integer')->setRect(200, 80, 30, 20);
    ];
```
Next you can add some options forwarded to tesseract in order to get more precise results :
```php
    $tesseractConfiguration = [
        'psm' => 8, // Page segmentation method is set to 8 (single word)
        'tessdataDir' => '/usr/share/tessdata_fast' // Other tesseract options ...
    ];
    $config = Configuration::fromArray(compact('regionsOfInterest', 'tesseractConfiguration'));
    $extractor->setConfig($config);
```
Then you set the document you want to extract data from :
```php
    $extractor->loadImage('/path/to/image.png'); // or
    $extractor->loadPDF('/path/to/document.pdf'); // or
    $extractor->setImage($imageData); // could be an Imagick or GD image or raw image data
```
And finally you call the `run()` method to extract the data :
```php
    $data = $extractor->run();
    /*
    * $data = [
    * ['label' => 'Name of the company', 'dataType' => 'text', 'data' => 'Company Limited'],
    * ['label' => 'Total', 'dataType' => 'integer', 'data' => '125']
    * ];
    */
```

You can save and load a `Configuration` object with the `toFile` and `fromFile` methods.
The file format is pretty formatted JSON.
