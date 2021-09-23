<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Parser as YamlParser;
use Symfony\Component\Yaml\Yaml;

class GenerateLangKeyCommand extends Command
{
    protected static $defaultName = 'app:generate-lang-key';

    protected function configure()
    {
        $this
            ->setDescription('Copie les clés de la langue de référence pour générer la langue clé')
            ->addArgument('baseLangue', InputArgument::OPTIONAL, 'Langue de référence');
    }

    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        parent::__construct(self::$defaultName);

        $this->parameterBag = $parameterBag;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $baseLangueName = $input->getArgument('baseLangue') ?? "fr";

        if ($baseLangueName) {
            $io->note(sprintf('Langue de référence: %s', $baseLangueName));
        }

        $rootDir = $this->parameterBag->get("kernel.project_dir");
        $translatePath = $rootDir . DIRECTORY_SEPARATOR . "translations" . DIRECTORY_SEPARATOR;
        $translateFile = $translatePath . "messages.$baseLangueName.db";

        if (!file_exists($translateFile)) {
            $io->error("Le fichier $translateFile n'existe pas");

            return Command::FAILURE;
        }

        $yamlParser = new YamlParser();
        $ymlContents = $yamlParser->parseFile((string)$translateFile, Yaml::PARSE_CONSTANT);

        $translateKeyContents = [];
        foreach ($ymlContents as $key => $translateValue) {
            $translateKeyContents[$key] = $key;
        }

        $translateFileResult = $translatePath . "messages.key.db";
        if (file_exists($translateFileResult)) {
            unlink($translateFileResult);
        }

        $yamlResult = Yaml::dump($translateKeyContents);
        file_put_contents($translateFileResult, $yamlResult);

        $io->success('Fichier de langue clé généré avec succès.');

        return Command::SUCCESS;
    }
}
