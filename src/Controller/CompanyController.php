<?php

namespace App\Controller;

use App\Entity\Companies;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    /**
     * See list of companies as User
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     * 
     * @Route("/profile/company", name="company")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Companies::class);
        $companies = $repository->findAll();
        return $this->render('company/company.html.twig', [
            'companies'=>$companies
        ]);
    }

     /**
      * Create company form as User
      *
      * @return Response
      * 
     * @Route("/profile/create_company", name="create_company")
     */
    public function create(): Response
    {
        return $this->render('company/create_company.html.twig', [
            'controller_name' => 'ContactController',
            'user' => 'Karla'
        ]);
    }

    /**
     * Submission of company form as User
     * 
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * 
     * @return Response
     * 
     * @Route("/profile/add_company_form", name="add_company_form")
     */
    public function add_company_form(ManagerRegistry $doctrine, Request $request): Response
    {
        $manager = $doctrine->getManager();
        $company = new Companies();
        $company->setName($request->request->get('name'));
        $company->setEmail($request->request->get('email'));
        $company->setPhone($request->request->get('phone'));
        $company->setAddress($request->request->get('address'));
        $company->setCity($request->request->get('city'));
        $company->setZipCode($request->request->get('zipCode'));
        $company->setCountry($request->request->get('country'));
        
        $manager->persist($company);
        $manager->flush();

        $this->addFlash('message', 'New company has been added');
        return $this->render('company/create_company.html.twig', [
            'company' => $company,
        ]);
    }
}
