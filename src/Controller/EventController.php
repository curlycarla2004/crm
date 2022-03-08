<?php

namespace App\Controller;

use App\Entity\Events;
use App\Entity\User;
use App\Form\EventsType;
use App\Repository\EventsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/", name="event_index", methods={"GET"})
     */
    public function index(EventsRepository $eventsRepository, ManagerRegistry $doctrine): Response
    {
        if($this->isGranted('ROLE_ADMIN')){
            $events = $doctrine->getRepository(Events::class)->findAll();
        }else {
            $events = $doctrine->getRepository(Events::class)->findBy(["User"=>$this->getUser()->getId()]);
        }

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/new", name="event_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Events();
        $form = $this->createForm(EventsType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event->setUser($this->getUser());

            $entityManager->persist($event);

            $entityManager->flush();

            if($this->isGranted('ROLE_ADMIN')){
                return $this->redirectToRoute('admin_calendar');
            }else {
                return $this->redirectToRoute('calendar');
            }
        }

        return $this->renderForm('event/new.html.twig', [
            'event' => $event,
            'form' => $form
        ]);
    }

    /**
     * @Route("/{id}", name="event_show", methods={"GET"})
     */
    public function show(Events $event, ManagerRegistry $doctrine, $id): Response
    {

        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="event_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Events $event, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(EventsType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            if($this->isGranted('ROLE_ADMIN')){
                return $this->redirectToRoute('admin_calendar');
            }else {
                return $this->redirectToRoute('calendar');
            }
            
        }

        return $this->renderForm('event/edit.html.twig', [
            'event' => $event,
            'form' => $form
        ]);
    }

    /**
     * @Route("/{id}", name="event_delete", methods={"POST"})
     */
    public function delete(Request $request, Events $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        if($this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('admin_calendar');
        }else {
            return $this->redirectToRoute('calendar');
        }
    }
}
