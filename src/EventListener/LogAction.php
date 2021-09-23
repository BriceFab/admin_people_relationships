<?php

namespace App\EventListener;

use App\Entity\ActionLog;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LogAction implements EventSubscriberInterface
{
    private $em;
    private $tokenStorage;
    private $unlogUri = [
        '/login',
    ];

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse'
        ];
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $method = $event->getRequest()->getMethod();
        $uri = $event->getRequest()->getRequestUri();
        if (strtoupper($event->getRequest()->getMethod()) !== 'GET' && $this->mustLogsUri($uri)) {
            $log = new ActionLog();
            $log->setMethod($method);
            $log->setUri($uri);
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
            $log->setRequestAt(new DateTime('now'));
            $log->setResponseCode($event->getResponse()->getStatusCode());
            $log->setIp($event->getRequest()->getClientIp());

            try {
                $this->em->persist($log);
                $this->em->flush();
            } catch (Exception $e) {
            }
        }
    }

    private function mustLogsUri($uri)
    {
        if (in_array($uri, $this->unlogUri)) {
            return false;
        } else {
            foreach ($this->unlogUri as $key => $value) {
                if (strpos($uri, $value) !== false) {
                    return false;
                }
            }
        }

        return true;
    }
}
