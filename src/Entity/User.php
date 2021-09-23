<?php

namespace App\Entity;

use App\Entity\Common\EnableTrait;
use App\Entity\Common\IdTrait;
use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=true, hardDelete=true)
 * @Gedmo\Loggable
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use IdTrait;
    use BlameableEntity;
    use TimestampableEntity;
    use SoftDeleteableEntity;
    use EnableTrait;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $username;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $derniereConnexion;

    public function __construct()
    {
        //Génération d'un pseudonyme par défaut
        $this->setUsername(uniqid("user_"));

        //Par défaut le compte est activé
        $this->setEnable(true);
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getDerniereConnexion(): ?DateTimeInterface
    {
        return $this->derniereConnexion;
    }

    public function setDerniereConnexion(?DateTimeInterface $derniereConnexion): self
    {
        $this->derniereConnexion = $derniereConnexion;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getUserIdentifier();
    }
}
