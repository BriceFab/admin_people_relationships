<?php

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @var $routeCollection RouteCollection
     */
    private $routeCollection;

    /**
     * @var $urlMatcher UrlMatcher;
     */
    private $urlMatcher;

    private $parameterBag;

    private $oldUrl;
    private $newUrl;
    private $languages;
    private $defaultLanguage;

    public function __construct(RouterInterface $router, $languages, string $defaultLocale = 'fr', ParameterBagInterface $parameterBag)
    {
        $this->routeCollection = $router->getRouteCollection();
        $this->languages = $languages;
        $this->defaultLanguage = $defaultLocale;
        $context = new RequestContext("/");
        $this->urlMatcher = new UrlMatcher($this->routeCollection, $context);
        $this->parameterBag = $parameterBag;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        //GOAL:
        // Redirect all incoming requests to their /locale/route equivalent when exists.
        // Do nothing if it already has /locale/ in the route to prevent redirect loops
        // Do nothing if the route requested has no locale param

        $request = $event->getRequest();
        $this->newUrl = $request->getPathInfo();
        $this->oldUrl = $request->headers->get('referer');
        $locale = $this->checkLanguage();
        if ($locale === null) return;

        $request->setLocale($locale);

        $pathLocale = "/" . $locale . $this->newUrl;

        //We have to catch the ResourceNotFoundException
        try {
            //Try to match the path with the local prefix
            $this->urlMatcher->match($pathLocale);
            $event->setResponse(new RedirectResponse($pathLocale));
        } catch (ResourceNotFoundException | MethodNotAllowedException $e) {
            try {
                //On essaie en enlevant le / à la fin de la route
                if (substr($pathLocale, -1) === "/") {
                    $pathLocale = substr($pathLocale, 0, -1);
                }

                //Try to match the path with the local prefix
                $this->urlMatcher->match($pathLocale);
                $event->setResponse(new RedirectResponse($pathLocale));
            } catch (ResourceNotFoundException | MethodNotAllowedException $e) {
                if (strtolower($this->parameterBag->get('APP_ENV')) === "prod" && !str_contains($pathLocale, "_wdt") && !str_contains($pathLocale, "_profiler")) {
                    throw $e;
                }
            }
        }

        /*
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        // try to see if the locale has been set as a _locale routing parameter
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
        */
    }

    private function checkLanguage()
    {
        foreach ($this->languages as $language) {
            if (preg_match_all("/\/$language\//", $this->newUrl))
                return null;
            if (preg_match_all("/\/$language\//", $this->oldUrl))
                return $language;
        }

        return $this->defaultLanguage;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
