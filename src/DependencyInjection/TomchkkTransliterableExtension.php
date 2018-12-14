<?php

namespace Tomchkk\TransliterableBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * TomchkkTransliterableExtension
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class TomchkkTransliterableExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => [
                    'TomchkkTransliterableBundle' => [
                        'type' => 'xml',
                        'prefix' => 'Tomchkk\TransliterableBundle\Embeddable'
                    ]
                ]
            ]
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $defaultManager = $container->getDefinition('tomchkk_transliterable.transliterator.default');
        $defaultManager->setArgument(0, $config['global_ruleset']);

        if (null !== $config['transliterator']) {
            // use a custom transliterator
            $container
                ->setAlias('tomchkk_transliterable.transliterator', $config['transliterator'])
                ->setPublic(true)
            ;
        }
    }
}
