$(function() {


    var fichas = $("#salas-detalle-tabs1").tabs();

   
    var jqGridSede, nombreSede, datosSede,
            jqGridBloque, nombreBloque, datosBloque,
            jqGridSala, nombreSala, datosSala,
            jqGridEquipoSala, idEquipoSala, datosEquipoSala,
            jqGridSoftwareSala, idSoftwareSala, datosSoftwareSala,
            jqGridSoftware, idSoftware, datosSoftware;

    crearTablaSede();
    crearTablaBloque();
    crearTablaSala();
    crearTablaEquipoSala();
    crearTablaSoftwareSala();
    crearTablaSoftware();

    /* Fin de lo que se ejecutará cuando el formulario Localidades.html cargue */

    /* Implementación necesaria */

    /**
     * Muestra una tabla con la información de las sedes 
     * Argumentos:
     * Agregar, editar, eliminar, buscar: true o false, dependiendo de las opciones que se quieran habilitar
     */
    function crearTablaSede() {
        jqGridSede = jQuery("#salas-detalle-tabla-sede").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'Sede',
                oper: 'select'
            },
            colNames: ['Nombre de la sede', 'Dirección de la sede'],
            colModel: [
                {name: 'nombre', index: 'nombre', width: 300, editable: true, editrules: {required: true}, editoptions: {size: 37, maxlength: 40,
                        dataInit: function(elemento) {
                            $(elemento).width(282);
                        }
                    }},
                {name: 'direccion', index: 'direccion', width: 300, editable: true, editrules: {required: true}, editoptions: {size: 37, maxlength: 40,
                        dataInit: function(elemento) {
                            $(elemento).width(282);
                        }
                    }}
            ],
            rowNum: 100,
            width: 700,
            rowList: [100, 200, 300],
            pager: '#salas-detalle-ptabla-sede',
            sortname: 'nombre',
            viewrecords: true,
            sortorder: "asc",
            caption: "Sedes",
            multiselect: false,
            editurl: "controlador/fachada.php?clase=Sede",
            onSelectRow: function(id) {
                nombreSede = id;
                datosSede = jQuery(jqGridSede).getRowData(nombreSede);   // Recuperar los datos de la fila seleccionada
                nombreBloque = ' ';
                crearTablaBloque();

            }
        }).jqGrid('navGrid', '#salas-detalle-ptabla-sede', {
            refresh: true,
            edit: true,
            add: true,
            del: true,
            search: true
        },
        {
            modal: true, jqModal: true,
            width: 420
        },
        {
            modal: true, jqModal: true,
            width: 420,
            afterSubmit: function(response, postdata) {
                console.log(response);  // 
                var respuesta = jQuery.parseJSON(response.responseText);
                return respuesta.ok ? [true, "", ""] : [false, respuesta.mensaje, ""];
            }
        },
        {modal: true, jqModal: true,
            width: 300
        },
        {multipleSearch: true, multipleGroup: true}
        );
    }


    /* Implementación necesaria */

    /**
     * Muestra una tabla con la información de los bloques 
     * Argumentos:
     * Agregar, editar, eliminar, buscar: true o false, dependiendo de las opciones que se quieran habilitar
     */

    function crearTablaBloque() {

        if (jqGridBloque) {
            jqGridBloque.jqGrid('setGridParam', {postData: {id: nombreSede}});
            if (!nombreSede) {
                jqGridBloque.jqGrid('setCaption', "Bloques de la sede").trigger("reloadGrid");
            } else {
                jqGridBloque.jqGrid('setCaption', "Bloques de la sede " + datosSede['nombre'].capitalize()).trigger("reloadGrid");
            }
            return
        }



        jqGridBloque = jQuery("#salas-detalle-tabla-bloque").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'Bloque',
                oper: 'select'
            },
            colNames: ['Nombre del bloque', 'Nombre de la sede'],
            colModel: [
                {name: 'nombre', index: 'nombre', width: 500, editable: true, editrules: {required: true}, editoptions: {size: 37, maxlength: 40,
                        dataInit: function(elemento) {
                            $(elemento).width(282)
                        }
                    }},
                {name: 'fk_sede', index: 'fk_sede', hidden: false, width: 500, editable: true, edittype: 'select',
                    editoptions: {
                        dataInit: function(elemento) {
                            $(elemento).width(292)
                        },                       
                        dataUrl: 'controlador/fachada.php?clase=Sede&oper=getSelect',
                        defaultValue: nombreSede
                    }
                }
            ],
            rowNum: 100,
            width: 700,
            rowList: [100, 200, 300],
            pager: '#salas-detalle-ptabla-bloque',
            sortname: 'nombre',
            viewrecords: true,
            sortorder: "asc",
            caption: "Bloques de la sala",
            multiselect: false,
            editurl: "controlador/fachada.php?clase=Bloque",
            onSelectRow: function(id) {
                nombreBloque = id;
                datosBloque = jQuery(jqGridBloque).getRowData(nombreBloque);   // Recuperar los datos de la fila seleccionada
                nombreSala = ' ';
                crearTablaSala();

            }
        }).jqGrid('navGrid', '#salas-detalle-ptabla-bloque', {
            refresh: true,
            edit: true,
            add: true,
            del: true,
            search: true
        },
        {// Antes de enviar a Bloque->edit(...) se agrega un POST
            modal: true, jqModal: true,
            width: 420,
            afterSubmit: function(response, postdata) {
                jqGridSede.jqGrid('setSelection', postdata.fk_sede);  // seleccionar la sede de este bloque (pudo haber cambiado)
                return respuestaServidor(response); 
            }
        },
        {// Antes de enviar a Bloque->add(...) se agrega un POST
            modal: true, jqModal: true,
            width: 420,
            afterSubmit: function(response, postdata) {
                jqGridSede.jqGrid('setSelection', postdata.fk_sede);  // seleccionar la sede del nuevo bloque para reflejar la adición
                return respuestaServidor(response);  
            },
            afterShowForm: function() {
                $('#fk_sede').val(nombreSede);
            },
        },
                {modal: true, jqModal: true,
                    width: 300
                },
        {multipleSearch: true, multipleGroup: true}
        );

    }

