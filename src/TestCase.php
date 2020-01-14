<?php
namespace JunitReports;

class TestCase
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $feature;

    /**
     * @var string
     */
    protected $assertions;

    /**
     * @var string
     */
    private $time;

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param  string $file
     * @return $this
     */
    public function setFile(string $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param  string $class
     * @return $this
     */
    public function setClass(string $class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getFeature(): string
    {
        return $this->feature;
    }

    /**
     * @param  string $feature
     * @return $this
     */
    public function setFeature(string $feature)
    {
        $this->feature = $feature;

        return $this;
    }

    /**
     * @return string
     */
    public function getAssertions(): string
    {
        return $this->assertions;
    }

    /**
     * @param  string $assertions
     * @return $this
     */
    public function setAssertions(string $assertions)
    {
        $this->assertions = $assertions;

        return $this;
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     * @param  string $time
     * @return $this
     */
    public function setTime(string $time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return string
     */
    public function getMethodOfTest(): string
    {
        return sprintf('%s:%s', $this->getClass(), $this->getName());
    }

    public static function fromDomNode(\DOMNode $node)
    {
        $testCase = new self();
        $testCase
            ->setAssertions($node->getAttribute('assertions'))
            ->setClass($node->getAttribute('class'))
            ->setFeature($node->getAttribute('feature'))
            ->setFile($node->getAttribute('file'))
            ->setName($node->getAttribute('name'))
            ->setTime($node->getAttribute('time'));

        return $testCase;
    }
}
