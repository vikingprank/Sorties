<?php
namespace App\Tools;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\EtatRepository;

class UpdateEtat {
  public function publierSortie(Sortie $sortie, EtatRepository $er){
    $etat = new Etat();
    //$etat->setLabel("Ouverte");
    $etat = $er->findOneBy(['label'=>"Ouverte"]);
    $sortie->setEtat($etat);
    return $sortie;
  }
  public function annulerSortie(Sortie $sortie, EtatRepository $er){
    $etat = new Etat();
    $etat = $er->findOneBy(['label'=>"Annulée"]);
    $sortie->setEtat($etat);
    return $sortie;
  }
  public function testDate(Sortie $sortie, EtatRepository $er){
    $etat = new Etat();
    $dateFinInscription = $sortie->getDateFinInscription();
    $dateToday = new \DateTime();
    $format = "d m Y";
    $dateSortieSansHeure = date_format($sortie->getDateSortie(), $format);
    $dateTodaySansHeure = date_format($dateToday, $format);
    $dateSortie = $sortie->getDateSortie();
    
    if ($dateFinInscription<$dateToday) {
      $etat = $er->findOneBy(['label'=>"Clôturée"]);
      $sortie->setEtat($etat);
    }
    if ($dateSortie<$dateToday) {
      $etat = $er->findOneBy(['label'=>"Passée"]);
      $sortie->setEtat($etat);
    }
    if ($dateSortieSansHeure == $dateTodaySansHeure) {
      $etat = $er->findOneBy(['label'=>"Activité en cours"]);
      $sortie->setEtat($etat);
    }
    return $sortie;
  }
}

