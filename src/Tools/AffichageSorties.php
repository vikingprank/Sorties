<?php
namespace App\Tools;

use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AffichageSorties{
  public function affichageSorties(SortieRepository $sr, CampusRepository $cr, AbstractController $ac){
      //reaffichage du SELECT *
      $sorties = $sr->findAll();
      $campus = $cr->findAll();

      //return $ac->render('sortie/sorties.html.twig', ["sorties"=>$sorties, "campus"=>$campus]);
  }
}