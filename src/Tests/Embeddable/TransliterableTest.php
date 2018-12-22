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

    /**
     * @dataProvider __toStringProvider
     */
    public function test__toString($transliterable, $expected)
    {
        $this->assertSame($expected, $transliterable->__toString());
    }

    public function __toStringProvider()
    {
        return array(
            'original and transliteration are null' => array(
                $this->getTransliterable(), '()'
            ),
            'only original is null' => array(
                $this->getTransliterable(null, 'Ilya'), '(Ilya)'
            ),
            'only transliteration is null' => array(
                $this->getTransliterable('Илья'), 'Илья ()'
            ),
            'neither original nor transliteration are null' => array(
                $this->getTransliterable('Илья', 'Ilya'), 'Илья (Ilya)'
            ),
        );
    }

    private function getTransliterable($original = null, $transliteration = null)
    {
        $transliterable = new Transliterable();

        if ($original) {
            $transliterable->setOriginal($original);
        }

        if ($transliteration) {
            $transliterable->setTransliteration($transliteration);
        }

        return $transliterable;
    }
}
