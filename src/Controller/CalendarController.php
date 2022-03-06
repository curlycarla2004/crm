<?php

namespace App\Controller;

use App\Entity\Events;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\EventsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
     /**
     * @Route("/profile/calendar", name="calendar")
     */
    public function index(UserRepository $users, ManagerRegistry $doctrine): Response
    {
        $calendars = $doctrine->getRepository(Events::class)->findBy(["User"=>$this->getUser()->getId()]);
        $rdvs = [];

        foreach($calendars as $calendar)
        {
            $rdvs[] = [
                'id' =>$calendar->getId(),
                'start' =>$calendar->getStart()->format('Y-m-d H:i:s'),
                'end' =>$calendar->getEnd()->format('Y-m-d H:i:s'),
                'title' =>$calendar->getTitle(),
                'description' =>$calendar->getDescription(),
                'backgroundColor' =>$calendar->getBackgroundColor(),
                'allDay' =>$calendar->getAllDay(),
                'createdAt' =>$calendar->getCreatedAt(),
                'User'=>$calendar->getUser()
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('calendar/index.html.twig', [
            'data'=>$data,
        ]);
    }
}
