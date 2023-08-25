<?php

	//llamo a la conexion de la base de datos 
    require_once("../config/conexion.php");
    //llamo al modelo Clientes
    require_once("../modelos/Clientes.php");
    //llamo al modelo Cuentas Corrientes
    require_once("../modelos/CuentasCorrientes.php");
	$cuentas_corrientes= new CuentaCorriente();

	//llamo al modelo Ventas
	require_once("../modelos/Ventas.php");
    $clientes = new Cliente();


    //declaramos las variables de los valores que se envian por el formulario y que recibimos por ajax y decimos que si existe el parametro que estamos recibiendo

	//los valores vienen del atributo name de los campos del formulario
	/*el valor id_usuario y dni_cliente se carga en el campo hidden cuando se edita un registro*/
	//se copian los campos de la tabla clientes
   $id_usuario=isset($_POST["id_usuario"]);
   $dni_cliente=isset($_POST["dni_cliente"]);
   $dni = isset($_POST["dni"]);
   $nombre=isset($_POST["nombre"]);
   $apellido=isset($_POST["apellido"]);
   $telefono=isset($_POST["telefono"]);
   $dni1=isset($_POST["dni"]);
   $direccion=isset($_POST["direccion"]);
   $estado=isset($_POST["estado"]);


    switch($_GET["op"]){

        case "guardaryeditar":

			/*si la dni_cliente no existe entonces lo registra
			importante: se debe poner el $_POST sino no funciona*/
	        if(empty($_POST["dni_cliente"])){

					/*verificamos si la dni del cliente en la base de datos, si ya existe un registro con el cliente entonces no se registra*/

					//importante: se debe poner el $_POST sino no funciona
					$datos = $clientes->get_datos_cliente($_POST["dni"],$_POST["nombre"],$_POST["telefono"]);

			       	if(is_array($datos)==true and count($datos)==0){

						//no existe el cliente por lo tanto hacemos el registros
					
						$clientes->registrar_cliente($dni,$nombre,$apellido,$telefono,$direccion,$estado,$id_usuario);
							
						$datos=$clientes->get_cliente_por_dni($_POST["dni"]);

					    // si existe el id del cliente entonces recorre el array
						if(is_array($datos)==true  and count($datos)>0){
							foreach($datos as $row){

								$id_cliente=$row["id_cliente"];
						
								require_once("../modelos/consolelog.php");
						
								echo Console::log('un_nombre',$id_cliente );
							
							}

							if($_POST["habilitar_cc"]=="1"){
								//me fijo si existe ya la cuenta corriente
								$datos_cc=$cuentas_corrientes->get_idcc_por_cliente($id_cliente);
								if(is_array($datos_cc)==true and count($datos_cc)==0){
									
								//se crea la cuenta corriente del usuario
								$cuentas_corrientes->crear_cuenta_corriente($id_cliente,$_POST["habilitar_cc"]);
								//edita el estado del cliente
								$messages[]="cuenta corriente creada con exito.";
								}
							}
													
				
						}
					$messages[]="El Cliente se registró correctamente.";
						                     
					}else {

							$errors[]="El Cliente ya existe";
					}
						 

			   }else {


	            	/*si ya existe entonces editamos el cliente*/


					$clientes->editar_cliente($dni,$nombre,$apellido,$telefono,$direccion,$estado,$id_usuario);
					$datos=$clientes->get_cliente_por_dni($_POST["dni_cliente"]);
					require_once("../modelos/consolelog.php");
					echo Console::log('un_nombre',"ESTAMOS ACA" );
					echo Console::log('un_nombre',$_POST["dni_cliente"] );	
					echo Console::log('un_nombre',$datos );	

					// si existe el id del cliente entonces recorre el array
					if(is_array($datos)==true and count($datos)>0){
									
						foreach($datos as $row)
						{
							$id_cliente=$row["id_cliente"];
							
						}
							
						if($_POST["habilitar_cc"]=="1"){
						
							//me fijo si existe ya la cuenta corriente
							$datos_cc=$cuentas_corrientes->get_idcc_por_cliente($id_cliente);
							if(is_array($datos_cc)==true and count($datos_cc)==0){
								
								//se crea la cuenta corriente del usuario
								$cuentas_corrientes->crear_cuenta_corriente($id_cliente,$_POST["habilitar_cc"]);
								//edita el estado del cliente
							}
						}else if($_POST["habilitar_cc"]=="0"){
							$cuentas_corrientes->editar_cuenta_corriente($id_cliente,$_POST["habilitar_cc"]);
															
						}
						

					}

	            	 $messages[]="El cliente se editó correctamente";
                            	 
	            }

      
			//mensaje success
		if (isset($messages)){
					
			?>
			<div class="alert alert-success" role="alert">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<strong>¡Bien hecho!</strong>
					<?php
						foreach ($messages as $message) {
								echo $message;
							}
						?>
			</div>
			<?php
					
					
		}


			//mensaje error
		if (isset($errors)){
				
			?>
				<div class="alert alert-danger" role="alert">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>Error!</strong> 
						<?php
							foreach ($errors as $error) {
									echo $error;
								}
							?>
				</div>
			<?php

		}

	
    break;


    case 'mostrar':

    
		//el parametro dni se envia por AJAX cuando se edita el cliente
		$datos=$clientes->get_cliente_por_dni($_POST["dni_cliente"]);

		//verifica si la dni tiene registro asociado a ventas
		$cliente_ventas=$clientes->get_cliente_por_dni_ventas($_POST["dni_cliente"]); 

		//verifica si la dni tiene registro asociado a detalle_ventas
		$cliente_detalle_ventas=$clientes->get_cliente_por_dni_detalle_ventas($_POST["dni_cliente"]);

        //si la dni del cliente NO tiene registros asociados en las tablas ventas y detalle_ventas entonces se puede editar el cliente completo
	    if(is_array($cliente_ventas)==true and count($cliente_ventas)==0 and is_array($cliente_detalle_ventas)==true and count($cliente_detalle_ventas)==0){


			foreach($datos as $row)
			{
				$output["dni_cliente"] = $row["dni_cliente"];
				$output["nombre"] = $row["nombre_cliente"];
				$output["apellido"] = $row["apellido_cliente"];
				$output["telefono"] = $row["telefono_cliente"];
				$output["direccion"] = $row["direccion_cliente"];
				$output["fecha"] = $row["fecha_alta_cliente"];
				$output["estado"] = $row["estado_cliente"];

			}


	    } else {
                 
	        //si la dni tiene relacion con la tabla ventas y detalle_ventas entonces se deshabilita el cliente,dni, apellido
            foreach($datos as $row){

				$output["dni_relacion"] = $row["dni_cliente"];
				$output["nombre"] = $row["nombre_cliente"];
				$output["apellido"] = $row["apellido_cliente"];
				$output["telefono"] = $row["telefono_cliente"];
				$output["direccion"] = $row["direccion_cliente"];
				$output["fecha"] = $row["fecha_alta_cliente"];
				$output["estado"] = $row["estado_cliente"];
			}

	    }


         echo json_encode($output);


	break;

    case "activarydesactivar":
		
		//los parametros id_cliente y est vienen por via ajax
		$datos=$clientes->get_cliente_por_id($_POST["id_cliente"]);

		// si existe el id del cliente entonces recorre el array
	    if(is_array($datos)==true and count($datos)>0){

			//edita el estado del cliente
			$clientes->editar_estado($_POST["id_cliente"],$_POST["est"]);
		     
	    } 

    break;

    case "listar":

		$datos=$clientes->get_clientes();

		//Vamos a declarar un array
		$data= Array();

        foreach($datos as $row){

				$sub_array = array();
				$est = '';
				$atrib = "btn btn-success btn-md estado";

				if($row["estado_cliente"] == 0){
					$est = 'INACTIVO';
					$atrib = "btn btn-warning btn-md estado";
				}
				else{
					if($row["estado_cliente"] == 1){
						$est = 'ACTIVO';
						
					}	
				}
				
			
	             $sub_array[] = $row["dni_cliente"];
				 $sub_array[] = $row["nombre_cliente"];
				 $sub_array[] = $row["apellido_cliente"];
				 $sub_array[] = $row["telefono_cliente"];
				 $sub_array[] = $row["direccion_cliente"];
				 $sub_array[] = date("d-m-Y",strtotime($row["fecha_alta_cliente"]));
				 $sub_array[] = '<button type="button" onClick="cambiarEstado('.$row["id_cliente"].','.$row["estado_cliente"].');" name="estado" id="'.$row["id_cliente"].'" class="'.$atrib.'">'.$est.'</button>';
                 $sub_array[] = '<button type="button" onClick="mostrar('.$row["dni_cliente"].');" id="'.$row["id_cliente"].'" class="btn btn-warning btn-md"><i class="glyphicon glyphicon-edit"></i> Editar</button>';
                 
				 $data[] = $sub_array;
		}

    	$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 	  	echo json_encode($results);
		


    break;

    /*se muestran en ventana modal el datatable de los clientes en ventas para seleccionar luego los clientes activos y luego se autocomplementa los campos de un formulario*/
    case "listar_en_ventas":

		$datos=$clientes->get_clientes();
		//Vamos a declarar un array
		$data= Array();

        foreach($datos as $row){

				$sub_array = array();
				$est = '';
				
				$atrib = "btn btn-success btn-md estado";
				if($row["estado_cliente"] == 0){
					$est = 'INACTIVO';
					$atrib = "btn btn-warning btn-md estado";
				}
				else{
					if($row["estado_cliente"] == 1){
						$est = 'ACTIVO';
						
					}	
				}
				

				$sub_array[] = $row["dni_cliente"];
				$sub_array[] = $row["nombre_cliente"];
				$sub_array[] = $row["apellido_cliente"];
				$sub_array[] = '<button type="button"  name="estado" id="'.$row["id_cliente"].'" class="'.$atrib.'">'.$est.'</button>';
				$sub_array[] = '<button type="button" onClick="agregar_registro('.$row["id_cliente"].','.$row["estado_cliente"].');" id="'.$row["id_cliente"].'" class="btn btn-primary btn-md"><i class="fa fa-plus" aria-hidden="true"></i> Agregar</button>';
                
				$data[] = $sub_array;
		}

		$results = array(
			"sEcho"=>1, //Información para el datatables
			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);


     break;


     /*valida los clientes activos y se muestran en un formulario*/
    case "buscar_cliente":


	    $datos=$clientes->get_cliente_por_id_estado($_POST["id_cliente"],$_POST["est"]);

		// comprobamos que el cliente esté activo, de lo contrario no lo agrega
		if(is_array($datos)==true and count($datos)>0){

			foreach($datos as $row){
				$output["dni_cliente"] = $row["dni_cliente"];
				$output["nombre"] = $row["nombre_cliente"];
				$output["apellido"] = $row["apellido_cliente"];
				$output["direccion"] = $row["direccion_cliente"];
				$output["estado"] = $row["estado_cliente"];
				
			}

			

	    } else {
			
			//si no existe el registro entonces no recorre el array
		
			$output["error"]="El cliente seleccionado está inactivo, intenta con otro";
	    }

	    echo json_encode($output);

    break;
	 
	/*valida los clientes activos y se muestran en un formulario*/
	case "buscar_cliente_id":

		$datos= $clientes->get_cliente_por_id($_POST["id_cliente"]);
	
		// comprobamos que el cliente esté activo, de lo contrario no lo agrega
		if(is_array($datos)==true and count($datos)>0){

			foreach($datos as $row){
				
				$nombre= $row["nombre_cliente"];
				$apellido = $row["apellido_cliente"];
			}
		} 

		$datos="Cliente: ".$nombre." ".$apellido;
			
	break;
	 	
	}
  


   ?>