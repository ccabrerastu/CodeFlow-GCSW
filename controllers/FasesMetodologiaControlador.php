<?php
require_once __DIR__ . '/../model/FasesMetodologiaModel.php';
require_once __DIR__ . '/../model/MetodologiaModel.php';

class FasesMetodologiaControlador {

    private $fasesMetodologiaModel;
    private $metodologiaModel;

    public function __construct() {
        $this->fasesMetodologiaModel = new FasesMetodologiaModel();
        $this->metodologiaModel = new MetodologiaModel(); 
    }


    public function listarPorMetodologia() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_metodologia = filter_input(INPUT_GET, 'id_metodologia', FILTER_VALIDATE_INT);

        if (!$id_metodologia) {
            $_SESSION['status_message'] = ['type' => 'error', 'text' => 'ID de metodología no válido.'];
            header("Location: index.php?c=Metodologia&a=index");
            exit;
        }

        $metodologia = $this->metodologiaModel->obtenerMetodologiaPorId($id_metodologia);
        if (!$metodologia) {
            $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Metodología no encontrada.'];
            header("Location: index.php?c=Metodologia&a=index");
            exit;
        }

        $fases = $this->fasesMetodologiaModel->obtenerFasesPorMetodologia($id_metodologia);
        $statusMessage = $_SESSION['status_message'] ?? null;
        unset($_SESSION['status_message']);

        $baseUrl = "/"; 

