<?php

namespace Console;


use Framework\ModuleManager;
use Orm\Orm;
use Orm\Schema;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateDatabaseSchemaCommand extends Command
{
    private $rootDirPath;

    public function __construct(string $rootDirPath, $name = null)
    {
        parent::__construct($name);
        $this->rootDirPath;
    }

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('orm:update-database')
            ->setDescription('Met à jour la structure de la base de données.')
            ->setHelp('Met à jour la base de données selon les schémas d\'entité de chaque module.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleManager = new ModuleManager($this->rootDirPath);
        $schema = new Schema();
        $schema->parseEntitiesSchema($moduleManager->getMergedSchema());
        $orm = new Orm($this->rootDirPath . "/config/config.yml", $schema);

        $orm->updateDatabaseSchema();

        $formatter = $this->getHelper('formatter');

        $successMessages = array('Génération terminée !', 'La base de données a été générée avec succès.');
        $formattedBlock = $formatter->formatBlock($successMessages, 'info');
        $output->writeln($formattedBlock);
    }
}