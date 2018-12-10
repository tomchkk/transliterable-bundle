<?php

namespace Tomchkk\TransliterableBundle\Tests\Embeddable;

use PHPUnit\Framework\TestCase;
use Tomchkk\TransliterableBundle\Embeddable\Transliterable;

/**
 * TransliterableTest
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class TransliterableTest extends TestCase
{
    /**
     * Setting the orignal value on a transliterable clears any current
     * transliterated value, unless the original value is identical.
     */
    public function testSetOriginalClearsTransliteration()
    {
        $transliterable = new Transliterable();

        $transliterable->setOriginal('original');
        $transliterable->setTransliteration('transliterated');
        $transliterable->setOriginal('original');

        $this->assertEquals('transliterated', $transliterable->getTransliteration());

        $transliterable->setOriginal('new');

        $this->assertNull($transliterable->getTransliteration());
    }
}
