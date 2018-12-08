<?php

namespace Tomchkk\TransliterableBundle\Annotation;

/**
 * Transliterable
 *
 * @Annotation
 * @Target({"CLASS", "PROPERTY"})
 */
class Transliterable
{
    /**
     * @var string
     */
    public $ruleset;

    /**
     * __construct
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->ruleset = $data['ruleset'];
    }
}
