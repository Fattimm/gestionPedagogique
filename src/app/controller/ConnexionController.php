<?php

namespace Src\App\Controller;

use Src\App\Validator\Validator;
use Src\Core\Database\DatabaseInterface; 
use Src\App\Model\ConnexionModel;

class ConnexionController {
    private $model;

    public function __construct(DatabaseInterface $database) {
        $this->model = new ConnexionModel($database);
    }

    public function showLoginForm() {
        require_once '/var/www/html/gestionPedagogique/views/Connexion.php';
    }       

    public function login() {
        // Pas besoin d'appeler session_start() ici

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Valider les données de connexion
            $errors = Validator::validateLogin($email, $password);

            if (empty($errors)) {
                $user = $this->model->checkCredentials($email, $password);
                // var_dump($user);

                if ($user) {
                    // Connexion réussie
                    $_SESSION['user'] = $user; 
                    $_SESSION['etudiantId'] = $user['id']; // Stocker l'ID de l'étudiant dans la session
    
                    if ($user['userType'] === 'etudiant') {
                        header('Location: /ListeCoursEtudiant'); 
                    } else {
                        header('Location: /ListeCours');
                    }
                    exit();
                } else {
                    $errors[] = "Email ou mot de passe incorrect.";
                }
            }
        }

        // Afficher le formulaire avec les erreurs, si elles existent
        require_once '/var/www/html/gestionPedagogique/views/Connexion.php';
    }

    public function logout() {
        // Démarrer la session
        session_start();

        // Détruire toutes les données de session
        $_SESSION = [];

        // Si vous voulez supprimer également le cookie de session
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, 
                $params["path"], 
                $params["domain"], 
                $params["secure"], 
                $params["httponly"]
            );
        }

        // Détruire la session
        session_destroy();

        // Redirection vers la page de connexion
        header('Location: /Connexion');
        exit();
    }
}
