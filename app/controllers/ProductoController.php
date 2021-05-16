<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['descripcion'],$parametros['tipo'],$parametros['rolResponsable'],$parametros['precio']))
        {
            $descripcion = $parametros['descripcion'];
            $tipo = $parametros['tipo'];
            $rolResponsable = $parametros['rolResponsable'];
            $precio = $parametros['precio'];
            $retorno = false;

            // Creamos el producto
            $producto = new Producto();
            $producto->descripcion = $descripcion;
            $producto->tipo = $tipo;
            $producto->rolResponsable = $rolResponsable;
            $producto->precio = $precio;

            if($producto->esValido())
            {
                $retorno = $producto->crear();
            }

            if($retorno)
            {
                $mensaje = "Producto $retorno creado con exito";
                $producto->id = $retorno;
            }
            else
            {
                $mensaje = "Error";
            }

        }   
        else
        {
            $mensaje = "Faltan datos";
        }
        
        $payload = json_encode(array("mensaje" => $mensaje));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        if(isset($args['descripcion']))
        {
            $descripcion = $args['descripcion'];
            $producto = Producto::obtenerUno($descripcion);
            $payload = json_encode($producto);
        }
        else
        {
            $payload = json_encode(array("mensaje" => "Faltan datos"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        if(isset($args['rolResponsable']))
        {
            $lista = Producto::obtenerRol($args['rolResponsable']);
        }
        elseif(isset($arg['tipo']))
        {
            $lista = Producto::obtenerTipo($args['tipo']);
        }
        else
        {
            $lista = Usuario::obtenerTodos();
        }
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['id'],$parametros['descripcion'],$parametros['tipo'],$parametros['rolResponsable'],$parametros['precio']))
        {
            $id = $parametros['id'];
            $descripcion = $parametros['descripcion'];
            $tipo = $parametros['tipo'];
            $rolResponsable = $parametros['rolResponsable'];
            $precio = $parametros['precio'];
    
            $producto = new Producto();
            $producto->id = $id;
            $producto->usuario = $usuario;
            $producto->clave = $clave;
            $producto->nombre = $nombre;
            $producto->rol = $rol;

            if ($producto->modificar()) 
            {
                $mensaje = "Se actualizó el usuario";
            }
            else
            {
                $mensaje = "No se pudo actualizar el usuario";
            }
        }
        else
        {
            $mensaje = "Faltan datos [id-usuario-nombre-clave]";
        }

        $payload = json_encode(array("mensaje" => $mensaje));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['usuarioId']))
        {
            $usuarioId = $parametros['usuarioId'];
            $borrados = Usuario::borrarUsuario($usuarioId);
            
            if($borrados == 1)
            {
                $mensaje = "Usuario borrado con exito";
            }
            else if ($borrados == 0)
            {
                $mensaje = "No se encontró usuario que borrar";
            }
            else
            {
                $mensaje = "Se borro mas de un usuario, CORRE";
            }
        }
        else
        {
          $mensaje = "Faltan datos";
        }

        $payload = json_encode(array("mensaje" => $mensaje));
        
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function Loguear($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];

        if(Usuario::verificarCredenciales($usuario, $clave))
        {
          $payload = json_encode(array("mensaje" => "Usuario logueado con exito."));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Usuario o clave incorrecto."));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>