var tabla;

var tabla_en_compras;

var tabla_compras_mes;

//Función que se ejecuta al inicio
function init() {
  listar();
}

//Función Listar
function listar() {
  tabla = $("#compras_data")
    .dataTable({
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      dom: "Bfrtip", //Definimos los elementos del control de tabla
      buttons: [],
      ajax: {
        url: "../ajax/compras.php?op=buscar_compras",
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
//VER DETALLE PROVEEDOR-COMPRA

$(document).on("click", ".detalle", function () {
  //toma el valor del id
  var numero_compra = $(this).attr("id");
  console.log("id:" + numero_compra);

  $.ajax({
    url: "../ajax/compras.php?op=ver_detalle_proveedor_compra",
    method: "POST",
    data: { numero_compra: numero_compra },
    cache: false,
    dataType: "json",
    success: function (data) {
      $("#proveedor").html(data.nombre_proveedor);
      $("#numero_compra").html(data.numero_compra);
      $("#cuit_proveedor").html(data.cuit_proveedor);
      $("#direccion").html(data.direccion_proveedor);
      $("#fecha_compra").html(data.fecha_compra);
    },
  });
});

//VER DETALLE COMPRA
$(document).on("click", ".detalle", function () {
  //toma el valor del id
  var numero_compra = $(this).attr("id");

  $.ajax({
    url: "../ajax/compras.php?op=ver_detalle_compra",
    method: "POST",
    data: { numero_compra: numero_compra },
    cache: false,
    success: function (data) {
      $("#detalles").html(data);
    },
  });
});

//CAMBIAR ESTADO DE LA COMPRA

function cambiarEstado(id_compras, numero_compra, est) {
  bootbox.confirm(
    "¿Estas seguro que quieres cambiar el estado de esta compra?",
    function (result) {
      if (result) {
        $.ajax({
          url: "../ajax/compras.php?op=cambiar_estado_compra",
          method: "POST",
          data: {
            id_compras: id_compras,
            numero_compra: numero_compra,
            est: est,
          },
          cache: false,

          success: function (data) {
            $("#compras_data").DataTable().ajax.reload();

            //refresca el datatable de compras por fecha
            $("#compras_fecha_data").DataTable().ajax.reload();

            //refresca el datatable de compras por fecha - mes
            $("#compras_fecha_mes_data").DataTable().ajax.reload();
          },
        });
      }
    }
  ); //bootbox
}

//CONSULTA COMPRAS-FECHA
$(document).on("click", "#btn_compra_fecha", function () {
  var fecha_inicial = $("#datepicker").val();
  var fecha_final = $("#datepicker2").val();

  //validamos si existe las fechas entonces se ejecuta el ajax

  if (fecha_inicial != "" && fecha_final != "") {
    // BUSCA LAS COMPRAS POR FECHA
    tabla_en_compras = $("#compras_fecha_data").DataTable({
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      dom: "Bfrtip", //Definimos los elementos del control de tabla
      buttons: [],

      ajax: {
        url: "../ajax/compras.php?op=buscar_compras_fecha",
        type: "post",
        data: { fecha_inicial: fecha_inicial, fecha_final: fecha_final },
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
    });
  } //cerrando condicional de las fechas
});

//****************************************************************

//FECHA COMPRA POR MES

$(document).on("click", "#btn_compra_fecha_mes", function () {
  var mes = $("#mes").val();
  var ano = $("#ano").val();

  //validamos si existe las fechas entonces se ejecuta el ajax

  if (mes != "" && ano != "") {
    // BUSCA LAS COMPRAS POR FECHA
    var tabla_compras_mes = $("#compras_fecha_mes_data").DataTable({
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      dom: "Bfrtip", //Definimos los elementos del control de tabla
      buttons: [],

      ajax: {
        url: "../ajax/compras.php?op=buscar_compras_fecha_mes",
        type: "post",
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
    });
  } //cerrando condicional de las fechas
});

init();
