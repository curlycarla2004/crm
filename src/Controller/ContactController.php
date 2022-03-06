<?php

namespace App\Controller;

use App\Entity\Companies;
use App\Entity\Contacts;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends AbstractController
{
    /**
     * @Route("/profile/contact", name="contact")
     */
    public function index( ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

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
     * @Route("/profile/create_contact", name="create_contact")
     */
    public function create(): Response
    {
        return $this->render('contact/create_contact.html.twig', [
            'controller_name' => 'ContactController',
            'user' => 'Karla'
        ]);
    }


    /**
     * @Route("/profile/add_contact_form", name="add_contact_form")
     */
    public function add_contact_form(ManagerRegistry $doctrine, Request $request): Response
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

        
        return $this->render('contact/create_contact.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * @Route("/profile/edit_contact/{id}", name="edit_contact")
     */
    public function edit(ManagerRegistry $doctrine, Request $request, int $id): Response
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
     * @Route("/profile/update_contact/{id}", name="update_contact")
     */
    public function update(Request $request, ManagerRegistry $doctrine, $id): Response
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

        return $this->redirectToRoute('contact');
    }

     /**
     * @Route("/profile/delete_contact/{id}", name="delete_contact")
     */
    public function delete(Request $request, ManagerRegistry $doctrine, $id): Response
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

        return $this->redirectToRoute('contact');
    }

    
}
