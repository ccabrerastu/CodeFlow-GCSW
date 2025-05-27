<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/ElementoConfiguracionModel.php';
require_once __DIR__ . '/ECSFaseMetodologiaModel.php';
class FasesMetodologiaModel {
    private $id_fase_metodologia;
    private $id_metodologia;
    private $nombre_fase;
    private $descripcion;
    private $orden;

    private $conexion;
    private $ecsFaseModel;

    public function __construct() {
        try {
            $db_conexion = new Conexion();
            $this->conexion = $db_conexion->getConexion();
            if ($this->conexion === null) {
                throw new Exception("La conexión a la base de datos no se pudo establecer en FaseMetodologiaModel.");
            }
            $this->ecsFaseModel = new ECSFaseMetodologiaModel();
        } catch (Exception $e) {
            error_log("Error de conexión en FasesMetodologiaModel: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
        }
    }

    public function setIdFaseMetodologia($id) { $this->id_fase_metodologia = $id; }
    public function getIdFaseMetodologia() { return $this->id_fase_metodologia; }
    public function setIdMetodologia($id) { $this->id_metodologia = $id; }
    public function getIdMetodologia() { return $this->id_metodologia; }
    public function setNombreFase($nombre) { $this->nombre_fase = $nombre; }
    public function getNombreFase() { return $this->nombre_fase; }
    public function setDescripcion($desc) { $this->descripcion = $desc; }
    public function getDescripcion() { return $this->descripcion; }
    public function setOrden($orden) { $this->orden = $orden; }
    public function getOrden() { return $this->orden; }



    public function obtenerFasesPorMetodologia($id_metodologia) {
        if ($this->conexion === null) {
            error_log("FasesMetodologiaModel: No hay conexión a la base de datos.");
            return [];
        }

        $sql = "SELECT id_fase_metodologia, nombre_fase, descripcion, orden 
                FROM FasesMetodologia 
                WHERE id_metodologia = ? 
                ORDER BY orden ASC, nombre_fase ASC";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (obtenerFasesPorMetodologia): " . $this->conexion->error);
            return [];
        }

        $stmt->bind_param("i", $id_metodologia);
        $stmt->execute();

        if ($stmt->error) {
            error_log("Error al ejecutar la consulta (obtenerFasesPorMetodologia): " . $stmt->error);
            $stmt->close();
            return [];
        }

        $resultado = $stmt->get_result();
        $fases = [];
        while ($fila = $resultado->fetch_assoc()) {
            $fases[] = $fila;
        }
        $stmt->close();
        return $fases;
    }
    


    public function crearFase() {
        if ($this->conexion === null) {
            error_log("FasesMetodologiaModel: No hay conexión a la base de datos.");
            return false;
        }
        $sql = "INSERT INTO FasesMetodologia (id_metodologia, nombre_fase, descripcion, orden) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (crearFase): " . $this->conexion->error);
            return false;
        }

        $stmt->bind_param("issi", $this->id_metodologia, $this->nombre_fase, $this->descripcion, $this->orden);

        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $stmt->close();
            return $new_id;
        } else {
            error_log("Error al ejecutar la consulta (crearFase): " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    public function obtenerFasePorId($id_fase) {
        if ($this->conexion === null) {
            error_log("FasesMetodologiaModel: No hay conexión a la base de datos.");
            return null;
        }
        $sql = "SELECT id_fase_metodologia, id_metodologia, nombre_fase, descripcion, orden FROM FasesMetodologia WHERE id_fase_metodologia = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare obtenerFasePorId: " . $this->conexion->error);
            return null;
        }
        $stmt->bind_param("i", $id_fase);
        if (!$stmt->execute()) {
            error_log("Error en execute obtenerFasePorId: " . $stmt->error);
            $stmt->close();
            return null;
        }
        $resultado = $stmt->get_result();
        $fase = $resultado->fetch_assoc();
        $stmt->close();
        return $fase;
    }
    public function obtenerFasesConSusECS($id_metodologia) {
        $fases = $this->obtenerFasesPorMetodologia($id_metodologia);
        $fasesConECS = [];

        if ($this->ecsFaseModel === null) {
            error_log("FaseMetodologiaModel::obtenerFasesConSusECS - ECSFaseMetodologiaModel no fue instanciado.");
            foreach ($fases as $fase) {
                $fase['elementos'] = [];
                $fasesConECS[] = $fase;
            }
            return $fasesConECS;
        }

        foreach ($fases as $fase) {
            $fase['elementos'] = $this->ecsFaseModel->obtenerECSPorFase($fase['id_fase_metodologia']);
            $fasesConECS[] = $fase;
        }
        return $fasesConECS;
    }
    public function obtenerFasesConSusECSD($id_metodologia) {
        $fases = $this->obtenerFasesPorMetodologia($id_metodologia);
        $fasesConECS = [];

        if ($this->ecsFaseModel === null) {
            error_log("FaseMetodologiaModel::obtenerFasesConSusECS - ECSFaseMetodologiaModel no fue instanciado.");
            foreach ($fases as $fase) {
                $fase['elementos'] = [];
                $fasesConECS[] = $fase;
            }
            return $fasesConECS;
        }

        foreach ($fases as $fase) {
            $fase['elementos'] = $this->ecsFaseModel->obtenerECSPorFaseD($fase['id_fase_metodologia']);
            $fasesConECS[] = $fase;
        }
        return $fasesConECS;
    }

    public function actualizarFase() {
        if ($this->conexion === null || $this->id_fase_metodologia === null) {
            error_log("FasesMetodologiaModel: No hay conexión o ID de fase no especificado para actualizar.");
            return false;
        }
        $sql = "UPDATE FasesMetodologia SET nombre_fase = ?, descripcion = ?, orden = ? WHERE id_fase_metodologia = ?";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (actualizarFase): " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param("ssii", $this->nombre_fase, $this->descripcion, $this->orden, $this->id_fase_metodologia);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al ejecutar la consulta (actualizarFase): " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }

    public function eliminarFase($id_fase) {
        if ($this->conexion === null) {
            error_log("FasesMetodologiaModel: No hay conexión a la base de datos.");
            return false;
        }
        $sql = "DELETE FROM FasesMetodologia WHERE id_fase_metodologia = ?";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (eliminarFase): " . $this->conexion->error);
            return false;
        }

        $stmt->bind_param("i", $id_fase);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al ejecutar la consulta (eliminarFase): " . $stmt->error);
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
