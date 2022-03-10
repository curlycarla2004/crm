<?php

namespace App\Controller;

use App\Entity\Companies;
use App\Entity\Contacts;
use App\Entity\Events;
use App\Entity\User;
use App\Form\UserType;
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
     * Admin dashboard
     * 
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     * 
     * @Route("/home", name="admin_home")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // get all users except admin
        $repository = $doctrine->getManager()->getRepository(User::class);
        $role = ('ROLE_USER');
        $users = $repository->findByRole($role);

        $repository = $doctrine->getRepository(Contacts::class);
        $contacts = $repository->findBy(array(), array('id'=>'DESC'), 3,0);

        $repository = $doctrine->getRepository(Companies::class);
        $companies = $repository->findBy(array(), array('id'=>'DESC'), 3,0);

        $calendars = $doctrine->getRepository(Events::class)->findAll();
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
                'User'=>$calendar->getUser()->getFirstName()
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

    // Admin Header
    public function header(ManagerRegistry $doctrine){
        
        return $this->render('site/header-admin.html.twig' , [
        ]);
    }

    /**
     * View admin calendar
     * 
     * @param ManagerRegistry $doctrine
     * @return Response
     * 
     * @Route("/calendar", name="admin_calendar")
     */
    public function admin_calendar_view(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Events::class);
        $events = $repository->findAll();

        $calendars = $doctrine->getRepository(Events::class)->findAll();
        
        $rdvs = [];

        foreach($calendars as $calendar)
        {
            $user = $doctrine->getRepository(User::class)->findOneBy(['id'=>$calendar->getUser()->getId()]);
            
            $rdvs[] = [
                'id' =>$calendar->getId(),
                'start' =>$calendar->getStart()->format('Y-m-d H:i:s'),
                'end' =>$calendar->getEnd()->format('Y-m-d H:i:s'),
                'title' =>$calendar->getTitle(),
                'description' =>$calendar->getDescription(),
                'backgroundColor' =>$calendar->getBackgroundColor(),
                'allDay' =>$calendar->getAllDay(),
                'User' => $user->getFirstName()
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('calendar/admin.html.twig', [
            'data'=>$data,
            'events' => $events
        ]);
    }

    /**
     * Admin Contact list
     * 
     * @param ManagerRegistry $doctrine
     * @return Response
     * 
     * @Route("/contact", name="contact_admin")
     */
    public function admin_contact( ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $repository = $doctrine->getRepository(Contacts::class);
        $contacts = $repository->findAll();

        $repository = $doctrine->getRepository(Companies::class);
        $companies = $repository->findAll();

        $repository = $doctrine->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('contact/contact.html.twig', [
            'contacts' => $contacts,
            'companies'=>$companies,
            'users'=>$users
        ]);
    }

      /**
       * Admin create contact formulaire
       * @return Response
       * 
     * @Route("/create_contact", name="create_contact_admin")
     */
    public function create_admin(): Response
    {
        return $this->render('contact/create_contact.html.twig', [
        ]);
    }

    /**
     * Submission form for adding contact as Admin
     * 
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return Response
     * 
     * @Route("/add_contact_form", name="add_contact_form_admin", methods={"POST"})
     */
    public function add_contact_form_admin(ManagerRegistry $doctrine, Request $request): Response
    {
        $manager = $doctrine->getManager();
        $contact = new Contacts();
        $contact->setFirstName($request->request->get('firstname'));
        $contact->setLastName($request->request->get('lastname'));
        $contact->setEmail($request->request->get('email'));
        $contact->setPhone($request->request->get('phone'));
        $contact->setAddress($request->request->get('address'));
        $contact->setCity($request->request->get('city'));
        $contact->setZipCode($request->request->get('zipCode'));
        $contact->setCountry($request->request->get('country'));
        
        $manager->persist($contact);
        $manager->flush();

        $this->addFlash('message', 'The contact has been added.');
        return $this->render('contact/create_contact.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * Admin Edit contact form
     * 
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param $id
     * @return Response
     * 
     * @Route("/admin/edit_contact/{id}", name="edit_contact")
     */
    public function edit(ManagerRegistry $doctrine, int $id): Response
    {
        $repository = $doctrine->getRepository(Contacts::class);
        $contact = $repository->find($id);

        if (is_null($contact)) {
            return new Response('Not found 404', 404);
        }
        
        return $this->render('contact/edit.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * Submission form for updating contact as Admin
     * 
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param $id
     * @return Response
     * 
     * @Route("/update_contact/{id}", name="update_contact")
     */
    public function update_contact(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Contacts::class);
        $contact = $repository->find($id);

        if (is_null($contact)) {
            return new Response('Not found 404', 404);
        }

        // Set keys
        if ($firstname = $request->request->get('firstname')) {
            $contact->setFirstName($firstname);
        }
        if ($lastname = $request->request->get('lastname')) {
            $contact->setLastName($lastname);
        }
        if ($email = $request->request->get('email')) {
            $contact->setPhone($email);
        }
        if ($address = $request->request->get('address')) {
            $contact->setAddress($address);
        }
        if ($city = $request->request->get('city')) {
            $contact->setCity($city);
        }
        if ($country = $request->request->get('country')) {
            $contact->setCountry($country);
        }
        if ($zipCode = $request->request->get('zipCode')) {
            $contact->setZipCode($zipCode);
        }
        if ($phone = $request->request->get('phone')) {
            $contact->setPhone($phone);
        }
        // Save
        $doctrine->getManager()->flush();

        $this->addFlash('message', 'The contact information has been edited.');
        return $this->redirectToRoute('contact_admin');
    }

     /**
      * Delete contact as admin
      * @param ManagerRegistry $doctrine
      * @param $id
      *
      * @return Response
      *
     * @Route("/delete_contact/{id}", name="delete_contact_admin")
     */
    public function delete_contact(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Contacts::class);
        $contact = $repository->find($id);
        $entityManager = $doctrine->getManager();

        if (is_null($contact)) {
            return new Response('Not found 404', 404);
        }

        // Remove
        $entityManager->remove($contact);
        // Commit
        $entityManager->flush();
        $this->addFlash('message', 'Contact has been deleted.');
        return $this->redirectToRoute('contact_admin');
    }

    /**
     * Change profile information as admin (the actual account)
      * @param ManagerRegistry $doctrine
      * @param $id
      * @param $user
      *
      * @return Response
     * @Route("/account/update/{id}", name="update_profile_admin", methods={"GET","POST"})
     */
    public function update_admin(Request $request, User $user, ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(User::class);
        $user = $repository->find($id);
        
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if (is_null($user)) {
            return new Response('Not found 404', 404);
          }
        if ($firstName = $request->request->get('firstname')) {
            $user->setFirstName($firstName);
        }
        if ($lastName = $request->request->get('lastname')) {
            $user->setLastName($lastName);
        }
        if ($phone = $request->request->get('phone')) {
            $user->setPhone($phone);
        }
        if ($image = $request->files->get('image')) {
            
            $destination=$this->getParameter('kernel.project_dir') . '/public/uploads';
            $newFilename = md5(uniqid()) . '.' . $image->guessExtension();
            $image->move(
                $destination,
                $newFilename
            );
            $user->setImage($newFilename);
        }
        
        $doctrine->getManager()->flush();
        $this->addFlash('message', 'Profile has been updated');
        return $this->redirectToRoute('account', ['id'=>$user->getId()]);
    }

     /**
      * View account information as Admin
      *
      * @param ManagerRegistry $doctrine
      * @param $id
      *
      * @return Response
      *
     * @Route("/account/{id}", name="account_admin")
     */
    public function account_admin(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(User::class);
        $user = $repository->find($id);

        if (is_null($user)) {
            return new Response('Not found 404', 404);
          }

        return $this->render('user/account.html.twig', [
            'user'=>$user
        ]);
    }

    // EMPLOYEES //

    /**
     * Show all employees = users to admin
     * 
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     * 
     * @Route("/employees", name="employees_admin")
     */
    public function show_users_admin(ManagerRegistry $doctrine):Response
    {
        $repository = $doctrine->getRepository(User::class);
        $users = $repository->findAll();

        if (is_null($users)) {
            return new Response('Not found 404', 404);
          }

        return $this->render('user/show_all_users.html.twig', [
            'users'=>$users
        ]);
    }


     /**
      * Edit employee information form - gotta finish
      *
      * @param ManagerRegistry $doctrine
      * @param $id
      * @return Response
      *
     * @Route("edit_employee/{id}", name="edit_employee")
     */
    public function edit_employee(ManagerRegistry $doctrine, int $id): Response
    {
        // find user
        $repository = $doctrine->getRepository(User::class);
        $user = $repository->find($id);

        if (is_null($user)) {
            return new Response('Not found 404', 404);
        }

        return $this->render('company/edit.html.twig', [
            'user' =>$user
        ]);
    }

     /**
      * Submission of edit employee form - gotta finish
      *
      * @param ManagerRegistry $doctrine
      * @param $id
      *
      * @return Response
     * @Route("/update_employee/{id}", name="update_employee")
     */
    public function update_employee(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Companies::class);
        $user = $repository->find($id);

        if (is_null($user)) {
            return new Response('Not found 404', 404);
        }

        // Set keys
        if ($name = $request->request->get('name')) {
            $user->setName($name);
        }
        if ($email = $request->request->get('email')) {
            $user->setEmail($email);
        }
        if ($address = $request->request->get('address')) {
            $user->setAddress($address);
        }
        if ($city = $request->request->get('city')) {
            $user->setCity($city);
        }
        if ($country = $request->request->get('country')) {
            $user->setCountry($country);
        }
        if ($zipCode = $request->request->get('zipCode')) {
            $user->setAddress($zipCode);
        }
        if ($phone = $request->request->get('phone')) {
            $user->setPhone($phone);
        }
        // Save
        $doctrine->getManager()->flush();
        $this->addFlash('success', 'The company information has been edited.');
        return $this->redirectToRoute('employees_admin');
    }

     /**
      * Delete Employee as admin - gotta finish
      *
      * @param ManagerRegistry $doctrine
      * @param $id
      *
      * @return Response
      *
     * @Route("/delete_employee/{id}", name="delete_employee")
     */
    public function delete_employee(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(User::class);
        $user = $repository->find($id);
        $entityManager = $doctrine->getManager();

        if (is_null($user)) {
            return new Response('Not found 404', 404);
        }

        // Remove
        $entityManager->remove($user);

        // Commit
        $entityManager->flush();

        $this->addFlash('message', 'User has been deleted');

        return $this->redirectToRoute('employees_admin');
    }



    // COMPANIES //

    /**
     * Show all companies to admin
      * 
      * @param ManagerRegistry $doctrine
      * @param $id
      *
      * @return Response
      *
     * @Route("/company", name="company_admin")
     */
    public function company_admin(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Companies::class);
        $companies = $repository->findAll();
        return $this->render('company/company.html.twig', [
            'companies'=>$companies
        ]);
    }

     /**
      * Create company form as admin
      * @return Response
      *
     * @Route("create_company", name="create_company_admin")
     */
    public function create(): Response
    {
        return $this->render('company/create_company.html.twig', [
            'controller_name' => 'ContactController',
            'user' => 'Karla'
        ]);
    }

    /**
     * Submission add company form as admin
     * 
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * 
     * @return Response
     * 
     * @Route("/add_company_form", name="add_company_form_admin")
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
      * Edit company form as admin
      * @param ManagerRegistry $doctrine
      * @param $id
      *
      * @return Response
      *
     * @Route("edit_company/{id}", name="edit_company_admin")
     */
    public function edit_company_admin(ManagerRegistry $doctrine, int $id): Response
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
      * Submission of edit comapny form as admin
      *
      * @param ManagerRegistry $doctrine
      * @param Request $request
      * @param $id
      *
      * @return Response
      *
     * @Route("/update_company/{id}", name="update_company_admin")
     */
    public function update_company_admin(Request $request, ManagerRegistry $doctrine, $id): Response
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
        return $this->redirectToRoute('company_admin');
    }

     /**
      * Delete company as admin
      * @param ManagerRegistry $doctrine
      * @param $id
      *
      * @return Response
      *
     * @Route("/delete_company_admin/{id}", name="delete_company_admin")
     */
    public function delete_company_admin(ManagerRegistry $doctrine, $id): Response
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
        return $this->redirectToRoute('company_admin');
    }
}
