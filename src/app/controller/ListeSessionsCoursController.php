<?php

namespace Src\App\Controller;

use Src\App\Model\ListeSessionsCoursModel;
use Src\Core\Controller;

class ListeSessionsCoursController extends Controller
{
    private $model;

    public function __construct($pdo)
    {
        parent::__construct();
        $this->model = new ListeSessionsCoursModel($pdo);
    }

    public function index()
    {
        // Récupérer l'ID du cours depuis la requête GET
        $coursId = isset($_GET['coursId']) ? (int)$_GET['coursId'] : null;

        // Appeler la méthode du modèle avec ou sans filtre
        $sessions = $coursId ? $this->model->getSessionsByCourse($coursId) : $this->model->getSessions();

        // Passer les données à la vue
        $this->renderView('ListeSessionsCours', [
            'sessions' => $sessions
        ]);
    }
}
