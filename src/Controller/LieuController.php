<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LieuController extends AbstractController
{
    /**
     * @Route("/lieu", name="lieux_liste")
     */
    public function lieux(LieuRepository $lr): Response
    {
        $lieux = $lr->findAll();
        return $this->render('lieu/lieux.html.twig', ["lieux"=>$lieux]);
    }


    /**
     * @Route("/lieu/create", name="lieu_create")
     */
    public function create(EntityManagerInterface $em, Request $request): Response
    {
        $lieu = new Lieu();
        $lieuForm = $this ->createForm(LieuType::class, $lieu);
        $lieuForm ->handleRequest($request);

        if ($lieuForm->isSubmitted()) {
            $em->persist($lieu);
            $em->flush();
            $this -> addFlash ('succes', 'Lieu créée!');
            return $this -> redirectToRoute('main_home');
        }

        return $this->render('lieu/create.html.twig', ["lieuForm" => $lieuForm->createView()]);
    }
}
