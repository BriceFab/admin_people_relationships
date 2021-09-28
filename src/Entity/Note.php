<?php

namespace App\Entity;

use App\Entity\Common\IdTrait;
use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 */
class Note
{
    use IdTrait;
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Personne::class, inversedBy="notes")
     */
    private $personne;

    /**
     * @ORM\ManyToOne(targetEntity=Relation::class, inversedBy="notes")
     */
    private $relation;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getRelation(): ?Relation
    {
        return $this->relation;
    }

    public function setRelation(?Relation $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    public function __toString(): string
    {
        return 'Note #' . ($this->getType() ?? $this->getId());
    }
}
