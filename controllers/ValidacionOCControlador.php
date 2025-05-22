<?php
require_once __DIR__ . '/../model/ValidacionOCModel.php';
require_once __DIR__ . '/../model/OrdenCambioModel.php';

class ValidacionOCControlador {
    private $model, $ordenModel;

    public function __construct() {
        session_start();
        $this->model      = new ValidacionOCModel();
        $this->ordenModel = new OrdenCambioModel();
    }

    public function validarForm($id_orden) {
        $orden = $this->ordenModel->obtenerOrdenPorId($id_orden);
        if (!$orden) {
            $_SESSION['status_message'] = ['type'=>'error','text'=>'Orden no encontrada.'];
            header("Location: index.php?c=OrdenCambio&a=index");
            exit;
        }
        $formErrors = $_SESSION['form_errors_val'] ?? [];
        unset($_SESSION['form_errors_val']);
        require __DIR__ . '/../views/validacionOC/validarOrdenCambioVista.php';
    }

    public function validar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=OrdenCambio&a=index");
            exit;
        }

        $id_orden   = filter_input(INPUT_POST,'id_orden',FILTER_VALIDATE_INT);
        $decision   = $_POST['decision'] ?? '';
        $comentario = trim($_POST['comentario'] ?? '');

        $errors = [];
        if (!$id_orden)                                          $errors['general']  = "Orden inválida.";
        if (!in_array($decision, ['Aprobada','Rechazada']))      $errors['decision'] = "Seleccione Aprobada o Rechazada.";

        if (!empty($errors)) {
            $_SESSION['form_errors_val'] = $errors;
            header("Location: index.php?c=ValidacionOC&a=validarForm&id_orden={$id_orden}");
            exit;
        }

        $ok = $this->model->validarOrden(
            $id_orden,
            $_SESSION['usuario']['id_usuario'],
            ($decision === 'Aprobada') ? 1 : 0,
            $comentario
        );

        $_SESSION['status_message'] = $ok
            ? ['type'=>'success','text'=>"Orden {$decision} correctamente."]
            : ['type'=>'error','text'=>'Error al validar.'];

        header("Location: index.php?c=OrdenCambio&a=index");
        exit;
    }
}
