<?php

namespace App\Entity;

use App\Repository\RelationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RelationRepository::class)
 */
class Relation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=RelationType::class, inversedBy="relations")
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="relation", cascade={"persist"})
     */
    private $notes;

    /**
     * @ORM\ManyToMany(targetEntity=Personne::class, mappedBy="relations")
     */
    private $personnes;

    /**
     * @ORM\ManyToOne(targetEntity=Personne::class)
     */
    private $personne;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->personnes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?RelationType
    {
        return $this->type;
    }

    public function setType(?RelationType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setRelation($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getRelation() === $this) {
                $note->setRelation(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return ($this->getType() ?? '') . $this->getPersonne();
    }

    /**
     * @return Collection|Personne[]
     */
    public function getPersonnes(): Collection
    {
        return $this->personnes;
    }

    public function addPersonne(Personne $personne): self
    {
        if (!$this->personnes->contains($personne)) {
            $this->personnes[] = $personne;
            $personne->addRelation($this);
        }

        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->personnes->removeElement($personne)) {
            $personne->removeRelation($this);
        }

        return $this;
    }

    public function getPersonne(): ?Personne
    {
        return $this->personne;
    }

    public function setPersonne(?Personne $personne): self
    {
        $this->personne = $personne;

        return $this;
    }
}
