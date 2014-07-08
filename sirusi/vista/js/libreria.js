
/*
 * **************** QUITAR LUEGO DE PRUEBAS USUARIO POR DEFECTO EN function autenticar()   ***********************************
 */

var anchoContenedor; // El ancho disponible en el contenedor
var fechaInicio, fechaFin;
var usuario = {};

$(document).on('ready', function() {

//    anchoContenedor = $('#columnaContenido').outerWidth() * 0.94;

    $('#index-frmautentica').dialog({
        autoOpen: true,
        width: 355,
        height: 200,
        modal: true,
        open: function() {
            $("#index-frmautentica label").css("width", "70px");
            $("#index-frmautentica input").css("width", "180");
            $(".ui-dialog-titlebar-close").hide();
            $("#autentica-aceptar").button({icons: {primary: "ui-icon-check"}});
            $("#autentica-cancelar").button({icons: {primary: "ui-icon-close"}});
        },
        buttons: [
            {id: "autentica-aceptar", text: "Aceptar", click: function() {
                    var privilegios = autenticar();
                    if (typeof (privilegios) !== "undefined") {
                        usuario.id = privilegios.usuarioID;
                        usuario.nombre = privilegios.usuarioNombre;
                        
                        if (privilegios.opciones.hasOwnProperty('Cerrar sesión')) {  // si existe 'Cerrar sesión'...
                            mostrarMenu(privilegios.opciones);
                        } else if (privilegios.opciones.hasOwnProperty('Restablecer')) {
                            alert('Debe restablecer su contraseña');
                            cambiarClave();
                        } else {
                            //Permite reintentar
                            alert($('#index-nombre-usuario').val() + '. Tu usuario o contraseña no son correctos.');
                            window.location.href = "index.html";
                        }
                    }
                    $(this).dialog("close");
                }},
            {id: "autentica-cancelar", text: "Cancelar", click: function() {
                    $(this).dialog("close");
                    window.location.href = "index.html";
                }}
        ]
    });
    $(".ui-dialog, .ui-dialog-titlebar, .ui-dialog-buttonpane").css({"font-size": "95%"});

//    $(window).on('resize', function() {
//        anchoContenedor = $('#columnaContenido').outerWidth() * 0.94;
//        if (grid = $('.ui-jqgrid-btable:visible')) {
//            grid.each(function(index) {
//                var gridId = $(this).attr('id');
//                gridParentWidth = $('#gbox_' + gridId).parent().width() * 0.99;
//                jQuery('#' + gridId).setGridWidth(gridParentWidth);
//            });
//        }
//    });

anchoContenedor = $('#contentcolumn').outerWidth() * 0.90;
      $(window).on('resize', function() {
          anchoContenedor = $('#contentcolumn').outerWidth() * 0.90;
          if (grid = $('.ui-jqgrid-btable')) {
              grid.each(function(index) {
                 var gridId = $(this).attr('id');
                 gridParentWidth = $('#gbox_' + gridId).parent().width() * 0.99;
                jQuery('#' + gridId).setGridWidth(gridParentWidth);             });
          }
     });


    /**
     * Crear el menú de la aplicación
     * @param {array} opciones las opciones que se reciben al autenticarse
     * @returns {undefined}
     */

    function timeout() {
        $("#dialog").dialog({// Manejo de fin de sesiÃƒÂ³n por tiempo de inactividad. Dependencias: index_Admin.js, Index.html -->
            autoOpen: false,
            modal: true,
            width: 400,
            height: 200,
            closeOnEscape: false,
            draggable: false,
            resizable: false,
            open: function() {
                $("#btnSeguirTrabajando").button({icons: {primary: "ui-icon-check"}});
                $("#btnCerrarSesion").button({icons: {primary: "ui-icon-close"}});
            },
            buttons: [
                {id: "btnSeguirTrabajando", text: "Seguir trabajando", click: function() {
                        $(this).data('idletimeout').resume.trigger('click'); // Se tuvo que agregar esto para forzar el evento
                        $(this).dialog('close');
                    }
                },
                {id: "btnCerrarSesion", text: "Cerrar sesión", click: function() {
                        $.idleTimeout.options.onTimeout.call(this);
                    }
                }
            ]
        });
        $.idleTimeout('#dialog', 'div.ui-dialog-buttonpane button:first', {
            idleAfter: 300,
            warningLength: 10, // Segundos que se deja visible el mensaje de cierre
            pollingInterval: 60, // Se envÃƒÂ­a una solicitud a la pÃƒÂ¡gina keepAliveURL cada minuto
            keepAliveURL: "controlador/fachada.php?clase=Usuario&oper=verificarEstado", // La pÃƒÂ¡gina a donde se redirecciona por defecto. CambiÃƒÂ© para que funcione por POST
            serverResponseEquals: 'OK', // La respuesta que se espera de la pÃƒÂ¡gina keepAliveURL
            data: {'mensaje': 'activo'}, // argumentos por POST enviados a keepAliveURL (agregÃƒÂ³ cacu)
            onTimeout: function() {                                 // A dÃƒÂ³nde se rederige el usuario en caso de tiempo agotado
                $.post("controlador/fachada.php", {clase: 'Usuario', oper: 'cerrarSesion'}, function() {
                    window.location.href = "index.html";  //TambiÃƒÂ©n funciona: window.open('http://localhost/gea/index.html', '_self'); ver document.location.hostname
                })
            },
            onIdle: function() {                                    // Mostrar el cuadro de diÃƒÂ¡logo cuando se alcance el lÃƒÂ­mite de inactividad
                $(this).dialog("open");
            },
            onCountdown: function(counter) {
                console.log(counter);
                $("#dialog-countdown").html(counter);               // Actualiza el contador de cuenta regresiva cada segundo
            },
            onResume: function() {
                // El cuadro de diÃƒÂ¡logo se cierra manualmente. No hay que hacer nada mÃƒÂ¡s
            }
        });
    }

    function mostrarMenu(opciones) {
        // ver en ../includes/slideMenu cómo se implementó este plugin
        var menu = $("#leftcolumn").sliderMenu({// elemento al que se agrega el menú
            ancho: '160px', // ancho del panel que contiene el menú
            opciones: opciones, // las opciones para las cuales tiene privilegios el usuario, normalmente se cargan desde el servidor
            menu: {// el menú de opciones que se muestra
                'Recursos tecnológicos': [
                    'Detalle de equipos',
                    'Reserva de equipos'
                ],
                'Espacios de trabajo': [
                    'Detalle de salas',
                    'Reserva de salas'
                ],
                'Monitorías': [
                    'Asignación',
                    'Control',
                    'Programación'
                ],
                'Usuarios': [
                    'Comunidad',
                    'Privilegios'
                ],
                'Sistema': [
                    'Cerrar sesión',
                    'Configuración',
                    'Mantenimiento',
                    'Reportes',
                    'Utilidades'
                ]
            },
            vincular: function(linksDeOpciones) {
                var menu = $(this);
                // <linksDeOpciones> son los hipervículos correspondientes a la propiedad <menu>
                // para cada link (<a href="">) activo de la lista del menú hacer algo
                $(linksDeOpciones).each(function() {
                    var opcion = $(this).text(); // el nombre de una opción del menú

                    $(this).on('click', function(event) {
                        if (opcion === "Cerrar sesión") {
                            // al dar clic sobre una opción se pueden ejecutar directamente acciones como se muestra aquí
                            // Más adelante se supone que habrá un método cerrarSesion() en la clase Usuario
                            // $.post("controlador/fachada.php", {clase: 'Usuario', oper: 'cerrarSesion'}, function() {
                            if (opciones[opcion]) {  // si la opción no está bloqueada...
                                window.location.href = "index.html";
                            }
                            //})
                        } else {
                            // al dar clic sobre una opción también se puede cargar una página en un contenedor de la aplicación
                            menu.sliderMenu('cargar', {contenedor: $("#contentcolumn"), pagina: opciones[opcion]});
                        }
                        event.preventDefault();
                    });
                });
                timeout();
            }
        }).sliderMenu('opcionesBloqueadas'); // un simple ejemplo cuyo resultado se puede ver en la consola

    }



});

