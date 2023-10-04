<?php

namespace App\Controller;

use App\Entity\Events;
use App\Repository\EventsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AgendaController extends AbstractController
{
    #[Route('/agenda', name: 'agenda')]
    #[IsGranted("ROLE_USER")]
    public function index(EventsRepository $repo): Response
    {
        $events = $repo->findAll();
        return $this->render('agenda/index.html.twig', [
            'events'=>$events
        ]);
    }

    #[Route('/agenda/{id}', name: 'event')]
    #[IsGranted("ROLE_USER")]
    public function event(EventsRepository $repo, Events $id): Response
    {
        $event = $repo->findById($id);
        return $this->render('agenda/event.html.twig', [
            'event'=> $event
        ]);
    }
}
