/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(function() {
    var jqGridMonitores;
    crearTablaMonitores();

    function crearTablaMonitores() {
        jqGridMonitores = jQuery("#tablaMonitores").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'Programacion',
                oper: 'select'
            },
            colNames: ['Monitor', 'Día', 'Hora inicio', 'Hora fin'],
            colModel: [
                {name: 'fk_usuario_monitor', index: 'fk_usuario_monitor', width: 600, editable: false, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }},
                        
//                           {name: 'fk_equipo', index: 'fk_equipo',hidden: false, width:150, editable: true, edittype:'select', 
//                        editoptions: {
//                        dataInit: function(elemento) { $(elemento).width(292)},
//                        value   : getElementos({'clase': 'Monitorias', 'oper': 'getListah',foranea: idSala}),
//                        defaultValue: idSala
//                    }},
                {name: 'dia', index: 'dia', width: 200, editable: true,edittype:'select', editoptions: {value:{1:'lunes',2:'martes',3:'miercoles',4:'jueves',5:'viernes',6:'sabado',7:'domingo'},
                        dataInit: function(elemento) {$(elemento).width(200)
                             }
                        
                        
                    }},
                {name: 'hora_inicio', index: 'hora_inicio', width: 200, editable: true, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }},
                {name: 'hora_fin', index: 'hora_fin', width: 200, editable: true, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }}


            ],
            rowNum: 100,
            width: 1000,
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
    }
    ;
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


