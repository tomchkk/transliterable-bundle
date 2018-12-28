<?php

namespace Tomchkk\TransliterableBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
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
     * @var string
     */
    const BUNDLE_ALIAS = 'tomchkk_transliterable';

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(self::BUNDLE_ALIAS);
        $rootNode = $this->getCompatibleRootNode($treeBuilder);

        $rootNode
            ->children()
                ->scalarNode('global_ruleset')
                    ->info('The default ruleset, required by the PHP Transliterator, in all cases where a specific ruleset is not set at class- or property-level.')
                    ->defaultValue(Transliterator::DEFAULT_RULESET)
                ->end()
                ->scalarNode('transliterator')
                    ->info(sprintf(
                        'By default TransliterableBundle uses PHP\'s built-in Transliterator class - decorated with a simple caching mechanism - as the transliteration engine. The default transliterator can be overridden by a custom service implementing %s.',
                        TransliteratorInterface::class
                    ))
                    ->defaultNull()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * getCompatibleRootNode
     *
     * Gets the root node for the given TreeBuilder, using the method compatible
     * with the installed symfony version.
     *
     * @param TreeBuilder $treeBuilder
     *
     * @return ArrayNodeDefinition|NodeDefinition
     */
    private function getCompatibleRootNode(TreeBuilder $treeBuilder)
    {
		if (!method_exists($treeBuilder, 'getRootNode')) {
            // compatible with symfony < 4.2
            return $treeBuilder->root(self::BUNDLE_ALIAS);
        }

        // compatible with symfony >= 4.2
        return $treeBuilder->getRootNode();
    }
}
