<?php

namespace Framework;


class Page
{
    /**
     * Contient l'identifiant de la page
     * (Ex : 'home')
     *
     * @var mixed
     */
    private $id;

    /**
     * Contient le nom du module
     *
     * @var mixed
     */
    private $moduleName;

    /**
     * Contient le nom de la page
     * (Ex : 'Accueil')
     *
     * @var mixed
     */
    private $label;

    /**
     * Contient l'uri de la page
     * (Ex : '/blog')
     *
     * @var mixed
     */
    private $url;

    /**
     * Contient le nom du template associé à cette page
     * (Ex : home.html.twig)
     *
     * @var mixed
     */
    private $template;

    /**
     * Contient sous forme de texte les actions à executer
     * (Ex : [
     *      0 => 'base:message:list'
     *      1 => 'base:message:form'
     * ])
     *
     * @var mixed
     */
    private $actions;

    /**
     * Contient les valeurs des paramètres utilisés par les actions lorque la page est appelée
     * (Ex : [
     *      "article_id" => 2
     * ]
     *
     * @var array
     */
    private $actionParameters = [];

    /**
     * Page constructor.
     *
     * @param $params
     */
    function __construct(array $params)
    {
        $this->id = $params['id'];
        $this->moduleName = $params['moduleName'];
        $this->label = $params['label'];
        $this->url = new Url($params['url']);
        $this->template = $params['template'];
        $this->actions = $params['actions'];
    }

    function __toString()
    {
        return $this->getLabel();
    }

    /**
     * Renvoi l'identifiant de la page.
     * (Ex : 'home')
     *
     * Utilisé par :
     *      ModuleManager::findPage()
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Retourne l'expression régulière de la route définie par la Page dans definition.yml
     *
     * Ex : return \/a\-propos\-de\-nous\/\d+
     *
     * @return mixed|string
     */
    public function getUrlPattern() {
        return $this->url->getUrlPattern();
    }

    /**
     * @return array
     */
    public function getUrl() : array
    {
        return $this->url->getUrl();
    }

    /**
     * @return string
     */
    public function getPath(): string {
        return $this->url->getPath();
    }

    /**
     * @return array
     */
    public function getUrlParameters() : array {
        return $this->url->getUrlParameters();
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return "@" . $this->moduleName . "/" . $this->template;
    }

    /**
     * @return mixed
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Ajoute un paramètre au tableau des paramètres envoyé aux actions qui vont être executées
     *
     * @param string $name
     * @param $value
     */
    public function addActionsParameter(string $name, $value) {
        $this->actionParameters[$name] = $value;
    }

    /**
     * Utilisé par :
     *      Router::extracParams()
     *
     * @param array $params
     */
    public function setActionsParameters(array $params) {
        $this->actionParameters = $params;
    }

    /**
     * @return array
     */
    public function getActionsParameters() {
        return $this->actionParameters;
    }
}