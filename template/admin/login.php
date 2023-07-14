<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

?>

<div class="container" style="padding: calc(5% + 50px) 0">
    <div class="section">
        <div class="row">
            <div class="col s12 m3 l4"></div>
            <div class="col s12 m6 l4">
                <div class="card-panel">
                    <form method="post" class="row" style="margin: 0">
                        <div class="input-field col s12">
                            <input id="login" name="login" type="text" class="validate">
                            <label for="login">Login</label>
                        </div>
                        <div class="input-field col s12">
                            <input id="password" name="pass" type="password" class="validate">
                            <label for="password">Password</label>
                        </div>
                        <div class="col s12">
                            <button name="act-login" class="btn border-blue border-accent-4 blue-text text-accent-4 waves-effect hover-blue hover-accent-4 hover-text-white">Войти</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>