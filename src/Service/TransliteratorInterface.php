<?php

namespace Tomchkk\TransliterableBundle\Service;

/**
 * TransliteratorInterface
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
interface TransliteratorInterface
{
    /**
     * transliterate
     *
     * Transliterates a given $string using an (optional) $ruleset.
     *
     * @param string $string
     *
     * @return string
     */
    public function transliterate(string $string, ?string $ruleset = null): string;
}
