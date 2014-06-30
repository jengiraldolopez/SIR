/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function() {
    var jqGridUsuarios, codigoUsuario, datosUsuario, idDependencia, idRol;
    crearTablaPrivilegios();

    function crearTablaPrivilegios() {
        jqGridUsuarios = jQuery("#privilegios-grid-passwords").jqGrid({
            url: 'controlador/fachada.php',
            datatype: "json",
            mtype: 'POST',
            postData: {
                clase: 'Usuario',
                oper: 'selectAdminpass'
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
                {name: 'rol', index: 'rol', width: 700, editable: true, edittype: 'select',
                    editoptions: {
                        dataInit: function(elemento) {
                            $(elemento).width(200)
                        },
                        dataUrl: 'controlador/fachada.php?clase=Usuario&oper=getSelectRA',
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
            pager: '#privilegios-pgrid-passwords',
            sortname: 'codigo',
            viewrecords: true,
            sortorder: "asc",
            caption: "Gestión de Passwords y Administradores",
            multiselect: false,
            editurl: "controlador/fachada.php?clase=Usuario",
            onSelectRow: function(codigo) {
                codigoUsuario = codigo
                datosUsuario = $(this).getRowData(codigoUsuario);   // Recuperar los datos de la fila seleccionada
            }
        }).jqGrid('navGrid', '#privilegios-pgrid-passwords', {
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
                postdata.paramAdd = 'privilegio';
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
        .navButtonAdd('#privilegios-pgrid-passwords', {
            buttonicon: "ui-icon-alert",
            cursor: "Restablecer contraseña",
            caption: "",
            position: "first",
            onClickButton: function() {
                $.post('controlador/fachada.php?clase=Usuario&oper=modifyPass', {
                    codigo: codigoUsuario
                });
                alert('Contraseña restablecida para ' + codigoUsuario);
            }
        });
    }
});