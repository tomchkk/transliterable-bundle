<?php

namespace Tomchkk\TransliterableBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use Tomchkk\TransliterableBundle\Service\Transliterator;

/**
 * TransliteratorTest
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class TransliteratorTest extends TestCase
{
    const DEFAULT_RULESET = 'Any-Latin';
    const ALT_RULESET = 'Russian-Latin/BGN';
    const ORIGINAL = 'Илья Арсеньев';

    /**
     * We're testing that the Transliterator service is able to handle different
     * rulesets in the expected way (rather than that the PHP Transliterator
     * engine, that the service uses, actually transliterates).
     *
     * @dataProvider transliterateProvider
     */
    public function testTransliterate($transliterator, $ruleset, $expected)
    {
        $transliteration = $transliterator->transliterate(self::ORIGINAL, $ruleset);

        $this->assertEquals($expected, $transliteration);
    }

    public function transliterateProvider()
    {
        // instantiate the $transliterator in the provider to mimic its caching
        // when in use as a service in the container
        $transliterator = new Transliterator(self::DEFAULT_RULESET);

        $defaultTransliterator = \Transliterator::create(self::DEFAULT_RULESET);
        $altTransliterator = \Transliterator::create(self::ALT_RULESET);

        return array(
            'a null ruleset uses the default' => array(
                $transliterator,
                null,
                $defaultTransliterator->transliterate(self::ORIGINAL)
            ),
            'a given ruleset can be used instead' => array(
                $transliterator,
                'Russian-Latin/BGN',
                $altTransliterator->transliterate(self::ORIGINAL)
            ),
            'an existing ruleset is re-used' => array(
                $transliterator,
                self::DEFAULT_RULESET,
                $defaultTransliterator->transliterate(self::ORIGINAL)
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTransliteratorException()
    {
        $transliterator = new Transliterator(self::DEFAULT_RULESET);

        $transliterator->transliterate('string', 'invalid ruleset');
    }
}
