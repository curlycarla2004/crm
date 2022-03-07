<?php

namespace App\Controller;

use App\Entity\Companies;
use App\Entity\Contacts;
use App\Entity\User;
use App\Form\ContactsType;
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
        ]);
    }

    /**
     * @Route("/profile/add_contact_form", name="add_contact_form")
     */
    public function add_contact_form(ManagerRegistry $doctrine, Request $request): Response
    {

        $form = $this->createForm(ContactsType::class);

        $form->submit($request->request->all());

        // if (!$form->isValid()) { //validate form info in UserFormType
        //     $errors = $form->getErrors(true); // Array of Error
        //     foreach ($errors as $error) {
        //         $this->addFlash('error', $error->getMessage());
        //     }
        //     return $this->redirectToRoute('create_contact');
        // }
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

        $this->addFlash('message', 'New contact has been added');
        return $this->render('contact/create_contact.html.twig', [
            'contact' => $contact,
        ]);
    }

}
