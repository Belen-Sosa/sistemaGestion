var tabla;

 var tabla_en_compras;

 var tabla_en_ventas;

 //Función que se ejecuta al inicio
function init(){
	
	listar();

	 //llama la lista de productos en ventana modal en compras.php
	listar_en_compras();


	//llama la lista de productos en ventana modal en ventas.php
	listar_en_ventas();

	 //cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
	$("#producto_form").on("submit",function(e)
	{

		guardaryeditar(e);	
	})
    
    //cambia el titulo de la ventana modal cuando se da click al boton
	$("#add_button").click(function(){

		   
		    /*habilita los campos categoria, producto ya que si se 
            editaba uno que tenia registro asociado entonces al crear un nuevo registro aparecian los campos deshabilitados*/
			 $("#categoria").attr('disabled', false);
			 $("#producto").attr('disabled', false);
		
			
			
			$(".modal-title").text("Agregar Producto");
		
	  });

	
}


//Función limpiar
/*IMPORTANTE: no limpiar el campo oculto del id_usuario, sino no se registra
la categoria*/
function limpiar()
{
	
	
    $("#id_producto").val("");
    $("#categoria").val("");
	$('#producto').val("");
    $('#presentacion').val("");
    $('#unidad').val("");
	$('#precio_venta').val("");
	$('#stock').val("");
	$('#estado').val("");
	$('#procedente').hide();
	$('#producto_imagen').val("");
	$('#titulo_procedente').hide();
	$('#stock_grs').html('Stock');
	$("#stock").attr('disabled', true);
	$('#producto_uploaded_image').html("");
	
}

