<?php
  
  //llamo a la conexion de la base de datos 
  require_once("../config/conexion.php");
  //llamo al modelo Venta
  require_once("../modelos/Ventas.php");
  $ventas = new Ventas();
  switch($_GET["op"]){

    case "buscar_ventas":

		$datos=$ventas->get_ventas();

		//Vamos a declarar un array
		$data= Array();

     	foreach($datos as $row){
			
				$sub_array = array();
				$est = '';
				
				if($row["estado_venta"] == 1){
					$est = 'dado de alta';
					$atrib = "btn btn-success btn-md estado";
				}
				if($row["estado_venta"] == 0){
					$est = 'dado de baja';
					$atrib = "btn btn-danger btn-md estado";
					
				}
				
					
				
				

				 $sub_array[] = '<button class="btn btn-warning detalle" id="'.$row["numero_venta"].'"  data-toggle="modal" data-target="#detalle_venta"><i class="fa fa-eye"></i></button>';
	             $sub_array[] = date("d-m-Y",strtotime($row["fecha_venta"]));
				 $sub_array[] = $row["numero_venta"];
				 $sub_array[] = $row["nombre_cliente"];
				 $sub_array[] = $row["dni_cliente"];
				 $sub_array[] = $row["usuario"];
				 $sub_array[] = $row["tipo_pago_venta"];
				 $sub_array[] = "$ ".number_format($row["total_venta"],2);
          		 /*IMPORTANTE: poner \' cuando no sea numero, sino no imprime*/
                 $sub_array[] = '<button type="button" onClick="cambiarEstado('.$row["id_ventas"].',\''.$row["numero_venta"].'\','.$row["estado_venta"].');" name="estado" id="'.$row["id_ventas"].'" class="'.$atrib.'">'.$est.'</button>';
                
				 $data[] = $sub_array;
		}



      $results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);


     break;

     case "ver_detalle_cliente_venta":


     	$datos= $ventas->get_detalle_cliente($_POST["numero_venta"]);	

            // si existe el proveedor entonces recorre el array
	    if(is_array($datos)==true and count($datos)>0){

			foreach($datos as $row){
					
				$output["cliente"] = $row["nombre_cliente"];
				$output["numero_venta"] = $row["numero_venta"];
				$output["dni_cliente"] = $row["dni_cliente"];
				$output["direccion"] = $row["direccion_cliente"];
				$output["fecha_venta"] = date("d-m-Y", strtotime($row["fecha_venta"]));
									
			}
		
		      
		     echo json_encode($output);


	    } else {
                 
                 //si no existe el registro entonces no recorre el array
                $errors[]="no existe";

	    }


	         //inicio de mensaje de error

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

  	case "ver_detalle_venta":

  	   $datos= $ventas->get_detalle_ventas_cliente($_POST["numero_venta"]);	

  	break;


 	case "consulta_cantidad_venta":

         //selecciona el id del registro

   		 require_once("../modelos/Productos.php");

		$producto= new Producto();
		$datos=$producto->get_producto_por_id($_POST["id_producto"]);

          // si existe el id del producto entonces recorre el array
	    if(is_array($datos)==true and count($datos)>0){

				foreach($datos as $row){
					
					$stock = $s["stock"] = $row["stock_producto"];
                 
                	 //consultamos si la cantidad que se va a querer vender es mayor a la cantidad de stock entonces que solo se refleje la cantidad maxima que se encuentre en el stock y que me devuelva ese valor en el campo

					$result = null;

					$stock_vender=$_POST["cantidad_vender"];

					//importante:tuve que poner esta condicional para que me funcionara la condicional
					if($stock_vender>$stock and $stock_vender!=0){

                        $result="<h4 class='text-danger'>La cantidad seleccionada es mayor al stock</h4>";
					}else {

						if($stock_vender==0){

						$result="<h4 class='text-danger'>El campo está vacío</h4>";

						 }

				    }
					
				}//cierre del foreach
		    
		        echo json_encode($result);

	    } else {
                 
                 //si no existe el registro entonces no recorre el array
                $errors[]="El producto no existe";

	    }
	         //inicio de mensaje de error

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

	        //fin de mensaje de error



	break;


	case "cambiar_estado_venta":


		$datos=$ventas->get_ventas_por_id($_POST["id_ventas"]);

		// si existe el id de la venta entonces se edita el estado del detalle de la venta
		if(is_array($datos)==true and count($datos)>0){

			//cambia el estado de la compra
			$ventas->cambiar_estado($_POST["id_ventas"], $_POST["numero_venta"], $_POST["est"]);
			foreach($datos as $row){
			$id_cliente=$row["id_cliente"];
			}
			require_once("../modelos/CuentasCorrientes.php");

			$cuentaCorriente= new CuentaCorriente();
			foreach($datos as $row){
			$id_cliente=$row["id_cliente"];
			}
			$dato_cc= $cuentaCorriente-> get_cc_por_cliente($id_cliente);
			foreach($dato_cc as $row){
			$id_cc=$row["id_cuenta_corriente"];
			}
			$cuentaCorriente->actualizar_saldo_cuenta_corriente($id_cliente,$id_cc);
		
		} 


	break;
	

	break;

    case "buscar_ventas_fecha":
          
     $datos=$ventas->lista_busca_registros_fecha($_POST["fecha_inicial"], $_POST["fecha_final"]);

     //Vamos a declarar un array
 	 $data= Array();

	foreach($datos as $row) {
		$sub_array = array();

		$est = '';
		
		
		if($row["estado_venta"] == 1){
			$est = 'dado de alta';
			$atrib = "btn btn-success btn-md estado";
		}
		if($row["estado_venta"] == 0){
			$est = 'dado de baja';
			$atrib = "btn btn-danger btn-md estado";
			
		}
		
		
		$sub_array[] = '<button class="btn btn-warning detalle" id="'.$row["numero_venta"].'"  data-toggle="modal" data-target="#detalle_venta"><i class="fa fa-eye"></i></button>';
		$sub_array[] = date("d-m-Y",strtotime($row["fecha_venta"]));
		$sub_array[] = $row["numero_venta"];
		$sub_array[] = $row["nombre_cliente"];
		$sub_array[] = $row["dni_cliente"];
		$sub_array[] = $row["usuario"];
		$sub_array[] = $row["tipo_pago_venta"];
		$sub_array[] ="$ ".number_format($row["total_venta"],2);

		/*IMPORTANTE: poner \' cuando no sea numero, sino no imprime*/
		$sub_array[] = '<button type="button" onClick="cambiarEstado('.$row["id_ventas"].',\''.$row["numero_venta"].'\','.$row["estado_venta"].');" name="estado" id="'.$row["id_ventas"].'" class="'.$atrib.'">'.$est.'</button>';
			
		$data[] = $sub_array;
		}




      $results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);


     break;

     case "buscar_ventas_fecha_mes":
 
     	 $datos= $ventas->lista_busca_registros_fecha_mes($_POST["mes"],$_POST["ano"]);
        //Vamos a declarar un array
 	    $data= Array();

		foreach($datos as $row){
		        $sub_array = array();
		        $est = '';		        		       
				if($row["estado_venta"] == 1){
					$est = 'dado de alta';
					$atrib = "btn btn-success btn-md estado";
				}
				if($row["estado_venta"] == 0){
					$est = 'dado de baja';
					$atrib = "btn btn-danger btn-md estado";
					
				}

        
      		$sub_array[] = '<button class="btn btn-warning detalle" id="'.$row["numero_venta"].'"  data-toggle="modal" data-target="#detalle_venta"><i class="fa fa-eye"></i></button>';
			$sub_array[] = date("d-m-Y", strtotime($row["fecha_venta"]));
			$sub_array[] = $row["numero_venta"];
			$sub_array[] = $row["nombre_cliente"];
			$sub_array[] = $row["dni_cliente"];
			$sub_array[] = $row["usuario"];
			$sub_array[] = $row["tipo_pago_venta"];
			$sub_array[] = number_format($row["total_venta"],2);
			/*IMPORTANTE: poner \' cuando no sea numero, sino no imprime*/
			$sub_array[] = '<button type="button" onClick="cambiarEstado('.$row["id_ventas"].',\''.$row["numero_venta"].'\','.$row["estado_venta"].');" name="estado" id="'.$row["id_ventas"].'" class="'.$atrib.'">'.$est.'</button>';
                
      		$data[] = $sub_array;
        
        }


      $results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);


     break;



}