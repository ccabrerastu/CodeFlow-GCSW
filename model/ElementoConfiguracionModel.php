<?php
require_once __DIR__ . '/../config/database.php';

class ElementoConfiguracionModel {
    private $id_ecs;
    private $id_proyecto;
    private $nombre_ecs;
    private $descripcion;
    private $tipo_ecs;
    private $version_actual;
    private $estado_ecs; // Corregido de stado_ec
    private $ruta_repositorio;
    private $fecha_creacion;
    private $id_creador;
    private $fecha_ultima_modificacion;
    private $id_ultimo_modificador;

    private $conexion;

    public function __construct() {
        try {
            $db_conexion = new Conexion();
            $this->conexion = $db_conexion->getConexion();
        } catch (Exception $e) {
            error_log("Error de conexión en ElementoConfiguracionModel: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
        }
    }

    // --- Getters y Setters (Añade según necesidad) ---
    public function setIdEcs($id_ecs) { $this->id_ecs = $id_ecs; }
    public function getIdEcs() { return $this->id_ecs; }
    public function setIdProyecto($id_proyecto) { $this->id_proyecto = $id_proyecto; }
    public function getIdProyecto() { return $this->id_proyecto; }
    public function setNombreEcs($nombre_ecs) { $this->nombre_ecs = $nombre_ecs; }
    public function getNombreEcs() { return $this->nombre_ecs; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
    public function getDescripcion() { return $this->descripcion; }
    public function setTipoEcs($tipo_ecs) { $this->tipo_ecs = $tipo_ecs; }
    public function getTipoEcs() { return $this->tipo_ecs; }
    public function setVersionActual($version_actual) { $this->version_actual = $version_actual; }
    public function getVersionActual() { return $this->version_actual; }
    public function setEstadoEcs($estado_ecs) { $this->estado_ecs = $estado_ecs; } // Corregido aquí también
    public function getEstadoEcs() { return $this->estado_ecs; }
    public function setIdCreador($id_creador) { $this->id_creador = $id_creador; }
    public function getIdCreador() { return $this->id_creador; } // Getter añadido para id_creador
    public function setIdUltimoModificador($id_ultimo_modificador) { $this->id_ultimo_modificador = $id_ultimo_modificador; } // Setter añadido
    public function getIdUltimoModificador() { return $this->id_ultimo_modificador; } // Getter añadido


    /**
     * Crea un nuevo Elemento de Configuración.
     * @return int|false El ID del ECS insertado o false en caso de error.
     */
    public function crearECS() {
        if ($this->conexion === null) {
            error_log("ElementoConfiguracionModel: No hay conexión a la base de datos.");
            return false;
        }
        $sql = "INSERT INTO ElementosConfiguracion 
                    (id_proyecto, nombre_ecs, descripcion, tipo_ecs, version_actual, estado_ecs, id_creador) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (crearECS): " . $this->conexion->error);
            return false;
        }

        // Valores por defecto si no se establecen
        $version = $this->version_actual ?? '1.0';
        $estado = $this->estado_ecs ?? 'Definido';

        $stmt->bind_param("isssssi",
            $this->id_proyecto,
            $this->nombre_ecs,
            $this->descripcion,
            $this->tipo_ecs,
            $version,
            $estado,
            $this->id_creador
        );

        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $stmt->close();
            return $new_id;
        } else {
            error_log("Error al ejecutar la consulta (crearECS): " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    /**
     * Obtiene todos los ECS de un proyecto específico.
     * @param int $id_proyecto
     * @return array Lista de ECS o un array vacío.
     */
    public function obtenerECS_PorProyecto($id_proyecto) {
        if ($this->conexion === null) return [];
        $sql = "SELECT e_pr.*, ec.nombre_ecs as nombre_ecs, fm.nombre_fase as nombre_fase, pr.nombre_proyecto as nombre_proyecto
                FROM ECS_Proyecto  e_pr
                LEFT JOIN ElementosConfiguracion ec ON e_pr.id_ecs_proyecto = ec.id_ecs
                LEFT JOIN FasesMetodologia fm ON e_pr.id_ec_fase_met = fm.id_fase_metodologia
                LEFT JOIN Proyectos pr ON e_pr.id_proyecto = pr.id_proyecto
                WHERE e_pr.id_proyecto = ? ORDER BY ec.nombre_ecs ASC";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare obtenerECS_PorProyecto: " . $this->conexion->error);
            return [];
        }
        $stmt->bind_param("i", $id_proyecto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $ecs_lista = [];
        while ($fila = $resultado->fetch_assoc()) {
            $ecs_lista[] = $fila;
        }
        $stmt->close();
        return $ecs_lista;
    }

    /**
     * Obtiene un ECS específico por su ID.
     * @param int $id_ecs
     * @return array|null Datos del ECS o null si no se encuentra.
     */
    public function obtenerECSPorId($id_ecs) {
        if ($this->conexion === null) return null;
        $sql = "SELECT * FROM ElementosConfiguracion WHERE id_ecs = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare obtenerECSPorId: " . $this->conexion->error);
            return null;
        }
        $stmt->bind_param("i", $id_ecs);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $ecs = $resultado->fetch_assoc();
        $stmt->close();
        return $ecs;
    }

    /**
     * Actualiza un ECS existente.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function actualizarECS() {
        if ($this->conexion === null || $this->id_ecs === null) {
            error_log("ElementoConfiguracionModel: No hay conexión o ID de ECS no especificado.");
            return false;
        }
        $sql = "UPDATE ElementosConfiguracion SET 
                    nombre_ecs = ?, 
                    descripcion = ?, 
                    tipo_ecs = ?, 
                    version_actual = ?, 
                    estado_ecs = ?,
                    id_ultimo_modificador = ?,
                    fecha_ultima_modificacion = CURRENT_TIMESTAMP
                WHERE id_ecs = ?";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (actualizarECS): " . $this->conexion->error);
            return false;
        }
        // Asumimos que id_ultimo_modificador se setea desde el controlador
        $stmt->bind_param("sssssii",
            $this->nombre_ecs,
            $this->descripcion,
            $this->tipo_ecs,
            $this->version_actual,
            $this->estado_ecs,
            $this->id_ultimo_modificador,
            $this->id_ecs
        );

        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al ejecutar la consulta (actualizarECS): " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }

    /**
     * Elimina un ECS por su ID.
     * @param int $id_ecs
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function eliminarECS($id_ecs) {
        if ($this->conexion === null) return false;
        // Considerar verificar si el ECS está referenciado en EntregablesActividad, SolicitudesCambio, OrdenesCambio, VersionesECS
        // antes de permitir la eliminación o manejarlo con ON DELETE SET NULL/CASCADE en la BD.
        $sql = "DELETE FROM ElementosConfiguracion WHERE id_ecs = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare eliminarECS: " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param("i", $id_ecs);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al ejecutar la consulta (eliminarECS): " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }

    public function __destruct() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}
?>
