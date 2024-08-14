<?php

namespace Src\App\Model;

use Src\Core\Database\DatabaseInterface;
use PDO;

class ListeSessionsCoursEtudiantModel
{
    private $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    // Méthode pour obtenir l'ID de la classe de l'étudiant
    private function getClasseIdByEtudiant($etudiantId)
    {
        $pdo = $this->db->getPDO();

        $stmt = $pdo->prepare("
            SELECT classeId 
            FROM Etudiant 
            WHERE id = :etudiantId
        ");
        $stmt->bindValue(':etudiantId', $etudiantId);
        $stmt->execute();
        $classe = $stmt->fetch(PDO::FETCH_ASSOC);

        return $classe ? $classe['classeId'] : null;
    }

    // Méthode pour obtenir les sessions de cours pour un étudiant
    public function getSessionsForEtudiant($etudiantId, $offset, $limit, $module = null, $periode = null)
    {
        $classeId = $this->getClasseIdByEtudiant($etudiantId);

        if (!$classeId) {
            return []; // Pas de classe trouvée
        }

        $pdo = $this->db->getPDO();

        // Préparer la requête pour obtenir les sessions avec des filtres
        $query = "
            SELECT 
            s.date, s.heureDebut, s.heureFin, 
            c.libelle AS coursNom, 
            sa.nom AS salleNom,
            p.prenom AS profPrenom,
            p.nom AS profNom,
            s.etat,
            s.motif_annulation AS motifAnnulation
        FROM 
            SessionCours s
        JOIN 
            Cours c ON s.coursId = c.id
        JOIN 
            Salle sa ON s.salleId = sa.id
        JOIN 
            Professeur p ON c.proffesseurId = p.id
        ORDER BY 
            s.date, s.heureDebut
        ";

        // Ajouter des conditions pour les filtres
        if ($module !== null) {
            $query .= " AND C.moduleId = :moduleId";
        }

        if ($periode !== null) {
            if ($periode === 'semaine') {
                $query .= " AND WEEK(SC.date) = WEEK(CURDATE())";
            } elseif ($periode === 'jour') {
                $query .= " AND DATE(SC.date) = CURDATE()";
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
        $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Date actuelle
        $currentDate = new \DateTime();

        // Convertir les données au format attendu par FullCalendar
        foreach ($sessions as &$session) {
            $session['start'] = $session['date'] . 'T' . $session['heureDebut'];
            $session['end'] = $session['date'] . 'T' . $session['heureFin'];
            $session['title'] = $session['coursNom'] . ' - ' . $session['salleNom'] . ' - ' . $session['profPrenom'] . ' ' . $session['profNom']; // Affiche le libellé du cours, le nom de la salle et le nom du professeur

            // Convertir la date et l'heure du cours en objet DateTime pour comparaison
            $sessionDateTime = new \DateTime($session['start']);

            // Déterminer la couleur basée sur l'état et la date
            if ($sessionDateTime < $currentDate) {
                // Date passée
                if ($session['etat'] === 'ANNULE') {
                    $session['color'] = 'red'; // Rouge pour annulé
                } else {
                    $session['color'] = 'green'; // Vert pour terminé
                }
            } else {
                // Date future ou en cours
                // Date future ou en cours
                if ($session['etat'] === 'EN_ATTENTE') {
                    $session['color'] = 'orange'; // Orange pour demande d'annulation
                } else
                if ($session['etat'] === 'ANNULE') {
                    $session['color'] = 'red'; // Rouge pour annulé
                } else {
                    $session['color'] = 'yellow'; // Jaune pour planifié ou en cours
                }
            }
        }


        return $session;
    }

    // Méthode pour compter les sessions de cours pour un étudiant
    public function countSessionsForEtudiant($etudiantId, $module = null, $periode = null)
    {
        $classeId = $this->getClasseIdByEtudiant($etudiantId);

        if (!$classeId) {
            return 0; // Pas de classe trouvée
        }

        $pdo = $this->db->getPDO();

        // Préparer la requête pour compter les sessions avec des filtres
        $query = "
           SELECT 
            s.date, s.heureDebut, s.heureFin, 
            c.libelle AS coursNom, 
            sa.nom AS salleNom,
            p.prenom AS profPrenom,
            p.nom AS profNom,
            s.etat,
            s.motif_annulation AS motifAnnulation
        FROM 
            SessionCours s
        JOIN 
            Cours c ON s.coursId = c.id
        JOIN 
            Salle sa ON s.salleId = sa.id
        JOIN 
            Professeur p ON c.proffesseurId = p.id
        WHERE 
            s.coursId = :coursId
        ORDER BY 
            s.date, s.heureDebut
        ";


        if ($module !== null) {
            $query .= " AND C.moduleId = :moduleId";
        }

        if ($periode !== null) {
            if ($periode === 'semaine') {
                $query .= " AND WEEK(SC.date) = WEEK(CURDATE())";
            } elseif ($periode === 'jour') {
                $query .= " AND DATE(SC.date) = CURDATE()";
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
}
