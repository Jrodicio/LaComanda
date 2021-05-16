<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['nombre'],$parametros['usuario'],$parametros['clave'],$parametros['rol']))
        {
            $nombre = $parametros['nombre'];
            $usuario = $parametros['usuario'];
            $clave = $parametros['clave'];
            $rol = $parametros['rol'];
            $retorno = false;

            if(strlen($nombre) > 3 && strlen($usuario) > 6 && strlen($clave) > 6 && in_array($rol,array("bartender","cervecero","cocinero","mozo","socio")))
            {
                // Creamos el usuario
                $usr = new Usuario();
                $usr->nombre = $nombre;
                $usr->usuario = $usuario;
                $usr->clave = $clave;
                $usr->rol = $rol;
                    
                $retorno = $usr->crearUsuario();    
            }

            if($retorno)
            {
                $mensaje = "Usuario $retorno creado con exito";
                $usr->id = $retorno;
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
        if(isset($args['usuario']))
        {
            $usr = $args['usuario'];
            $usuario = Usuario::obtenerUsuario($usr);
            $payload = json_encode($usuario);
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
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        if(isset($parametros['id'],$parametros['usuario'],$parametros['nombre'],$parametros['clave'],$parametros['rol']))
        {
            $id = $parametros['id'];
            $usuario = $parametros['usuario'];
            $nombre = $parametros['nombre'];
            $clave = $parametros['clave'];
            $rol = $parametros['rol'];
    
            $usr = new Usuario();
            $usr->id = $id;
            $usr->usuario = $usuario;
            $usr->clave = $clave;
            $usr->nombre = $nombre;
            $usr->rol = $rol;

            if ($usr->modificarUsuario()) 
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