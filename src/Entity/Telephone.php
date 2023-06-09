<?php

namespace App\Entity;

use App\Entity\Common\IdTrait;
use App\Repository\TelephoneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=TelephoneRepository::class)
 */
class Telephone
{
    use IdTrait;
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numero;

    /**
     * @ORM\ManyToOne(targetEntity=TelephoneType::class, inversedBy="telephones")
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity=Personne::class, mappedBy="telephones")
     */
    private $personnes;

    public function __construct()
    {
        $this->personnes = new ArrayCollection();
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getType(): ?TelephoneType
    {
        return $this->type;
    }

    public function setType(?TelephoneType $type): self
    {
        $this->type = $type;

        return $this;
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
            $personne->addTelephone($this);
        }

        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        if ($this->personnes->removeElement($personne)) {
            $personne->removeTelephone($this);
        }

        return $this;
    }

    public function __toString(): ?string
    {
        return $this->getNumero();
    }
}