/**
     * Muestra una tabla con la información de las salas 
     * Argumentos:
     * Agregar, editar, eliminar, buscar: true o false, dependiendo de las opciones que se quieran habilitar
     */

    crearTablaSala();

    function crearTablaSala() {


        if (jqGridSala) {
            jqGridSala.jqGrid('setGridParam', {postData: {id: nombreBloque}})
            if (!nombreBloque) {
                jqGridSala.jqGrid('setCaption', "Salas del bloque ").trigger("reloadGrid")
            } else {
                jqGridSala.jqGrid('setCaption', "Salas del bloque " + datosBloque['nombre'].capitalize()).trigger("reloadGrid")
            }
            return
        }

        jqGridSala = jQuery("#salas-detalle-tabla-sala").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'Sala',
                oper: 'select'
            },
            colNames: ['Nombre de la sala', 'Capacidad de la sala', 'Nombre del bloque'],
            colModel: [
                {name: 'nombre', index: 'nombre', width: 400, editable: true, editrules: {required: true}, editoptions: {size: 37, maxlength: 40,
                        dataInit: function(elemento) {
                            $(elemento).width(282);
                        }
                    }},
                {name: 'capacidad', index: 'capacidad', width: 400, editable: true, editrules: {required: true}, editoptions: {size: 37, maxlength: 40,
                        dataInit: function(elemento) {
                            $(elemento).width(282);
                        }
                    }},
                {name: 'fk_bloque', index: 'fk_bloque', hidden: false, width: 400, editable: true, edittype: 'select',
                    editoptions: {
                        dataInit: function(elemento) {
                            $(elemento).width(292);
                        },
                        dataUrl: 'controlador/fachada.php?clase=Bloque&oper=getSelect',
                        defaultValue: nombreBloque
                    }
                }

            ],
            rowNum: 100,
            width: 700,
            rowList: [100, 200, 300],
            pager: '#salas-detalle-ptabla-sala',
            sortname: 'nombre',
            viewrecords: true,
            sortorder: "asc",
            caption: "Sala",
            multiselect: false,
            editurl: "controlador/fachada.php?clase=Sala",
            onSelectRow: function(id) {
                nombreSala = id;
                datosSala = jQuery(jqGridSala).getRowData(nombreSala);   // Recuperar los datos de la fila seleccionada
                idEquipoSala = '';
                idSoftwareSala = '';
                crearTablaEquipoSala();
                crearTablaSoftwareSala();

            }
        }).jqGrid('navGrid', '#salas-detalle-ptabla-sala', {
            refresh: true,
            edit: true,
            add: true,
            del: true,
            search: true
        },
        {// Antes de enviar a Sala->edit(...) se agrega un POST
            modal: true, jqModal: true,
            width: 420,
            afterSubmit: function(response, postdata) {
                jqGridBloque.jqGrid('setSelection', postdata.fk_bloque);  // seleccionar la sala de este bloque (pudo haber cambiado)
                return respuestaServidor(response); 
            }
        },
        {// Antes de enviar a Sala->add(...) se agrega un POST
            modal: true, jqModal: true,
            width: 420,
            afterSubmit: function(response, postdata) {
                jqGridBloque.jqGrid('setSelection', postdata.fk_bloque);  // seleccionar la sala del nuevo bloque para reflejar la adición
                return respuestaServidor(response);   OJO
            },
            afterShowForm: function() {
                $('#fk_bloque').val(nombreBloque);
            }
        },
        {modal: true, jqModal: true,
            width: 300
        },
        {multipleSearch: true, multipleGroup: true}
        );
    }

