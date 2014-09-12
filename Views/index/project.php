<style>
    #nav .menu a, a.logo {
        color: #000!important;
        text-shadow: 0 0 0 #fff!important;
    }
</style>
<!--SLIDE DE FOTOS-->
<div class="section">
    <div class="wrapper" id="welove">
        <h2 class="subtitle right_align">Proyectos que amam<i class="icomoon-corazon"></i>s</h2>
    </div>
    
    <div id="slide-project"></div>
</div>

<!--Parte I-->
<div id="parte1">
    <div class="wrapper">
        <h2><?= Partial::fetchRows($_VARS['project'], ":titulo"); ?></h2>
        <h3 id="more"><a href="#">Más Información <i class="icomoon-file-pdf"></i></a></h3>
        
        <div class="texto">
            <?= Partial::fetchRows($_VARS['project'], ":primero"); ?>
        </div>
    </div>
</div>

<!--Imagen central-->
<div id="projects">
    <?=
        Partial::fetchRows($_VARS['project'], 
            "<div class='project'>
                <img src=':imagen_central' />
                <div class='context'>
                    <div class='wrapper'>
                        :texto_central
                    </div>
                </div>
            </div>");
    ?>
</div>

<!--Parte II-->
<div id="parte2">
    <div class="wrapper">
        <div class="texto">
            <?= Partial::fetchRows($_VARS['project'], ":segundo"); ?>
        </div>
    </div>
</div>

<script>
    $(function () {
        var slide1 = new imageviewer([<?=
                substr(Partial::fetchRows($_VARS['slide'], 
                    "\"<img src=':imagen' /><div class='context'><div class='wrapper'><h2>:titulo</h2>:descripcion</div></div>\","), 0, -1);
            ?>], $("#slide-project"), 5000, 500);
    });
</script>