<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VilleControlleurController extends AbstractController
{
    /**
     * @Route("/villes", name="villes_liste")
     */
    public function villes(VilleRepository $vr): Response
    {
        $villes = $vr->findAll();
        return $this->render('ville/villes.html.twig', ["villes"=>$villes]);
    }
    /**
     * @Route("/ville/create", name="ville_create")
     */
    public function create(EntityManagerInterface $em, Request $request): Response
    {
        $ville = new Ville();
        $villeForm = $this ->createForm(VilleType::class, $ville);
        $villeForm ->handleRequest($request);

        if ($villeForm->isSubmitted()) {
            $em->persist($ville);
            $em->flush();
            $this -> addFlash ('succes', 'Ville créée!');
            return $this -> redirectToRoute('main_home');
        }

        return $this->render('lieu/create.html.twig', ["lieuForm" => $villeForm->createView()]);
    }
}