/**
     * Muestra una tabla con la información de los Equipos que pertenecen a la sala  
     * Argumentos:
     * Agregar, editar, eliminar, buscar: true o false, dependiendo de las opciones que se quieran habilitar
     */
    
    crearTablaEquipoSala();

    function crearTablaEquipoSala() {


        if (jqGridEquipoSala) {
            jqGridEquipoSala.jqGrid('setGridParam', {postData: {id: nombreSala}})
            if (!nombreSala) {
                jqGridEquipoSala.jqGrid('setCaption', "Equipo de la sala").trigger("reloadGrid")
            } else {
                jqGridEquipoSala.jqGrid('setCaption', "Equipo de la sala  " + datosSala['nombre'].capitalize()).trigger("reloadGrid")
            }
            return
        }


        jqGridEquipoSala = jQuery("#salas-detalle-tabla-equipo-sala").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'EquipoSala',
                oper: 'select'
            },
            colNames: ['Código inventario', 'Observaciones', 'Estado', 'Nombre de la sala'],
            colModel: [
                {name: 'codigo_inventario', index: 'codigo_inventario', width: 440, editable: true, editrules: {required: true}, editoptions: {size: 37, maxlength: 40,
                        dataInit: function(elemento) {
                            $(elemento).width(282);
                        }
                    }},
                {name: 'observaciones', index: 'observaciones', width: 440, editable: true, editrules: {required: true}, editoptions: {size: 37, maxlength: 40,
                        dataInit: function(elemento) {
                            $(elemento).width(282);
                        }
                    }},
                {name: 'estado', index: 'estado', width: 440, editable: true, edittype: 'select',
                    editoptions: { dataInit: function(elemento) {
                            $(elemento).width(292);
                        },
                        value: {0: 'Bueno', 1: 'Malo', 2: 'Prestado', 3: 'Disponible'}
                    }},
                {name: 'fk_sala', index: 'fk_sala', hidden: false, width: 440, editable: true, edittype: 'select',
                    editoptions: {
                        dataInit: function(elemento) {
                            $(elemento).width(292);
                        },
                        dataUrl: 'controlador/fachada.php?clase=Sala&oper=getSelect',
                        defaultValue: nombreSala

                    }
                }

            ],
            rowNum: 100,
            width: 700,
            rowList: [100, 200, 300],
            pager: '#salas-detalle-ptabla-equipo-sala',
            sortname: 'id',
            viewrecords: true,
            sortorder: "asc",
            caption: "Equipos de la sala",
            multiselect: false,
            editurl: "controlador/fachada.php?clase=EquipoSala",
            onSelectRow: function(id) {
                idEquipoSala = id;
                datosEquipoSala = jQuery(jqGridEquipoSala).getRowData(idEquipoSala);   // Recuperar los datos de la fila seleccionada
                idSoftwareSala = '';
                crearTablaSoftwareSala();

            }
        }).jqGrid('navGrid', '#salas-detalle-ptabla-equipo-sala', {
            refresh: true,
            edit: true,
            add: true,
            del: true,
            search: true
        },
        {// Antes de enviar a EquipoSala->edit(...) se agrega un POST
            modal: true, jqModal: true,
            width: 420,
            afterSubmit: function(response, postdata) {
                jqGridSala.jqGrid('setSelection', postdata.fk_sala);  // seleccionar el equipo de esta sala (pudo haber cambiado)
                return respuestaServidor(response); 
            }
        },
        {// Antes de enviar a EquipoSala->add(...) se agrega un POST
            modal: true, jqModal: true,
            width: 420,
            afterSubmit: function(response, postdata) {
                jqGridSala.jqGrid('setSelection', postdata.fk_sala);  // seleccionar el equipo de la nueva sala (pudo haber cambiado)
                return respuestaServidor(response); 
            },
            afterShowForm: function() {
                $('#fk_sala').val(nombreSala);
            }
        },
        {modal: true, jqModal: true,
            width: 300
        },
        {multipleSearch: true, multipleGroup: true}
        );
    }

    

    /**
     * Muestra una tabla con la información del sofware de la sala
     * Argumentos:
     * Agregar, editar, eliminar, buscar: true o false, dependiendo de las opciones que se quieran habilitar
     */


    crearTablaSoftwareSala();

    function crearTablaSoftwareSala() { 

        if (jqGridSoftwareSala) {
            jqGridSoftwareSala.jqGrid('setGridParam', {postData: {id: nombreSala}})
            if (!nombreSala) {
                jqGridSoftwareSala.jqGrid('setCaption', "Software de la sala").trigger("reloadGrid")
            } else {
                jqGridSoftwareSala.jqGrid('setCaption', "Software de la sala  " + datosSala['nombre'].capitalize()).trigger("reloadGrid")
            }
            return
        }

        if (jqGridSoftwareSala) {
            jqGridSoftwareSala.jqGrid('setGridParam', {postData: {id: idSoftware}})
            if (!idSoftware) {
                jqGridSoftwareSala.jqGrid('setCaption', "Software Sala").trigger("reloadGrid")
            } else {
                jqGridSoftwareSala.jqGrid('setCaption', "Software Sala de " + datosSoftware['nombre'].capitalize()).trigger("reloadGrid")
            }
            return
        }

        jqGridSoftwareSala = jQuery("#salas-detalle-tabla-software-sala").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'SoftwareSala',
                oper: 'select'
            },
            colNames: ['Id del software', 'Nombre de la sala'],
            colModel: [
                {name: 'fk_software', index: 'fk_software', hidden: false, width: 400, editable: true, edittype: 'select',
                    editoptions: {
                        dataInit: function(elemento) {
                            $(elemento).width(292);
                        },
                        dataUrl: 'controlador/fachada.php?clase=Software&oper=getSelect',
                        defaultValue: idSoftware
                    }
                },
                {name: 'fk_sala', index: 'fk_sala', hidden: false, width: 400, editable: true, edittype: 'select',
                    editoptions: {
                        dataInit: function(elemento) {
                            $(elemento).width(292);
                        },

                        dataUrl: 'controlador/fachada.php?clase=Sala&oper=getSelect',
                        defaultValue: nombreSala
                    }
                }

            ],
            rowNum: 100,
            width: 700,
            rowList: [100, 200, 300],
            pager: '#salas-detalle-ptabla-software-sala',
            sortname: 'id',
            viewrecords: true,
            sortorder: "asc",
            caption: "Software de la sala",
            multiselect: false,
            editurl: "controlador/fachada.php?clase=SoftwareSala",
            onSelectRow: function(id) {
                idSoftwareSala = id;
                datosSoftwareSala = jQuery(jqGridSoftwareSala).getRowData(idSoftwareSala);   // Recuperar los datos de la fila seleccionada


            }
        }).jqGrid('navGrid', '#salas-detalle-ptabla-software-sala', {
            refresh: true,
            edit: true,
            add: true,
            del: true,
            search: true
        },
        {// Antes de enviar a SoftwareSala->edit(...) se agrega un POST
            modal: true, jqModal: true,
            width: 420,
            afterSubmit: function(response, postdata) {
                jqGridSala.jqGrid('setSelection', postdata.fk_sala);  // seleccionar el software de esta sala (pudo haber cambiado)
                return respuestaServidor(response); 
            }
        },
        {// Antes de enviar a SoftwareSala->add(...) se agrega un POST
            modal: true, jqModal: true,
            width: 420,
            afterSubmit: function(response, postdata) {
                jqGridSala.jqGrid('setSelection', postdata.fk_sala);  // seleccionar el software de la nueva sala para reflejar la adición
                return respuestaServidor(response); 
            },
            afterShowForm: function() {
                $('#fk_software').val(idSoftware);
            }
        },
        {// Antes de enviar a SoftwareSala->add(...) se agrega un POST
            modal: true, jqModal: true,
            width: 420,
            afterShowForm: function() {
                $('#fk_sala').val(nombreSala);
            }
        },
        {modal: true, jqModal: true,
            width: 300
        },
        {multipleSearch: true, multipleGroup: true}
        );
    }

    /**
     * Muestra una tabla con la información de la software 
     * Argumentos:
     * Agregar, editar, eliminar, buscar: true o false, dependiendo de las opciones que se quieran habilitar
     */


    function crearTablaSoftware() {


        jqGridSoftware = jQuery("#salas-detalle-tabla-software").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'Software',
                oper: 'select'
            },
            colNames: ['Nombre del software', 'Version del software'],
            colModel: [
                {name: 'nombre', index: 'nombre', width: 400, editable: true, editrules: {required: true}, editoptions: {size: 37,
                        dataInit: function(elemento) {
                            $(elemento).width(282);
                        }
                    }},
                {name: 'version', index: 'version', width: 400, editable: true, editrules: {required: true}, editoptions: {size: 37, maxlength: 40,
                        dataInit: function(elemento) {
                            $(elemento).width(282);
                        }
                    }}

            ],
            rowNum: 100,
            width: 700,
            rowList: [100, 200, 300],
            pager: '#salas-detalle-ptabla-software',
            sortname: 'id',
            viewrecords: true,
            sortorder: "asc",
            caption: "Software",
            multiselect: false,
            editurl: "controlador/fachada.php?clase=Software",
            onSelectRow: function(id) {
                idSoftware = id;
                datosSoftware = jQuery(jqGridSoftware).getRowData(idSoftware);   // Recuperar los datos de la fila seleccionada
                idSoftwareSala = '';
                crearTablaSoftwareSala();

            }
        }).jqGrid('navGrid', '#salas-detalle-ptabla-software', {
            refresh: true,
            edit: true,
            add: true,
            del: true,
            search: true
        },
        {// Antes de enviar a Departamento->edit(...) se agrega un POST
            modal: true, jqModal: true,
            width: 420
        },
        {// Antes de enviar a TemaCongreso->add(...) se agrega un POST
            modal: true, jqModal: true,
            width: 420,
            afterSubmit: function(response, postdata) {
                // Enseguida se muestran lo fundamental de las validaciones de errores ocurridos en el servidor
                console.log(response);  // 
                var respuesta = jQuery.parseJSON(response.responseText);
                return respuesta.ok ? [true, "", ""] : [false, respuesta.mensaje, ""];
            }
        },
        {modal: true, jqModal: true,
            width: 300
        },
        {multipleSearch: true, multipleGroup: true}
        );
    }

});





