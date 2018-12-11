<?php

namespace Tomchkk\TransliterableBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * PublicServicesForTestEnvPass
 *
 * Redefines all services as public when the bundle is booted in 'test'. Based
 * on an article by Tomas Votruba:
 * - @link https://www.tomasvotruba.cz/blog/2018/05/17/how-to-test-private-services-in-symfony/
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class PublicServicesForTestEnvPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->getParameter('kernel.environment') !== 'test') {
            return;
        }

        $this->makePublic($container->getDefinitions());
        $this->makePublic($container->getAliases());
    }

    /**
     * makePublic
     *
     * Sets the given service $definitions as public.
     *
     * @param array $definitions
     */
    private function makePublic(array $definitions)
    {
        foreach ($definitions as $definition) {
            $definition->setPublic(true);
        }
    }
}
