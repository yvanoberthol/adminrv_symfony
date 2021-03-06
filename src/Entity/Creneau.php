<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CreneauRepository")
 */
class Creneau
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="time")
     * @Assert\Time(message="Ce champ doit être un temps normal")
     */
    private $heure_debut;

    /**
     * @ORM\Column(type="time")
     * @Assert\Time(message="Ce champ doit être un temps normal")
     */
    private $heure_fin;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Medecin", inversedBy="creneaus")
     */
    private $medecin;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeureDebut(): ?\DateTimeInterface
    {
        return $this->heure_debut;
    }

    public function setHeureDebut(\DateTimeInterface $heure_debut)
    {
        $this->heure_debut = $heure_debut;
    }

    public function getHeureFin(): ?\DateTimeInterface
    {
        return $this->heure_fin;
    }

    public function setHeureFin(\DateTimeInterface $heure_fin)
    {
        $this->heure_fin = $heure_fin;
    }

    public function getMedecin(): ?Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(?Medecin $medecin)
    {
        $this->medecin = $medecin;
    }

    public function getCreno()
    {
        return $this->heure_debut->format('h:i') . ' — ' . $this->heure_fin->format('h:i');
    }
}
