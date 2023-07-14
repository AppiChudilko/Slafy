<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

?>

<div class="container" style="padding: calc(3% + 50px) 0">
    <div class="section">
        <div class="row">
            <div class="col s12 m3 l4"></div>
            <div class="col s12 m6 l4">
                <div style="width: 100%;" class="center">
                    <a href="/">
                        <img style="width: 70px; margin-bottom: 30px" src="/client/images/logo/svg/logoUsa.svg">
                    </a>
                </div>
                <div class="card-panel">
                    <form method="post" class="row" style="margin: 0">
                        <div class="input-field col s12">
                            <input name="login" placeholder="Логин" type="text">
                        </div>
                        <div class="input-field col s12">
                            <input name="pass" placeholder="Пароль" type="password">
                        </div>
                        <div class="col s12">
                            <button name="act-login" class="btn border-blue border-accent-4 blue-text text-accent-4 waves-effect hover-blue hover-accent-4 hover-text-white">Войти</button>
                            <a href="/signup" class="btn border-grey border-darken-3 black-text text-accent-4 waves-effect hover-blue hover-accent-4 hover-text-white">Регистрация</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>