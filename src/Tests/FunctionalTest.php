<?php

namespace Tomchkk\TransliterableBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use Tomchkk\TransliterableBundle\Service\Transliterator;
use Tomchkk\TransliterableBundle\Service\TransliteratorInterface;
use Tomchkk\TransliterableBundle\TomchkkTransliterableBundle;

/**
 * FunctionalTest
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class FunctionalTest extends TestCase
{
    private $kernel;

    /**
     * @dataProvider servicePropertyWiringProvider
     */
    public function testServicePropertyWiring($id, $propName, $config, $expected)
    {
        $container = $this->getTestContainer($config);
        $service = $container->get($id);

        $reflClass = new \ReflectionClass($service);
        $property = $reflClass->getProperty($propName);
        $property->setAccessible(true);

        $this->assertEquals($expected, $property->getValue($service));
    }

    public function servicePropertyWiringProvider()
    {
        return array(
            'Transliterator with default ruleset' => array(
                'tomchkk_transliterable.transliterator',
                'globalRuleset',
                array(),
                Transliterator::DEFAULT_RULESET
            ),
            'Transliterator with null params' => array(
                'tomchkk_transliterable.transliterator',
                'globalRuleset',
                array('global_ruleset' => null),
                Transliterator::DEFAULT_RULESET
            ),
            'Transliterator with override params' => array(
                'tomchkk_transliterable.transliterator',
                'globalRuleset',
                array('global_ruleset' => 'Test-Test'),
                'Test-Test'
            )
        );
    }

    /**
     * @dataProvider serviceWiringProvider
     */
    public function testServiceWiring($id, $config, $expected)
    {
        $container = $this->getTestContainer($config);
        $service = $container->get($id);

        $this->assertInstanceOf($expected, $service);
    }

    public function serviceWiringProvider()
    {
        return array(
            'Default Transliterator' => array(
                'tomchkk_transliterable.transliterator',
                array(),
                Transliterator::class
            ),
            'Substitute Transliterator' => array(
                'tomchkk_transliterable.transliterator',
                array('transliterator' => MockTransliterator::class),
                MockTransliterator::class
            )
        );
    }

    protected function tearDown()
    {
        $fileSystem = new Filesystem();

        // clear the cache after each test case
        $fileSystem->remove($this->kernel->getCacheDir());
    }

    /**
     * getTestContainer
     *
     * Gets a test container with a given config.
     *
     * @param array|null $config
     *
     * @return Container
     */
    private function getTestContainer(?array $config = [])
    {
        $this->kernel = new TomchkkTransliterableTestKernel($config);
        $this->kernel->boot();

        return $this->kernel->getContainer();
    }
}

class TomchkkTransliterableTestKernel extends Kernel
{
    private $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;

        parent::__construct('test', true);
    }

    public function registerBundles()
    {
        return array(
            new TomchkkTransliterableBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            if (isset($this->config['transliterator'])) {
                // use a custom transliterator
                $container->register($this->config['transliterator'], $this->config['transliterator']);
            }

            $container->loadFromExtension('tomchkk_transliterable', $this->config);
        });
    }
}

class MockTransliterator implements TransliteratorInterface
{
    public function transliterate(string $string, string $ruleset = null): string
    {
        return 'mock';
    }
}
