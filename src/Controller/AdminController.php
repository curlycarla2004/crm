<?php

namespace App\Controller;

use App\Entity\Companies;
use App\Entity\Contacts;
use App\Entity\Events;
use App\Entity\User;
use App\Repository\EventsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/home", name="admin_home")
     */
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // get all users except admin
        $repository = $doctrine->getManager()->getRepository(User::class);
        $role = ('ROLE_USER');
        $users = $repository->findByRole($role);

        $repository = $doctrine->getRepository(Contacts::class);
        $contacts = $repository->findAll();

        $repository = $doctrine->getRepository(Companies::class);
        $companies = $repository->findAll();

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

        return $this->render('admin/index.html.twig', [
            'contacts' => $contacts,
            'companies'=>$companies,
            'users'=>$users,
            'data'=>$data,
        ]);
    }

    // Header
    public function header(ManagerRegistry $doctrine){
        
        return $this->render('admin/header-admin.html.twig' , [
        ]);
    }
}
