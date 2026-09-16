<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\HtmlHelper;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

/**
 * Classe abstraite de base pour l'ensemble des actions de l'application.
 */
abstract class Action {
    protected ?string $http_method = null;
    protected ?string $hostname = null;
    protected ?string $script_name = null;
    protected bool $requireAuth = true;
    protected ?array $user = null;

    /**
     * Constructeur d'action.
     * Initialise les attributs d'environnement à partir des variables serveur.
     */
    public function __construct() {
        $this->http_method = $_SERVER['REQUEST_METHOD'];
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->script_name = $_SERVER['SCRIPT_NAME'];

        try {
            $this->user = AuthnProvider::getSignedInUser();
        } catch (AuthnException) {
            $this->user = null;
        }
    }

    /**
     * Exécute l'action appropriée en appelant get() ou post() selon la méthode HTTP.
     *
     * @return string Balises HTML générées en réponse à la requête.
     */
    public function execute() : string {
        if ($this->requireAuth && $this->user === null) {
            return HtmlHelper::authRequired();
        }

        switch ($this->http_method) {
            case 'GET' : return $this->get();
            case 'POST' : return $this->post();
            default : return '';
        }
    }

    /**
     * Permet d'invoquer directement l'instance de l'action comme une fonction.
     *
     * @return string Résultat de l'exécution de l'action.
     */
    public function __invoke() : string {
        return $this->execute();
    }

    /**
     * Traite les requêtes HTTP de type GET.
     *
     * @return string Code HTML généré pour l'affichage (formulaire, vue, etc.).
     */
    abstract public function get() : string;

    /**
     * Traite les requêtes HTTP de type POST.
     *
     * @return string Code HTML résultant du traitement (succès, redirection, erreur).
     */
    abstract public function post() : string;
}