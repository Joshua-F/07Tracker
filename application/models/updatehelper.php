<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Updatehelper extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->helper( 'tracker' );
    }

    /**
     * Cron job that is ran every day at 12pm
     */
    public function updateUsers() {
        $yesterday = strtotime( 'yesterday 00:00:00' );
        $today     = strtotime( 'today 00:00:00' );
        $query     = $this->db->query( "SELECT `id`, `username` FROM `users` ORDER BY `id` ASC" );
        foreach ( $query->result() as $row ) {
            $page = get_web_page( "http://services.runescape.com/m=hiscore_oldschool/index_lite.ws?player={$row->username}" );
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

                // Update yesterday for the last time
                // But first we need to check to see if there even is a yesterday for this user
                $yesterdayCheck = $this->db->query( "SELECT `id` FROM `day_rank` WHERE `user_id` = ? AND `date` = ?", array( $row->id, $yesterday ) );
                if ( $yesterdayCheck->num_rows() > 0 ) {
                    $yesterdayStats = $this->db->query( "SELECT * FROM `day_stats` WHERE `user_id` = ? AND `date` = ?", array( $row->id, $yesterday ) );
                    $yesterdayRow   = $yesterdayStats->row_array();

                    $differences = array( 'ranks' => array(), 'levels' => array(), 'xp' => array() ); // Stores rank, level and xp differences
                    for ( $i = 0; $i < 24; $i++ ) {
                        $json                    = json_decode( $yesterdayRow['' . $i . ''] );
                        $differences['ranks'][]  = $info[$i]['rank'] - $json->{'rank'};
                        $differences['levels'][] = $info[$i]['level'] - $json->{'level'};
                        $differences['xp'][]     = $info[$i]['xp'] - $json->{'xp'};
                    }
                    $updateStrings = array( 'ranks' => array(), 'levels' => array(), 'xp' => array() ); // store update strings
                    for ( $i = 0; $i < 24; $i++ ) {
                        $updateStrings['ranks'][]  = "`{$i}` = {$differences['ranks'][$i]}";
                        $updateStrings['levels'][] = "`{$i}` = {$differences['levels'][$i]}";
                        $updateStrings['xp'][]     = "`{$i}` = {$differences['xp'][$i]}";
                    }
                    $this->db->query( "UPDATE `day_rank` SET " . implode( ",", $updateStrings['ranks'] ) ." WHERE `user_id` = ? AND `date` = ?", array( $row->id, $yesterday ) );
                    $this->db->query( "UPDATE `day_level` SET " . implode( ",", $updateStrings['levels'] ) ." WHERE `user_id` = ? AND `date` = ?", array( $row->id, $yesterday ) );
                    $this->db->query( "UPDATE `day_xp` SET " . implode( ",", $updateStrings['xp'] ) ." WHERE `user_id` = ? AND `date` = ?", array( $row->id, $yesterday ) );
                }

                // Insert new day starting stats
                $insertString = "";
                for ( $i=0; $i < count( $info ); $i++ ) {
                    $insertString .= "'" . json_encode( $info[$i] ) . "', ";
                }
                $insertString = substr( $insertString, 0, -2 );
                $this->db->query( "INSERT INTO `day_stats` (`user_id`, `date`, `0`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22`, `23`) VALUES ({$row->id}, {$today}, {$insertString})" );
                // Start new day
                $this->db->query( "INSERT INTO `day_rank` (`user_id`, `date`) VALUES (?, ?)", array( $row->id, $today ) );
                $this->db->query( "INSERT INTO `day_level` (`user_id`, `date`) VALUES (?, ?)", array( $row->id, $today ) );
                $this->db->query( "INSERT INTO `day_xp` (`user_id`, `date`) VALUES (?, ?)", array( $row->id, $today ) );
                // Update last checked
                $this->db->query( "UPDATE `users` SET `last_check` = ? WHERE `id` = ?", array( time(), $row->id ) );
            } else {
                $this->db->query( "UPDATE `users` SET `last_check` = ?, `flagged` = 1 WHERE `id` = ?", array( time(), $row->id ) );
            }
        }
    }

    /**
     * Creates a new user
     */
    public function newUser( $user ) {
        $this->load->helper( 'tracker' );
        $today = strtotime( 'today 00:00:00' );
        $page  = get_web_page( "http://services.runescape.com/m=hiscore_oldschool/index_lite.ws?player={$user}" );
        if ( $page['http_code'] != 200 ) {
            if ($page['http_code'] == 404) {
                show_error( "The username \"{$user}\" is either not a member or does not exist." );
            } else {
                show_error( "There seems to be a problem with the RuneScape hiscores right now and we could not fetch the information needed. Please try again later." );
            }
        } else {
            $user  = strtolower( str_replace( '+', ' ', $user ) );
            // The user is a member so lets create a new user
            $query = $this->db->query( "SELECT `id` FROM `users` WHERE `username` = ?", array( $user ) );
            if ( $query->num_rows() > 0 ) {
                redirect( "/track/" . $user, 'location' );
            }
            $this->db->query( "INSERT INTO `users` (`username`, `date`, `last_check`) VALUES (?, ?, ?)", array( $user, time(), time() ) );
            $userid = $this->db->insert_id();
            $this->db->query( "INSERT INTO `day_rank` (`user_id`, `date`) VALUES (?, ?)", array( $userid, $today ) );
            $this->db->query( "INSERT INTO `day_level` (`user_id`, `date`) VALUES (?, ?)", array( $userid, $today ) );
            $this->db->query( "INSERT INTO `day_xp` (`user_id`, `date`) VALUES (?, ?)", array( $userid, $today ) );
            // Set the current stats
            // Lets setup the skill info pulled from the url..
            $skills = explode( "\n", $page['content'] );
            $info   = array(); // Stores ranks, levels and xp

            for ( $i = 0; $i < 24; $i++ ) {
                $skillBits  = explode( ",", $skills[$i] );
                $info[] = array(
                    'rank'  => $skillBits[0],
                    'level' => $skillBits[1],
                    'xp'    => $skillBits[2]
                );
            }
            $insertString = "";
            for ( $i=0; $i < count( $info ); $i++ ) {
                $insertString .= "'" . json_encode( $info[$i] ) . "', ";
            }
            $insertString = substr( $insertString, 0, -2 );
            $this->db->query( "INSERT INTO `day_stats` (`user_id`, `date`, `0`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `15`, `16`, `17`, `18`, `19`, `20`, `21`, `22`, `23`) VALUES ({$userid}, {$today}, {$insertString})" );
            redirect( "/track/" . $user, 'location' );
        }
    }

    public function updateUser( $user='' ) {
        $today  = strtotime( 'today 00:00:00' );
        $page  = get_web_page( "http://services.runescape.com/m=hiscore_oldschool/index_lite.ws?player={$user}" );
        if ( $page['http_code'] != 200 ) {
            if ($page['http_code'] == 404) {
                show_error( "The username \"{$user}\" is no longer a member and can not be tracked." );
            } else {
                show_error( "There seems to be a problem with the RuneScape hiscores right now and we could not fetch the information needed. Please try again later. ({$page['http_code']})" );
            }
        } else {
            $user   = strtolower( str_replace( '+', ' ', $user ) );
            $userid = $this->getIdByUsername( $user );
            $query  = $this->db->query( "SELECT `id` FROM `users` WHERE `username` = ?", array( $user ) );
            if ( $query->num_rows() == 0 ) {
                redirect( "/track/" . ucwords($user), 'location' );
            }

            $todayStats = $this->db->query( "SELECT * FROM `day_stats` WHERE `user_id` = ? AND `date` = ?", array( $userid, $today ) );
            if ( $todayStats->num_rows() > 0 ) {
                $todayRow = $todayStats->row_array();

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
                $differences = array( 'ranks' => array(), 'levels' => array(), 'xp' => array() ); // Stores rank, level and xp differences
                for ( $i = 0; $i < 24; $i++ ) {
                    $json                    = json_decode( $todayRow['' . $i . ''] );
                    $differences['ranks'][]  = $info[$i]['rank'] - $json->{'rank'};
                    $differences['levels'][] = $info[$i]['level'] - $json->{'level'};
                    $differences['xp'][]     = $info[$i]['xp'] - $json->{'xp'};
                }
                $updateStrings = array( 'ranks' => array(), 'levels' => array(), 'xp' => array() ); // store update strings
                for ( $i = 0; $i < 24; $i++ ) {
                    $updateStrings['ranks'][]  = "`{$i}` = {$differences['ranks'][$i]}";
                    $updateStrings['levels'][] = "`{$i}` = {$differences['levels'][$i]}";
                    $updateStrings['xp'][]     = "`{$i}` = {$differences['xp'][$i]}";
                }
                $this->db->query( "UPDATE `day_rank` SET " . implode( ",", $updateStrings['ranks'] ) ." WHERE `user_id` = ? AND `date` = ?", array( $userid, $today ) );
                $this->db->query( "UPDATE `day_level` SET " . implode( ",", $updateStrings['levels'] ) ." WHERE `user_id` = ? AND `date` = ?", array( $userid, $today ) );
                $this->db->query( "UPDATE `day_xp` SET " . implode( ",", $updateStrings['xp'] ) ." WHERE `user_id` = ? AND `date` = ?", array( $userid, $today ) );
                $this->db->query( "UPDATE `users` SET `last_check` = ? WHERE `id` = ?", array( time(), $userid ) );
                redirect( '/track/' . ucwords($user), 'location' );
            } else {
                show_error( 'There is a problem with this user, please contact support.' );
            }
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

/* End of file updatehelper.php */
/* Location: ./application/models/updatehelper.php */
