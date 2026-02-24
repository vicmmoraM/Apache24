<?php
	// Inicia la sesión PHP para poder utilizar variables de sesión.
	header('Cache-Control: no cache'); //no cache
	session_cache_limiter('private_no_expire'); // works
	//session_cache_limiter('public'); // works too	
	session_start();

	// Verifica si el usuario ya está logueado y lo redirige a home.php.
	if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) 
	{
    	header('Location: pages/home.php', true, 301);
    	exit;
	}

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>.:: Cadena de Suministros ::.</title>
<link href="css/styles_000.css" rel="stylesheet" type="text/css">
</head>

<body >
	<div class="css_tbl_marco_cabecera">
	  <table width="100%" border="0">
	    <tbody>
	      <tr>
	        <td><img src="images/LOGO_OFICIAL_FC_COMPLETO.png" width="480" height="123" alt=""/></td>
          </tr>
        </tbody>
      </table>
	</div>
<div class="css_tbl_marco" id="marco" align="center">
		<form action="pages/validar.php" method="post">
			<table class="css_tbl_login">
				<tr height="10"></tr>
				<tr>
    				<td width="10"></td>
					<td width="65" class="css_lbl_Usuario">Usuario:</td> 
					<td><input maxlength="45" class="css_tbx_general" type="text" name="username" required></td>
					<td width="10"></td>
				</tr>
				<tr>
    				<td></td>
					<td class="css_lbl_Usuario">Clave:</td> 
					<td width="65"><input maxlength="25" class="css_tbx_general" type="password" name="password" required></td>
					<td></td>
				</tr>
				<tr>
    				<td></td>
					<td class="css_lbl_Usuario">Departamento:</td> 
					<td width="65"><select class="css_cbx_1" name="cmb_Departamento">
						<option value="0">Seleccionar...</option>
						<?php
								include "class/DB.php";

								$resultado= '';
								
								$dataBase= new DB ();
							    
						        $resultado= $dataBase->_getDepartamentos();
						
							   while ($valores = $resultado->fetch_assoc()) 
							   {
									   echo "<option value=" . $valores['codigo'] . " >" . $valores['descripcion'] . "</option>";
							   } 
							
							    unset($dataBase);
						?>
						</select>
					</td>
					<td></td>
				</tr>
				<tr align="center" valign="middle">
					<td></td>
					<td></td>					
					<td height="40" align="center" valign="middle"><input class="css_btn_Inicio" type="submit" value="Iniciar sesión"></td>
					<td></td>
				</tr>	
				<tr height="5"></tr>
			</table>
		</form>
		<table>
			<tr><td>&nbsp;<br/></td></tr>
			<tr ><td class="css_lbl_mensajes"><p id="lbl_mensaje"><?php 
				echo $_SESSION['mensajes']; 
				echo "<script type='text/javascript'> 
						var blink = 
						document.getElementById('lbl_mensaje'); 

						setInterval(function () { 
							blink.style.opacity = 
							(blink.style.opacity == 0 ? 1 : 0); 
						}, 1000); 
					</script> ";
				?></p></td></tr>
		</table>
	</div>
	<div class="css_tbl_marco_pie"></div>
</body>
</html>