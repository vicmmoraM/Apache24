<?php
	// Inicia la sesión PHP para poder utilizar variables de sesión.
	header('Cache-Control: no cache'); //no cache
	session_cache_limiter('private_no_expire'); // works
	//session_cache_limiter('public'); // works too
	session_start();
	error_reporting(1);
	
	// Verifica si el usuario está logueado; si no, redirige a index.php.
	if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
	{
		header('Location: ../index.php');
		exit;
	}

	include '../class/DB.php';
	require_once '../dompdf/autoload.inc.php';
	use Dompdf\Dompdf;

	$fecha = date("Y-m-d");
	$resultado= '';
	$dataBase= new DB ();


	if (isset($_POST['cmb_tipoSuministro'])) 
	{
		
		$_SESSION['cmbSum']= $_POST['cmb_tipoSuministro'];
	}


	if (isset($_POST['agregar'])) 
	{
		$nombreSuministro= "";
		$tipoSuministro= "";
		$precioSuministro= "";
		$cadena= "";

		if (($_POST['slt_Cantidad'] != null) && ($_POST['cmb_suministro'] != 0) && ($_POST['cmb_tipoSuministro'] != 0))
		{
						
			$nombreSuministro= $dataBase->_getSuministro ($_POST['cmb_suministro']);
			$tipoSuministro= $dataBase->_getTipoSuministro ($_POST['cmb_tipoSuministro']);
			$precioSuministro= $dataBase->_getPrecioSuministro ($_POST['cmb_suministro']);

			$cadena= $_POST['cmb_suministro'] .",". $nombreSuministro['descripcion'] .",". $_POST['cmb_tipoSuministro'] .",". $tipoSuministro['descripcion'] .",". $_POST['slt_Cantidad'] .",". $precioSuministro['precio'] .",". number_format(($_POST['slt_Cantidad'] * $precioSuministro['precio']),2); 
			
			array_push($_SESSION['matrizPed'], $cadena);
		}

	}

	if (isset($_POST['X'])) 
	{
		//var_dump($_POST['X']);
		$partes= explode("    ",$_POST['X']);
		array_splice($_SESSION['matrizPed'], $partes[1], 1);
	}

	if (isset($_POST['cmb_PDVs'])) 
	{
		$limite= 0.00;
		$nombrePDV= '';
		
		if ($_POST['cmb_PDVs'] > 0)
		{
			$_SESSION['cmbPDV']= $_POST['cmb_PDVs'];
			//var_dump($_SESSION['cmbPDV']);
			$limite= $dataBase->_getLimitePDV ($_POST['cmb_PDVs']);
			$nombrePDV= $dataBase->_getPDV ($_POST['cmb_PDVs']);
			//var_dump($nombrePDV);
			$_SESSION['PDV']= $nombrePDV['descripcion'];
			$_SESSION['ciudadPDV']= $nombrePDV['ciudad'];
			$_SESSION['direccionPDV']= $nombrePDV['direccion'];
			$_SESSION['limitePOS']= $limite ['monto_autorizado'];			
		}
	}

	if (isset($_POST['rPedido'])) 
	{
		$codigoRegistro= 0;
		
		if (count($_SESSION['matrizPed']) > 0)
		{
			if ($_SESSION['totalPedido'] <= $_SESSION['limitePOS'])
			{
				
				
				$codigoRegistro = $dataBase->_setCabeceraPedidos($_SESSION['userlogin'], $_SESSION['cmbPDV'], $fecha);
				//var_dump($codigoRegistro);
				for ($indice=0; $indice < count($_SESSION['matrizPed']); $indice++) 
				{
					$separar= explode(",",$_SESSION['matrizPed'][$indice]);
					$dataBase->_setDetallePedidos($codigoRegistro,$separar[0],$separar[4],$separar[5]);	
					
				}
				
				$path = '../images/LOGO_OFICIAL_FC_COMPLETO_2.png';
				$type = pathinfo($path, PATHINFO_EXTENSION);
				$data = file_get_contents($path);
				$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
				
				$nombreCSV= 'pedidoSuministro_' .$_SESSION['PDV']. '_' .$fecha;
				$archivo_csv = fopen('../files/'.$nombreCSV. '.csv', 'w');
				
				if($archivo_csv)
				{
					fputs($archivo_csv, 'Solicitado por: ,' .htmlspecialchars($_SESSION['username']).PHP_EOL);
					fputs($archivo_csv, 'PDV: ,' .$_SESSION['PDV'].PHP_EOL);
					fputs($archivo_csv, 'Ciudad: ,' .$_SESSION['ciudadPDV'].PHP_EOL);
					fputs($archivo_csv, 'Dirección: ,' .$_SESSION['direccionPDV'].PHP_EOL);
					fputs($archivo_csv, PHP_EOL);
    				fputs($archivo_csv, 'Descripcion, Tipo de Suministro, Cantidad, Precio Unitario, Total' .PHP_EOL);  
				}
				else
				{
					echo "Error en la generación del CSV";
				}


				$html='<!DOCTYPE html>
						<html>
						<head>
							<link href="http://' .$_SERVER['HTTP_HOST']. '/css/styles_000.css" rel="stylesheet" type="text/css">
							<meta charset="UTF-8">
							<meta name="viewport" content="width=device-width, initial-scale=1.0">
							<meta http-equiv="X-UA-Compatible" content="ie=edge">
							<title>.:: Solicitud de Suministro ::.</title>
						</head>
						<body>
							<div class="css_tbl_marco_cabecera_2"></div>
							<div  align="center">
							<img src="' .$base64. '" width="203" height="136" alt=""/> </div>	
							<div>
								<table>
									<tr>
										<td width="100%" class="css_lbl_welcome"> </br>Solicitado por: &nbsp; </td>
										<td width="100%" class="row"> 
												</br>' .htmlspecialchars($_SESSION['username']). '
										</td>
									</tr>
								</table>
								<hr>
							</div>

							<div>
							<div>
								<div align="center" class="css_lbl_Suministros">
									Solicitud de Suministros
								</div>
							</div>
							</hr>

						<div>
							<div>
							  <table width="100%">
									<tr>
										<td width="70%">
											<table>
												<tr>
													<td class="css_lbl_welcome_2">
														Punto de Venta: 
													</td>
													<td class="row_2">
														' .$_SESSION['PDV']. ' 
													</td>
													<td>
													</td>

												</tr>
												<tr>
													<td class="css_lbl_welcome_2">
														Ciudad:
													</td>
													<td class="row_2">
														' .$_SESSION['ciudadPDV']. '
													</td>
													<td>
													</td>
												</tr>
												<tr>
													<td class="css_lbl_welcome_2">
														Dirección:
													</td>
													<td class="row_2" colspan="3">
														' .$_SESSION['direccionPDV']. '
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
										<td width="30%"  class="row">
											<p>
											<strong>Fecha: </strong>' .$fecha. '
											<br>
											</p>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<hr>								
					   <div class="factura">
							<div>
								<table align="center">
									<thead>
									<tr class="css_cab_factura_2">
										<th width="250" class="css_tbl_bordeFact_1">Descripción</th>
										<th width="70" class="css_tbl_bordeFact_1">Tipo Suministro</th>
										<th width="60" class="css_tbl_bordeFact_1">Cantidad</th>
										<th width="60" class="css_tbl_bordeFact_1">Precio unitario</th>
										<th width="65" class="css_tbl_bordeFact_1">Total</th>
									</tr>
									</thead>
									<tbody>
									';

									$subtotalSO = 0;
									$subtotalSL = 0;
									
									for ($indice=0; $indice < count($_SESSION['matrizPed']); $indice++) 
									{
										$separar= explode(",",$_SESSION['matrizPed'][$indice]);
										$totalProducto = floatval($separar[6]);
										if ($separar[2] == 1)
										{
											$subtotalSO += $totalProducto;	
										}
										else
										{
											$subtotalSL += $totalProducto;
										}

										$html .='

										<tr>
											<td align="left" class="css_tbl_bordeFact_3">' .$separar[1]. '</td>
											<td align="center" class="css_tbl_bordeFact_3">' .$separar[3]. '</td>
											<td align="center" class="css_tbl_bordeFact_3">' .$separar[4]. '</td>
											<td align="right" class="css_tbl_bordeFact_3">$' .$separar[5]. '</td>
											<td align="right" class="css_tbl_bordeFact_3">$' .$separar[6]. '</td>
											<td></td>
										</tr>';
										
										fputs($archivo_csv, $separar[1].','.$separar[3].','.$separar[4].','.$separar[5].','.$separar[6].PHP_EOL);
									}
									
									fputs($archivo_csv, PHP_EOL);
									fputs($archivo_csv, ',,,Total S. Oficina: ,'.number_format($subtotalSO, 2).PHP_EOL);
									fputs($archivo_csv, ',,,Total S. Limpieza: ,'.number_format($subtotalSL, 2).PHP_EOL);
									fputs($archivo_csv, PHP_EOL);
									fputs($archivo_csv, ',,,Total: ,'.number_format($_SESSION['totalPedido'], 2).PHP_EOL);
				
									fclose($archivo_csv);
				
									$html .='
									</tbody>
									<tfoot>
									<tr>
										<td colspan="4" align="right" class="css_lbl_Total_2">
											Total S. Oficina</td>
										<td align="right" class="css_lbl_Total_2">
											$' .number_format($subtotalSO, 2). '
										</td>
										<td>
										</td>
									</tr>
									<tr>
										<td colspan="4" align="right" class="css_lbl_Total_2">
											Total S. Limpieza</td>
										<td align="right" class="css_lbl_Total_2">
											$'  .number_format($subtotalSL, 2). '
										</td>
										<td>
										</td>
									</tr>
									<tr>
										<td colspan="4" align="right" class="css_lbl_Total">
											<p id="blink">Total ';

														if ($_SESSION['totalPedido'] > $_SESSION['limitePOS'])
														{
															$html.= 'EXCEDIDO';
														}
										$html .= '</p></td>
										<td align="right">
											'; 						
													if ($_SESSION['totalPedido'] > $_SESSION['limitePOS'])
													{
														$html .= '<p class="css_valorTotal_2">';
													}
													else
													{
														$html .= '<p class="css_valorTotal_1">';
													}

												$html .= "$" .number_format($_SESSION['totalPedido'], 2). "</p>
										</td>
										<td>
										</td>
									</tr>
									</tfoot>
								</table>
							</div>
						</div>
						</div>
						<div class='css_tbl_marco_pie_2'></div>

					</body>";

					//$dompdf = new Dompdf();
				//$dompdf = new Dompdf(['enable_remote' =>true]);
				
				//$dompdf->set_paper("A4", "portrait");
					//$dompdf->set_paper('A4', 'landscape');
				//$dompdf->loadHtml($html);
				//$dompdf->render();
				//$contenidoPDF = $dompdf->output();
				$nombrePDF= 'pedidoSuministro_' .$_SESSION['PDV']. '_' .$fecha;
				
				//file_put_contents('../files/'.$nombrePDF. '.pdf', $contenidoPDF);
					//header("Content-type: application/pdf");
					//header("Content-Disposition: inline; filename=pedidoSuministro.pdf");

					//$dompdf->stream('pedidoSuministro_' .$_SESSION['PDV']. '_' .$fecha. '.pdf');
				
				$_SESSION['file']= $nombrePDF;
				//echo '<script> location.replace("notificacionSolicitud.php"); </script>';
				header('Location: notificacionSolicitud.php');
				exit;
				
			}					
		}
	}

	// Cerrar sesión y finalizar conexión LDAP si se solicita.
	if (isset($_POST['logout'])) 
	{
		// Cerrar la sesión.
		unset($dataBase);
		session_unset(); // Eliminar las variables de sesión.
		session_destroy(); // Destruir la sesión.

		// Redirigir al usuario a la página de inicio de sesión.
		header('Location: ../index.php');
		exit;
	}


