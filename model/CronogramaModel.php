<?php
require_once __DIR__ . '/../config/database.php';

class CronogramaModel {
    private $id_cronograma;
    private $id_proyecto;
    private $descripcion;
    private $fecha_creacion;
    private $fecha_ultima_modificacion;

    private $conexion;

    public function __construct() {
        try {
            $db_conexion = new Conexion();
            $this->conexion = $db_conexion->getConexion();
        } catch (Exception $e) {
            error_log("Error de conexión en CronogramaModel: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
        }
    }

    // --- Getters y Setters ---
    public function setIdCronograma($id) { $this->id_cronograma = $id; }
    public function getIdCronograma() { return $this->id_cronograma; }
    public function setIdProyecto($id_proyecto) { $this->id_proyecto = $id_proyecto; }
    public function getIdProyecto() { return $this->id_proyecto; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
    public function getDescripcion() { return $this->descripcion; }
    public function crearCronograma() {
        if ($this->conexion === null) {
            error_log("CronogramaModel: No hay conexión a la base de datos.");
            return false;
        }
        $sql = "INSERT INTO Cronogramas (id_proyecto, descripcion) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (crearCronograma): " . $this->conexion->error);
            return false;
        }

        $descripcion = $this->descripcion ?? "Cronograma del proyecto"; // Valor por defecto

        $stmt->bind_param("is", $this->id_proyecto, $descripcion);

        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $stmt->close();
            return $new_id;
        } else {
            error_log("Error al ejecutar la consulta (crearCronograma): " . $stmt->error);
            $stmt->close();
            return false;
        }
    }


    public function obtenerCronogramaPorProyecto($id_proyecto) {
        if ($this->conexion === null) return null;
        $sql = "SELECT * FROM Cronogramas WHERE id_proyecto = ? LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare obtenerCronogramaPorProyecto: " . $this->conexion->error);
            return null;
        }
        $stmt->bind_param("i", $id_proyecto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $cronograma = $resultado->fetch_assoc();
        $stmt->close();
        return $cronograma;
    }


    public function __destruct() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}
?>
