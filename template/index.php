<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
$count = $qb->createQueryBuilder('users')->selectSql('id')->limit(1)->orderBy('id DESC')->executeQuery()->getSingleResult();
?>

<style>
    body {
        background: #fff !important;
    }

    img {
        object-fit: cover;
    }
</style>
<div style="position: absolute; width: 100%; height: 100vh; overflow: hidden; z-index: -1;">
    <img class="hide-on-med-and-down" src="https://i0.wp.com/tbilisi.link/wp-content/uploads/2017/10/2-flag-usa-b.jpg" style="position: absolute;left: 0%;top: 0%;opacity: 0.1; width: 100%; height: 100vh">
    <img class="show-on-medium-and-down" src="/client/images/logo/svg/logo_cl.svg" style="position: absolute; width: 300%; top: -30%; left: -100%; opacity: 0.1; display: none">
</div>
<div class="container fullsize">
    <div class="section">
        <div class="row">
            <div class="col s12">
                <br>
                <br>
                <div class="row">
                    <div class="col s12 l7">
                        <div style="position: relative; z-index: 2; margin-top: 120px">
                            <h1 class="bw-text" style="font-weight: 700; font-size: 7rem; margin-top: 0; width: fit-content;">ADAPTATION USA</h1>
                            <div class="flex" style="margin: auto">
                                <a href="/login" class="btn btn-large border-indigo border-accent-4 indigo-text text-accent-4 waves-effect hover-indigo hover-accent-4 hover-text-white">Войти</a>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 l5 center">
                        <img style="width: 100%; margin-top: 30px; max-width: 250px; b" src="https://i.imgur.com/BUUmC0X.png">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>