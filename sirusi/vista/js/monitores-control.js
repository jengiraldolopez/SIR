/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(function() {
    var jqGridMonitores;
    var idSala=0;
    crearTablaMonitores();

    function crearTablaMonitores() {
        var columnas = {'cabeceras': ['fk_usuario_monitor', 'dia', 'hora_inicio', 'hora_fin'],
           'datos': [
                {name: 'fk_usuario_monitor', index: 'fk_usuario_monitor', width: 250, editable: false, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }},
                {name: 'dia', index: 'dia', width: 150, editable: true,edittype:'select', editoptions: {value:{1:'lunes',2:'martes',3:'miercoles',4:'jueves',5:'viernes',6:'sabado',7:'domingo'},
                        dataInit: function(elemento) {$(elemento).width(200)
                             }
                        
                        
                    }},
                {name: 'hora_inicio', index: 'hora_inicio', width: 100, editable: true, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }},
                {name: 'hora_fin', index: 'hora_fin', width: 100, editable: true, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }}


            ]};
        jqGridMonitores = jQuery("#tablaMonitores").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'Programacion',
                oper: 'select'
            },
//           
            colNames: columnas.cabeceras, //  observe que ahora se utilizan las cabeceras definidas en el objeto columnas.
            colModel: columnas.datos, //  observe que ahora se utilizan los datos definidos en el objeto columnas.
            autowidth: false, //  OJO
            shrinkToFit: false, //  observe
            width: calcularAnchoGrid(columnas), //  observe que ahora el ancho de los grid los calcula la función definida en librería.js

            rowNum: 100,
            rowList: [100, 200, 300],
            pager: '#pTablaMonitores',
            sortname: 'id',
            viewrecords: true,
            sortorder: "asc",
            caption: "Control de Monitores",
            multiselect: false,
            editurl: "controlador/fachada.php?clase=Programacion",
            onSelectRow: function(id) {
            }
        }).jqGrid('navGrid', '#pTablaMonitores', {
            refresh: true,
            edit: true,
            add: true,
            del: true,
            search: true,
            view: true,
//            useColSpanStyle: true

        },
                {// Antes de enviar a Departamento->edit(...) se agrega un POST
                    modal: true, jqModal: true,
                    width: 370,
                    afterSubmit: function(response, postdata) {/////////////////////////////////////////////////
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
            afterSubmit: function(response, postdata) {/////////////////////////////////////////////////
                return trespuestaServidor(response);
            }
        },
        {multipleSearch: true, multipleGroup: true}
        )
    };
    //

//    {   // Antes de enviar a Departamento->edit(...) se agrega un POST
//            modal:true, jqModal:true,
//            width:420,
//             afterSubmit: function(response, postdata) {/////////////////////////////////////////////////
//                jqGridSala.jqGrid('setSelection', postdata.fk_sala);  // seleccionar el bloque de esta sala (pudo haber cambiado)
//                return respuestaServidor(response); ///////////////////////////////////////////// OJO
//            }
//        },
//        {   // Antes de enviar a TemaCongreso->add(...) se agrega un POST
//            modal:true, jqModal:true,
//            width:420,
//             afterSubmit: function(response, postdata) {/////////////////////////////////////////////////
//                jqGridSala.jqGrid('setSelection', postdata.fk_sala);  // seleccionar el bloque de esta sala (pudo haber cambiado)
//                return respuestaServidor(response); ///////////////////////////////////////////// OJO
//            },
//            afterShowForm: function() {
//                        $('#fk_software').val(idSoftware);
//                    }
//        },

    //
    jqGridMonitores.jqGrid('setGroupHeaders', {
        useColSpanStyle: true, groupHeaders: [
            {startColumnName: 'dia', numberOfColumns: 3, titleText: '<em>Disponibilidad</em>'}

        ]

    });

});


