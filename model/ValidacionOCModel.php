<?php
require_once __DIR__ . '/../config/database.php';

class ValidacionOCModel {
    private $db;

    public function __construct() {
        $this->db = (new Conexion())->getConexion();
    }

    public function validarOrden($id_orden, $id_validador, $resultado, $comentarios) {
        $this->db->begin_transaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO ValidacionesOC
                    (id_oc, id_validador, resultado_validacion, comentarios_validacion, fecha_validacion)
                 VALUES (?, ?, ?, ?, NOW())"
            );
            if (!$stmt) throw new Exception($this->db->error);
            $stmt->bind_param("iiis", $id_orden, $id_validador, $resultado, $comentarios);
            if (!$stmt->execute()) throw new Exception($stmt->error);
            $stmt->close();

            $nuevoEstado = $resultado
                ? 'Aprobada'
                : 'Rechazada';
            $upd = $this->db->prepare(
                "UPDATE OrdenesCambio
                 SET estado_oc = ?
                 WHERE id_oc = ?"
            );
            if (!$upd) throw new Exception($this->db->error);
            $upd->bind_param("si", $nuevoEstado, $id_orden);
            if (!$upd->execute()) throw new Exception($upd->error);
            $upd->close();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("ValidacionOCModel::validarOrden - " . $e->getMessage());
            return false;
        }
    }
}
