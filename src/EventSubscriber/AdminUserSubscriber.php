<?php

namespace App\EventSubscriber;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserSubscriber implements EventSubscriberInterface
{
	private $passwordEncoder;

	public function __construct(UserPasswordHasherInterface $passwordEncoder)
	{
		$this->passwordEncoder = $passwordEncoder;
	}

	public function updateUserPasswordAfterPersit(BeforeEntityPersistedEvent $event)
	{
		$entity = $event->getEntityInstance();
		if (!($entity instanceof User)) {
			return;
		}
		$this->updateUserPassword($entity);
	}

	private function updateUserPassword(User $user)
	{
		if (!is_null($user->getPassword())) {
			$user->setPassword(
				$this->passwordEncoder->hashPassword(
					$user,
					$user->getPassword()
				)
			);
		}
	}

	public static function getSubscribedEvents(): array
	{
		return [
			BeforeEntityPersistedEvent::class => ['updateUserPasswordAfterPersit'],
		];
	}
}
