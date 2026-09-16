<?php

namespace iutnc\deefy\render;

/**
 * Interface pour le rendu HTML des éléments de l'application.
 */
interface Renderer {
    /**
     * Mode de rendu compact (carte réduite, vue grille).
     */
    public const int COMPACT = 1;

    /**
     * Mode de rendu long (carte horizontale avec lecteur complet).
     */
    public const int LONG = 2;

    /**
     * Génère et retourne le code HTML représentant l'élément selon le sélecteur fourni.
     *
     * @param int $selector Constante indiquant le mode de rendu désiré (Renderer::COMPACT ou Renderer::LONG).
     * @return string Balises HTML prêtes à être insérées dans la page.
     */
    public function render(int $selector) : string;
}