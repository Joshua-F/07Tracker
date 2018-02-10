<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Track extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getSkillInfo() {
        return array(
            "Overall", "Attack", "Defence", "Strength", "Hitpoints",
            "Ranged", "Prayer", "Magic", "Cooking", "Woodcutting",
            "Fletching", "Fishing", "Firemaking", "Crafting", "Smithing",
            "Mining", "Herblore", "Agility", "Thieving", "Slayer",
            "Farming", "Runecraft", "Hunter", "Construction"
        );
    }

    public function fetchUserData( $user='' ) {
        $today  = strtotime( 'today 00:00:00' );
        $userid = $this->getIdByUsername( $user );

        $data = array( 'last_check' => time(), 'ranks' => array(), 'levels' => array(), 'xp' => array(), 'ranksDifferent' => array(), 'levelsDifferent' => array(), 'xpDifferent' => array() );
        if ( !$userid ) {
            return false;
        } else {
            $userQuery      = $this->db->query( "SELECT `last_check` FROM `users` WHERE `id` = ?", array( $userid ) );
            $rankQuery      = $this->db->query( "SELECT `0`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22`, `23` FROM `day_rank` WHERE `user_id` = ? AND `date` = ?", array( $userid, $today ) );
            $levelQuery     = $this->db->query( "SELECT `0`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22`, `23` FROM `day_level` WHERE `user_id` = ? AND `date` = ?", array( $userid, $today ) );
            $xpQuery        = $this->db->query( "SELECT `0`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22`, `23` FROM `day_xp` WHERE `user_id` = ? AND `date` = ?", array( $userid, $today ) );
            $statQuery      = $this->db->query( "SELECT `0`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22`, `23` FROM `day_stats` WHERE `user_id` = ? AND `date` = ?", array( $userid, $today ) );
            if ( $statQuery->num_rows() > 0 ) {
                $data['ranksDifferent']  = $rankQuery->row_array();
                $data['levelsDifferent'] = $levelQuery->row_array();
                $data['xpDifferent']     = $xpQuery->row_array();
                $statRow = $statQuery->row_array();
                for ( $i = 0; $i < 24; $i++ ) {
                    $json = json_decode( $statRow['' . $i . ''] );
                    $data['ranks'][]  = $json->{'rank'} + $data['ranksDifferent'][$i];
                    $data['levels'][] = $json->{'level'} + $data['levelsDifferent'][$i];
                    $data['xp'][]     = $json->{'xp'} + $data['xpDifferent'][$i];
                }
            } else {
                $this->load->helper( 'tracker' );
                $page = get_web_page( "http://services.runescape.com/m=hiscore_oldschool/index_lite.ws?player={$user}" );
                if ( $page['http_code'] == 200 ) {
                    // Lets setup the skill info pulled from the url..
                    $skills = explode( "\n", $page['content'] );
                    $info   = array(); // Stores ranks, levels and xp

                    for ( $i = 0; $i < 24; $i++ ) {
                        $skillBits        = explode( ",", $skills[$i] );
                        $info[] = array(
                            'rank'  => $skillBits[0],
                            'level' => $skillBits[1],
                            'xp'    => $skillBits[2]
                        );
                    }

                    // Insert new day starting stats
                    $insertString = "";
                    for ( $i=0; $i < count( $info ); $i++ ) {
                        $insertString .= "'" . json_encode( $info[$i] ) . "', ";
                    }
                    $insertString = substr( $insertString, 0, -2 );
                    $this->db->query( "INSERT INTO `day_stats` (`user_id`, `date`, `0`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22`, `23`) VALUES ({$userid}, {$today}, {$insertString})" );
                    $this->db->query( "INSERT INTO `day_rank` (`user_id`, `date`) VALUES (?, ?)", array( $userid, $today ) );
                    $this->db->query( "INSERT INTO `day_level` (`user_id`, `date`) VALUES (?, ?)", array( $userid, $today ) );
                    $this->db->query( "INSERT INTO `day_xp` (`user_id`, `date`) VALUES (?, ?)", array( $userid, $today ) );
                    for ( $i = 0 ; $i < 24; $i++ ) {
                        $data['ranksDifferent'][]  = 0;
                        $data['levelsDifferent'][] = 0;
                        $data['xpDifferent'][]     = 0;
                        $data['ranks'][]           = $info[$i]['rank'];
                        $data['levels'][]          = $info[$i]['level'];
                        $data['xp'][]              = $info[$i]['xp'];
                    }
                    $this->db->query( "UPDATE `users` SET `last_check` = ? WHERE `id` = ?", array( time(), $userid ) );
                } else {
                    if ( $page['http_code'] == 404 ) {
                        $data['error'] = 'This account is not a member or doesn\'t exist on the Oldschool Server hiscores. If you need a name change please use the ' . anchor( 'contact', 'contact page' ) . '.';
                    } else {
                        $data['error'] = 'There seems to be a problem with this user and it was not able to be automatically fixed. Try again in a few minutes.';
                    }
                    for ( $i = 0 ; $i < 24; $i++ ) {
                        $data['ranksDifferent'][]  = 0;
                        $data['levelsDifferent'][] = 0;
                        $data['xpDifferent'][]     = 0;
                        $data['ranks'][]           = 0;
                        $data['levels'][]          = 0;
                        $data['xp'][]              = 0;
                    }
                }
            }
            $row                = $userQuery->row(); // Get some info from the users table
            $data['last_check'] = $row->last_check;
            return $data;
        }
    }

    public function fetchUserHistoryData( $user, $date ) {
        $date   = strtotime( str_replace( '-', '/', $date ) );
        $today  = strtotime( 'today 00:00:00' );
        $userid = $this->getIdByUsername( $user );

        if ( $date > $today ) {
            redirect( '/track/' . $user );
            exit;
        }

        $data = array( 'ranks' => array(), 'levels' => array(), 'xp' => array(), 'ranksDifferent' => array(), 'levelsDifferent' => array(), 'xpDifferent' => array() );
        $rankQuery      = $this->db->query( "SELECT `0`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22`, `23` FROM `day_rank` WHERE `user_id` = ? AND `date` = ?", array( $userid, $date ) );
        $levelQuery     = $this->db->query( "SELECT `0`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22`, `23` FROM `day_level` WHERE `user_id` = ? AND `date` = ?", array( $userid, $date ) );
        $xpQuery        = $this->db->query( "SELECT `0`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22`, `23` FROM `day_xp` WHERE `user_id` = ? AND `date` = ?", array( $userid, $date ) );
        $statQuery      = $this->db->query( "SELECT `0`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22`, `23` FROM `day_stats` WHERE `user_id` = ? AND `date` = ?", array( $userid, $date ) );
        if ( $statQuery->num_rows() > 0 ) {
            $data['ranksDifferent']  = $rankQuery->row_array();
            $data['levelsDifferent'] = $levelQuery->row_array();
            $data['xpDifferent']     = $xpQuery->row_array();
            $statRow = $statQuery->row_array();
            for ( $i = 0; $i < 24; $i++ ) {
                $json = json_decode( $statRow['' . $i . ''] );
                $data['ranks'][]  = $json->{'rank'} + $data['ranksDifferent'][$i];
                $data['levels'][] = $json->{'level'} + $data['levelsDifferent'][$i];
                $data['xp'][]     = $json->{'xp'} + $data['xpDifferent'][$i];
            }
        }
        return $data;
    }

    public function fetchUserHistoryDates( $user ) {
        $userid = $this->getIdByUsername( $user );
        if ( !$userid ) {
            return array();
        }

        $query = $this->db->query( "SELECT `date` FROM `day_level` WHERE `user_id` = ? AND `date` != ? GROUP BY `date` ORDER BY `date` DESC", array( $userid, strtotime( 'today 00:00:00' ) ) );
        $dates = array();
        foreach ( $query->result() as $result ) {
            $dates[] = $result->date;
        }
        return $dates;
    }

    public function getGraphData( $user, $skillid=0 ) {
        $today  = strtotime( 'today 00:00:00' );
        $userid = $this->getIdByUsername( $user );

        if ( !$userid ) {
            return array();
        } else {
            $return = array( 'ranks' => array(), 'levels' => array(), 'xp' => array() );
            $rankQuery  = $this->db->query( "SELECT `{$skillid}` FROM `day_rank` WHERE `user_id` = ?", array( $userid ) );
            $levelQuery = $this->db->query( "SELECT `{$skillid}` FROM `day_level` WHERE `user_id` = ?", array( $userid ) );
            $xpQuery    = $this->db->query( "SELECT `{$skillid}` FROM `day_xp` WHERE `user_id` = ?", array( $userid ) );
            $statQuery  = $this->db->query( "SELECT `date`, `{$skillid}` as `stats` FROM `day_stats` WHERE `user_id` = ?", array( $userid ) );
            $count = 0;
            $ranksDifferent  = $rankQuery->result_array();
            $levelsDifferent = $levelQuery->result_array();
            $xpDifferent     = $xpQuery->result_array();
            foreach ( $statQuery->result() as $result ) {
                if ( $count === 0 ) {
                    $return['startDate'] = $result->date;
                }
                $json = json_decode( $result->stats );
                $date = $result->date * 1000;
                $date = "Date.UTC(" . date( 'Y', $result->date ) .", " . ( date( 'n', $result->date ) - 1 ) . ", " . date( 'j', $result->date ) . ")";
                $return['ranks'][] = "[" . $date . ", " . ( $json->{'rank'} + $ranksDifferent[$count][0] ). "]";
                $return['levels'][] = "[" . $date . ", " . ( $json->{'level'} + $levelsDifferent[$count][0] ) . "]";
                $return['xp'][] = "[" . $date . ", " . ( $json->{'xp'} + $xpDifferent[$count][0] ) . "]";
                $count++;
            }
            return $return;
        }
    }

    public function getIdByUsername( $user = '' ) {
        $query = $this->db->query( "SELECT `id` FROM `users` WHERE `username` = ?", array( $user ) );
        if ( $query->num_rows() > 0 ) {
            $result = $query->row_array();
            return $result['id'];
        } else {
            return false;
        }
    }
}

/* End of file track.php */
/* Location: ./application/models/track.php */
