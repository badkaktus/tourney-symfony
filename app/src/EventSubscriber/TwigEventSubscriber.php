<?php

namespace App\EventSubscriber;

use App\Repository\TourneyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private TourneyRepository $tourneyRepository,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }

    public function onControllerEvent(ControllerEvent $event)
    {
        $this->twig->addGlobal('tourneys', $this->tourneyRepository->findAll());
    }
}
