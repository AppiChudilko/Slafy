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
                    <form action="https://www.instagram.com/oauth/authorize" class="row" style="margin: 0">
                        <div class="col s12">
                            <input type="hidden" name="client_id" value="678017163538368">
                            <input type="hidden" name="redirect_uri" value="https://adaptation-usa.com/">
                            <input type="hidden" name="scope" value="user_profile,user_media">
                            <input type="hidden" name="response_type" value="code">
                            <button class="btn border-blue border-accent-4 blue-text text-accent-4 waves-effect hover-blue hover-accent-4 hover-text-white">Войти</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>