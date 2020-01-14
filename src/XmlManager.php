<?php

namespace JunitReports;

use JunitReports\Exception\XmlException;

/**
 * Мененджемент junit xml отчётов.
 */
class XmlManager
{

    /**
     * Объединение отчётов с перезаписью результов тестов.
     *
     * @param $mainFile
     * @param $file
     *
     * @throws XmlException
     */
    public function mergeWithReplace($mainFile, $file)
    {
        // достаём имена перезапущенных тестов
        $reportDocument = $this->loadXmlFile($file);

        $suiteNodes          = (new \DOMXPath($reportDocument))->query('//testsuites/testsuite/testcase');
        $testsToReplaceNames = [];
        foreach ($suiteNodes as $suiteNode) {
            /** @var $suiteNode \DOMElement  * */
            $testsToReplaceNames[] = $suiteNode->getAttribute('name');
        }

        if (empty($testsToReplaceNames)) {
            throw new XmlException('No tests reports to merge');
        }

        // remove existed tests records
        $allTestsReportDocument = $this->loadXmlFile($mainFile);
        $suiteNodes             = (new \DOMXPath($allTestsReportDocument))->query('//testsuites/testsuite/testcase');

        foreach ($suiteNodes as $suiteNode) {
            if (in_array($suiteNode->getAttribute("name"), $testsToReplaceNames)) {
                $suiteNode->parentNode->removeChild($suiteNode);
            }
        }

        $allTestsReportDocument->save($mainFile);

        $this->merge($mainFile, $file);
    }

    /**
     * @param  string                               $file
     * @param  string                               $cutPrefix
     * @return string[]
     * @throws \JunitReports\Exception\XmlException
     */
    public function getFailedTests($file, $cutPrefix = '')
    {
        $testCases = $this->getFailedTestCases($file);
        $result    = [];

        foreach ($testCases as $case) {
            $fileName = $case->getFile();

            if ($cutPrefix) {
                $fileName = str_replace($cutPrefix, '', $fileName);
            }

            $result[] = $fileName . ':' . $case->getName();
        }
        
        return $result;
    }

    /**
     * @param  string                               $file
     * @return TestCase[]
     * @throws \JunitReports\Exception\XmlException
     */
    public function getFailedTestCases($file)
    {
        $document   = $this->loadXmlFile($file);
        $suiteNodes = (new \DOMXPath($document))
            ->query('//testsuites/testsuite/testcase/failure|//testsuites/testsuite/testcase/error');
        $result     = [];

        foreach ($suiteNodes as $suiteNode) {
            $testCase = TestCase::fromDomNode($suiteNode->parentNode);
            $result[$testCase->getMethodOfTest()] = $testCase;
        }

        return $result;
    }

    /**
     * Мержит первый файл с остальными. Результатом будет первый файл содержащий в себе другие файлы.
     *
     * @param array ...$files
     */
    public function merge(... $files)
    {
        $resultDocument = new \DOMDocument();
        $resultDocument->appendChild($resultDocument->createElement('testsuites'));

        $resultNodes = [];
        $resultFile  = null;

        foreach ($files as $file) {
            if (!$resultFile) {
                $resultFile = $file;
            }

            $document   = $this->loadXmlFile($file);
            $suiteNodes = (new \DOMXPath($document))->query('//testsuites/testsuite');

            foreach ($suiteNodes as $suiteNode) {
                $suiteNode = $resultDocument->importNode($suiteNode, true);

                /** @var $suiteNode \DOMElement  * */
                $suiteName = $suiteNode->getAttribute('name');

                if (!isset($resultNodes[$suiteName])) {
                    $resultNode = $resultDocument->createElement("testsuite");
                    $resultNode->setAttribute('name', $suiteName);
                    $resultNodes[$suiteName] = $resultNode;
                }

                $this->mergeSuites($resultNodes[$suiteName], $suiteNode);
            }
        }

        foreach ($resultNodes as $suiteNode) {
            $resultDocument->firstChild->appendChild($suiteNode);
        }

        $resultDocument->save($resultFile);
    }


    /**
     * @param \DOMElement $resulted
     * @param \DOMElement $current
     */
    protected function mergeSuites(\DOMElement $resulted, \DOMElement $current)
    {
        foreach (['tests', 'assertions', 'failures', 'errors'] as $attr) {
            $sum = (int) $current->getAttribute($attr) + (int) $resulted->getAttribute($attr);
            $resulted->setAttribute($attr, $sum);
        }

        $resulted->setAttribute(
            'time',
            (float) $current->getAttribute('time') + (float) $resulted->getAttribute('time')
        );

        /** @var \DOMNode $node */
        foreach ($current->childNodes as $node) {
            $resulted->appendChild($node->cloneNode(true));
        }
    }

    /**
     * @param $file
     *
     * @return \DOMDocument
     * @throws XmlException
     */
    protected function loadXmlFile($file)
    {
        if (!file_exists($file)) {
            throw new XmlException('File "' . $file . '" does not exist');
        }

        $document = new \DOMDocument();

        if (!$document->load($file)) {
            throw new XmlException('File "' . $file . '" can not be loaded as XML');
        }

        return $document;
    }
}
