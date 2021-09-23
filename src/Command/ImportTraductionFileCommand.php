<?php
/**
 * https://raw.githubusercontent.com/andrew72ru/symfony-db-i18n-bundle
 */

declare(strict_types=1);

namespace App\Command;

use App\Classes\Interfaces\EntityInterface;
use App\Classes\Interfaces\TranslationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImportTraductionFileCommand extends Command
{
    protected static $defaultName = 'd1f:translate:import';

    private const HELP = <<<EOL
You can load all messages, stored in translation (yaml / xml) files, 
and save it to database to use in future with db-i18n module
Application container must have a 'locales' parameter, and this parameter must be an array.
Filename, passed as argument, must be compatible with Symfony localization files agreement.
For example: <info>messages.ru.yaml</info>
             <info>messages.ru.xlf</info>
             <info>my_awesome_translations.en.xlf</info> 
EOL;

    private const BATCH_SIZE = 100;

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

    /**
     * MigrateToDatabaseCommand constructor.
     *
     * @param ContainerInterface $container
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $em
     * @param KernelInterface $kernel
     * @param string|null $name
     */
    public function __construct(ContainerInterface $container, TranslatorInterface $translator, EntityManagerInterface $em, KernelInterface $kernel, string $name = null)
    {
        parent::__construct($name);

        $this->container = $container;
        $this->translator = $translator;
        $this->entityClass = $this->container->getParameter('db_i18n.entity');
        $this->em = $em;
        $this->kernel = $kernel;
    }

    protected function configure()
    {
        $this->setDescription('Load data from translation file and pass it to database')
            ->setHelp(self::HELP);
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

        $io = new SymfonyStyle($input, $output);

        $fileAsk = new Question("Entrez le fichier à importer (ex: security.fr.yaml) ");
        $fileName = $helper->ask($input, $output, $fileAsk);
        if (is_null($fileName) || !$fileName) {
            throw new RuntimeException('Vous devez spécifier un fichier');
        }
        $filePath = $this->locateFile($fileName);

        $locale = $this->getLocale(pathinfo($filePath, PATHINFO_FILENAME));
        $domain = trim(str_replace($locale, '', pathinfo($filePath, PATHINFO_FILENAME)), '.');
        $domainAsk = new Question("Entrez le domain à mettre en database (defaut: $domain) ", $domain);
        if (is_null($fileName) || !$fileName) {
            throw new RuntimeException('Vous devez spécifier un domain');
        }
        $domain = $helper->ask($input, $output, $domainAsk);

        $catalogue = $this->translator->getCatalogue($locale);

        $forExport = $catalogue->all($domain);
        $exported = $this->exportToDatabase($forExport, $locale, $domain);

        $io->writeln(sprintf(
            'Loaded form %s: %u messages, exported to database: %s',
            $filePath,
            count($forExport),
            $exported
        ));

        return Command::SUCCESS;
    }

    /**
     * @param array $messages
     * @param string $locale
     * @param string $domain
     *
     * @return int
     */
    protected function exportToDatabase(array $messages, string $locale, string $domain): int
    {
        $count = 0;
        $i = 0;
        foreach ($messages as $key => $value) {
            ++$count;
            ++$i;
            $this->em->persist($this->makeEntity($key, $value, $locale, $domain));
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
     * @return EntityInterface
     */
    protected function makeEntity(string $key, string $translation, string $locale, string $domain): EntityInterface
    {
        $entity = $this->checkEntityExists($locale, $key);
        $entity->load([
            'domain' => $domain,
            'locale' => $locale,
            'key' => $key,
            'translation' => $translation,
        ]);

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
            $realPath = $this->kernel->getProjectDir() . '\translations\\' . $path;
        }

        if (!is_file($realPath) || !is_readable($realPath)) {
            throw new RuntimeException(sprintf('Unable to load %s file', $realPath));
        }

        return $realPath;
    }
}
