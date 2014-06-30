/**
 * Ejemplos varios
 * @returns {undefined}
 */
$(function() {
    
    

    $("#reportes_acordion").accordion({
        heightStyle: "content"
    });


    /*REPORTES RESERVA EQUIPOS */

    /* generar reportes reserva equipor por estado de la reserva */

    $("#reportes_select_estado_equipo").multiselect({
        noneSelectedText: "Seleccione el estado",
        selectedText: "# de # seleccionados"
    });

    $("#reportes_generar_reporte_porestado_equipo").button().on("click", function() {
            $.blockUI({message: "Generando Reporte Por favor Espere"});

        $.post("controlador/fachada.php", {
            clase: 'UtilReportes',
            oper: 'getPrestamoEquipos', 
            estado: $("#reportes_select_estado_equipo").val()  
        },
        function(data) {
            if (data.msj === "bien")
                descargar(data.var_ratu);
            else {
                alert(data.msj);
            }
        }, "json").always(function(){
           $.unblockUI();
          });
    });


    /* generar reporte reserva equipo por fecha de  la reserva */

    $("#reportes_fecha_inicial_equipos").datepicker({dateFormat: 'yy-mm-dd', firstDay: 1});

    $("#reportes_fecha_final_equipos").datepicker({dateFormat: 'yy-mm-dd', firstDay: 1});

    $("#reportes_generar_reporte_porfecha_equipo").button().on("click", function() {

  $.blockUI({message: "Generando Reporte Por favor Espere"});
        $.post("controlador/fachada.php", {
            clase: 'UtilReportes',
            oper: 'getPrestamoEquiposporFecha',
            fechainicial: $("#reportes_fecha_inicial_equipos").val(),
            fechafinal: $("#reportes_fecha_final_equipos").val()
        },
        function(data) {
            if (data.msj === "bien")
                descargar(data.var_ratu);
            else {
                alert(data.msj);
            }
        }, "json").always(function(){
           $.unblockUI();

          });
    });




    /* generar reportes reserva equipo por nombre del equipo */

    var equipos = getElementos({
        'clase': 'UtilReportes',
        'oper': 'getListaEquipos'
    });

    $("#reportes_equipo").agregarElementos(equipos);


    $("#reportes_generar_reporte_pornombre_equipo").button().on("click", function() {
          $.blockUI({message: "Generando Reporte Por favor Espere"});
        $.post("controlador/fachada.php", {
            clase: 'UtilReportes',
            oper: 'getPrestamoEquiposporEquipo',
            nombre: $("#reportes_equipo :selected").text()
        },
        function(data) {
            if (data.msj === "bien")
                descargar(data.var_ratu);
            else {
                alert(data.msj);
            }
        }, "json").always(function(){
           $.unblockUI();

          });
    });



    /*REPORTES RESERVA SALAS */


    /* generar reportes reserva sala por estado de la reserva */
//    
    $("#reportes_select_estado_sala").multiselect({
        noneSelectedText: "Seleccione el estado",
         selectedText: "# de # seleccionados"
//        selectedList: false// 0-based index,
    });

    $("#reportes_generar_reporte_porestado_sala").button().on("click", function() {
  $.blockUI({message: "Generando Reporte Por favor Espere"});
        $.post("controlador/fachada.php", {
            clase: 'UtilReportes',
            oper: 'getPrestamoSalas',
            estado: $("#reportes_select_estado_sala").val()
        },
        function(data) {
           if (data.msj === "bien")
                descargar(data.var_ratu);
            else {
                alert(data.msj);
            }
        }, "json").always(function(){
           $.unblockUI();

          });

    });

    /* generar reportes reserva sala por fecha de reserva */


    $("#reportes_fecha_inicial_sala").datepicker({dateFormat: 'yy-mm-dd', firstDay: 1});

    $("#reportes_fecha_final_sala").datepicker({dateFormat: 'yy-mm-dd', firstDay: 1});

    $("#reportes_generar_reporte_porfecha_sala").button().on("click", function() {
  $.blockUI({message: "Generando Reporte Por favor Espere"});
        $.post("controlador/fachada.php", {
            clase: 'UtilReportes',
            oper: 'getPrestamoSalasporFecha',
            fechainicial: $("#reportes_fecha_inicial_sala").val(),
            fechafinal: $("#reportes_fecha_final_sala").val()
        },
        function(data) {
            if (data.msj === "bien")
                descargar(data.var_ratu);
            else {
                alert(data.msj);
            }
        }, "json").always(function(){
           $.unblockUI();

          });
    });


    /* generar reportes reserva sala por nombre de sala */

    var salas = getElementos({
        'clase': 'UtilReportes',
        'oper': 'getListaSalas'
    });

    $("#reportes_sala").agregarElementos(salas);

    $("#reportes_generar_reporte_pornombre_sala").button().on("click", function() {
          $.blockUI({message: "Generando Reporte Por favor Espere"});
        $.post("controlador/fachada.php", {
            clase: 'UtilReportes',
            oper: 'getPrestamoSalasporSala',
            nombres: $("#reportes_sala :selected").text()
        },
        function(data) {
             if (data.msj === "bien")
                descargar(data.var_ratu);
            else {
                alert(data.msj);
            }
        }, "json").always(function(){
           $.unblockUI();

          });
    });





});