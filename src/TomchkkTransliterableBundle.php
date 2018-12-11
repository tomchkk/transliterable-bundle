<?php

namespace Tomchkk\TransliterableBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tomchkk\TransliterableBundle\DependencyInjection\Compiler\PublicServicesForTestEnvPass;

/**
 * TomchkkTransliterableBundle
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class TomchkkTransliterableBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new PublicServicesForTestEnvPass());
    }
}
