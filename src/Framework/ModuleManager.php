<?php

namespace Framework;

use Orm\Schema;
use Symfony\Component\Yaml\Yaml;

class ModuleManager
{
    /**
     * Contient les modules
     * Cette propriété est notamment utilisée pour récupérer un à un les définitions des différents modules
     *
     * @var array|Module
     */
    private $modules = [];

    /**
     * ModuleManager constructor.
     * @param string $rootDirPath
     *
     * $moduleDirectory indique quel répertoire contient les modules.
     * Ce paramètre sert uniquement pour les test avec phpunit
     * Il permet de charger des faux modules, situés dans /tests/assets, servant aux tests.
     */
    function __construct(string $rootDirPath)
    {
        /**
         * On récupère la liste des modules situés dans /module/module.yml
         */
        $modulesArray = Yaml::parse(file_get_contents($rootDirPath . "/config/config.yml"));

        /**
         * Grâce à la liste des modules on récupère leur definition dans les dossiers correspondants
         */
        $length = count($modulesArray['modules']);

        for ($i = 0; $i < $length; $i++) {

            $tmpArray = array_merge(
                Yaml::parse(file_get_contents($rootDirPath . "/module" . $modulesArray['modules'][$i]['schema'])),
                Yaml::parse(file_get_contents($rootDirPath . "/module" .  $modulesArray['modules'][$i]['definition']))
            );

            /**
             * On fusionne les tableaux des différents modules
             */
            $this->modules[] = new Module($tmpArray);
        }
    }

    /**
     * Renvoi le module demandé
     *
     * @param  string $request
     * @return Module
     */
    public function getModule(string $request) : Module
    {
        $length = count($this->modules);
        $request = strtolower($request);

        /**
         * On parcoure la liste des modules enregistrés dans l'application
         * On regarde si la requête match avec le label de l'un des modules
         * Si le module demandé existe, on retourne le module
         * Sinon on déclenche une exception
         */
        for ($i = 0; $i < $length; $i++) {
            if ($this->modules[$i]->getLabel() === $request) {
                return $this->modules[$i];
            }
        }

        die('ERREUR : Vous appelez un module \'' . $request . '\' qui n\'existe pas. [ModuleManager.php][' . __LINE__ . ']');
    }

    /**
     * Retourne tous les chemins vers les dossier "template" des différents module
     *
     * @return array
     */
    public function getAllTemplateDirs() : array
    {
        $templateDirs = [];
        $length = count($this->modules);

        for ($i = 0; $i < $length; $i++) {
            $templateDirs[] = $this->modules[$i]->getTemplateDir();
        }

        return $templateDirs;
    }

    /**
     * Retourne tous les modules
     *
     * (Ex : [
     *      [0] => Module(),
     *      [1] => Module(),
     *      [2] => Module()
     * ]
     *
     * @return array|Module
     */
    public function getAllModules() : array
    {
        return $this->modules;
    }

    /**
     * Retourne toutes les entités
     *
     * @return array|Entity
     */
    public function getAllTables() : array
    {
        $tmpArray = [];

        foreach ($this->modules as $module) {
            $tmpArray = array_merge($module->getTables(), $tmpArray);
        }

        return $tmpArray;
    }

    /**
     * @return array
     */
    public function getMergedSchema(): array
    {
        $tmpArray = [];

        foreach ($this->modules as $module) {
            if (!empty($module->getEntitiesSchema()))
                $tmpArray = array_merge($module->getEntitiesSchema(), $tmpArray);
        }

        return [
            'entities' => $tmpArray
        ];
    }

    /**
     * Retourne le schema global des entités présentes dans le framework
     *
     * @return Schema
     */
    public function getGlobalSchema(): Schema
    {
        $schema = new Schema();
        $entities = $this->getMergedSchema();
        $schema->parseEntitiesSchema($entities['entities']);

        return $schema;
    }

    /**
     * @param string $requestedEntity
     * @return mixed|null
     */
    public function getEntityProperties(string $requestedEntity) {
        $globalSchema = $this->getMergedSchema();
        foreach ($globalSchema as $entity => $fields) {
            if ($entity === $requestedEntity) {
                return $fields;
            }
        }

        return null;
    }

    /**
     * Renvoi une page demandé par son identifiant.
     *
     * Utilisé par :
     *      Router::generateURI()
     *
     * @param string $name
     * @return Page|null
     */
    public function findPage(string $name) : Page {
        foreach ($this->getAllModules() as $module) {
            $page = $module->getPage($name, false);

            if(!is_null($page) && $page->getId() === $name) {
                return $page;
            }
        }

        return null;
    }
}