        require __DIR__ . '/../views/gestionarFasesVista.php';
    }

    public function mostrarFormularioCrear() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_metodologia = filter_input(INPUT_GET, 'id_metodologia', FILTER_VALIDATE_INT);
        if (!$id_metodologia) {
            $_SESSION['status_message'] = ['type' => 'error', 'text' => 'ID de metodología no válido para crear fase.'];
            header("Location: index.php?c=Metodologia&a=index");
            exit;
        }

        $metodologia = $this->metodologiaModel->obtenerMetodologiaPorId($id_metodologia);
        if (!$metodologia) {
            $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Metodología no encontrada.'];
            header("Location: index.php?c=Metodologia&a=index");
            exit;
        }
        
        $formData = $_SESSION['form_data_fase'] ?? ['nombre_fase' => '', 'descripcion' => '', 'orden' => ''];
        $formErrors = $_SESSION['form_errors_fase'] ?? [];
        unset($_SESSION['form_data_fase'], $_SESSION['form_errors_fase']);

        $baseUrl = "/";
        require __DIR__ . '/../views/crearEditarFaseVista.php';
    }


    public function crear() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_metodologia = filter_input(INPUT_POST, 'id_metodologia', FILTER_VALIDATE_INT);
            $nombre_fase = trim($_POST['nombre_fase'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $orden = filter_input(INPUT_POST, 'orden', FILTER_VALIDATE_INT);

            $formErrors = [];
            if (!$id_metodologia) {
                $formErrors['general'] = "ID de metodología no válido.";
            }
            if (empty($nombre_fase)) {
                $formErrors['nombre_fase'] = "El nombre de la fase es obligatorio.";
            }
            if ($orden === false || $orden < 0) { 
                $formErrors['orden'] = "El orden debe ser un número entero no negativo.";
            }

            if (!empty($formErrors)) {
                $_SESSION['form_data_fase'] = $_POST;
                $_SESSION['form_errors_fase'] = $formErrors;
                header("Location: index.php?c=FasesMetodologia&a=mostrarFormularioCrear&id_metodologia=" . $id_metodologia);
                exit;
            }

            $this->fasesMetodologiaModel->setIdMetodologia($id_metodologia);
            $this->fasesMetodologiaModel->setNombreFase($nombre_fase);
            $this->fasesMetodologiaModel->setDescripcion($descripcion);
            $this->fasesMetodologiaModel->setOrden($orden);
            
            $resultado = $this->fasesMetodologiaModel->crearFase();

            if ($resultado) {
                $_SESSION['status_message'] = ['type' => 'success', 'text' => 'Fase creada exitosamente.'];
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Error al crear la fase.'];
            }
            header("Location: index.php?c=FasesMetodologia&a=listarPorMetodologia&id_metodologia=" . $id_metodologia);
            exit;
        }
        header("Location: index.php?c=Metodologia&a=index");
        exit;
    }


    public function mostrarFormularioEditar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id_fase = filter_input(INPUT_GET, 'id_fase', FILTER_VALIDATE_INT);
        if (!$id_fase) {
            $_SESSION['status_message'] = ['type' => 'error', 'text' => 'ID de fase no válido.'];
            header("Location: index.php?c=Metodologia&a=index"); 
            exit;
        }

        $fase = $this->fasesMetodologiaModel->obtenerFasePorId($id_fase);
        if (!$fase) {
            $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Fase no encontrada.'];
            header("Location: index.php?c=Metodologia&a=index"); 
            exit;
        }
        
        $metodologia = $this->metodologiaModel->obtenerMetodologiaPorId($fase['id_metodologia']);

        $formData = $_SESSION['form_data_fase'] ?? $fase;
        $formErrors = $_SESSION['form_errors_fase'] ?? [];
        unset($_SESSION['form_data_fase'], $_SESSION['form_errors_fase']);
        
        $baseUrl = "/";
        $accion = "editar"; 
        require __DIR__ . '/../views/crearEditarFaseVista.php';
    }


    public function editar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_fase = filter_input(INPUT_POST, 'id_fase_metodologia', FILTER_VALIDATE_INT);
            $id_metodologia = filter_input(INPUT_POST, 'id_metodologia', FILTER_VALIDATE_INT);
            $nombre_fase = trim($_POST['nombre_fase'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $orden = filter_input(INPUT_POST, 'orden', FILTER_VALIDATE_INT);

            $formErrors = [];
            if (!$id_fase) {
                $formErrors['general'] = "ID de fase no válido.";
            }
            if (!$id_metodologia) {
                $formErrors['general'] = "ID de metodología no válido.";
            }
            if (empty($nombre_fase)) {
                $formErrors['nombre_fase'] = "El nombre de la fase es obligatorio.";
            }
            if ($orden === false || $orden < 0) {
                $formErrors['orden'] = "El orden debe ser un número entero no negativo.";
            }

            if (!empty($formErrors)) {
                $_SESSION['form_data_fase'] = $_POST;
                $_SESSION['form_errors_fase'] = $formErrors;
                header("Location: index.php?c=FasesMetodologia&a=mostrarFormularioEditar&id_fase=" . $id_fase);
                exit;
            }

            $this->fasesMetodologiaModel->setIdFaseMetodologia($id_fase);
            $this->fasesMetodologiaModel->setNombreFase($nombre_fase);
            $this->fasesMetodologiaModel->setDescripcion($descripcion);
            $this->fasesMetodologiaModel->setOrden($orden);
            
            $resultado = $this->fasesMetodologiaModel->actualizarFase();

            if ($resultado) {
                $_SESSION['status_message'] = ['type' => 'success', 'text' => 'Fase actualizada exitosamente.'];
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Error al actualizar la fase.'];
            }
            header("Location: index.php?c=FasesMetodologia&a=listarPorMetodologia&id_metodologia=" . $id_metodologia);
            exit;
        }
        header("Location: index.php?c=Metodologia&a=index");
        exit;
    }


    public function eliminar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        $id_fase = filter_input(INPUT_GET, 'id_fase', FILTER_VALIDATE_INT);
        $id_metodologia = filter_input(INPUT_GET, 'id_metodologia', FILTER_VALIDATE_INT);

        if (!$id_fase || !$id_metodologia) {
            $_SESSION['status_message'] = ['type' => 'error', 'text' => 'ID de fase o metodología no válido.'];
        } else {
            $resultado = $this->fasesMetodologiaModel->eliminarFase($id_fase);
            if ($resultado) {
                $_SESSION['status_message'] = ['type' => 'success', 'text' => 'Fase eliminada exitosamente.'];
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Error al eliminar la fase. Puede estar en uso.'];
            }
        }
        header("Location: index.php?c=FasesMetodologia&a=listarPorMetodologia&id_metodologia=" . $id_metodologia);
        exit;
    }
}
?>
