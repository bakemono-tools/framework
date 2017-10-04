<?php

namespace framework;


class Router
{
    private $moduleManager;

    function __construct(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    public function match(string $path) : Page
    {

        $modules = $this->moduleManager->getAllModules();

        foreach ($modules as $module) {
            foreach ($module->getPages() as $page) {
                $pattern = "/^" . $page->getUrlPattern() . "$/";
                if (preg_match($pattern, $path)) {

                    $params = $this->extractParams($path, $page);

                    if (!empty($params)) {
                        $page->setActionsParameters($params);
                    }

                    return $page;
                }
            }
        }

        /**
         * Si aucune route n'a matché, on renvoi sur la page d'accueil
         */
        return $this->moduleManager->getModule('base')->getPage('notfound');
    }

    /**
     * Génère une URI
     *
     * @param string $name
     * @param array $params
     * @return array
     */
    public function generateURI(string $name, array $params = []) {
        $page = $this->moduleManager->findPage($name);
        $uri = $page->getPath();

        /**
         * On remplace les paramètres par leur valeur
         *
         * (Ex : /blog/article/@id
         * devient
         * /blog/article/2
         */
        if (!empty($params)) {
            foreach ($params as $param => $value) {
                $pattern = '/@' . $param . '/';
                $uri = preg_replace($pattern, $value, $uri);
            }
        }

        return $uri;
    }

    public function extractParams(string $path, Page $page) {
        $parameters = [];

        // Change le patern \/blog\/article\/d+ en /blog/article/d+
        $url = preg_replace("/\\\/", '', $page->getPath());

        $url = explode("/", $url);
        $path = explode("/", $path);

        $length = count($url);

        for ($i = 0; $i < $length; $i++) {
            if (preg_match("/@/", $url[$i])) {
                $parameters[preg_replace("/@/", "", $url[$i])] = $path[$i];
            }
        }

        return $parameters;
    }
}