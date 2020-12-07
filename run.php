<?php
require __DIR__ . '/src/XmlManager.php';
require __DIR__ . '/src/TestCase.php';
require __DIR__ . '/src/Exception/XmlException.php';

if (!isset($argv[1])) {
    die('Нужно указать команду');
}

if (!isset($argv[2])) {
    die('Нужно указать файл с XML отчетом');
}

if ($argv[1] === 'getFailedTestCasesAsJson') {
    $manager = new \JunitReports\XmlManager();

    $failedTestCases = $manager->getFailedTestCasesAsArray($argv[2]);

    echo json_encode($failedTestCases, JSON_UNESCAPED_UNICODE);
}



