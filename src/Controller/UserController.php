<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user_profil')]
    public function index()
    {
        return $this->render('user/index.html.twig');
}

    /**
     * @Route("/user/editprofil", name="user_editprofile")
     */
    public function editProfile(Request $request)
    {
        $user = $this->getUser();;
        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user');
        }

        return $this->render('user/editprofile.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    }
