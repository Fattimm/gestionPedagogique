<?php

namespace Src\Core;

abstract class Controller
{
    // Constructeur sans gestion de session
    public function __construct()
    {
        // Initialisation ou autres tâches si nécessaire
    }

    // Méthode pour rendre une vue
    protected function renderView(string $view, array $data = [])
    {
        // Extraire les données pour les rendre disponibles dans la vue
        extract($data);

        // Inclure le fichier de vue
        $viewPath = ROOT . '/views/' . $view . '.php';
        if (file_exists($viewPath)) {
            include_once $viewPath;
        } else {
            throw new \Exception("La vue {$view} n'existe pas.");
        }
    }
    
    // Méthode pour rediriger vers une autre URL
    protected function redirect(string $url)
    {
        header("Location: {$url}");
        exit;
    }
}
