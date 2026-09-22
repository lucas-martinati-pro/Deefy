<?php

namespace iutnc\deefy\enums;

enum TypeRender {
    /**
     * Mode de rendu compact (carte réduite, vue grille).
     */
    case COMPACT;
    /**
     * Mode de rendu long (carte horizontale avec lecteur complet).
     */
    case LONG;
}