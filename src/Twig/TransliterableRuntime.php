<?php

namespace Tomchkk\TransliterableBundle\Twig;

use Tomchkk\TransliterableBundle\Service\TransliteratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * TransliterableRuntime
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class TransliterableRuntime implements RuntimeExtensionInterface
{
    /**
     * @var TransliteratorInterface
     */
    private $transliterator;

    /**
     * __construct
     *
     * @param TransliteratorInterface $transliterator
     */
    public function __construct(TransliteratorInterface $transliterator)
    {
        $this->transliterator = $transliterator;
    }

    /**
     * transliterate
     *
     * Transliterates the given string using the optional $ruleset, or the
     * default one, if not passed.
     *
     * @param string        $string
     * @param string|null   $ruleset
     *
     * @return string|void
     */
    public function transliterate(?string $string, string $ruleset = null)
    {
        if ($string === null) {
            return;
        }

        return $this->transliterator->transliterate($string, $ruleset);
    }
}
