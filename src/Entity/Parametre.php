<?php

namespace App\Entity;

use App\Entity\Common\IdTrait;
use App\Repository\ParametreRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ParametreRepository::class)
 * @UniqueEntity(fields={"cle"}, message="Cette cle existe déjà")
 * @Gedmo\Loggable
 */
class Parametre
{
    use IdTrait;
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cle;

    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="text", nullable=true)
     */
    private $valeur;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateFinValidite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function getCle(): ?string
    {
        return $this->cle;
    }

    public function setCle(string $cle): self
    {
        $this->cle = $cle;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(?string $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getDateFinValidite(): ?\DateTimeInterface
    {
        return $this->dateFinValidite;
    }

    public function setDateFinValidite(?\DateTimeInterface $dateFinValidite): self
    {
        $this->dateFinValidite = $dateFinValidite;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function __toString()
    {
        return $this->getCle();
    }
}
