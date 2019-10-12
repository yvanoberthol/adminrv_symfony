<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MedecinRepository")
 * @UniqueEntity(fields={"nom"},message="ce médecin possède déjà ce nom")
 */
class Medecin
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Le nom ne doit pas être vide")
     * @Assert\Length(min="3",minMessage="Le nom doit être d'au moins 3 caractères")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Le prénom ne doit pas être vide")
     * @Assert\Length(min="3",minMessage="Le prénom doit être d'au moins 3 caractères")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le prénom ne doit pas être vide")
     * @Assert\Email(message="Entrez une adresse Email valide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=15)
     * @Assert\NotBlank(message="Le matricule ne doit pas être vide")
     */
    private $matricule;

    /**
     * @ORM\Column(type="string", length=9)
     * @Assert\NotBlank(message="Le téléphone ne doit pas être vide")
     * @Assert\Positive()
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(min="4",minMessage="La ville doit contenir au moins 4 caractères")
     */
    private $ville;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date(message="Entrez une date valide")
     */
    private $date_naissance;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ImageMedecin",cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Specialite",cascade={"persist"},inversedBy="medecins")
     * @ORM\JoinTable(name="medecin_specialite")
     * @Assert\Valid()
     */
    private $specialites;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\CompteMedecin", mappedBy="medecin", cascade={"persist", "remove"})
     */
    private $compteMedecin;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Creneau", mappedBy="medecin")
     */
    private $creneaus;

    public function __construct()
    {
        $this->creneaus = new ArrayCollection();
        $this->specialites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom)
    {
        $this->nom = $nom;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom)
    {
        $this->prenom = $prenom;
    }

    public function getAllName(){
        return $this->nom.' '.$this->prenom;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(string $matricule)
    {
        $this->matricule = $matricule;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville)
    {
        $this->ville = $ville;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance)
    {
        $this->date_naissance = $date_naissance;
    }

    /**
     * @return ImageMedecin
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param ImageMedecin $image
     */
    public function setImage(ImageMedecin $image = null)
    {
        $this->image = $image;
    }

    public function getCompteMedecin(): ?CompteMedecin
    {
        return $this->compteMedecin;
    }

    public function setCompteMedecin(?CompteMedecin $compteMedecin)
    {
        $this->compteMedecin = $compteMedecin;

        // set (or unset) the owning side of the relation if necessary
        $newMedecin = $compteMedecin === null ? null : $this;
        if ($newMedecin !== $compteMedecin->getMedecin()) {
            $compteMedecin->setMedecin($newMedecin);
        }

    }

    /**
     * @return Collection|Specialite[]
     */
    public function getSpecialites(): Collection
    {
        return $this->specialites;
    }

    /**
     * @param  Specialite
     * @return Medecin
     */
    public function addSpecialite(Specialite $specialite)
    {
        if (!$this->specialites->contains($specialite)) {
            $this->specialites[] = $specialite;
        }

        return $this;
    }

    /**
     * @param  Specialite
     * @return Medecin
     */
    public function removeSpecialite(Specialite $specialite): self
    {
        if ($this->specialites->contains($specialite)) {
            $this->specialites->removeElement($specialite);
        }

        return $this;
    }

    /**
     * @return Collection|Creneau[]
     */
    public function getCreneaus(): Collection
    {
        return $this->creneaus;
    }

    public function addCreneau(Creneau $creneau): self
    {
        if (!$this->creneaus->contains($creneau)) {
            $this->creneaus[] = $creneau;
            $creneau->setMedecin($this);
        }

        return $this;
    }

    public function removeCreneau(Creneau $creneau): self
    {
        if ($this->creneaus->contains($creneau)) {
            $this->creneaus->removeElement($creneau);
            // set the owning side to null (unless already changed)
            if ($creneau->getMedecin() === $this) {
                $creneau->setMedecin(null);
            }
        }

        return $this;
    }
}
