<?php


   
   require_once("../config/conexion.php");

   if(isset($_SESSION["id_usuario"])){

   	require_once("../modelos/Compras.php");

   	 
//SI EXISTE EL POST ENTONCES SE LLAMA AL METODO PARA SELECCIONAR LA FECHA
     
    $compras=new Compras();
	$año=date("Y");
	$mes=date("n");
	$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
	$nombre_mes= $meses[$mes-1];


   	if(isset($_POST["year"])){
   

      $datos= $compras->get_compras_mensual($_POST["year"],$_POST["mes"]);  
 
    } else {

  
        $datos= $compras->get_compras_mensual($año,$mes);  
    }


    $fecha_compras_año= $compras->get_year_compras();
	$fecha_compras_mes= $compras->get_mes_compras();
?>


<!-- INICIO DEL HEADER - LIBRERIAS -->
<?php require_once("header.php");?>

<!-- FIN DEL HEADER - LIBRERIAS -->



  <?php if($_SESSION["reporte_compras"]==1)
     {

     ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

   <h2 id="titulo" class="reporte_compras_general container-fluid bg-red text-white col-lg-12 text-center mh-50">
        
         REPORTE DE COMPRAS MENSUAL
    </h2>


   <div id="panel_imprimir" class="panel panel-default">
        
        <div class="panel-body">

         <div class="btn-group text-center">
          <button type='button' id="buttonExport" class="btn btn-primary btn-lg" ><i class="fa fa-print" aria-hidden="true"></i> Imprimir</button>
         </div>


       </div>
      </div>

   <div id="panel_form"class="panel panel-default">
        
        <div class="panel-body">

            <form  action="reporte_compras_mensual.php" class="form-inline" method="post">

            
              <div class="form-group">
                <!--<label for="staticEmail" class="col-sm-2 col-form-label">Año</label>-->
                 <div class="col-sm-10">
                  <select class="form-control" name="mes" id="mes">
	
						 <option value="0">Enero</option>
						 <option value="1">Febrero</option>
						 <option value="2">Marzo</option>
						 <option value="3">Abril</option>
						 <option value="4">Mayo</option>
						 <option value="5">Junio</option>
						 <option value="6">Julio</option>
						 <option value="7">Agosto</option>
						 <option value="8">Septiembre.</option>
						 <option value="9">Octubre</option>
						 <option value="10">Nombiembre</option>
						 <option value="11">Diciembre</option>
						
					 </select>
                 </div>
              </div>
			  <div class="form-group">
                <!--<label for="staticEmail" class="col-sm-2 col-form-label">Año</label>-->
                 <div class="col-sm-10">
                  <select class="form-control" name="year" id="year">
						 <option value="0">Seleccione...</option>
						 <?php
                       
                       //si se envia el POST
                       if(isset($_POST["year"])){
                          

						 for($i=0; $i<count($fecha_compras_año); $i++){

                        
                          	if($fecha_compras_año[$i]["año"]==$_POST["year"]){


                          	echo '<option value="'.$fecha_compras_año[$i]["año"].'" selected=selected>'.$fecha_compras_año[$i]["año"].'</option>';
                                  
       	      
                            } else{ 

                            	  /*despues de enviarse entonces se alistan los años*/

                                   	echo '<option value="'.$fecha_compras_año[$i]["año"].'">'.$fecha_compras_año[$i]["año"].'</option>';
                             } 

                        }//cierre del ciclo for

                       
                       //SI NO SE ENVIA EL POST
                       } else {


						 for ($i=0; $i<count($fecha_compras_año); $i++){

                     /*si no se envia entonces se alistan los años*/

                    echo '<option value="'. $fecha_compras_año[$i]["año"].'" selected=selected>'. $fecha_compras_año[$i]["año"].'</option>';
                           
						 //}          

                        }//cierre del ciclo for

                      }//cierre del ese*/

					  ?>
						 
					 </select>
                 </div>
              </div>

             

               <div class="btn-group text-center">
                 <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> Consultar</button>
               </div>
           </form>

       </div>
      </div>

  

	<div class="row">

	 <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">

	    <div class="box">

	       <div class="">

				  <h2 class="reporte_compras_general container-fluid bg-primary text-white col-lg-12 text-center mh-50">REPORTE DE COMPRAS MENSUAL</h2>
			  <h3 class="col-lg-12 ">  AÑO:  <?php if(isset($_POST["year"])){ echo" ".$_POST["year"];}else{ echo date("Y");} ?></h3>
				  <h3 class="col-lg-12">  MES: <?php if(isset($_POST["mes"])){ echo" ".$meses[$_POST["mes"]];}else{ echo" ".$meses[date("n")-1];} ?></h3>
				              
				  <table class="table table-bordered">
				    <thead>
				      <tr>
					
				        
				        <th>TIPO DE PAGO</th>
						<th>TOTAL</th>
						<th>ESTADO</th>
				      </tr>
				    </thead>

				     <tbody>

				     
                   <?php


			   	  //si existe el envia del post entonces se llama al metodo
			   	  if(isset($_POST["year"])&&isset($_POST["mes"])){
			    
				//SI EXISTE EL POST ENTONCES SE LLAMA AL METODO
			     
			      $datos= $compras->get_compras_mensual($_POST["year"],$_POST["mes"]); 

				    
                    for($i=0;$i<count($datos);$i++){

                    //imprime la fecha por separado ejemplo: dia, mes y año
                      $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
 
                       $fecha= $datos[$i]["mes"];
					   $fecha_mes =$meses[$datos[$i]["numero_mes"]-1] ;   
					   $estado=$datos[$i]["estado_compra"];
					   if($estado==1){
						$estado="PAGADOS";

					   }elseif($estado==2){
						$estado="PENDIENTES";
					   }elseif($estado==0){
						$estado="ANULADOS";
					   }



    			     ?>


					      <tr>
					      
						  <td><?php echo $datos[$i]["tipo_pago_compra"]?></td>
                            <td><?php echo "$ ".number_format($datos[$i]["total_compra"],2)?></td>
							<td><?php echo $estado?></td>
					      </tr>
					      
				      <?php

                       
                        }//cierre del for

                     

                     } else {


                     //SI NO EXISTE EL POST ENTONCES SE LLAMA AL METODO

                     
			           $datos= $compras->get_compras_mensual($año,$mes);  

				    
                        for($i=0;$i<count($datos);$i++){

                        //imprime la fecha por separado ejemplo: dia, mes y año
                      $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
 
                      
					 $fecha_mes =$meses[$datos[$i]["numero_mes"]-1] ;   
					 $estado=$datos[$i]["estado_compra"];
					 if($estado==1){
					  $estado="PAGADOS";

					 }elseif($estado==2){
					  $estado="PENDIENTES";
					 }elseif($estado==0){
					  $estado="ANULADOS";
					 }

				   ?>

	  
						<tr>
						
						  <td><?php echo $datos[$i]["tipo_pago_compra"]?></td>
						  <td><?php echo "$ ".number_format($datos[$i]["total_compra"],2)?></td>
						  <td><?php echo $estado?></td>
						</tr>
						
					<?php
                        }//cierre ciclo for

                     }//cierre condicional else
                                 
				      ?>
                      
                  
				    </tbody>

				   
				  </table>

		   </div><!--fin box-body-->
      </div><!--fin box-->
			
		</div><!--fin col-xs-12-->

		  <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
		  	  <div class="box">

	             <!--<div class="">-->

				   
		         <h2 class="reporte_compras_general container-fluid bg-red text-white col-lg-12 text-center mh-50">REPORTE DE COMPRAS MENSUAL</h2>

      
	          <!--GRAFICA-->
	         <div id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
		        


		         <!--</div>--><!--fin box-body-->
               </div><!--fin box-->
		  </div><!--fin col-xs-6-->

  </div><!--fin row-->



</div>
  <!-- /.content-wrapper -->


   
  <?php  } else {

       require("noacceso.php");
  }
   
  ?><!--CIERRE DE SESSION DE PERMISO -->


   <?php require_once("footer.php");?>

      <!--AJAX COMPRAS-->
<!--<script type="text/javascript" src="js/compras.js"></script>-->



				<script type="text/javascript">

     $(document).ready(function() {

			//Highcharts.chart('container', {

			var chart = new Highcharts.Chart({
		  //$('#container').highcharts({
        
			   chart: {
			    	
			        renderTo: 'container', 
			        plotBackgroundColor: null,
			        plotBorderWidth: null,
			        plotShadow: false,
			        type: 'pie'
			    },

			        exporting: {
			        url: 'http://export.highcharts.com/',
			        enabled: false
        
                },

			    title: {
			        text: ''
			    },
			    tooltip: {
			        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			    },
			    plotOptions: {
			        pie: {
			        	showInLegend:true,
			            allowPointSelect: true,
			            cursor: 'pointer',
			            dataLabels: {
			                enabled: true,
			                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
			                style: {
			                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black',

			                    fontSize: '20px'
			                }
			            }
			        }
			    },
			     legend: {
			        symbolWidth: 12,
			        symbolHeight: 18,
			        padding: 0,
			        margin: 15,
			        symbolPadding: 5,
			        itemDistance: 40,
			        itemStyle: { "fontSize": "17px", "fontWeight": "normal" }
			    },

			    series: [

                {
        name: 'Brands',
        colorByPoint: true,
        data: [

          <?php 

           //si existe el envia del post entonces se llama al metodo
		  if(isset($_POST["year"])){

          echo $datos_grafica= $compras->suma_compras_anio_mes_grafica($_POST["year"],$_POST["mes"]);

           } else {

           //si no existe el envio post entonces se mostraran los datos de la grafica del año actual
           
           $fecha_inicial=date("Y");

           	echo $datos_grafica= $compras->suma_compras_anio_mes_grafica($año,$mes);
           }

          ?>

			    ]

			    }], 

			    exporting: {
                enabled: false
             }

			});

			//si se le da click al boton entonces se envia la imagen al archivo PDF por ajax
			$('#buttonExport').click(function() {
           

			   //alert("clic");
            printHTML()
			document.addEventListener("DOMContentLoaded", function(event) {
			 printHTML(); 
			});

  
    }); 
			//fin prueba

});

 //function

	function printHTML() { 
	  if (window.print) { 

		$('#panel_imprimir').hide();
		$('#titulo').hide(); 
		$('#panel_form').hide(); 
		$('#buttonExport').hide();
		
	    window.print();
	  }
	  $('#panel_imprimir').show();
		$('#titulo').show(); 
		$('#buttonExport').show();
		$('#panel_form').show(); 
	}
	
</script>


<?php
 
   } else {

   	  header("Location:".Conectar::ruta()."index.php");
   }

?>