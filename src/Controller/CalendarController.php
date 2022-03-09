<?php

namespace App\Controller;

use App\Entity\Events;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
     /**
      * See calendar and its events as a User
      * @param ManagerRegistry $doctrine
      *
      * @return Response
      *
     * @Route("/profile/calendar", name="calendar")
     */
    public function index(ManagerRegistry $doctrine): Response
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
                'createdAt' =>$calendar->getCreatedAt()
            ];
        }

        $data = json_encode($rdvs);
        return $this->render('calendar/index.html.twig', [
            'data'=>$data,
        ]);
    }
}
