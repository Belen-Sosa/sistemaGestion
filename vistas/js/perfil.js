//una vez se da click a submit se llama a la funcion editar_perfil(e)

$("#perfil_form").on("submit", function (e) {
  editar_perfil(e);
});

//MOSTRAR PERFIL DE USUARIO
function mostrar_perfil(id_usuario_perfil) {
  $.post(
    "../ajax/perfil.php?op=mostrar_perfil",
    { id_usuario_perfil: id_usuario_perfil },
    function (data, status) {
      data = JSON.parse(data);

      //alert(data.cedula);

      //console.log(data.cedula);

      $("#perfilModal").modal("show");
      $("#dni_usuario").val(data.dni);
      $("#nombre_perfil").val(data.nombre);
      $("#apellido_perfil").val(data.apellido);
      $("#usuario_perfil").val(data.usuario_perfil);
      $("#password1_perfil").val(data.password1);
      $("#password2_perfil").val(data.password2);
      $("#telefono_perfil").val(data.telefono);
      $("#email_perfil").val(data.correo);
      $("#direccion_perfil").val(data.direccion);
      $(".modal-title").text("Editar Usuario");
      $("#id_usuario_perfil").val(id_usuario_perfil);
      $("#action").val("Edit");
      $("#operation").val("Edit");
    }
  );
}

//EDITAR PERFIL

//la funcion guardaryeditar(e); se llama cuando se da click al boton submit
function editar_perfil(e) {
  e.preventDefault(); //No se activará la acción predeterminada del evento
  var formData = new FormData($("#perfil_form")[0]);

  var password1 = $("#password1_perfil").val();
  var password2 = $("#password2_perfil").val();

  if (password1 == password2) {
    $.ajax({
      url: "../ajax/perfil.php?op=editar_perfil",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,

      success: function (datos) {
        $("#perfilModal").modal("hide");
        $("#resultados_ajax").html(datos);
      },
    });
  } //cierre del condicional
}
