<?php

namespace Tomchkk\TransliterableBundle\EventSubscriber;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tomchkk\TransliterableBundle\Embeddable\Transliterable;
use Tomchkk\TransliterableBundle\EventSubscriber\AbstractDoctrineEventSubscriber;
use Tomchkk\TransliterableBundle\Service\TransliterableReader;
use Tomchkk\TransliterableBundle\Service\TransliteratorInterface;

/**
 * TransliterableSubscriber
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
class TransliterableSubscriber extends AbstractDoctrineEventSubscriber
{
    /**
     * @var TransliteratorInterface
     */
    private $transliterator;

    /**
     * @var TransliterableReader
     */
    private $reader;

    /**
     * __construct
     *
     * @param TransliteratorInterface $transliterator
     */
    public function __construct(TransliteratorInterface $transliterator, TransliterableReader $reader)
    {
        $this->transliterator = $transliterator;
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $classMetadata = $args
            ->getObjectManager()
            ->getClassMetadata(get_class($entity))
        ;

        $this->handleEntity($entity, $classMetadata);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $classMetadata = $args
            ->getObjectManager()
            ->getClassMetadata(get_class($entity))
        ;

        $this->handleEntity($entity, $classMetadata);

        // forces the update action to see the change
        $this->forceEntityUpdate($args->getObjectManager(), $entity);
    }

    /**
     * handleEntity
     *
     * Handles a given $entity, checking for transliterable fields and
     * transliterating them, if possible.
     *
     * @param mixed $entity
     * @param ClassMetadata $classMetadata
     */
    public function handleEntity($entity, ClassMetadata $classMetadata)
    {
        if (!$fields = $this->getTransliterableFields($classMetadata)) {
            // the entity has no transliterables
            return;
        }

        $this->transliterateFields($entity, $fields, $classMetadata);
    }

    /**
     * getTransliterableFields
     *
     * Gets an array of entity field names that are embedded Transliterable
     * instances.
     *
     * @param ClassMetadata $classMetadata
     *
     * @return array
     */
    private function getTransliterableFields(ClassMetadata $classMetadata): array
    {
        $transliterableFields = [];
        foreach ($classMetadata->embeddedClasses as $fieldName => $fieldMeta) {
            if ($fieldMeta['class'] === Transliterable::class) {
                $transliterableFields[] = $fieldName;
            }
        }

        return $transliterableFields;
    }

    /**
     * transliterateFields
     *
     * Transliterates the transliterable $fields of the given $entity.
     *
     * @param mixed $entity
     * @param array $fields
     * @param classMetadata $classMetadata
     */
    private function transliterateFields($entity, array $fields, ClassMetadata $classMetadata)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $reflClass = $classMetadata->getReflectionClass();
        $classRuleset = $this->reader->getClassRuleset($reflClass);

        foreach ($fields as $field) {
            $transliterable = $propertyAccessor->getValue($entity, $field);

            if ($this->canTransliterate($transliterable)) {
                $reflProperty = $reflClass->getProperty($field);
                $ruleset = $this->reader->getPropertyRuleset($reflProperty)?: $classRuleset;

                $original = $transliterable->getOriginal();
                $transliteration = $this->transliterator->transliterate($original, $ruleset);

                $transliterable->setTransliteration($transliteration);

                $propertyAccessor->setValue($entity, $field, $transliterable);
            }
        }
    }

    /**
     * canTransliterate
     *
     * Checks that the given $transliterable can be transliterated - i.e. that:
     * - the transliterable field is instantiated
     * - there is **not** a current transliteration
     * - there **is** an original value to transliterate
     *
     * @param Transliterable|null $transliterable
     *
     * @return bool
     */
    private function canTransliterate(?Transliterable $transliterable): bool
    {
        if (!$transliterable) {
            // the $transliterable field was not instantiated
            return false;
        }

        return !$transliterable->getTransliteration() && $transliterable->getOriginal();
    }
}
