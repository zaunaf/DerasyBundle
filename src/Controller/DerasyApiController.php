<?php
namespace Derasy\DerasyBundle\Controller;

/**
 * Created by PhpStorm.
 * User: Abah
 * Date: 08/10/2018
 * Time: 08.35
 */
use Derasy\DerasyBundle\DerasyGreeting;
use Derasy\DerasyBundle\Event\DerasyEvents;
use Derasy\DerasyBundle\Event\FilterApiResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DerasyApiController extends AbstractController
{
    private $derasy;
    private $eventDispatcher;

    public function __construct(DerasyGreeting $derasyGreeting, EventDispatcherInterface $eventDispatcher = null)
    {
        $this->derasy = $derasyGreeting;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function index(){

        $data = [
            'greeting' => $this->derasy->getGreeting(),
            'salam' => $this->derasy->getSalamList()
        ];

        $event = new FilterApiResponseEvent($data);

        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(DerasyEvents::FILTER_API, $event);
        }

        return $this->json($event->getData());
    }
}