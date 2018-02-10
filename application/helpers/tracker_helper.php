<?php
function get_web_page( $url ) {
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "07Tracker", // who am i
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

function colorize( $number, $rank=false ) {
    if ( $rank ) {
        if ( $number < 0 ) {
            $number = $number * -1;
            return '<span style="color: green;">-' . number_format( $number ) . '</span>';
        } else if ( $number >= 1 ) {
            return '<span style="color: red;">+' . number_format( $number ) . '</span>';
        } else {
            return number_format( $number );
        }
    } else {
        if ( $number > 0 ) {
            return '<span style="color: green;">+' . number_format( $number ) . '</span>';
        } else if ( $number < 0 ) {
            return '<span style="color: red;">-' . number_format( $number ) . '</span>';
        } else {
            return number_format( $number );
        }
    }
}

function calculateCombatLevel( $data=array() ) {
    return newFormula($data);
    /*$data['prayer'] = $data['prayer'] % 2 ? $data['prayer'] : $data['prayer'] - 1 ;
    $melee = ( $data['defence'] + $data['hitpoints'] + ( $data['prayer'] / 2 ) + 1.3 * ( $data['attack'] + $data['strength'] ) ) / 4;
    $range = ( $data['defence'] + $data['hitpoints'] + ( $data['prayer'] / 2 ) + 1.3 * floor( 1.5 * $data['range'] ) ) / 4;
    $magic = ( $data['defence'] + $data['hitpoints'] + ( $data['prayer'] / 2 ) + 1.3 * floor( 1.5 * $data['magic'] ) ) / 4;
    return ( max( $melee, $range, $magic ) );*/
}

function newFormula( $levels ) {
    $base = ( $levels['defence'] + $levels['hitpoints'] + floor( 0.5 * $levels['prayer'] ) );
    $melee = ( $base + ( 1.3 * ( $levels['attack'] + $levels['strength'] ) ) ) / 4;
    $magic = ( $base + ( 1.3 * ( 1.5 * $levels['magic'] ) ) ) / 4;
    $range = ( $base + ( 1.3 * ( 1.5 * $levels['range'] ) ) ) / 4;
    return max( $melee, $magic, $range );
}

function formatExperience( $currentLevel, $currentXP, $skillName ) {

    if ( $currentLevel >= 99 || $skillName == "Overall" ) {
        return number_format( $currentXP );
    }

    $levels = array( 0, 83, 174, 276, 388, 512, 650, 801, 969, 1154, 1358, 1584, 1833, 2107, 2411, 2746, 3115, 3523, 3973, 4470, 5018, 5624, 6291, 7028, 7842, 8740, 9730, 10824, 12031, 13363, 14833, 16456, 18247, 20224, 22406, 24815, 27473, 30408, 33648, 37224, 41171, 45529, 50339, 55649, 61512, 67983, 75127, 83014, 91721, 101333, 111945, 123660, 136594, 150872, 166636, 184040, 203254, 224466, 247886, 273742, 302288, 333804, 368599, 407015, 449428, 496254, 547953, 605032, 668051, 737627, 814445, 899257, 992895, 1096278, 1210421, 1336443, 1475581, 1629200, 1798808, 1986068, 2192818, 2421087, 2673114, 2951373, 3258594, 3597792, 3972294, 4385776, 4842295, 5346332, 5902831, 6517253, 7195629, 7944614, 8771558, 9684577, 10692629, 11805606, 13034431 );
    $difference = number_format( $levels[$currentLevel] - $currentXP );
    return "<abbr title=\"{$difference} Experience until " . ( $currentLevel + 1 ) . " {$skillName}\">" . number_format( $currentXP ) . "</abbr>";
}

function getVirturalLevel( $currentXP ) {
    $levels = array( 14391160, 15889109, 17542976, 19368992, 21385073, 23611006, 26068632, 28782069, 31777943, 35085654, 38737661, 42769801, 47221641, 52136869, 57563718, 63555443, 70170840, 77474828, 85539082, 94442737, 104273167, 115126838, 127110260, 140341028, 154948977, 171077457, 188884740, 200000001 );
    for ( $i = 0; $i < count( $levels ); $i++ )
        if ( $currentXP < $levels[$i] )
            return "<abbr title=\"Because this user exceeds level 99 by such a great amount we list their 'virtual level'.\">". ( 99 + $i ) ."</abbr>";
}
