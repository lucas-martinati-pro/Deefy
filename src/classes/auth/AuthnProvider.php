<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\RepositoryFactory;

class AuthnProvider {
    public static function signin(string $email, string $password) : void {
        $r = RepositoryFactory::getReader();

        $user = $r->findByEmail(filter_var($email, FILTER_SANITIZE_EMAIL));

        // Faire !$user au cas où user vaux false
        if (!$user || !password_verify($password, $user['passwd'])) {
            throw new AuthnException("Auth error : invalid credentials");
        }
        $_SESSION['user'] = serialize($user);
    }

    public static function register(string $email, string $password) : void {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        if ($email === false) {
            throw new AuthnException("L'adresse email n'est pas valide.");
        }

        $r = RepositoryFactory::getReader();

        if ($r->existByEmail($email)) {
            throw new AuthnException("Cette adresse email est déjà utilisée.");
        }

        $minimumLength = 10;

        if (strlen($password) < $minimumLength) { // longueur minimale
            throw new AuthnException("Le mot de passe doit contenir au moins {$minimumLength} caractères.");
        };
        if (!preg_match("#[\d]#", $password)) {// au moins un digit
            throw new AuthnException("Le mot de passe doit contenir au moins un chiffre.");
        }
        if (!preg_match("#[\W]#", $password)) { // au moins un car. spécial
            throw new AuthnException("Le mot de passe doit contenir au moins un caractère spécial.");
        }
        if (!preg_match("#[a-z]#", $password)) { // au moins une minuscule
            throw new AuthnException("Le mot de passe doit contenir au moins une lettre minuscule.");
        }
        if (!preg_match("#[A-Z]#", $password)) { // au moins une majuscule
            throw new AuthnException("Le mot de passe doit contenir au moins une lettre majuscule.");
        }

        $w = RepositoryFactory::getWriter();
        $w->adduser($email, $password);
    }

    public static function getSignedInUser() : array {
        if (!isset($_SESSION['user'])) {
            throw new AuthnException("Vous devez être connecté pour accéder à cette page.");
        }

        return unserialize($_SESSION['user']);
    }
}