//Función Listar
function listar()
{
	tabla=$('#producto_data').dataTable(
	{
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
	    buttons: [		          

		          
		        ],
		"ajax":
				{
					url: '../ajax/producto.php?op=listar',
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"responsive": true,
		"bInfo":true,
		"iDisplayLength": 10,//Por cada 10 registros hace una paginación
	    "order": [[ 0, "desc" ]],//Ordenar (columna,orden)
	    
	    "language": {
 
			    "sProcessing":     "Procesando...",
			 
			    "sLengthMenu":     "Mostrar _MENU_ registros",
			 
			    "sZeroRecords":    "No se encontraron resultados",
			 
			    "sEmptyTable":     "Ningún dato disponible en esta tabla",
			 
			    "sInfo":           "Mostrando un total de _TOTAL_ registros",
			 
			    "sInfoEmpty":      "Mostrando un total de 0 registros",
			 
			    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
			 
			    "sInfoPostFix":    "",
			 
			    "sSearch":         "Buscar:",
			 
			    "sUrl":            "",
			 
			    "sInfoThousands":  ",",
			 
			    "sLoadingRecords": "Cargando...",
			 
			    "oPaginate": {
			 
			        "sFirst":    "Primero",
			 
			        "sLast":     "Último",
			 
			        "sNext":     "Siguiente",
			 
			        "sPrevious": "Anterior"
			 
			    },
			 
			    "oAria": {
			 
			        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
			 
			        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
			 
			    }

			   }//cerrando language
	       
	}).DataTable();
}

 //Mostrar datos del producto en la ventana modal 
function mostrar(id_producto)
{
	
	$.post("../ajax/producto.php?op=mostrar",{id_producto : id_producto}, function(data, status)
	{
		data = JSON.parse(data);

		
				
		  //si existe el id_producto es porque el producto tiene relacion con otras tablas
             if(data.producto_id){
				
				
				$('#productoModal').modal('show');
				$('#categoria').val(data.categoria);

			
				$('#categoria').attr('disabled', true); 
           				
                $('#producto').val(data.producto);
				$("#producto").attr('disabled', true);

           
				$('#presentacion').val(data.presentacion);
				$('#unidad').val(data.unidad);
				$('#precio_venta').val(data.precio_venta);
				$('#stock').val(data.stock);

				$('#estado').val(data.estado);
				$('.modal-title').text("Editar Producto");
				$('#id_producto').val(id_producto);
				$('#producto_uploaded_image').html(data.producto_imagen);
				$('#resultados_ajax').html(data);
				$("#producto_data").DataTable().ajax.reload();
			
				if(data.categoria==9){
					$('#procedente').show();
					$('#titulo_procedente').show();
					$('#procedente').val(data.id_procedente);
					$("#stock").attr('disabled', false);
					$('#stock_grs').html('Stock en grs.');
					if(data.id_procedente!=0){
						$("#stock").attr('disabled', false);
						}else{
							$("#stock").attr('disabled', true);
						}
				
				
					
				}else{
					$('#procedente').hide();
					$('#titulo_procedente').hide();
					$('#stock_grs').html('Stock');
				}
				

		    } else {


		    	    $('#productoModal').modal('show');
					$('#categoria').val(data.categoria);
					$('#categoria').attr('disabled', false);                 
					$('#producto').val(data.producto);
					$("#producto").attr('disabled', false);
					$('#presentacion').val(data.presentacion);
					$('#unidad').val(data.unidad);
					
					if(data.categoria==9){
						$('#procedente').show();
						$('#titulo_procedente').show();
						$('#stock_grs').html('Stock en grs.');
						$('#procedente').val(data.id_procedente);
						//es para que me deje modificar el stock solo si tiene procedente
						if(data.id_procedente!=0){
						$("#stock").attr('disabled', false);
						}else{
							$("#stock").attr('disabled', true);
						}
				
						
			
					}else{
						$('#procedente').hide();
						$('#titulo_procedente').hide();
						$('#stock_grs').html('Stock');
					}
		

					$('#precio_venta').val(data.precio_venta);
					$('#stock').val(data.stock);
					$('#estado').val(data.estado);
					$('.modal-title').text("Editar Producto");
					$('#id_producto').val(id_producto);
					$('#producto_uploaded_image').html(data.producto_imagen);
					$('#resultados_ajax').html(data);
					$("#producto_data").DataTable().ajax.reload();
					

		    }		
				
		});
		     
        
	}


	//la funcion guardaryeditar(e); se llama cuando se da click al boton submit
function guardaryeditar(e){   
	
	e.preventDefault(); //No se activará la acción predeterminada del evento
	var formData = new FormData($("#producto_form")[0]);


		$.ajax({
			url: "../ajax/producto.php?op=guardaryeditar",
		    type: "POST",
		    data: formData,
		    contentType: false,
		    processData: false,

		    success: function(datos)
		    {                    
		         
                 /*imprimir consulta en la consola debes hacer un print_r($_POST) al final del metodo 
                    y si se muestran los valores es que esta bien, y se puede imprimir la consulta desde el metodo
                    y se puede ver en la consola o desde el mensaje de alerta luego pegar la consulta en phpmyadmin*/
		         console.log(datos);

	            $('#producto_form')[0].reset();
				$('#productoModal').modal('hide');
				$('#resultados_ajax').html(datos);
				$('#producto_data').DataTable().ajax.reload();
				setTimeout('document.location.reload()',10);
                limpiar();
					
		    }

		});
       
}


//EDITAR ESTADO DEL PRODUCTO
//importante:id_categoria, est se envia por post via ajax


function cambiarEstado(id_categoria, id_producto, est){


 bootbox.confirm("¿Está Seguro de cambiar de estado?", function(result){
		if(result)
		{

   
			$.ajax({
				url:"../ajax/producto.php?op=activarydesactivar",
				 method:"POST",
				//toma el valor del id y del estado
				data:{id_categoria:id_categoria,id_producto:id_producto, est:est},
				success: function(data){
                 
                  $('#producto_data').DataTable().ajax.reload();
				
				  console.log(data);
				  console.log("aksdj");
			    
			    }

			});

			 }

		 });//bootbox



}



   //Función Listar
function listar_en_compras(){

	tabla_en_compras=$('#lista_productos_data').dataTable(
	{
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
	    buttons: [		          

		          
		        ],
		"ajax":
				{
					url: '../ajax/producto.php?op=listar_en_compras',
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"responsive": true,
		"bInfo":true,
		"iDisplayLength": 10,//Por cada 10 registros hace una paginación
	    "order": [[ 0, "desc" ]],//Ordenar (columna,orden)
	    
	    "language": {
 
			    "sProcessing":     "Procesando...",
			 
			    "sLengthMenu":     "Mostrar _MENU_ registros",
			 
			    "sZeroRecords":    "No se encontraron resultados",
			 
			    "sEmptyTable":     "Ningún dato disponible en esta tabla",
			 
			    "sInfo":           "Mostrando un total de _TOTAL_ registros",
			 
			    "sInfoEmpty":      "Mostrando un total de 0 registros",
			 
			    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
			 
			    "sInfoPostFix":    "",
			 
			    "sSearch":         "Buscar:",
			 
			    "sUrl":            "",
			 
			    "sInfoThousands":  ",",
			 
			    "sLoadingRecords": "Cargando...",
			 
			    "oPaginate": {
			 
			        "sFirst":    "Primero",
			 
			        "sLast":     "Último",
			 
			        "sNext":     "Siguiente",
			 
			        "sPrevious": "Anterior"
			 
			    },
			 
			    "oAria": {
			 
			        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
			 
			        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
			 
			    }

			   }//cerrando language
	       
	}).DataTable();
}


/*IMPORTANTE function agregarDetalle y function listarDetalles:
	Asi que detalles pertenece al arreglo detalles[]
	Para agregar elementos a un arreglo en javascript, se utiliza el metodo push()
	Puedes agregar variables u objetos, lo que yo he hecho es agregarle un objeto y ese objeto que contiene mucha informacion, que sería como una fila con muchas columnas
	Para crear un objeto de ese tipo (con columnas), se utliliza esto :
	var obj = { }
	Dentro de las llaves definas columna y valor (Todo esto para una fila)
	Lo defines asi:
	nombre_columna : valor
	El lenght 
	sirve para calcular la longitud del arreglo o el 
	numero de objetos que tiene el arreglo, que es lo mismo Y es por eso que 
	lo necesito en el for. Claro que puedes agregarle un id y name al td*/
    
    //este es un arreglo vacio
	var detalles = [];

	
	 function agregarDetalle(id_producto,producto, estado){



		        $.ajax({
					url:"../ajax/producto.php?op=buscar_producto",
					 method:"POST",
					data:{id_producto:id_producto, producto:producto, estado:estado},
					cache: false,
					dataType:"json",
					success:function(data){
                     

                     if(data.id_producto){

						if (typeof data == "string"){
						      data = $.parseJSON(data);
						}
						console.log(data);
		                
		        /*IMPORTANTE: var obj: es un objeto con mucha informacion que contiene una fila con muchas columnas
				Para crear un objeto de ese tipo (con columnas), se utliliza esto :
		        var obj = { }, Dentro de las llaves definas columna y valor (Todo esto para una fila)
				Lo defines asi:
				nombre_columna : valor 
				este var obj es un objeto que trae la informacion de la data (../ajax/producto.php?op=buscar_producto)
			        */
						var obj = {
							cantidad : 0,
							codProd  : id_producto,
							codCat   : data.id_categoria,
							producto : data.producto,
							precio   : 0,
							stock    : data.stock,
							dscto    : 0,
							importe  : 0,
							estado   : data.estado,
							nombre_categoria: data.nombre_categoria,
							id_categoria: data.id_categoria
						};
		                
		 /*IMPORTANTE: detalles.push(obj);: Para agregar elementos a un arreglo en javascript, se utiliza el metodo push()
			Puedes agregar variables u objetos, lo que yo he hechos es agregarle un objeto y ese objeto que contiene mucha informacion,
		    el detalles de detalles.push(obj); viene de detalles = [], una vez se agrega el objeto al arreglo entonces se llama a la function listarDetalles(); 
			*/
						detalles.push(obj);
						listarDetalles();

						$('#lista_productosModal').modal("hide");

                       }//if validacion id_producto

                        else {

                        	 //si el producto está inactivo entonces se muestra una ventana modal

                        	  bootbox.alert(data.error);
							  $('#lista_productosModal').modal("hide");
                        }
						
					}//fin success		

				});//fin de ajax
			
		    
		  }// fin de funcion

  
//***********************************************************************

 /*IMPORTANTE: El lenght 
	sirve para calcular la longitud del arreglo o el 
	numero de objetos que tiene el arreglo, que es lo mismo Y es por eso que 
	lo necesito en el for*/



  function listarDetalles(){

  	  
  	$('#listProdCompras').html('');

  

  	var filas = "";
  	
  	var subtotal ;


  	var totalFinal = 0;


      

  	 for(var i=0; i<detalles.length; i++){
		if( detalles[i].estado == 1 ){
			if(detalles[i].id_categoria == "9" ||  detalles[i].id_categoria=="11" || detalles[i].id_categoria== "14" ){
			
			


				var importe = detalles[i].importe = ((detalles[i].precio)/1000)*detalles[i].cantidad ;		
				importe = detalles[i].importe = detalles[i].importe - (detalles[i].importe * detalles[i].dscto/100);
				if(detalles[i].stock>=1000){
				$stock_grs=detalles[i].stock/1000;
				$stock_grs=$stock_grs+" Kg. ";
				}else{
					$stock_grs=detalles[i].stock+" Grs. ";
				}
				var filas = filas + "<tr><td>"+(i+1)+"</td> <td name='producto[]'>"+detalles[i].producto+"</td> <td><input name='precio[]' id='precio[]'' value='"+detalles[i].precio+"' onClick='setPrecioCompra(event, this, "+(i)+");'  onKeyUp='setPrecioCompra(event, this, "+(i)+");'></td> <td> "+$stock_grs+"</td> <td><input type='number' class='cantidad input-group-sm' name='cantidad[]' id='cantidad[]' onClick='setCantidad(event, this, "+(i)+");' onKeyUp='setCantidad(event, this, "+(i)+");' value='"+detalles[i].cantidad+"'>grs.  </td> <td><input type='number' name='descuento[]' required id='descuento[]' onClick='setDescuento(event, this, "+(i)+");' onKeyUp='setDescuento(event, this, "+(i)+");' value='"+detalles[i].dscto+"'></td> <td> <span name='importe[]'  id='importe"+i+"'>$ "+detalles[i].importe+"</span> </td> <td>  <button href='#' class='btn btn-danger btn-lg' role='button' onClick='eliminarProdCompras(event, "+(i)+");' aria-pressed='true'><span class='glyphicon glyphicon-trash'></span> </button></td> </tr>";
				
				
			
				
			}
			else{
		
			
		
				var importe =detalles[i].importe = detalles[i].cantidad * detalles[i].precio;
				importe = detalles[i].importe = detalles[i].importe - (detalles[i].importe * detalles[i].dscto/100);
				var filas = filas + "<tr><td>"+(i+1)+"</td> <td name='producto[]'>"+detalles[i].producto+"</td> <td><input name='precio[]' id='precio[]' ' value='"+detalles[i].precio+"' onClick='setPrecioCompra(event, this, "+(i)+");'  onKeyUp='setPrecioCompra(event, this, "+(i)+");'></td> <td>"+detalles[i].stock+"</td> <td><input type='number' class='cantidad input-group-sm' name='cantidad[]' id='cantidad[]' onClick='setCantidad(event, this, "+(i)+");' onKeyUp='setCantidad(event, this, "+(i)+");' value='"+detalles[i].cantidad+"'></td>  <td><input type='number' name='descuento[]'required id='descuento[]' onClick='setDescuento(event, this, "+(i)+");' onKeyUp='setDescuento(event, this, "+(i)+");' value='"+detalles[i].dscto+"'></td> <td> <span name='importe[]' id='importe"+i+"'>$ "+detalles[i].importe+"</span> </td> <td>  <button href='#' class='btn btn-danger btn-lg' role='button' onClick='eliminarProdCompras(event, "+(i)+");' aria-pressed='true'><span class='glyphicon glyphicon-trash'></span> </button></td> </tr>";
		
		
			}
			if(isNaN(importe) || importe=="NaN"){
				importe=0;
			
			   }
		
	
				 	subtotal = subtotal + importe; 
				 	
				 		if(isNaN(	subtotal) || 	subtotal=="NaN"){
					subtotal=0;
				
			   }
	
				 	
               totalFinal = "$ "+subtotal.toFixed(2);
               


          

		}
	



	}
	

	 
	
	
	$('#listProdCompras').html(filas);

	//total
	$('#total').html(totalFinal);
	$('#total_compra').html(totalFinal);


      
  }



/*IMPORTANTE:Event es el objeto del evento que los manejadores de eventos utilizan
parseInt es una función para convertir un valor string a entero
obj.value es el valor del campo de texto*/
  function setCantidad(event, obj, idx){
  	event.preventDefault();
  	detalles[idx].cantidad = parseInt(obj.value);
  	recalcular(idx);
  }
  function setDescuento(event, obj, idx){
  	event.preventDefault();
	
if (parseInt(obj.value)==0 || parseInt(obj.value)==null || parseInt(obj.value)==NaN){
detalles[idx].dscto =0;
}else{
detalles[idx].dscto = parseInt(obj.value);
}
  	
  	recalcular(idx);
  }
  function setPrecioCompra(event, obj, idx){
	
	event.preventDefault();

	detalles[idx].precio = parseFloat(obj.value);
	
	recalcular(idx);
}

  	
  function recalcular(idx){

	if(detalles[idx].id_categoria == "9" ||  detalles[idx].id_categoria=="11" || detalles[idx].id_categoria== "14" ){
		
		var importe =(detalles[idx].importe = detalles[idx].precio/1000)*detalles[idx].cantidad ;
		importe = (detalles[idx].importe = detalles[idx].importe - detalles[idx].importe * detalles[idx].dscto/100);
		
		
	}
	else{

	

		var importe =detalles[idx].importe = detalles[idx].cantidad * detalles[idx].precio;
		importe = detalles[idx].importe = detalles[idx].importe - (detalles[idx].importe * detalles[idx].dscto/100);
		


	}
	if(isNaN(importe)){
		importe=0;
	   }
  	

	
  	


  	importeFinal ="$ "+importe.toFixed(2);
	  

  	$('#importe'+idx).html(importeFinal);
	
  	calcularTotales();
  }

  function calcularTotales(){
 
	var subtotal = 0;

	var total = 0;

  var subtotalFinal = 0;

	var totalFinal = 0;

 
  for(var i=0; i<detalles.length; i++){
		if(detalles[i].estado == 1){
			if(detalles[i].id_categoria == "9" ||  detalles[i].id_categoria=="11" || detalles[i].id_categoria== "14" ){
		
			
	
				subtotal = (subtotal + detalles[i].precio/1000)*detalles[i].cantidad  - (detalles[i].cantidad*detalles[i].precio*detalles[i].dscto/100);
				
			}
			else{
				subtotal = (subtotal + detalles[i].cantidad * detalles[i].precio) - (detalles[i].cantidad*detalles[i].precio*detalles[i].dscto/100);
			}
			if(isNaN(subtotal) || subtotal=="NaN"){
				subtotal=0;
			   }
	  
				var total= subtotal.toFixed(2);
			
				totalFinal = "$ "+total;
	  
			
		 
		
	  }
  }
 

  

  //subtotal
  $('#subtotal').html(subtotalFinal);
  $('#subtotal_compra').html(subtotalFinal);

  //total
  $('#total').html(totalFinal);
  $('#total_compra').html(totalFinal);
  }


  //*******************************************************************
    /*IMPORTANTE:Event es el objeto del evento que los manejadores de eventos utilizan
parseInt es una función para convertir un valor string a entero
obj.value es el valor del campo de texto*/

  	function  eliminarProdCompras(event, idx){
  		event.preventDefault();
  		//console.log('ELIMINAR EYTER');
		  detalles[idx].estado = 0;
		  
		  /*con el splice se elimina el registro del array detalles*/
		   if(detalles[idx].estado == 0){
			  
			   detalles.splice(idx,1);
		   }

  		listarDetalles();
  	}

 //********************************************************************


 function registrarCompra(){
    
    /*IMPORTANTE: se declaran las variables ya que se usan en el data, sino da error*/
    var numero_compra = $("#numero_compra").val();
    var cuit = $("#cuit").val();
    var razon = $("#razon").val();
    var direccion = $("#direccion").val();
    var total = $("#total").html();
    var comprador = $("#comprador").html();
    var tipo_pago = $("#tipo_pago").val();
    var id_usuario = $("#id_usuario").val();
    var id_proveedor = $("#id_proveedor").val();


    //validamos, si los campos(proveedor) estan vacios entonces no se envia el formulario

    if(cuit!="" && razon!="" && direccion!="" && tipo_pago!="" && detalles!=""){


    /*IMPORTANTE: el array detalles de la data viene de var detalles = []; esta vacio pero como ya se usó en la function agregarDetalle(id_producto,producto)
    se reusa, pero ya viene cargado con la informacion que se va a enviar con ajax*/
    $.ajax({
		url:"../ajax/producto.php?op=registrar_compra",
		method:"POST",
		data:{'arrayCompra':JSON.stringify(detalles), 'numero_compra':numero_compra,'cuit':cuit,'razon':razon,'direccion':direccion,'total':total,'comprador':comprador,'tipo_pago':tipo_pago,'id_usuario':id_usuario,'id_proveedor':id_proveedor},
		cache: false,
		dataType:"html",
		error:function(x,y,z){
			console.log(x);
			console.log(y);
			console.log(z);
		},
         
         
			
		success:function(data){
			//IMPORTANTE: esta se descomenta cuando imprimo el console.log
		
		
			var cuit = $("#cuit").val("");
		    var razon = $("#razon").val("");
		    var direccion = $("#direccion").val("");
		    var subtotal = $("#subtotal").html("");
		    var total = $("#total").html("");
		   
            
            detalles = [];
            $('#listProdCompras').html('');



          
          //muestra un mensaje de exito
          setTimeout ("bootbox.alert('Se ha registrado la compra con éxito');", 100); 
          
          //refresca la pagina, se llama a la funtion explode
          setTimeout ("explode();", 2000); 

         	
		}


	});	

	 //cierre del condicional de validacion de los campos del producto,proveedor,pago

	 } else{

	 	 bootbox.alert("Debe agregar un producto, los campos del proveedor y el tipo de pago");
	 	 return false;
	 } 	
	
  }


  //*****************************************************************************
 /*RESFRESCA LA PAGINA DESPUES DE REGISTRAR LA COMPRA*/
       function explode(){

	    location.reload();
}


/********VENTAS***********************************************/

 //BUSCA LOS PRODUCTOS EN VENTANA MODAL EN VENTAS
  
function listar_en_ventas(){

	tabla_en_ventas=$('#lista_productos_ventas_data').dataTable(
	{
		"aProcessing": true,//Activamos el procesamiento del datatables
	    "aServerSide": true,//Paginación y filtrado realizados por el servidor
	    dom: 'Bfrtip',//Definimos los elementos del control de tabla
	    buttons: [		          
	
		           
		        ],
		"ajax":
				{
					url: '../ajax/producto.php?op=listar_en_ventas',
					type : "get",
					dataType : "json",						
					error: function(e){
						console.log(e.responseText);	
					}
				},
		"bDestroy": true,
		"responsive": true,
		"bInfo":true,
		"iDisplayLength": 10,//Por cada 10 registros hace una paginación
	    "order": [[ 0, "desc" ]],//Ordenar (columna,orden)
	    
	    "language": {
 
			    "sProcessing":     "Procesando...",
			 
			    "sLengthMenu":     "Mostrar _MENU_ registros",
			 
			    "sZeroRecords":    "No se encontraron resultados",
			 
			    "sEmptyTable":     "Ningún dato disponible en esta tabla",
			 
			    "sInfo":           "Mostrando un total de _TOTAL_ registros",
			 
			    "sInfoEmpty":      "Mostrando un total de 0 registros",
			 
			    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
			 
			    "sInfoPostFix":    "",
			 
			    "sSearch":         "Buscar:",
			 
			    "sUrl":            "",
			 
			    "sInfoThousands":  ",",
			 
			    "sLoadingRecords": "Cargando...",
			 
			    "oPaginate": {
			 
			        "sFirst":    "Primero",
			 
			        "sLast":     "Último",
			 
			        "sNext":     "Siguiente",
			 
			        "sPrevious": "Anterior"
			 
			    },
			 
			    "oAria": {
			 
			        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
			 
			        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
			 
			    }

			   }//cerrando language
	       
	}).DataTable();
}


 //CARGAR PRODUCTO, PRECIO, CANTIDAD, IGV, IMPORTE EN VENTAS


	/*IMPORTANTE function agregarDetalleVenta y function listarDetalles:
	Asi que detalles pertenece al arreglo detalles[]
	Para agregar elementos a un arreglo en javascript, se utiliza el metodo push()
	Puedes agregar variables u objetos, lo que yo he hechos es agregarle un objeto y ese objeto que contiene mucha informacion, que sería como una fila con muchas columnas
	Para crear un objeto de ese tipo (con columnas), se utliliza esto :
	var obj = { }
	Dentro de las llaves definas columna y valor (Todo esto para una fila)
	Lo defines asi:
	nombre_columna : valor
	El lenght 
	sirve para calcular la longitud del arreglo o el 
	numero de objetos que tiene el arreglo, que es lo mismo Y es por eso que 
	lo necesito en el for. Claro que puedes agregarle un id y name al td*/
   
	var detalles = [];

	
	 function agregarDetalleVenta(id_producto,producto,estado){
		        $.ajax({
					url:"../ajax/producto.php?op=buscar_producto_en_venta",
					 method:"POST",
					data:{id_producto:id_producto, producto:producto,estado:estado},
					cache: false,
					dataType:"json",

					success:function(data){
                        
                       if(data.id_producto){

						if (typeof data == "string"){
						      data = $.parseJSON(data);
						}
						console.log(data);
					
		                
		        /*IMPORTANTE: var obj: es un objeto con mucha informacion que contiene una fila con muchas columnas
				Para crear un objeto de ese tipo (con columnas), se utliliza esto :
		        var obj = { }, Dentro de las llaves definas columna y valor (Todo esto para una fila)
				Lo defines asi:
				nombre_columna : valor 
				este var obj es un objeto que trae la informacion de la data (ajax/buscar_precio_compra.php)
			        */
						var obj = {
							cantidad : 0,
							codProd  : id_producto,
							producto : data.producto,
							precio   : data.precio_venta,
							stock    : data.stock,
							dscto    : 0,
							importe  : 0,
							estado   : data.estado,
							nombre_categoria: data.nombre_categoria,
							id_categoria: data.id_categoria
						};
		                
		 /*IMPORTANTE: detalles.push(obj);: Para agregar elementos a un arreglo en javascript, se utiliza el metodo push()
			Puedes agregar variables u objetos, lo que yo he hechos es agregarle un objeto y ese objeto que contiene mucha informacion,
		    el detalles de detalles.push(obj); viene de detalles = [], una vez se agrega el objeto al arreglo entonces se llama a la function listarDetalles(); 
			*/
						detalles.push(obj);
						listarDetallesVentas();

						$('#lista_productos_ventas_Modal').modal("hide");

						//esconde el mensaje de alerta del stock

						 }//if validacion id_producto

                        else {

                        	 //si el producto está inactivo entonces se muestra una ventana modal

                        	  bootbox.alert(data.error);
							  $('#lista_productos_ventas_Modal').modal("hide");

                        }
                     
                     
						
					}//fin success		

				});//fin de ajax
			
		    
		  }// fin de funcion



//***********************************************************************
  
  /*IMPORTANTE: El lenght 
	sirve para calcular la longitud del arreglo o el 
	numero de objetos que tiene el arreglo, que es lo mismo Y es por eso que 
	lo necesito en el for*/

  function listarDetallesVentas(){
  	$('#listProdVentas').html('');
  	var filas = "";
  	
  	var subtotal = 0;

    var subtotalFinal = 0;

  	var totalFinal = 0;


  	for(var i=0; i<detalles.length; i++){
		if(detalles[i].estado == 1){
			if(detalles[i].id_categoria == "9" ||  detalles[i].id_categoria=="11" || detalles[i].id_categoria== "14" ){
			
			


				var importe = detalles[i].importe = (detalles[i].precio/1000)*detalles[i].cantidad ;		
				//aplico descuento
				importe = detalles[i].importe = detalles[i].importe - (detalles[i].importe * detalles[i].dscto/100);
				if(detalles[i].stock>=1000){
					$stock_grs=detalles[i].stock/1000;
					$stock_grs=$stock_grs+" Kg. ";
					}else{
						$stock_grs=detalles[i].stock+" Grs. ";
					}
				
			
				var filas = filas + "<tr><td>"+(i+1)+"</td> <td name='producto[]'>"+detalles[i].producto+"</td> <td name='precio[]' id='precio[]'>$ "+detalles[i].precio+"</td> <td>"+$stock_grs+" </td> <td> <input type='text' class='cantidad' name='cantidad[]' id=cantidad_"+i+" onClick='setCantidad(event, this, "+(i)+");' onKeyUp='setCantidadAjax(event, this, "+(i)+");' value='"+detalles[i].cantidad+"'>  grs.</td>  <td><input type='text' name='descuento[]' id='descuento[]' required -onClick='setDescuento(event, this, "+(i)+");' onKeyUp='setDescuento(event, this, "+(i)+");' value='"+detalles[i].dscto+"'></td> <td> <span name='importe[]' id=importe"+i+">$ "+detalles[i].importe+"</span> </td> <td>  <button href='#' class='btn btn-danger btn-lg' role='button' onClick='eliminarProd(event, "+(i)+");' aria-pressed='true'><span class='glyphicon glyphicon-trash'></span> </button></td>   </tr>";
			}
			else{
		
			
		
				var importe =detalles[i].importe = detalles[i].cantidad * detalles[i].precio;
				importe =detalles[i].importe = detalles[i].importe - (detalles[i].importe * detalles[i].dscto/100);
				var filas = filas + "<tr><td>"+(i+1)+"</td> <td name='producto[]'>"+detalles[i].producto+"</td> <td name='precio[]' id='precio[]'>$ "+detalles[i].precio+"</td> <td>"+detalles[i].stock+"</td> <td> <input type='text' class='cantidad' name='cantidad[]' id=cantidad_"+i+" onClick='setCantidad(event, this, "+(i)+");' onKeyUp='setCantidadAjax(event, this, "+(i)+");' value='"+detalles[i].cantidad+"'> </td>  <td><input type='text' name='descuento[]' id='descuento[]' required onClick='setDescuento(event, this, "+(i)+");' onKeyUp='setDescuento(event, this, "+(i)+");' value='"+detalles[i].dscto+"'></td> <td> <span name='importe[]' id=importe"+i+">$ "+detalles[i].importe+"</span> </td> <td>  <button href='#' class='btn btn-danger btn-lg' role='button' onClick='eliminarProd(event, "+(i)+");' aria-pressed='true'><span class='glyphicon glyphicon-trash'></span> </button></td>   </tr>";
		
		
			}
       
    
			



         
       

		}//cierre if
		subtotal = subtotal + importe;
            
	}//cierre for

	   totalFinal = "$ "+subtotal;
	$('#listProdVentas').html(filas);

	//subtotal
	$('#subtotal').html(subtotalFinal);
	$('#subtotal_venta').html(subtotalFinal);

	//total
	$('#total').html(totalFinal);
	$('#total_venta').html(totalFinal);

  }

  /*IMPORTANTE:Event es el objeto del evento que los manejadores de eventos utilizan
parseInt es una función para convertir un valor string a entero
obj.value es el valor del campo de texto*/
function setCantidad(event, obj, idx){
	console.log("set cantidad =  idx"+idx+" event"+event+" obj"+obj)
  	event.preventDefault();
	
  	detalles[idx].cantidad = parseInt(obj.value);
  	recalcular(idx);
  }
 
  function setCantidadAjax(event, obj, idx){
  	event.preventDefault();


  	var id_producto = detalles[idx].codProd;
  	var cantidad_vender = detalles[idx].cantidad = parseInt(obj.value);
    var stock = detalles[idx].stock;   
  	
       $.ajax({
         
         url:"../ajax/ventas.php?op=consulta_cantidad_venta",
         method:"POST",
         data:{id_producto:id_producto, cantidad_vender:cantidad_vender},
         dataType:"json",

         success:function(data){

            
              $("#resultados_ventas_ajax").html(data);

                //se pone isNaN porque al ser vacio indica que no es un numero, entonces si valida que es cierto entonces se desabilita el boton del envio del formulario y de agregar productos
                /*si la cantidad a vender es igual a cero o a vacio o si es mayor al stock entonces se desabilita el boton de enviar formulario y de agregar productos*/
	             if(cantidad_vender=="0" || isNaN(cantidad_vender)==true || cantidad_vender>stock){
	             
	            
	             //si la cantidad es mayor al stock el borde se pone en rojo
	             $("#cantidad_"+idx).addClass("rojo");

	             //bloquea el boton "agregar producto"
	             $(".btn_producto").removeAttr("data-target");

	            //oculta el boton "enviar formulario"

	             $("#btn_enviar").addClass("oculta_boton");

	                

                     

	            } else {
                     
                       
                     // si la cantidad seleccionada es menor al stock entonces remueve la clase rojo
	              	 $("#cantidad_"+idx).removeClass("rojo");

	              	 //Desbloquea el boton "agregar producto"
	                 $(".btn_producto").attr({"data-target":"#lista_productos_ventas_Modal"});
	
                      //aparece el boton "enviar formulario"

	                 $("#btn_enviar").removeClass("oculta_boton");
	              }
         }

       })


  	recalcular(idx);
  }
  

 

  function setDescuento(event, obj, idx){
  	event.preventDefault();
	


detalles[idx].dscto = parseInt(obj.value);

  	
  	recalcular(idx);
  }
  	
  function recalcular(idx){
	console.log(detalles);
	console.log(idx);
	if(detalles[idx].id_categoria == "9" ||  detalles[idx].id_categoria=="11" || detalles[idx].id_categoria== "14" ){
		
		var importe =detalles[idx].importe = (detalles[idx].precio/1000)*detalles[idx].cantidad ;
		importe = detalles[idx].importe = detalles[idx].importe - (detalles[idx].importe * detalles[idx].dscto/100);
		
		
	}
	else{

	

		var importe =detalles[idx].importe = detalles[idx].cantidad * detalles[idx].precio;
		importe = detalles[idx].importe = detalles[idx].importe - (detalles[idx].importe * detalles[idx].dscto/100);
		


	}
	if(isNaN(importe)){
		importe=0;
	   }
  	


  	importeFinal ="$ "+importe;
   
  	$('#importe'+idx).html(importeFinal);
  	calcularTotales();
  	



	  
  }



  function calcularTotales(){
	 
	var  subtotal = 0;

	var total = 0;

  var subtotalFinal = 0;

	var totalFinal = 0;

 
  for(var i=0; i<detalles.length; i++){
		if(detalles[i].estado == 1){
			if(detalles[i].id_categoria == "9" ||  detalles[i].id_categoria=="11" || detalles[i].id_categoria== "14" ){
		
				var precioxkilo=(detalles[i].precio/1000)*detalles[i].cantidad ;
	
				subtotal = subtotal+precioxkilo -(precioxkilo *detalles[i].dscto/100);
				
			}
			else{
				subtotal = subtotal + (detalles[i].cantidad * detalles[i].precio) - (detalles[i].cantidad*detalles[i].precio*detalles[i].dscto/100);
			}
			if(isNaN(subtotal)){
				subtotal=0;
			   }
	
	  
				var total= subtotal.toFixed(2);
			
				totalFinal = "$ "+total;
	  
			
		 
		
	  }
  }
  



  

  //subtotal
  $('#subtotal').html(subtotalFinal);
  $('#subtotal_compra').html(subtotalFinal);
  //total
  $('#total').html(totalFinal);
  $('#total_compra').html(totalFinal);
  }


  //*******************************************************************
    /*IMPORTANTE:Event es el objeto del evento que los manejadores de eventos utilizan
parseInt es una función para convertir un valor string a entero
obj.value es el valor del campo de texto*/

  	function  eliminarProd(event, idx){
  		event.preventDefault();

  		detalles[idx].estado = 0;
			 
		   /*con el splice se elimina el registro del array detalles*/
		 if(detalles[idx].estado == 0){
			  
			detalles.splice(idx,1);
		}
  		listarDetallesVentas();
  	}



 //********************************************************************
 


/* {'arrayCompra':JSON.stringify(detalles)}:Esa parte encapsula el arreglo detalles y lo envía como un solo parametro

*/

 function registrarVenta() {
    
    /*IMPORTANTE: se declaran las variables ya que se usan en el data, sino da error*/
    var numero_venta = $("#numero_venta").val();
    var dni = $("#dni").val();
    var nombre = $("#nombre").val();
    var apellido = $("#apellido").val();
    var direccion = $("#direccion").val();
    var total = $("#total").html();
    var vendedor = $("#vendedor").html();
    var tipo_pago = $("#tipo_pago").val();
    var id_usuario = $("#id_usuario").val();
    var id_cliente = $("#id_cliente").val();
	console.log("id_cliente:"+id_cliente)
    
	var cuenta_corriente_habilitada=""; 	

	//buscamos el estado de su cuenta corriente 
	//0=DESCATIVADA ,1 =ACTIVADA 
	
	$.ajax({
		url:"../ajax/cuenta_corriente.php?op=ver_estado",
		method:"GET",
		data:{id_cliente:id_cliente},
		async:false,
		success:function(data){
		data = JSON.parse(data);

		cuenta_corriente_habilitada=data.estado_cc;


	}});


    if(dni!="" && nombre!="" && apellido!="" && direccion!="" && tipo_pago!="" && detalles!=""){
		
		if(tipo_pago=="CUENTA CORRIENTE" &&   cuenta_corriente_habilitada==0){
			bootbox.alert("El Cliente no tiene Cuenta Corriente habilitada");
			return false;
		}else if(tipo_pago=="CUENTA CORRIENTE" &&   cuenta_corriente_habilitada==2){
			bootbox.alert("El Cliente no tiene creada una Cuenta Corriente");
			return false;
		}
		else  {
			
			
	

		/*IMPORTANTE: el array detalles de la data viene de var detalles = []; esta vacio pero como ya se usó en la function agregarDetalle(id_producto,producto)
		se reusa, pero ya viene cargado con la informacion que se va a enviar con ajax*/
		$.ajax({
			url:"../ajax/producto.php?op=registrar_venta",
			method:"POST",
			data:{'arrayVenta':JSON.stringify(detalles), 'numero_venta':numero_venta,'dni':dni,'nombre':nombre, 'apellido':apellido,'direccion':direccion,'total':total,'vendedor':vendedor,'tipo_pago':tipo_pago,'id_usuario':id_usuario,'id_cliente':id_cliente},
			cache: false,
			dataType:"html",
			error:function(x,y,z){
				console.log(x);
				console.log(y);
				console.log(z);
			},  
       
      
			
		success:function(data){
			

			if(tipo_pago=="CUENTA CORRIENTE"   &&   cuenta_corriente_habilitada==1){
		

			
					$.ajax({
							url:"../ajax/cuenta_corriente.php?op=registrar_detalle_cc",
							method:"POST",
							data:{'numero_venta':numero_venta,'dni':dni,'tipo_pago':tipo_pago,'id_usuario':id_usuario,'id_cliente':id_cliente,"descripcion":"adeuda","tipo_movimiento":"f"},
							cache: false,
							dataType:"html",
							error:function(x,y,z){
								console.log(x);
								console.log(y);
								console.log(z);
							},  
					
				
						
					success:function(data){
						
						
				
			
						//IMPORTANTE:limpia los campos despues de enviarse
						//cuando se imprime el alert(data) estas variables deben comentarse
						var dni = $("#dni").val("");
						var nombre = $("#nombre").val("");
						var apellido = $("#apellido").val("");
						var direccion = $("#direccion").val("");
						var total = $("#total").html("");
						
						detalles = [];
						$('#listProdVentas').html('');
						//muestra un mensaje de exito
						setTimeout ("bootbox.alert('Se ha registrado a cuenta corriente');", 100); 
						//refresca la pagina, se llama a la funtion explode
						setTimeout ("explode();", 2000); 
						
				
					}	}); 
			}
			console.log(data);

			//IMPORTANTE:limpia los campos despues de enviarse
			//cuando se imprime el alert(data) estas variables deben comentarse
			var dni = $("#dni").val("");
			var nombre = $("#nombre").val("");
			var apellido = $("#apellido").val("");
			var direccion = $("#direccion").val("");
			var total = $("#total").html("");
		
			
			detalles = [];
			$('#listProdVentas').html('');
			//muestra un mensaje de exito
            setTimeout ("bootbox.alert('Se ha registrado la venta');", 100); 
		
			//refresca la pagina, se llama a la funtion explode
			setTimeout ("explode();", 2000); 
       
		}

	});	

	
      } 
	 } else{

	 	 bootbox.alert("Debe agregar un producto, los campos del cliente y el tipo de pago");
	 	 return false;
	 } 	
	
  }

   

  //*****************************************************************************
   /*RESFRESCA LA PAGINA DESPUES DE REGISTRAR LA VENTA*/
function explode(){

 location.reload();
}


 //ELIMINAR PRODUCTOS

function eliminar(id_producto){

   
	bootbox.confirm("¿Está Seguro de eliminar el producto?", function(result){
	if(result)
	{

			$.ajax({
				url:"../ajax/producto.php?op=eliminar_producto",
				method:"POST",
				data:{id_producto:id_producto},

				success:function(data)
				{
				
					$("#resultados_ajax").html(data);
					$("#producto_data").DataTable().ajax.reload();
				}
			});

		}

	});//bootbox


}

   //activar o desactivar campo stock en formulario de carniceria 
  


function habilitar_stock(id_procedente){

	d = id_procedente.value;
	console.log(d);
	if(d != "0"){
		$('#stock').attr('disabled', false);
			
     	//muestra un mensaje de exito

	}else{
		$('#stock').attr('disabled', true);

		
	}
	
}

  $("#categoria").change(function() {
	
	var categoria = $(this).find('option:selected').text(); // Capturamos el texto del option seleccionado
	
    if(categoria=="Carniceria" || categoria=="Carnicería"  || categoria=="carniceria" || ategoria=="carnicería" ){
		$('#procedente').show();
		$('#titulo_procedente').show();
		$('#stock_grs').html('Stock en Grs.');

	}
	if(categoria=="Fiambres" || categoria=="Quesos" || categoria=="Carniceria" ){

		$('#stock_grs').html('Stock en Grs.');

	}
	else{
		
		$('#procedente').hide();
		$('#titulo_procedente').hide();
		$('#stock').attr('disabled', true);
		$('#stock_grs').html('Stock');	
		
	}
	
	
  });
 


 init();


