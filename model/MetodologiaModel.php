<?php
require_once __DIR__ . '/../config/database.php';

class MetodologiaModel {
    private $id_metodologia;
    private $nombre_metodologia;
    private $descripcion;

    private $conexion;

    public function __construct() {
        try {
            $db_conexion = new Conexion();
            $this->conexion = $db_conexion->getConexion();
        } catch (Exception $e) {
            error_log("Error de conexión en MetodologiaModel: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
        }
    }

    public function getIdMetodologia() {
        return $this->id_metodologia;
    }

    public function setIdMetodologia($id_metodologia) {
        $this->id_metodologia = $id_metodologia;
    }

    public function getNombreMetodologia() {
        return $this->nombre_metodologia;
    }

    public function setNombreMetodologia($nombre_metodologia) {
        $this->nombre_metodologia = $nombre_metodologia;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }


    public function obtenerTodasLasMetodologias() {
        if ($this->conexion === null) {
            error_log("MetodologiaModel: No hay conexión a la base de datos.");
            return [];
        }

        $sql = "SELECT id_metodologia, nombre_metodologia, descripcion FROM Metodologias ORDER BY nombre_metodologia ASC";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (obtenerTodasLasMetodologias): " . $this->conexion->error);
            return [];
        }

        $stmt->execute();
        if ($stmt->error) {
            error_log("Error al ejecutar la consulta (obtenerTodasLasMetodologias): " . $stmt->error);
            $stmt->close();
            return [];
        }

        $resultado = $stmt->get_result();
        $metodologias = [];
        while ($fila = $resultado->fetch_assoc()) {
            $metodologias[] = $fila;
        }
        $stmt->close();
        return $metodologias;
    }

    public function obtenerMetodologiaPorId($id_metodologia) {
        if ($this->conexion === null) {
            error_log("MetodologiaModel: No hay conexión a la base de datos.");
            return null;
        }

        $sql = "SELECT id_metodologia, nombre_metodologia, descripcion FROM Metodologias WHERE id_metodologia = ?";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (obtenerMetodologiaPorId): " . $this->conexion->error);
            return null;
        }

        $stmt->bind_param("i", $id_metodologia);
        $stmt->execute();

        if ($stmt->error) {
            error_log("Error al ejecutar la consulta (obtenerMetodologiaPorId): " . $stmt->error);
            $stmt->close();
            return null;
        }

        $resultado = $stmt->get_result();
        $metodologia = $resultado->fetch_assoc();
        $stmt->close();
        return $metodologia;
    }


    public function crearMetodologia() {
        if ($this->conexion === null) {
            error_log("MetodologiaModel: No hay conexión a la base de datos.");
            return false;
        }
        $sql = "INSERT INTO Metodologias (nombre_metodologia, descripcion) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (crearMetodologia): " . $this->conexion->error);
            return false;
        }

        $stmt->bind_param("ss", $this->nombre_metodologia, $this->descripcion);

        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $stmt->close();
            return $new_id;
        } else {
            error_log("Error al ejecutar la consulta (crearMetodologia): " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    public function actualizarMetodologia() {
        if ($this->conexion === null || $this->id_metodologia === null) {
            error_log("MetodologiaModel: No hay conexión o ID de metodología no especificado para actualizar.");
            return false;
        }
        $sql = "UPDATE Metodologias SET nombre_metodologia = ?, descripcion = ? WHERE id_metodologia = ?";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (actualizarMetodologia): " . $this->conexion->error);
            return false;
        }

        $stmt->bind_param("ssi", $this->nombre_metodologia, $this->descripcion, $this->id_metodologia);

        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al ejecutar la consulta (actualizarMetodologia): " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }

    public function eliminarMetodologia($id_metodologia) {
        if ($this->conexion === null) {
            error_log("MetodologiaModel: No hay conexión a la base de datos.");
            return false;
        }
        $sql = "DELETE FROM Metodologias WHERE id_metodologia = ?";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (eliminarMetodologia): " . $this->conexion->error);
            return false;
        }

        $stmt->bind_param("i", $id_metodologia);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al ejecutar la consulta (eliminarMetodologia): " . $stmt->error);
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
