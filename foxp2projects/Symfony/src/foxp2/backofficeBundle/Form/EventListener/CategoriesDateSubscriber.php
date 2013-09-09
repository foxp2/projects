<?php

namespace foxp2\backofficeBundle\Form\EventListener;

use DateTime;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class CategoriesDateSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(            
            FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();       

        if ($data->getId()) {  
          $data->setDateModified(new DateTime('now'));            
        }
    }
}
?>