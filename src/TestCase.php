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
     * @var int
     */
    protected $assertions = 0;

    /**
     * @var float
     */
    private $time = 0;

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param string $file
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
     * @param string $name
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
     * @param string $class
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
     * @param string $feature
     * @return $this
     */
    public function setFeature(string $feature)
    {
        $this->feature = $feature;

        return $this;
    }

    /**
     * @return int
     */
    public function getAssertions(): int
    {
        return $this->assertions;
    }

    /**
     * @param int $assertions
     * @return $this
     */
    public function setAssertions(int $assertions)
    {
        $this->assertions = $assertions;

        return $this;
    }

    /**
     * @return float
     */
    public function getTime(): float
    {
        return $this->time;
    }

    /**
     * @param float $time
     * @return $this
     */
    public function setTime(float $time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @param string $cutStr
     * @return string
     */
    public function getRunTestArgument(string $cutStr = ''): string
    {
        return str_replace($cutStr, '', sprintf('%s:%s', $this->getFile(), $this->getName()));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'file'       => $this->file,
            'name'       => $this->name,
            'class'      => $this->class,
            'feature'    => $this->feature,
            'assertions' => $this->assertions,
            'time'       => $this->time,
        ];
    }

    /**
     * @param \DOMNode $node
     * @return \JunitReports\TestCase
     */
    public static function fromDomNode(\DOMNode $node)
    {
        $testCase = new self();
        $testCase
            ->setAssertions((int) $node->getAttribute('assertions'))
            ->setClass($node->getAttribute('class'))
            ->setFeature($node->getAttribute('feature'))
            ->setFile($node->getAttribute('file'))
            ->setName($node->getAttribute('name'))
            ->setTime((float) $node->getAttribute('time'));

        return $testCase;
    }
}
