<?php

namespace iutnc\deefy\repository;

class RepositoryFactory extends DeefyRepository {
    private static ?DeefyReadRepository $readInstance = null;
    private static ?DeefyWriteRepository $writeInstance = null;

    // Singleton pour la lecture
    public static function getReader(): DeefyReadRepository {
        if (is_null(self::$readInstance)) {
            self::$readInstance = new DeefyReadRepository();
        }
        return self::$readInstance;
    }

    // Singleton pour l'écriture
    public static function getWriter(): DeefyWriteRepository {
        if (is_null(self::$writeInstance)) {
            self::$writeInstance = new DeefyWriteRepository();
        }
        return self::$writeInstance;
    }
}
