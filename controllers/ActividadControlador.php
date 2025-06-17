<?php
require_once __DIR__ . '/../model/ActividadCronogramaModel.php';
require_once __DIR__ . '/../model/EntregableActividadModel.php';
require_once __DIR__ . '/../model/ProyectoModel.php';
require_once __DIR__ . '/../model/ElementoConfiguracionModel.php';

class ActividadControlador {
    private $actividadModel;
    private $entregableModel;
    private $proyectoModel;
    private $ecsModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->actividadModel = new ActividadCronogramaModel();
        $this->entregableModel = new EntregableActividadModel();
        $this->proyectoModel = new ProyectoModel();
        $this->ecsModel = new ElementoConfiguracionModel();
    }

    
    public function index() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?c=Login&a=mostrarFormularioLogin");
            exit;
        }

        $id_usuario = $_SESSION['id_usuario'];
        $actividades = $this->actividadModel->obtenerActividadesPorResponsable($id_usuario);
        
        $statusMessage = $_SESSION['status_message'] ?? null;
        unset($_SESSION['status_message']);
        
        $baseUrl = "/";
        require __DIR__ . '/../views/actividades/listarActividadesVista.php';
    }

    public function gestionar() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?c=Login&a=mostrarFormularioLogin");
            exit;
        }

        $id_actividad = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id_actividad) {
            die("ID de actividad no válido.");
        }

        $actividad = $this->actividadModel->obtenerActividadPorId($id_actividad);
        
        // Verificación de seguridad: ¿Este usuario es el responsable de la actividad?
        if (!$actividad || $actividad['id_responsable'] != $_SESSION['id_usuario']) {
            $_SESSION['status_message'] = ['type' => 'error', 'text' => 'No tiene permisos para gestionar esta actividad.'];
            header("Location: index.php?c=Actividad&a=index");
            exit;
        }

        // Obtener los ECS asociados a esta actividad como entregables
        $entregables = $this->entregableModel->obtenerECSAsociadosAActividad($id_actividad);

        $statusMessage = $_SESSION['status_message'] ?? null;
        unset($_SESSION['status_message']);

        $baseUrl = "/";
        require __DIR__ . '/../views/actividades/gestionarActividadVista.php';
    }


    public function subirEntregable() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?c=Login&a=mostrarFormularioLogin");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_actividad = filter_input(INPUT_POST, 'id_actividad', FILTER_VALIDATE_INT);
            $id_ecs = filter_input(INPUT_POST, 'id_ecs', FILTER_VALIDATE_INT);
            $id_proyecto = filter_input(INPUT_POST, 'id_proyecto', FILTER_VALIDATE_INT); // <<< CORRECCIÓN AQUÍ

            // Verificación completa de los datos necesarios
            if (!$id_actividad || !$id_ecs || !$id_proyecto || !isset($_FILES['archivo_entregable']) || $_FILES['archivo_entregable']['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Error: Faltan datos o hubo un problema con la subida del archivo.'];
                // Redirigir de vuelta a la página de gestión si hay un ID de actividad
                $redirect_url = $id_actividad ? "index.php?c=Actividad&a=gestionar&id=" . $id_actividad : "index.php?c=Actividad&a=index";
                header("Location: " . $redirect_url);
                exit;
            }

            // Lógica de seguridad adicional: verificar que el usuario tenga permisos sobre esta actividad
            $actividad_data = $this->actividadModel->obtenerActividadPorId($id_actividad);
            if (!$actividad_data || $actividad_data['id_responsable'] != $_SESSION['id_usuario']) {
                 $_SESSION['status_message'] = ['type' => 'error', 'text' => 'No tienes permiso para subir un entregable a esta actividad.'];
                 header("Location: index.php?c=Actividad&a=index");
                 exit;
            }

            $archivo = $_FILES['archivo_entregable'];
            $directorioDestino = 'uploads/entregables/';
            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0775, true);
            }
            
            // Construir un nombre de archivo único y descriptivo
            $nombreArchivo = "proy" . $id_proyecto . "_act" . $id_actividad . "_ecs" . $id_ecs . "_" . time() . "_" . preg_replace('/[^A-Za-z0-9\.\-]/', '_', basename($archivo['name']));
            $rutaCompleta = $directorioDestino . $nombreArchivo;

            if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
                $resultado = $this->entregableModel->registrarEntrega($id_actividad, $id_ecs, $rutaCompleta);
                $this->actividadModel->actualizarEstadoActividad($id_actividad, 'Completada'); // Opcional: cambiar a 'Completada' o 'En Revisión'

                $_SESSION['status_message'] = $resultado 
                    ? ['type' => 'success', 'text' => 'Archivo subido y entrega registrada exitosamente.']
                    : ['type' => 'error', 'text' => 'Error al guardar la ruta del archivo en la base de datos.'];
            } else {
                $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Error al mover el archivo subido. Verifique los permisos de la carpeta de subidas.'];
            }
            header("Location: index.php?c=Actividad&a=gestionar&id=" . $id_actividad);
            exit;
        }
    }

    /**
     * Procesa la actualización del estado de una actividad.
     */
    public function actualizarEstado() {
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: index.php?c=Login&a=mostrarFormularioLogin");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_actividad = filter_input(INPUT_POST, 'id_actividad', FILTER_VALIDATE_INT);
            $nuevo_estado = $_POST['estado_actividad'] ?? '';
            
            // Validar que el estado sea uno de los permitidos para el usuario
            $estados_permitidos = ['Pendiente', 'En Progreso']; // El usuario no puede ponerse "Completada" directamente
            if (!$id_actividad || !in_array($nuevo_estado, $estados_permitidos)) {
                 $_SESSION['status_message'] = ['type' => 'error', 'text' => 'Datos inválidos para actualizar el estado.'];
            } else {
                 $resultado = $this->actividadModel->actualizarEstadoActividad($id_actividad, $nuevo_estado);
                 $_SESSION['status_message'] = $resultado
                    ? ['type' => 'success', 'text' => 'Estado de la actividad actualizado.']
                    : ['type' => 'error', 'text' => 'Error al actualizar el estado.'];
            }
            header("Location: index.php?c=Actividad&a=gestionar&id=" . $id_actividad);
            exit;
        }
    }
}
?>
