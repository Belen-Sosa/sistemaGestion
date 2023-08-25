
<?php
  
  //llamo a la conexion de la base de datos 
  require_once("../config/conexion.php");
  //llamo al modelo Compras
  require_once("../modelos/Compras.php");
  $compras = new Compras();


  switch($_GET["op"]){


  	case "ver_detalle_proveedor_compra":


        $datos= $compras->get_detalle_proveedor($_POST["numero_compra"]);	

        // si existe el proveedor entonces recorre el array
	    if(is_array($datos)==true and count($datos)>0){

			foreach($datos as $row)
			{
				
				$output["nombre_proveedor"] = $row["nombre_proveedor"];
				$output["numero_compra"] = $row["numero_compra"];
				$output["cuit_proveedor"] = $row["cuit_proveedor"];
				$output["direccion_proveedor"] = $row["direccion_proveedor"];
				$output["fecha_compra"] = date("d-m-Y", strtotime($row["fecha_compra"]));
								
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

	    //fin de mensaje de error	    
    break;

  	case "ver_detalle_compra":

  	   $datos= $compras->get_detalle_compras_proveedor($_POST["numero_compra"]);	


    break;

  	 
    case "buscar_compras":

		$datos=$compras->get_compras();

		//Vamos a declarar un array
		$data= Array();

		foreach($datos as $row){

				$sub_array = array();
				$est = '';
				
				
			if($row["estado_compra"] == 1){
				$est = 'dado de alta';
				$atrib = "btn btn-success btn-md estado";
			}
			else{
				if($row["estado_compra"] == 0){
					$est = 'dado de baja';
					$atrib = "btn btn-danger btn-md estado";
				}	
			}

		
			$sub_array[] = '<button class="btn btn-warning detalle"  id="'.$row["numero_compra"].'"  data-toggle="modal" data-target="#detalle_compra"><i class="fa fa-eye"></i></button>';
			$sub_array[] = date("d-m-Y", strtotime($row["fecha_compra"]));
			$sub_array[] = $row["numero_compra"];
			$sub_array[] = $row["nombre_proveedor"];
			$sub_array[] = $row["cuit_proveedor"];
			$sub_array[] = $row["usuario"];
			$sub_array[] = $row["tipo_pago_compra"];
			$sub_array[] = "$".number_format($row["total_compra"],2);
		    $sub_array[] = '<button type="button" onClick="cambiarEstado('.$row["id_compras"].',\''.$row["numero_compra"].'\','.$row["estado_compra"].');" name="estado" id="'.$row["id_compras"].'" class="'.$atrib.'">'.$est.'</button>';
                
			$data[] = $sub_array;
		}

        $results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);


    break;

    case "cambiar_estado_compra":


		$datos=$compras->get_compras_por_id($_POST["id_compras"]);

		// si existe el id de la compra entonces se edita el estado del detalle de la compra
	    if(is_array($datos)==true and count($datos)>0){

                  //cambia el estado de la compra
				  $compras->cambiar_estado($_POST["id_compras"], $_POST["numero_compra"], $_POST["est"]);
		
	    } 


    break;

    case "buscar_compras_fecha":
          
		$datos=$compras->lista_busca_registros_fecha($_POST["fecha_inicial"], $_POST["fecha_final"]);

		//Vamos a declarar un array
		$data= Array();

     	foreach($datos as $row){
			$sub_array = array();

			$est = '';
			if($row["estado_compra"] == 1){
				$est = 'dado de alta';
				$atrib = "btn btn-success btn-md estado";
			}
			else{
				if($row["estado_compra"] == 0){
					$est = 'dado de baja';
					$atrib = "btn btn-danger btn-md estado";
				}	
			}
			
			
		    $sub_array[] = '<button class="btn btn-warning detalle" id="'.$row["numero_compra"].'"  data-toggle="modal" data-target="#detalle_compra"><i class="fa fa-eye"></i></button>';
	        $sub_array[] = date("d-m-Y", strtotime($row["fecha_compra"]));
			$sub_array[] = $row["numero_compra"];
			$sub_array[] = $row["nombre_proveedor"];
			$sub_array[] = $row["cuit_proveedor"];
			$sub_array[] = $row["usuario"];
			$sub_array[] = $row["tipo_pago_compra"];
			$sub_array[] = "$ ".number_format($row["total_compra"],2);	
           /*IMPORTANTE: poner \' cuando no sea numero, sino no imprime*/
            $sub_array[] = '<button type="button" onClick="cambiarEstado('.$row["id_compras"].',\''.$row["numero_compra"].'\','.$row["estado_compra"].');" name="estado" id="'.$row["id_compras"].'" class="'.$atrib.'">'.$est.'</button>';
                
			$data[] = $sub_array;
		}


        $results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 	    echo json_encode($results);


    break;


    case "buscar_compras_fecha_mes":

    $datos= $compras->lista_busca_registros_fecha_mes($_POST["mes"],$_POST["ano"]);
       
	//Vamos a declarar un array
    $data= Array();

    foreach($datos as $row){
		$sub_array = array();
		$est = '';

		if($row["estado_compra"] == 1){
			$est = 'dado de alta';
			$atrib = "btn btn-success btn-md estado";
		}
		else{
			if($row["estado_compra"] == 0){
				$est = 'dado de baja';
				$atrib = "btn btn-danger btn-md estado";
			}	
		}
		

		$sub_array[] = '<button class="btn btn-warning detalle" id="'.$row["numero_compra"].'"  data-toggle="modal" data-target="#detalle_compra"><i class="fa fa-eye"></i></button>'; 
        $sub_array[] = date("d-m-Y", strtotime($row["fecha_compra"]));
		$sub_array[] = $row["numero_compra"];
		$sub_array[] = $row["nombre_proveedor"];
		$sub_array[] = $row["cuit_proveedor"];
		$sub_array[] = $row["usuario"];
		$sub_array[] = $row["tipo_pago_compra"];
		$sub_array[] = "$ ".number_format($row["total_compra"],2);
	    $sub_array[] = '<button type="button" onClick="cambiarEstado('.$row["id_compras"].',\''.$row["numero_compra"].'\','.$row["estado_compra"].');" name="estado" id="'.$row["id_compras"].'" class="'.$atrib.'">'.$est.'</button>';
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

  ?>