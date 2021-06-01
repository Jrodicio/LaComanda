<?php
class VerificacionMW
{
	public function VerificarToken($request = null, $response = null, $args = null)
    {  
		var_dump($request);
		var_dump($response);
		var_dump($args);
		$header = $request->getHeaderLine('Authorization');
		$token = trim(explode("Bearer", $header)[1]);

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
			$response = $next($request, $response);
		} 
        else 
        {

		}

        return $response;
	}

	public function VerificarAdmin($request, $handler)
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

	public function VerificarEmpleado($request, $handler)
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

	public function VerificarMozo($request, $handler)
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