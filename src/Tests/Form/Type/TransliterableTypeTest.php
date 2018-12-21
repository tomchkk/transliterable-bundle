<?php

namespace Tomchkk\TransliterableBundle\Tests\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Test\TypeTestCase;
use Tomchkk\TransliterableBundle\Embeddable\Transliterable;
use Tomchkk\TransliterableBundle\Form\Type\TransliterableType;

/**
 * TransliterableTypeTest
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class TransliterableTypeTest extends TypeTestCase
{
    /**
     * @dataProvider optionsDefaultsProvider
     */
    public function testOptionDefaults($name, $expected)
    {
        $form = $this->getFormForOptions();
        $option = $form->getConfig()->getOption($name);

        $this->assertSame($expected, $option);
        $this->assertInternalType(\gettype($expected), $option);
    }

    public function optionsDefaultsProvider()
    {
        return array(
            array('data_class', Transliterable::class),
            array('error_bubbling', false),
            array('exclude_transliteration', false),
            array('options', array()),
            array('original_options', array()),
            array('transliteration_options', array())
        );
    }

    /**
     * testExcludeTransliteration
     *
     * Asserts that setting the 'exclude_transliteration' option to true will
     * prevent the transliteration field from being built.
     *
     * @dataProvider excludeTransliterationProvider
     */
    public function testExcludeTransliteration($value, $expected)
    {
        $form = $this->getFormForOptions(array('exclude_transliteration' => $value));

        $this->assertEquals($expected, isset($form['transliteration']));
    }

    public function excludeTransliterationProvider()
    {
        return array(
            array(false, true),
            array(true, false)
        );
    }

    /**
     * testOptions
     *
     * Asserts that the 'options' option are merged into both the 'original' and
     * the 'transliterable' fields' options.
     *
     * @dataProvider optionsProvider
     */
    public function testOptions($name, $value, $expected)
    {
        $options = array('options' => array($name => $value));
        $form = $this->getFormForOptions($options);

        $this->assertSame($expected[0], $form['original']->getConfig()->getOption($name));
        $this->assertSame($expected[1], $form['transliteration']->getConfig()->getOption($name));
    }

    public function optionsProvider()
    {
        return array(
            array('label', 'foo', array('foo', 'foo')),
            array('required', false, array(false, false)),
            array('required', true, array(true, true)),
        );
    }

    /**
     * testOriginalOptions
     *
     * Asserts that 'original_options' options overwrite any given 'options'
     * options.
     */
    public function testOriginalOptions()
    {
        $options = array(
            'options' => array('label' => 'foo'),
            'original_options' => array('label' => 'bar')
        );
        $form = $this->getFormForOptions($options);

        $this->assertSame('bar', $form['original']->getConfig()->getOption('label'));
        $this->assertSame('foo', $form['transliteration']->getConfig()->getOption('label'));
    }

    /**
     * testTransliterationOptions
     *
     * Asserts that 'transliteration_options' options overwrite any given
     * 'options' options.
     */
    public function testTransliterationOptions()
    {
        $options = array(
            'options' => array('label' => 'foo'),
            'transliteration_options' => array('label' => 'bar')
        );
        $form = $this->getFormForOptions($options);

        $this->assertSame('foo', $form['original']->getConfig()->getOption('label'));
        $this->assertSame('bar', $form['transliteration']->getConfig()->getOption('label'));
    }

    /**
     * getFormForOptions
     *
     * @param array $options
     *
     * @return FormInterface
     */
    private function getFormForOptions(array $options = array()): FormInterface
    {
        return $this->factory->create(TransliterableType::class, null, $options);
    }
}
