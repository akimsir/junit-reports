# junit-reports
Php manager junit tests reports

### Useful case

codeception.dist.yml:
```
groups:
    rerunfailed: Tests/_output/rerunfailed
```

Execute all tests:

```
# produce all_results.xml
vendor/bin/codecept run
```

Then rerun only failed tests and merge with all results

```
$manager = new \JunitReports\XmlManager();

// get failed tests
$failedTests = $manager->getFailedTests('all_results.xml', __DIR__ . '/');

// rerun only failed
$rerunFailedFile = 'Tests/_output/rerunfailed';
file_put_contents($rerunFailedFile, implode(PHP_EOL, $failedTests));
```

Execute only failed:

```
# produce rerunfailed.xml
vendor/bin/codecept run -g rerunfailed
```

Then merge: 
```
$this->parallelRun(['rerunfailed' => []], 'Rerun failed tests');

// merge results with replace 
$manager->mergeWithReplace('all_results.xml', 'rerunfailed.xml');
```

### Run with Docker

docker pull akimsir/junit-reports
docker run -ti --rm  -v /path/to/results.xml:/project/results.xml akimsir/junit-reports "php run.php getFailedTestCasesAsJson /project/results.xml"

Result is the list of failed tests with parameters:

```
[
    {
        "file": "/code/Tests/functional/Backend/VerifiedCest.php", 
        "name": "acceptRequest", 
        "class": "Tests\\Functional\\Backend\\VerifiedCest", 
        "feature": "Одобрить заявку для компании", 
        "assertions": 17, 
        "time": 28.493741
    }
]
```