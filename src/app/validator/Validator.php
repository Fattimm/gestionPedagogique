<?php

namespace Src\App\Validator;

class Validator {
    public static function validateLogin($email, $password) {
        $errors = [];

        if (empty($email) || empty($password)) {
            $errors[] = "Le champ email ou mot de passe est requis.";
        }

        return $errors;
    }
}
