<?php

class Psr4ClassLoader {
    private string $prefixe, $basefilename;

    public function __construct(string $prefixe, string $basefilename) {
        $this->prefixe = $prefixe;
        $this->basefilename = $basefilename;
    }

    public function loadClass(string $filename) {
        if (str_starts_with($filename, $this->prefixe)) {
            $filename = substr($filename, strlen($this->prefixe));
            $filename = $this->basefilename . "/" . str_replace("\\", "/", $filename) . ".php";
            if (is_file($filename)) {
                require_once $filename;
            }
        }
    }

    public function register() {
        spl_autoload_register([$this, 'loadClass']);
    }
}