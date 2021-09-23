<?php

namespace App\Entity\Common;

use Doctrine\ORM\Mapping as ORM;

trait EnableTrait
{

    /**
     * @ORM\Column(type="boolean")
     */
    private $enable = true;

    public function getEnable(): ?bool
    {
        return $this->enable;
    }

    public function setEnable(bool $enable): self
    {
        $this->enable = $enable;

        return $this;
    }

}
