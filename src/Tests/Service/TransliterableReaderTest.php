<?php

namespace Tomchkk\TransliterableBundle\Tests\Service;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Tomchkk\TransliterableBundle\Annotation as Tomchkk;
use Tomchkk\TransliterableBundle\Service\TransliterableReader;

/**
 * TransliterableReaderTest
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class TransliterableReaderTest extends TestCase
{
    protected $reader;

    protected function setUp()
    {
        $annotationReader = new AnnotationReader();
        $this->reader = new TransliterableReader($annotationReader);
    }

    /**
     * @dataProvider getClassRulesetProvider
     */
    public function testGetClassRuleset($class, $expected)
    {
        $reflClass = new \ReflectionClass($class);

        $this->assertEquals(
            $expected,
            $this->reader->getClassRuleset($reflClass)
        );
    }

    public function getClassRulesetProvider()
    {
        return array(
            'Class has no annotation' => array(
                TestNoAnnotation::class, null
            ),
            'Class has empty annotation ruleset' => array(
                TestEmptyAnnotation::class, null
            ),
            'Class has annotation ruleset' => array(
                TestRulesetAnnotation::class, 'class-ruleset'
            )
        );
    }

    /**
     * @dataProvider getPropertyRulesetProvider
     */
    public function testGetPropertyRuleset($class, $property, $expected)
    {
        $reflClass = new \ReflectionClass($class);
        $reflProperty = $reflClass->getProperty($property);

        $this->assertEquals(
            $expected,
            $this->reader->getPropertyRuleset($reflProperty)
        );
    }

    public function getPropertyRulesetProvider()
    {
        return array(
            'Property has no annotation' => array(
                TestNoAnnotation::class, 'property', null
            ),
            'Property has empty annotation ruleset' => array(
                TestEmptyAnnotation::class, 'property', null
            ),
            'Property has annotation ruleset' => array(
                TestRulesetAnnotation::class, 'property', 'property-ruleset'
            )
        );
    }
}

class TestNoAnnotation
{
    private $property;
}

/**
 * @Tomchkk\Transliterable(ruleset="")
 */
class TestEmptyAnnotation
{
    /**
     * @Tomchkk\Transliterable(ruleset="")
     */
    private $property;
}

/**
 * @Tomchkk\Transliterable(ruleset="class-ruleset")
 */
class TestRulesetAnnotation
{
    /**
     * @Tomchkk\Transliterable(ruleset="property-ruleset")
     */
    private $property;
}
