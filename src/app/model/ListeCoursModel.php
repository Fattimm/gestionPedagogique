<?php

namespace Src\App\Model;

use PDO;

class ListeCoursModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo->getPDO(); 
    }

    public function getCours($semestreId, $module, $annee, $limit, $offset)
    {
        // Récupérer l'ID du professeur depuis la session
        $professeurId = $_SESSION['user_id'] ?? null;

        // Requête de base
        $sql = "SELECT C.*, P.nom AS professeurNom, P.prenom AS professeurPrenom 
            FROM Cours C
            LEFT JOIN Professeur P ON C.proffesseurId = P.id
            WHERE 1=1";

        if ($semestreId) {
            $sql .= " AND C.semestreId = :semestreId";
        }
        if ($module) {
            $sql .= " AND C.moduleId = :module";
        }
        if ($annee) {
            $sql .= " AND C.anneeScolaireId = :annee";
        }

        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        if ($semestreId) {
            $stmt->bindValue(':semestreId', $semestreId, PDO::PARAM_INT);
        }
        if ($module) {
            $stmt->bindValue(':module', $module, PDO::PARAM_INT);
        }
        if ($annee) {
            $stmt->bindValue(':annee', $annee, PDO::PARAM_INT);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mettre à jour le statut des cours après récupération
        foreach ($cours as &$course) {
            if ($course['heureGlobal'] > 0) {
                $course['status'] = 'En Cours';
            } else {
                $course['status'] = 'Terminé';
            }
        }

        return $cours;
    }

    public function getTotalCours($semestreId, $module, $annee)
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM Cours 
                WHERE 1=1";

        if ($semestreId) {
            $sql .= " AND semestreId = :semestreId";
        }
        if ($module) {
            $sql .= " AND moduleId = :module";
        }
        if ($annee) {
            $sql .= " AND anneeScolaireId = :annee";
        }

        $stmt = $this->pdo->prepare($sql);
        if ($semestreId) {
            $stmt->bindValue(':semestreId', $semestreId);
        }
        if ($module) {
            $stmt->bindValue(':module', $module);
        }
        if ($annee) {
            $stmt->bindValue(':annee', $annee);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    public function getProfesseurInfo($professeurId)
    {
        $stmt = $this->pdo->prepare("SELECT nom, prenom FROM Professeur WHERE id = :id");
        $stmt->bindValue(':id', $professeurId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSemestres()
    {
        $stmt = $this->pdo->query("SELECT id, libelle FROM Semestre ORDER BY libelle");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getModules()
    {
        $stmt = $this->pdo->query("SELECT DISTINCT moduleId FROM Cours ORDER BY moduleId");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAnnees()
    {
        $stmt = $this->pdo->query("SELECT DISTINCT anneeScolaireId FROM Cours ORDER BY anneeScolaireId DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateHeuresGlobales($coursId, $heuresReduites)
    {
        $sql = "UPDATE Cours 
                SET heureGlobal = heureGlobal - :heuresReduites, 
                    status = CASE 
                        WHEN heureGlobal - :heuresReduites <= 0 THEN 'Terminé'
                        ELSE 'En Cours'
                    END
                WHERE id = :coursId";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':heuresReduites', $heuresReduites, PDO::PARAM_INT);
        $stmt->bindValue(':coursId', $coursId, PDO::PARAM_INT);

        $stmt->execute();
    }
}
