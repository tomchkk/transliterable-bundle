<?php

namespace Tomchkk\TransliterableBundle\Service;

use Tomchkk\TransliterableBundle\Service\TransliteratorInterface;

/**
 * Transliterator
 *
 * A Transliterator service that transliterates and manages transliterator
 * instances for a default or other given transliteration ruleset.
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class Transliterator implements TransliteratorInterface
{
    /**
     * The absolute default ruleset for use in this transliterator.
     */
    const DEFAULT_RULESET = 'Any-Latin';

    /**
     * A global ruleset that can be applied to all transliterations if no other
     * is provided.
     *
     * @var string
     */
    private $globalRuleset;

    /**
     * A cache of available transliterators with different rulesets.
     *
     * @var array
     */
    private $transliterators = array();

    /**
     * __construct
     *
     * @param string $globalRuleset
     */
    public function __construct(?string $globalRuleset)
    {
        $this->globalRuleset = $globalRuleset ?: self::DEFAULT_RULESET;
    }

    /**
     * {@inheritDoc}
     */
    public function transliterate(string $string, ?string $ruleset = null): string
    {
        $ruleset = $ruleset ?: $this->globalRuleset;

        $transliterator = $this->getTransliterator($ruleset);

        return $transliterator->transliterate($string);
    }

    /**
     * getTransliterator
     *
     * Returns an instance of a transliterator for a given $ruleset.
     *
     * @param string|null $ruleset
     *
     * @return \Transliterator
     */
    private function getTransliterator(?string $ruleset): \Transliterator
    {
        if (!\array_key_exists($ruleset, $this->transliterators)) {
            $this->transliterators[$ruleset] = $this->createTransliterator($ruleset);
        }

        return $this->transliterators[$ruleset];
    }

    /**
     * createTransliterator
     *
     * Creates a new \Transliterator instance from the given $ruleset.
     *
     * @param string|null $ruleset
     *
     * @return \Transliterator
     *
     * @throws \InvalidArgumentException
     */
    private function createTransliterator(?string $ruleset): \Transliterator
    {
        if (!$transliterator = \Transliterator::create($ruleset)) {
            throw new \InvalidArgumentException(sprintf(
                'Unable to create Transliterator from ruleset \'%s\'',
                $ruleset
            ));
        }

        return $transliterator;
    }
}
