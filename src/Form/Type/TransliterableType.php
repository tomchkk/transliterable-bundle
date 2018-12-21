<?php

namespace Tomchkk\TransliterableBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tomchkk\TransliterableBundle\Embeddable\Transliterable;

/**
 * TransliterableType
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class TransliterableType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['original_options']['required'] =
            $options['original_options']['required'] ??
                $options['options']['required'] ??
                    $options['required'];

        // By default, the transliteration field is not required, since a missing
        // value will be generated during a prePersist event. However it can be
        // explicitly required as a 'transliteration_options' option.
        $options['transliteration_options']['required'] =
            $options['transliteration_options']['required'] ??
                $options['options']['required'] ??
                    false;

        $builder->add('original', TextType::class, array_merge(
                $options['options'],
                $options['original_options']
        ));

        if (!$options['exclude_transliteration']) {
            $builder->add('transliteration', TextType::class, array_merge(
                $options['options'],
                $options['transliteration_options']
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Transliterable::class,
            'error_bubbling' => false,
            'exclude_transliteration' => false,
            'options' => array(),
            'original_options' => array(),
            'transliteration_options' => array()
        ));

        $resolver->setAllowedTypes('exclude_transliteration', 'boolean');
        $resolver->setAllowedTypes('options', 'array');
        $resolver->setAllowedTypes('original_options', 'array');
        $resolver->setAllowedTypes('transliteration_options', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'transliterable';
    }
}
