<?php

namespace Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class CreateModuleCommand extends Command
{
    protected $rootDirPath;

    public function __construct($rootDirPath, $name = null)
    {
        parent::__construct($name);
        $this->rootDirPath = $rootDirPath;
    }

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('framework:create-module')
            ->addArgument('module-name', InputArgument::REQUIRED, 'The name of the new module.')
            ->setDescription('Créer un module vide avec l\'arborescence de base.')
            ->setHelp('Créer un module vide avec l\'arborescence de base.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Création du module : '.$input->getArgument('module-name'));
        mkdir($this->rootDirPath . "/module/" . $input->getArgument('module-name'));

        mkdir($this->rootDirPath . "/module/" . $input->getArgument('module-name') . "/action");
        mkdir($this->rootDirPath . "/module/" . $input->getArgument('module-name') . "/definition");
        mkdir($this->rootDirPath . "/module/" . $input->getArgument('module-name') . "/entity");
        mkdir($this->rootDirPath . "/module/" . $input->getArgument('module-name') . "/template");

        /**
         * Definition
         */
        touch($this->rootDirPath . "/module/" . $input->getArgument('module-name') . "/definition/definition.yml");
        $array = array(
            'label' => $input->getArgument('module-name'),
            'templateDir' => "/" . $input->getArgument('module-name') . "/template",
            'entityDir' => "/" . $input->getArgument('module-name') . "/entity",
            'pages' => null
        );
        $yaml = Yaml::dump($array);
        file_put_contents($this->rootDirPath . "/module/" . $input->getArgument('module-name') . "/definition/definition.yml", $yaml);

        /**
         * Schema
         */
        touch($this->rootDirPath . "/module/" . $input->getArgument('module-name') . "/definition/schema.yml");
        $array = array(
            'entities' => null,
        );
        $yaml = Yaml::dump($array);
        file_put_contents($this->rootDirPath . "/module/" . $input->getArgument('module-name') . "/definition/schema.yml", $yaml);

        try {
            $value = Yaml::parse(file_get_contents($this->rootDirPath . '/config/config.yml'));

            $value['modules'][] = [
                'label' => $input->getArgument('module-name'),
                'definition' => '/' . $input->getArgument('module-name') . '/definition/definition.yml',
                'schema' => '/' . $input->getArgument('module-name') . '/definition/schema.yml',
            ];

            $yaml = Yaml::dump($value);
            file_put_contents($this->rootDirPath . '/config/config.yml', $yaml);

            $formatter = $this->getHelper('formatter');

            $successMessages = array('Génération terminée !', 'Le module "' . $input->getArgument('module-name') . '" a été créer avec succès.');
            $formattedBlock = $formatter->formatBlock($successMessages, 'info');
            $output->writeln($formattedBlock);
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }
    }
}