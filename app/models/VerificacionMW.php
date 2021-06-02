<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require_once __DIR__ . '/../vendor/autoload.php';

class VerificacionMW
{
	
	public function VerificarToken($request, $handler)
    {  
		$arrayConToken = $request->getHeader('token');
		$token = $arrayConToken[0];
		
		try 
        {
			AuthJWT::verificarToken($token);
			$esValido = true;
		} 
        catch (Exception $e) 
        {
			$esValido = false;
		}
		
		if($esValido)
        {
			$payload = AuthJWT::ObtenerData($token);
			$request = $request->withAttribute('usuario', $payload);
			$response = $handler->handle($request);
		} 
        else 
        {
			$response = new Response();
			$response->getBody()->write("Por favor logueese para realizar esta accion");
		}
        		
        return $response;
	}

	public function VerificarAdmin($request, $response, $next) 
    {
		$objResponse = new stdclass();
		$objResponse->respuesta = "";
		$rol = $request->getAttribute('usuario')->rol;

		if($rol == "socio")
        {
			$response = $next($request, $response);
		}
		else
		{
			$objResponse->respuesta = "Ud no tiene permisos para realizar esta acción";
		}
        
        if($objResponse->respuesta != "")
        {
			$nueva = $response->withJson($objResponse, 401);
			return $nueva;
        }

        return $response;
	}

	public function VerificarEmpleado($request, $response, $next)
    {
		$objResponse = new stdclass();
		$objResponse->respuesta = "";
		$rol = $request->getAttribute('usuario')->rol;
		if(in_array($rol,array("bartender","cervecero","cocinero","mozo","socio"))) 
        {
			$response = $next($request, $response);
		}
		else
		{
			$objResponse->respuesta = "Solo habilitado para usuarios";
		}
        
        if($objResponse->respuesta != "") {
			$nueva = $response->withJson($objResponse, 401);
			return $nueva;
        }

        return $response;
	}

	public function VerificarMozo($request, $response, $next)
    {
		$objResponse = new stdclass();
		$objResponse->respuesta = "";
		$rol = $request->getAttribute('usuario')->rol;
		if($rol == "mozo" || $rol == "socio")
        {
			$response = $next($request, $response);
		}
		else
		{
			$objResponse->respuesta = "Solo habilitado para mozos";
		}
        
        if($objResponse->respuesta != "") {
			$nueva = $response->withJson($objResponse, 401);
			return $nueva;
        }

        return $response;
	}
}

?>