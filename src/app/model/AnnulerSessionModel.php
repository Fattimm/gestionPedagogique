<?php
namespace Src\App\Model;

use Src\Core\Database\MysqlDatabase;

class AnnulerSessionModel
{
    private $db;

    public function __construct(MysqlDatabase $db)
    {
        $this->db = $db;
    }

    public function annulerSession($id, $reason)
    {
        // Vérifier si la session existe et si une annulation a déjà été faite
        $query = 'SELECT motif_annulation FROM sessions WHERE id = :id';
        $stmt = $this->db->getPDO()->prepare($query);
        $stmt->execute([':id' => $id]);
        $session = $stmt->fetch();

        if ($session) {
            if ($session['motif_annulation'] === null) {
                // Mettre à jour la colonne motif_annulation avec la raison
                $updateQuery = 'UPDATE sessions SET motif_annulation = :reason WHERE id = :id';
                $updateStmt = $this->db->getPDO()->prepare($updateQuery);
                $result = $updateStmt->execute([':reason' => $reason, ':id' => $id]);

                return $result;
            } else {
                // Une annulation a déjà été faite pour cette session
                return false;
            }
        } else {
            // La session n'existe pas
            return false;
        }
    }
}

