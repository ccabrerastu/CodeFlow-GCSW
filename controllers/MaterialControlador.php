<?php
require_once __DIR__ . '/../model/MaterialModel.php';

class MaterialControlador {

    public function index() {

        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $isAdmin = (isset($_SESSION['idRol']) && $_SESSION['idRol'] == 2);

        $materialModel = new MaterialModel();
        $materiales = $materialModel->getAllMaterialesList();


        if ($isAdmin) {
            require __DIR__ . '/../views/gestionarMaterialesVista.php';
        } else {
            require __DIR__ . '/../views/visualizarMaterialesVista.php';
        }
    }


}
?>
