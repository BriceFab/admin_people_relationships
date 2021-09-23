<?php


namespace App\EventListener;

use App\Entity\Traduction;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

class TraductionListener
{
    private $parameter;
    private $kernel;
    private $env;

    public function __construct(ParameterBagInterface $parameter, KernelInterface $appKernel)
    {
        $this->parameter = $parameter;
        $this->env = strtolower($this->parameter->get('APP_ENV'));
        $this->kernel = $appKernel;
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        if ($event->getObject() instanceof Traduction) {
            $this->refreshTransalteCache();
        }
    }

    public function postPersist(LifecycleEventArgs $event)
    {
        if ($event->getObject() instanceof Traduction) {
            $entity = $event->getEntity();

            //Si le fichier de la langue n'existe pas, on le créer. Sinon les traductons ne seront pas prises en compte
            $domain = $entity->getDomain();
            $locale = $entity->getLocale();
            $fileTranslate = $this->parameter->get("kernel.project_dir") . DIRECTORY_SEPARATOR . "translations" . DIRECTORY_SEPARATOR . "$domain.$locale.db";

            if (!file_exists($fileTranslate)) {
                fopen($fileTranslate, "w");
            }

            $this->refreshTransalteCache();
        }
    }

    private function refreshTransalteCache()
    {
        $files = glob($this->kernel->getProjectDir() . "/var/cache/$this->env/translations/*"); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file))
                unlink($file); // delete file
        }
    }

}
