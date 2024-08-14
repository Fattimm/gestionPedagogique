<?php

namespace Src\App\Controller;

use Src\Core\Database\MysqlDatabase;
use Src\App\Model\AnnulerSessionModel;

class AnnulerSessionController
{
    private $model;

    public function __construct(MysqlDatabase $db)
    {
        $this->model = new AnnulerSessionModel($db);
    }

    public function annulerSession()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $reason = $_POST['reason'] ?? null;

            if ($id && $reason) {
                $result = $this->model->annulerSession($id, $reason);

                if ($result) {
                    echo '<p class="text-orange-500">Succès de l\'annulation</p>';
                } else {
                    echo '<p class="text-red-500">Échec de l\'annulation ou demande déjà faite</p>';
                }
            } else {
                echo '<p class="text-red-500">Données manquantes</p>';
            }
        } else {
            echo '<p class="text-red-500">Méthode non autorisée</p>';
        }
    }
}
