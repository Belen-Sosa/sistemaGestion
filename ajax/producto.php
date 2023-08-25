<?php

  //llamo a la conexion de la base de datos 
  require_once("../config/conexion.php");
  //llamo al modelo Producto
  require_once("../modelos/Productos.php");
  $productos = new Producto();

   //declaramos las variables de los valores que se envian por el formulario y que recibimos por ajax y decimos que si existe el parametro que estamos recibiendo
   
   //los valores vienen del atributo name de los campos del formulario
   /*el valor id_producto, id_categoria y id_usuario se carga en el campo hidden cuando se edita un registro*/
   
   $id_producto=isset($_POST["id_producto"]);
   $id_categoria=isset($_POST["categoria"]);
   $id_usuario=isset($_POST["id_usuario"]);
   $producto=isset($_POST["producto"]);  
   $precio_venta=isset($_POST["precio_venta"]);
   $stock = isset($_POST["stock"]);
   $estado = isset($_POST["estado"]);
   $procedente=isset($_POST["procedente"]);
   $imagen = isset($_POST["hidden_producto_imagen"]);
        


    switch($_GET["op"]){

        case "guardaryeditar":

           	   /*si el id no existe entonces lo registra
	           importante: se debe poner el $_POST sino no funciona*/
	        if(empty($_POST["id_producto"])){

               //si tiene procedente verificamos si tiene stock 

                if($_POST["procedente"]!= 0){
					require_once('../modelos/Productos.php');

					$producto = new Producto();
			
					$producto=$producto->get_producto_procente($_POST["procedente"]);
					
					foreach($producto as $row)
					{
						$stock_procedente = $row["stock_producto"];
					}
					
					// verificamos que el stock_procedente sea mayor al stock del producto 
					if($stock_procedente>=$_POST["stock"]){
							$datos = $productos->get_producto_nombre($_POST["producto"]);


							if(is_array($datos)==true and count($datos)==0){

							//no existe el producto por lo tanto hacemos el registros
							

							$productos->registrar_producto($id_categoria,$producto,$precio_venta,$stock,$estado,$imagen,$_POST["procedente"],$id_usuario);

							$productos->editar_stock_procedente($_POST["procedente"],$_POST["stock"]);

							$messages[]="El producto se registró correctamente";
						
			 		        }
				    }else{
				    	$errors[]="El procedente no tiene stock suficiente.";
				    }
			    }else{

					//si no tiene procedente continuamos registro normal 
					/*verificamos si existe el producto en la base de datos, si ya existe un registro con la categoria entonces no se registra*/
					//importante: se debe poner el $_POST sino no funciona
						$datos = $productos->get_producto_nombre($_POST["producto"]);

					    if(is_array($datos)==true and count($datos)==0){

						//no existe el producto por lo tanto hacemos el registros

						$productos->registrar_producto($id_categoria,$producto,$precio_venta,$stock,$estado,$imagen,$procedente,$id_usuario);
						$messages[]="El producto se registró correctamente";

					    }else {

							$errors[]="El producto ya existe";
						}
				}//cierre del if procedentte
			} else {


				if($_POST["procedente"]!= 0){
					require_once('../modelos/Productos.php');
					$producto = new Producto();

					$producto=$producto->get_producto_procente($_POST["procedente"]);
					
					foreach($producto as $row)
					{
						$stock_procedente = $row["stock_producto"];
					}
					
					// verificamos que el stock_procedente sea mayor al stock del producto 
					if($stock_procedente>=$_POST["stock"]){

							$datos = $productos->get_producto_nombre($_POST["producto"]);
							$productos->editar_producto($id_producto,$id_categoria,$producto,$precio_venta,$_POST["stock"],$estado,$imagen,$procedente,$id_usuario);
							$productos->editar_stock_procedente($_POST["procedente"],$_POST["stock"]);

							$messages[]="El producto se edito correctamente";
						
							 
					}else{
						$errors[]="El procedente no tiene stock suficiente.";
					}
				}else{


	            	/*si ya existe entonces editamos el producto*/
	                $productos->editar_producto($id_producto,$id_categoria,$producto,$precio_venta,$stock,$estado,$imagen,$procedente,$id_usuario);
	                $messages[]="El producto se editó correctamente";
				}
	            	 
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

		//selecciona el id del producto
		
		//el parametro id_producto se envia por AJAX cuando se edita el producto
		$datos=$productos->get_producto_por_id($_POST["id_producto"]);

		//verifica si el id_producto tiene registro asociado a detalle_compra
		$producto_detalle_compra=$productos->get_producto_por_id_detalle_compra($_POST["id_producto"]);

	    //verifica si el id_producto tiene registro asociado a detalle_venta
		$producto_detalle_venta=$productos->get_producto_por_id_detalle_venta($_POST["id_producto"]);
	
        //si el id del producto NO tiene registros asociados en las tablas detalle_compras y detalle_ventas entonces se puede editar el producto
	    if(is_array($producto_detalle_compra)==true and count($producto_detalle_compra)==0 and is_array($producto_detalle_venta)==true and count($producto_detalle_venta)==0){



			foreach($datos as $row)
			{
				$output["id_producto"] = $row["id_producto"];
				$output["categoria"] = $row["id_categoria"];
				$output["categoria_nombre"] = $row["categoria"];
				$output["producto"] = $row["nombre_producto"];
				$output["precio_venta"] = $row["precio_venta_producto"];
			    $output["stock"] = $row["stock_producto"];				
				$output["estado"] = $row["estado_producto"];
				$output["procedente"] = $row["procedente"];
				$output["id_procedente"] = $row["id_procedente"];

				if($row["imagen_producto"] != ''){
						$output['producto_imagen'] = '<img src="upload/'.$row["imagen_producto"].'" class="img-thumbnail" width="30" height="10" /><input type="hidden" name="hidden_producto_imagen" value="'.$row["imagen_producto"].'" />';
				}else{
						$output['producto_imagen'] = '<input type="hidden" name="hidden_producto_imagen" value="" />';
				}


			}



	    } else {
                 
                
				foreach($datos as $row)
				{
					$output["producto_id"] = $row["id_producto"];
					$output["categoria"] = $row["id_categoria"];
					$output["producto"] = $row["nombre_producto"];
					$output["precio_venta"] = $row["precio_venta_producto"];
					if( $row["id_categoria"]=="14" ||  $row["id_categoria"]=="9" ||  $row["id_categoria"]=="11"){
						
						$output["stock"] = $row["stock_producto"];
					}else{
						$output["stock"] = $row["stock_producto"];
					}
					
					$output["estado"] = $row["estado_producto"];
					$output["categoria_nombre"] = $row["categoria"];
					$output["procedente"] = $row["procedente"];
					$output["id_procedente"] = $row["id_procedente"]; 
					if($row["imagen_producto"] != ''){
							$output['producto_imagen'] = '<img src="upload/'.$row["imagen_producto"].'" class="img-thumbnail" width="30" height="10" /><input type="hidden" name="hidden_producto_imagen" value="'.$row["imagen_producto"].'" />';
					}else{
						$output['producto_imagen'] = '<input type="hidden" name="hidden_producto_imagen" value="" />';
					}


			

			    }
			    

	    }//cierre del else


            echo json_encode($output);


	 break;

	case "activarydesactivar":
     
		//los parametros id_producto y est vienen por via ajax
		$datos=$productos->get_producto_por_id($_POST["id_producto"]);

          // si existe el id del producto entonces recorre el array
	    if(is_array($datos)==true and count($datos)>0){

			//edita el estado del producto
			$productos->editar_estado($_POST["id_producto"],$_POST["est"]);
            //editar estado de la categoria por producto
		    $productos->editar_estado_categoria_por_producto($_POST["id_categoria"],$_POST["est"]); 
	    } 

     break;

    case "listar":

        $datos=$productos->get_productos();

		//Vamos a declarar un array
		$data= Array();

		foreach($datos as $row){
				$sub_array = array();

				$est = '';
				 $atrib = "btn btn-success btn-md estado";
				if($row["estado_producto"] == 0){
					$est = 'INACTIVO';
					$atrib = "btn btn-warning btn-md estado";
				}
				else{
					if($row["estado_producto"] == 1){
						$est = 'ACTIVO';
					}	
				}

				  //STOCK, si es mejor de 10 se pone rojo sino se pone verde
				  $stock=""; 

				if($row["stock_producto"]<=10){
					
					$stock = $row["stock_producto"];
					$atributo = "badge bg-red-active";
						
				
				} else {

					$stock = $row["stock_producto"];
					$atributo = "badge bg-green";
				
				}


				$sub_array[] = $row["nombre_categoria"];
				$sub_array[] = $row["nombre_producto"];
				$sub_array[] = "$ ".$row["precio_venta_producto"];
                if($row["id_categoria"]=="9" || $row["id_categoria"]=="11" || $row["id_categoria"]=="14"){
					if($row["stock_producto"]>=1000){
						$stock_prod_grs=$row["stock_producto"]/1000;
						$sub_array[] = '<span class="'.$atributo.'">'.$stock_prod_grs.'
						Kg.</span>';
					}else{
						$sub_array[] = '<span class="'.$atributo.'">'.$row["stock_producto"].'
						grs.</span>';}
				}else{
					$sub_array[] = '<span class="'.$atributo.'">'.$row["stock_producto"].'
                  </span>';
				  }
				  $sub_array[] = $row["procedente"];

				$sub_array[] = '<button type="button" onClick="cambiarEstado('.$row["id_categoria"].','.$row["id_producto"].','.$row["estado_producto"].');" name="estado" id="'.$row["id_producto"].'" class="'.$atrib.'">'.$est.'</button>';


				$sub_array[] = '<button type="button" onClick="mostrar('.$row["id_producto"].');" id="'.$row["id_producto"].'" class="btn btn-warning btn-md"><i class="glyphicon glyphicon-edit"></i> Editar</button>';


               		

				
				if($row["imagen_producto"] != ''){
						$sub_array[] = ' <img src="upload/'.$row["imagen_producto"].'" class="img-thumbnail" width="200" height="50" /><input type="hidden" name="hidden_producto_imagen" value="'.$row["imagen_producto"].'" />

						';
				}else{
						

				           $sub_array[] = '<button type="button" id="" class="btn btn-primary btn-md"><i class="fa fa-picture-o" aria-hidden="true"></i> Sin imagen</button>';
					}
                
			

				 $data[] = $sub_array;

		

			 }
			 


     	 $results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);

 		echo json_encode($results);


	break;

	case "listar_en_compras":

		$datos=$productos->get_productos();

		//Vamos a declarar un array
		$data= Array();

		foreach($datos as $row){
				$sub_array = array();

				$est = '';
				 $atrib = "btn btn-success btn-md estado";
				if($row["estado_producto"] == 0){
					$est = 'INACTIVO';
					$atrib = "btn btn-warning btn-md estado";
				}
				else{
					if($row["estado_producto"] == 1){
						$est = 'ACTIVO';
				
					}	
				}

				  //STOCK, si es mejor de 10 se pone rojo sino se pone verde
				$stock=""; 

				if($row["stock_producto"]<=10){
				$stock = $row["stock_producto"];
					$atributo = "badge bg-red-active";
						
				
				} else {

				$stock = $row["stock_producto"];
					$atributo = "badge bg-green";
				
				}

				$sub_array[] = $row["nombre_categoria"];
				$sub_array[] = $row["nombre_producto"];
				$sub_array[] = "$ ".$row["precio_venta_producto"];
				if(	$row["id_categoria"]=="9" || $row["id_categoria"]=="11" || $row["id_categoria"]=="14"){
					if($row["stock_producto"]>=1000){
						$stock_productos_grs=$row["stock_producto"]/1000;
						$sub_array[] = '<span class="'.$atributo.'">'.$stock_productos_grs.' Kg.
					</span>';
					}else{
						$sub_array[] = '<span class="'.$atributo.'">'.$row["stock_producto"].' grs.
						</span>';
					}
					
  
				}else{
					$sub_array[] = '<span class="'.$atributo.'">'.$row["stock_producto"].'
					</span>';
  
				}
			
				$sub_array[] = '<button type="button"  name="estado" id="'.$row["id_producto"].'" class="'.$atrib.'">'.$est.'</button>';


				if($row["imagen_producto"] != ''){
					$sub_array[] = ' <img src="upload/'.$row["imagen_producto"].'" class="img-thumbnail" width="100" height="100" /><input type="hidden" name="hidden_producto_imagen" value="'.$row["imagen_producto"].'" />';
				}else{
						
					$sub_array[] = '<button type="button" id="" class="btn btn-primary btn-md"><i class="fa fa-picture-o" aria-hidden="true"></i> Sin imagen</button>';
				}
               		

				$sub_array[] = '<button type="button" name="" id="'.$row["id_producto"].'" class="btn btn-primary btn-md " onClick="agregarDetalle('.$row["id_producto"].',\''.$row["nombre_producto"].'\','.$row["estado_producto"].')"><i class="fa fa-plus"></i> Agregar</button>';
				$data[] = $sub_array;
			 
			 }


			$results = array(
					"sEcho"=>1, //Información para el datatables
					"iTotalRecords"=>count($data), //enviamos el total registros al datatable
					"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
					"aaData"=>$data);
			echo json_encode($results);


     break;


     case "buscar_producto";
          
          $datos=$productos->get_producto_por_id_estado($_POST["id_producto"], $_POST["estado"]);

            /*comprobamos que el producto esté activo, de lo contrario no lo agrega*/
	      if(is_array($datos)==true and count($datos)>0){

				foreach($datos as $row)
				{
					
					$output["id_producto"] = $row["id_producto"];
					$output["id_categoria"] = $row["id_categoria"];
					$output["producto"] = $row["nombre_producto"];				
					$output["stock"] = $row["stock_producto"];	     
				    $output["estado"] = $row["estado_producto"];
					$output["nombre_categoria"] = $row["nombre_categoria"];
					$output["id_categoria"] = $row["id_categoria"];
						
				}
		
	        } else {
                 
                 //si no existe el registro entonces no recorre el array
                 $output["error"]="El producto seleccionado está inactivo, intenta con otro";

	        }

	        echo json_encode($output);

     break;

     case "registrar_compra";

        //se llama al modelo Compras.php

        require_once('../modelos/Compras.php');

	    $compra = new Compras();

	    $compra->agrega_detalle_compra();

     break;


     /****************VENTAS*******************************/

    case "listar_en_ventas":

    	 $datos=$productos->get_productos_en_ventas();

    	 //Vamos a declarar un array
 		 $data= Array();

    	foreach($datos as $row){
			$sub_array = array();

			$est = '';
	
			$atrib = "btn btn-success btn-md estado";
			if($row["estado_producto"] == 0){
				$est = 'INACTIVO';
				$atrib = "btn btn-warning btn-md estado";
			}
			else{
				if($row["estado_producto"] == 1){
					$est = 'ACTIVO';

				}	
			}

				  //STOCK, si es mejor de 10 se pone rojo sino se pone verde
			$stock=""; 

			if($row["stock_producto"]<=10){
				
				
				$atributo = "badge bg-red-active";
					
			
			} else {

		
				$atributo = "badge bg-green";
			
			}
				 
		
			$sub_array[] = $row["nombre_categoria"];
			$sub_array[] = $row["nombre_producto"];
			$sub_array[] = "$ ".number_format($row["precio_venta_producto"],2);
			if(	$row["id_categoria"]=="9" || $row["id_categoria"]=="11" || $row["id_categoria"]=="14"){
				if($row["stock_producto"]>=1000){
					
						$stock_productos_grs=$row["stock_producto"]/1000;
					$sub_array[] = '<span class="'.$atributo.'">'.$stock_productos_grs.' Kg.	</span>';
				}
				else{
				$sub_array[] = '<span class="'.$atributo.'">'.$row["stock_producto"].' grs.</span>';}

			}else{
				$sub_array[] = '<span class="'.$atributo.'">'.$row["stock_producto"].'
				</span>';

			}

			$sub_array[] = '<button type="button" onClick="cambiarEstado('.$row["id_producto"].','.$row["estado_producto"].');" name="estado" id="'.$row["id_producto"].'" class="'.$atrib.'">'.$est.'</button>';

			if($row["imagen_producto"] != ''){
				$sub_array[] = '	<img src="upload/'.$row["imagen_producto"].'" class="img-thumbnail" width="100" height="100" /><input type="hidden" name="hidden_producto_imagen" value="'.$row["imagen_producto"].'" />	';
			}
			else{
						
				$sub_array[] = '<button type="button" id="" class="btn btn-primary btn-md"><i class="fa fa-picture-o" aria-hidden="true"></i> Sin imagen</button>';
			}
               			
			$sub_array[] = '<button type="button" name="" id="'.$row["id_producto"].'" class="btn btn-primary btn-md " onClick="agregarDetalleVenta('.$row["id_producto"].',\''.$row["nombre_producto"].'\','.$row["estado_producto"].')"><i class="fa fa-plus"></i> Agregar</button>';

			$data[] = $sub_array;
			 
			 }


      $results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 	echo json_encode($results);


     break;

    case "buscar_producto_en_venta":
          
          $datos=$productos->get_producto_por_id_estado($_POST["id_producto"], $_POST["estado"]);

            /*comprobamos que el producto esté activo, de lo contrario no lo agrega*/
	      if(is_array($datos)==true and count($datos)>0){

				foreach($datos as $row)
				{
					$output["id_producto"] = $row["id_producto"];
					$output["producto"] = $row["nombre_producto"];
					$output["precio_venta"] = $row["precio_venta_producto"];
					$output["stock"] = $row["stock_producto"];
					$output["estado"] = $row["estado_producto"];
					$output["nombre_categoria"] = $row["nombre_categoria"];
					$output["id_categoria"] = $row["id_categoria"];
					
				}
		

			} else {
                 
                 //si no existe el registro entonces no recorre el array
                 $output["error"]="El producto seleccionado está inactivo, intenta con otro";

	        }

	        echo json_encode($output);

     break;

     case "registrar_venta";
	    $id_cliente = $_POST["id_cliente"];

        //se llama al modelo Ventas.php

        require_once('../modelos/Ventas.php');

	    $venta = new Ventas();

		$venta->agrega_detalle_venta();

		
     break;
	 
  
     case "eliminar_producto":


		//verificamos si el producto existe en la bd y si el stock es igual a 0

		$datos= $productos->get_producto_por_id($_POST["id_producto"]);

		//verifica si el id_producto tiene registro asociado a detalle_compra
		$producto_detalle_compra=$productos->get_producto_por_id_detalle_compra($_POST["id_producto"]);

		//verifica si el id_producto tiene registro asociado a detalle_venta
		$producto_detalle_venta=$productos->get_producto_por_id_detalle_venta($_POST["id_producto"]);
  
          
		//si no hay productos en detalle_compras y en detalle_ventas entonces se elimina el producto
		if(is_array($datos)==true and count($datos)>0 and is_array($producto_detalle_compra)==true and count($producto_detalle_compra)==0 and is_array($producto_detalle_venta)==true and count($producto_detalle_venta)==0){

	
			$productos->eliminar_producto($_POST["id_producto"]);
			$messages[]="El producto se eliminó correctamente";

		} else {

				$errors[]="Hay stock o tienes compras o ventas realizadas o anuladas, no se puede eliminar";
		}

        

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
  	
       }

?>