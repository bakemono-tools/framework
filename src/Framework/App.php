<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
use Orm\Orm;
use Orm\Schema;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class App
{
    private $rootDirPath;

    private $moduleManager;
    private $renderer;
    private $router;
    private $orm;
    private $executor;

    /**
     * App constructor.
     * @param string $rootDirPath
     */
    public function __construct(string $rootDirPath)
    {
        $this->moduleManager = new ModuleManager($rootDirPath);
        $this->router = new Router($this->moduleManager);
        $this->renderer = new Renderer($this->moduleManager->getAllModules());

        $entitiesSchema = new Schema();
        $entitiesSchema->parseEntitiesSchema($this->moduleManager->getMergedSchema());
        $this->orm = new Orm($this->rootDirPath . "/config/config.yml", $entitiesSchema);
        $this->executor = new Executor();
    }

    /**
     * Execute la requête
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request) : ResponseInterface
    {
        /**
         * On ajoute le router aux vues twig pour pouvoir générer les liens
         */
        $this->renderer->addGlobal('router', $this->router);

        /**
         * On créer une nouvelle instance de GuzzleHttp\Psr7\Response;
         */
        $response = new Response();

        /**
         * Grâce à l'objet Psr\Http\Message\ServerRequestInterface en paramètre dans $requete
         * On récupère l'uri de la requête
         */
        $path = $request->getUri()->getPath();

        /**
         * Cette variable contient les variables qui devront être envoyé à la vue après l'execution des actions
         */
        $variables = [];

        /**
         * Si la requête demande un fichier situé dans le dossier public
         * on renvoi le contenu du fichier
         * sinon en renvoi la page
         */
        if (preg_match('#^\/public\/.+#', $path)) {
            $response->getBody()->write(file_get_contents(__DIR__ . $path));
        } else {
            /**
             * Grâce au Router on récupère la Page qui correspont au $path demandé par la requête
             */
            $page = $this->router->match($path);

            /**
             * Si la Page a des actions a executer, on les execute grâce à l'objet Executor.
             * On stocke les variables renvoyées par les actions dans $variables.
             */
            if ($page->getActions() !== null) {
                $variables = $this->executor->execute($page->getActions(), $page->getActionsParameters());
            }

            /**
             * Et enfin, on écrit le template dans le body de la réponse
             */
            $response->getBody()->write($this->renderer->render($page->getTemplate(), $variables));
        }

        return $response;
    }
}