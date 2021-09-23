<?php
/**
 * https://raw.githubusercontent.com/andrew72ru/symfony-db-i18n-bundle
 */

declare(strict_types=1);

namespace App\Command;

use App\Classes\Interfaces\EntityInterface;
use App\Classes\Interfaces\TranslationRepositoryInterface;
use App\Loader\TranslateDbLoader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Yaml\Parser as YamlParser;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportAllTraductionsFilesCommand extends Command
{
    protected static $defaultName = 'd1f:translate:import:all';

    private const HELP = <<<EOL
Import tous les fichiers de traductions dans /translates
For example: <info>messages.fr.yaml</info>
             <info>messages.en.yaml</info> 
EOL;

    private const BATCH_SIZE = 100000;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var TranslatorInterface|Translator
     */
    private $translator;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TranslationRepositoryInterface
     */
    private $translationEntityRepository;

    private $parameter;

    /**
     * MigrateToDatabaseCommand constructor.
     *
     * @param ParameterBagInterface $parameterBag
     * @param ContainerInterface $container
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     * @param KernelInterface $kernel
     * @param string|null $name
     */
    public function __construct(ParameterBagInterface $parameterBag, ContainerInterface $container, TranslatorInterface $translator, EntityManagerInterface $em, KernelInterface $kernel, string $name = null)
    {
        parent::__construct($name);

        $this->container = $container;
        $this->translator = $translator;
        $this->entityClass = $this->container->getParameter('db_i18n.entity');
        $this->em = $em;
        $this->kernel = $kernel;
        $this->parameter = $parameterBag;
    }

    protected function configure()
    {
        $this->setDescription('Load data from translation file and pass it to database')
            ->setHelp(self::HELP)
            ->addArgument('domain', InputArgument::OPTIONAL, 'Entrez le nom du domain à importer')
            ->addArgument('force', InputArgument::OPTIONAL, 'Force la mise à jour des traductions');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->translationEntityRepository = $this->em->getRepository($this->entityClass);
        $helper = $this->getHelper('question');

        if (!method_exists($this->translator, 'getCatalogue')) {
            throw new RuntimeException('Translator service of application has no \'getCatalogue\' method');
        }

        if (!$this->container->hasParameter('locales') || !is_array($this->container->getParameter('locales'))) {
            throw new RuntimeException('Application container must have a \'locales\' parameter, and this parameter must be an array');
        }

        $forceArg = $input->getArgument('force');
        $force = isset($forceArg) && strtolower($forceArg) === 'force';

        $io = new SymfonyStyle($input, $output);
        $this->translator->addLoader('db', new TranslateDbLoader($this->container, $this->em));
//        $this->translator->addResource('db', $this->kernel->getProjectDir() . '\translations\\techno_war.fr.db', 'fr', 'techno_war');
//        $this->translator->addResource('db', $this->kernel->getProjectDir() . '\translations\\techno_war.de.db', 'de', 'techno_war');
//        $this->translator->addResource('db', $this->kernel->getProjectDir() . '\translations\\techno_war.en.db', 'en', 'techno_war');

        $finder = new Finder();
        $finder->files()->in($this->kernel->getProjectDir() . '/translations//');
        if (!$finder->hasResults()) {
            throw new RuntimeException('Aucun fichier de traduction trouvé dans \\translations\\');
        }

        foreach ($finder as $file) {
            $fileName = $file->getFilename();
            if (is_null($fileName) || !$fileName) {
                throw new RuntimeException('Vous devez spécifier un fichier');
            }

            if ($fileName === "messages.key.db") {
                //On importe pas ce fichier en db
                continue;
            }

            //Filtre par rapport au paramètres du domain
            $domain = $input->getArgument('domain');
            if (isset($domain) && !is_null($domain)) {
                if (strpos($fileName, $domain) === false) {
                    continue;
                }
            }

            $filePath = $this->locateFile($fileName);

            $io->write("Traitement du fichier $fileName -> $filePath \r\n");

            $locale = $this->getLocale(pathinfo($filePath, PATHINFO_FILENAME));
            $domain = trim(str_replace($locale, '', pathinfo($filePath, PATHINFO_FILENAME)), '.');
            if (is_null($fileName) || !$fileName) {
                throw new RuntimeException('Vous devez spécifier un domain');
            }

            $catalogue = $this->translator->getCatalogue($locale);
            $forExport = $catalogue->all($domain);

            if ($force) {
                $yamlParser = new YamlParser();
                $ymlContents = $yamlParser->parseFile((string)$file, Yaml::PARSE_CONSTANT);
                $forExport = array_merge($forExport, $ymlContents);
            }

            $exported = $this->exportToDatabase($forExport, $locale, $domain, $force);

            $io->writeln(sprintf(
                'Loaded form %s: %u messages, exported to database: %s',
                $filePath,
                count($forExport),
                $exported
            ));
        }

        if ($force) {
            //clear cache des traductions
            $env = $this->parameter->get('APP_ENV');
            $files = glob($this->kernel->getProjectDir() . "/var/cache/$env/translations/*"); // get all file names
            foreach ($files as $file) { // iterate files
                if (is_file($file)) {
                    unlink($file); // delete file
                }
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @param array $messages
     * @param string $locale
     * @param string $domain
     *
     * @param bool $force
     * @return int
     */
    protected function exportToDatabase(array $messages, string $locale, string $domain, bool $force = false): int
    {
        $count = 0;
        $i = 0;
        foreach ($messages as $key => $value) {
            ++$count;
            ++$i;

            if (!is_string($value)) {
                //n'importe pas si pas un string
                echo "la clé de traduction '$key' pas importée car n'est pas un string.\r\n";
                continue;
            }

            try {
                if (is_string($key)) {
                    $entity = $this->makeEntity($key, $value, $locale, $domain, $force);

                    $this->em->persist($entity);
                    $this->em->flush();
                } else {
                    echo "la clé $key n'est pas un string\r\n";
                }
            } catch (Exception $e) {
                echo "exception " . $e->getMessage() . "\r\n";
            }

            if ($i > self::BATCH_SIZE) {
                $i = 0;
                $this->em->flush();
            }
        }

        $this->em->flush();

        return $count;
    }

    /**
     * @param string $key
     * @param string $translation
     * @param string $locale
     * @param string $domain
     * @param bool $force
     * @return EntityInterface
     */
    protected function makeEntity(string $key, string $translation, string $locale, string $domain, bool $force = false): EntityInterface
    {
        $entity = $this->checkEntityExists($locale, $key);

        $entity->load([
            'domain' => $domain,
            'locale' => $locale,
            'key' => $key,
            'translation' => $translation,
        ]);

        if ($force) {
            if (method_exists($entity, 'setTranslation')) {
                $entity->setTranslation($translation);
            }
            if (method_exists($entity, 'setKey')) {
                $entity->setKey($key);
            }
        }

        return $entity;
    }

    /**
     * @param string $locale
     * @param string $key
     *
     * @return EntityInterface|object
     */
    protected function checkEntityExists(string $locale, string $key): EntityInterface
    {
        $entity = $this->translationEntityRepository->findOneBy([
            'locale' => $locale,
            'key' => $key,
        ]);

        if ($entity === null) {
            $entity = new $this->entityClass();
        }

        return $entity;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    protected function getLocale(string $filename): ?string
    {
        $locales = $this->container->getParameter('locales');
        $locale = null;
        foreach ($locales as $localeParam) {
            if (strpos($filename, $localeParam) !== false) {
                $locale = $localeParam;
            }
        }

        if ($locale === null) {
            throw new RuntimeException(sprintf('No one %s found in \'%s\'', implode(', ', $locales), $filename));
        }

        return $locale;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function locateFile(string $path): string
    {
        $realPath = null;
        if (strpos($path, '/') === 0) {
            $realPath = $path;
        } else {
            $realPath = $this->kernel->getProjectDir() . '/translations//' . $path;
        }

        if (!is_file($realPath) || !is_readable($realPath)) {
            throw new RuntimeException(sprintf('Unable to load %s file', $realPath));
        }

        return $realPath;
    }
}
