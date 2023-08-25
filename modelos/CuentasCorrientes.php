<?php

	   //conexión a la base de datos

	   require_once("../config/conexion.php");

	   class CuentaCorriente extends Conectar{


      //toma todas las ventas del cliente hechas en cc
        public function get_filas_ventas_cc_cliente($dni_cliente){

         $conectar=parent::conexion();
         parent::set_names();

        $sql="select v.fecha_venta,v.numero_venta, v.nombre_cliente, v.dni_cliente,v.total_venta,v.tipo_pago_venta,c.id_cliente,c.dni_cliente,c.nombre_cliente, c.apellido_cliente,c.telefono_cliente,c.direccion_cliente,c.fecha_alta_cliente,c.estado_cliente
        from ventas as v, clientes as c
        where 
        v.dni_cliente=?
        and
        v.dni_cliente=c.dni_cliente
        and
        v.tipo_pago_venta='CUENTA CORRIENTE'
        
        and v.estado_venta!=0
        ;";


        $sql=$conectar->prepare($sql);
            

            $sql->bindValue(1,$id_cliente);
        $sql->execute();
        return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);

    }

           
       //método para seleccionar registros

   	   public function get_cuentas_corrientes(){

   	   	  $conectar=parent::conexion();
   	   	  parent::set_names();

   	   	

          $sql="select cc.id_cuentas_corrientes,cc.estado_cc,c.id_cliente,c.nombre_cliente,c.apellido_cliente,c.dni_cliente,c.direccion_cliente,c.telefono_cliente,cc.saldo_cc,c.estado_cliente  from cuentas_corrientes cc, clientes c where c.id_cliente=cc.id_cliente and c.estado_cliente='1'";

   	   	  $sql=$conectar->prepare($sql);
   	   	  $sql->execute();
           
   	   	  return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
   	   }
         
       #metodo para saber el total de cc por cliente
        public function ver_total_cc_cliente($id_cliente){

          $conectar=parent::conexion();
          parent::set_names();  

          $sql="select saldo_cc  from cuentas_corrientes where id_cliente=?";

          $sql=$conectar->prepare($sql);
          $sql->bindValue(1, $id_cliente);
          $sql->execute();
         
          
          $datos=$sql->fetchAll(PDO::FETCH_ASSOC);
          foreach($datos as $row)
			{
         $dato = $row["saldo_cc"];
         if($dato==null){
          $dato=0;
         }

			}
        echo number_format($dato,2);
      }

   	     //método para crear cuenta corriente

        public function crear_cuenta_corriente($id_cliente,$estado){

 
         $conectar=parent::conexion();
         parent::set_names();
          	 
				//el cliente se registra con un saldo de 0 pesos en la cuenta corriente
           $saldo=0;

           $sql="insert into cuentas_corrientes
           values(null,?,?,?)";

          
            $sql=$conectar->prepare($sql);

            $sql->bindValue(1, $id_cliente);
            $sql->bindValue(2, $saldo);
            $sql->bindValue(3, $estado);
            $sql->execute();
      
         
        }
        public function editar_cuenta_corriente($id_cuenta_corriente,$estado){

 
          $conectar=parent::conexion();
          parent::set_names();
        
 
          

        	 //si el estado es igual a 0 entonces el estado cambia a 1
        	 //el parametro est se envia por via ajax
        	 if($_POST["est"]==0 ){

        	   $est=1;

        	 } 
            if($_POST["est"]==1){

        	 	 $est=0;
        	 }

        	 $sql="update cuentas_corrientes set 
              
              estado_cc=?
              where   id_cuentas_corrientes=?	 ";

        	 $sql=$conectar->prepare($sql);

        	 $sql->bindValue(1,$est);
        	 $sql->bindValue(2,$id_cuenta_corriente);
        	 $sql->execute();

       
          
         }
         //método para crear detalle de cuenta corriente

         public function registrar_detalle_cc($id_venta,$id_cc,$total,$id_usuario,$id_cliente,$descripcion,$estado){


            $conectar= parent::conexion();
            parent::set_names();
 
            $sql="insert into detalle_cuentas_corrientes
            values(null,?,?,now(),?,?,?,?,?)";
 
           
             $sql=$conectar->prepare($sql);
 
             $sql->bindValue(1, $id_cc);
             $sql->bindValue(2, $id_venta);
             $sql->bindValue(3, $total);
             $sql->bindValue(4, $id_cliente);
             $sql->bindValue(5, $id_usuario);
             $sql->bindValue(6, $descripcion);
             $sql->bindValue(7, $estado);
             $sql->execute();
       
            


             $sql="select sum(d.monto_detalle_cc) as saldo from detalle_cuentas_corrientes as d  where  d.id_cliente=? and d.tipo_movimiento_detalle_cc='f'";

             $sql=$conectar->prepare($sql);
             $sql->bindValue(1, $id_cliente);
             $sql->execute();
            
             
             $datos=$sql->fetchAll(PDO::FETCH_ASSOC);
             foreach($datos as $row)
              {
              $saldo_adeudado = $row["saldo"];
              if($saldo_adeudado==null){
              $saldo_adeudado=0;
              }
            }
            $sql="select sum(d.monto_detalle_cc) as saldo from detalle_cuentas_corrientes as d where d.id_cliente=? and d.tipo_movimiento_detalle_cc='p'";

             $sql=$conectar->prepare($sql);
             $sql->bindValue(1, $id_cliente);
             $sql->execute();
            
             
             $datos=$sql->fetchAll(PDO::FETCH_ASSOC);
             foreach($datos as $row)
              {
              $saldo_pagado = $row["saldo"];
              if($saldo_pagado==null){
              $saldo_pagado=0;
              }
            }

            $saldo_actual=$saldo_adeudado-$saldo_pagado;

             $sql2 = "update cuentas_corrientes set 
                     
              saldo_cc=?
              where 
              id_cuentas_corrientes=?
              ";


              $sql2 = $conectar->prepare($sql2);
              $sql2->bindValue(1,$saldo_actual);
              $sql2->bindValue(2,$id_cc);
              $sql2->execute(); 

            

       
          
         }


         //método para mostrar los datos en detalles cuenta corriente por cliente
        public function get_cc_por_cliente($id_cliente){

            
            $conectar= parent::conexion();
            parent::set_names();

            $sql="select dc.id_detalle_cc,dc.id_cliente,dc.id_cuenta_corriente,dc.id_ventas,v.numero_venta,dc.fecha_detalle_cc,dc.monto_detalle_cc,dc.descripcion_detalle_cc,dc.tipo_movimiento_detalle_cc,u.usuario from usuarios as u,detalle_cuentas_corrientes as dc left join ventas as v
            on  v.id_ventas=dc.id_ventas where dc.id_cliente=? and u.id_usuario=dc.id_usuario";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $id_cliente);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }
     

        //metodo para tomar la id_cc de una cuenta corriente 
        public function get_idcc_por_cliente($id_cliente){

            
         $conectar= parent::conexion();
         parent::set_names();
         $sql="select * from cuentas_corrientes where id_cliente=?";
         $sql=$conectar->prepare($sql);
         $sql->bindValue(1, $id_cliente);
         $sql->execute();
         return $resultado=$sql->fetchAll();
     }

           

         //método para editar saldo de cc
       
        public function actualizar_saldo_cuenta_corriente($id_cliente,$id_cc){
          $conectar= parent::conexion();
          parent::set_names();

        
          $sql="select sum(d.monto_detalle_cc) as saldo from detalle_cuentas_corrientes as d  where  d.id_cliente=? and d.tipo_movimiento_detalle_cc='f'";

          $sql=$conectar->prepare($sql);
          $sql->bindValue(1, $id_cliente);
          $sql->execute();
         
          
          $datos=$sql->fetchAll(PDO::FETCH_ASSOC);
          foreach($datos as $row)
           {
           $saldo_adeudado = $row["saldo"];
           if($saldo_adeudado==null){
           $saldo_adeudado=0;
           }
         }
         $sql="select sum(d.monto_detalle_cc) as saldo from detalle_cuentas_corrientes as d where d.id_cliente=? and d.tipo_movimiento_detalle_cc='p'";

          $sql=$conectar->prepare($sql);
          $sql->bindValue(1, $id_cliente);
          $sql->execute();
         
          
          $datos=$sql->fetchAll(PDO::FETCH_ASSOC);
          foreach($datos as $row)
           {
           $saldo_pagado = $row["saldo"];
           if($saldo_pagado==null){
           $saldo_pagado=0;
           }
         }

         $saldo_actual=$saldo_adeudado-$saldo_pagado;

          $sql2 = "update cuentas_corrientes set 
                  
           saldo_cc=?
           where 
           id_cuentas_corrientes=?
           ";


           $sql2 = $conectar->prepare($sql2);
           $sql2->bindValue(1,$saldo_actual);
           $sql2->bindValue(2,$id_cc);
           $sql2->execute(); 

         

        }
        public function get_detalle_ventas_cc_cliente($id_cliente){

         $conectar=parent::conexion();

         $sql= "select * from detalle_cuentas_corrientes where id_cliente=?";

         $sql=$conectar->prepare($sql);

     
         $sql->bindValue(1, $id_cliente);
         $sql->execute();



         return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
      }

      public function get_detalle_cc_por_id($id_detalle_cc){

        $conectar=parent::conexion();

        $sql= "select * from detalle_cuentas_corrientes where id_detalle_cc=?";

        $sql=$conectar->prepare($sql);

    
        $sql->bindValue(1, $id_detalle_cc);
        $sql->execute();

        

        return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
     }



     public function ver_estado($id_cliente){

      $conectar=parent::conexion();

      $sql= "select * from cuentas_corrientes
       where id_cliente=?";

      $sql=$conectar->prepare($sql);

      $sql->bindValue(1, $id_cliente);
      $sql->execute();
      return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);


   }



        //consulta si la cuenta corriente tiene registros 
       
      public function get_registros_por_cc($id_cliente){

                
            $conectar=parent::conexion();
            parent::set_names();


            $sql="select *
            from detalle_cuentas_corrientes d 
            
            INNER JOIN cuentas_corrientes c ON c.id_cliente=d.id_cliente


            where c.id_cliente=?
            ";

            $sql=$conectar->prepare($sql);
            $sql->bindValue(1,$dni_cliente);
            $sql->execute();

            return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
    
      }

