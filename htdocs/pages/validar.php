<?php
	// Inicia la sesión PHP para poder utilizar variables de sesión.
	header('Cache-Control: no cache'); //no cache
	session_cache_limiter('private_no_expire'); // works
	//session_cache_limiter('public'); // works too
	session_start();

	// Verifica si el usuario ya está logueado y lo redirige a home.php.
	if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) 
	{
    	header('Location: home.php', true, 301);
    	exit;
	}
	else
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST') 
		{
			try
			{
				if ($_POST['cmb_Departamento'] > 0)
				{
					include "../class/conexion.php";
					include "../class/DB.php";

					$resultado= '';
					$codigo= 0;

					$vConexion= new Conexion ();
					$dataBase= new DB ();

					$resultado= $dataBase->_getDepartamento($_POST['cmb_Departamento']);
					$codigo = $resultado->fetch_assoc();
					//var_dump($codigo);	
					$vConexion->autenticar($_POST['username'], $_POST['password'], $codigo['descripcion'], $_POST['cmb_Departamento']);

					unset($dataBase);
					unset($vConexion);					
				}
				else
				{
					$_SESSION['mensajes']= 'Seleccione su departamento!';
					echo '<script> location.replace("../index.php"); </script>';
				}
				
			}
			catch (Exception $e) 
			{
    			die("Could not connect " . $e->getMessage());
			}	

		} 
		else 
		{
			die('Error de conexión con el Directorio Activo.');
		}
	}
?>