<?php
/**
 * Created by PhpStorm.
 * User: melvin
 * Date: 17/09/17
 * Time: 14:46
 */

namespace Framework;


class Renderer
{
    private $loader;
    private $twig;

    /**
     * Renderer constructor.
     *
     * @param array $modules
     * @param string $rootDirPath
     */
    public function __construct(array $modules, string $rootDirPath)
    {
        $this->loader = new \Twig_Loader_Filesystem();

        $this->loader->addPath($rootDirPath . "/module", 'layouts');

        foreach ($modules as $module) {
            $this->loader->addPath($rootDirPath . $module->getTemplateDir(), $module->getLabel());
        }

        $this->twig = new \Twig_Environment($this->loader, []);
    }

    /**
     * Retourne une vue twig
     *
     * @param  string $template
     * @param  array  $params
     * @return string
     */
    public function render(string $template, array $params)
    {
        return $this->twig->render($template, $params);
    }

    /**
     * Ajoute une variable globale aux templates twig.
     * Utilisé notamment pour injecter le router qui créé les chemins (Ex : liens)
     *
     * @param string $key
     * @param $value
     */
    public function addGlobal(string $key, $value) {
        $this->twig->addGlobal($key, $value);
    }
}