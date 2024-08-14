<?php
namespace Src\App\Controller;

use Src\App\Model\ListeCoursModel;
use Src\Core\Controller;

class ListeCoursController extends Controller
{
    private $model;

    public function __construct($pdo)
    {
        parent::__construct(); // Appelle le constructeur de la classe parente
        $this->model = new ListeCoursModel($pdo);
    }

    public function index() {
         // Récupérer l'ID du professeur depuis la session
         $professeurId = $_SESSION['user']['id'] ?? null;
 
         // Vérifier si l'ID du professeur est disponible
         if (!$professeurId) {
             // Gérer le cas où l'ID du professeur n'est pas disponible (par exemple, rediriger vers la page de connexion)
             header('Location: /login');
             exit();
         }
        // Récupérer les données du professeur
        $professeur = $this->model->getProfesseurInfo($professeurId);
    
        // Récupérer les paramètres de filtre depuis la requête GET
        $semestreId = $_GET['semestre'] ?? '';
        $module = $_GET['module'] ?? '';
        $annee = $_GET['annee'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 2; // Nombre de cours par page
        $offset = ($page - 1) * $limit;
        
        // Utiliser le modèle instancié dans le constructeur
        $totalCours = $this->model->getTotalCours($semestreId, $module, $annee);
        $totalPages = ceil($totalCours / $limit);
        
        // Récupérer les cours avec les filtres et la pagination
        $cours = $this->model->getCours($semestreId, $module, $annee, $limit, $offset);
    
        // Passer les données à la vue
        $this->renderView('ListeCours', [
            'cours' => $cours,
            'totalCours' => $totalCours,
            'totalPages' => $totalPages, // Passez totalPages
            'currentPage' => $page, // Passez currentPage
            'semestres' => $this->model->getSemestres(),
            'modules' => $this->model->getModules(),
            'annees' => $this->model->getAnnees(),
            'page' => $page,
            'limit' => $limit,
            'professeur' => $professeur // Ajouté ici
        ]);
    }
    
        
}

