<?php
require_once __DIR__ . '/../model/MetodologiaModel.php';

class MetodologiaControlador {

    private $metodologiaModel;

    public function __construct() {
        $this->metodologiaModel = new MetodologiaModel();
    }


    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        

        $statusMessage = $_SESSION['status_message'] ?? null;
        unset($_SESSION['status_message']); 

        $metodologias = $this->metodologiaModel->obtenerTodasLasMetodologias();
        $baseUrl = "/";

        require __DIR__ . '/../views/listarMetodologiasVista.php';
    }

    
    public function apiListarMetodologias() {
        header('Content-Type: application/json');
        $metodologias = $this->metodologiaModel->obtenerTodasLasMetodologias();
        if ($metodologias === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener las metodologías.']);
        } else {
            echo json_encode($metodologias);
        }
    }

    public function apiObtenerMetodologia($id_metodologia) {
        header('Content-Type: application/json');
        if (!filter_var($id_metodologia, FILTER_VALIDATE_INT)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID de metodología inválido.']);
            return;
        }
        $metodologia = $this->metodologiaModel->obtenerMetodologiaPorId((int)$id_metodologia);
        if ($metodologia) {
            echo json_encode($metodologia);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Metodología no encontrada.']);
        }
    }


    public function mostrarFormularioCrear() {
        echo "Funcionalidad para crear metodologías no implementada en esta vista de ejemplo.";
    }

    public function crear() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre_metodologia'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $formErrors = [];

            if (empty($nombre)) {
                $formErrors['nombre_metodologia'] = "El nombre de la metodología es obligatorio.";
            }
            if (!empty($formErrors)) {
                $_SESSION['form_data'] = $_POST;
                $_SESSION['form_errors'] = $formErrors;
                echo "Errores de validación: " . implode(", ", $formErrors);
                return;
            }

            $this->metodologiaModel->setNombreMetodologia($nombre);
            $this->metodologiaModel->setDescripcion($descripcion);
            $resultado = $this->metodologiaModel->crearMetodologia();

            if ($resultado) {
                $_SESSION['status_message'] = ['type' => 'success', 'text' => 'Metodología creada exitosamente.'];
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Error al crear la metodología.'];
            }
            header("Location: index.php?c=Metodologia&a=index");
            exit;
        }
        header("Location: index.php?c=Metodologia&a=index");
        exit;
    }
}
?>
