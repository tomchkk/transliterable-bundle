<?php

namespace Tomchkk\TransliterableBundle\Twig;

use Tomchkk\TransliterableBundle\Twig\TransliterableRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * TransliterableExtension
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class TransliterableExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('transliterate', array(
                TransliterableRuntime::class,
                'transliterate'
            ))
        );
    }
}