/**
 * Permite autenticar los usuarios que hacen uso del sistema y guarda en sesión los datos necesarios
 * ¡¡¡¡IMPORTANTE!!!!! se està probando con un usuario por defecto
 */
function autenticar() {
    var opciones;
    // debe ser síncrono, por eso se utiliza $.ajax
    $.ajax({
        type: "POST",
        url: "controlador/fachada.php",
        data: {
            clase: "Usuario",
            oper: 'autenticar',
//            usuario: $('#index-nombre-usuario').val(),
//            contrasena: MD5($('#index-contrasena').val())
//          QUITAR ESTO AL TERMINAR LAS PRUEBAS  ****************************************
            usuario: '147',
            contrasena: MD5('1')
        },
        async: false,
        dataType: "json"
    }).done(function(data) {
        opciones = data;
    }).fail(function() {
        alert("No se pudo realizar la autenticación del usuario");
    });
    return opciones;
}

/**
 * Elimina los actuales elementos de un combo y agrega los del argumento.
 * @param {Array} elementos Un array asociativo con los elementos 
 * @returns {jQuery.fn.agregarElementos}
 */
jQuery.fn.agregarElementos = function(elementos) {
    var combo = this;
    combo.empty();

    if (typeof elementos[0] === 'object') {  // los datos vienen de tipo PDO::FETCH_ASSOC
        for (var i in elementos) {
            var elemento = $.map(elementos[i], function(value, index) {
                return [value];
            });
            combo.append($("<option></option>").attr("value", elemento[0]).text(elemento[1]));
        }
    } else {  // los datos vienen de tipo FETCH_KEY_PAIR
        $.each(elementos, function(indice, valor) {
            combo.append($("<option></option>").attr("value", indice).text(valor));
        });
    }
    return combo;
};

