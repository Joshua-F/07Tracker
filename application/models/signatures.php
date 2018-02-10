<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Signatures extends CI_Model {

    private $signatureInfo = array(
        0              => array( 'x' => 31, 'y' => 21, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        2              => array( 'x' => 31, 'y' => 46, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        1              => array( 'x' => 31, 'y' => 69, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        3              => array( 'x' => 31, 'y' => 92, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        4              => array( 'x' => 31, 'y' => 117, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        6              => array( 'x' => 71, 'y' => 21, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        5              => array( 'x' => 71, 'y' => 46, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        8              => array( 'x' => 71, 'y' => 69, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        11             => array( 'x' => 71, 'y' => 92, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        9              => array( 'x' => 71, 'y' => 117, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        14             => array( 'x' => 117, 'y' => 21, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        13             => array( 'x' => 117, 'y' => 46, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        10             => array( 'x' => 117, 'y' => 69, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        7              => array( 'x' => 117, 'y' => 92, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        12             => array( 'x' => 117, 'y' => 117, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        16             => array( 'x' => 160, 'y' => 21, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        17             => array( 'x' => 160, 'y' => 46, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        18             => array( 'x' => 160, 'y' => 69, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        15             => array( 'x' => 160, 'y' => 92, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        19             => array( 'x' => 160, 'y' => 117, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        20             => array( 'x' => 203, 'y' => 21, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        21             => array( 'x' => 203, 'y' => 46, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        22             => array( 'x' => 203, 'y' => 69, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        "Username"     => array( 'x' => 235, 'y' => 25, 'font' => 'trajan_bold.ttf', 'size' => 14 ),
        "Rank"         => array( 'x' => 269, 'y' => 51, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        "Total Lvl"    => array( 'x' => 305, 'y' => 69, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        "Total EXP"    => array( 'x' => 295, 'y' => 87, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        "Combat Level" => array( 'x' => 318, 'y' => 105, 'font' => 'runescape_chat.ttf', 'size' => 12 ),
        "Watermark"    => array( 'x' => 328, 'y' => 124, 'font' => 'runescape_chat.ttf', 'size' => 12 )
    );

    public function __construct() {
        parent::__construct();
    }

    public function showSignature( $username, $style ) {
        if ( !file_exists( './assets/sigs/images/' . $style . '.png' ) ) {
            $this->displayError( $style . " is not a valid style." );
            return;
        }

        $userData = $this->getRuneScapeInfo( $username );

        if ( !$userData ) {
            $this->displayError( "User is not a member or doesn't exist." );
            return;
        }

        header( "Content-type: image/png" );
        echo $this->cacheImage( $username, $style, $userData );
    }

    public function cacheImage( $username, $style, $userData ) {
        $this->load->helper( 'file' );
        $file      = './application/cache/signatures/' .str_replace( "+", "_", strtolower( $username ) ) . '-' . $style . '.png';
        $imageInfo = get_file_info( $file );
        if ( !$imageInfo || ( time() - $imageInfo['date'] ) >= 300 ) {
            $image = imagecreatefrompng( './assets/sigs/images/' . $style . '.png' );
            $white = imagecolorallocate( $image, 255, 255, 255 );
            foreach ( $this->signatureInfo as $key => $info ) {
                if ( $key === "Username" ) {
                    $text = ucwords( str_replace( "+", " ", $username ) );
                } elseif ( $key === "Watermark" ) {
                    $text = "2007HQ.com";
                } elseif ( $key === "Rank" ) {
                    $text = number_format( $userData['overall_rank'] );
                } elseif ( $key === "Total Lvl" ) {
                    $text = number_format( $userData['overall_level'] );
                } elseif ( $key === "Total EXP" ) {
                    $text = number_format( $userData['overall_exp'] );
                } elseif ( $key === "Combat Level" ) {
                    $text = floor( $userData['combat_level'] );
                } else {
                    $text = $userData['levels'][$key];
                }
                imagettftext( $image, $info['size'], 0, $info['x'], $info['y'], $white, './assets/sigs/fonts/' . $info['font'], $text );
            }
            imagepng( $image, $file );
            imagedestroy( $image );
        }
        return read_file( $file );
    }

    public function getRuneScapeInfo( $username ) {
        $this->load->helper( 'tracker' );

        $page = get_web_page( "http://services.runescape.com/m=hiscore_oldschool/index_lite.ws?player={$username}" );
        if ( $page['http_code'] == 200 ) {
            $skills = explode( "\n", $page['content'] );
            $info   = array( 'overall_rank' => 0, 'overall_level' => 0, 'overall_exp' => 0, 'combat_level' => 3, 'levels' => array() );

            for ( $i = 0; $i < 24; $i++ ) {
                $skillBits = explode( ",", $skills[$i] );

                if ( $i == 0 ) {
                    $info['overall_rank']  = $skillBits[0];
                    $info['overall_level'] = $skillBits[1];
                    $info['overall_exp']   = $skillBits[2];
                } else
                    $info['levels'][] = $skillBits[1];
            }

            $combat_levels = array(
                'attack'    => $info['levels'][0],
                'defence'   => $info['levels'][1],
                'strength'  => $info['levels'][2],
                'hitpoints' => $info['levels'][3],
                'range'     => $info['levels'][4],
                'prayer'    => $info['levels'][5],
                'magic'     => $info['levels'][6]
            );
            $info['combat_level'] = calculateCombatLevel( $combat_levels );

            return $info;
        } else {
            return false;
        }
    }

    public function displayError( $error ) {
        $image      = imagecreate( 400, 125 );
        $background = imagecolorallocate( $image, 255, 255, 255 );
        $black      = imagecolorallocate( $image, 0, 0, 0 );
        $fontWidth  = imagefontwidth( 5 );

        imagestring( $image, 5, ( ( 400 - ( strlen( "Error" ) * $fontWidth ) ) / 2 ), 40, "Error", $black );

        imagestring( $image, 5, ( ( 400 - ( strlen( $error ) * $fontWidth ) ) / 2 ), 60, $error, $black );

        imagestring( $image, 5, ( ( 400 - ( strlen( "www.07Tracker.com/signature" ) * $fontWidth ) ) / 2 ), 80, "www.07Tracker.com/signature", $black );

        header( "content-type: image/png" );
        imagepng( $image );
        imagedestroy( $image );
    }

}

/* End of file signatures.php */
/* Location: ./application/models/signatures.php */
