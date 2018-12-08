<?php

namespace Tomchkk\TransliterableBundle\Service;

use Doctrine\Common\Annotations\Reader as ReaderInterface;
use Tomchkk\TransliterableBundle\Annotation\Transliterable;

/**
 * TransliterableReader
 *
 * Provides an convenient way to read Transliterable annotation values.
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class TransliterableReader
{
    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * __construct
     *
     * @param ReaderInterface $reader
     */
    public function __construct(ReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    /**
     * getClassRuleset
     *
     * Gets the class-level ruleset property from any available Transliterable
     * annotations.
     *
     * @param \ReflectionClass $reflClass
     *
     * @return string|null
     */
    public function getClassRuleset(\ReflectionClass $reflClass): ?string
    {
        $annotation = $this->reader->getClassAnnotation(
            $reflClass,
            Transliterable::class
        );

        return $this->getRuleset($annotation);
    }

    /**
     * getPropertyRuleset
     *
     * Gets the property-level ruleset property from any available Transliterable
     * annotations.
     *
     * @param \ReflectionProperty $reflProperty
     *
     * @return string|null
     */
    public function getPropertyRuleset(\ReflectionProperty $reflProperty): ?string
    {
        $annotation = $this->reader->getPropertyAnnotation(
            $reflProperty,
            Transliterable::class
        );

        return $this->getRuleset($annotation);
    }

    /**
     * getRuleset
     *
     * Gets the ruleset from the annotation.
     *
     * @param Transliterable|null $annotation
     *
     * @return string|null
     */
    private function getRuleset(?Transliterable $annotation): ?string
    {
        if (!$annotation) {
            return null;
        }

        return $annotation->ruleset;
    }
}
