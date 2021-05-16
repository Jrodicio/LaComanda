<?php

class Mesa
{
    public $id;
    public $descripcion;
    public $tipo;
    public $rolResponsable;
    public $precio;
    public $estado;
    
    public function crear()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $mesaExistente = Mesa::obtenerUno($this->descripcion);

        if(isset($mesaExistente->id))
        {
            return false;
        }

        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (descripcion, tipo, rolResponsable, precio, estado) 
                                                            SELECT :descripcion, :tipo, :rolResponsable, :precio, 'disponible'");
        
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':rolResponsable', $this->rolResponsable, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        
        $consulta->execute();
        return $objAccesoDatos->obtenerUltimoId();
     
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, tipo, rolResponsable, precio, estado FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerRol($rolResponsable)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, tipo, rolResponsable, precio, estado FROM mesas WHERE rolResponsable = :rolResponsable");
        $consulta->bindValue(':rolResponsable', $rolResponsable, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerTipo($tipo)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, tipo, rolResponsable, precio, estado FROM mesas WHERE tipo = :tipo");
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerUno($descripcion)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, descripcion, tipo, rolResponsable, precio, estado FROM mesas WHERE descripcion = :descripcion AND estado <> 'eliminado' LIMIT 1");
        $consulta->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public function modificar()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET descripcion = :descripcion, tipo = :tipo, rolResponsable = :rolResponsable, precio = :precio WHERE id = :id AND estado <> 'eliminado'");
        $consulta->bindValue(':descripcion', $this->descripcion, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':rolResponsable', $this->rolResponsable, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->rowCount();
    }

    public static function borrar($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = 'eliminado' WHERE id = :id AND estado <> 'eliminado'");

        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->rowCount();
    }

    public function toggleEstado()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = CASE WHEN estado = 'disponible' THEN 'no disponible' ELSE 'disponible' END WHERE id = :id and estado <> 'eliminado'");

        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        
        return $consulta->rowCount();
    } 

    public function esValido()
    {
        return (strlen($this->descripcion) > 3 && in_array($this->tipo,array("bebida","comida")) && in_array($this->rolResponsable,array("bartender","cervecero","cocinero","mozo","socio")) && floatval($this->precio) > 0);
    }
}

?>