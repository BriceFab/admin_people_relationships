<?php

namespace App\Entity;

use App\Classes\Interfaces\EntityInterface;
use App\Entity\Common\IdTrait;
use App\Repository\TraductionRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=TraductionRepository::class)
 */
class Traduction implements EntityInterface
{
    use IdTrait;
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $domain;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $locale;

    /**
     * @ORM\Column(type="string", length=255, name="`key`")
     */
    private $key;

    /**
     * @ORM\Column(type="text")
     */
    private $translation;

    public function __construct()
    {
        //default domain
        $this->setDomain('messages');
    }

    /**
     * @return string|null
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return Traduction
     */
    public function setDomain(string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return Traduction
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return Traduction
     */
    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTranslation(): ?string
    {
        return $this->translation;
    }

    /**
     * @param string $translation
     *
     * @return Traduction
     */
    public function setTranslation(string $translation): self
    {
        $this->translation = $translation;
        return $this;
    }

    public function load(array $params): EntityInterface
    {
        foreach ($params as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getKey();
    }
}
