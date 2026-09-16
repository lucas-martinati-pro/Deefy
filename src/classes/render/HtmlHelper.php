<?php

namespace iutnc\deefy\render;

/**
 * Classe d'assistance pour la génération composants HTML Bootstrap.
 */
class HtmlHelper {

    /**
     * Génère une alerte Bootstrap (texte simple ou liste non ordonnée).
     *
     * @param string|null $type Type d'alerte ('success', 'warning', 'danger', 'info'). Par défaut 'info'.
     * @param string|array|null $content Texte unique ou tableau de messages à afficher.
     * @param bool|null $dismissible True pour inclure un bouton de fermeture de l'alerte.
     * @return string Balises HTML de l'alerte Bootstrap.
     */
    public static function alert(?string $type = null, string|array|null $content = null, ?bool $dismissible = null) : string {
        $type = $type ?? 'info';
        $content = $content ?? '';
        $dismissible = $dismissible ?? false;

        $iconClass = match ($type) {
            'success' => 'bi-check-circle-fill',
            'warning' => 'bi-exclamation-triangle-fill',
            'danger'  => 'bi-exclamation-octagon-fill',
            'info'    => 'bi-info-circle-fill',
            default   => 'bi-bell-fill',
        };

        if (is_array($content)) {
            $items = '';
            foreach ($content as $msg) {
                $items .= "<li>{$msg}</li>";
            }
            $body = "<ul class=\"mb-0 ps-3\">{$items}</ul>";
        } else {
            $body = $content;
        }

        $dismissBtn = $dismissible ? '<button class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' : '';
        $dismissClass = $dismissible ? ' alert-dismissible fade show' : '';

        return <<<HTML
            <div class="alert alert-{$type} d-flex align-items-center{$dismissClass}" role="alert">
                <i class="bi {$iconClass} me-2 fs-5 flex-shrink-0"></i>
                <div class="flex-grow-1">
                    {$body}
                </div>
                {$dismissBtn}
            </div>
        HTML;
    }

    /**
     * Génère un écran d'erreur générique complet avec bouton de retour.
     *
     * @param string|null $title Titre affiché en tête de page (défaut: 'Erreur').
     * @param string|array|null $message Message d'erreur ou liste d'erreurs.
     * @param string|null $backUrl URL du lien de retour (défaut: 'main.php').
     * @param string|null $backLabel Libellé du bouton de retour (défaut: 'Retour à l'accueil').
     * @param string|null $type Type d'alerte Bootstrap ('danger', 'warning', etc. défaut: 'danger').
     * @return string Balises HTML de la page d'erreur.
     */
    public static function errorPage(?string $title = null, string|array|null $message = null, ?string $backUrl = null, ?string $backLabel = null, ?string $type = null) : string {
        $title = $title ?? "Erreur";
        $message = $message ?? "Une erreur inattendue est survenue.";
        $backUrl = $backUrl ?? 'main.php';
        $backLabel = $backLabel ?? "Retour à l'accueil";
        $type = $type ?? 'danger';

        $alertHtml = self::alert($type, $message);

        $titleColor = match ($type) {
            'success' => 'text-success',
            'warning' => 'text-warning',
            'danger'  => 'text-danger',
            default   => 'text-primary',
        };

        return <<<HTML
            <h1 class="h2 fw-bold {$titleColor} mb-3 d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill {$titleColor} me-2"></i>{$title}
            </h1>
            {$alertHtml}
            <p class="mt-3">
                <a class="btn btn-secondary d-inline-flex align-items-center" href="{$backUrl}">
                    <i class="bi bi-arrow-left me-2"></i>{$backLabel}
                </a>
            </p>
        HTML;
    }

    /**
     * Génère un écran d'accès refusé quand l'utilisateur n'est pas connecté.
     *
     * @param string|null $message Message explicatif (défaut: 'Vous devez être connecté pour accéder à cette page.').
     * @param string|null $loginUrl URL vers la page de connexion (défaut: '?action=signin').
     * @param string|null $loginLabel Libellé du bouton de connexion (défaut: 'Se connecter').
     * @param string|null $backUrl URL du bouton retour (défaut: 'main.php').
     * @param string|null $backLabel Libellé du bouton retour (défaut: 'Retour à l'accueil').
     * @return string Balises HTML de l'écran d'accès refusé.
     */
    public static function authRequired(?string $message = null, ?string $loginUrl = null, ?string $loginLabel = null, ?string $backUrl = null, ?string $backLabel = null) : string {
        $message = $message ?? "Vous devez être connecté pour accéder à cette page.";
        $loginUrl = $loginUrl ?? "?action=signin";
        $loginLabel = $loginLabel ?? "Se connecter";
        $backUrl = $backUrl ?? "main.php";
        $backLabel = $backLabel ?? "Retour à l'accueil";

        $alert = self::alert('warning', $message);

        return <<<HTML
            <h1 class="h2 fw-bold text-danger mb-3 d-flex align-items-center">
                <i class="bi bi-shield-lock-fill text-danger me-2"></i>Accès refusé
            </h1>
            {$alert}
            <p class="mt-3">
                <a class="btn btn-primary d-inline-flex align-items-center" href="{$loginUrl}">
                    <i class="bi bi-box-arrow-in-right me-1"></i>{$loginLabel}
                </a>
                <a class="btn btn-secondary ms-2 d-inline-flex align-items-center" href="{$backUrl}">
                    <i class="bi bi-house-door-fill me-1"></i>{$backLabel}
                </a>
            </p>
        HTML;
    }

