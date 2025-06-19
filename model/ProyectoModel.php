<?php
require_once __DIR__ . '/../config/database.php';

class ProyectoModel {
    private $id_proyecto;
    private $nombre_proyecto;
    private $descripcion;
    private $id_metodologia;
    private $id_product_owner;
    private $fecha_creacion;
    private $fecha_inicio_planificada;
    private $fecha_fin_planificada;
    private $estado_proyecto;

    private $conexion;

    public function __construct() {
        try {
            $db_conexion = new Conexion();
            $this->conexion = $db_conexion->getConexion();
        } catch (Exception $e) {
            error_log("Error de conexión en ProyectoModel: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
        }
    }

    // --- Getters y Setters ---
    public function getIdProyecto() { return $this->id_proyecto; }
    public function setIdProyecto($id) { $this->id_proyecto = $id; }

    public function getNombreProyecto() { return $this->nombre_proyecto; }
    public function setNombreProyecto($nombre) { $this->nombre_proyecto = $nombre; }

    public function getDescripcion() { return $this->descripcion; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }

    public function getIdMetodologia() { return $this->id_metodologia; }
    public function setIdMetodologia($id_metodologia) { $this->id_metodologia = $id_metodologia; }

    public function getIdProductOwner() { return $this->id_product_owner; }
    public function setIdProductOwner($id_product_owner) { $this->id_product_owner = $id_product_owner; }

    public function getFechaInicioPlanificada() { return $this->fecha_inicio_planificada; }
    public function setFechaInicioPlanificada($fecha) { $this->fecha_inicio_planificada = $fecha; }

    public function getFechaFinPlanificada() { return $this->fecha_fin_planificada; }
    public function setFechaFinPlanificada($fecha) { $this->fecha_fin_planificada = $fecha; }

    public function getEstadoProyecto() { return $this->estado_proyecto; }
    public function setEstadoProyecto($estado) { $this->estado_proyecto = $estado; }

    public function getFechaCreacion() { return $this->fecha_creacion; }

    public function crearProyecto() {
        if ($this->conexion === null) {
            error_log("ProyectoModel: No hay conexión a la base de datos.");
            return false;
        }
        $sql = "INSERT INTO Proyectos (nombre_proyecto, descripcion, id_metodologia, id_product_owner, fecha_inicio_planificada, fecha_fin_planificada, estado_proyecto) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (crearProyecto): " . $this->conexion->error);
            return false;
        }

        $estado = $this->estado_proyecto ?? 'Activo';

        $stmt->bind_param("ssiisss",
            $this->nombre_proyecto,
            $this->descripcion,
            $this->id_metodologia,
            $this->id_product_owner,
            $this->fecha_inicio_planificada,
            $this->fecha_fin_planificada,
            $estado
        );
        
        if ($stmt->execute()) {
            $new_id = $this->conexion->insert_id;
            $stmt->close();
            return $new_id;
        } else {
            error_log("Error al ejecutar la consulta (crearProyecto): " . $stmt->error);
            $stmt->close();
            return false;
        }
    }


    public function obtenerTodosLosProyectos() {
        if ($this->conexion === null) return [];
        $sql = "SELECT p.id_proyecto, p.nombre_proyecto, p.descripcion, p.estado_proyecto, m.nombre_metodologia, u.nombre_completo as nombre_product_owner
                FROM Proyectos p
                LEFT JOIN Metodologias m ON p.id_metodologia = m.id_metodologia
                LEFT JOIN Usuarios u ON p.id_product_owner = u.id_usuario
                ORDER BY p.fecha_creacion DESC";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare obtenerTodosLosProyectos: " . $this->conexion->error);
            return [];
        }
        $stmt->execute();
        $resultado = $stmt->get_result();
        $proyectos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $proyectos[] = $fila;
        }
        $stmt->close();
        return $proyectos;
    }


    public function obtenerProyectoPorId($id_proyecto) {
        if ($this->conexion === null) return null;
        $sql = "SELECT p.*, m.nombre_metodologia, u.nombre_completo as nombre_product_owner
                FROM Proyectos p
                LEFT JOIN Metodologias m ON p.id_metodologia = m.id_metodologia
                LEFT JOIN Usuarios u ON p.id_product_owner = u.id_usuario
                WHERE p.id_proyecto = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare obtenerProyectoPorId: " . $this->conexion->error);
            return null;
        }
        $stmt->bind_param("i", $id_proyecto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $proyecto = $resultado->fetch_assoc();
        $stmt->close();
        return $proyecto;
    }


    public function actualizarProyecto() {
        if ($this->conexion === null || $this->id_proyecto === null) {
            error_log("ProyectoModel: No hay conexión o ID de proyecto no especificado para actualizar.");
            return false;
        }
        $sql = "UPDATE Proyectos SET 
                    nombre_proyecto = ?, 
                    descripcion = ?, 
                    id_metodologia = ?, 
                    id_product_owner = ?, 
                    fecha_inicio_planificada = ?, 
                    fecha_fin_planificada = ?,
                    estado_proyecto = ?
                WHERE id_proyecto = ?";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (actualizarProyecto): " . $this->conexion->error);
            return false;
        }

        $stmt->bind_param("ssiisssi",
            $this->nombre_proyecto,
            $this->descripcion,
            $this->id_metodologia,
            $this->id_product_owner,
            $this->fecha_inicio_planificada,
            $this->fecha_fin_planificada,
            $this->estado_proyecto,
            $this->id_proyecto
        );

        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al ejecutar la consulta (actualizarProyecto): " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }

    public function eliminarProyecto($id_proyecto) {
        if ($this->conexion === null) return false;
        $sql = "DELETE FROM Proyectos WHERE id_proyecto = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare eliminarProyecto: " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param("i", $id_proyecto);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al ejecutar la consulta (eliminarProyecto): " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }
    


    public function obtenerAvancePorProyecto(): array {
        if ($this->conexion === null) {
            error_log("ProyectoModel::obtenerAvancePorProyecto sin conexión");
            return [];
        }

        $sql = "
        SELECT
            p.id_proyecto,
            p.nombre_proyecto,
            COUNT(sc.id_sc)               AS total_sc,
            SUM(sc.estado_sc = 'Aprobada') AS sc_aprobadas
        FROM Proyectos p
        LEFT JOIN SolicitudesCambio sc
            ON p.id_proyecto = sc.id_proyecto
        GROUP BY p.id_proyecto, p.nombre_proyecto
        ";

        $res = $this->conexion->query($sql);

        if (! $res) {
            error_log("ProyectoModel::obtenerAvancePorProyecto error en query: " . $this->conexion->error);
            return [];
        }

        return $res->fetch_all(MYSQLI_ASSOC);
    }

    public function obtenerProyectosPorUsuario(int $id_usuario): array {
        $sql = "
          SELECT DISTINCT p.id_proyecto, p.nombre_proyecto
          FROM Proyectos p
          JOIN MiembrosEquipo me ON p.id_equipo = me.id_equipo
          WHERE me.id_usuario = ?
        ";
        $stmt = $this->conexion->query($sql);
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $arr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $arr;
    }

    public function __destruct() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}
?>
