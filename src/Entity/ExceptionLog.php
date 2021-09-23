<?php

namespace App\Entity;

use App\Entity\Common\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExceptionLogRepository")
 */
class ExceptionLog
{
    use IdTrait;
    use BlameableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $method;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $uri;

    /**
     * @ORM\Column(type="text")
     */
    private $exceptionMessage;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $exceptionCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $user;

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(?string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(?string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getExceptionMessage(): ?string
    {
        return $this->exceptionMessage;
    }

    public function setExceptionMessage(string $exceptionMessage): self
    {
        $this->exceptionMessage = $exceptionMessage;

        return $this;
    }

    public function getExceptionCode(): ?int
    {
        return $this->exceptionCode;
    }

    public function setExceptionCode(?int $exceptionCode): self
    {
        $this->exceptionCode = $exceptionCode;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function __toString()
    {
        return $this->getUri();
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

}
