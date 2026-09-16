<?php

/**
 * Chargeur automatique de classes (PSR-4).
 */
class Psr4ClassLoader {
    /**
     * Préfixe du namespace géré par ce chargeur.
     */
    private string $prefixe;

    /**
     * Chemin racine du répertoire contenant les classes.
     */
    private string $basefilename;

    /**
     * Initialise le chargeur avec un préfixe de namespace et un répertoire de base.
     *
     * @param string $prefixe Le préfixe du namespace à mapper.
     * @param string $basefilename Le répertoire racine correspondant.
     */
    public function __construct(string $prefixe, string $basefilename) {
        $this->prefixe = $prefixe;
        $this->basefilename = $basefilename;
    }

    /**
     * Charge le fichier de classe correspondant au nom fourni s'il correspond au préfixe.
     *
     * @param string $filename Nom complet et qualifié de la classe (ex: 'iutnc\\deefy\\audio\\tracks\\AudioTrack').
     * @return void
     */
    public function loadClass(string $filename) : void {
        if (str_starts_with($filename, $this->prefixe)) {
            $filename = substr($filename, strlen($this->prefixe));
            $filename = $this->basefilename . "/" . str_replace("\\", "/", $filename) . ".php";
            if (is_file($filename)) {
                require_once $filename;
            }
        }
    }

    public function register() : void {
        spl_autoload_register([$this, 'loadClass']);
    }
}