<?php

class Usuario
{
    public $id;
    public $nombre;
    public $usuario;
    public $clave;
    public $rol;
    public $estado;
    
    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $usuarioExistente = Usuario::obtenerUsuario($this->usuario);

        if(isset($usuarioExistente->id))
        {
            return false;
        }

        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (nombre, usuario, clave, rol, estado) SELECT :nombre, :usuario, :clave, :rol, 'activo'");
        
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);

        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':rol', $this->rol, PDO::PARAM_STR);
        
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
     
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, usuario, clave, rol, estado FROM usuarios WHERE estado <> 'eliminado'");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, usuario, clave, rol, estado FROM usuarios WHERE usuario = :usuario AND estado <> 'eliminado' LIMIT 1");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public function modificarUsuario()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave, nombre = :nombre WHERE id = :id AND estado <> 'eliminado'");
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public function borrarUsuario()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET estado = 'eliminado' WHERE id = :id and estado <> 'eliminado'");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public function toggleEstado()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET estado = CASE WHEN estado = 'suspendido' THEN 'activo' ELSE 'suspendido' END WHERE id = :id and estado <> 'eliminado'");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public static function verificarCredenciales($usuario, $clave)
    {
        $usuarioObtenido = Usuario::obtenerUsuario($usuario);

        if(isset($usuarioObtenido->id))
        {
            if(!isset($usuarioObtenido->fechaBaja))
            {
                return password_verify($clave, $usuarioObtenido->clave);
            }
        }
        return false;
    }  
}

?>