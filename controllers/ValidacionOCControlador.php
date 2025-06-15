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

        $formDataVal   = $_SESSION['form_data_val']   ?? [];
        $formErrorsVal = $_SESSION['form_errors_val'] ?? [];
        unset($_SESSION['form_data_val'], $_SESSION['form_errors_val']);

        require __DIR__ . '/../views/validacionOC/validarOrdenCambioVista.php';
    }

    public function validar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?c=OrdenCambio&a=index");
            exit;
        }

        $id_orden            = filter_input(INPUT_POST, 'id_orden', FILTER_VALIDATE_INT);
        $decision            = $_POST['resultado_validacion']    ?? '';
        $comentario_validar  = trim($_POST['comentarios_validacion'] ?? '');

        $errors = [];
        if (!$id_orden) {
            $errors['general'] = "Orden inválida.";
        }
        if (!in_array($decision, ['1','0'], true)) {
            $errors['resultado_validacion'] = "Seleccione Aprobado o Rechazado.";
        }
        if ($comentario_validar === '') {
            $errors['comentarios_validacion'] = "El comentario justificativo es obligatorio.";
        }

        if ($errors) {
            $_SESSION['form_data_val']   = $_POST;
            $_SESSION['form_errors_val'] = $errors;
            header("Location: index.php?c=ValidacionOC&a=validarForm&id_orden={$id_orden}");
            exit;
        }
        
        $id_validador = $_SESSION['id_usuario'];
        $ok = $this->model->validarOrden(
            $id_orden,
            $id_validador,
            (int)$decision,
            $comentario_validar
        );

        $_SESSION['status_message'] = $ok
            ? ['type'=>'success','text'=>'Orden validada exitosamente.']
            : ['type'=>'error','text'=>'Error al validar la orden.'];

        header("Location: index.php?c=OrdenCambio&a=index");
        exit;
    }
}