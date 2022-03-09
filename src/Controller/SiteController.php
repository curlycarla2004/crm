<?php

namespace App\Controller;

use App\Entity\Companies;
use App\Entity\Contacts;
use App\Entity\Events;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * Dashboard of User
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     * 
     * @Route("/profile/site", name="site")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        // get all users except admin
        $repository = $doctrine->getManager()->getRepository(User::class);
        $role = ('ROLE_USER');
        $users = $repository->findByRole($role);

        $repository = $doctrine->getRepository(Contacts::class);
        $contacts = $repository->findBy(array(), array('id'=>'DESC'), 3,0);

        $repository = $doctrine->getRepository(Companies::class);
        $companies = $repository->findBy(array(), array('id'=>'DESC'), 3,0);

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

        return $this->render('site/index.html.twig', [
            'data'=>$data,
            'contacts' => $contacts,
            'companies'=>$companies,
            'users'=>$users
        ]);
    }

   

    // Header of User
    public function header(){

        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('site/header-user.html.twig' , [
        ]);
    }

    // Footer User and Admin
    public function footer(){
        return $this->render('site/header.html.twig' , [
        ]);
    }

    
}
