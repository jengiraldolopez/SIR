/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Compendio de funciones CRUD para xxxxxxxxxxxxxxxxxxxxxxxxxxxx
 */
//var equipoInicial;

$(function() {

    /* Inicio de lo que se ejecutará cuando el formulario Localidades.html cargue */

    var jqGridEquipos, jqGridTurnos, jqGridControl, idEquipo, idMonitor,idConsa, datosEquipo, idTurnos, datosTurnos, idSala, foranea, hora = $('#hora').val(), fecha = $('#fecha').text(), desde = $('#fechad').val(), hasta = $('#fechah').text();

    crearTablaControl();
    crearTablaEquipos();
    


    console.log("ñññññññññññññññññññññññññññññññññññññññññññññññññññññññññññññññññññññhora")
    console.log(hora)
    console.log(fecha)

    /* Fin de lo que se ejecutará cuando el formulario Localidades.html cargue */

    /* Implementación necesaria */

    /**
     * Muestra una tabla con la información de los temas de congresos a partir de
     * la información recibida de TemaCongreso.seleccionar()
     */

    $(function() {
        $("#tabs").tabs();
    });

    $("#action").button().on("click", function() {
        var hora = $('#hora').val(), fecha = $('#fecha').val();
        $.post("controlador/fachada.php", {// Comprobar comunicación C/S
            clase: 'Sala',
            oper: 'getSelectj',
            id: 'listasalas',
            hora: hora,
            fecha: fecha
        }, function(data) {
            //console.log(data)
//            var prueba = "riosueño";
//            prueba = prueba.substring(3, 6);
//            String.substring(5, 10);
//            console.log(prueba)
            $('#salas').html(data);
            $('#listasalas').on("change", function() {
                idSala = $(this).val(); // asignar a una variable el ID del grupo seleccionado y ...
                //  idSala = idSala.substring(5,10);
                crearTablaEquipos();
//                console.log("ñññññññññññññññññññññññññññññññññññññññññññññññññññññññññññññññññññññhora")
//                console.log(hora)
//                console.log(fecha)
            }).val('0').change();
        }, "json");
    });

    function crearTablaEquipos() {
        var columnas = {
            'cabeceras': ['nombre', 'fk_equipoo', 'inicia','finaliza','observaciones'],
            'datos': [
                {name: 'nombre', index: 'nombre', width: 200, align: 'center', editable: true, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }},
                {name: 'fk_equipo', index: 'fk_equipo',hidden: false, width:100, editable: true, edittype:'select', 
                        editoptions: {
                        dataInit: function(elemento) { $(elemento).width(292)},
                        value   : getElementos({'clase': 'Monitorias', 'oper': 'getListah',foranea: idSala}),
                        defaultValue: idSala
                    }},
                        
                {name: 'inicia', index: 'inicia', width: 100, editable: true, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }},
                        {name: 'finaliza', index: 'finaliza', width: 100, editable: true, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }},
                        {name: 'observaciones', index: 'observaciones', width: 100, editable: true, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }}
            ]};
        var hora = $('#hora').val(), fecha = $('#fecha').val();
        console.log(idSala)
        if (jqGridEquipos) { // si el grid ya fue creado, no se re-crea sino que se actualizan los parametros y se recarga 
            var titulo;
            if (!idSala || idSala == -1)
            {
                titulo = 'Control de turnos de monitoria';
            } else {
                titulo = 'Monitoria en:  ' + $('#listasalas :selected').text();
            }
            $("#tablaEquipos").jqGrid('setGridParam', {// hacer lo siguiente con con el grid de asignaturas: 
                postData: {
                    foranea: idSala, // 1) enviar al servidor el ID del grupo seleccionado
                    hora: hora,
                    fecha: fecha
                }}).setCaption(titulo).trigger("reloadGrid"); // 2) asignarle un título acorde con el grupo seleccionado y recargar el grid 
            return; // Esta instrucción garantiza que el grid se cree completo sólo una vez 
        }

        jqGridEquipos = jQuery("#tablaEquipos").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'Monitorias',
                oper: 'selectp',
                foranea: idSala,
                hora: hora,
                fecha: fecha
            },
            colNames: columnas.cabeceras,  //  observe que ahora se utilizan las cabeceras definidas en el objeto columnas.
            colModel: columnas.datos,           //  observe que ahora se utilizan los datos definidos en el objeto columnas.
            autowidth: false,                            //  OJO
            shrinkToFit: false,                          //  observe
            width: calcularAnchoGrid(columnas),   //  observe que ahora el ancho de los grid los calcula la función definida en librería.js

            rowNum: 100,
            rowList: [100, 200, 300],
            pager: '#pTablaEquipos',
            sortname: 'id',
            viewrecords: true,
            sortorder: "asc",
            caption: "Control de turnos de monitoria",
            multiselect: false,
            editurl: "controlador/fachada.php?clase=Monitorias",
            onSelectRow: function(id) {
                idEquipo = id
                datosEquipo = jQuery(jqGridEquipos).getRowData(idEquipo);   // Recuperar los datos de la fila seleccionada
                //idTurnos = ''
                console.log(datosEquipo)
                crearTablaTurnos()
            }
        }).jqGrid('navGrid', '#pTablaEquipos', {
            refresh: true,
            edit: true,
            add: true,
            del: true,
            search: true
        },
        {// Antes de enviar a Departamento->edit(...) se agrega un POST
            modal: true, jqModal: true,
            width: 370,
            afterSubmit: function(response, postdata) {
            return respuestaServidor(response);
            }
        },
                {// Antes de enviar a TemaCongreso->add(...) se agrega un POST
                    modal: true, jqModal: true,
                    width: 370,
                    afterSubmit: function(response, postdata) {
                        // Enseguida se muestran lo fundamental de las validaciones de errores ocurridos en el servidor
                        console.log(response);  // 
                        return respuestaServidor(response);
//                        var respuesta = jQuery.parseJSON(response.responseText)
//                        return respuesta.ok ? [true, "", ""] : [false, respuesta.mensaje, ""];
                    }
                },
        {modal: true, jqModal: true,
            width: 370,
             afterSubmit: function(response, postdata) {
            return respuestaServidor(response);
            }
        },
                {multipleSearch: true, multipleGroup: true}
        )
    }



    $("#sis").button().on("click", function() {
        var desde = $('#fechad').val(), hasta = $('#fechah').val();
        $.post("controlador/fachada.php", {// Comprobar comunicación C/S
            clase: 'Control',
            oper: 'getSelectj'

        }, function(data) {
            $('#consul').html(data);
            $('#monitores').on("change", function() {
                idMonitor = $(this).val(); // asignar a una variable el ID del grupo seleccionado y ...
                console.log(idMonitor);
                 console.log(desde);
                 console.log(hasta);
                crearTablaControl();
            }).val('0').change();
            $('#salas').on("change", function() {
                idConsa = $(this).val(); // asignar a una variable el ID del grupo seleccionado y ...
               
                 console.log(idConsa);
                  console.log(desde);
                  console.log(hasta);
                  crearTablaControl();
            }).val('0').change();
        }, "json");
    });


    function crearTablaControl() {
         var desde = $('#fechad').val(), hasta = $('#fechah').val();
    
       var columnas= { 'cabeceras': ['Monitores', 'Salas', 'Fecha', 'Hora inicio', 'Hora fin'],
            'datos': [
                {name: 'nombreu', index: 'nombreu', width: 300, align: 'center', editable: true, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }},
                {name: 'nombres', index: 'nombres', width: 250, editable: true, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }},
                {name: 'fecha', index: 'fecha', width: 100, editable: true, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }},
                {name: 'horai', index: 'horai', width: 100, editable: true, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }},
                {name: 'horaf', index: 'horaf', width: 100, editable: true, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }}

            ]
       };  
         
        if (jqGridControl) { // si el grid ya fue creado, no se re-crea sino que se actualizan los parametros y se recarga 
            var titulo;
            if ((!idMonitor || idMonitor == -1)&&(!idConsa || idConsa == -1))
            {
                titulo = 'Control de monitores';
            } else {
                titulo = 'Monitor  :' + $('#monitores :selected').text()+'--'+' sala :  ' + $('#salas :selected').text();
            }
            $("#tablaControl").jqGrid('setGridParam', {// hacer lo siguiente con con el grid de asignaturas: 
                postData: {
                     mon:idMonitor,
                sal:idConsa,
                desd:desde,
                hasta:hasta
                }}).setCaption(titulo).trigger("reloadGrid"); // 2) asignarle un título acorde con el grupo seleccionado y recargar el grid 
            return; // Esta instrucción garantiza que el grid se cree completo sólo una vez 
        }
        
       
        jqGridControl = jQuery("#tablaControl").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'Control',
                oper: 'selectp',
                mon:idMonitor,
                sal:idConsa,
                desd:desde,
                hasta:hasta
            },
           colNames: columnas.cabeceras,  //  observe que ahora se utilizan las cabeceras definidas en el objeto columnas.
            colModel: columnas.datos,           //  observe que ahora se utilizan los datos definidos en el objeto columnas.
            autowidth: false,                            //  OJO
            shrinkToFit: false,                          //  observe
            width: calcularAnchoGrid(columnas),   //  observe que ahora el ancho de los grid los calcula la función definida en librería.js  
            rowNum: 100,
           rowList: [100, 200, 300],
            pager: '#pTablaControl',
            sortname: 'id',
            viewrecords: true,
            sortorder: "asc",
            caption: "Control de Monitorias",
            multiselect: false,
            editurl: "controlador/fachada.php?clase=Departamento",
            onSelectRow: function(id) {
            }
        }).jqGrid('navGrid', '#pTablaControl', {
            refresh: true,
            edit: true,
            add: true,
            del: true,
            search: true,
            view: true,
            useColSpanStyle: true

        },
        {// Antes de enviar a Departamento->edit(...) se agrega un POST
            modal: true, jqModal: true,
            width: 370,
              afterSubmit: function(response, postdata) {
            return respuestaServidor(response);
            }
        },
                {// Antes de enviar a TemaCongreso->add(...) se agrega un POST
                    modal: true, jqModal: true,
                    width: 370,
                    afterSubmit: function(response, postdata) {
                        // Enseguida se muestran lo fundamental de las validaciones de errores ocurridos en el servidor
                        console.log(response); 
                        return respuestaServidor(response); 
//                        var respuesta = jQuery.parseJSON(response.responseText)
//                        return respuesta.ok ? [true, "", ""] : [false, respuesta.mensaje, ""];
                    }
                },
        {modal: true, jqModal: true,
            width: 370,
              afterSubmit: function(response, postdata) {
            return respuestaServidor(response);
            }
        },
                {multipleSearch: true, multipleGroup: true}
        )
    }
    ;

    jqGridControl.jqGrid('setGroupHeaders', {
        useColSpanStyle: true, groupHeaders: [
            {startColumnName: 'fecha', numberOfColumns: 3, titleText: '<em>Disponibilidad</em>'}

        ]

    });

});
