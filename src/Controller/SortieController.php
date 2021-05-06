<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Tools\UpdateEtat;
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
    public function sorties(EntityManagerInterface $em, UpdateEtat $ue, SortieRepository $sr, CampusRepository $cr, EtatRepository $er): Response
    {
        //AFFICHAGE DES SORTIES AVEC LE FITRE CAMPUS, MIS A JOUR DE L'ETAT ET REDIRECTION VERS SORTIES
        $sorties = $sr->findAll();
        $campus = $cr->findAll();
        foreach ($sorties as $sortie) {
            $ue->testDate($sortie, $er);
        }
        $em->flush();
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
                unset($sorties[$index]);
            }
            $index++;
        }
        
        //dd($sorties);
        //pour le re-affichage du filtre
        $campus = $cr->findAll();

        return $this->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
    }
    /**
     * @Route("/sortie/detail/{id}", name="sortie_detail")
     */
    public function detail($id, SortieRepository $sr): Response
    {
        $sortie = $sr->find($id);

        return $this->render('sortie/detail.html.twig', ["sortie" => $sortie]);
    }
    /**
     * @Route("/sortie/create", name="sortie_create")
     */
    public function create(EntityManagerInterface $em, Request $request, EtatRepository $er): Response
    {
        $sortie = new Sortie();
        $sortie -> setDateCreation(new \DateTime());
        $prenomUserConnected = $this->getUser()->getPseudo();
        $sortie -> setOrganisateur($prenomUserConnected);
        $etat = new Etat();
        $etat = $er->findOneBy(['label'=>"Créée"]);
        $sortie->setEtat($etat);

        $sortieForm = $this -> createForm(SortieType::class, $sortie);
        $sortieForm -> handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $em->persist($sortie);
            $em->flush();
            $this -> addFlash ('succes', 'Sortie créée!');
            return $this -> redirectToRoute('main_home');
        }

        return $this->render('sortie/create.html.twig', ["sortieForm" => $sortieForm->createView()]);
    }

    /**
     * @Route("/sortie/delete/{id}", name="sortie_delete")
     */
    public function delete($id, SortieRepository $sr, CampusRepository $cr, EntityManagerInterface $em, Request $request): Response
    {
        $sortie = new Sortie();
        $user = new User();

        $sortie = $sr->findOneBy(['id'=>$id]);
        $user = $this->getUser();
        
        if ($user->getPseudo() != $sortie->getOrganisateur()) {
            $this -> addFlash ('warning', 'Tu ne peut pas supprimer cette sortie!');
            //reaffichage du SELECT *
            $sorties = $sr->findAll();
            $campus = $cr->findAll();
    
            return $this->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
        } else {
            $em->remove($sortie);
            $em->flush();
            $this -> addFlash ('succes', 'Sortie supprimée!');
            //reaffichage du SELECT *
            $sorties = $sr->findAll();
            $campus = $cr->findAll();
    
            return $this->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
        }
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

        if (!$user->getSorties()->contains($sortie) && !$sortie->getParticipants()->contains($user) && count($sortie->getParticipants())<$sortie->getNombrePlaces() && $sortie->getEtat() == "Ouverte") {
            $sortie->getParticipants()->add($user);
            $user->getSorties()->add($sortie);
            $em->flush();
        } else {
            $this -> addFlash ('warning', "Les inscriptions ne sont pas ouvertes, il n'y a plus de places ou tu est déjà inscrit...");
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
        
        if ($user->getSorties()->contains($sortie) && $sortie->getParticipants()->contains($user) && $sortie->getEtat() == "Ouverte") {
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
    /**
     * @Route("/sortie/modifier/{id}", name="sortie_modifier")
     */
    public function modifier($id, SortieRepository $sr, CampusRepository $cr, EntityManagerInterface $em, Request $request): Response
    {
        $sortie = new Sortie();
        $user = new User();

        $sortie = $sr->findOneBy(['id'=>$id]);
        $user = $this->getUser();

        if ($user->getPseudo() != $sortie->getOrganisateur()) {
            $this -> addFlash ('warning', "Tu n'est pas l'oganisa(teur/trice), ne peut pas modifier cette sortie!");
            //reaffichage du SELECT *
            $sorties = $sr->findAll();
            $campus = $cr->findAll();
    
            return $this->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
        } else {
        
            $sortieForm = $this -> createForm(SortieType::class, $sortie);
            $sortieForm -> handleRequest($request);
            
            if ($sortieForm->isSubmitted()) {
                $em->persist($sortie);
                $em->flush();
                $this -> addFlash ('succes', 'Sortie mis à jour!');
                return $this -> redirectToRoute('main_home');
            }
            return $this->render('sortie/modifier.html.twig', ["sortie"=>$sortie, "sortieForm" => $sortieForm->createView()]);
        }
    }

    /**
     * @Route("/sortie/publier/{id}", name="sortie_publier")
     */
    public function publier($id, UpdateEtat $ue, EtatRepository $er, SortieRepository $sr, CampusRepository $cr, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();
        $user = new User();

        $sortie = $sr->findOneBy(['id'=>$id]);
        $user = $this->getUser();

        if ($user->getPseudo() != $sortie->getOrganisateur()) {
            $this -> addFlash ('warning', 'Tu ne peut pas publier cette sortie!');
            //reaffichage du SELECT *
            $sorties = $sr->findAll();
            $campus = $cr->findAll();
    
            return $this->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
        } else {
        
            $ue->publierSortie($sortie, $er);
            $em->flush();

            $this -> addFlash ('succes', 'Les inscriptions sont maintenant ouvertes pour ta sortie!');
            //reaffichage du SELECT *
            $sorties = $sr->findAll();
            $campus = $cr->findAll();

            return $this->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
        }
    }
    /**
     * @Route("/sortie/annuler/{id}", name="sortie_annuler")
     */
    public function annuler($id, UpdateEtat $ue, EtatRepository $er, SortieRepository $sr, CampusRepository $cr, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();
        $user = new User();

        $sortie = $sr->findOneBy(['id'=>$id]);
        $user = $this->getUser();

        if ($user->getPseudo() != $sortie->getOrganisateur()) {
            $this -> addFlash ('warning', 'Tu ne peut pas annuler cette sortie!');
            //reaffichage du SELECT *
            $sorties = $sr->findAll();
            $campus = $cr->findAll();
    
            return $this->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
        } else {
        
            $ue->annulerSortie($sortie, $er);
            $em->flush();

            $this -> addFlash ('succes', 'La sortie est annulée!');
            //reaffichage du SELECT *
            $sorties = $sr->findAll();
            $campus = $cr->findAll();

            return $this->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
        }
    }
}
