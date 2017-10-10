<?php

namespace framework;


use GuzzleHttp\Psr7\Request;
use Orm\EntityManager;
use Psr\Http\Message\ServerRequestInterface;

class Executor
{
    private $moduleManager;

    private $entityManager;

    private $request;

    public function __construct(ModuleManager $moduleManager, EntityManager $entityManager)
    {
        $this->moduleManager = $moduleManager;
        $this->entityManager = $entityManager;
    }

    /**
     * Execute les actions d'une page
     *
     * @param array $actions
     * @param array $params
     * @return array
     */
    public function execute(array $actions, array $params = []) : array
    {
        $tmpArray = [];

        foreach ($actions as $var => $action) {
            $actionPointer = explode(':', $action);

            /**
             * Transforme une chaine de caractère : hello_world_title => helloWorldTitlensform
             */
            $actionPointer[1] = $this->parseActionName($actionPointer[1], false);

            /**
             * Transforme une chaine de caractère : hello_world_title => helloWorldTitlensform
             */
            $actionPointer[2] = $this->parseActionName($actionPointer[2], true);

            /**
             * On recompose le namespace
             */
            $actionPointer[1] = "module\\" . strtolower($actionPointer[0]) . '\\action\\' . $actionPointer[1] . 'Action';

            $action = new $actionPointer[1]($this->moduleManager->getGlobalSchema(), $this->entityManager, $this->request);
            $method = $actionPointer[2];

            $tmpArray[$var] = $action->$method($params);
        }

        return $tmpArray;
    }

    /**
     * Transforme une chaine de caractère : hello_world_title => helloWorldTitle
     *
     * @param  string $action
     * @param  bool   $firstLetterLowercase
     * @return string
     */
    public function parseActionName(string $action, bool $firstLetterLowercase) : string
    {

        $response = "";
        $cpt = 0;

        $tmpArray = explode('_', $action);

        foreach ($tmpArray as $item) {

            $cpt++;

            if ($cpt < 2 && $firstLetterLowercase) {
                $response .= $item;
            } else {
                $response .= ucfirst($item);
            }
        }

        return $response;
    }

    public function addRequest(ServerRequestInterface $request) {
        $this->request = $request;
    }
}