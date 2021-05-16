<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
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

            // Creamos la mesa
            $mesa = new Mesa();
            $mesa->descripcion = $descripcion;
            $mesa->tipo = $tipo;
            $mesa->rolResponsable = $rolResponsable;
            $mesa->precio = $precio;

            if($mesa->esValido())
            {
                $retorno = $mesa->crear();
            }

            if($retorno)
            {
                $mensaje = "Mesa $retorno creada con exito";
                $mesa->id = $retorno;
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
            $mesa = Mesa::obtenerUno($descripcion);
            $payload = json_encode($mesa);
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
            $lista = Mesa::obtenerRol($args['rolResponsable']);
        }
        elseif(isset($arg['tipo']))
        {
            $lista = Mesa::obtenerTipo($args['tipo']);
        }
        else
        {
            $lista = Mesa::obtenerTodos();
        }
        $payload = json_encode(array("listaMesa" => $lista));

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
    
            $mesa = new Mesa();
            $mesa->id = $id;
            $mesa->descripcion = $descripcion;
            $mesa->tipo = $tipo;
            $mesa->rolResponsable = $rolResponsable;
            $mesa->precio = $precio;

            if ($mesa->modificar()) 
            {
                $mensaje = "Se actualizó la mesa";
            }
            else
            {
                $mensaje = "No se pudo actualizar la mesa";
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

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        if(isset($parametros['mesaId']))
        {
            $mesaId = $parametros['mesaId'];
            $borrados = Mesa::borrar($mesaId);
            
            switch ($borrados) {
                case 0:
                    $mensaje = "No se encontró mesa que borrar";
                    break;
                case 1:
                    $mensaje = "Mesa borrado con exito";
                    break;
                default:
                    $mensaje = "Se borro mas de un mesa, CORRE";
                    break;
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
}
?>