public function get_ventas_por_id_detalle_cc($id_detalle_cc){

    $conectar= parent::conexion();

    
      
        $sql="select * from id_ventas where id_detalle_cc=?";
        
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$id_detalle_cc);
        $sql->execute();

        return $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);

   
   }
   //sirve para tomar la id de la venta asi cambiamos el estado en ventas

   public function get_id_ventas_por_id_detalle_cc($id_detalle_cc){

        $conectar= parent::conexion();

        $sql="select * from detalle_cuentas_corrientes where id_detalle_cc=?";
        
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$id_detalle_cc);
        $sql->execute();

        return $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);

   
   }
  

   /*cambiar estado de la venta en cc, solo se cambia si se quiere pagar una cc*/

   public function cambiar_estado($id_detalle_cc,$id_cuenta_corriente,$estado){

    $cuentaCorriente= new CuentaCorriente();
    $conectar=parent::conexion();
    parent::set_names();
   
    
          
     
    $est = "";
    //el parametro est se envia por via ajax, viene del $est:est
    /*si el estado es ==p cambiaria a cancelado ==p Y SE EJECUTARIA TODO LO QUE ESTA ABAJO*/
    if($estado == "p"){
      $est = "c";
    


      $sql="update detalle_cuentas_corrientes set 
            
            tipo_movimiento_detalle_cc=?
            where 
            id_detalle_cc=?";


      $sql=$conectar->prepare($sql);
      $sql->bindValue(1,$est);
      $sql->bindValue(2,$id_detalle_cc);
      $sql->execute();
      $resultado= $sql->fetch(PDO::FETCH_ASSOC);


      /*una vez se cambie de estado a ACTIVO entonces actualizamos el saldo en la cuenta corriente*/



      $sql2="select * from detalle_cuentas_corrientes where id_detalle_cc=?";

      $sql2=$conectar->prepare($sql2);      
      $sql2->bindValue(1,$id_detalle_cc);
      $sql2->execute();
      $resultado=$sql2->fetchAll();

      foreach($resultado as $row){

          $id_cliente=$row["id_cliente"];
          $id_cc=$row["id_cuenta_corriente"];
      


      }
      $cuentaCorriente->actualizar_saldo_cuenta_corriente($id_cliente,$id_cc);
      
            

    }//cierre del if del estado
    else {

      /*si el estado es igual a 0, entonces pasaria a ANULADO y reverteria de nuevo la cantidad de productos al stock*/

      if($estado == "c"){
        $est = "p";
    


        $sql="update detalle_cuentas_corrientes set 
              
              tipo_movimiento_detalle_cc=?
              where 
              id_detalle_cc=?";
  
  
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1,$est);
        $sql->bindValue(2,$id_detalle_cc);
        $sql->execute();
        $resultado= $sql->fetch(PDO::FETCH_ASSOC);
  
  
        /*una vez se cambie de estado a ACTIVO entonces actualizamos el saldo en la cuenta corriente*/
  
  
  
        $sql2="select * from detalle_cuentas_corrientes where id_detalle_cc=?";
  
        $sql2=$conectar->prepare($sql2);      
        $sql2->bindValue(1,$id_detalle_cc);
        $sql2->execute();
        $resultado=$sql2->fetchAll();
  
        foreach($resultado as $row){
  
            $id_cliente=$row["id_cliente"];
            $id_cc=$row["id_cuenta_corriente"];
        
  
  
        }
        $cuentaCorriente->actualizar_saldo_cuenta_corriente($id_cliente,$id_cc);

      }


    }
    
  }
}

   ?>