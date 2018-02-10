<?php
function get_web_page( $url ) {
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 10,      // timeout on connect
        CURLOPT_TIMEOUT        => 10,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;
    return $header;
}

$page = get_web_page('http://oldschool.runescape.com/oldschool_index');
if ($page['http_code'] == 200) {
    preg_match( '/There are currently (\d+) people playing!/', $page['content'], $matches );
    $oldCount = $matches[1];
    echo $oldCount . PHP_EOL;
} else {
    die($page['errno'] != 0 ? $page['errmsg'] . "-" . $page['http_code'] : $page['http_code'] );
}

$page = get_web_page('http://www.runescape.com/player_count.js?varname=iPlayerCount&callback=jQuery1720483994532970072_1361571418268&_=1361571439230');
if ($page['http_code'] == 200) {
    preg_match( '/jQuery1720483994532970072_1361571418268\((\d+)\);/', $page['content'], $matches );
    $rsCount = $matches[1] - $oldCount;
    echo $rsCount . PHP_EOL;
} else {
    die($page['errno'] != 0 ? $page['errmsg'] . "-" . $page['http_code'] : $page['http_code'] );
}

$totalCount = $rsCount + $oldCount;

echo $totalCount . PHP_EOL;

$mysqli = new mysqli('localhost', 'root', '', '07tracker');

if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

echo "INSERT INTO `player_counter` (`time`, `count`, `rs_count`, `total_count`) VALUES ('".time()."', {$oldCount}, {$rsCount}, {$totalCount});";

$mysqli->query("INSERT INTO `player_counter` (`time`, `count`, `rs_count`, `total_count`) VALUES ('".time()."', {$oldCount}, {$rsCount}, {$totalCount});") or die($mysqli->error);
?>
