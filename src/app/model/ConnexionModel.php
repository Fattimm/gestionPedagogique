<?php

namespace Src\App\Model;

use Src\Core\Database\DatabaseInterface;
use PDO;

class ConnexionModel
{
    private $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function checkCredentials($email, $password)
{
    $pdo = $this->db->getPDO();

    // Vérifier dans la table Professeur
    $stmt = $pdo->prepare("
        SELECT id, nom, prenom, mot_de_passe, 'professeur' AS userType 
        FROM Professeur 
        WHERE email = :email
    ");
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    var_dump($stmt); // Debug: Voir les données récupérées

    // Si non trouvé dans Professeur, vérifier dans la table Etudiant
    if (!$user) {
        $stmt = $pdo->prepare("
            SELECT id, nom, prenom, mot_de_passe, 'etudiant' AS userType 
            FROM Etudiant 
            WHERE email = :email
        ");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        var_dump($user); // Debug: Voir les données récupérées
    }
    
   // Vérifier le mot de passe
   if ($user && $user['mot_de_passe'] === $password) {
    return $user;
}

    return false;
}

}
