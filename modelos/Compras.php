<?php

  
     //conexion a la base de datos
     require_once("../config/conexion.php");

   
      class Compras extends Conectar{


        public function get_filas_compra(){

             $conectar= parent::conexion();   
             $sql="select * from compras";          
             $sql=$conectar->prepare($sql);
             $sql->execute();
             $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);

             return $sql->rowCount();
        
        }


      	public function get_compras(){

             $conectar= parent::conexion();          
             $sql="select * from compras";        
             $sql=$conectar->prepare($sql);
             $sql->execute();
             return $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);
        
        }

   public function get_compras_por_id($id_compras){

             $conectar= parent::conexion();
             $sql="select * from compras where id_compras=?";            
             $sql=$conectar->prepare($sql);
             $sql->bindValue(1,$id_compras);
             $sql->execute();

             return $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);

    
    }

             
    public function numero_compra(){

		    $conectar=parent::conexion();
		    parent::set_names();
		    $sql="select numero_compra from detalle_compras;";
		    $sql=$conectar->prepare($sql);
		    $sql->execute();
		    $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);

        //aqui selecciono solo un campo del array y lo recorro que es el campo numero_compra
        foreach($resultado as $k=>$v){

            $numero_compra["numero"]=$v["numero_compra"];
               
        }
		   //luego despues de tener seleccionado el numero_compra digo que si el campo numero_compra està vacio entonces se le asigna un F000001 de lo contrario ira sumando

		        

        if(empty($numero_compra["numero"]))
        {
          echo 'F000001';
        }else
  
          {
            $num     = substr($numero_compra["numero"] , 1);
            $dig     = $num + 1;
            $fact = str_pad($dig, 6, "0", STR_PAD_LEFT);
            echo 'F'.$fact;
            
          } 

		}



		   /*metodo para agregar la compra */
    public function agrega_detalle_compra(){

        
      //echo json_encode($_POST['arrayCompra']);
      $str = '';
      $detalles = array();
      $detalles = json_decode($_POST['arrayCompra']);
      $conectar=parent::conexion();


      foreach ($detalles as $k => $v) {
    
        //IMPORTANTE:estas variables son del array detalles
        $cantidad = $v->cantidad;
        $codProd = $v->codProd;
            $codCat = $v->codCat;
        $producto = $v->producto;
        $precio = $v->precio; 
        $dscto = $v->dscto;
        $importe = $v->importe;
        //$total = $v->total;
        $estado = $v->estado;

      //echo "***************";
      //echo "Cant: ".$cantidad." codProd: ".$codProd. " Producto: ". $producto. " " precio: ".$precio. " descuento: ".$dscto. " estado: ".$estado;
        
        $numero_compra = $_POST["numero_compra"];
        $cuit_proveedor = $_POST["cuit"];
        $proveedor = $_POST["razon"];
        $direccion = $_POST["direccion"];
        $total = $_POST["total"];
        $comprador = $_POST["comprador"];
        $tipo_pago = $_POST["tipo_pago"];
        $id_usuario = $_POST["id_usuario"];
        $id_proveedor = $_POST["id_proveedor"];

          

        $sql="insert into detalle_compras
        values(null,?,?,?,?,?,?,?,?,now(),?,?,?,?);";


        $sql=$conectar->prepare($sql);

        /*importante:se ingresó el id_producto=$codProd ya que se necesita para relacionar las tablas compras con detalle_compras para cuando se vaya a hacer la consulta de la existencia del producto y del stock para cuando se elimine un detalle compra y se reintegre la cantidad de producto*/

        $sql->bindValue(1,$numero_compra);
        $sql->bindValue(2,$cuit_proveedor);
        $sql->bindValue(3,$codProd);
        $sql->bindValue(4,$producto);
        $sql->bindValue(5,$precio);
        $sql->bindValue(6,$cantidad);
        $sql->bindValue(7,$dscto);
        $sql->bindValue(8,$importe);
        $sql->bindValue(9,$id_usuario);
        $sql->bindValue(10,$id_proveedor);
        $sql->bindValue(11,$estado);
        $sql->bindValue(12,$codCat);
        $sql->execute();

     

          //si existe el producto entonces actualiza la cantidad, en caso contrario no lo inserta


        $sql3="select * from producto where id_producto=?;";
        $sql3=$conectar->prepare($sql3);
        $sql3->bindValue(1,$codProd);
        $sql3->execute();
        $resultado = $sql3->fetchAll(PDO::FETCH_ASSOC);

        foreach($resultado as $b=>$row){

          $re["existencia"] = $row["stock_producto"];

        }

        //la cantidad total es la suma de la cantidad más la cantidad actual
        $cantidad_total = $cantidad + $row["stock_producto"];

             
        //si existe el producto entonces actualiza el stock en producto
              
        if(is_array($resultado)==true and count($resultado)>0) {
              
          //actualiza el stock en la tabla producto

          $sql4 = "update producto set 
              
              stock_producto=?
              where 
              id_producto=?
          ";


        $sql4 = $conectar->prepare($sql4);
        $sql4->bindValue(1,$cantidad_total);
        $sql4->bindValue(2,$codProd);
        $sql4->execute();

        } //cierre la condicional


	    }//cierre del foreach

	     /*IMPORTANTE: hice el procedimiento de imprimir la consulta y me di cuenta a traves del mensaje alerta que la variable total no estaba definida y tube que agregarla en el arreglo y funcionó*/


	     //SUMO EL TOTAL DE IMPORTE SEGUN EL CODIGO DE DETALLES DE COMPRA

      $sql5="select sum(importe_dc) as total from detalle_compras where numero_compra=?"; 
      $sql5=$conectar->prepare($sql5);
      $sql5->bindValue(1,$numero_compra);
      $sql5->execute();

      $resultado2 = $sql5->fetchAll();

      foreach($resultado2 as $c=>$d){

          $row["total"]=$d["total"];
        
      }
      $total=$d["total"];

      //IMPORTANTE: hay que sacar la consulta INSERT INTO COMPRAS fuera del foreach sino se repetiria el registro en la tabla compras
		  //la fecha no se puede formatear por es un objeto date, solo se formatea en el select, cuando se va a obtener una fecha, por lo tanto la fecha queda en el formato y/m/d en la tabla de la bd	

      $sql2="insert into compras 
      values(null,now(),?,?,?,?,?,?,?,?,?);";
      $sql2=$conectar->prepare($sql2);
      $sql2->bindValue(1,$numero_compra);
      $sql2->bindValue(2,$proveedor);
      $sql2->bindValue(3,$cuit_proveedor);
      $sql2->bindValue(4,$comprador);
      $sql2->bindValue(5,$total);
      $sql2->bindValue(6,$tipo_pago);
      $sql2->bindValue(7,$estado);
      $sql2->bindValue(8,$id_usuario);
      $sql2->bindValue(9,$id_proveedor);         
      $sql2->execute();



  }

  	   //metodo para ver el detalle del proveedor en una compra
  public function get_detalle_proveedor($numero_compra){

    $conectar=parent::conexion();
    parent::set_names();
    $sql="select c.fecha_compra,c.numero_compra, c.nombre_proveedor, c.cuit_proveedor,c.total_compra,p.id_proveedor,p.cuit_proveedor,p.nombre_proveedor,p.telefono_proveedor,p.correo_proveedor,p.direccion_proveedor,p.fecha_alta_proveedor,p.estado_proveedor
    from compras as c, proveedor as p
    where 
    c.cuit_proveedor=p.cuit_proveedor
    and
    c.numero_compra=?;";

    $sql=$conectar->prepare($sql);          
    $sql->bindValue(1,$numero_compra);
    $sql->execute();
    return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC); 
  }


   public function get_detalle_compras_proveedor($numero_compra){

      $conectar=parent::conexion();
      parent::set_names();

      $sql="select d.numero_compra,d.cuit_proveedor,d.nombre_producto, d.precio_compra_dc,d.cantidad_compra_dc,d.descuento_dc,d.importe_dc, d.fecha_compra_dc,c.numero_compra, c.total_compra,p.id_proveedor,p.cuit_proveedor,p.nombre_proveedor,p.telefono_proveedor,p.correo_proveedor,p.direccion_proveedor,p.fecha_alta_proveedor,p.estado_proveedor,d.id_categoria
      from detalle_compras as d, compras as c, proveedor as p
      where  d.numero_compra=c.numero_compra
      and d.cuit_proveedor=p.cuit_proveedor
      and d.numero_compra=?;";


      $sql=$conectar->prepare($sql);
      $sql->bindValue(1,$numero_compra);
      $sql->execute();
      $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);

       
      $html= "

              <thead style='background-color:#A9D0F5'>

                                    <th>Cantidad</th>
                                    <th>Producto</th>
                                    <th>Precio Compra</th>
                                    <th>Descuento (%)</th>
                                    <th>Importe</th>
                                   
                                </thead>


                              ";

           

      foreach($resultado as $row){

         if($row["id_categoria"]==9 or $row["id_categoria"]==11 or $row["id_categoria"]==14 ){
          if($row['cantidad_compra_dc']>=1000){
            $cantidad_compra_grms=$row['cantidad_compra_dc']/1000;

            $html.="<tr class='filas'><td>".$cantidad_compra_grms." Kg.</td><td>".$row['nombre_producto']."</td> <td>$  ".number_format($row['precio_compra_dc'],2)."</td> <td>".$row['descuento_dc']."</td> <td>$" .number_format($row['importe_dc'],2)."</td></tr>";
            
          }else{
            $html.="<tr class='filas'><td>".$row['cantidad_compra_dc']." grs.</td><td>".$row['nombre_producto']."</td> <td>$  ".number_format($row['precio_compra_dc'],2)."</td> <td>".$row['descuento_dc']."</td> <td>$" .number_format($row['importe_dc'],2)."</td></tr>";
            
          }
               
         }else{
          $html.="<tr class='filas'><td>".$row['cantidad_compra_dc']."</td><td>".$row['nombre_producto']."</td> <td>$  ".    number_format($row['precio_compra_dc'],2)."</td> <td>".$row['descuento_dc']."</td> <td>$" .number_format($row['importe_dc'],2)."</td></tr>";
                   
                
               
         }
             
          $total="$ ".number_format($row["total_compra"],2);
        }

         $html .= "<tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>
                                     
                                     <p class='margen_total'>TOTAL</p>
                                    </th>
                                    <th>

                             

                                     <p><strong>".$total."</strong></p>

                                    </th> 
                                </tfoot>";
      
      echo $html;

  }


         /*cambiar estado de la compra, solo se cambia si se quiere eliminar una compra y se revertería la cantidad de compra al stock*/

  public function cambiar_estado($id_compras, $numero_compra, $est){
    

      $conectar=parent::conexion();
      parent::set_names();
            
            //si estado es igual a 0 entonces lo cambia a 1
            //0= anulado , 1= pagado
      $estado = 0;
      //el parametro est se envia por via ajax, viene del $est:est
      /*si el estado es ==0 cambiaria a PAGADO Y SE EJECUTARIA TODO LO QUE ESTA ABAJO*/
    if($_POST["est"] == 0){
      $estado = 1;
      $numero_compra=$_POST["numero_compra"];
      $sql="update compras set  
            estado_compra=?
            where 
            id_compras=?  ";

      
      $sql=$conectar->prepare($sql);
      $sql->bindValue(1,$estado);
      $sql->bindValue(2,$_POST["id_compras"]);
      $sql->execute();
      $resultado= $sql->fetch(PDO::FETCH_ASSOC);
      $sql_detalle= "update detalle_compras set
          estado_dc=?
          where 
          numero_compra=?
      ";

      $sql_detalle=$conectar->prepare($sql_detalle);
      $sql_detalle->bindValue(1,$estado);
      $sql_detalle->bindValue(2,$numero_compra);
      $sql_detalle->execute();

      $resultado= $sql_detalle->fetch(PDO::FETCH_ASSOC);

        /*una vez se cambie de estado a ACTIVO entonces actualizamos la cantidad de stock en productos*/
        //INICIO CONSULTA DE DETALLE DE COMPRAS Y COMPRAS

      $sql2="select * from detalle_compras where numero_compra=?";

      $sql2=$conectar->prepare($sql2);
      $sql2->bindValue(1,$numero_compra);
      $sql2->execute();
      $resultado=$sql2->fetchAll();

      foreach($resultado as $row){

        $id_producto=$output["id_producto"]=$row["id_producto"];
        //selecciona la cantidad comprada
        $cantidad_compra=$output["cantidad_compra_dc"]=$row["cantidad_compra_dc"];

                 //si el id_producto existe entonces que consulte si la cantidad de productos existe en la tabla producto

                  if(isset($id_producto)==true /*and is_countable($id_producto)>0*/){
                      $sql3="select * from producto where id_producto=?";
                      $sql3=$conectar->prepare($sql3);
                      $sql3->bindValue(1, $id_producto);
                      $sql3->execute();
                      $resultado=$sql3->fetchAll();

                         foreach($resultado as $row2){
                           
                           //este es la cantidad de stock para cada producto
                           $stock=$output2["stock"]=$row2["stock_producto"];
                           //esta debe estar dentro del foreach para que recorra el $stock de los productos, ya que es mas de un producto que está asociado a la compra
                           //cuando das click a estado pasa a PAGADO Y SUMA la cantidad de stock con la cantidad de compra
                           $cantidad_actual= $stock + $cantidad_compra;
                    
                         }
                  }

               
                //LE ACTUALIZO LA CANTIDAD DEL PRODUCTO 

        $sql6="update producto set 
        stock_producto=?
        where
        id_producto=?
        ";
        
        $sql6=$conectar->prepare($sql6);   
        $sql6->bindValue(1,$cantidad_actual);
        $sql6->bindValue(2,$id_producto);
        $sql6->execute();

      }//cierre del foreach

   }//cierre del if del estado

  else {

              /*si el estado es igual a 1, entonces pasaria a ANULADO y restaria la cantidad de productos al stock*/

   if($_POST["est"] == 1){
      $estado = 0;
    //declaro $numero_compra, viene via ajax
      $numero_compra=$_POST["numero_compra"];
      $sql="update compras set  
        estado_compra=?
        where 
        id_compras=?  ";

      $sql=$conectar->prepare($sql);

      $sql->bindValue(1,$estado);
      $sql->bindValue(2,$_POST["id_compras"]);
      $sql->execute();
      $resultado= $sql->fetch(PDO::FETCH_ASSOC);


      $sql_detalle= "update detalle_compras set
          estado_dc=?
          where 
          numero_compra=?
          ";

      $sql_detalle=$conectar->prepare($sql_detalle);
      $sql_detalle->bindValue(1,$estado);
      $sql_detalle->bindValue(2,$numero_compra);
      $sql_detalle->execute();
      $resultado= $sql_detalle->fetch(PDO::FETCH_ASSOC);
            /*una vez se cambie de estado a ACTIVO entonces actualizamos la cantidad de stock en productos*/

            
            //INICIO ACTUALIZAR LA CANTIDAD DE PRODUCTOS COMPRADOS EN EL STOCK

      $sql2="select * from detalle_compras where numero_compra=?";
      $sql2=$conectar->prepare($sql2);
      $sql2->bindValue(1,$numero_compra);
      $sql2->execute();

      $resultado=$sql2->fetchAll();

      foreach($resultado as $row){

         $id_producto=$output["id_producto"]=$row["id_producto"];
                //selecciona la cantidad comprada
          $cantidad_compra=$output["cantidad_compra_dc"]=$row["cantidad_compra_dc"];

                 //si el id_producto existe entonces que consulte si la cantidad de productos existe en la tabla producto
                 
                
                  if(isset($id_producto)==true ){
                    
               
                      $sql3="select * from producto where id_producto=?";
                      $sql3=$conectar->prepare($sql3);
                      $sql3->bindValue(1, $id_producto);
                      $sql3->execute();
                      $resultado=$sql3->fetchAll();

                         foreach($resultado as $row2){
                           
                           //este es la cantidad de stock para cada producto
                           $stock=$output2["stock_producto"]=$row2["stock_producto"];
                           
                           //esta debe estar dentro del foreach para que recorra el $stock de los productos, ya que es mas de un producto que está asociado a la compra
                         //cuando le da click al estado pasa de PAGADO A ANULADO y resta la cantidad de stock en productos con la cantidad de compra de detalle_compras, disminuyendo de esta manera la cantidad actual de productos en el stock de productos
                           $cantidad_actual= $stock - $cantidad_compra;
                       

                         }
                  }

               
                //LE ACTUALIZO LA CANTIDAD DEL PRODUCTO 

               $sql6="update producto set 
               stock_producto=?
               where
               id_producto=?";
               
               $sql6=$conectar->prepare($sql6);                
               $sql6->bindValue(1,$cantidad_actual);
               $sql6->bindValue(2,$id_producto);
               $sql6->execute();

              }//cierre del foreach



         }//cierre del if del estado del else


          }


       }//CIERRE DEL METODO



         //BUSCA REGISTROS COMPRAS-FECHA

  public function lista_busca_registros_fecha($fecha_inicial, $fecha_final){

            $conectar= parent::conexion();
            $date_inicial = $_POST["fecha_inicial"];
            $date = str_replace('/', '-', $date_inicial);
            $fecha_inicial = date("Y-m-d", strtotime($date)); 
            $date_final = $_POST["fecha_final"];
            $date = str_replace('/', '-', $date_final);
            $fecha_final = date("Y-m-d", strtotime($date));

            $sql= "SELECT * FROM compras WHERE fecha_compra>=? and fecha_compra<=? ";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$fecha_inicial);
            $sql->bindValue(2,$fecha_final);
            $sql->execute();
            return $result = $sql->fetchAll(PDO::FETCH_ASSOC);

       }


       
        //BUSCA REGISTROS COMPRAS-FECHA-MES

    public function lista_busca_registros_fecha_mes($mes, $ano){

      $conectar= parent::conexion();


      //variables que vienen por POST VIA AJAX
          $mes=$_POST["mes"];
          $ano=$_POST["ano"];
          $fecha= ($ano."-".$mes."%");

        //la consulta debe hacerse asi para seleccionar el mes/ano

        /*importante: explicacion de cuando se pone el like y % en una consulta: like sirve para buscar una palabra en especifica dentro de la columna, por ejemplo buscar 09 dentro de 2017-09-04. Los %% se ocupan para indicar en que parte se quiere buscar, si se pone like '%queBusco' significa que lo buscas al final de una cadena, si pones 'queBusco%' significa que se busca al principio de la cadena y si pones '%queBusco%' significa que lo busca en medio, asi la imprimo la consulta en phpmyadmin SELECT * FROM compras WHERE fecha_compra like '2017-09%'*/

  
        $sql= "SELECT * FROM compras WHERE fecha_compra like ? ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$fecha);
        $sql->execute();
        return $result = $sql->fetchAll(PDO::FETCH_ASSOC);


    }


    public function get_compras_por_id_proveedor($id_proveedor){

    $conectar= parent::conexion();
    $sql="select * from compras where id_proveedor=?";
    $sql=$conectar->prepare($sql);
    $sql->bindValue(1, $id_proveedor);
    $sql->execute();

    return $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);


  }

  public function get_detalle_compras_por_id_proveedor($id_proveedor){

    $conectar= parent::conexion();
    $sql="select * from detalle_compras where id_proveedor=?";
    $sql=$conectar->prepare($sql);
    $sql->bindValue(1, $id_proveedor);
    $sql->execute();
    return $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);

  }

   public function get_compras_por_id_usuario($id_usuario){

    $conectar= parent::conexion();
    $sql="select * from compras where id_usuario=?";
    $sql=$conectar->prepare($sql);
    $sql->bindValue(1, $id_usuario);
    $sql->execute();

    return $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);


    }


    public function get_detalle_compras_por_id_usuario($id_usuario){

        $conectar= parent::conexion();
        $sql="select * from detalle_compras where id_usuario=?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $id_usuario);
        $sql->execute();

        return $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);


    }



        /*REPORTES COMPRAS*/

    public function get_compras_reporte_general(){

      $conectar=parent::conexion();
      parent::set_names();
       //hacer la consulta que seleccione la fecha de mayor a menos
      $sql="SELECT  MONTHname(fecha_compra) as mes, MONTH(fecha_compra) as numero_mes, YEAR(fecha_compra) as ano, SUM(total_compra) as total_compra FROM compras where fecha_compra>?  GROUP BY year(fecha_compra), month(fecha_compra) ";      
      $mes=date("m");
      $año= date("Y")-1;
      $dia=date("d");
      $fecha= $año."-".$mes."-".$dia;
      $sql=$conectar->prepare($sql);
      $sql->bindValue(1,$fecha );
      $sql->execute();
      return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);

    }
     
     //suma el total de compras por año

     public function suma_compras_total_ano(){

      $conectar=parent::conexion();
      $sql="SELECT YEAR(fecha_compra) as ano,SUM(total_compra) as total_compra_ano FROM compras where estado_compra='1' GROUP BY YEAR(fecha_compra) desc";      
      $sql=$conectar->prepare($sql);
      $sql->execute();
      return $resultado= $sql->fetchAll();


     }
     
     //recorro el array para traerme la lista de una en vez de traerlo con el return, y hago el formato para la grafica
     //suma total por año 
   public function suma_compras_total_grafica(){
      $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

      $conectar=parent::conexion();
      $sql="SELECT  MONTH(fecha_compra) as mes, YEAR(fecha_compra) as ano, SUM(total_compra) as total_compra_mes FROM compras where fecha_compra>?  GROUP BY year(fecha_compra), month(fecha_compra) order by YEAR(fecha_compra) ";      
      $mes=date("m");
      $año= date("Y")-1;
      $dia=date("d");

      $fecha= $año."-".$mes."-".$dia;
    
      $sql=$conectar->prepare($sql);
      $sql->bindValue(1,$fecha );
      $sql->execute();
      $resultado= $sql->fetchAll();
             
             //recorro el array y lo imprimo
      foreach($resultado as $row){

        $mes= $output["mes"]=$row["mes"];
        $p = $output["total_compra_mes"]=$row["total_compra_mes"];
        $año= $row["ano"];
        echo $grafica= "{name:'".$meses[$mes-1]." ".$año."', y:".$p."},";

      }



     }
      public function suma_compras_canceladas_total_grafica(){

        $conectar=parent::conexion();
          $sql="SELECT YEAR(fecha_compra) as ano,SUM(total_compra) as total_compra_ano FROM compras where estado_compra='0' GROUP BY YEAR(fecha_compra) desc";
          $sql=$conectar->prepare($sql);
          $sql->execute();
          $resultado= $sql->fetchAll();
            
             //recorro el array y lo imprimo
           foreach($resultado as $row){

                 $ano= $output["ano"]=$row["ano"];
                 $p = $output["total_compra_ano"]=$row["total_compra_ano"];
                 echo $grafica= "{name:'".$ano."', y:".$p."},";

           }


       }


       /*REPORTE DE COMPRAS MENSUAL*/

     public function suma_compras_anio_mes_grafica($año,$mes){

          $conectar=parent::conexion();
          parent::set_names();
              
         //se usa para traducir el mes en la grafica
       //imprime la fecha por separado ejemplo: dia, mes y año
          $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

         

       //SI EXISTE EL ENVIO POST ENTONCES SE MUESTRA LA FECHA SELECCIONADA
        if(isset($_POST["year"])&&isset($_POST["mes"])){

                  $year=$_POST["year"];
                  $mes=$_POST["mes"]+1;
                  $sql="SELECT YEAR(fecha_compra) as ano, MONTHname(fecha_compra) as mes, SUM(total_compra) as total_compra,tipo_pago_compra,estado_compra
                  FROM compras WHERE YEAR(fecha_compra)=? and MONTH(fecha_compra)=? and estado_compra='1' GROUP BY mes,tipo_pago_compra desc";
                  

                  $sql=$conectar->prepare($sql);
                  $sql->bindValue(1,$year);
                  $sql->bindValue(2,$mes);
                  $sql->execute();
                  $resultado= $sql->fetchAll();
                       
                       //recorro el array y lo imprimo
                     foreach($resultado as $row){
                      $estado="";
                      if($row["estado_compra"]==1){
                        $estado="PAGADO";
                      }
                      if($row["estado_compra"]==2){
                        $estado="PENDIENTE";
                      }
                      if($row["estado_compra"]==0){
                        $estado="ANULADO";
                      }
              
              
              
                      $mes= $output["mes"]=$row["tipo_pago_compra"];
                      $p = $output["total_compra"]=$row["total_compra"];
          
                   echo $grafica= "{name:'".$mes."', y:".$p."},";
                     }//cierre del foreach


         } else {


            //sino se envia el POST, entonces se mostraria los datos del año actual cuando se abra la pagina por primera vez
            $year=date("Y"); 
            $mes=date("n");

            $sql="SELECT YEAR(fecha_compra) as ano, MONTHname(fecha_compra) as mes, SUM(total_compra) as total_compra,tipo_pago_compra,estado_compra
            FROM compras WHERE YEAR(fecha_compra)=? and MONTH(fecha_compra)=? and estado_compra='1' GROUP BY mes,tipo_pago_compra desc";


           $sql=$conectar->prepare($sql);
           $sql->bindValue(1,$año);
           $sql->bindValue(2,$mes);
           $sql->execute();

           $resultado= $sql->fetchAll();
             
             //recorro el array y lo imprimo
           foreach($resultado as $row){
            $estado="";
            if($row["estado_compra"]==1){
              $estado="PAGADO";
            }
            if($row["estado_compra"]==2){
              $estado="PENDIENTE";
            }
            if($row["estado_compra"]==0){
              $estado="ANULADO";
            }
    
    
    
            $mes= $output["mes"]=$row["tipo_pago_compra"];
            $p = $output["total_compra"]=$row["total_compra"];

         echo $grafica= "{name:'".$mes."', y:".$p."},";
           }//cierre del foreach


         }//cierre del else


     }


     public function get_year_compras(){

          $conectar=parent::conexion();
          $sql="select year(fecha_compra) as año from compras group by year(fecha_compra) asc";         
          $sql=$conectar->prepare($sql);
          $sql->execute();
          return $resultado= $sql->fetchAll();


     }
     
     public function get_mes_compras(){

      $conectar=parent::conexion();
      $sql="select month(fecha_compra) as mes from compras group by month(fecha_compra) asc";
      $sql=$conectar->prepare($sql);
      $sql->execute();
      return $resultado= $sql->fetchAll();


   }


    public function get_compras_mensual($año,$mes){


      $conectar=parent::conexion();
       if(isset($_POST["year"]) && isset($_POST["mes"])){

        $año=$_POST["year"];
        $mes=$_POST["mes"]+1;

        $sql="select MONTHname(fecha_compra) as mes, MONTH(fecha_compra) as numero_mes, YEAR(fecha_compra) as ano, SUM(total_compra) as total_compra,tipo_pago_compra,estado_compra
        from compras where YEAR(fecha_compra)=? and  MONTH(fecha_compra)=? and estado_compra='1' group by mes,tipo_pago_compra ";

        

        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$año);
        $sql->bindValue(2,$mes);
        $sql->execute();
        return $resultado= $sql->fetchAll();

      } else {


              //sino se envia el POST, entonces se mostraria los datos del año actual cuando se abra la pagina por primera vez    
         $año=date("Y");
         $mes=date("n");


          $sql="select MONTHname(fecha_compra) as mes, MONTH(fecha_compra) as numero_mes, YEAR(fecha_compra) as ano, SUM(total_compra) as total_compra,tipo_pago_compra,estado_compra
          from compras where YEAR(fecha_compra)=? and  MONTH(fecha_compra)=? group by mes,tipo_pago_compra,estado_compra ";
      

          $sql=$conectar->prepare($sql);
          $sql->bindValue(1,$año);
          $sql->bindValue(2,$mes);
          $sql->execute();
          return $resultado= $sql->fetchAll();



     }//cierre del else
        
  }



         /*REPORTE POR RANGO DE FECHA Y PROVEEDOR*/


   public function get_pedido_por_fecha($cuit,$fecha_inicial,$fecha_final){

            $conectar=parent::conexion();
            parent::set_names();
                
          
            $date_inicial = $_POST["datepicker"];
            $date = str_replace('/', '-', $date_inicial);
            $fecha_inicial = date("Y-m-d", strtotime($date));
            $date_final = $_POST["datepicker2"];
            $date = str_replace('/', '-', $date_final);
            $fecha_final = date("Y-m-d", strtotime($date));

            $sql="select * from detalle_compras where cuit_proveedor=? and fecha_compra_dc>=? and fecha_compra_dc<=? and estado_dc='1';";

  
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1,$cuit);
            $sql->bindValue(2,$fecha_inicial);
            $sql->bindValue(3,$fecha_final);
            $sql->execute();

            return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
             

    }



    public function get_cant_productos_por_fecha($cuit,$fecha_inicial,$fecha_final){

            $conectar=parent::conexion();
            parent::set_names();
            $date_inicial = $_POST["datepicker"];
            $date = str_replace('/', '-', $date_inicial);
            $fecha_inicial = date("Y-m-d", strtotime($date)); 
            $date_final = $_POST["datepicker2"];
            $date = str_replace('/', '-', $date_final);
            $fecha_final = date("Y-m-d", strtotime($date));


            $sql="select sum(cantidad_compra_dc) as total from detalle_compras where cuit_proveedor=? and fecha_compra_dc >=? and fecha_compra_dc <=? and estado_dc = '1';";

        
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1,$cuit);
            $sql->bindValue(2,$fecha_inicial);
            $sql->bindValue(3,$fecha_final);
            $sql->execute();

            return $resultado=$sql->fetch(PDO::FETCH_ASSOC);
           
        } 



        public function get_compras_anio_actual(){

            $conectar=parent::conexion();
            parent::set_names();

            $sql="SELECT YEAR(fecha_compra) as ano, MONTHname(fecha_compra) as mes, SUM(total_compra) as total_compra_mes FROM compras WHERE YEAR(fecha_compra)=YEAR(CURDATE()) and estado_compra='1' GROUP BY MONTHname(fecha_compra) desc";

            $sql=$conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();

        }


          public function get_compras_anio_actual_grafica(){

           $conectar=parent::conexion();
           parent::set_names();
            $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
           
           $sql="SELECT  MONTHname(fecha_compra) as mes, SUM(total_compra) as total_compra_mes FROM compras WHERE YEAR(fecha_compra)=YEAR(CURDATE()) and estado_compra='1' GROUP BY MONTHname(fecha_compra) desc";
               
               $sql=$conectar->prepare($sql);
               $sql->execute();

               $resultado= $sql->fetchAll();
                 
                 //recorro el array y lo imprimo
               foreach($resultado as $row){


                    $mes= $output["mes"]=$meses[date("n", strtotime($row["mes"]))-1];
                    $p = $output["total_compra_mes"]=$row["total_compra_mes"];

                  echo $grafica= "{name:'".$mes."', y:".$p."},";

               }
     
        }
       

    }




?>