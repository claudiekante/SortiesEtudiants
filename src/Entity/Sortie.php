<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SortieRepository::class)
 * @ORM\Table(name="sortie", indexes={@ORM\Index(columns={"nom","infos_sortie"}, flags={"fulltext"})})
 */
class Sortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank (message="Ce champs est obligatoire")

     * @Assert\Length(max=255, min=3, minMessage="Le champ doit contenir au moins 3 caractères")
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank (message="Ce champs est obligatoire")
     * @Assert\GreaterThan("today", message="cette date doit être supérieur à la date du jour")
     */
    private $dateHeureDebut;

    /**
     * @ORM\Column(type="time")
     * @Assert\NotBlank (message="Ce champs est obligatoire")
     */
    private $duree;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank (message="Ce champs est obligatoire")
     * @Assert\LessThan(propertyPath="dateHeureDebut", message="Cette date doit être inferieur à la date de début de la sortie")
     */
    private $dateLimitInscription;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank (message="Ce champs est obligatoire")
     */
    private $nbInscriptionsMax;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank (message="Ce champs est obligatoire")
     * @Assert\Length(max=255)
     */
    private $infosSortie;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="sortie")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campus;

    /**
     * @ORM\ManyToOne(targetEntity=Etat::class, inversedBy="sortie")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="sortie")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;

    /**
     * @ORM\ManyToMany(targetEntity=Utilisateur::class, inversedBy="sorties")
     */
    private $participant;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="sortieOrganisee")
     *
     */
    private $Organisateur;



    public function __construct()
    {
        $this->participant = new ArrayCollection();


    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?\DateTimeInterface
    {
        return $this->duree;
    }

    public function setDuree(\DateTimeInterface $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimitInscription(): ?\DateTimeInterface
    {
        return $this->dateLimitInscription;
    }

    public function setDateLimitInscription(\DateTimeInterface $dateLimitInscription): self
    {
        $this->dateLimitInscription = $dateLimitInscription;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(string $infosSortie): self
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getParticipant(): Collection
    {
        return $this->participant;
    }

    public function addParticipant(Utilisateur $participant): self
    {
        if (!$this->participant->contains($participant)) {
            $this->participant[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(Utilisateur $participant): self
    {
        $this->participant->removeElement($participant);

        return $this;
    }

    public function getOrganisateur(): ?Utilisateur
    {
        return $this->Organisateur;
    }

    public function setOrganisateur(?Utilisateur $Organisateur): self
    {
        $this->Organisateur = $Organisateur;

        return $this;
    }

}
