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

     /**
      * Edit company info as User
      *
      * @param ManagerRegistry $doctrine
      * @param $id
      *
      * @return Response
      *
     * @Route("/profile/edit_company/{id}", name="edit_company")
     */
    public function edit(ManagerRegistry $doctrine, int $id): Response
    {
        // find company
        $repository = $doctrine->getRepository(Companies::class);
        $company = $repository->find($id);

        if (is_null($company)) {
            return new Response('Not found 404', 404);
        }

        return $this->render('company/edit.html.twig', [
            'company' =>$company
        ]);
    }

     /**
      * Updating the edit comapny form as User
      *
      * @param ManagerRegistry $doctrine
      * @param Request $request
      * @param $id

      * @return Response

     * @Route("/profile/update_company/{id}", name="update_company")
     */
    public function update(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Companies::class);
        $company = $repository->find($id);

        if (is_null($company)) {
            return new Response('Not found 404', 404);
        }

        // Set keys
        if ($name = $request->request->get('name')) {
            $company->setName($name);
        }
        if ($email = $request->request->get('email')) {
            $company->setEmail($email);
        }
        if ($address = $request->request->get('address')) {
            $company->setAddress($address);
        }
        if ($city = $request->request->get('city')) {
            $company->setCity($city);
        }
        if ($country = $request->request->get('country')) {
            $company->setCountry($country);
        }
        if ($zipCode = $request->request->get('zipCode')) {
            $company->setAddress($zipCode);
        }
        if ($phone = $request->request->get('phone')) {
            $company->setPhone($phone);
        }

        // Save
        $doctrine->getManager()->flush();
        $this->addFlash('success', 'The company information has been edited.');
        return $this->redirectToRoute('company');
    }

     /**
      * Delete company as User
      * @param ManagerRegistry $doctrine
      * @param $id
      *
      * @return Response
      *
     * @Route("/profile/delete_company/{id}", name="delete_company")
     */
    public function delete(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Companies::class);
        $company = $repository->find($id);
        $entityManager = $doctrine->getManager();

        if (is_null($company)) {
            return new Response('Not found 404', 404);
        }

        // Remove
        $entityManager->remove($company);
        // Commit
        $entityManager->flush();
        $this->addFlash('message', 'Company has been deleted');
        return $this->redirectToRoute('company');
    }
}
