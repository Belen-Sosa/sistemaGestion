<?php

  require_once("../config/conexion.php");

    if(isset($_SESSION["id_usuario"])){
      require_once("../modelos/CuentasCorrientes.php");

      $cc = new CuentaCorriente();

      $lista = $cc-> get_cc_por_cliente($_GET["id_cliente"]);
    
        

?>


<!-- INICIO DEL HEADER - LIBRERIAS -->
<?php require_once("header.php");?>

<!-- FIN DEL HEADER - LIBRERIAS -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
       Detalle Cuenta Corriente 
       
      </h1>
      
    <h2 id="h2_nombre_cliente"></h2>
      <div class="box-header with-border">
                          <h1 class="box-title">
                            <button class="btn btn-primary btn-lg" id="add_button" onclick="" data-toggle="modal" data-target="#pagoModal"><i class="fa fa-plus" aria-hidden="true"></i> Pago a Cuenta</button></h1>
        
                        <div class="box-tools pull-right">
                        </div>
                    </div>
    </section>

    <!-- Main content -->
    <section class="content">
    
   <div id="resultados_ajax"></div>


    


       <!--VISTA MODAL PARA VER DETALLE VENTA EN VISTA MODAL-->
       <?php require_once("modal/detalle_venta_modal.php");?>
    
   
      <div class="row">
        <div class="col-xs-12">
          
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Movimientos</h3>
              
            </div>
           
            <!-- /.box-header -->
            <div class="box-body">
             <table id="cuenta_corriente_data" class="table table-bordered table-striped">
                 <div></div>
                <thead>
                <tr>
                 
                  <th>Fecha</th>
                  <th>descripcion</th>
                  <th>N° Venta </th>
                  <th>Total</th>
                  <th>Usuario</th>
                  <th>Accion</th>
                  
             
             
                  
                 
                </tr>
                </thead>
                
              </table>
              <div class="box-body">
              <table class="table table-striped">
                <thead>
                <tr class="bg-success">
                    <th ></th> 
                    <th ></th>   
                    <th ></th>  
                    <th class="col-lg-4">TOTAL</th> 
                    
                </tr>
                </thead>


                <tbody>
                      <tr class="bg-gray">
                         
                        <td></td>
                        <td></td>
                        <td></td>
                      
                        <!--IMPORTANTE: hay que poner el name=total en el h4 para que lo pueda enviar, NO se envia si lo pones en el input hidden-->
                        <td class="col-lg-4"><h4 id="total_cc" name="total_cc"> 0.00</h4><input type="hidden" name="total_venta_cc" id="total_venta_cc"></td>
                               
                     
                      </tr>

                       
                </tbody>
           
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
    

  

  </div>
    <!--FORMULARIO VENTANA MODAL-->
  
<div id="pagoModal" class="modal fade">
  <div class="modal-dialog">
    <form class="form-horizontal" method="post" id="pago_form">
      <div class="modal-content">
      
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Pago a Cuenta</h4>
        </div>
        <div class="modal-body">

        <div class="form-group">
                  <label  for=""  class="control-label">Descripcion de pago:</label>

             
                    
                    <input type="" name="descripcion_pago" id="descripcion_pago" value="" class="form-control">   

                    
                 
                
              </div>



               <div class="form-group">
                  <label for="inputText4" class="col-lg-1 control-label">Monto:</label>

            
                    <input type="text" class="form-control" id="monto" name="monto" placeholder="monto" required >
                  
                </div>

               

                
          
          </div>
                 <!--modal-body-->

        <div class="modal-footer">
          <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $_SESSION["id_usuario"];?>"/>
    

          <button type="submit" name="action" id="#" class="btn btn-success pull-left" value="Add"><i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar </button>

          <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Cerrar</button>
        </div>
      </div>
    </form>
  </div>
</div>
 <!--FIN FORMULARIO VENTANA MODAL-->
  


   

    
   <?php require_once("footer.php");?>

<script type="text/javascript" src="js/cuenta_corriente_detalle.js"></script>


<?php
   
  } else {

         header("Location:".Conectar::ruta()."index.php");
     }

?>