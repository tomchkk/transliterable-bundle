<?php

namespace Tomchkk\TransliterableBundle\Tests\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;
use Tomchkk\TransliterableBundle\Embeddable\Transliterable;
use Tomchkk\TransliterableBundle\EventSubscriber\TransliterableSubscriber;
use Tomchkk\TransliterableBundle\Service\TransliterableReader;
use Tomchkk\TransliterableBundle\Service\TransliteratorInterface;

/**
 * TransliterableSubscriberTest
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class TransliterableSubscriberTest extends TestCase
{
    const ORIGINAL = 'original';
    const TRANSLITERATED = 'transliterated';
    const UNALTERED = 'unaltered';

    protected $em;
    protected $manager;
    protected $reader;
    protected $subscriber;

    protected function setUp()
    {
        $unitOfWork = $this
            ->getMockBuilder(UnitOfWork::class)
            ->disableOriginalConstructor()
            ->getMock();
        $unitOfWork
            ->method('recomputeSingleEntityChangeSet')
            ->willReturn(true);

        $this->em = $this
            ->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->em
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork);

        $transliterator = $this
            ->getMockBuilder(TransliteratorInterface::class)
            ->getMock();
        $transliterator
            ->method('transliterate')
            ->willReturn(TransliterableSubscriberTest::TRANSLITERATED);

        $reader = $this
            ->getMockBuilder(TransliterableReader::class)
            ->disableOriginalConstructor()
            ->getMock();
        $reader
            ->method('getClassRuleset')
            ->willReturn(null);
        $reader
            ->method('getPropertyRuleset')
            ->willReturn(null);

        $this->subscriber = new TransliterableSubscriber($transliterator, $reader);
    }

    /**
     * @dataProvider preEventsProvider
     */
    public function testPreEvents($entity, $classMetadata, $expected)
    {
        $this->em
            ->method('getClassMetadata')
            ->willReturn($classMetadata);

        $eventArgs = new LifecycleEventArgs($entity, $this->em);

        $events = array('prePersist', 'preUpdate');
        foreach ($events as $event) {
            $this->subscriber->{$event}($eventArgs);

            foreach ($expected as $field => $value) {
                $result = \method_exists($entity->{$field}, 'getTransliteration')
                    ? $entity->{$field}->getTransliteration()
                    : null;
                $this->assertEquals($value, $result);
            }
        }
    }

    public function preEventsProvider()
    {
        return array(
            'entity has uninstantiated transliterable class mappings' => array(
                $entity = new TestEntity1(),
                $this->buildClassMetadata(
                    $entity,
                    array('fieldName' => 'field1', 'class' => Transliterable::class)
                ),
                array('field1' => null)
            ),
            'entity has no transliterable class mappings' => array(
                $entity = new TestEntity2(),
                $this->buildClassMetadata(
                    $entity,
                    array('fieldName' => 'field1', 'class' => StdClass::class),
                    array('fieldName' => 'field2', 'class' => StdClass::class),
                    array('fieldName' => 'field3', 'class' => StdClass::class)
                ),
                array(
                    'field1' => null,
                    'field2' => null,
                    'field3' => self::UNALTERED
                )
            ),
            'entity has transliterable class mappings' => array(
                $entity = new TestEntity2(),
                $this->buildClassMetadata(
                    $entity,
                    array('fieldName' => 'field1', 'class' => Transliterable::class),
                    array('fieldName' => 'field2', 'class' => StdClass::class),
                    array('fieldName' => 'field3', 'class' => Transliterable::class)
                ),
                array(
                    'field1' => self::TRANSLITERATED,
                    'field2' => null,
                    'field3' => self::UNALTERED
                )
            )
        );
    }

    private function buildClassMetadata($entity, ...$mappings)
    {
        $classMetadata = new ClassMetadata(get_class($entity));
        $classMetadata->reflClass = new \ReflectionClass(get_class($entity));
        $builder = new ClassMetadataBuilder($classMetadata);

        foreach ($mappings as $mapping) {
            $builder->addEmbedded($mapping['fieldName'], $mapping['class']);
        }

        return $builder->getClassMetadata();
    }
}

class TestEntity1
{
    public $field1;
}

class TestEntity2
{
    public $field1;
    public $field2;
    public $field3;

    public function __construct()
    {
        $this->field1 = new Transliterable();
        $this->field1->setOriginal(TransliterableSubscriberTest::ORIGINAL);

        $this->field2 = new Transliterable();
        $this->field2->setOriginal(TransliterableSubscriberTest::ORIGINAL);

        $this->field3 = new Transliterable();
        $this->field3->setOriginal(TransliterableSubscriberTest::ORIGINAL);
        $this->field3->setTransliteration(TransliterableSubscriberTest::UNALTERED);
    }
}
