
<?php

   require_once("../config/conexion.php");

    if(isset($_SESSION["correo"])){



       /*Se llaman los modelos y se crean los objetos para llamar el numero de registros en el menu lateral izquierdo y en el home*/
      
      require_once("../modelos/Proveedores.php");
      require_once("../modelos/Compras.php");
      require_once("../modelos/Clientes.php");
      require_once("../modelos/Ventas.php");

      
       $proveedor = new Proveedor();
       $compra = new Compras();
       $cliente = new Cliente();
       $venta = new Ventas();



        $datos=$compra->get_compras_anio_actual();

        $datos_venta=$venta->get_ventas_anio_actual();  


?>


<?php require_once("header.php");?>

     
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Inicio
      </h1>
      
    </section>

    <!-- Main content -->
    <section class="content">

       <div class="row panel_modulos">

       	   <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">

             <a href="<?php echo Conectar::ruta()?>vistas/clientes.php">
              
              <h3><?php echo $cliente->get_filas_cliente();?></h3>

               <h2>CLIENTES</h2>
             </a>

            </div>
            <div class="icon">
              <i class="fa fa-users" aria-hidden="true"></i>
            </div>
            
          </div>
        </div>
        <!-- ./col -->


         <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">

           <a href="<?php echo Conectar::ruta()?>vistas/ventas.php">
              <h3><?php echo $venta->get_filas_venta();?></h3>
           
              <h2>VENTAS</h2>
           </a>

            </div>
            <div class="icon">
              <i class="fa fa-shopping-cart" aria-hidden="true"></i>
            </div>
           
          </div>
        </div>
        <!-- ./col -->

        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">

            <a href="<?php echo Conectar::ruta()?>vistas/proveedores.php">
              <h3><?php echo $proveedor->get_filas_proveedor();?></h3>
             
              <h2>PROVEEDORES</h2>
             </a>

            </div>
            <div class="icon">
              <i class="fa fa-truck" aria-hidden="true"></i>
            </div>
            
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">

             <a href="<?php echo Conectar::ruta()?>vistas/compras.php">
              <h3><?php echo $compra->get_filas_compra();?></h3>
           
              <h2>COMPRAS</h2>
            </a>

            </div>
            <div class="icon">
              <i class="fa fa-cart-plus" aria-hidden="true"></i>
            </div>
            
          </div>
        </div>
        <!-- ./col -->
       	
       </div><!--ROW-->


          

           
       </div><!--fin box-body-->
      </div><!--fin box-->
      
    </div><!--fin col-sm-6-->

     
    </div><!--row-->

  




    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


<?php require_once("footer.php");?>



  <script type="text/javascript">
   
   /*GRAFICA COMPRAS*/
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

          <?php echo $datos_grafica= $compra->get_compras_anio_actual_grafica();?>

          ]

          }], 

          exporting: {
                enabled: false
             }

      });


});



   /*GRAFICA VENTAS*/
     $(document).ready(function() {

      //Highcharts.chart('container', {

      var chart = new Highcharts.Chart({
      //$('#container').highcharts({
        
         chart: {
            
              renderTo: 'container_ventas', 
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

        <?php echo $datos_grafica= $venta->get_ventas_anio_actual_grafica();?>
          ]

          }], 

          exporting: {
                enabled: false
             }

      });


});


  
</script>


<?php
     
     } else {

        header("Location:".Conectar::ruta()."index.php");
        exit();
     }
  ?>


