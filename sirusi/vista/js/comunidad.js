$(function() {
    var fichas = $("#comunidad-tabs").tabs();

    // toda la funcionalidad para estudiantes, administrativos y docentes

    var jqGridUsuarios, codigoUsuario, datosUsuario, idDependencia, idRol;

    crearTablaEstudiantes();
    crearTablaAdministrativos();
    crearTablaDocentes();
    crearTablaInvitados();

    function crearTablaEstudiantes() {
        jqGridUsuarios = jQuery("#comunidad-grid-estudiantes").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'Usuario',
                oper: 'selectEstudiantes'
            },
            colNames: ['CODIGO', 'NOMBRE', 'APELLIDO', 'TELEFONO', 'EMAIL', 'DEPENDENCIA'],
            colModel: [
                {name: 'codigo', index: 'codigo', width: 300, align: 'center', editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'nombre', index: 'nombre', width: 500, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'apellido', index: 'apellido', width: 500, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'telefono', index: 'telefono', width: 500, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'email', index: 'email', width: 900, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'dependencia', index: 'dependencia', width: 700, editable: true, edittype: 'select',
                    editoptions: {
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        },
                        dataUrl: 'controlador/fachada.php?clase=Usuario&oper=getSelectD',
                        defaultValue: idDependencia
                    }}
            ],
            rowNum: 100,
            width: 700,
            rowList: [100, 200, 300],
            pager: '#comunidad-pgrid-estudiantes',
            sortname: 'codigo',
            viewrecords: true,
            sortorder: "asc",
            caption: "Gestión de Estudiantes",
            multiselect: false,
            editurl: "controlador/fachada.php?clase=Usuario",
            onSelectRow: function(codigo) {
                codigoUsuario = codigo;
                datosUsuario = $(this).getRowData(codigoUsuario);   // Recuperar los datos de la fila seleccionada
            }
        }).jqGrid('navGrid', '#comunidad-pgrid-estudiantes', {
            refresh: true,
            edit: true,
            add: true,
            del: true,
            search: true
        },
        {// Antes de enviar a Departamento->edit(...) se agrega un POST
            modal: true, jqModal: true,
            width: 325,
            beforeSubmit: function(postdata) {
//              acceder a los datos de la fila seleccionada:
//              var fila = $(this).getRowData($(this).getGridParam("selrow"));
//              agregar un parámetro a los datos enviados (ej. el ID introducido en el formulario de edición)
                postdata.idNuevo = $('#usuario').val();
                postdata.paramEdit = '';
                return[true, ''];
            },
//            afterSubmit: function(response, postdata) {
//                var respuesta = jQuery.parseJSON(response.responseText);
//                return [respuesta.ok, respuesta.mensaje, ''];
//                jqGridUsuarios.jqGrid('setSelection', postdata.codigo);
//                return respuestaServidor(response);
//            }
        },
        {// Antes de enviar a Departamento->add(...) se agrega un POST
            modal: true, jqModal: true,
            width: 325,
            afterSubmit: function(response, postdata) {
                jqGridUsuarios.jqGrid('setSelection', postdata.codigo);
                return respuestaServidor(response);
            },
            afterShowForm: function() {
                $('#dependencia').val(idDependencia)
            },
            beforeSubmit: function(postdata) {
                postdata.paramAdd = 'estudiante';
                return[true, ''];
            }
        },
        {modal: true, jqModal: true,
            width: 300,
            afterSubmit: function(response, postdata) {
                var respuesta = jQuery.parseJSON(response.responseText);
                return [respuesta.ok, respuesta.mensaje, ''];
            }
        },
        {multipleSearch: true, multipleGroup: true}
        )
    }

    /*****************************************************************************************************************************************************/

    function crearTablaDocentes() {
        jqGridUsuarios = jQuery("#comunidad-grid-docentes").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'Usuario',
                oper: 'selectDocentes'
            },
            colNames: ['CODIGO', 'NOMBRE', 'APELLIDO', 'TELEFONO', 'EMAIL', 'DEPENDENCIA'],
            colModel: [
                {name: 'codigo', index: 'codigo', width: 300, align: 'center', editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'nombre', index: 'nombre', width: 500, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'apellido', index: 'apellido', width: 500, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'telefono', index: 'telefono', width: 500, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'email', index: 'email', width: 900, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'dependencia', index: 'dependencia', width: 700, editable: true, edittype: 'select',
                    editoptions: {
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        },
                        dataUrl: 'controlador/fachada.php?clase=Usuario&oper=getSelectD',
                        defaultValue: idDependencia
                    }}
            ],
            rowNum: 100,
            width: 700,
            rowList: [100, 200, 300],
            pager: '#comunidad-pgrid-docentes',
            sortname: 'codigo',
            viewrecords: true,
            sortorder: "asc",
            caption: "Gestión de Usuarios",
            multiselect: false,
            editurl: "controlador/fachada.php?clase=Usuario",
            onSelectRow: function(codigo) {
                codigoUsuario = codigo
                datosUsuario = $(this).getRowData(codigoUsuario);   // Recuperar los datos de la fila seleccionada
            }
        }).jqGrid('navGrid', '#comunidad-pgrid-docentes', {
            refresh: true,
            edit: true,
            add: true,
            del: true,
            search: true
        },
        {// Antes de enviar a Departamento->edit(...) se agrega un POST
            modal: true, jqModal: true,
            width: 325,
            beforeSubmit: function(postdata) {
//              acceder a los datos de la fila seleccionada:
//              var fila = $(this).getRowData($(this).getGridParam("selrow"));
//              agregar un parámetro a los datos enviados (ej. el ID introducido en el formulario de edición)
                postdata.idNuevo = $('#usuario').val();
                postdata.paramEdit = '';
                return[true, ''];
            },
            afterSubmit: function(response, postdata) {
                var respuesta = jQuery.parseJSON(response.responseText);
                return [respuesta.ok, respuesta.mensaje, ''];
            }
        },
        {// Antes de enviar a Departamento->add(...) se agrega un POST
            modal: true, jqModal: true,
            width: 325,
            afterShowForm: function() {
                $('#dependencia').val(idDependencia)
            },
            beforeSubmit: function(postdata) {
                postdata.paramAdd = 'docente';
                return[true, ''];
            }
        },
        {modal: true, jqModal: true,
            width: 300,
            afterSubmit: function(response, postdata) {
                var respuesta = jQuery.parseJSON(response.responseText);
                return [respuesta.ok, respuesta.mensaje, ''];
            }
        },
        {multipleSearch: true, multipleGroup: true}
        )
    }

    /*****************************************************************************************************************************************************/

    function crearTablaAdministrativos() {
        jqGridUsuarios = jQuery("#comunidad-grid-admitivos").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'Usuario',
                oper: 'selectAdministrativos'
            },
            colNames: ['CODIGO', 'NOMBRE', 'APELLIDO', 'TELEFONO', 'EMAIL', 'DEPENDENCIA'],
            colModel: [
                {name: 'codigo', index: 'codigo', width: 300, align: 'center', editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'nombre', index: 'nombre', width: 500, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'apellido', index: 'apellido', width: 500, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'telefono', index: 'telefono', width: 500, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'email', index: 'email', width: 900, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'dependencia', index: 'dependencia', width: 700, editable: true, edittype: 'select',
                    editoptions: {
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        },
                        dataUrl: 'controlador/fachada.php?clase=Usuario&oper=getSelectD',
                        defaultValue: idDependencia
                    }}
            ],
            rowNum: 100,
            width: 700,
            rowList: [100, 200, 300],
            pager: '#comunidad-pgrid-admitivos',
            sortname: 'codigo',
            viewrecords: true,
            sortorder: "asc",
            caption: "Gestión de Administrativos",
            multiselect: false,
            editurl: "controlador/fachada.php?clase=Usuario",
            onSelectRow: function(codigo) {
                codigoUsuario = codigo
                datosUsuario = $(this).getRowData(codigoUsuario);   // Recuperar los datos de la fila seleccionada
            }
        }).jqGrid('navGrid', '#comunidad-pgrid-admitivos', {
            refresh: true,
            edit: true,
            add: true,
            del: true,
            search: true
        },
        {// Antes de enviar a Departamento->edit(...) se agrega un POST
            modal: true, jqModal: true,
            width: 325,
            beforeSubmit: function(postdata) {
//              acceder a los datos de la fila seleccionada:
//              var fila = $(this).getRowData($(this).getGridParam("selrow"));
//              agregar un parámetro a los datos enviados (ej. el ID introducido en el formulario de edición)
                postdata.idNuevo = $('#usuario').val();
                postdata.paramEdit = '';
                return[true, ''];
            },
            afterSubmit: function(response, postdata) {
                var respuesta = jQuery.parseJSON(response.responseText);
                return [respuesta.ok, respuesta.mensaje, ''];
            }
        },
        {// Antes de enviar a Departamento->add(...) se agrega un POST
            modal: true, jqModal: true,
            width: 325,
            afterShowForm: function() {
                $('#dependencia').val(idDependencia)
            },
            beforeSubmit: function(postdata) {
                postdata.paramAdd = 'administrativo';
                return[true, ''];
            }
        },
        {modal: true, jqModal: true,
            width: 300,
            afterSubmit: function(response, postdata) {
                var respuesta = jQuery.parseJSON(response.responseText);
                return [respuesta.ok, respuesta.mensaje, ''];
            }
        },
        {multipleSearch: true, multipleGroup: true}
        )
    }

    /*****************************************************************************************************************************************************/

    function crearTablaInvitados() {
        jqGridUsuarios = jQuery("#comunidad-grid-invitados").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'Usuario',
                oper: 'selectInvitados'
            },
            colNames: ['CODIGO', 'NOMBRE', 'APELLIDO', 'TELEFONO', 'EMAIL', 'ROL', 'DEPENDENCIA'],
            colModel: [
                {name: 'codigo', index: 'codigo', width: 300, align: 'center', editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'nombre', index: 'nombre', width: 500, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'apellido', index: 'apellido', width: 500, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'telefono', index: 'telefono', width: 500, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'email', index: 'email', width: 900, editable: true, editoptions: {size: 30,
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        }
                    }},
                {name: 'rol', index: 'rol', width: 500, editable: true, edittype: 'select',
                    editoptions: {
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        },
                        dataUrl: 'controlador/fachada.php?clase=Usuario&oper=getSelectRI',
                        defaultValue: idRol
                    }},
                {name: 'dependencia', index: 'dependencia', width: 700, editable: true, edittype: 'select',
                    editoptions: {
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        },
                        dataUrl: 'controlador/fachada.php?clase=Usuario&oper=getSelectD',
                        defaultValue: idDependencia
                    }}
            ],
            rowNum: 100,
            width: 800,
            rowList: [100, 200, 300],
            pager: '#comunidad-pgrid-invitados',
            sortname: 'codigo',
            viewrecords: true,
            sortorder: "asc",
            caption: "Gestión de Invitados",
            multiselect: false,
            editurl: "controlador/fachada.php?clase=Usuario",
            onSelectRow: function(codigo) {
                codigoUsuario = codigo
                datosUsuario = $(this).getRowData(codigoUsuario);   // Recuperar los datos de la fila seleccionada
            }
        }).jqGrid('navGrid', '#comunidad-pgrid-invitados', {
            refresh: true,
            edit: true,
            add: true,
            del: true,
            search: true
        },
        {// Antes de enviar a Departamento->edit(...) se agrega un POST
            modal: true, jqModal: true,
            width: 325,
            beforeSubmit: function(postdata) {
//              acceder a los datos de la fila seleccionada:
//              var fila = $(this).getRowData($(this).getGridParam("selrow"));
//              agregar un parámetro a los datos enviados (ej. el ID introducido en el formulario de edición)
                postdata.idNuevo = $('#usuario').val();
                postdata.paramEdit = 'invitado';
                return[true, ''];
            },
            afterSubmit: function(response, postdata) {
                var respuesta = jQuery.parseJSON(response.responseText);
                return [respuesta.ok, respuesta.mensaje, ''];
            }
        },
        {// Antes de enviar a Departamento->add(...) se agrega un POST
            modal: true, jqModal: true,
            width: 325,
            afterShowForm: function() {
                $('#dependencia').val(idDependencia);
                $('#rol').val(idRol);
            },
            beforeSubmit: function(postdata) {
                postdata.paramAdd = 'invitado';
                return[true, ''];
            }
        },
        {modal: true, jqModal: true,
            width: 300,
            afterSubmit: function(response, postdata) {
                var respuesta = jQuery.parseJSON(response.responseText);
                return [respuesta.ok, respuesta.mensaje, ''];
            }
        },
        {multipleSearch: true, multipleGroup: true}
        )
    }

});