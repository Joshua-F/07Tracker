<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Index extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getTotalUsers() {
        $query = $this->db->query( "SELECT COUNT(`id`) as `total` FROM `users`" );
        $result = $query->row();
        return $result->total;
    }

    public function getTotalXPGained() {
        $query = $this->db->query( "SELECT SUM(`0`) as total FROM `day_xp`" );
        $row = $query->row();
        return $row->total;
    }

    public function getTotalLevelsGained() {
        $query = $this->db->query( "SELECT SUM(`0`) as total FROM `day_level`" );
        $row = $query->row();
        return $row->total;
    }

    public function getTop50( $skillid=0 ) {
        $today = strtotime( 'today 00:00:00' );

        $query = $this->db->query( "SELECT `user_id`, `{$skillid}` FROM `day_xp` WHERE `{$skillid}` > 0 AND `date` = {$today} GROUP BY `user_id` ORDER BY `{$skillid}` DESC LIMIT 0, 50" );
        if ( $query->num_rows() > 0 ) {
            return array( 'skillid' => $skillid, 'data' => $query->result_array() );
        } else {
            return array( 'skillid' => $skillid, 'data' => array() );
        }
    }

    /**
     * Get the daily records
     *
     * @param int     $skillid The skill id to lookup
     * @return array           Array holding all information needed to display on the page
     */
    public function getDailyRecords( $skillid ) {
        $data = array( 'usernames' => array(), 'xp' => array(), 'dates' => array() );
        $query = $this->db->query( "SELECT `user_id`, `date`, `{$skillid}` as `skill` FROM `day_xp` GROUP BY `user_id`, `date` ORDER BY `{$skillid}` DESC LIMIT 0, 30" );
        foreach ( $query->result() as $result ) {
            $data['usernames'][] = $this->getUsernameById( $result->user_id );
            $data['xp'][]        = $result->skill;
            $data['dates'][]     = $result->date;
        }
        return $data;
    }

    /**
     * Get the dates for top 50 over the past x days
     *
     * @param int     $days The amount of days to get
     * @return array        The dates
     */
    public function getTop50HistoryDates( $days ) {
        // $today   = time();
        // $daysAgo = strtotime("-30 days");

        /*$dates = array();

        $query = $this->db->query( "SELECT `date` FROM `day_xp` WHERE `user_id` = 2 GROUP BY `date` ORDER BY `date` DESC LIMIT 1, 18446744073709551615" ); // AND `date` >= '{$daysAgo}' AND `date` <= '{$today}'
        foreach ( $query->result() as $result ) {
            $dates[] = $result->date;
        }*/

        $dates = array();

        foreach ($this->createDateRangeArray('2013-02-27', date('Y-m-d', strtotime('yesterday'))) as $date) {
            $dates[] = strtotime($date);
        }

        arsort($dates);

        return $dates;
    }

    public function createDateRangeArray( $strDateFrom, $strDateTo ) {
        $aryRange=array();

        $iDateFrom=mktime( 1, 0, 0, substr( $strDateFrom, 5, 2 ),     substr( $strDateFrom, 8, 2 ), substr( $strDateFrom, 0, 4 ) );
        $iDateTo=mktime( 1, 0, 0, substr( $strDateTo, 5, 2 ),     substr( $strDateTo, 8, 2 ), substr( $strDateTo, 0, 4 ) );

        if ( $iDateTo>=$iDateFrom ) {
            array_push( $aryRange, date( 'Y-m-d', $iDateFrom ) ); // first entry
            while ( $iDateFrom<$iDateTo ) {
                $iDateFrom+=86400; // add 24 hours
                array_push( $aryRange, date( 'Y-m-d', $iDateFrom ) );
            }
        }
        return $aryRange;
    }

    /**
     * Fetch the top 50 hiscores for a skill on a specific date
     *
     * @param int     $skillid The skill id
     * @param int     $date    The dates timestamp
     * @return array           The top 50 users
     */
    public function getTop50HistoryUsers( $skillid, $date ) {
        $query = $this->db->query( "SELECT `user_id`, `{$skillid}` FROM `day_xp` WHERE `date` = ? AND `{$skillid}` > 0 GROUP BY `user_id` ORDER BY `{$skillid}` DESC LIMIT 0, 50", array( $date ) );
        if ( $query->num_rows() > 0 ) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    public function getUsernameById( $id ) {
        $query = $this->db->query( "SELECT `username` FROM `users` WHERE `id` = ?", array( $id ) );
        if ( $query->num_rows() > 0 ) {
            $row = $query->row();
            return str_replace( array( '%2520', '%20' ), ' ', $row->username );
        } else {
            return "#" . $id;
        }
    }

}

/* End of file index.php */
/* Location: ./application/models/index.php */
