<?php

   //conexión a la base de datos

   require_once("../config/conexion.php");

   class Proveedor extends Conectar{

       

      public function get_filas_proveedor(){

        $conectar= parent::conexion();  
        $sql="select * from proveedor";            
        $sql=$conectar->prepare($sql);
        $sql->execute();
        $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);
        return $sql->rowCount();
      
      }


      //método para seleccionar registros

      public function get_proveedores(){

        $conectar=parent::conexion();
        parent::set_names();
        $sql="select * from proveedor";
        $sql=$conectar->prepare($sql);
        $sql->execute();

        return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
      }

   	    //método para insertar registros

      public function registrar_proveedor($cuit,$proveedor,$telefono,$correo,$direccion,$estado,$id_usuario){


          $conectar= parent::conexion();
          parent::set_names();

          $sql="insert into proveedor
          values(null,?,?,?,?,?,now(),?,?);";

        
          $sql=$conectar->prepare($sql);
          $sql->bindValue(1, $_POST["cuit"]);
          $sql->bindValue(2, $_POST["razon"]);
          $sql->bindValue(3, $_POST["telefono"]);
          $sql->bindValue(4, $_POST["email"]);
          $sql->bindValue(5, $_POST["direccion"]);
          $sql->bindValue(6, $_POST["estado"]);
          $sql->bindValue(7, $_POST["id_usuario"]);
          $sql->execute();
    
          
          
      }

        //método para mostrar los datos de un registro a modificar
      public function get_proveedor_por_cuit($cuit){

        $conectar= parent::conexion();
        parent::set_names();
        $sql="select * from proveedor where cuit_proveedor=?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $cuit);
        $sql->execute();
        return $resultado=$sql->fetchAll();

      }

         //este metodo es para validar el id del proveedor(luego llamamos el metodo de editar_estado()) 
      //el id_proveedor se envia por ajax cuando se editar el boton cambiar estado y que se ejecuta el evento onclick y llama la funcion de javascript
      public function get_proveedor_por_id($id_proveedor){

        $conectar= parent::conexion();
        $sql="select * from proveedor where id_proveedor=?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $id_proveedor);
        $sql->execute();
        return $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);


      } 


        
        /*metodo que valida si hay registros activos*/
      public function get_proveedor_por_id_estado($id_proveedor,$estado){

        $conectar= parent::conexion();
        //declaramos que el estado esté activo, igual a 1
        $estado=1;
        $sql="select * from proveedor where id_proveedor=? and estado_proveedor=?";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $id_proveedor);
        $sql->bindValue(2, $estado);
        $sql->execute();

        return $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);


        }


        public function editar_proveedor($cuit,$proveedor,$telefono,$correo,$direccion,$estado,$id_usuario){

            $conectar=parent::conexion();
            parent::set_names();
            require_once("Proveedores.php");
            $proveedor = new Proveedor();
            //verifica si la cuit tiene registro asociado a compras
            $proveedor_compras=$proveedor->get_proveedor_por_cuit_compras($_POST["cuit_proveedor"]);
              //verifica si la cuit tiene registro asociado a detalle_compras
            $proveedor_detalle_compras=$proveedor->get_proveedor_por_cuit_detalle_compras($_POST["cuit_proveedor"]);
              //si la cuit del proveedor NO tiene registros asociados en las tablas compras y detalle_compras entonces se puede editar el proveedor completo
            if(is_array($proveedor_compras)==true and count($proveedor_compras)==0 and is_array($proveedor_detalle_compras)==true and count($proveedor_detalle_compras)==0){


              $sql="update proveedor set 

                 cuit_proveedor=?,
                 nombre_proveedor=?,
                 telefono_proveedor=?,
                 correo_proveedor=?,
                 direccion_proveedor=?,
                 estado_proveedor=?,
                 id_usuario=?
                 where cuit_proveedor=? ";
                
              $sql=$conectar->prepare($sql);
              $sql->bindValue(1, $_POST["cuit"]);
              $sql->bindValue(2, $_POST["razon"]);
              $sql->bindValue(3, $_POST["telefono"]);
              $sql->bindValue(4, $_POST["email"]);
              $sql->bindValue(5, $_POST["direccion"]);
              $sql->bindValue(6, $_POST["estado"]);
              $sql->bindValue(7, $_POST["id_usuario"]);
              $sql->bindValue(8, $_POST["cuit_proveedor"]);
              $sql->execute();


            } else {

                  
          //si el proveedor tiene registros asociados en compras y detalle_compras entonces no se edita el la cuit del proveedor y la razon social

              $sql="update proveedor set 
                
                  telefono_proveedor=?,
                  correo_proveedor=?,
                  direccion_proveedor=?,  
                  estado_proveedor=?,
                  id_usuario=?
                  where 
                  cuit_proveedor=?";

              $sql=$conectar->prepare($sql);     
              $sql->bindValue(1, $_POST["telefono"]);
              $sql->bindValue(2, $_POST["email"]);
              $sql->bindValue(3, $_POST["direccion"]);
              $sql->bindValue(4, $_POST["estado"]);
              $sql->bindValue(5, $_POST["id_usuario"]);
              $sql->bindValue(6, $_POST["cuit_proveedor"]);
              $sql->execute();

            }

        }


         //método si el proveedor existe en la base de datos
        //valida si existe la cuit, proveedor o correo, si existe entonces se hace el registro del proveedor
        public function get_datos_proveedor($cuit,$proveedor, $correo){

          $conectar=parent::conexion();

          $sql="select * from proveedor where cuit_proveedor=? or nombre_proveedor=? or correo_proveedor=?";
          $sql=$conectar->prepare($sql);
          $sql->bindValue(1, $cuit);
          $sql->bindValue(2, $proveedor);
          $sql->bindValue(3, $correo);
          $sql->execute();

         
           return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
        }


          //método para activar Y/0 desactivar el estado del proveedor

        public function editar_estado($id_proveedor,$estado){

        	 $conectar=parent::conexion();

        	 //si el estado es igual a 0 entonces el estado cambia a 1
        	 //el parametro est se envia por via ajax
        	 if($_POST["est"]=="0"){

        	   $estado=1;

        	 } else {

        	 	 $estado=0;
        	 }

        	 $sql="update proveedor set 
              
              estado_proveedor=?
              where 
              id_proveedor=?";

        	 $sql=$conectar->prepare($sql);
        	 $sql->bindValue(1,$estado);
        	 $sql->bindValue(2,$id_proveedor);
        	 $sql->execute();
        }

       

        public function get_proveedor_por_id_usuario($id_usuario){

          $conectar= parent::conexion();
          $sql="select * from proveedor where id_usuario=?";
          $sql=$conectar->prepare($sql);
          $sql->bindValue(1, $id_usuario);
          $sql->execute();
          return $resultado= $sql->fetchAll(PDO::FETCH_ASSOC);


      }


        //consulta si la cuit del proveedor tiene una compra asociada
       
       public function get_proveedor_por_cuit_compras($cuit_proveedor){
  
            $conectar=parent::conexion();
            parent::set_names();

            $sql="select p.cuit_proveedor,c.cuit_proveedor
            from proveedor p 
            INNER JOIN compras c ON p.cuit_proveedor=c.cuit_proveedor
            where p.cuit_proveedor=?";

            $sql=$conectar->prepare($sql);
            $sql->bindValue(1,$cuit_proveedor);
            $sql->execute();

             return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);

    }

      
      //consulta si la cuit del proveedor tiene un detalle_compra asociado
      public function get_proveedor_por_cuit_detalle_compras($cuit_proveedor){

          $conectar=parent::conexion();
          parent::set_names();
          $sql="use dbproyecto;
           select p.cuit_proveedor,d.cuit_proveedor
           from proveedor as  p 
            INNER JOIN detalle_compras as d
            ON p.cuit_proveedor=?";
          $sql=$conectar->prepare($sql);
          $sql->bindValue(1,$cuit_proveedor);
          $sql->execute();

           return $resultado=$sql->fetchAll(PDO::FETCH_ASSOC);
    

       }




   
}


?>