    /**
     * Génère un écran d'accès interdit pour un utilisateur sans droits suffisants.
     *
     * @param string|null $message Message d'erreur explicatif.
     * @param string|null $backUrl URL de retour (défaut: '?action=playlists').
     * @param string|null $backLabel Libellé du bouton retour (défaut: 'Retour à mes playlists').
     * @return string Balises HTML de l'écran d'interdiction.
     */
    public static function forbidden(?string $message = null, ?string $backUrl = null, ?string $backLabel = null) : string {
        $message = $message ?? "Vous n'êtes pas autorisé à effectuer cette action.";
        $backUrl = $backUrl ?? "?action=playlists";
        $backLabel = $backLabel ?? "Retour à mes playlists";

        $alert = self::alert('danger', $message);

        return <<<HTML
            <h1 class="h2 fw-bold text-danger mb-3 d-flex align-items-center">
                <i class="bi bi-shield-lock-fill text-danger me-2"></i>Accès refusé
            </h1>
            {$alert}
            <p class="mt-3">
                <a class="btn btn-secondary d-inline-flex align-items-center" href="{$backUrl}">
                    <i class="bi bi-arrow-left me-2"></i>{$backLabel}
                </a>
            </p>
        HTML;
    }

    /**
     * Génère un écran pour ressource introuvable.
     *
     * @param string|null $item Nom de la ressource introuvable (ex: 'Playlist', 'Piste').
     * @param string|null $message Message d'erreur explicatif.
     * @param string|null $backUrl URL du lien de retour (défaut: '?action=playlists').
     * @param string|null $backLabel Libellé du bouton de retour.
     * @return string Balises HTML de la page 404.
     */
    public static function notFound(?string $item = null, ?string $message = null, ?string $backUrl = null, ?string $backLabel = null) : string {
        $item = $item ?? "Ressource";
        $message = $message ?? "L'élément demandé n'existe pas ou est introuvable.";
        $backUrl = $backUrl ?? "?action=playlists";
        $backLabel = $backLabel ?? "Retour à mes playlists";

        $alert = self::alert('danger', $message);

        return <<<HTML
            <h1 class="h2 fw-bold text-danger mb-3 d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>{$item} introuvable
            </h1>
            {$alert}
            <p class="mt-3">
                <a class="btn btn-secondary d-inline-flex align-items-center" href="{$backUrl}">
                    <i class="bi bi-arrow-left me-2"></i>{$backLabel}
                </a>
            </p>
        HTML;
    }

    /**
     * Génère un écran d'erreur de formulaire avec bouton de retour arrière vers le formulaire.
     *
     * @param string|array|null $errors Message d'erreur unique ou tableau de messages.
     * @param string|null $backUrl URL de retour (par défaut: 'javascript:history.back()').
     * @param string|null $backLabel Libellé du bouton de retour.
     * @param string|null $title Titre de l'écran d'erreur.
     * @return string Balises HTML de l'écran d'erreur de formulaire.
     */
    public static function formError(string|array|null $errors = null, ?string $backUrl = null, ?string $backLabel = null, ?string $title = null) : string {
        $errors = $errors ?? "Une erreur est survenue lors de la validation du formulaire.";
        $backUrl = $backUrl ?? "javascript:history.back()";
        $backLabel = $backLabel ?? "Retour au formulaire";
        $title = $title ?? "Erreur dans le formulaire";

        $alert = self::alert('danger', $errors);

        return <<<HTML
            <h1 class="h2 fw-bold text-danger mb-3 d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>{$title}
            </h1>
            {$alert}
            <p class="mt-3">
                <a class="btn btn-secondary d-inline-flex align-items-center" href="{$backUrl}">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>{$backLabel}
                </a>
            </p>
        HTML;
    }

    /**
     * Génère un écran de confirmation de succès avec bouton d'action suivante.
     *
     * @param string|null $title Titre du message de succès.
     * @param string|null $message Message de confirmation textuel ou HTML.
     * @param string|null $backUrl URL de redirection ou d'action suivante (défaut: '?action=playlists').
     * @param string|null $backLabel Libellé du bouton d'action suivante.
     * @return string Balises HTML de la page de confirmation de succès.
     */
    public static function successPage(?string $title = null, ?string $message = null, ?string $backUrl = null, ?string $backLabel = null) : string {
        $title = $title ?? "Opération réussie";
        $message = $message ?? "L'opération s'est déroulée avec succès.";
        $backUrl = $backUrl ?? "?action=playlists";
        $backLabel = $backLabel ?? "Retour à mes playlists";

        $alert = self::alert('success', $message);

        return <<<HTML
            <h1 class="h2 fw-bold text-success mb-3 d-flex align-items-center">
                <i class="bi bi-check-circle-fill text-success me-2"></i>{$title}
            </h1>
            {$alert}
            <p class="mt-3">
                <a class="btn btn-primary d-inline-flex align-items-center" href="{$backUrl}">
                    <i class="bi bi-arrow-left me-2"></i>
                    {$backLabel}
                </a>
            </p>
        HTML;
    }
}