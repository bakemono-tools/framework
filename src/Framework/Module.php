<?php

namespace Framework;


class Module
{
    private $label;
    private $pages = [];
    private $templateDir;
    private $entityDir;
    private $entitiesSchema;

    public function __construct(array $moduleDefinition)
    {
        $this->label          = $moduleDefinition['label'];
        $this->templateDir    = $moduleDefinition['templateDir'];
        $this->entityDir      = $moduleDefinition['entityDir'];
        $this->entitiesSchema = $moduleDefinition['entities'];

        if ($moduleDefinition['pages'] !== null) {
            foreach ($moduleDefinition['pages'] as $key => $page) {
                $this->pages[] = new Page(
                    [
                        'id'         => $key,
                        'moduleName' => $this->label,
                        'label'      => $page['label'],
                        'url'        => $page['url'],
                        'template'   => $page['template'],
                        'actions'    => $page['actions']
                    ]
                );
            }
        }
    }

    /**
     * @return mixed
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @return mixed
     */
    public function getTemplateDir()
    {
        return __DIR__ . '/../../module' . $this->templateDir;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return mixed
     */
    public function getEntitiesSchema()
    {
        return $this->entitiesSchema;
    }

    /**
     * Utilisé par :
     *      ModuleManager
     * @param string $pageId
     * @param bool $throwExeption sert à déclenché une exception si la page que l'ont cherche n'est pas trouvée. "False" utilisé par ModuleManager::findPage()
     * @return Page
     */
    public function getPage(string $pageId, bool $throwExeption = true)
    {
        foreach ($this->getPages() as $page) {
            if ($page->getId() === $pageId) {
                return $page;
            }
        }

        if ($throwExeption) {
            die('ERREUR : La page avec l\'identifiant : \'' . $pageId . '\' n\'a pas été trouvée.');
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getEntityDir()
    {
        return __DIR__ . "/../../module" . $this->entityDir;
    }

    /**
     * Renvoi le nom des tables
     */
    public function getTables() : array
    {
        return array_keys($this->getEntitiesSchema());
    }
}