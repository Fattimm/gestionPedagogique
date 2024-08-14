<?php

namespace Src\App\Controller;

use Src\App\Model\ListeSessionsCoursEtudiantModel;
use Src\Core\Controller;

class ListeSessionsCoursEtudiantController extends Controller
{
    private $model;

    public function __construct($pdo)
    {
        parent::__construct();
        $this->model = new ListeSessionsCoursEtudiantModel($pdo);
    }

    public function index()
    {
        // Récupérer l'ID de l'étudiant depuis la session
        $etudiantId = $_SESSION['etudiantId'] ?? null;

        // Vérifier si l'ID de l'étudiant est disponible
        if (!$etudiantId) {
            // Rediriger vers une page d'erreur ou de connexion si l'ID n'est pas disponible
            header('Location: /login');
            exit();
        }

        // Optionnel : récupérer des paramètres de filtrage depuis la requête (ex : module, période)
        $module = $_GET['module'] ?? null;
        $periode = $_GET['periode'] ?? null;
        $page = $_GET['page'] ?? 1;
        $limit = 10; // Nombre de sessions par page

        // Récupérer les sessions de cours pour l'étudiant
        $sessions = $this->model->getSessionsForEtudiant($etudiantId, $page, $limit, $module, $periode);

        // Compter le nombre total de sessions pour la pagination
        $totalSessions = $this->model->countSessionsForEtudiant($etudiantId, $module, $periode);

        // Calculer le nombre total de pages
        $totalPages = ceil($totalSessions / $limit);

        // Passer les données à la vue
        $this->renderView('ListeSessionsCoursEtudiant', [
            'sessions' => $sessions,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'module' => $module,
            'periode' => $periode,
        ]);
    }
}
