<?php

namespace Tests;

use JunitReports\XmlManager;
use PHPUnit\Framework\TestCase;

/**
 * Test Xml manager
 */
class Xml extends TestCase
{
    public function testGetFailedTests()
    {
        $manager  = new XmlManager();
        $testsDir = __DIR__;

        $tests = $manager->getFailedTests($testsDir . '/data/all_results_with_failture.xml');

        $this->assertArraySubset([
            "AdvertEditCest.php:editAdvertValues",
            "ClaimCest.php:deleteAdvertByClaim",
        ], $tests);
    }


    public function testMergeWithReplace()
    {
        $manager  = new XmlManager();
        $testsDir = __DIR__;

        $testReportTmpFile = $testsDir . '/data/all_results_with_failture_tmp.xml';

        copy($testsDir . '/data/all_results_with_failture.xml', $testReportTmpFile);

        $failedTests = $manager->getFailedTests($testReportTmpFile);

        $this->assertEquals(2, count($failedTests));

        $manager->mergeWithReplace(
            $testsDir . '/data/all_results_with_failture_tmp.xml',
            $testsDir . '/data/all_results.xml'
        );

        $failedTests = $manager->getFailedTests($testReportTmpFile);

        $this->assertEmpty($failedTests);

        unlink($testReportTmpFile);
    }
}