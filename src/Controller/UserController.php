<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserController extends AbstractController
{
    /**
     * Submission to update User profile
     * 
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param $id
     * @param $user
     * 
     * @return Response
     * 
     * @Route("/profile/account/update/{id}", name="update_profile", methods={"POST"})
     */
    public function update(Request $request, User $user, ManagerRegistry $doctrine, $id): Response
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
      * See his profile as User
      *
      * @param ManagerRegistry $doctrine
      * @param $id
      * 
      * @return Response
      *
     * @Route("/profile/account/{id}", name="account")
     */
    public function account(ManagerRegistry $doctrine, $id): Response
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

    /**
     * Show all employess as User
     * 
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     * 
     * @Route("/profile/employees", name="employees")
     */
    public function show_users(ManagerRegistry $doctrine):Response
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
}