?>



<!DOCTYPE html>
<html>
<head>
	<link href="../css/styles_000.css" rel="stylesheet" type="text/css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>.:: Solicitud de Suministro ::.</title>
</head>
<body>
	<div class="css_tbl_marco_cabecera_2"></div>
	<div  align="center">
    <img src="../images/LOGO_OFICIAL_FC_COMPLETO_2.png" width="203" height="136" alt=""/> </div>	
	<div>
		<table>
			<tr>
				<td width="100%" class="css_lbl_welcome"> Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?>!</td>
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
            Solicitud de Suministros
        </div>
    </div>
    <hr>
    <div>
        <div>
		  <table width="100%">
				<tr>
					<td width="84%">
						<table>
							<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
							<tr>
								<td class="css_lbl_welcome">
									Punto de Venta:
								</td>
								<td>
									<select class="css_cbx_1" name="cmb_PDVs" onChange="submit ();">
										<option value="0">Seleccionar...</option>
										<?php
											   $resultado= $dataBase->_getPDVs();
											   while ($valores = $resultado->fetch_assoc()) 
											   {
												   if ($_SESSION['cmbPDV'] == $valores['codigo'])
												   {
														echo "<option value=" . $valores['codigo'] . " selected>" . $valores['descripcion'] . "</option>";
												   }
												   else
												   {
													   echo "<option value=" . $valores['codigo'] . " >" . $valores['descripcion'] . "</option>";
												   }
												} 
										?>
									</select>
								</td>
								<td colspan="3" class="row_2">
									<strong><?php
												if ($_SESSION['cmbPDV'] > 0)
												{
													echo "[ CUPO ASIGNADO: $ " .$_SESSION['limitePOS']. " ]";	
												}
										?> 
									</strong>
								</td>
							</tr>
							<tr>
								<td class="css_lbl_welcome">
									Tipo de Suministro:
								</td>
								<td>
									<select class="css_cbx_1" name="cmb_tipoSuministro" onChange="submit ();">
										<option value="0">Seleccionar...</option>
										<?php
											   $resultado= $dataBase->_getTipoSuministros();
											   while ($valores = $resultado->fetch_assoc()) 
											   {
												   if ($_SESSION['cmbSum'] == $valores['codigo'])
												   {
														echo "<option value=" . $valores['codigo'] . " selected>" . $valores['descripcion'] . "</option>";
												   }
												   else
												   {
													   echo "<option value=" . $valores['codigo'] . " >" . $valores['descripcion'] . "</option>";
												   }
												} 
										?>
									</select>
								</td>
								<td>
								</td>
							</tr>
							<tr>
								<td class="css_lbl_welcome">
									Suministro:
								</td>
								<td>
									<select class="css_cbx_1" name="cmb_suministro" id="cmb_suministro" >
										<option value="0">Seleccionar...</option>
										<?php
											$resultado= $dataBase->_getSuministros($_SESSION['cmbSum']);
											if ($_SESSION['cmbSum'] != 0)
											{
											   while ($valores = $resultado->fetch_assoc()) 
											   {
													echo "<option value=" . $valores['codigo'] . ">" . $valores['descripcion'] . "</option>";
											   }
											}
										?>
									</select>
								</td>
								<td class="css_lbl_welcome">
									Cantidad:
								</td>
								<td>
									<input class="css_tbx_general_2" type="number" min="1" max="10" name="slt_Cantidad">
								</td>
								<td>
									<input type="submit" name="agregar" value="Agregar al Pedido">
									
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
    <hr>
    <div class="factura">
        <div>
            <table align="center">
                <thead>
                <tr class="css_cab_factura">
                    <th width="550" class="css_tbl_bordeFact_1">Descripción</th>
					<th width="180" class="css_tbl_bordeFact_1">Tipo Suministro</th>
                    <th width="100" class="css_tbl_bordeFact_1">Cantidad</th>
                    <th width="180" class="css_tbl_bordeFact_1">Precio unitario</th>
                    <th width="150" class="css_tbl_bordeFact_1">Total</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $subtotal = 0;
				$subtotalSO = 0;
				$subtotalSL = 0;
				$fila= 0;
                for ($indice=0; $indice < count($_SESSION['matrizPed']); $indice++) 
				{
					$separar= explode(",",$_SESSION['matrizPed'][$indice]);
					$totalProducto = floatval($separar[6]);
                    $subtotal += $totalProducto;
					if ($separar[2] == 1)
					{
						$subtotalSO += $totalProducto;	
					}
					else
					{
						$subtotalSL += $totalProducto;
					}
                    ?>
                    <tr>
                        <td align="left" class="css_tbl_bordeFact_2"><?php echo htmlspecialchars($separar[1]); ?></td>
						<td align="center" class="css_tbl_bordeFact_2"><?php echo $separar[3]; ?></td>
                        <td align="center" class="css_tbl_bordeFact_2"><?php echo $separar[4]; ?></td>
                        <td align="right" class="css_tbl_bordeFact_2">$<?php echo $separar[5]; ?></td>
                        <td align="right" class="css_tbl_bordeFact_2">$<?php echo $separar[6]; ?></td>
						<td><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"><input type="submit" name="X" value="<?php echo "X    " .$fila; ?>" class="css_btn_Eliminar"></form></td>
                    </tr>
                <?php 
					$fila++;	
				}
                $total = $subtotal;
				$_SESSION['totalPedido'] = $total;
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="4" align="right" class="css_lbl_Total">
                        Total S. Oficina</td>
                    <td align="right" class="css_lbl_Total">
                        $<?php echo number_format($subtotalSO, 2) ?>
                    </td>
					<td>
					</td>
                </tr>
                <tr>
                    <td colspan="4" align="right" class="css_lbl_Total">
                        Total S. Limpieza</td>
                    <td align="right" class="css_lbl_Total">
                        $<?php echo number_format($subtotalSL, 2) ?>
                    </td>
					<td>
					</td>
                </tr>
				<tr>
                    <td colspan="4" align="right" class="css_lbl_Total">
                        <p id="blink">Total <?php
									if ($total > $_SESSION['limitePOS'])
									{
										echo "EXCEDIDO";
										echo "<script type='text/javascript'> 
        										var blink = 
            									document.getElementById('blink'); 
 
        										setInterval(function () { 
            										blink.style.opacity = 
            										(blink.style.opacity == 0 ? 1 : 0); 
        										}, 1000); 
    										</script> ";
									}
						?></p></td>
                    <td align="right">
                        <?php 						
								if ($total > $_SESSION['limitePOS'])
								{
									echo "<p class='css_valorTotal_2'>";
								}
								else
								{
									echo "<p class='css_valorTotal_1'>";
								}
								echo "$" .number_format($total, 2). "</p>";
						?>
                    </td>
					<td>
					</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div>
		<table align="right">
			<tr>
				<td>
					    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        				<input class="css_btn_Pedido" type="submit" name="rPedido" value="Realizar Pedido">
						</form>
				</td>
			</tr>
		</table>
    </div>
</div>
<div class="css_tbl_marco_pie_2"></div>
</body>
</html>
