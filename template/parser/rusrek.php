<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

global $methods;


?>
<div class="container" style="padding-top: 80px;">
    <div class="section">
<?php


$context = stream_context_create(
    array(
        "http" => array(
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
        )
    )
);
$dom = new DomDocument();
$res = file_get_contents('https://rusrek.com/mall/job_help_wanted-b381934_0-ru/?PAGEN_1=5', false, $context);

@ $dom->loadHTML($res);

$finder = new DomXPath($dom);
$classname="listinf";
$nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");


foreach ($nodes->item(0)->childNodes as $item) {
    echo '<br><hr>';
    print_r($item->nodeValue ?? '');
    echo '<br><hr>';
    print_r($item ?? '');
    echo '<br><hr>';
}

/*//DOMElement
$table = $dom->getElementById('content');
//DOMNodeList
$child_elements = $table->getElementsByTagName('li');
$row_count = $child_elements->length ;

print_r($child_elements->item(0)->textContent);*/

//echo "No. of rows in the table is " . $row_count;

?>
    </div>
</div>

