<?php

namespace Tomchkk\TransliterableBundle\Tests\Service;

use App\Service\TransliteratorManager;
use PHPUnit\Framework\TestCase;

/**
 * TransliteratorManagerTest
 */
class TransliteratorManagerTest extends TestCase
{
    const DEFAULT_RULESET = 'Any-Latin';

    /**
     * @dataProvider GetTransliteratorProvider
     */
    public function testGetTransliterator($manager, $ruleset, $expected)
    {
        $transliterator = $manager->getTransliterator($ruleset);

        $this->assertEquals($expected, $transliterator->id);
    }

    public function GetTransliteratorProvider()
    {
        // instantiate the $manager in the provider to mimic container usage
        $manager = new TransliteratorManager(self::DEFAULT_RULESET);

        return array(
            'an empty ruleset uses the default ruleset' => array(
                $manager, null, self::DEFAULT_RULESET
            ),
            'a given ruleset can be used instead' => array(
                $manager, 'Russian-Latin/BGN', 'Russian-Latin/BGN'
            ),
            'an existing ruleset is re-used' => array(
                $manager, 'Any-Latin', 'Any-Latin'
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetTransliteratorException()
    {
        $manager = new TransliteratorManager(self::DEFAULT_RULESET);

        $transliterator = $manager->getTransliterator('invalid ruleset');
    }
}
