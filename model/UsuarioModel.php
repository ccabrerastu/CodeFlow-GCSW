<?php
require_once __DIR__ . '/../config/database.php';

class UsuarioModel {
    private $id_usuario;
    private $nombre_completo;
    private $nombre_usuario;
    private $contrasena_hash;
    private $email;
    private $activo;
    private $conexion;

    public function __construct() {
        try {
            $conexion_db = new Conexion();
            $this->conexion = $conexion_db->getConexion();
        } catch (Exception $e) {
            error_log("Error de conexión en UsuarioModel: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
        }
    }

    public function getIdUsuario() { return $this->id_usuario; }
    public function setIdUsuario($id_usuario) { $this->id_usuario = $id_usuario; }

    public function getNombreCompleto() { return $this->nombre_completo; }
    public function setNombreCompleto($nombre_completo) { $this->nombre_completo = $nombre_completo; }

    public function getNombreUsuario() { return $this->nombre_usuario; }
    public function setNombreUsuario($nombre_usuario) { $this->nombre_usuario = $nombre_usuario; }

    
    public function setContrasena($contrasena_plain) {
        $this->contrasena_hash = password_hash($contrasena_plain, PASSWORD_DEFAULT);
    }
    

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function isActivo() { return $this->activo; }
    public function setActivo($activo) { $this->activo = (bool)$activo; }



    public function verificarCredenciales($nombre_usuario, $clave_plain) {
        $stmt = $this->conexion->prepare("SELECT id_usuario, nombre_completo, nombre_usuario, contrasena_hash, email, activo FROM Usuarios WHERE nombre_usuario = ? AND activo = TRUE");
        if (!$stmt) {
            error_log("Error en prepare verificarCredenciales: " . $this->conexion->error);
            return null;
        }
        $stmt->bind_param("s", $nombre_usuario);
        if (!$stmt->execute()) {
            error_log("Error en execute verificarCredenciales: " . $stmt->error);
            $stmt->close();
            return null;
        }

        $resultado = $stmt->get_result();
        $usuarioData = $resultado->fetch_assoc();
        $stmt->close();

        if ($usuarioData && password_verify($clave_plain, $usuarioData['contrasena_hash'])) {
            // No devolver el hash de la contraseña
            unset($usuarioData['contrasena_hash']);
            return $usuarioData;
        }
        return null; // Usuario no encontrado, inactivo o contraseña incorrecta
    }


    public function agregarUsuario() {
        if (empty($this->contrasena_hash)) {
            error_log("Error: Contraseña no hasheada antes de intentar guardar el usuario.");
            return false;
        }

        $stmt = $this->conexion->prepare("INSERT INTO Usuarios (nombre_completo, nombre_usuario, contrasena_hash, email, activo) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            error_log("Error en prepare agregarUsuario: " . $this->conexion->error);
            return false;
        }

        $activo_db = $this->activo ?? true;

        $stmt->bind_param("ssssi",
            $this->nombre_completo,
            $this->nombre_usuario,
            $this->contrasena_hash,
            $this->email,
            $activo_db
        );

        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $stmt->close();
            return $new_id;
        } else {
            error_log("Error en execute agregarUsuario: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }


    public function findByNombreUsuario($nombre_usuario) {
        $sql = "SELECT id_usuario, nombre_completo, nombre_usuario, email, activo FROM Usuarios WHERE nombre_usuario = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) { error_log("Error prepare findByNombreUsuario: ".$this->conexion->error); return null; }
        $stmt->bind_param("s", $nombre_usuario);
        if (!$stmt->execute()) { error_log("Error execute findByNombreUsuario: ".$stmt->error); $stmt->close(); return null; }
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $resultado->free();
        $stmt->close();
        return $usuario;
    }


    public function findByEmail($email) {
        $sql = "SELECT id_usuario, nombre_completo, nombre_usuario, email, activo FROM Usuarios WHERE email = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) { error_log("Error prepare findByEmail: ".$this->conexion->error); return null; }
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) { error_log("Error execute findByEmail: ".$stmt->error); $stmt->close(); return null; }
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();
        $resultado->free();
        $stmt->close();
        return $usuario;
    }


    public function obtenerTodosLosUsuarios() {
        $conn = $this->conexion;
        $sql = "SELECT id_usuario, nombre_completo, nombre_usuario, email, activo FROM Usuarios ORDER BY nombre_completo ASC";
        $resultado = $conn->query($sql);
        $usuarios = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $usuarios[] = $fila;
            }
            $resultado->free();
        } else {
            error_log("Error en obtenerTodosLosUsuarios: " . $conn->error);
        }
        return $usuarios;
    }

}
?>