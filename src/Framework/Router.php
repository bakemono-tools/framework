<?php

namespace framework;


class Router
{
    private $moduleManager;

    private $urlParameters = [];

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

    /**
     * Retourne un tableau contenant en clé le nom du paramètre et en valeu, la valeur du paramètre
     * Ex :
     * [
     *      'id' => 2
     * ]
     *
     * @param string $requestPath contient le chemin de la requête, ex : "/blog/2"
     * @param Page $page
     * @return array
     */
    public function extractParams(string $requestPath, Page $page) {
        /**
         * On sépare chaque morceau de l'url de la page qui sert de base
         */
        $url = explode("/", $page->getPath());

        /**
         * On sépare chaque morceau de l'url demandée
         */
        $path = explode("/", $requestPath);

        $length = count($url);

        /**
         * Pour chaque morceau de l'url définie dans definition.yml contenu dans $url (Ex : /blog/@id)
         * [
         *      [0] => blog
         *      [1] => @id
         * ]
         *
         * dès qu'on tombe sur une section qui commence par "@", on enlève le "@"
         * et on se sert du nom du paramètre comme clé dans le tableau des paramètres renvoyés
         * et dans le tableau de la requête effectuées (ex "/blog/2") on affecte la valeur du paramètre situé au même niveau du tableau
         *
         * [
         *      [0] => blog
         *      [1] => 2
         * ]
         *
         * ce qui donne : (ex $parameters)
         * [
         *      ['id'] => 2
         * ]
         */
        for ($i = 0; $i < $length; $i++) {
            if (preg_match("/@/", $url[$i])) {
                $this->urlParameters[preg_replace("/@/", "", $url[$i])] = $path[$i];
            }
        }

        return $this->urlParameters;
    }

    /**
     * Retourne la valeur d'un paramètre passé dans l'url
     * Utile notamment dans les vue grâce à {{ router.urlParameter('article_id') }}
     *
     * @param string $label
     * @return mixed
     */
    public function getUrlParameter(string $label) {
        return $this->urlParameters[$label];
    }
}