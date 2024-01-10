var tabla;
var id_cliente = getParameterByName("id_cliente");
console.log("id:" + id_cliente);
//agrega el id del cliente por si se necesia hacer un pago a cuenta

//cuando se da click al boton submit entonces se ejecuta la funcion guardaryeditar(e);
$("#pago_form").on("submit", function (e) {
  guardaryeditar(e);
});

function getParameterByName(name) {
  name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
  return results === null
    ? ""
    : decodeURIComponent(results[1].replace(/\+/g, " "));
}
function listar_detalle_cc() {
  tabla = $("#cuenta_corriente_data")
    .dataTable({
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      dom: "Bfrtip", //Definimos los elementos del control de tabla
      buttons: [],
      ajax: {
        url:
          "../ajax/cuenta_corriente.php?op=ver_detalle_ventas_cc_cliente&id_cliente=" +
          id_cliente,
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

//VER DETALLE CLIENTE-VENTA
$(document).on("click", ".detalle", function () {
  //toma el valor del id
  var numero_venta = $(this).attr("id");

  $.ajax({
    url: "../ajax/ventas.php?op=ver_detalle_cliente_venta",
    method: "POST",
    data: { numero_venta: numero_venta },
    cache: false,
    dataType: "json",
    success: function (data) {
      $("#cliente").html(data.cliente);
      $("#numero_venta").html(data.numero_venta);
      $("#dni_cliente").html(data.dni_cliente);
      $("#direccion").html(data.direccion);
      $("#fecha_venta").html(data.fecha_venta);
    },
  });
});

//VER DETALLE VENTA
$(document).on("click", ".detalle", function () {
  //toma el valor del id
  var numero_venta = $(this).attr("id");

  $.ajax({
    url: "../ajax/ventas.php?op=ver_detalle_venta",
    method: "POST",
    data: { numero_venta: numero_venta },
    cache: false,
    success: function (data) {
      $("#detalles").html(data);
    },
  });
});

function mostrar_total() {
  $.ajax({
    url: "../ajax/cuenta_corriente.php?op=ver_total_cc_cliente",
    method: "POST",
    data: { id_cliente: id_cliente },
    cache: false,

    success: function (dato) {
      $("#total_cc").html("$" + dato);
      $("#total_venta_cc").html("$" + dato);
    },
  });
}

//CAMBIAR ESTADO DE LA VENTA

function cambiarEstado(id_detalle_cc, id_cuenta_corriente, est) {
  console.log(id_detalle_cc, id_cuenta_corriente, est);

  bootbox.confirm(
    "¿Estas seguro que deseas cambiar el estado de este pago?",
    function (result) {
      if (result) {
        $.ajax({
          url: "../ajax/cuenta_corriente.php?op=cambiar_estado_venta_dc",
          method: "POST",
          data: {
            id_detalle_cc: id_detalle_cc,
            id_cuenta_corriente: id_cuenta_corriente,
            est: est,
          },
          cache: false,

          success: function (data) {
            $("#cuenta_corriente_data").DataTable().ajax.reload();
            mostrar_total();
          },
        });

        $.ajax({
          url: "../ajax/ventas.php?op=cambiar_estado_venta_cc",
          method: "POST",
          //toma el valor del id y del estado
          data: { id_detalle_cc: id_detalle_cc, est: est },
          cache: false,

          success: function (data) {
            $("#ventas_data").DataTable().ajax.reload();
            //refresca el datatable de ventas por fecha
            $("#ventas_fecha_data").DataTable().ajax.reload();
            //refresca el datatable de ventas por fecha - mes
            $("#ventas_fecha_mes_data").DataTable().ajax.reload();
          },
        });
      }
    }
  ); //bootbox
}

function guardaryeditar(e) {
  e.preventDefault(); //No se activará la acción predeterminada del evento
  var formData = new FormData($("#pago_form")[0]);
  formData.append("id_cliente", id_cliente);

  $.ajax({
    url: "../ajax/cuenta_corriente.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      $("#pago_form")[0].reset();
      $("#pagoModal").modal("hide");
      $("#cuenta_corriente_data").DataTable().ajax.reload();
      mostrar_total();
    },
  });
}

function nombre_cliente() {
  $.ajax({
    url: "../ajax/cliente.php?op=buscar_cliente_id",
    method: "POST",
    data: { id_cliente: id_cliente },
    cache: false,

    success: function (dato) {
      $("#h2_nombre_cliente").html(dato);
    },
  });
}
nombre_cliente();
listar_detalle_cc();
mostrar_total();
