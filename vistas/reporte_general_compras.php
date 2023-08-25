<?php
   
   require_once("../config/conexion.php");

   if(isset($_SESSION["id_usuario"])){
   	require_once("../modelos/Compras.php");
   	$compras=new Compras();

   	$datos= $compras->get_compras_reporte_general();


   	$datos_ano= $compras->suma_compras_total_ano();
      
	
	
?>


<!-- INICIO DEL HEADER - LIBRERIAS -->
<?php require_once("header.php");?>

<!-- FIN DEL HEADER - LIBRERIAS -->



  <?php if($_SESSION["reporte_compras"]==1)
     {

     ?>


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">



   <h2 ID="titulo" class="reporte_compras_general container-fluid bg-red text-white col-lg-12 text-center mh-50">
        
        
         REPORTE DE GENERAL DE COMPRAS 
    </h2>

   <div id="panel_imprimir" class="panel panel-default">
        
        <div class="panel-body">

         <div class="btn-group text-center">
		 <button type='button' id="buttonExport" class="btn btn-primary btn-lg" ><i class="fa fa-print" aria-hidden="true"></i> Imprimir</button>
         </div>


       </div>
      </div>

   

	<div class="row">

	 <div class="col-md-12 col-sm-12 col-xs-12">

	    <div class="box">

	       <div class="">

				  <h2 class="reporte_compras_general container-fluid bg-red text-white col-lg-12 text-center mh-50">Reporte General : Ultimos 12 Meses </h2>
				              
				  <table class="table table-bordered">
				    <thead>
				      <tr>
				      
				        <th>NOMBRE MES</th>
				        <th>TOTAL</th>
				      </tr>
				    </thead>


				    <tbody>
				     
                   <?php
                    $total_final=0;
         
                   
                    for($i=0;$i<count($datos);$i++){


				    //para traducir el nombre del mes ya que si lo traemos desde phpmyadmin lo traerá en ingles
                      $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                      
                      //trae el nombre del mes
                       $fecha= $datos[$i]["mes"];
                       
                       //aqui se imprime el nombre del mes en español
                       $fecha_mes = $meses[date("n", strtotime($fecha))-1];
                      $total_final=$total_final+$datos[$i]["total_compra"];

				     ?>


					      <tr>
					  
					        <td><?php echo $fecha_mes?></td>
					     
					        <td><?php echo "$ ".number_format($datos[$i]["total_compra"],2)?></td>
						
                       </tr>
					      
				      <?php

                       
                       }//cierre del for
                   
					  
				      ?>
					<tr>
					     <td>TOTAL</td>
					     
						 <td><?php echo "$ ".number_format($total_final,2); ?></td>
					</tr>
					   
                  
				    </tbody>
				  </table>

		   </div><!--fin box-body-->
      </div><!--fin box-->
			
		</div><!--fin col-xs-12-->

		

  </div><!--fin row-->

  <!--SEGUNDA FILA DE LA GRAFIA-->
		<div class="row">
            
            <!--COMPRAS HECHAS-->
			 <div class="col-md-12 col-sm-12 col-xs-12">

			    <div class="box">

			       <div class="">

					<h2 class="reporte_compras_general container-fluid bg-red text-white col-lg-12 text-center mh-50">GRAFICO</h2>

      
	          <!--GRAFICA-->
	            <div id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>


		            </div><!--fin box-body-->
		        </div><!--fin box-->
			</div><!--fin col-lg-6-->


          
		</div><!--fin row-->
     


</div>
  <!-- /.content-wrapper -->

  
   
  <?php  } else {

       require("noacceso.php");
  }
   
  ?><!--CIERRE DE SESSION DE PERMISO -->


  
   <?php require_once("footer.php");?>

<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
  <script type="text/javascript">


     $(document).ready(function() {

		
		  //COMPRAS HECHAS

			var chart = new Highcharts.Chart({
		
        
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

          <?php echo $datos_grafica= $compras->suma_compras_total_grafica();?>
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
		$('#buttonExport').hide();
		$('#titulo').hide();
		window.print();
	  }
	  $('#panel_imprimir').show();
		$('#titulo').show();
		$('#buttonExport').show();
	}

	
</script>



<?php
   } else {

   	    header("Location:".Conectar::ruta()."index.php");
   }
?>