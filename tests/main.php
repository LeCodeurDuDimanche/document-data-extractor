<?php
namespace lecodeurdudimanche\DocumentDataExtractor\Tests;

use lecodeurdudimanche\DocumentDataExtractor\{ROI, Extractor, Configuration};

require_once("vendor/autoload.php");

class Main {

    public function __invoke($args)
    {
        $path = $args[1] ?? "tests/example.pdf";
        $configuration = Configuration::fromFile("tests/example-config.json");
        $data = (new Extractor())
            ->setConfig($configuration)
            ->loadPDF($path)
            ->run();

        echo json_encode($data);
    }
}

(new Main())($argv);
