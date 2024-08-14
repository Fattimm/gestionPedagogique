<?php

namespace Src\App\Model;

use Src\Core\Database\DatabaseInterface;
use PDO;

class ListeCoursEtudiantModel
{
    private $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function getEtudiantById($etudiantId)
    {
        $pdo = $this->db->getPDO();

        $stmt = $pdo->prepare("
            SELECT * 
            FROM Etudiant 
            WHERE id = :etudiantId
        ");
        $stmt->bindValue(':etudiantId', $etudiantId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCoursForEtudiant($etudiantId, $offset, $limit, $module = null, $periode = null) {
        $pdo = $this->db->getPDO();
    
        // Étape 1: Récupérer l'ID de la classe de l'étudiant
        $stmt = $pdo->prepare("
            SELECT classeId 
            FROM Etudiant 
            WHERE id = :etudiantId
        ");
        $stmt->bindValue(':etudiantId', $etudiantId);
        $stmt->execute();
        $classe = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$classe) {
            return []; // Pas de classe trouvée
        }
    
        $classeId = $classe['classeId'];
    
        // Étape 2: Préparer la requête pour obtenir les cours avec des filtres
        $query = "
            SELECT C.*, M.libelle AS moduleLibelle
            FROM Cours C
            INNER JOIN CoursClasse CC ON C.id = CC.coursId
            INNER JOIN Module M ON C.moduleId = M.id
            WHERE CC.classeId = :classeId
        ";
    
        // Ajouter des conditions pour les filtres
        if ($module !== null) {
            $query .= " AND C.moduleId = :moduleId";
        }
    
        if ($periode !== null) {
            if ($periode === 'semaine') {
                $query .= " AND WEEK(C.dateDebut) = WEEK(CURDATE())";
            } elseif ($periode === 'jour') {
                $query .= " AND DATE(C.dateDebut) = CURDATE()";
            }
        }
    
        $query .= " LIMIT :limit OFFSET :offset";
    
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':classeId', $classeId);
    
        if ($module !== null) {
            $stmt->bindValue(':moduleId', $module, PDO::PARAM_INT);
        }
    
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function countCoursForEtudiant($etudiantId, $module = null, $periode = null) {
        $pdo = $this->db->getPDO();
    
        // Étape 1: Récupérer l'ID de la classe de l'étudiant
        $stmt = $pdo->prepare("
            SELECT classeId 
            FROM Etudiant 
            WHERE id = :etudiantId
        ");
        $stmt->bindValue(':etudiantId', $etudiantId);
        $stmt->execute();
        $classe = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$classe) {
            return 0; // Pas de classe trouvée
        }
    
        $classeId = $classe['classeId'];
    
        // Étape 2: Préparer la requête pour compter les cours avec des filtres
        $query = "
            SELECT COUNT(*) as total
            FROM Cours C
            INNER JOIN CoursClasse CC ON C.id = CC.coursId
            WHERE CC.classeId = :classeId
        ";
    
        if ($module !== null) {
            $query .= " AND C.moduleId = :moduleId";
        }
    
        if ($periode !== null) {
            if ($periode === 'semaine') {
                $query .= " AND WEEK(C.dateDebut) = WEEK(CURDATE())";
            } elseif ($periode === 'jour') {
                $query .= " AND DATE(C.dateDebut) = CURDATE()";
            }
        }
    
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':classeId', $classeId);
    
        if ($module !== null) {
            $stmt->bindValue(':moduleId', $module, PDO::PARAM_INT);
        }
    
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    public function getProfesseurInfo($proffesseurId)
    {
        $pdo = $this->db->getPDO();

        $stmt = $pdo->prepare("
            SELECT nom, prenom 
            FROM Professeur 
            WHERE id = :proffesseurId
        ");
        $stmt->bindValue(':proffesseurId', $proffesseurId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Méthode pour obtenir la liste des modules
    public function getModules()
    {
        $pdo = $this->db->getPDO();
        $query = "SELECT id, libelle FROM Module"; // Assurez-vous que le nom de la table et les colonnes sont corrects
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
