var tabla;

var tabla_en_compras;

var tabla_compras_mes;

//Función que se ejecuta al inicio
function init() {
  listar();
}

//Función Listar
function listar() {
  tabla = $("#cuenta_corriente_data")
    .dataTable({
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      dom: "Bfrtip", //Definimos los elementos del control de tabla
      buttons: [],
      ajax: {
        url: "../ajax/cuenta_corriente.php?op=buscar_cuentas_corrientes",
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      responsive: true,
      bInfo: true,
      iDisplayLength: 10, //Por cada 10 registros hace una paginación
      order: [[0, "desc"]], //Ordenar (columna,orden)

      language: {
        sProcessing: "Procesando...",

        sLengthMenu: "Mostrar _MENU_ registros",

        sZeroRecords: "No se encontraron resultados",

        sEmptyTable: "Ningún dato disponible en esta tabla",

        sInfo: "Mostrando un total de _TOTAL_ registros",

        sInfoEmpty: "Mostrando un total de 0 registros",

        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",

        sInfoPostFix: "",

        sSearch: "Buscar:",

        sUrl: "",

        sInfoThousands: ",",

        sLoadingRecords: "Cargando...",

        oPaginate: {
          sFirst: "Primero",

          sLast: "Último",

          sNext: "Siguiente",

          sPrevious: "Anterior",
        },

        oAria: {
          sSortAscending:
            ": Activar para ordenar la columna de manera ascendente",

          sSortDescending:
            ": Activar para ordenar la columna de manera descendente",
        },
      }, //cerrando language
    })
    .DataTable();
}

//CAMBIAR ESTADO DE LA COMPRA

function cambiarEstado(id_cuentas_corrientes, est) {
  console.log(id_cuentas_corrientes);
  console.log(est);

  bootbox.confirm(
    "¿Estas seguro que quieres cambiar de estado?",
    function (result) {
      if (result) {
        console.log(id_cuentas_corrientes + " el id de la cc");

        $.ajax({
          url: "../ajax/cuenta_corriente.php?op=activarydesactivar",
          method: "POST",

          //toma el valor del id y del estado

          data: { id_cuentas_corrientes: id_cuentas_corrientes, est: est },

          success: function (data) {
            console.log(data);

            $("#cuenta_corriente_data").DataTable().ajax.reload();
          },
        });
      }
    }
  ); //bootbox
}

//****************************************************************

//FECHA COMPRA POR MES

$(document).on("click", "#btn_compra_fecha_mes", function () {
  //var proveedor= $("#proveedor").val();

  var mes = $("#mes").val();
  var ano = $("#ano").val();

  //alert(mes);
  //alert(ano);

  //validamos si existe las fechas entonces se ejecuta el ajax

  if (mes != "" && ano != "") {
    // BUSCA LAS COMPRAS POR FECHA
    var tabla_compras_mes = $("#compras_fecha_mes_data").DataTable({
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      dom: "Bfrtip", //Definimos los elementos del control de tabla
      buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdf"],

      ajax: {
        url: "../ajax/compras.php?op=buscar_compras_fecha_mes",
        type: "post",
        //dataType : "json",
        data: { mes: mes, ano: ano },
        error: function (e) {
          console.log(e.responseText);
        },
      },

      bDestroy: true,
      responsive: true,
      bInfo: true,
      iDisplayLength: 10, //Por cada 10 registros hace una paginación
      order: [[0, "desc"]], //Ordenar (columna,orden)

      language: {
        sProcessing: "Procesando...",

        sLengthMenu: "Mostrar _MENU_ registros",

        sZeroRecords: "No se encontraron resultados",

        sEmptyTable: "Ningún dato disponible en esta tabla",

        sInfo:
          "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",

        sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",

        sInfoFiltered: "(filtrado de un total de _MAX_ registros)",

        sInfoPostFix: "",

        sSearch: "Buscar:",

        sUrl: "",

        sInfoThousands: ",",

        sLoadingRecords: "Cargando...",

        oPaginate: {
          sFirst: "Primero",

          sLast: "Último",

          sNext: "Siguiente",

          sPrevious: "Anterior",
        },

        oAria: {
          sSortAscending:
            ": Activar para ordenar la columna de manera ascendente",

          sSortDescending:
            ": Activar para ordenar la columna de manera descendente",
        },
      }, //cerrando language

      //"scrollX": true
    });
  } //cerrando condicional de las fechas
});

function limpiar() {
  $("#id_cliente").val("");
}

init();
