<?php

namespace Tomchkk\TransliterableBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Tomchkk\TransliterableBundle\Service\Transliterator;
use Tomchkk\TransliterableBundle\Service\TransliteratorInterface;

/**
 * Configuration
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('tomchkk_transliterable');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('global_ruleset')
                    ->info('The default ruleset, required by the PHP Transliterator, in all cases where a specific ruleset is not set at class- or property-level.')
                    ->defaultValue(Transliterator::DEFAULT_RULESET)
                ->end()
                ->scalarNode('transliterator')
                    ->info(sprintf(
                        'By default TransliterableBundle uses PHP\'s built-in Transliterator class - decorated with a simple caching mechanism - to perform transliterations. The default transliterator can be overridden by a custom service implementing %s.',
                        TransliteratorInterface::class
                    ))
                    ->defaultNull()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
