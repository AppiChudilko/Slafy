<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

?>

<div class="container" style="padding: calc(2% + 30px) 0">
    <div class="section">
        <div class="row">
            <div class="col s12 m3 l4"></div>
            <div class="col s12 m6 l4">
                <div style="width: 100%;" class="center">
                    <a href="/">
                        <img style="width: 70px; margin-bottom: 30px" src="/client/images/logo/svg/logoUsa.svg">
                    </a>
                </div>
                <div style="width: 100%;" class="center">
                    Чтобы публиковать объявления, необходимо пройти регистрацию. Этот сайт аналог Avito, только в США и с более расширенными возможностями для ведения своего блога.
                    <br>
                    <br>
                </div>
                <div class="card-panel">
                    <form method="post" action="/signup" class="row" style="margin: 0">
                        <div class="input-field col s12">
                            <input name="email" value="<?php echo time() ?>" required placeholder="Email" type="text">
                        </div>
                        <div class="input-field col s12">
                            <input name="pass1" value="112233" required placeholder="Пароль" type="password">
                        </div>
                        <div class="input-field col s12">
                            <input name="pass2" value="112233" required placeholder="Повторите пароль" type="password">
                        </div>
                        <div class="input-field col s12 hide" style="padding-bottom: 12px;">
                            <div class="switch">
                                <label>
                                    <input name="accept" checked type="checkbox">
                                    <span style="margin-left: 0; margin-right: 6px" class="lever"></span>
                                    Соглашаюсь с <a href="/privacy">политикой</a>
                                </label>
                            </div>
                        </div>
                        <div class="col s12">
                            <button name="act-reg" class="btn border-blue border-darken-3 blue-text text-accent-4 waves-effect hover-blue hover-accent-4 hover-text-white">Регистрация</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>