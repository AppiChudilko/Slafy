<?php

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $qb;
global $user;
global $userInfo;
global $methods;
global $tmp;
?>
<div class="center feed-like-big hide">
    <i class="material-icons-round red-text animated">favorite</i>
</div>
<div class="container container-full-mobile" style="padding-top: 80px">
    <div class="section hide" style="padding: 0">
        <div class="row ">
            <div class="col s12">
                <div class="profile-hightlight-list flex animated slafy-anim">
                    <div class="center profile-hightlight">
                        <img class="circle" src="<?php echo $user->getUserAvatar($userInfo['id'], $userInfo['avatar']) ?>">
                        <br>
                        <label><?php echo $user->getUserName($userInfo['login'], $userInfo['name']) ?></label>
                    </div>
                    <?php

                    $resultHighlight = $qb
                        ->createQueryBuilder('users')
                        ->selectSql()
                        ->where('name != \'\'')
                        ->andWhere('id != ' . $userInfo['id'])
                        ->executeQuery()
                        ->getResult()
                    ;

                    foreach ($resultHighlight as $item) {
                        echo '
                            <div class="center profile-hightlight">
                                <img class="circle" src="' . $user->getUserAvatar($item['id'], $item['avatar']) . '">
                                <br>
                                <label>' . substr($user->getUserName($item['login'], $item['name']), 0, 11) . '</label>
                            </div>
                        ';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container container-feed">
    <div class="section">
        <div class="row">
            <div class="col s12" id="user-feed-content">
                <?php
                    $tmp->showBlockPage('feedList');
                ?>
            </div>
        </div>
    </div>
</div>
<?php
echo $methods->getFeedModals();