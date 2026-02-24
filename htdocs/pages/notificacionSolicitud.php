<?php
	// Inicia la sesión PHP para poder utilizar variables de sesión.
	header('Cache-Control: no cache'); //no cache
	session_cache_limiter('private_no_expire'); // works
	//session_cache_limiter('public'); // works too
	session_start();
	
	// Verifica si el usuario está logueado; si no, redirige a index.php.
	if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
	{
		header('Location: ../index.php');
		exit;
	}

	require "../phpmailer/PHPMailer.php";
	require "../phpmailer/Exception.php";
	require "../phpmailer/SMTP.php";


	use PHPMailer\PHPMailer\PHPMailer;

	$fecha = date("Y-m-d");

	// Cerrar sesión y finalizar conexión LDAP si se solicita.
	if (isset($_POST['logout'])) 
	{
		// Cerrar la sesión.
		//unset($dataBase);
		session_unset(); // Eliminar las variables de sesión.
		session_destroy(); // Destruir la sesión.

		// Redirigir al usuario a la página de inicio de sesión.
		header('Location: ../index.php');
		exit;
	}

   try
   {
	   $mail = new PHPMailer;
	   $mail->isSMTP();
	   //$mail->SMTPDebug = 2;
	   $mail->SMTPDebug = 0;
	   $mail->Host = 'chimborazo.ecuahosting.net';
	   $mail->Port = 465;
	   $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
	   $mail->SMTPAuth = true;
	   $mail->Username = 'reqsuministros@farmcorp.com.ec';
	   $mail->Password = 'Sumin!str0sFC024';
	   $mail->setFrom("reqsuministros@farmcorp.com.ec", "Solicitud de Suministros");
	   $mail->addReplyTo('alfonso.macias@farmcorp.com.ec', '');
	   $mail->addAddress("alfonso.macias@farmcorp.com.ec", "");
	   $mail->Subject = 'Pedido de Suministro';
	   $response= "<font face='verdana' size='3'>Hola, </br></br> Tienes un nuevo pedido de suministro por atender. </br></br> ";

	   //$mail->Body = $response;
	   if (file_exists('../files/' .$_SESSION["file"]. '.csv'))
	   {
		   //$mail->addAttachment('../files/' .$_SESSION["file"]. '.pdf');
		   $mail->addAttachment('../files/' .$_SESSION["file"]. '.csv');
		   $response.="Se adjunta la solicitud en formato CSV. ";
	   }

		$response .= "</br></br></br> <strong>Atentamente,</strong></br></br>Sistema de Pedidos. </br></br></br> PD: Este mensaje ha sido generado por un sistema automatico, favor no responder.</font>";

	   $mail->msgHTML($response);

	   //var_dump($mail);
	   
   }
   catch (Exception $e) 
   {
		die("Could format Email.");
   }

?>

<!DOCTYPE html>
<html>
<head>
	<link href="../css/styles_000.css" rel="stylesheet" type="text/css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>.:: Notificación de Solicitud ::.</title>
</head>
<body>
	<div class="css_tbl_marco_cabecera_2"></div>
	<div  align="center">
    <img src="../images/LOGO_OFICIAL_FC_COMPLETO_2.png" width="203" height="136" alt=""/> </div>	
	<div>
		<table>
			<tr>
				<td width="100%" class="css_lbl_welcome"> Gracias, <?php echo htmlspecialchars($_SESSION['username']); ?>!</td>
				<td width="100%"> <!-- Formulario para cerrar sesión -->
    					<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        				<input class="css_btn_Salir" type="submit" name="logout" value="Cerrar Sesión">
						</form>
				</td>
			</tr>
		</table>
		<hr>
	</div>
	
	<div>
    <div>
        <div align="center" class="css_lbl_Suministros">
		<?php 
			try
			{
			   if (!$mail->send()) 
			   {
				   //echo 'Mailer Error: -> ' . $mail->ErrorInfo;
				   echo 'Su requerimiento NO ha sido enviado.';

			   } 
			   else 
			   {
				  echo 'Su requerimiento ha sido enviado.';
				  if (file_exists('../files/' .$_SESSION["file"]. '.csv'))
				   {
						//unlink ('../files/' .$_SESSION["file"]. '.pdf');
					    unlink ('../files/' .$_SESSION["file"]. '.csv');
				   }
			   }				
			}
		   catch (Exception $e) 
		   {
				die("Could send Email.");
		   }
		?>
        </div>
    </div>
    <hr>
    <div>
        <div>
		  <table width="100%">
				<tr>
					<td width="84%">
						<table>
							<tr>
								<td class="css_lbl_welcome">									
								</td>
								<td>
								</td>
								<td>
								</td>
								
							</tr>
							<tr>
								<td class="css_lbl_welcome">
								</td>
								<td>
								</td>
								<td>
								</td>
							</tr>
							<tr>
								<td class="css_lbl_welcome">
								</td>
								<td>
								</td>
								<td class="css_lbl_welcome">
								</td>
								<td>
								</td>
								<td>
							  </td>
							</tr>
						  </form>
					  </table>
					</td>
					<td width="16%"  class="row">
						<p>
						<strong>Fecha: </strong><?php echo $fecha; ?>
						<br>
						</p>
					</td>
				</tr>
			</table>
        </div>
    </div>
 
</div>
<div class="css_tbl_marco_pie_2"></div>
</body>
</html>