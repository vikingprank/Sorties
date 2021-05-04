<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="sortie_liste")
     */
    public function sorties(SortieRepository $sr, CampusRepository $cr): Response
    {
        $sorties = $sr->findAll();
        $campus = $cr->findAll();

        return $this->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
    }
    /**
     * @Route("/sortie_by_campus/{id}", name="sortie_by_campus")
     */
    public function sortiesByCampus($id, SortieRepository $sr, CampusRepository $cr): Response
    {
        //$sorties = $sr->findSortiesByCampus($id);
        $sorties = $sr->findAll();
        
        $index = 0;
        foreach ($sorties as $sortie) {
            if ($sortie->getCampus()->getId() != $id) {
                array_splice($sorties, $index, 1);
            }
            $index++;
        }
        
        //dd($sorties);
        //pour le re-affichage du filtre
        $campus = $cr->findAll();

        return $this->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
    }
    /**
     * @Route("/sortie/create", name="sortie_create")
     */
    public function create(EntityManagerInterface $em, Request $request): Response
    {
        $sortie = new Sortie();
        $sortie -> setDateCreation(new \DateTime());
        $prenomUserConnected = $this->getUser()->getPrenom();
        $sortie -> setOrganisateur($prenomUserConnected);

        $sortieForm = $this -> createForm(SortieType::class, $sortie);
        $sortieForm -> handleRequest($request);

        if ($sortieForm->isSubmitted()) {
            $em->persist($sortie);
            $em->flush();
            $this -> addFlash ('succes', 'Sortie créée!');
            return $this -> redirectToRoute('main_home');
        }

        return $this->render('sortie/create.html.twig', ["sortieForm" => $sortieForm->createView()]);
    }
    /**
     * @Route("/sortie/participer/{id}", name="sortie_participer")
     */
    public function participer($id, SortieRepository $sr, CampusRepository $cr, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();
        $user = new User();

        $sortie = $sr->findOneBy(['id'=>$id]);
        $user = $this->getUser();

        if (!$user->getSorties()->contains($sortie) && !$sortie->getParticipants()->contains($user)) {
            $sortie->getParticipants()->add($user);
            $user->getSorties()->add($sortie);
            $em->flush();
        } else {
            $this -> addFlash ('warning', 'Tu participes déjà à cette sortie!');
            $sorties = $sr->findAll();
            $campus = $cr->findAll();

            return $this->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
        }

        $this -> addFlash ('succes', 'Tu participes à la sortie!');
        //reaffichage du SELECT *
        $sorties = $sr->findAll();
        $campus = $cr->findAll();

        return $this->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
    }
    /**
     * @Route("/sortie/se_desister/{id}", name="sortie_se_desister")
     */
    public function seDesister($id, SortieRepository $sr, CampusRepository $cr, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();
        $user = new User();

        $sortie = $sr->findOneBy(['id'=>$id]);
        $user = $this->getUser();
        
        if ($user->getSorties()->contains($sortie) && $sortie->getParticipants()->contains($user)) {
            $sortie->removeParticipant($user);
            $user->removeSorty($sortie); 
            $em->flush();
        } else {
            $this -> addFlash ('warning', 'Tu ne peut pas de desister si tu ne participes pas!');
            $sorties = $sr->findAll();
            $campus = $cr->findAll();

            return $this->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
        }
        
        $this -> addFlash ('succes', 'Dommage que tu ne puisse pas venir!');
        //reaffichage du SELECT *
        $sorties = $sr->findAll();
        $campus = $cr->findAll();

        return $this->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
    }
}
