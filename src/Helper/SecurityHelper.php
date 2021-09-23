<?php

namespace App\Helper;

use App\Entity\User;
use LogicException;
use Psr\Container\ContainerInterface;

class SecurityHelper
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Checks if the attribute is granted against the current authentication token and optionally supplied subject.
     *
     * @throws LogicException
     */
    protected function isGranted($attribute, $subject = null): bool
    {
        if (!$this->container->has('security.authorization_checker')) {
            throw new LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
        }

        return $this->container->get('security.authorization_checker')->isGranted($attribute, $subject);
    }

    /**
     * Est-ce que l'utilisateur a rôles égal ou + élevé que
     * @param User $user
     * @return bool
     */
    public function aRolePlusGrandQue(User $user): bool
    {
        if (is_null($user)) return false;

        $roles = $user->getRoles();

        foreach ($roles as $role) {
            if (!$this->isGranted($role)) {
                return false;
            }
        }

        return true;
    }

}
