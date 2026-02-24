<?php

	class Conexion
	{
		private $ldap_dn = 			'';
		private $ldap_password = 	'';
		private $ldap_con=			'';
		
		public function __construct ()
		{
			try
			{
				error_reporting(0);
				
				$this->ldap_dn = 			'src_TI_ldap@farmcorp.com.ec';
				$this->ldap_password = 		'Farmc0rp*';
				$this->ldap_con = 			ldap_connect("LDAP://SRVDCR000.farmcorp.com.ec") or die("Could not connect to HOST");
				//putenv('LDAPTLS_REQCERT=allow'); //require, never, allow
				//ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
				//$this->ldap_con = 			ldap_connect("LDAPS://SRVDCR000.farmcorp.com.ec") or die("Could not connect to HOST");
				//var_dump($this->ldap_con);
				ldap_set_option($this->ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
				
				if (!ldap_bind($this->ldap_con, $this->ldap_dn, $this->ldap_password)) 
				{
					//echo 'Usuario y clave incorrecto.';
					echo '<script> location.replace("../index.php"); </script>';
				} 				
			}
			catch (Exception $e) 
			{
				//echo "Mensaje : ";
    			die("Could not connect to AD " . $e->getMessage());
				//die("Could not connect to AD ");
			}	
		}
		
		public function autenticar ($usuario,$clave,$departamento,$id_departamento)
		{
			try
			{
				$user_dn =		"";
				$arreglo=		"";
				$name = 		"";
				$user = 		$usuario;
				$password = 	$clave;
				$this->ldap_dn ="ou=".$departamento.",ou=usuarios,ou=farmcorp,dc=farmcorp,dc=com,dc=ec";
				$filter = 	"(sAMAccountName=$user)";
				$result = 	ldap_search($this->ldap_con, $this->ldap_dn, $filter);
				$entries = 	ldap_get_entries($this->ldap_con, $result);
				//var_dump ($entries);
				
				if ($entries['count'] > 0) 
				{
					$user_dn= $entries[0]['dn'];
					$arreglo= explode(',', $user_dn);
					$name= explode('=', $arreglo[0]);
					//var_dump ($name);

					if (@ldap_bind($this->ldap_con, $user_dn, $password)) 
					{
						$_SESSION['loggedin'] = true;
						$_SESSION['userlogin'] = $user;
						$_SESSION['username'] = $name[1];
						$_SESSION['departamento'] = $id_departamento;
						$_SESSION['cmbSum'] = 0;
						$_SESSION['cmbPDV'] = 0;
						$_SESSION['matrizPed']= array(); 
						$_SESSION['PDV']= '';
						$_SESSION['ciudadPDV']= '';
						$_SESSION['direccionPDV']= '';
						$_SESSION['limitePOS'] = 0.00;
						$_SESSION['totalPedido'] = 0.00;
						$_SESSION['file']= '';
						$_SESSION['mensajes']= '';
						echo '<script> location.replace("../pages/home.php"); </script>';
					} 
					else 
					{
						//var_dump(@ldap_bind($this->ldap_con, $user, $password));
						$_SESSION['mensajes']= 'Error de usuario o clave!';
						echo '<script> location.replace("../index.php"); </script>';
						//header('Location: ../index.php', true, 301);
						//exit;
					}
				}
				else
				{
					$_SESSION['mensajes']= 'Ingrese los datos correctos!';
					echo '<script> location.replace("../index.php"); </script>';
				}
			}
			catch (Exception $e) 
			{
    			die("Could not connect to AD " . $e->getMessage());
			}	
		}
	}
?>
</body>
</html>
