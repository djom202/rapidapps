var dir = [];

/*Listar archivos*/
function scandir() {
    var location = $("#location ul");
    location.html("<li onclick=\"javascript:back_folders(-1)\"><i class='icon-home'></i> <i class='icon-caret-right'></i></li>");

    for (var i = 0; i < dir.length; i++) {
        location.append("<li onclick=\"javascript:back_folders(" + i + ")\">"
                + dir[i] + " <i class='icon-caret-right'></i></li>");
    }

    $("#explorer").html("<p style='text-align:center;margin-top:36px;'><i style='font-size:36px;' class='icon-spinner'></i></p>");

    var strdir = dir.join('/');
    strdir += (dir.length > 0) ? '/' : '';

    uget({
        type: 'GET',
        url: LinkServer.Url('file', 'scan', {
            dir: encodeURI(strdir)
        })
    }).done(function(data) {
        var explorer = $("#explorer");
        if (data._code === 200) {
            explorer.html("");
            for (var i = 0; i < data._response.scan.length; i++) {
                var ob = data._response.scan[i],
                        str = "<div class='item' onclick=\"javascript:"
                        + "open_" + ob.type + "('" + ob.name + "');\"><div class='center'><img src='"
                        + ((ob.type === "folder") ? 'res/folder.png' :
                                LinkServer.Url('file', 'thumb', {
                                    dir: encodeURI(strdir + ob.name)
                                })) + "' /></div>"
                        + "<p>" + ob.name + "</p></div>";

                explorer.append(str);
            }

            $("#loadfile").contents().find("#inputname").val(strdir);

            if (data._response.scan.length === 0) {
                $("#explorer").html("<p style='text-align:center;margin-top:36px;'>Carpeta vacia. </p>"
                        + "<p style='text-align:center;'><button class='eliminable'><i class='icon-trash'></i> Eliminar</button></p>");

                $(".eliminable").click(function() {
                    delete_file('');
                });
            }
        } else {
            alert("Problemas al abrir directorio.");
        }
    });
}

function timeConverter(UNIX_timestamp) {
    var a = new Date(UNIX_timestamp * 1000),
            months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            year = a.getFullYear(),
            month = months[a.getMonth()],
            date = a.getDate(),
            hour = a.getHours(),
            min = a.getMinutes(),
            sec = a.getSeconds(),
            time = date + ',' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec;

    return time;
}

/*Iniciar el creado de nueva carpeta*/
function new_folder() {
    var explorer = $("#explorer");
    var str = "<div class='item select'><div class='center'><img src='res/folder.png' /></div>"
            + "<p><textarea class='new_folder' placeholder='Nueva Carpeta'></textarea></p></div>";

    explorer.find(".select").remove();
    explorer.append(str);
    explorer.find(".select").find("textarea").focus();
    explorer.scrollTop(999999);
    $(".new_folder").blur(function() {
        create_folder($(this).val());
    });

    $(".new_folder").keydown(function(e) {
        var code = (e.wich) ? e.wich : e.keyCode;
        if (code == 13) {
            e.preventDefault();
            $(".new_folder").blur();
        }
    });
}

/*Crear nueva carpeta*/
function create_folder(str) {
    if (str == "") {
        $("#explorer").find(".select").remove();
    } else {
        var strdir = dir.join('/');
        strdir += (dir.length > 0) ? '/' : '';

        uget({
            type: 'GET',
            url: LinkServer.Url('file', 'add', {
                dir: encodeURI(strdir + str)
            })
        }).done(function(data) {
            if (data._code !== 200) {
                alert("No se pudo crear la carpeta");
                $("#explorer").find(".select").remove();
            } else {
                scandir();
            }
        });
    }
}

function open_folder(str) {
    dir.push(str);
    scandir();
}

function delete_file(str) {
    var strdir = dir.join('/');
    strdir += (dir.length > 0) ? '/' : '';

    var sw = confirm("Va a eliminar. Desea continuar?");
    if (sw) {
        uget({
            type: 'GET',
            url: LinkServer.Url('file', 'delete', {
                dir: encodeURI(strdir + str)
            })
        }).done(function(data) {
            if (data._code === 200) {
                if (str == '') {
                    dir.pop();
                }
                scandir();
            } else {
                alert("Imposible eliminar.");
            }
        });
    }
}

function open_file(str) {
    var strdir = dir.join('/');
    strdir += (dir.length > 0) ? '/' : '';
    var explorer = $("#explorer");

    explorer.html("<p style='text-align:center;margin-top:36px;'><i style='font-size:36px;' class='icon-spinner'></i></p>");

    uget({
        type: 'GET',
        url: LinkServer.Url('file', 'info', {
            dir: encodeURI(strdir + str)
        })
    }).done(function(data) {
        if (data._code === 200) {
            var tmp = dir.slice(0),
                    ob = data._response;
            tmp.push(str);
            explorer.html("<h2>" + str + "</h2>"
                    + "<img class='preview' src='" + LinkPage.Url('res', 'elements', tmp) + "' />");
            explorer.append("<div class='file-info'><p><b>Link imagen:</b><input type='text' value='" + LinkPage.Url('res', 'elements', tmp) + "'></p>"
                    + "<hr /><ul>"
                    + "<li><b>Tamaño:</b> " + ob.size + "B</li>"
                    + "<li><b>Creado:</b> " + timeConverter(ob.ctime) + "</li>"
                    + "<li><b>Modificado:</b> " + timeConverter(ob.mtime) + "</li>"
                    + "<li><b>Ultimo acceso:</b> " + timeConverter(ob.atime) + "</li>"
                    + "</ul><button class='eliminable'><i class='icon-trash'></i> Eliminar</button></div>");

            $(".eliminable").click(function() {
                delete_file(str);
            });
        } else {
            explorer.html("<h1>Algo no salio como lo planeado :(</h1>");
            explorer.append("<p>" + data._message + "</p>");
        }
    });
}

function upload_file() {
    $("#loadfile").contents().find("body")
            .find("#inputfile").trigger("click");
}

function back_folders(i) {
    dir = dir.slice(0, i + 1);
    scandir();
}

$(function() {
    /*Boton Level Up explorador*/
    $("#btn_up").click(function() {
        dir = dir.slice(0, -1);
        scandir();
    });

    /*Boton nueva carpeta*/
    $("#btn_new_folder").click(function() {
        new_folder();
    });

    /*Boton para subir archivo*/
    $("#btn_upload").click(function() {
        upload_file();
    });

    /*Seleccionar todo al hacer clic sobre el texto de info file*/
    $(document).on('click', '#explorer input', function() {
        $(this).select();
    });

    /*Iframe para subir archivo*/
    var str = "<form id='formfile' method='POST' action='" + LinkServer.Url('file', 'upload', []) + "' enctype='multipart/form-data'>"
            + "<input type='file' name='file' id='inputfile' onchange='this.form.submit();' />"
            + "<input type='text' name='dir' id='inputname' />"
            + "</form>";

    $("#loadfile").contents().find("body").html(str);
    $("#loadfile").load(function() {
        var jstr = $("#loadfile").contents().find("body").text(),
                json = JSON.parse(jstr);

        if (json._code !== 200) {
            alert("Ocurrió un error al subir archivo.");
        }

        setTimeout(function() {
            $("#loadfile").contents().find("body").html(str);
            scandir();
        }, 150);
    });
});