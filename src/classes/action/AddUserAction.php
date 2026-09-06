<?php

namespace iutnc\deefy\action;

class AddUserAction extends Action {

    #[\Override]
    public function get() : string {
        return <<<HTML
        <form method="post" action="?action=add-user" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Nom">
            <input type="text" name="email" placeholder="Email">
            <input type="number" name="age" placeholder="Age">
            <button type="submit">Connexion</button>
        </form>
        HTML;
    }

    #[\Override]
    public function post() : string {
        $error = [];

        if (!isset($_POST['name']) || $_POST['name'] === '') {
            $error[] = 'Le nom est obligatoire.';
        }
        if (!isset($_POST['email']) || $_POST['email'] === '') {
            $error[] = "L'adresse email est obligatoire.";
        }
        if (!isset($_POST['age']) || $_POST['age'] === '') {
            $error[] = "L'âge est obligatoire.";
        }

        if ($error != []) {
            $errorList = '';
            foreach ($error as $message) {
                $errorList .= "<li>{$message}</li>";
            }

            return <<<HTML
                <h1>Erreur dans le formulaire</h1>
                <ul>{$errorList}</ul>
                <a href="?action=add-user">Retour au formulaire</a>
            HTML;
        }

        $_POST['name'] = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $_POST['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $_POST['age'] = filter_var($_POST['age'], FILTER_SANITIZE_NUMBER_INT);

        return <<<HTML
            <span>Nom : <strong>{$_POST['name']}</strong>, Email : <strong>{$_POST['email']}</strong>, Age : <strong>{$_POST['age']}</strong></span>
        HTML;
    }
}