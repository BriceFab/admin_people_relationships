<?php

namespace App\EventListener;

use App\Entity\ExceptionLog;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LogException implements EventSubscriberInterface
{
    private $em;
    private $tokenStorage;
    private $container;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, ContainerInterface $container)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::EXCEPTION => [
                ['processException', 10],
            ],
        ];
    }

    public function processException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $log = new ExceptionLog();
        $log->setMethod($event->getRequest()->getMethod());
        $log->setUri($event->getRequest()->getUri());
        if ($this->tokenStorage->getToken() !== null && $this->tokenStorage->getToken()->isAuthenticated()) {
            $user = $this->tokenStorage->getToken()->getUser();
            if ($user instanceof User) { //n'est pas anonymous
                /** @var User $user_entity */
                $user_entity = $this->em->getRepository(User::class)->find($user->getId());
                if (!is_null($user_entity)) {
                    $log->setUser($user_entity->getUserIdentifier());
                }
            }
        }
        $log->setExceptionMessage($exception->getMessage());
        $code = null;
        if (property_exists($exception, 'statusCode')) {
            $code = $exception->getStatusCode();
        } else {
            $code = $exception->getCode();
        }
        if (is_int($code)) {
            $log->setExceptionCode($code);
        }
        $log->setIp($event->getRequest()->getClientIp());
        $log->setCreatedAt(new DateTime('now'));

        try {
            if (!$this->em->isOpen()) {
                /** @var ManagerRegistry $doctrine */
                $doctrine = $this->container->get('doctrine');
                $doctrine->resetManager();
            }

            if ($this->em->isOpen()) {
                $this->em->persist($log);
                $this->em->flush();
            }
        } catch (Exception $e) {

        }
    }
}
