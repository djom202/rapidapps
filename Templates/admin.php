<html>
	<head>
        <meta charset="utf-8" />
        <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,600,300' rel='stylesheet' type='text/css' />
        <?php
            Link::loadStyle('Resource/load.php?file=jquery-ui.min.css&type=text/css', 'text/css', 'stylesheet', 'screen');
            Link::loadStyle('Resource/load.php?file=admin.css&type=text/css', 'text/css', 'stylesheet', 'screen');
            Link::loadStyle('Resource/load.php?file=css/font-awesome.min.css&type=text/css', 'text/css', 'stylesheet', 'screen');
        ?>
        
        <!--Javascript-->
        <?php
            Link::loadScript('Scripts/load.php?file=js/jquery.min.js&type=text/javascript');
            Link::loadScript('Scripts/load.php?file=js/jquery-ui.min.js&type=text/javascript');
            Link::loadScript('Scripts/load.php?file=js/file.explorer.js&type=text/javascript');
            Link::loadScript('Scripts/load.php?file=js/render.js&type=text/javascript');
            Link::loadProjectBase();
        ?>
        <title>RapidApps - Admin</title>
	</head>
	<body>
        <div id="admin-panel">
            <div id="header">
                <div class="lefty">
                    <a class="icon icon-signout" title="Cerrar Sesión" onclick="javascript:logout();"></a>
                    <a class="icon icon-bell-alt" title="Alertas"></a>
                    <a class="icon icon-th"></a>
                    <a id="h-user">...</a>
                </div>
            </div>
            
            <div id="tabs">
                <ul class="tabs">
                    <li class="select" onclick="javascript:changeTab(0);">
                        <a href="#forms"><i class="icon-tags"></i> Editar formularios</a>
                    </li>
                    
                    <li onclick="javascript:changeTab(1);">
                        <a class="tiny" href="#explorer" title="Explorador de Archivos">
                            <i class="icon-folder-close"></i> &nbsp;</a>
                    </li>

                    <li onclick="javascript:changeTab(2);">
                        <a class="tiny" href="#installation" title="Revisar Contenidos">
                            <i class="icon-archive"></i> &nbsp;</a>
                    </li>
                </ul>
                
                <div id="tabs-area">
                    <div class="tab" id="chat">
                        <div class="list"></div>
                        
                        <div class="body" id="containner">
                            <div class="sidebar">
                                <div class="side" id="_tools"></div>
                                
                                <div class="side" id="_properties">
                                    <button onclick="javascript:R.save();"><i class="icon-save"></i></button>
                                </div>
                            </div>
                            
                            <!--Aqui se crea la ventana-->
                            <div class="component"></div>
                        </div>
                    </div>
                    
                    <div class="tab" id="photos">
                        <iframe id="loadfile" src=""></iframe>
                        <div id="location_bar">
                            <button id="btn_up" class="icon-chevron-sign-up"></button>

                            <div id="location">
                                <ul></ul>
                            </div>
                            
                            <button id="btn_new_folder" class="icon-folder-close"></button>
                            <button id="btn_upload" class="icon-cloud-upload"></button>
                        </div>

                        <div id="explorer"></div>
                    </div>

                    <div class="tab" id="installation">
                        <div class="list"></div>
                        
                        <div class="body">
                            <!--Cuerpo de contenido-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
        <div id="loadInformation">
            <a href="javascript:Forms.close();" class="close">X cerrar</a>
            <div class="information"></div>
        </div>
        
        <div id="login">
            <form id="frm-login">
                <br />
                <br />
                <p>
                    <input type="text" id="user" name="user" placeholder="Usuario" />
                </p>
                
                <p>
                    <input type="password" id="pass" name="pass" placeholder="Clave" />
                </p>
                
                <p>
                    <button>Iniciar Sesion</button>
                </p>
            </form>
        </div>
        
        <script type="text/javascript">
            /*Iniciar el LinkServer y eventos para obtener la informacion*/
            var LinkServer = new Linker("<?= Link::Url(); ?>");
            LinkServer.setExtension(".json?");

            var LinkPage = new Linker("<?= Link::Url(); ?>");
            LinkPage.setExtension("/");
            
            var nombre = "", usuario = "", request = false;

            /*Logout*/
            function logout () {
                uget({
                    type: 'GET',
                    url: LinkServer.Url('user', 'logout', [])
                }).done(function () {
                    $("#login").css({
                        opacity: 0,
                        display: 'block'
                    }).animate({
                        opacity: 1
                    }, 500);
                });
            }
            
            function init () {
                for(var i in _tools) {
                    var _t = _tools[i];
                    
                    $("<div/>", {
                        id: i,
                        class: '_row',
                        title: _t.description
                    }).data(
                        'type', i
                    ).html(
                        "<img src='" + _t.icon + "' />" + _t.text
                    ).draggable({
                        containment: '#containner',
                        helper: "clone",
                        revert: "invalid",
                        cursor: "move"
                    }).appendTo("#_tools");
                }
            }
            
            /*Formulario de login*/
            $(function () {
                init();
                
//                $(".screen ul").sortable({
//                    revert: true,
//                    axis: 'y'
//                });
//                
//                $(".element").droppable({
//                    accept: '._row',
//                    hoverClass: 'hovered',
//                    drop: R.Drop.Tool
//                });
                
                $("#frm-login").submit(function (e) {
                    e.preventDefault();
                    
                    uget({
                        type: 'POST',
                        url: LinkServer.Url('login', 'login', []),
                        data: {
                            user: $("#user").val(),
                            pass: $("#pass").val()
                        }
                    }).done(function (data) {
                        if(data._code === 200) {
                            nombre = data._response.nombre;
                            usuario = data._response.usuario;
                            
                            Forms.load();
                            scandir();
                            
                            $("#h-user").html(nombre);
                            $("#login").animate({
                                opacity: 0
                            }, {
                                duration: 500,
                                complete: function () {
                                    $("#login").css({
                                        display: 'none'
                                    });
                                }
                            });
                        } else {
                            alert(data._message);
                        }
                    });
                    
                    return false;
                });
            });
            
            /*Cambiar entre pestañas*/
            function changeTab (i) {
                $("#tabs .tabs li").removeClass("select");
                $("#tabs .tabs li").eq(i).addClass("select");
                
                $("#tabs-area .tab").css({
                    display: 'none'
                });
                $("#tabs-area .tab").eq(i).css({
                    display: 'block'
                });
                
                if(i === 4 && map === null) {
                    //initialize();
                }
            }
            
            /*Compañia*/
            var Forms = {
                _info: {},
                load: function () {
                    uget({
                        type: 'GET',
                        url: LinkServer.Url('form', 'get', [])
                    }).done(function (data) {
                        //Lista en tab de slides
                        var list = $(".idform"),
                            form = $("#chat .list"),
                            form2 = $("#installation .list");
                    
                        list.html("");
                        form.html("");
                        form2.html("");

                        for (var i=0; i<data._response.length; i++) {
                            var ob = data._response[i];
                            
                            list.append("<option value='" + ob.idform + "'>" 
                                        + ob.name + "</option>");
                            
                            form.append("<div class='row'>"
                                        + "<p onclick='javascript:Forms.get(" + ob.idform + ");'>" + ob.name + "</p>"
                                        + "<p> last Mod: " + ob.creation + "</p>"
                                        + "<a href='javascript:Forms.delete(" + ob.idform + ");' class='icon-trash'></a>"
                                        + "</div>");
                                
                            form2.append("<div class='row'>"
                                        + "<p onclick=\"javascript:Forms.information(" + ob.idform + ");\">" + ob.name + "</p>"
                                        + "<p> last Mod: " + ob.creation + "</p>"
                                        + "</div>");
                        }
                        
                        form.append($("<button/>", {
                            id: 'createProject'
                        }).html("<i class='icon-plus-sign'></i> Nuevo Formulario")
                          .click(function () {
                              R.new_form();
                          }));
                    });
                }, delete: function (id) {
                    var del = confirm("Desea eliminar el formulario?");
                    
                    if(del) {
                        uget({
                            type: 'DELETE',
                            url: LinkServer.Url('form', 'remove', []),
                            data: {
                                idform: id
                            }
                        }).done(function (data) {
                            if(data._code === 200) {
                                Forms.load();
                            } else {
                                alert("Hubo un error al eliminar. " + data._message);
                            }
                        });
                    }
                }, get: function (id) {
                    uget({
                        type: 'GET',
                        url: LinkServer.Url('form', 'get', {
                            idform: id
                        })
                    }).done(function (data) {
                        if(data._code === 200) {
                            R.load(data._response.value);
                            console.log(JSON.parse(data._response.value));
                        } else {
                            alert("Hubo un error al cargar el proyecto: " + data._message);
                        }
                    });
                }, information: function (id) {
                    uget({
                        type: 'GET',
                        url: LinkServer.Url('screens', 'get', {
                            idform: id
                        })
                    }).done(function (data) {
                        if(data._code === 200) {
                            var table = $("<table/>"),
                                tbody = $("<tbody/>");
                        
                            $("<thead/>").html(
                                $("<tr/>").html(
                                    $("<th/>").html("Fecha")
                                ).append(
                                    $("<th/>").html("Enviado por")
                                ).append(
                                    $("<th/>").html("Aplicacion")
                                ).append($("<th/>", {
                                    colspan: 2
                                }))
                            ).appendTo(table);
                            
                            for(var i in data._response) {
                                var ob = data._response[i];
                                
                                Forms._info[ob._id] = ob.screens;
                                
                                tbody.append(
                                    $("<tr/>").html(
                                        $("<td/>").html(ob.date)
                                    ).append(
                                        $("<td/>").html(ob.user)
                                    ).append(
                                        $("<td/>").html(ob.name)
                                    ).append(
                                        $("<td/>").html(
                                            $("<a/>", {
                                                href: 'javascript: Forms.show(\'' + ob._id + '\')'
                                            }).html("ver")
                                        )
                                    ).append(
                                        $("<td/>").html(
                                            $("<a/>", {
                                                href: 'javascript: Forms.remove(\'' + ob._id + '\')'
                                            }).html("eliminar")
                                        )
                                    )
                                );
                            }
                            
                            table.append(tbody);
                            
                            $("#installation .body").html(table);
                        }
                    });
                }, show : function (_id) {
                    var e = Forms._info[_id],
                        information = $("#loadInformation .information");
                    
                    information.empty();
                    
                    if(e) {
                        for(var name in e) {
                            var table = $("<table/>"),
                                tbody = $("<tbody/>");
                            
                            $("<thead/>").html(
                                $("<tr/>").html(
                                    $("<th/>", {
                                        colspan: 2
                                    }).html(name)
                                )
                            ).appendTo(table);
                    
                            for(var comp in e[name]) {
                                $("<tr/>").html(
                                    $("<th/>").html(comp)
                                ).append(
                                    $("<td/>").html(e[name][comp])
                                ).appendTo(tbody);
                            }
                            
                            table.append(tbody);
                            information.append(table);
                        }
                        
                        $("#loadInformation").css({
                            display: 'block'
                        });
                    } else {
                        alert("No se ha encontrado el formulario.");
                    }
                }, remove: function (_id) {
                    var yes = confirm("Desea eliminar " + _id + "?");
                    
                    if(yes) {
                        alert("Not implemented yet!");
                    }
                }, close: function () {
                    $("#loadInformation").css({
                        display: 'none'
                    });
                }
            };
        </script>
	</body>
</html>