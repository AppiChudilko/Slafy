<?php

namespace Server\Core;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Server
 */
class Server
{

    private $timeStampNow;
    private $timeStampUTCNow;
    private $dateTimeNow;
    private $dateNow;
    private $timeNow;

    protected $config;

    /**
     * Server constructor.
     */
    function __construct()
    {
        $this->timeStampNow = time();
        $this->timeStampUTCNow = $this->timeStampNow + (3600 * $this->getClientUTC());
        $this->dateTimeNow = gmdate('Y-m-d H:i:s', $this->timeStampNow);
        $this->dateNow = gmdate('Y-m-d', $this->timeStampNow);
        $this->timeNow = gmdate('H:i:s', $this->timeStampNow);
        //$this->requestLog();

        $this->config = new Config;
        $this->config = $this->config->getAppiAllConfig()->getObjectResult();
    }

    /**
     * @param $url
     * @return string
     */
    public function getUrlPath($url) {
        $url = parse_url($url);
        return str_replace('/', '', $url['path']);
    }

    /**
     * Mehtod. Set UTC user;
     * @param $utc
     * @return bool
     */
    public function setClientUTC($utc) {
        setcookie("UTC", $utc, 0x6FFFFFFF, "/");
        return true;
    }

    /**
     * Mehtod. Set UTC user;
     * @param $name
     * @param $value
     */
    public function setCookie($name, $value) {
        setcookie($name, $value, 0x6FFFFFFF, "/");
    }

    /**
     * Mehtod. Get UTC user;
     */
    public function getClientUTC() {
        if(isset($_COOKIE['UTC']))
            return $_COOKIE['UTC'];
        return 0;
    }

    /**
     * Mehtod. Get time stamp;
     */
    public function timeStampNow() {
        return $this->timeStampNow;
    }

    /**
     * Mehtod. Get time stamp;
     */
    public function timeStampUTCNow() {
        return $this->timeStampUTCNow;
    }

    /**
     * Mehtod. Get date time;
     */
    public function dateTimeNow() {
        return $this->dateTimeNow;
    }

    /**
     * Mehtod. Get date;
     */
    public function dateNow() {
        return $this->dateNow;
    }

    /**
     * Mehtod. Get time;
     */
    public function timeNow() {
        return $this->timeNow;
    }

    /**
     * Mehtod. Get version framework;
     */
    public function getVersionFW() {
        return EnumConst::VERSION;
    }

    /**
     * Mehtod. Get console log;
     */
    public function consoleLog($text) {
        echo '<script type="text/javascript">console.log("' . $text . '")</script>';
    }

    /**
     * Mehtod. Replace quotes;
     */
    public function replaceQuotes($text) {
        $lit = ["'"];
        $sp = ['"'];
        return str_replace($lit, $sp, $text);
    }

    /**
     * Mehtod. Get referrer;
     */
    public  function getLocationByQuery($query) {
        return file_get_contents('http://api.positionstack.com/v1/forward?access_key=c1a2cf86c8391e7eebfb814f153e0866&query=' . $query);
    }

