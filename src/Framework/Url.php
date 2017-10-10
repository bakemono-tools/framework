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
                $paramPatternReplaced = $this->replaceParametersRulesShortcuts($paramPattern);
                $pattern = "/@" . $parameter . "/";
                $urlPattern = preg_replace($pattern, $paramPatternReplaced, $urlPattern);

                /**
                 * Si le dernier paramètre est optionnel
                 * on doit l'entourer de parenthèse pour qu'il soit ignoré par le router si le paramètre n'est pas reseigné
                 *
                 * Ex : L'adresse "/blog/@page" ou le paramètre page suit la règle "?integer"
                 *
                 * Ce que l'ont veut, c'est transformer : "\/blog\/\d+" en "\/blog(\/\d+)?"
                 * pour indiquer que le paramètre est optionnel
                 *
                 * $paramPattern contient "?integer". On test si ça commence par un "?".
                 * Si oui, on rentre dans la condition
                 */
                if (preg_match("/^\?/", $paramPattern)) {

                    /**
                     * On explose le pattern de l'url qui ressemble à ça
                     * "\/blog\/\d+
                     *
                     * ce qui nous donne :
                     * [
                     *      [0] => "\"
                     *      [1] => "blog\"
                     *      [3] => "\d+"
                     */
                    $urlPattern = explode('/', $urlPattern);

                    /**
                     * On inverse le tableau pour avoir le paramètre optionnel
                     * (Qui est toujours le dernier) en premier
                     */
                    $urlPattern = array_reverse($urlPattern);

                    /**
                     * Il faut ajouter, à la section qui précède le paramètre optionnel,
                     * une parenthèse avant l'antislashe
                     *
                     * Ex : "blog\" doit devenir "blog(\"
                     *
                     * Pour faire cela, on supprime le dernier caractère de "blog\", donc le "\"
                     */
                    $urlPattern[1] = substr($urlPattern[1], 0, -1);

                    /**
                     * Et on lui rajoute "(\"
                     *
                     * on a maintenant "blog(\"
                     */
                    $urlPattern[1] .= "(\\";

                    /**
                     * On inverse de nouveau le tableau pour avoir le pattern dans le bon sens
                     */
                    $urlPattern = array_reverse($urlPattern);

                    /**
                     * On recréé la chaine de caractère
                     *
                     * On a maintenant "\/blog(\/\d+"
                     */
                    $urlPattern = implode('/', $urlPattern);

                    /**
                     * Et pour finnir on rajoute ")?" à la fin du pattern
                     *
                     * On obtient finalement "\/blog(\/\d+)?"
                     */
                    $urlPattern .= ")?";
                }
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
         * On supprime le "?" pour que "?integer" match avec "integer" dans le switch
         */
        $ruleValue = preg_replace("/^\?/", "", $ruleValue);

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
                break;
            case "slug":
                $pattern = "[a-z0-9]+(?:-[a-z0-9]+)*";
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