jQuery.fn.getSelectList = function(parametros) {
    var combo = this;
    var asincrono = ("async" in parametros) ? parametros['async'] : false;
    var aviso = ("aviso" in parametros) ? parametros['aviso'] : false;

    if (!("id" in parametros)) {
        parametros['id'] = $(this).attr('id');
    }
    if (!("dataType" in parametros)) {
        parametros['dataType'] = 'json';
    }

    $.ajax({
        type: "POST",
        url: "controlador/fachada.php",
        beforeSend: function(xhr) {
            if (aviso) {
                // $.blockUI({message: getMensaje(aviso)});
            }
        },
        data: parametros,
        async: asincrono,
        tipoRetorno: parametros['dataType']  // [xml|html|json|jsonp|text]
    }).done(function(data) {
        combo.html(data);
    }).fail(function() {
        console.log("Error de carga de datos: " + JSON.stringify(parametros));
        alert("Error de carga de datos");
    }).always(function() {
        if (aviso) {
            // $.unblockUI();
        }
    });
    return combo;
};

/**
 * Retorna una lista de elementos creados a partir de una tabla
 * @param {object} parametros clase, operacion y argumentos adicionales de la forma {p1:v1, .. pN:vN}, incluso el parámetro asincrono[true|false] por defecto false
 * @returns Object Un objeto con la lista de la forma {id1:elemento1, .. idN:elementoN}
 */
function getElementos(parametros) {
    var asincrono = false, aviso = false, elementos = new Object();
    aviso = ("aviso" in parametros) ? parametros['aviso'] : false;
    asincrono = ("async" in parametros) ? parametros['async'] : false;
    mapear = ("mapear" in parametros) ? parametros['mapear'] : false;

    $.ajax({
        type: "POST",
        url: "controlador/fachada.php",
        beforeSend: function(xhr) {
            if (aviso) {
                // $.blockUI({message: getMensaje(aviso)});
            }
        },
        data: parametros,
        async: asincrono,
        dataType: "json"
    }).done(function(data) {
        elementos = data;
    }).fail(function() {
        console.log("Error de carga de datos: " + JSON.stringify(parametros));
        alert("Error de carga de datos");
    }).always(function() {
        if (aviso) {
            // $.unblockUI();
        }
    });
    return elementos;
}

/**
 * Descarga de manera controlada un archivo
 * @param String nombreArchivo El nombre del archivo a descargar
 */
