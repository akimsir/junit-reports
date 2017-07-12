<?php

class Xml
{

    protected $reRunnedTestsReport;

    protected $allTestsReport;

    /**
     * Объединение отчтётов перезапуска зафеленых тестов и всех тестов.
     *
     * @throws Exception
     */
    protected function mergeFailedTestsReports()
    {
        if (!file_exists($this->reRunnedTestsReport)) {
            // если отчётов по зафейленым тестам нет ничего не делаем
            $this->say('Failed tests report not exist');

            return;
        } else {
            $this->say('Exist failed tests report - try to merge with all');
        }

        //!!!


        // достаём имена перезапущенных тестов
        $filedTestsDocument = $this->loadXmlFile($this->reRunnedTestsReport);

        $suiteNodes       = (new \DOMXPath($filedTestsDocument))->query('//testsuites/testsuite/testcase');
        $failedTestsNames = [];
        foreach ($suiteNodes as $suiteNode) {
            /** @var $suiteNode \DOMElement  * */
            $failedTestsNames[] = $suiteNode->getAttribute('name');
        }

        if (empty($failedTestsNames)) {
            $this->say('No tests reports to merge');

            return;
        }


        // выпиливаем из отчёта по тестам все тесты, которые перезапускались
        $allTestsDocument = $this->loadXmlFile($this->allTestsReport);
        $suiteNodes       = (new \DOMXPath($allTestsDocument))->query('//testsuites/testsuite/testcase');

        foreach ($suiteNodes as $suiteNode) {
            if (in_array($suiteNode->getAttribute("name"), $failedTestsNames)) {
                $suiteNode->parentNode->removeChild($suiteNode);
            }
        }

        $allTestsDocument->save($this->allTestsReport);

        // смержим перезапущенные тесты

        $this->merge($this->allTestsReport, $this->reRunnedTestsReport);


        // $this->say('Done: failed test merged with all');
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
            //$this->printTaskInfo("Processing $file");

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
        //$this->printTaskInfo("File <info>{$this->dst}</info> saved. " . count($resultNodes) . ' suites added');
    }


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
     * @return DOMDocument
     * @throws Exception
     */
    protected function loadXmlFile($file)
    {
        $document = new \DOMDocument();

        if (!file_exists($file)) {
            throw new \Exception('File "' . $file . '" does not exist');
        }

        if (!$document->load($file)) {
            throw new \Exception('File "' . $file . '" can not be loaded as XML');
        }

        return $document;
    }
}