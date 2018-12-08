<?php

namespace Tomchkk\TransliterableBundle\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;

/**
 * AbstractDoctrineEventSubscriber
 *
 * Provides methods common to Doctrine Event Subscriber implementations.
 *
 * @author Tom Moore <tpm.moore@gmail.com>
 */
abstract class AbstractDoctrineEventSubscriber implements EventSubscriber
{
    /**
     * forceEntityUpdate
     *
     * Forces Doctrine to update the changeset when making an entity update via
     * a Doctrine 'preUpdate' event.
     *
     * @param EntityManagerInterface  $em
     * @param mixed                   $entity
     */
    protected function forceEntityUpdate(EntityManagerInterface $em, $entity)
    {
        $meta = $em->getClassMetadata(get_class($entity));
        $em
            ->getUnitOfWork()
            ->recomputeSingleEntityChangeSet($meta, $entity)
        ;
    }
}