    /**
     * Mehtod. Get referrer;
     */
    public  function getReferrer() {
        if (isset($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        }
        return false;
    }

    /**
     * Mehtod. Get client ip;
     */
    public function getClientIp() {
        if(isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        else {
            return "localhost";
        }
    }

    /**
     * Mehtod. Get Server URL;
     */
    public function getServerURL() {
        $url = "http://";
        $url .= $_SERVER["SERVER_NAME"]; // $_SERVER["HTTP_HOST"] is equivalent
        if ($_SERVER["SERVER_PORT"] != "80") $url .= ":".$_SERVER["SERVER_PORT"];
        return $url;
    }

    /**
     * Mehtod. Get full URL;
     */
    public function getCompleteURL() {
        return $this->getServerURL() . $_SERVER["REQUEST_URI"];
    }

    /**
     * @param $url
     * Mehtod. Redirect;
     */
    public function redirect($url = '/') {
        header("Cache-Control: no-store,no-cache,mustrevalidate");
        header("Location: " . $url);
    }

    /**
     * Mehtod. HtmlSpecialChars, StrIpSlashes, AddcSlashes;
     */
    public function charsString($string, $isHtmlSpecialChars = true) {
        if($isHtmlSpecialChars)
            return addcslashes(htmlspecialchars(stripslashes($string)), '\'"\\');
        else
            return addcslashes(stripslashes($string), '\'"\\');
    }

    /**
     * Mehtod. HtmlSpecialChars, StrIpSlashes, AddcSlashes;
     */
    public function decodeString($string, $toHtml = false) {
        if ($toHtml)
            $string = htmlspecialchars_decode($string);
        return htmlspecialchars_decode($string);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function deleteAllSymbolsAndNumbers($string) {
        return preg_replace('/[^a-zA-Zа-яА-Я]/uix','',$string);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function deleteAllSymbols($string) {
        return preg_replace('![^\w]*!uix','',$string);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function deleteAllNumbers($string) {
        return preg_replace('/[\d]/', '', $string);
    }

    /**
     * @param $timeStamp
     * @return string
     */
    public function timeStampToDate($timeStamp) {
        global $UTC_TO_TIME;
        $timeStamp = $timeStamp + $UTC_TO_TIME;
        return gmdate('m', $timeStamp) . '/' . gmdate('d', $timeStamp) . '/' . gmdate('Y', $timeStamp);
    }

    /**
     * @param $timeStamp
     * @return string
     */
    public function timeStampToTime($timeStamp) {
        global $UTC_TO_TIME;
        $timeStamp = $timeStamp + $UTC_TO_TIME;
        return gmdate('H', $timeStamp) . ':' . gmdate('i', $timeStamp);
    }


    public function timeStampToDateFormat($timeStamp, $isShowYear = true){
        global $monthN;
        global $UTC_TO_TIME;
        //$date = new DateTime(intval($timeStamp));
        //return $isShowYear ? $date->format('d') . ' ' . $monthN[$date->format('m')] . ' ' . $date->format('Y') : $date->format('d') . ' ' . $monthN[$date->format('m')] ;
        $timeStamp = $timeStamp + $UTC_TO_TIME;
        if ($isShowYear)
            return gmdate('d', $timeStamp) . ' ' . $monthN[gmdate('m', $timeStamp)] . ' ' . gmdate('Y', $timeStamp);
        return gmdate('d', $timeStamp) . ' ' . $monthN[gmdate('m', $timeStamp)];
    }


    public function timeStampToDateTimeFormat($timeStamp, $isShowYear = true){
        global $monthN;
        global $UTC_TO_TIME;
        //$date = new DateTime(intval($timeStamp));
        //return $isShowYear ? $date->format('d') . ' ' . $monthN[$date->format('m')] . ' ' . $date->format('Y') : $date->format('d') . ' ' . $monthN[$date->format('m')] ;
        $timeStamp = $timeStamp + $UTC_TO_TIME;
        if ($isShowYear)
            return gmdate('d', $timeStamp) . ' ' . $monthN[gmdate('m', $timeStamp)] . ' ' . gmdate('Y', $timeStamp) . ' ' . gmdate('H:i', $timeStamp);
        return gmdate('d', $timeStamp) . ' ' . $monthN[gmdate('m', $timeStamp)] . ' ' . gmdate('H:i', $timeStamp);
    }

    /**
     * @param $timeStamp
     * @return string
     */
    public function timeStampToAgoUTC($timeStamp, $postfix = ' назад') {
        $timeStampOffset = $this->timeStampNow() - $timeStamp;

        if ($timeStampOffset < 60)
            return 'только что';
        if ($timeStampOffset / 60 < 60)
        {
            $time = round($timeStampOffset / 60, 0, PHP_ROUND_HALF_DOWN);
            $label = match ($time % 10) {
                1 => $time . ' минуту' . $postfix,
                2, 3, 4 => $time . ' минуты' . $postfix,
                default => $time . ' минут' . $postfix,
            };
            if ($time == 11 || $time == 12 || $time == 13 || $time == 14)
                $label = $time . ' минут' . $postfix;
        }
        else if ($timeStampOffset / (60 * 60) < 24)
        {
            $time = round($timeStampOffset / (60 * 60), 0, PHP_ROUND_HALF_DOWN);
            $label = match ($time % 10) {
                1 => $time . ' час' . $postfix,
                2, 3, 4 => $time . ' часа' . $postfix,
                default => $time . ' часов' . $postfix,
            };
            if ($time == 11 || $time == 12 || $time == 13 || $time == 14)
                $label = $time . ' часов' . $postfix;
        } else if ($timeStampOffset / (60 * 60 * 24) < 7)
        {
            $time = round($timeStampOffset / (60 * 60 * 24), 0, PHP_ROUND_HALF_DOWN);
            $label = $time . ' дней' . $postfix;
            if ($time == 1)
                $label = $time . ' день' . $postfix;
            if ($time == 2 || $time == 3 || $time == 4)
                $label = $time . ' дня' . $postfix;
        }
        else
            return $this->timeStampToDateFormat($timeStamp, gmdate('Y', $timeStamp) != gmdate('Y', $this->timeStampNow()));
        return $label;
    }


    /**
     * @param $n
     * @return string
     */
    public function numberToKkk($n){
        $n = (0+str_replace(",","",$n));
        if(!is_numeric($n)) return null;
        if($n>1000000000000) return round(($n/1000000000000),1).'t';
        else if($n>1000000000) return round(($n/1000000000),1).'b';
        else if($n>1000000) return round(($n/1000000),1).'m';
        else if($n>1000) return round(($n/1000),1).'k';
        return number_format($n);
    }

    /**
     * @param $n
     * @return string
     */
    public function parseText($content, $parseNick = true, $parseTag = true, $parseLink = false, $markDown = false) {
        if ($parseTag)
            $content = preg_replace('/(?:^|\s)#(\w+)/iu', ' <a href="/search?q=$1">#$1</a>', $content);
        if ($parseNick)
            $content = preg_replace('/(?:^|\s)@(\w+)/iu', ' <a spa="@$1">@$1</a>', $content);
        if ($parseNick)
            $content = preg_replace('/(?:^|\s)\(@(\w+)\)/iu', ' (<a spa="@$1">@$1</a>)', $content);
        if ($parseLink)
            $content = preg_replace('/(?:^|\s)https:\/\/([\w\d\b.\/?\-\(\)%"_=+]+)/iu', ' <a target="_blank" href="https://$1">$1</a>', $content);
        if ($markDown) {
            $md = new Markdown();
            $content = $md->render($content);
        }
        return $content;
    }

    /**
     * Mehtod. Get server info;
     */
    public function serverInfo(){
        if (!@phpinfo()) echo 'No Php Info...';
        echo "<br><br>";
        $a=ini_get_all();
        $output="<table border=1 cellspacing=0 cellpadding=4 align=center>";
        $output.="<tr><th colspan=2>ini_get_all()</td></tr>";

        while(list($key, $value)=each($a)) {
            list($k, $v)= each($a[$key]);
            $output.="<tr><td align=right>$key</td><td>$v</td></tr>";
        }

        $output.="</table>";
        echo $output;
        echo "<br><br>";
        $output="<table border=1 cellspacing=0 cellpadding=4 align=center>";
        $output.="<tr><th colspan=2>\$_SERVER</td></tr>";

        foreach ($_SERVER as $k=>$v) {
            $output.="<tr><td align=right>$k</td><td>$v</td></tr>";
        }

        $output.="</table>";
        echo $output;
        echo "<br><br>";
        echo "<table border=1 cellspacing=0 cellpadding=4 align=center>";
        $safe_mode=trim(ini_get("safe_mode"));

        if ((strlen($safe_mode)==0)||($safe_mode==0)) $safe_mode=false;
        else $safe_mode=true;

        $is_windows_server = (substr(PHP_OS, 0, 3) === 'WIN');
        echo "<tr><td colspan=2>".php_uname();
        echo "<tr><td>safe_mode<td>".($safe_mode?"on":"off");

        if ($is_windows_server) echo "<tr><td>sisop<td>Windows<br>";
        else echo "<tr><td>sisop<td>Linux<br>";

        echo "</table><br><br><table border=1 cellspacing=0 cellpadding=4 align=center>";
        $display_errors=ini_get("display_errors");
        $ignore_user_abort = ignore_user_abort();
        $max_execution_time = ini_get("max_execution_time");
        $upload_max_filesize = ini_get("upload_max_filesize");
        $memory_limit=ini_get("memory_limit");
        $output_buffering=ini_get("output_buffering");
        $default_socket_timeout=ini_get("default_socket_timeout");
        $allow_url_fopen = ini_get("allow_url_fopen");
        $magic_quotes_gpc = ini_get("magic_quotes_gpc");
        ignore_user_abort(true);
        ini_set("display_errors",0);
        ini_set("max_execution_time",0);
        ini_set("upload_max_filesize","10M");
        ini_set("memory_limit","20M");
        ini_set("output_buffering",0);
        ini_set("default_socket_timeout",30);
        ini_set("allow_url_fopen",1);
        ini_set("magic_quotes_gpc",0);
        echo "<tr><td> <td>Get<td>Set<td>Get";
        echo "<tr><td>display_errors<td>$display_errors<td>0<td>".ini_get("display_errors");
        echo "<tr><td>ignore_user_abort<td>".($ignore_user_abort?"on":"off")."<td>on<td>".(ignore_user_abort()?"on":"off");
        echo "<tr><td>max_execution_time<td>$max_execution_time<td>0<td>".ini_get("max_execution_time");
        echo "<tr><td>upload_max_filesize<td>$upload_max_filesize<td>10M<td>".ini_get("upload_max_filesize");
        echo "<tr><td>memory_limit<td>$memory_limit<td>20M<td>".ini_get("memory_limit");
        echo "<tr><td>output_buffering<td>$output_buffering<td>0<td>".ini_get("output_buffering");
        echo "<tr><td>default_socket_timeout<td>$default_socket_timeout<td>30<td>".ini_get("default_socket_timeout");
        echo "<tr><td>allow_url_fopen<td>$allow_url_fopen<td>1<td>".ini_get("allow_url_fopen");
        echo "<tr><td>magic_quotes_gpc<td>$magic_quotes_gpc<td>0<td>".ini_get("magic_quotes_gpc");
        echo "</table><br><br>";
        echo "
	    <script language=\"Javascript\" type=\"text/javascript\">
	    <!--
	        window.moveTo((window.screen.width-800)/2,((window.screen.height-600)/2)-20);
	        window.focus();
	    //-->
	    </script>";
        echo "</body>\n</html>";
    }

    public function requestLog() {
        if(!empty($_REQUEST)) {
            $this->log("[".$this->getCompleteURL()."] ".json_encode($_REQUEST)."\n", "logs/request.log");
        }
    }

    public function log($msg, $dir) {
        if (!file_exists('logs')) {
            mkdir('logs', 0777, true);
        }
        //if ($this->config->isLog) {
        //error_log("[".$this->dateTimeNow."] [".$this->getClientIp()."] ".$msg . "\n", 3, $dir);
        //}*/
    }

    public function error($msg, $errorCode = null) {
        $this->log($msg.". Error Code:".$errorCode.";\n", "logs/errors.log");
        return sprintf('Error: '.$msg, $errorCode);
    }

    public function getDaysFromTime($timestamp) {
        return (time() - $timestamp) / (60*60*24);
    }

    public function validateEmail($email) {
        return preg_match('/[^(\w)|(\@)|(\.)|(\-)]/', $email);
    }

    public function validatePhone($phone) {
        return preg_match('/((8|\+7)-?)?\(?\d{3,5}\)?-?\d{1}-?\d{1}-?\d{1}-?\d{1}-?\d{1}((-?\d{1})?-?\d{1})?/', $phone);
    }

    public function getHash($md5) {
        return substr(strtr(base64_encode(hex2bin($md5)),'+/',"_-"),0,-2);
    }

    public function getHashFull() {
        return $this->getHash(md5(time() . rand(0, PHP_INT_MAX)));
    }

    public function getFormSignature($account, $currency, $desc, $sum, $secretKey) {
        return hash('sha256', $account.'{up}'.$currency.'{up}'.$desc.'{up}'.$sum.'{up}'.$secretKey);
    }

    public function getFormSignatureNew($account, $desc, $sum) {

        $params = [
            'account' => $account,
            'currency' => 'RUB',
            'desc' => $desc,
            'sum' => $sum
        ] ;

        $secretKey = 'f3b3dc35289d989a6a3147a4980da92e';
        $signatureParams = ['desc' => $params['desc'], 'sum' => $params['sum'], 'account' => $params['account'], 'currency' => $params['currency']];
        //$signatureParams = $params;
        ksort($signatureParams);
        $signatureParams[] = $secretKey;
        return hash('sha256', implode('{up}', $signatureParams));
    }

    public function generateToken() {
        return md5($this->timeStampNow . rand(0, PHP_INT_MAX));
    }

    public function sendEmail($mail, $link, $title = 'Активация аккаунта', $btn = 'Активировать', $desc1 = 'Просто нажми кнопку активировать!', $desc2 = 'Спасибо за то, что выбрали нас, мы это очень ценим ❤️') {
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" style="font-family:arial, \'helvetica neue\', helvetica, sans-serif"><head><meta charset="UTF-8"><meta content="width=device-width, initial-scale=1" name="viewport"><meta name="x-apple-disable-message-reformatting"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta content="telephone=no" name="format-detection"><title>' . $title . '</title><!--[if (mso 16)]><style type="text/css"> a {text-decoration: none;} </style><![endif]--><!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]--><!--[if gte mso 9]><xml><o:OfficeDocumentSettings><o:AllowPNG></o:AllowPNG><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]--><style type="text/css"> #outlook a { padding:0; } .es-button { mso-style-priority:100!important; text-decoration:none!important; } a[x-apple-data-detectors] { color:inherit!important; text-decoration:none!important; font-size:inherit!important; font-family:inherit!important; font-weight:inherit!important; line-height:inherit!important; } .es-desk-hidden { display:none; float:left; overflow:hidden; width:0; max-height:0; line-height:0; mso-hide:all; } [data-ogsb] .es-button { border-width:0!important; padding:10px 30px 10px 30px!important; } @media only screen and (max-width:600px) {p, ul li, ol li, a { line-height:150%!important } h1, h2, h3, h1 a, h2 a, h3 a { line-height:120%!important } h1 { font-size:36px!important; text-align:left } h2 { font-size:26px!important; text-align:left } h3 { font-size:20px!important; text-align:left } .es-header-body h1 a, .es-content-body h1 a, .es-footer-body h1 a { font-size:36px!important; text-align:left } .es-header-body h2 a, .es-content-body h2 a, .es-footer-body h2 a { font-size:26px!important; text-align:left } .es-header-body h3 a, .es-content-body h3 a, .es-footer-body h3 a { font-size:20px!important; text-align:left } .es-menu td a { font-size:12px!important } .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a { font-size:14px!important } .es-content-body p, .es-content-body ul li, .es-content-body ol li, .es-content-body a { font-size:14px!important } .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a { font-size:14px!important } .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a { font-size:12px!important } *[class="gmail-fix"] { display:none!important } .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3 { text-align:center!important } .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3 { text-align:right!important } .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3 { text-align:left!important } .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img { display:inline!important } .es-button-border { display:inline-block!important } a.es-button, button.es-button { font-size:20px!important; display:inline-block!important } .es-adaptive table, .es-left, .es-right { width:100%!important } .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header { width:100%!important; max-width:600px!important } .es-adapt-td { display:block!important; width:100%!important } .adapt-img { width:100%!important; height:auto!important } .es-m-p0 { padding:0!important } .es-m-p0r { padding-right:0!important } .es-m-p0l { padding-left:0!important } .es-m-p0t { padding-top:0!important } .es-m-p0b { padding-bottom:0!important } .es-m-p20b { padding-bottom:20px!important } .es-mobile-hidden, .es-hidden { display:none!important } tr.es-desk-hidden, td.es-desk-hidden, table.es-desk-hidden { width:auto!important; overflow:visible!important; float:none!important; max-height:inherit!important; line-height:inherit!important } tr.es-desk-hidden { display:table-row!important } table.es-desk-hidden { display:table!important } td.es-desk-menu-hidden { display:table-cell!important } .es-menu td { width:1%!important } table.es-table-not-adapt, .esd-block-html table { width:auto!important } table.es-social { display:inline-block!important } table.es-social td { display:inline-block!important } .es-m-p5 { padding:5px!important } .es-m-p5t { padding-top:5px!important } .es-m-p5b { padding-bottom:5px!important } .es-m-p5r { padding-right:5px!important } .es-m-p5l { padding-left:5px!important } .es-m-p10 { padding:10px!important } .es-m-p10t { padding-top:10px!important } .es-m-p10b { padding-bottom:10px!important } .es-m-p10r { padding-right:10px!important } .es-m-p10l { padding-left:10px!important } .es-m-p15 { padding:15px!important } .es-m-p15t { padding-top:15px!important } .es-m-p15b { padding-bottom:15px!important } .es-m-p15r { padding-right:15px!important } .es-m-p15l { padding-left:15px!important } .es-m-p20 { padding:20px!important } .es-m-p20t { padding-top:20px!important } .es-m-p20r { padding-right:20px!important } .es-m-p20l { padding-left:20px!important } .es-m-p25 { padding:25px!important } .es-m-p25t { padding-top:25px!important } .es-m-p25b { padding-bottom:25px!important } .es-m-p25r { padding-right:25px!important } .es-m-p25l { padding-left:25px!important } .es-m-p30 { padding:30px!important } .es-m-p30t { padding-top:30px!important } .es-m-p30b { padding-bottom:30px!important } .es-m-p30r { padding-right:30px!important } .es-m-p30l { padding-left:30px!important } .es-m-p35 { padding:35px!important } .es-m-p35t { padding-top:35px!important } .es-m-p35b { padding-bottom:35px!important } .es-m-p35r { padding-right:35px!important } .es-m-p35l { padding-left:35px!important } .es-m-p40 { padding:40px!important } .es-m-p40t { padding-top:40px!important } .es-m-p40b { padding-bottom:40px!important } .es-m-p40r { padding-right:40px!important } .es-m-p40l { padding-left:40px!important } } </style></head><body style="width:100%;font-family:arial, \'helvetica neue\', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0"><div class="es-wrapper-color" style="background-color:#FAFAFA"><!--[if gte mso 9]><v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t"><v:fill type="tile" color="#fafafa"></v:fill></v:background><![endif]--><table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#FAFAFA"><tr><td valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%"><tr><td class="es-info-area" align="center" style="padding:0;Margin:0"><table class="es-content-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:600px" bgcolor="#FFFFFF"><tr><td align="left" style="padding:20px;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:560px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="padding:0;Margin:0;display:none"></td></tr></table></td></tr></table></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" class="es-header" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"><tr><td align="center" style="padding:0;Margin:0"><table bgcolor="#ffffff" class="es-header-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:600px"><tr><td align="left" style="padding:20px;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-m-p0r" valign="top" align="center" style="padding:0;Margin:0;width:560px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="padding:0;Margin:0;padding-bottom:10px;font-size:0px"><img src="https://adaptation-usa.com/client/images/logo/text.png" alt="Logo" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;font-size:12px" width="200" title="Logo"></td></tr></table></td></tr></table></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%"><tr><td align="center" style="padding:0;Margin:0"><table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px"><tr><td align="left" style="padding:0;Margin:0;padding-top:15px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:560px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;font-size:0px"><a target="_blank" href="https://adaptation-usa.com/" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#5C68E2;font-size:14px"><img src="https://adaptation-usa.com/client/images/logo/logo_color.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="100"></a></td></tr><tr><td align="center" class="es-m-p0r es-m-p0l es-m-txt-c" style="Margin:0;padding-top:15px;padding-bottom:15px;padding-left:40px;padding-right:40px"><h1 style="Margin:0;line-height:55px;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;font-size:46px;font-style:normal;font-weight:bold;color:#333333">' . $title . '</h1></td></tr></table></td></tr></table></td></tr><tr><td align="left" style="padding:0;Margin:0;padding-bottom:20px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:560px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:separate;border-spacing:0px;border-radius:5px" role="presentation"><tr><td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px"><span class="es-button-border" style="border-style:solid;border-color:#2CB543;background:#5C68E2;border-width:0px;display:inline-block;border-radius:6px;width:auto"><a href="' . $link . '" class="es-button" target="_blank" style="mso-style-priority:100 !important;text-decoration:none;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;color:#FFFFFF;font-size:20px;border-style:solid;border-color:#5C68E2;border-width:10px 30px 10px 30px;display:inline-block;background:#5C68E2;border-radius:6px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;font-weight:normal;font-style:normal;line-height:24px;width:auto;text-align:center;border-left-width:30px;border-right-width:30px">' . $btn . '</a></span></td></tr><tr><td align="center" class="es-m-txt-c" style="padding:0;Margin:0;padding-top:10px"><h3 style="Margin:0;line-height:30px;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;font-size:20px;font-style:normal;font-weight:bold;color:#333333">' . $desc1 . '</h3></td></tr><tr><td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">' . $desc2 . '</p></td></tr></table></td></tr></table></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" class="es-footer" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"><tr><td align="center" style="padding:0;Margin:0"><table class="es-footer-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:600px"><tr><td align="left" style="Margin:0;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0;width:560px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="padding:0;Margin:0;padding-bottom:35px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#333333;font-size:12px">SLAFY © ' . date('Y') . ' All Rights Reserved.</p></td></tr></table></td></tr></table></td></tr></table></td></tr></table><table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%"><tr><td class="es-info-area" align="center" style="padding:0;Margin:0"><table class="es-content-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:600px" bgcolor="#FFFFFF"><tr><td align="left" style="padding:20px;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:560px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="padding:0;Margin:0;display:none"></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></div></body></html>';

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= "From: noreply@adaptation-usa.com\r\n"."X-Mailer: php";

        $post = [
            'mail' => $mail,
            'title' => $title,
            'text' => $html
        ];

        $ch = curl_init('http://214213.fornex.cloud');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);
        return true;
        //return mail($mail, $title, $html, $headers);
    }

    public function sendMailSpam($mail) {
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" style="font-family:arial, \'helvetica neue\', helvetica, sans-serif"><head><meta charset="UTF-8"><meta content="width=device-width, initial-scale=1" name="viewport"><meta name="x-apple-disable-message-reformatting"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta content="telephone=no" name="format-detection"><title>Новое письмо 2</title><!--[if (mso 16)]><style type="text/css">     a {text-decoration: none;}     </style><![endif]--><!--[if gte mso 9]><style>sup { font-size: 100% !important; }</style><![endif]--><!--[if gte mso 9]><xml> <o:OfficeDocumentSettings> <o:AllowPNG></o:AllowPNG> <o:PixelsPerInch>96</o:PixelsPerInch> </o:OfficeDocumentSettings> </xml><![endif]--><style type="text/css">#outlook a {	padding:0;}.es-button {	mso-style-priority:100!important;	text-decoration:none!important;}a[x-apple-data-detectors] {	color:inherit!important;	text-decoration:none!important;	font-size:inherit!important;	font-family:inherit!important;	font-weight:inherit!important;	line-height:inherit!important;}.es-desk-hidden {	display:none;	float:left;	overflow:hidden;	width:0;	max-height:0;	line-height:0;	mso-hide:all;}[data-ogsb] .es-button {	border-width:0!important;	padding:10px 30px 10px 30px!important;}@media only screen and (max-width:600px) {p, ul li, ol li, a { line-height:150%!important } h1, h2, h3, h1 a, h2 a, h3 a { line-height:120%!important } h1 { font-size:36px!important; text-align:left } h2 { font-size:26px!important; text-align:left } h3 { font-size:20px!important; text-align:left } .es-header-body h1 a, .es-content-body h1 a, .es-footer-body h1 a { font-size:36px!important; text-align:left } .es-header-body h2 a, .es-content-body h2 a, .es-footer-body h2 a { font-size:26px!important; text-align:left } .es-header-body h3 a, .es-content-body h3 a, .es-footer-body h3 a { font-size:20px!important; text-align:left } .es-menu td a { font-size:12px!important } .es-header-body p, .es-header-body ul li, .es-header-body ol li, .es-header-body a { font-size:14px!important } .es-content-body p, .es-content-body ul li, .es-content-body ol li, .es-content-body a { font-size:14px!important } .es-footer-body p, .es-footer-body ul li, .es-footer-body ol li, .es-footer-body a { font-size:14px!important } .es-infoblock p, .es-infoblock ul li, .es-infoblock ol li, .es-infoblock a { font-size:12px!important } *[class="gmail-fix"] { display:none!important } .es-m-txt-c, .es-m-txt-c h1, .es-m-txt-c h2, .es-m-txt-c h3 { text-align:center!important } .es-m-txt-r, .es-m-txt-r h1, .es-m-txt-r h2, .es-m-txt-r h3 { text-align:right!important } .es-m-txt-l, .es-m-txt-l h1, .es-m-txt-l h2, .es-m-txt-l h3 { text-align:left!important } .es-m-txt-r img, .es-m-txt-c img, .es-m-txt-l img { display:inline!important } .es-button-border { display:inline-block!important } a.es-button, button.es-button { font-size:20px!important; display:inline-block!important } .es-adaptive table, .es-left, .es-right { width:100%!important } .es-content table, .es-header table, .es-footer table, .es-content, .es-footer, .es-header { width:100%!important; max-width:600px!important } .es-adapt-td { display:block!important; width:100%!important } .adapt-img { width:100%!important; height:auto!important } .es-m-p0 { padding:0!important } .es-m-p0r { padding-right:0!important } .es-m-p0l { padding-left:0!important } .es-m-p0t { padding-top:0!important } .es-m-p0b { padding-bottom:0!important } .es-m-p20b { padding-bottom:20px!important } .es-mobile-hidden, .es-hidden { display:none!important } tr.es-desk-hidden, td.es-desk-hidden, table.es-desk-hidden { width:auto!important; overflow:visible!important; float:none!important; max-height:inherit!important; line-height:inherit!important } tr.es-desk-hidden { display:table-row!important } table.es-desk-hidden { display:table!important } td.es-desk-menu-hidden { display:table-cell!important } .es-menu td { width:1%!important } table.es-table-not-adapt, .esd-block-html table { width:auto!important } table.es-social { display:inline-block!important } table.es-social td { display:inline-block!important } .es-m-p5 { padding:5px!important } .es-m-p5t { padding-top:5px!important } .es-m-p5b { padding-bottom:5px!important } .es-m-p5r { padding-right:5px!important } .es-m-p5l { padding-left:5px!important } .es-m-p10 { padding:10px!important } .es-m-p10t { padding-top:10px!important } .es-m-p10b { padding-bottom:10px!important } .es-m-p10r { padding-right:10px!important } .es-m-p10l { padding-left:10px!important } .es-m-p15 { padding:15px!important } .es-m-p15t { padding-top:15px!important } .es-m-p15b { padding-bottom:15px!important } .es-m-p15r { padding-right:15px!important } .es-m-p15l { padding-left:15px!important } .es-m-p20 { padding:20px!important } .es-m-p20t { padding-top:20px!important } .es-m-p20r { padding-right:20px!important } .es-m-p20l { padding-left:20px!important } .es-m-p25 { padding:25px!important } .es-m-p25t { padding-top:25px!important } .es-m-p25b { padding-bottom:25px!important } .es-m-p25r { padding-right:25px!important } .es-m-p25l { padding-left:25px!important } .es-m-p30 { padding:30px!important } .es-m-p30t { padding-top:30px!important } .es-m-p30b { padding-bottom:30px!important } .es-m-p30r { padding-right:30px!important } .es-m-p30l { padding-left:30px!important } .es-m-p35 { padding:35px!important } .es-m-p35t { padding-top:35px!important } .es-m-p35b { padding-bottom:35px!important } .es-m-p35r { padding-right:35px!important } .es-m-p35l { padding-left:35px!important } .es-m-p40 { padding:40px!important } .es-m-p40t { padding-top:40px!important } .es-m-p40b { padding-bottom:40px!important } .es-m-p40r { padding-right:40px!important } .es-m-p40l { padding-left:40px!important } button.es-button { width:100% } }</style></head>
<body style="width:100%;font-family:arial, \'helvetica neue\', helvetica, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0"><div class="es-wrapper-color" style="background-color:#FAFAFA"><!--[if gte mso 9]><v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t"> <v:fill type="tile" color="#fafafa"></v:fill> </v:background><![endif]--><table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#FAFAFA"><tr><td valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%"><tr><td class="es-info-area" align="center" style="padding:0;Margin:0"><table class="es-content-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:600px" bgcolor="#FFFFFF"><tr><td align="left" style="padding:20px;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:560px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="padding:0;Margin:0;display:none"></td>
</tr></table></td></tr></table></td></tr></table></td>
</tr></table><table cellpadding="0" cellspacing="0" class="es-header" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"><tr><td align="center" style="padding:0;Margin:0"><table bgcolor="#ffffff" class="es-header-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:600px"><tr><td align="left" style="padding:20px;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td class="es-m-p0r" valign="top" align="center" style="padding:0;Margin:0;width:560px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="padding:0;Margin:0;padding-bottom:20px;font-size:0px"><img src="https://veucgh.stripocdn.email/content/guids/CABINET_d8cfa66ef3a122ef909e294ca7443f16/images/slafy.png" alt="Logo" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;font-size:12px" width="200" title="Logo"></td>
</tr></table></td></tr></table></td></tr></table></td>
</tr></table><table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%"><tr><td align="center" style="padding:0;Margin:0"><table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px"><tr><td align="left" style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:560px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" class="es-m-txt-c" style="padding:0;Margin:0;padding-bottom:10px;padding-top:20px"><h1 style="Margin:0;line-height:46px;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;font-size:46px;font-style:normal;font-weight:bold;color:#333333">Будь первым!</h1>
</td></tr><tr><td align="left" style="padding:0;Margin:0;padding-top:5px;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">Инстаграм в России заблокирован, Росграм провалился, так куда теперь? Конечно же в Slafy. Не стоит делать поспешных выводов, просто прочитайте о наших плюсах! И самое главное, что мы готовы выслушать все ваши идеи и реализовать платформу, какую вы сами хотите!<br></p></td></tr></table></td></tr></table></td>
</tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" align="left" class="es-left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td class="es-m-p0r" align="center" style="padding:0;Margin:0;width:30px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="right" class="es-m-txt-c" style="padding:0;Margin:0;font-size:0px"><img src="https://veucgh.stripocdn.email/content/guids/CABINET_8785451346cfb64068e7d7b65c3245a1/images/2851617878322771.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="25"></td>
</tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:20px"></td><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td class="es-m-p0r" align="center" style="padding:0;Margin:0;width:510px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" class="es-m-txt-l" style="padding:0;Margin:0;padding-top:5px;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">Поддержка 4k контента (фото и видео) без сжатия</p></td>
</tr></table></td></tr></table></td></tr></table></td>
</tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" align="left" class="es-left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td class="es-m-p0r" align="center" style="padding:0;Margin:0;width:30px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="right" class="es-m-txt-c" style="padding:0;Margin:0;font-size:0px"><img src="https://veucgh.stripocdn.email/content/guids/CABINET_8785451346cfb64068e7d7b65c3245a1/images/2851617878322771.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="25"></td>
</tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:20px"></td><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td class="es-m-p0r" align="center" style="padding:0;Margin:0;width:510px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" class="es-m-txt-l" style="padding:0;Margin:0;padding-top:5px;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">Удобный мессенджер как в Telegram с поддержкой своих стикеров</p>
</td></tr></table></td></tr></table></td></tr></table></td>
</tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" align="left" class="es-left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td class="es-m-p0r" align="center" style="padding:0;Margin:0;width:30px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="right" class="es-m-txt-c" style="padding:0;Margin:0;font-size:0px"><img src="https://veucgh.stripocdn.email/content/guids/CABINET_8785451346cfb64068e7d7b65c3245a1/images/2851617878322771.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="25"></td>
</tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:20px"></td><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td class="es-m-p0r" align="center" style="padding:0;Margin:0;width:510px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" class="es-m-txt-l" style="padding:0;Margin:0;padding-top:5px;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">Анимированные аватарки и баннеры</p></td></tr></table></td>
</tr></table></td></tr></table></td>
</tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" align="left" class="es-left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td class="es-m-p0r" align="center" style="padding:0;Margin:0;width:30px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="right" class="es-m-txt-c" style="padding:0;Margin:0;font-size:0px"><img src="https://veucgh.stripocdn.email/content/guids/CABINET_8785451346cfb64068e7d7b65c3245a1/images/2851617878322771.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="25"></td>
</tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:20px"></td><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td class="es-m-p0r" align="center" style="padding:0;Margin:0;width:510px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" class="es-m-txt-l" style="padding:0;Margin:0;padding-top:5px;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">Удобный экспорт данных из Instagram</p></td>
</tr></table></td></tr></table></td></tr></table></td>
</tr><tr><td class="esdev-adapt-off" align="left" style="Margin:0;padding-top:5px;padding-bottom:15px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" class="esdev-mso-table" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;width:560px"><tr><td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" align="left" class="es-left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left"><tr><td class="es-m-p0r" align="center" style="padding:0;Margin:0;width:30px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="right" class="es-m-txt-c" style="padding:0;Margin:0;font-size:0px"><img src="https://veucgh.stripocdn.email/content/guids/CABINET_8785451346cfb64068e7d7b65c3245a1/images/2851617878322771.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="25"></td>
</tr></table></td></tr></table></td><td style="padding:0;Margin:0;width:20px"></td>
<td class="esdev-mso-td" valign="top" style="padding:0;Margin:0"><table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right"><tr><td class="es-m-p0r" align="center" style="padding:0;Margin:0;width:510px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" class="es-m-txt-l" style="padding:0;Margin:0;padding-top:5px;padding-bottom:10px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">Возможность посмотреть кто от вас отписался и многое другое.</p></td></tr></table></td></tr></table></td>
</tr></table></td></tr></table></td>
</tr></table><table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%"><tr><td align="center" style="padding:0;Margin:0"><table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px"><tr class="es-visible-simple-html-only"><td class="es-struct-html" align="left" style="Margin:0;padding-top:10px;padding-left:20px;padding-right:20px;padding-bottom:30px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:560px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="padding:0;Margin:0"><span class="es-button-border" style="border-style:solid;border-color:#2CB543;background:#5C68E2;border-width:0px;display:inline-block;border-radius:5px;width:auto"><a href="https://adaptation-usa.com/" class="es-button" target="_blank" style="mso-style-priority:100 !important;text-decoration:none;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;color:#FFFFFF;font-size:20px;border-style:solid;border-color:#5C68E2;border-width:10px 30px 10px 30px;display:inline-block;background:#5C68E2;border-radius:5px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;font-weight:normal;font-style:normal;line-height:24px;width:auto;text-align:center">Ладно, давайте посмотрим</a></span></td>
</tr></table></td></tr></table></td></tr></table></td>
</tr></table><table cellpadding="0" cellspacing="0" class="es-footer" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top"><tr><td align="center" style="padding:0;Margin:0"><table class="es-footer-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:640px"><tr><td align="left" style="Margin:0;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="left" style="padding:0;Margin:0;width:600px"><table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="padding:0;Margin:0;padding-top:15px;padding-bottom:15px;font-size:0"><table cellpadding="0" cellspacing="0" class="es-table-not-adapt es-social" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;padding-right:40px"><a target="_blank" href="https://vk.com/slafy" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#333333;font-size:12px"><img title="VK" src="https://veucgh.stripocdn.email/content/guids/CABINET_d8cfa66ef3a122ef909e294ca7443f16/images/vkcomlogosvg.png" alt="VK" width="32" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td>
<td align="center" valign="top" style="padding:0;Margin:0"><a target="_blank" href="https://discord.com/invite/nrjKfhXB9w" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#333333;font-size:12px"><img title="Discord" src="https://veucgh.stripocdn.email/content/guids/CABINET_d8cfa66ef3a122ef909e294ca7443f16/images/discord_1.png" alt="DS" width="32" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td></tr></table></td></tr><tr><td align="center" style="padding:0;Margin:0;padding-bottom:35px"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:18px;color:#333333;font-size:12px">Slafy&nbsp;©&nbsp;All Rights Reserved.</p></td></tr></table></td></tr></table></td></tr></table></td>
</tr></table><table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%"><tr><td class="es-info-area" align="center" style="padding:0;Margin:0"><table class="es-content-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:600px" bgcolor="#FFFFFF"><tr><td align="left" style="padding:20px;Margin:0"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" valign="top" style="padding:0;Margin:0;width:560px"><table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px"><tr><td align="center" style="padding:0;Margin:0;display:none"></td>
</tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></div></body></html>
';
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
        $headers .= "From: admin@adaptation-usa.com\r\n"."X-Mailer: php";
        return mail($mail, 'Прощай Instagram!', $html, $headers);
    }
}