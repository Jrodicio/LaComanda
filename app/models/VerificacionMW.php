<?php
class VerificacionMW
{
	public static function VerificarToken($request, $response, $next)
    {  
		$objResponse = new stdclass();
		$objResponse->respuesta = "";
		$arrayConToken = $request->getHeader('token');
		$token = $arrayConToken[0];
		
		try 
        {
			AuthJWT::verificarToken($token);
			$objResponse->esValido = true;
		} 
        catch (Exception $e) 
        {
			$objResponse->excepcion = $e->getMessage();
			$objResponse->esValido = false;
		}
		
		if($objResponse->esValido)
        {
			$payload = AuthJWT::ObtenerData($token);
			$request = $request->withAttribute('usuario', $payload);
			$response = $next($request, $response);
		} 
        else 
        {
			$objResponse->respuesta = "Por favor logueese para realizar esta accion";
			$objResponse->elToken = $token;
		}
        
        if($objResponse->respuesta != "") 
        {
			$nueva = $response->withJson($objResponse, 401);
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