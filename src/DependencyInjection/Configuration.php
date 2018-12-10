<?php

namespace Tomchkk\TransliterableBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Tomchkk\TransliterableBundle\Service\Transliterator;
use Tomchkk\TransliterableBundle\Service\TransliteratorInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('tomchkk_transliterable');

        $rootNode
            ->children()
                ->scalarNode('global_ruleset')
                    ->info('The transliterator ruleset that should be used in all cases where a specific one is not set on a class or class property.')
                    ->defaultValue(Transliterator::DEFAULT_RULESET)
                ->end()
                ->scalarNode('transliterator')
                    ->info(sprintf(
                        'By default TransliterableBundle uses PHP\'s built-in \Transliterator class, decorated with a simple caching mechanism, to perform transliterations. The default transliterator can be overridden by a custom service implementing %s.',
                        TransliteratorInterface::class
                    ))
                    ->defaultNull()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}