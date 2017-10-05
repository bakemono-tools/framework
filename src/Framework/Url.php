<?php
/**
 * Created by PhpStorm.
 * User: melvin
 * Date: 05/10/17
 * Time: 15:35
 */

namespace Framework;


class Url
{
    /**
     * Contient l'url d'une page
     *
     * @var string
     */
    private $url;

    /**
     * Url constructor.
     *
     * @param array $url
     */
    function __construct(array $url)
    {
        $this->url = $url;
    }

    /**
     * Retourne l'expression régulière de la route définie par la Page dans definition.yml
     *
     * Ex : return \/a\-propos\-de\-nous\/\d+
     *
     * @return mixed|string
     */
    public function getUrlPattern() {
        $urlPattern = $this->getPath();

        /**
         * On échappe les tirets "-"
         */
        $urlPattern = preg_replace("/\-/", "\-", $urlPattern);

        /**
         * On échape les slashes "/"
         */
        $urlPattern = preg_replace("/\//", "\/", $urlPattern);

        /**
         * Si il existe des règles pour les paramètres de la route
         * On les remplaces par leur pattern
         */
        if (isset($this->url['rules']) && !empty(isset($this->url['rules']))) {
            foreach ($this->url['rules'] as $parameter => $paramPattern) {

                /**
                 * Remplace les raccourcis utiliser dans la définition des route par leur valeur en regex
                 */
                $paramPattern = $this->replaceParametersRulesShortcuts($paramPattern);
                $pattern = "/@" . $parameter . "/";
                $urlPattern = preg_replace($pattern, $paramPattern, $urlPattern);
            }
        }

        return $urlPattern;
    }

    /**
     * Remplace les raccourcis utiliser dans la définition des route par leur valeur en regex
     *
     * ex : "integer" devient "\d+"
     *
     * @param $ruleValue
     * @return string
     */
    public function replaceParametersRulesShortcuts($ruleValue): string
    {
        /**
         * Si la valeur définie par la règle commence par un "?"
         * C'est que le paramètre est optionnel
         * required vaut donc false
         */
        $required = !preg_match("/^\?/", $ruleValue);

        /**
         * On test chaque raccourci
         * Si ça matche avec un raccourci, on le remplace par son équivalent en regex
         * Ex : Si on tombe sur "integer", on le remplace par "\d+"
         *
         * Si aucun raccourci n'est utilisé on renvoie le pattern saisi sans le modifier
         */
        switch ($ruleValue) {
            case "integer":
                $pattern = "\d";
                // Si le paramètre est obligatoire on ajoute le "+" dans la regex
                if ($required) { $pattern .= "+"; }
                break;
            default: // Si aucun raccourci n'a été utilisé on retourne le pattern saisie dans le fichier definition.yml
                $pattern = $ruleValue;
        }

        return $pattern;
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
        return $this->url['rules'];
    }
}