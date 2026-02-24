<?php

	class DB
	{
		private $server =   '';
		private $base = 	'';
		private $usuario = 	'';
		private $clave=	'';
		private $con=   '';
		
		public function __construct ()
		{
			try 
			{	
				error_reporting(0);
				$this->server=			'127.0.0.1';
				//$this->server=			'192.168.1.138';
				$this->base = 			'DB_SupplyChain';
				$this->usuario = 		'dbSystemSC';
				$this->clave = 			'C0n3x10nSC2024';
				//$this->base = 			'mysql';
				//$this->usuario = 		'root';
				//$this->clave = 			'supply.farmcorp';	
				/*$this->con= mysqli_connect($this->server, $this->usuario, $this->clave, $this->base);
				
				if (!$this->con) 
				{
					die("Connection failed: " . mysqli_connect_error());
				}*/

			}
			catch (Exception $e) 
			{
				die("Could not connect to the database: " . $e->getMessage());
			}
		}
		
		public function _conectarDB()
		{
			try
			{	
				$this->con= mysqli_connect($this->server, $this->usuario, $this->clave, $this->base);

				if (!$this->con) 
				{
					die("Connection failed: " . mysqli_connect_error());
				}
			}
			catch (Exception $e) 
			{
    			die("Could not connect to the database");
			}
		}
		
		public function _getTipoSuministros ()
		{
			$data= '';
			$indice= 0;
			try
			{
				$this->_conectarDB();
				
				if ($this->con)
				{
					$sql='Select codigo, descripcion from tipo_suministros';
					$conexion = $this->con;
					$consulta= $conexion->query ($sql);
					if ($consulta->num_rows >0)
					{
   					   $data= $consulta;
					}
					else
					{
						$data= '0';
					}
				}
				
				$this->cerrarDB();
				
			}
			catch (Exception $e) 
			{
				
    			die("Could not connect to the database " . $e->getMessage());
			}		
				
			return ($data);
		}

		public function _getSuministros ($tipo)
		{
			$data= '';
			$indice= 0;
			try
			{
				$this->_conectarDB();

				if ($this->con)
				{
					$sql='Select codigo, descripcion from suministros where tipo_suministro=' .$tipo;
					$conexion = $this->con;
					$consulta= $conexion->query ($sql);
					if ($consulta->num_rows >0)
					{
   					   $data= $consulta;
					}
					else
					{
						$data= '0';
					}
				}
				
				$this->cerrarDB();
			}
			catch (Exception $e) 
			{
    			die("Could not connect to the database " . $e->getMessage());
			}		
			
			return ($data);
		}

		public function _getSuministro ($codigo)
		{
			$data= '';
			$indice= 0;
			try
			{
				$this->_conectarDB();

				if ($this->con)
				{
					$sql='Select descripcion from suministros where codigo=' .$codigo;
					$conexion = $this->con;
					$consulta= $conexion->query ($sql);
					if ($consulta->num_rows >0)
					{
   					   $data= $consulta->fetch_assoc();
					}
					else
					{
						$data= '0';
					}
				}
				
				$this->cerrarDB();
			}
			catch (Exception $e) 
			{
    			die("Could not connect to the database " . $e->getMessage());
			}		
			
			return ($data);
		}
		
		public function _getTipoSuministro ($codigo)
		{
			$data= '';
			$indice= 0;
			try
			{
				$this->_conectarDB();
				
				if ($this->con)
				{
					$sql='Select descripcion from tipo_suministros where codigo=' .$codigo;
					$conexion = $this->con;
					$consulta= $conexion->query ($sql);
					if ($consulta->num_rows >0)
					{
   					   $data= $consulta->fetch_assoc();
					}
					else
					{
						$data= '0';
					}
				}
				
				$this->cerrarDB();
			}
			catch (Exception $e) 
			{
    			die("Could not connect to the database " . $e->getMessage());
			}		
			
			return ($data);
		}
		
		public function _getPrecioSuministro ($codigo)
		{
			$data= '';
			$indice= 0;
			try
			{
				$this->_conectarDB();
				
				if ($this->con)
				{
					$sql='Select precio from suministros where codigo=' .$codigo;
					$conexion = $this->con;
					$consulta= $conexion->query ($sql);
					if ($consulta->num_rows >0)
					{
   					   $data= $consulta->fetch_assoc();
					}
					else
					{
						$data= '0';
					}
				}
				
				$this->cerrarDB();
				
			}
			catch (Exception $e) 
			{
    			die("Could not connect to the database " . $e->getMessage());
			}		
			
			return ($data);
		}

		public function _getPDVs ()
		{
			$data= '';
			$indice= 0;
			try
			{
				$this->_conectarDB();
				
				if ($this->con)
				{
					$sql='Select codigo, descripcion from pdvs where estado_pdv=1';
					$conexion = $this->con;
					$consulta= $conexion->query ($sql);
					if ($consulta->num_rows >0)
					{
   					   $data= $consulta;
					}
					else
					{
						$data= '0';
					}
				}
				
				$this->cerrarDB();
			}
			catch (Exception $e) 
			{
    			die("Could not connect to the database " . $e->getMessage());
			}		
			
			return ($data);
		}
		
		public function _getPDV ($codigo)
		{
			$data= '';
			$indice= 0;
			try
			{
				$this->_conectarDB();
				
				if ($this->con)
				{
					//$sql='Select descripcion, ciudad, direccion from pdvs where codigo=' .$codigo;
					$sql= 'SELECT pdvs.descripcion, ciudades.descripcion as ciudad, pdvs.direccion FROM pdvs INNER JOIN ciudades ON pdvs.ciudad = ciudades.codigo where pdvs.codigo=' .$codigo;
					$conexion = $this->con;
					$consulta= $conexion->query ($sql);
					if ($consulta->num_rows >0)
					{
   					   $data= $consulta->fetch_assoc();
					   //var_dump($data);
					}
					else
					{
						$data= '0';
					}
				}
				
				$this->cerrarDB();
			}
			catch (Exception $e) 
			{
    			die("Could not connect to the database " . $e->getMessage());
			}		
			
			return ($data);
		}
		
		public function _getCiudad ($codigo)
		{
			$data= '';
			$indice= 0;
			try
			{
				$this->_conectarDB();
				
				if ($this->con)
				{
					$sql='Select descripcion from ciudades where codigo=' .$codigo;
					$conexion = $this->con;
					$consulta= $conexion->query ($sql);
					if ($consulta->num_rows >0)
					{
   					   $data= $consulta->fetch_assoc();
					}
					else
					{
						$data= '0';
					}
				}
				
				$this->cerrarDB();
			}
			catch (Exception $e) 
			{
    			die("Could not connect to the database " . $e->getMessage());
			}		
			
			return ($data);
		}		

		public function _getDepartamentos ()
		{
			$data= '';
			$indice= 0;
			try
			{
				$this->_conectarDB();
				
				if ($this->con)
				{
					$sql='Select codigo, descripcion from departamentos';
					$conexion = $this->con;
					$consulta= $conexion->query ($sql);
					if ($consulta->num_rows >0)
					{
   					   $data= $consulta;
					}
					else
					{
						$data= '0';
					}
				}
				
				$this->cerrarDB();
			}
			catch (Exception $e) 
			{
    			die("Could not connect to the database: " . $e->getMessage());
			}		
			
			return ($data);
		}
		
		public function _getDepartamento ($codigo)
		{
			$data= '';
			$indice= 0;
			try
			{
				$this->_conectarDB();
				
				if ($this->con)
				{
					$sql='Select descripcion from departamentos where codigo=' .$codigo;
					$conexion = $this->con;
					$consulta= $conexion->query ($sql);
					if ($consulta->num_rows >0)
					{
   					   $data= $consulta;
					}
					else
					{
						$data= '0';
					}
				}
				
				$this->cerrarDB();
			}
			catch (Exception $e) 
			{
    			die("Could not connect to the database " . $e->getMessage());
			}		
			
			return ($data);
		}
			
		public function _getLimitePDV ($codigo)
		{
			$data= '';
			$indice= 0;
			try
			{
				$this->_conectarDB();
				
				if ($this->con)
				{
					$sql='Select monto_autorizado from grupo_pdvs inner join pdvs on grupo_pdvs.codigo = pdvs.grupo_pdv where pdvs.codigo=' .$codigo;
					$conexion = $this->con;
					$consulta= $conexion->query ($sql);
					if ($consulta->num_rows >0)
					{
   					   $data= $consulta->fetch_assoc();
					}
					else
					{
						$data= '0';
					}
				}
				
				$this->cerrarDB();
			}
			catch (Exception $e) 
			{
    			die("Could not connect to the database " . $e->getMessage());
			}		
			
			return ($data);
		}
		
		public function _setCabeceraPedidos ($usuario, $pdv, $fechaSolicitud)
		{
			$data= '';
			try
			{
				$this->_conectarDB();
				
				if ($this->con)
				{
					$usuario=$_SESSION['userlogin'];
					$sql='Select codigo from usuarios where login="' .$usuario. '"';
					//var_dump($sql);
					$conexion = $this->con;
					$consulta= $conexion->query ($sql);
					//var_dump($consulta);
					if ($consulta->num_rows == 0)
					{
						$sql='Insert into usuarios (departamento, rol, login, nombres, email) values (' .$_SESSION["departamento"]. ',1,"' .$usuario. '","' .$_SESSION['username']. '","' .$usuario. '@farmcorp.com.ec")';
						//var_dump($sql);
						$conexion = $this->con;
						$consulta= $conexion->query ($sql);
						if ($consulta)
						{
							$data= mysqli_insert_id($conexion);					
						}		
						else
						{
							die("Error en registro! ");
						}
					}
					else
					{
						$registro= $consulta->fetch_assoc();
						$data= $registro['codigo'];
					}
					
					//$sql='Insert into cabecera_pedidos (usuario, pdv, estadoPedido, fecha) Select codigo,' .$pdv. ',1,"' .$fechaSolicitud. '" from usuarios where login="' .$usuario. '"';
					$sql='Insert into cabecera_pedidos (usuario, pdv, estadoPedido, fecha) values (' .$data. ',' .$pdv. ',1,"' .$fechaSolicitud. '")';
					//var_dump($sql);
					$conexion = $this->con;
					$consulta= $conexion->query ($sql);
					if ($consulta)
					{
						$data= mysqli_insert_id($conexion);					
					}
					else
					{
						die("Error en registro! ");
					}
				}
				
				$this->cerrarDB();
			}
			catch (Exception $e) 
			{
    			die("Could not connect to the database " . $e->getMessage());
			}
			
			return ($data);
		}

		public function _setDetallePedidos ($cabecera,$suministro, $cantidad, $precioU)
		{
			$data= '';
			try
			{
				$this->_conectarDB();
				
				if ($this->con)
				{				
					$sql='Insert into detalle_pedidos (cabPedido, suministro, cantidad, precioUnitario) values (' .$cabecera. ',' .$suministro. ',' .$cantidad. ',' .$precioU. ')';
					//var_dump($sql);
					$conexion = $this->con;
					$consulta= $conexion->query ($sql);
					if ($consulta)
					{
						$data= $consulta;					
					}
					else
					{
						die("Error en registro! ");
					}
				}
				
				$this->cerrarDB();
			}
			catch (Exception $e) 
			{
    			die("Could not connect to the database " . $e->getMessage());
			}
			
			return ($data);
		}
		
		
		public function cerrarDB ()
		{
			try
			{
				mysqli_close($this->con);
			}
			catch (Exception $e) 
			{
    			die("Could not connect to the database " . $e->getMessage());
			}	
			
		}
	}
?>