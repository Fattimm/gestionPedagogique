<?php
namespace Src\App\Controller;

use Src\App\Model\ListeCoursEtudiantModel;
use Src\Core\Controller;
use Src\Core\Database\DatabaseInterface;

class ListeCoursEtudiantController extends Controller {
    private $model;

    public function __construct(DatabaseInterface $db) {
        $this->model = new ListeCoursEtudiantModel($db);
    }

    public function index() {
        if (!isset($_SESSION['etudiantId'])) {
            header('Location: /Connexion');
            exit();
        }

        $etudiantId = $_SESSION['etudiantId'];
        $etudiant = $this->model->getEtudiantById($etudiantId);

        // Pagination
        $itemsPerPage = 2;
        $currentPage = $_GET['page'] ?? 1;
        $offset = ($currentPage - 1) * $itemsPerPage;

        // Récupérer les filtres
        $module = $_GET['module'] ?? null;
        $periode = $_GET['periode'] ?? null;

        // Récupération des cours avec pagination et filtres
        $cours = $this->model->getCoursForEtudiant($etudiantId, $offset, $itemsPerPage, $module, $periode);

        $totalItems = $this->model->countCoursForEtudiant($etudiantId, $module, $periode);
        $totalPages = ceil($totalItems / $itemsPerPage);


        $professeurs = [];
        foreach ($cours as $coursItem) {
            $proffesseurId = $coursItem['proffesseurId'] ?? null;
            if ($proffesseurId) {
                $professeur = $this->model->getProfesseurInfo($proffesseurId);
                $professeurs[$coursItem['id']] = $professeur;
            } else {
                $professeurs[$coursItem['id']] = ['nom' => 'Non défini', 'prenom' => 'Non défini'];
            }
        }

        $totalItems = $this->model->countCoursForEtudiant($etudiantId); // Assurez-vous d'avoir une méthode pour compter les cours
        $totalPages = ceil($totalItems / $itemsPerPage);

        // Passer les filtres à la vue
        $data = [
            'etudiant' => $etudiant,
            'cours' => $cours,
            'professeurs' => $professeurs,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'selectedModule' => $module,
            'selectedPeriode' => $periode,
            // Vous devrez fournir les données de modules et périodes à la vue
            'modules' => $this->model->getModules(),
            'periodes' => [
                ['value' => 'semaine', 'label' => 'Cette Semaine'],
                ['value' => 'jour', 'label' => 'Aujourd\'hui']
            ]
        ];

        $this->renderView('ListeCoursEtudiant', $data);
    }
}
