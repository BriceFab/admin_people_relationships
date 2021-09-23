<?php

namespace App\Loader;

use App\Classes\Interfaces\EntityInterface;
use App\Classes\Interfaces\TranslateDbLoaderInterface;
use App\Classes\Interfaces\TranslationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Yaml\Parser as YamlParser;
use Symfony\Component\Yaml\Yaml;
use function sprintf;

class TranslateDbLoader implements LoaderInterface, TranslateDbLoaderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $doctrine;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * DbLoader constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $doctrine
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->entityClass = $container->getParameter('db_i18n.entity');
    }

    /**
     * Loads a locale.
     *
     * @param mixed $resource A resource
     * @param string $locale A locale
     * @param string $domain The domain
     *
     * @return MessageCatalogue A MessageCatalogue instance
     *
     * @throws NotFoundResourceException when the resource cannot be found
     * @throws InvalidResourceException  when the resource cannot be loaded
     */
    public function load($resource, string $locale, string $domain = 'messages')
    {
        // transaltes from db
        $messages = $this->getRepository()->findByDomainAndLocale($domain, $locale);

        $values = array_map(static function (EntityInterface $entity) {
            return $entity->getTranslation();
        }, $messages);

        //* translates .db
        $yamlParser = new YamlParser();
        $ymlContents = $yamlParser->parseFile($resource, Yaml::PARSE_CONSTANT) ?? [];

        //Merges les 2 arrays (db et yaml)
        if (count($values) === 0) {
            $merges_values = $ymlContents;
        } else {
            $merges_values = array_merge($ymlContents, $values);
        }

        return new MessageCatalogue($locale, [
            $domain => $merges_values,
        ]);
    }

    /**
     * @return TranslationRepositoryInterface
     */
    public function getRepository(): TranslationRepositoryInterface
    {
        $repository = $this->doctrine->getRepository($this->entityClass);
        if ($repository instanceof TranslationRepositoryInterface) {
            return $repository;
        }

        throw new RuntimeException(sprintf('Cannot load repository %s', TranslationRepositoryInterface::class));
    }
}
