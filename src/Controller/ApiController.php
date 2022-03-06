<?php

namespace App\Controller;

use App\Entity\Events;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="api")
     */
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }

    /**
     * @Route("/api/{id}/edit", name="api_event_edit", methods={"PUT"} )
     */
    public function majEvent(?Events $events, Request $request, ManagerRegistry $doctrine): Response
    {
        //on recupere les données
        $donnees = json_decode($request->getContent());

        if(
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->start) && !empty($donnees->start) &&
            isset($donnees->description) && !empty($donnees->description) &&
            isset($donnees->backgroundColor) && !empty($donnees->backgroundColor) &&
            isset($donnees->title) && !empty($donnees->title) &&
            isset($donnees->title) && !empty($donnees->title)
        ){
            $code = 200;

            if(!$events){
                $events = new Events;
                
                $code = 201;
            }
            $events->setTitle($donnees->title);
            $events->setDescription($donnees->description);
            $events->setStart(new DateTime($donnees->start));
            if($donnees->allDay){
                $events->setEnd(new DateTime($donnees->start));
            }else{
                $events->setEnd(new DateTime($donnees->start));
            }
            $events->setAllDay($donnees->allDay);
            $events->setBackgroundColor($donnees->backgroundColor);

            $em = $doctrine->getManager();
            $em->persist($events);
            $em->flush();

            return new Response('Ok', $code);
        }else{
            return new Response('Données incomptes', 404);
        }



        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
}
