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
        $this->url = $params['url'];
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

    public function getUrlPattern() {
        $urlPattern = $this->getPath();
        $urlPattern = preg_replace("/\-/", "\-", $urlPattern);
        $urlPattern = preg_replace("/\//", "\/", $urlPattern);
        if (isset($this->url['parameters']) && !empty(isset($this->url['parameters']))) {
            foreach ($this->url['parameters'] as $parameter => $paramPattern) {
                $pattern = "/@" . $parameter . "/";
                $urlPattern = preg_replace($pattern, $paramPattern, $urlPattern);
            }
        }

        return $urlPattern;
    }

    /**
     * @return array
     */
    public function getUrl() : array
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getPath(): string {
        return $this->url['path'];
    }

    /**
     * @return array
     */
    public function getUrlParameters() : array {
        return $this->url['parameters'];
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