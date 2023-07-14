<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $page;

$enableLightTheme = isset($_COOKIE['slafy-is-light']);
//if ($user->isLogin()) {
//if ($userInfo['is_light_theme'])
//    $enableLightTheme = true;
//}
?>
</main>

<!--  Scripts-->
<!-- Compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="/client/js/material-appi.js"></script>
<script src="/client/js/material-charts.js"></script>
<script src="/client/js/slider.js"></script>
<script src="/client/js/main.js?<?php echo time() ?>"></script>

<?php

if ($this->modal['show']) {
    //echo '<script type="text/javascript">$(document).ready(function(){ $("#modalInfo").openModal(); });</script>';
    echo '<script type="text/javascript">M.toast({html: \'' . $this->modal['text'] . '\', classes: \'rounded\'});</script>';
}
?>

<script>
    var alpha = /[ A-Za-z]/;
    var numeric = /[0-9]/;
    var alphanumeric = /[ A-Za-z0-9]/;

    function validateKeypress(validChars) {
        var keyChar = String.fromCharCode(event.which || event.keyCode);
        return validChars.test(keyChar) ? keyChar : false;
    }
</script>

<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

    ym(91630220, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true
    });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/91630220" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

</body>
</html>