function descargar(nombreArchivo) {
    $.fileDownload('controlador/fachada.php', {
        httpMethod: "POST",
        data: {
            clase: 'Utilidades',
            oper: 'descargar',
            archivo: nombreArchivo
        },
        failCallback: function(respuesta, url) {
            console.log('OJO: ' + respuesta)
            if (respuesta) {
                respuesta = jQuery.parseJSON(respuesta);
                alert('El intento de descarga reporta el siguiente error:<br>' + respuesta.mensaje);
            } else {
                alert('Sucedió un error inesperado intentando la descarga');
            }
        }
    });
}

/**
 * Devuelve la cadena con mayúsculas iniciales
 * @returns {String}
 */
String.prototype.capitalize = function() {
    return this.replace(/[^\s]+/g, function(str) {
        str = str.toLowerCase();
        if ('de del el la los las y o'.indexOf(str) > 0) {
            return str;
        } else {
            return str.substr(0, 1).toUpperCase() + str.substr(1);
        }
    });
};

function respuestaServidor(response) {
    var respuesta = jQuery.parseJSON(response.responseText)
    return [respuesta.ok, respuesta.mensaje]
}

function cambiarClave() {
    //Abrir un dialogo con contraseña actual, nueva contraseña y confirmar contraseña
    //Si nueva=confirmada => post para hacer cambio de contraseña
    $('#index-frmautentica').hide();
    $('#index-frmrestablece').dialog({
        autoOpen: true,
        width: 430,
        height: 250,
        modal: true,
        open: function() {
            $("#index-frmrestablece label").css("width", "150px");
            $("#index-frmrestablece input").css("width", "180");
            $(".ui-dialog-titlebar-close").hide();
            $("#restablece-aceptar").button({icons: {primary: "ui-icon-check"}});
            $("#restablece-cancelar").button({icons: {primary: "ui-icon-close"}});
        },
        buttons: [
            {id: "restablece-aceptar", text: "Aceptar", click: function() {
                    var opciones = restablecer();
                    //En esta parte se valida el acceso o no a la aplicación
                    if (opciones == true) {
                        alert('Contraseña correctamente restablecida');
                        window.location.href = "index.html";
                    } else {
                        //Permite reintentar
                        alert('Las contraseñas no coinciden');
                        window.location.href = "index.html";
                    }
                    $(this).dialog("close");
                }},
            {id: "restablece-cancelar", text: "Cancelar", click: function() {
                    window.location.href = "index.html";
                    //$(this).dialog("close");
                }}
        ]
    })
}

function restablecer() {
    var opciones;
    // debe ser síncrono, por eso se utiliza $.ajax
    $.ajax({
        type: "POST",
        url: "controlador/fachada.php",
        data: {
            clase: "Usuario",
            oper: 'restablecer',
            claveAnterior: MD5($('#index-conrasena-anterior').val()),
            claveNueva: MD5($('#index-contrasena-nueva').val()),
            claveConfirmada: MD5($('#index-repetir-contrasena-nueva').val())
        },
        async: false,
        dataType: "json"
    }).done(function(data) {
        opciones = data;
    }).fail(function() {
        alert("Error al restablecer las contraseñas");
    });
    return opciones;
    }
 /**
  * Calcula el ancho inicial para un objeto jqGrid
  * @param {Object} columnas
  * @returns {Integer|anchoColumnas|ColsGridContenedor|Number|ancho}
  */
 function calcularAnchoGrid(columnas) {
     var anchoGrid = anchoContenedor;
     if ((typeof columnas === "object") && (columnas !== null)) {
         var anchoColumnas = 63; // tiene en cuenta el ancho aproximado de los bordes izquierdo y derecho del grid
         for (var i in columnas.datos) {
             anchoColumnas += columnas.datos[i].width;
         }
         if (anchoColumnas < anchoContenedor) {
             // aumentar un poco el ancho a cada columna hasta copar el ancho del grid
             var aumento = (anchoContenedor - anchoColumnas) / columnas.datos.length;
             for (var i in columnas.datos) {
                 columnas.datos[i].width = columnas.datos[i].width + aumento;
             }
         }
     } else {
        alert('Se espera un objeto que tenga las definiciones para colNames y colModel');
     }
     return anchoGrid;
 }


