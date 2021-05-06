<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditProfileType;
use App\Repository\UserRepository;
use App\Tools\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    
    /**
     *@Route("/user", name= "user_profil")
     */
    public function index()
    {
        return $this->render('user/index.html.twig');
}

    /**
     * @Route("/user/editprofil", name="user_editprofile")
     */
    public function editProfile(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $imageFile = $form->get('images')->getData();
            foreach($imageFile as $image){
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('upload_directory'),
                    $fichier
                );
                $user->setImage($fichier);
            }
            

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('user_profil');
        }

        return $this->render('user/editprofile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/viewotherprofile/{pseudo}", name="view_other_profile")
     */
    public function viewOtherProfile ($pseudo, UserRepository $ur): Response
    {
        $user = $ur->findOneby(['pseudo' => $pseudo]);
        
        return $this->render('user/viewotherprofile.html.twig', ["user" => $user]);
    }

}
