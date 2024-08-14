<?php

namespace Src\App\Model;

use Src\Core\Database\MysqlDatabase;

class ListeSessionsCoursModel
{
    private $db;

    public function __construct(MysqlDatabase $db)
    {
        $this->db = $db;
    }

    public function getSessions(): array
    {
        $pdo = $this->db->getPDO();

        $sql = 'SELECT 
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
        ';

        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $sessions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
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

        return $sessions;
    }

    public function getSessionsByCourse(int $coursId): array
    {
        $pdo = $this->db->getPDO();

        $sql = 'SELECT 
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
        ';

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':coursId', $coursId, \PDO::PARAM_INT);
        $stmt->execute();
        $sessions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
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

        return $sessions;
    }
}
