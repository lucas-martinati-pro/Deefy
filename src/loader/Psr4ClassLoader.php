<?php

namespace loader;

class Psr4ClassLoader {
    private string $prefixe, $basePath;

    public function __construct(string $prefixe, string $basePath) {
        $this->prefixe = $prefixe;
        $this->basePath = $basePath;
    }

    public function loadClass(string $path) {
        if (str_starts_with($path, $this->prefixe)) {
            $path = substr($path, strlen($this->prefixe));
            $filename = $this->basePath . "/" . str_replace("\\", "/", $path) . ".php";
            if (is_file($filename)) {
                require_once $filename;
            }
        }
    }

    public function register() {
        spl_autoload_register([$this, 'loadClass']);
    }
}