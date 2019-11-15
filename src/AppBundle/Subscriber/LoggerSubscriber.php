<?php

namespace AppBundle\Subscriber;

use AppBundle\Entity\Log;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class LoggerSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->index($args, "update");
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->index($args, "insert");
    }

    public function index(LifecycleEventArgs $args, $action)
    {
        $entity = $args->getObject();

        if ($entity instanceof Log)
            return;

        $entityManager = $args->getObjectManager();
        $log = new Log();
        $log->setEntityName(get_class($entity));
        $log->setAction($action);
        $entityManager->persist($log);
        $entityManager->flush();
    }
}