<?php
/*IMPORTANTE:ESTE ARCHIVO DE PDF NO ACEPTA LOS ESTILOS DE LIBRERIAS EXTERNAS NI BOOTSTRAP, HAY QUE USAR STYLE COMO ATRIBUTO*/

   require_once("../config/conexion.php"); 


  if(isset($_SESSION["nombre"]) and isset($_SESSION["correo"])){

    require_once("../modelos/Proveedores.php");
    require_once("../modelos/Compras.php");


    $proveedores=new Proveedor();
    $compra = new Compras();



    $datos=$proveedores->get_proveedor_por_cuit($_POST["cuit"]);
    $pedidos=$compra->get_pedido_por_fecha($_POST["cuit"],$_POST["datepicker"],$_POST["datepicker2"]);
    $total_productos=$compra->get_cant_productos_por_fecha($_POST["cuit"],$_POST["datepicker"],$_POST["datepicker2"]);


    ob_start(); 

   
?>

<link type="text/css" rel="stylesheet" href="dompdf/css/print_static.css"/>
  <style type="text/css">

    
    .Estilo1{
      font-size: 13px;
      font-weight: bold;
    }
    .Estilo2{font-size: 13px}
    .Estilo3{font-size: 13px; font-weight: bold;}
    .Estilo4{color: #FFFFFF}

  </style>



<table style="width: 100%;" class="header">
<tr>
<td width="54%" height="111"><h1 style="text-align: left; margin-right:20px;"><img src="../public/images/logo_mercado.jfif" width="340" height="200"  /></h1></td>


<td width="46%" height="111">
<table style="width: 100%; font-size: 10pt;">

<tr>
    <td><strong>LA INDUSTRIA DEL POLLO</strong></td>
  </tr>

  <tr>
    <td><strong>CUIT: 30-71731420-0</strong></td>
  </tr>
  <tr>
    <td> </td>
  </tr>
  
  <tr>
    <td width="43%"><strong>DATOS DEL USUARIO</strong></td>
  </tr>
  <tr>
    <td><strong>NOMBRE Y APELLIDO: </strong><?php echo $_SESSION["nombre"];echo" ".$_SESSION["apellido"];?></td>
  </tr>
  <tr>
    <td><strong>D.N.I.: </strong><?php echo $_SESSION["dni_usuario"]; ?></td>
  </tr>
  <tr>
    <td><strong>FECHA-HORA IMPRESO: </strong>
      <?php date_default_timezone_set("America/Argentina/Buenos_Aires");  echo $fecha=date("d-m-Y h:i:s A"); ?></td>
  </tr>
   <tr></tr>
</table><!--fin segunda tabla-->
</td> <!--fin segunda columna-->

</tr>
</table>




  <div align="center" style="color:black; font-weight:bolder; font-size:20px;">COMPRAS DE PRODUCTOS A PROVEEDOR   </div>
<table width="101%" class="change_order_items">

<tr>
  <th colspan="5" style="font-size:15pt">DATOS PERSONALES DEL PROVEEDOR </th>
  </tr>
<tr>
<th width="5%" bgcolor="#317eac"><span class="Estilo11">CUIT</span></th>
<th width="15%" bgcolor="#317eac"><span class="Estilo11">NOMBRE</span></th>
<th width="12%" bgcolor="#317eac"><span class="Estilo11">TELEFONO</span></th>
<th width="30%" bgcolor="#317eac"><span class="Estilo11">DIRECCION</span></th>
<th width="20%" bgcolor="#317eac"><span class="Estilo11">CORREO</span></th>
     
      <?php

         if(empty($_POST["cuit"])){

             echo "<span style='font-size:20px; color:red'>SELECCIONA UN PROVEEDOR</span>";
         
         }

      ?>

</tr>

<?php
  
  for($i=0;$i<sizeof($datos);$i++){

?>

<tr style="font-size:7pt" class="even_row">
<td><div align="center"><span class=""><?php echo $datos[$i]["cuit_proveedor"];?></span></div></td>
<td style="text-align: center"><div align="center"><span class=""><?php echo utf8_decode($datos[$i]["nombre_proveedor"]);?></span></div></td>
<td style="text-align: center"><div align="center"><span class=""><?php echo $datos[$i]["telefono_proveedor"];?></span></div></td>
<td style="text-align: right"><div align="center"><span class=""><?php echo utf8_decode($datos[$i]["direccion_proveedor"]);?></span></div></td>
<td style="text-align:right"><div align="center"><span class=""><?php echo utf8_decode($datos[$i]["correo_proveedor"]);?></span></div></td>
</tr>

<?php
  }
?>



</table>
</div>


 <div style="font-size: 7pt">

<table width="102%" class="change_order_items">
  <tr>
    <td colspan="5" style="font-size:15pt"><div align="center"><strong>LISTA DE COMPRAS DE PRODUCTOS </strong></div></td>
  </tr>
  
    <tr>
    <th width="10%" bgcolor="#317eac"><span class="Estilo11">FECHA COMPRA </span></th>
      
      <th width="15%" bgcolor="#317eac"><span class="Estilo1">PRODUCTO </span></th>
      <th width="10%" bgcolor="#317eac"><span class="Estilo11">PRECIO COMPRA</span></th>
      <th width="5%" bgcolor="#317eac"><span class="Estilo11">CANTIDAD</span></th>
      <th width="10%" bgcolor="#317eac"><span class="Estilo11">TOTAL</span>
      
      </tr>
      <?php

         if(is_array($pedidos)==true and count($pedidos)==0){

             echo "<span style='font-size:20px; color:red'>EL PROVEEDOR NO TIENE COMPRAS ASOCIADAS EN EL RANGO DE FECHA INDICADO</span>";

         }

      ?>

        <?php
        
        $pagoTotal=0;
        $cantidad=0;

       
         for($j=0;$j<count($pedidos);$j++){

          
            if($pedidos[$j]["id_categoria"]==9 or $pedidos[$j]["id_categoria"]==11  or $pedidos[$j]["id_categoria"]==14){
              $decision=$pedidos[$j]["precio_compra_dc"] * ($pedidos[$j]["cantidad_compra_dc"]/1000);
              $pagoTotal= $pagoTotal + $decision;
              $cantidad= $cantidad +1;

            }else{
              $decision=$pedidos[$j]["precio_compra_dc"] * $pedidos[$j]["cantidad_compra_dc"];

              $pagoTotal= $pagoTotal + $decision;
              $cantidad= $cantidad + $pedidos[$j]["cantidad_compra_dc"];

            }
         ?>
         
    <tr class="even_row" style="font-size:10pt">
    <td style="text-align: center"><span><?php echo $fecha=date("d-m-Y",strtotime($pedidos[$j]["fecha_compra_dc"])); ?></span></td>
      <td style="text-align: center"><span><?php echo utf8_decode($pedidos[$j]["nombre_producto"]);?></span></td>
       <td style="text-align: center"><span><?php echo "$ ".$pedidos[$j]["precio_compra_dc"];?></span></td>
      
      <td style="text-align: center"><span><?php
      if($pedidos[$j]["id_categoria"]==9 or $pedidos[$j]["id_categoria"]==11 or $pedidos[$j]["id_categoria"]==14){
          if($pedidos[$j]["cantidad_compra_dc"]>=1000){echo ($pedidos[$j]["cantidad_compra_dc"]/1000)." Kg.";
          }else{
               echo $pedidos[$j]["cantidad_compra_dc"]." Grs.";
          }
       }
      else{echo $pedidos[$j]["cantidad_compra_dc"]; }?></span></td>

        
      <td style="text-align: center"><span class=""><?php 
      if($pedidos[$j]["id_categoria"]==9 or $pedidos[$j]["id_categoria"]==11 or $pedidos[$j]["id_categoria"]==14 )echo "$ ".number_format($pedidos[$j]["cantidad_compra_dc"] * ($pedidos[$j]["precio_compra_dc"]/1000),2);
      else{echo "$ ".number_format($pedidos[$j]["cantidad_compra_dc"] * $pedidos[$j]["precio_compra_dc"],2);}?></span></td>
   
     
      </tr>
      <?php } ?>



 <!--comienzo de la suma de productos y monto total-->
   <tr class="even_row">
  <td colspan="5" style="text-align: center"><table style="width: 100%; font-size: 8pt;">
   
  <tr>
    <td class="even_row" style="text-align: center">&nbsp;</td>
    <td class="odd_row" style="text-align: right; border-right-style: none;">&nbsp;</td>
  </tr>
  
  <tr>
    <td width="84%" class="even_row" style="font-size:10pt; text-align: center">
      <div align="right"><strong><span style="text-align: right;">TOTAL :</span></strong></div>
    </td>
    <td width="16%" class="odd_row" style="font-size:12pt" text-align: right; border-right-style: none;">
      <div align="center">
      <strong>
      <?php 

       

        echo "$ ".number_format($pagoTotal,2);



      ?>
      </strong>
      
      </div>
    </td>
  </tr>
  
  <tr>
    <td class="even_row" style="font-size:10pt; text-align: center"><div align="right"><strong><span style="text-align:right;">TOTAL PRODUCTOS COMPRADOS:</span></strong></div></td>
    <td class="odd_row" style="font-size:12pt;text-align: right; border-right-style: none;"><div align="center"><strong>
      <?php 


      if($pagoTotal!=0){

        echo $cantidad;

       } else {

            echo "0";
       }
      

      ?>
    </strong></div>
  </td>
  </tr> 
  
    </td>
  </tr>     
       <!--termina la suma de productos y monto total-->


</table>

<table style="border-top: 1px solid black; padding-top: 2em; margin-top: 2em;">
 
  <tr>
    <td style="padding-top: 0em"><span class="Estilo1">REALIZADO EL DIA <?php echo date("d")?> DE <?php echo Conectar::convertir(date('m'))?> DEL <?php echo date("Y")?></span></td>
    <td style="text-align: center; padding-top: 0em;">&nbsp;</td>
  </tr>
</table>


 </div>


  <?php
  

  require_once("dompdf/dompdf_config.inc.php");    
    
    $dompdf = new DOMPDF();
    $dompdf->load_html(ob_get_clean());
    $dompdf->render();
    $pdf= $dompdf->output();
    $filename="informe.pdf";
    file_put_contents($filename,$pdf);
    $dompdf->stream($filename, array('Attachment'=>'0'));


  } else{

     header("Location:".Conectar::ruta()."index.php");
  }
    
?>