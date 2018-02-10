<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Tracker extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // if ($_SERVER['REMOTE_ADDR'] != '-') show_error('The website is currently down for some testing, we will be back soon.', 503);

        if ( strpos( $this->input->cookie( 'rsname' ), '%20' ) !== FALSE || strpos( $this->input->cookie( 'rsname' ), '%2520' ) ) {
            $cookie = array(
                'name'   => 'rsname',
                'value'  => str_replace( array( '%20', '%2520' ), '+', $rsname ),
                'expire' => strtotime( '+1 year' ) - time()
            );
            $this->input->set_cookie( $cookie );
        }
    }

    public function track( $user='', $type='track' ) {
        if ( $type === 'track' ) {
            $this->_track( $user );
        } elseif ( $type === 'history' ) {
            $args = func_get_args();
            $date = isset( $args[2] ) ? $args[2] : $this->input->post( 'date' ) ;
            $this->_history( $user, $date );
        } else {
            show_404();
        }
    }

    private function _track( $user='' ) {
        $rsname = $this->input->post( 'rsname' );

        if ( $rsname ) {
            redirect( '/track/' . $rsname, 'location' );
            exit;
        }

        if ( empty( $user ) ) {
            show_error( 'This page can not be directly accessed without a username supplied in the link.<br/>Example: ' . site_url( "track/joshua f" ) );
        }

        if ( !empty( $user ) ) {
            $rsname = $user;
        }

        if ( strpos( $rsname, '%20' ) !== FALSE || strpos( $rsname, '%2520' ) ) {
            redirect( '/track/' . str_replace( array( '%20', '%2520' ), '+', $rsname ), 'location' );
            exit;
        }

        $rsname = ucwords( strtolower( str_replace( '+', ' ', $rsname ) ) );

        $cookie = array(
            'name'   => 'rsname',
            'value'  => $rsname,
            'expire' => strtotime( '+1 year' ) - time()
        );
        $this->input->set_cookie( $cookie );

        $this->load->model( 'track' );

        $userInfo = $this->track->fetchUserData( $rsname );

        $data['title'] = $rsname . '\'s User Page';

        if ( !$userInfo ) {
            $data['curUser'] = ucwords( $rsname );
            $this->load->view( 'newuser', $data );
        } else {
            $this->load->helper( 'date' );
            $this->load->helper( 'tracker' );
            $data['description']   = $rsname . "'s user page showing graphs and analytics on 2007 Tracker.";
            $data['userGraphData'] = $this->track->getGraphData( $rsname );
            $data['history_dates'] = $this->track->fetchUserHistoryDates( $rsname );
            $data['last_check']    = time();
            $data['comatLevel']    = calculateCombatLevel( array( "attack" => $userInfo['levels'][1], "defence" => $userInfo['levels'][2], "strength" => $userInfo['levels'][3], "hitpoints" => $userInfo['levels'][4], "range" => $userInfo['levels'][5], "prayer" => $userInfo['levels'][6], "magic" => $userInfo['levels'][7] ) );
            $data['userInfo']      = $userInfo;
            $data['curUser']       = $rsname;
            $data['skills']        = $this->track->getSkillInfo();
            $this->load->view( 'viewuser', $data );
        }
    }

    public function _history( $user='', $date='' ) {
        $datePost = $this->input->post( 'date' );

        if ( $datePost ) {
            redirect( '/track/' . $user . '/history/' . date( "m-d-Y", $datePost ) );
            exit;
        }

        if ( !$datePost && ( is_null( $date ) || empty( $date ) ) ) {
            redirect( '/track/' . $user );
            exit;
        }

        if ( strpos( $user, '%20' ) !== FALSE || strpos( $user, '%2520' ) ) {
            redirect( '/track/' . str_replace( array( '%20', '%2520' ), '+', $user ) . '/history/' . $date, 'location' );
            exit;
        }

        $user = ucwords( strtolower( str_replace( '+', ' ', $user ) ) );

        $this->load->model( 'track' );

        $userInfo = $this->track->fetchUserHistoryData( $user, $date );

        if ( !$userInfo ) {
            $data['title'] = $rsname . '\'s User Page';
            $data['curUser'] = ucwords( $username );
            $this->load->view( 'newuser', $data );
        } else {
            $this->load->helper( 'date' );
            $this->load->helper( 'tracker' );
            $data['title']         = $user . '\'s User History Page ('.date( 'F j, Y', strtotime( str_replace( "-", "/", $date ) ) ).')';
            $data['history_dates'] = $this->track->fetchUserHistoryDates( $user );
            $data['userInfo']      = $this->track->fetchUserHistoryData( $user, $date );
            $data['curUser']       = $user;
            $data['comatLevel']    = calculateCombatLevel( array( "attack" => $userInfo['levels'][1], "defence" => $userInfo['levels'][2], "strength" => $userInfo['levels'][3], "hitpoints" => $userInfo['levels'][4], "range" => $userInfo['levels'][5], "prayer" => $userInfo['levels'][6], "magic" => $userInfo['levels'][7] ) );
            $data['skills']        = $this->track->getSkillInfo();
            $data['history_date']  = $date;

            $this->load->view( 'viewuser', $data );
        }
    }

}

/* End of file tracker.php */
/* Location: ./application/controllers/tracker.php */
