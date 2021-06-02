<?php
class VerificacionMW
{
	public function VerificarToken($request, $handler)
    {  

		$response = $handler->handle($request);
		
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
		} 
        else 
        {
			$respuesta = "Por favor logueese para realizar esta accion";
		}
        
        if(isset($respuesta))
        {
			$nueva = $response->withJson($response, 401);
			return $nueva;
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