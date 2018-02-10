<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // if ($_SERVER['REMOTE_ADDR'] != '-') show_error('The website is currently down for some testing, we will be back soon.', 503);

        $this->load->model( 'index' );

        if (strpos($this->input->cookie( 'rsname' ), '%20') !== FALSE || strpos($this->input->cookie( 'rsname' ), '%2520')) {
            $cookie = array(
                'name'   => 'rsname',
                'value'  => str_replace(array('%20', '%2520'), '+', $rsname),
                'expire' => strtotime( '+1 year' ) - time()
            );
            $this->input->set_cookie( $cookie );
        }
    }

    public function index() {
        $this->load->helper( 'date' );
        $data['totalUsers']        = $this->index->getTotalUsers();
        $data['totalXPGained']     = $this->index->getTotalXPGained();
        $data['totalLevelsGained'] = $this->index->getTotalLevelsGained();
        $data['description']       = "2007 Tracker is a website designed to analyze and track yours and other players' levels.";
        $data['curUser']           = ucwords( str_replace( '+', ' ', $this->input->cookie( 'rsname' ) ) );
        $this->load->view( 'home', $data );
    }

    public function contact() {
        $this->load->helper('form');
        $this->load->library('form_validation');
        $data['title'] = 'Contact';
        $data['description'] = 'Contact the administrators of 07Tracker.com.';
        $data['curUser'] = ucwords( str_replace( '%20', ' ', $this->input->cookie( 'rsname' ) ) );
        $data['formData'] = array(
            'firstname' => array(
                'name' => 'firstname',
                'value' => set_value('firstname', $this->input->post('firstname')),
                'class' => 'span3',
                'placeholder' => 'Your First Name'
            ),
            'runescapename' => array(
                'name' => 'runescapename',
                'value' => set_value('runescapename', $this->input->post('runescapename')),
                'class' => 'span3',
                'placeholder' => 'Your RuneScape Name'
            ),
            'email' => array(
                'name' => 'email',
                'value' => set_value('email', $this->input->post('email')),
                'class' => 'span3',
                'placeholder' => 'Your email address'
            ),
            'subject' => array(
                'na' => 'Choose One:',
                'support' => 'General Support',
                'displayname' => 'Display Name',
                'suggestion' => 'Suggestion',
                'other' => 'Other'
            ),
            'message' => array(
                'name' => 'message',
                'value' => set_value('message', $this->input->post('message')),
                'class' => 'input-xlarge span5',
                'style' => 'min-width: 456px; max-width: 456px;'
            )
        );

        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|alpha');
        $this->form_validation->set_rules('runescapename', 'RuneScape Name', 'trim|required|callback_runescapename_check');
        $this->form_validation->set_rules('email', 'Email Address', 'trim|required|valid_email');
        $this->form_validation->set_rules('subject', 'Subject', 'callback_subject_check');
        $this->form_validation->set_rules('message', 'Message', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('contact', $data);
        } else {
            $this->load->library('email');
            $subjects = array(
                'support' => 'Support',
                'displayname' => 'Display name change',
                'suggestion' => 'Suggestion',
                'other' => 'Other'
            );
            $subject = $subjects[$this->input->post('subject')];
            $this->email->from($this->input->post('email'), $this->input->post('runescapename'));
            $this->email->to('support@07tracker.com');
            $this->email->subject($subject);
            $this->email->message($this->input->post('message'));
            $this->email->send();
            $this->load->view('contactsuccess', $data);
        }
    }

    public function subject_check($str) {
        $validSubjects = array('support', 'displayname', 'suggestion', 'other');

        if (!in_array($str, $validSubjects) || $str === "na") {
            $this->form_validation->set_message('subject_check', 'The %s field is not valid, please pick one from the drop down.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function runescapename_check($str) {
        if (!preg_match('#[a-zA-Z0-9\s_-]#', $str)) {
            $this->form_validation->set_message('runescapename_check', 'The %s field can only contain alphanumeric, space, underscore and dashes.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function top50( $skillid=0 ) {

        if ( $skillid < 0 || $skillid > 23 ) {
            $skillid = 0;
        }

        // $skillid = (int) $skillid;

        if ( !preg_match( '/\d+/', $skillid ) ) {
            $skillid = 0;
        }

        $this->load->model( 'track' );
        $data['skills'] = $this->track->getSkillInfo();
        $data['description'] = "The top 50 users today in {$data['skills'][$skillid]} on 2007 Tracker.";
        $data['skillid'] = $skillid;
        $data['title'] = 'Daily ' . $data['skills'][$skillid] . ' Top 50';
        $data['curUser'] = ucwords( str_replace( '%20', ' ', $this->input->cookie( 'rsname' ) ) );
        $data['users'] = $this->index->getTop50( $skillid );
        $this->load->view( 'top50', $data );
    }

    public function top50history( $skillid=null ) {
        $this->load->library('session');

        if ($date = $this->input->post('date')) {
            $this->session->set_flashdata('date', $date);
        } elseif ($date = $this->session->flashdata('date')) {
            $this->session->set_flashdata('date', $date);
        } else {
            $date = strtotime('yesterday 00:00:00');
        }

        $skillid = (int) $skillid;

        if ( !preg_match( '/\d+/', $skillid ) ) {
            $skillid = 0;
        }

        if ( $skillid < 0 || $skillid > 23 ) {
            $skillid = 0;
        }

        $days = 30;

        $this->load->model( 'track' );
        $data['skillid']   = $skillid;
        $data['dateFlash'] = $date;
        $data['skills']    = $this->track->getSkillInfo();
        $data['dates']     = $this->index->getTop50HistoryDates($days);
        $data['users']     = $this->index->getTop50HistoryUsers($skillid, $date);
        $data['title']     = !is_null( $skillid ) ? $data['skills'][$skillid] . ' Top 50 History' : 'Top 50 History' ;
        $data['curUser']   = ucwords( str_replace( '%20', ' ', $this->input->cookie( 'rsname' ) ) );
        $this->load->view( 'top50history', $data );
    }

    /**
     * Display the records page
     * @param  integer $type The type to show, 0 = days, 1 = week, 2 = month
     */
    public function records($type="daily", $skillid=0) {
        $validTypes = array("daily", "weekly", "monthly");
        if (!in_array(strtolower($type), $validTypes)) {
            $type = "daily";
        }

        $skillid = (int) $skillid;

        if ( !preg_match( '/\d+/', $skillid ) ) {
            $skillid = 0;
        }

        if ( $skillid < 0 || $skillid > 23 ) {
            $skillid = 0;
        }

        $this->load->model('track');
        $data['userData']    = $type == "daily" ? $this->index->getDailyRecords($skillid) : array('usernames' => array('Coming Soon'), 'xp' => array(0), 'dates' => array(time())) ;
        $data['skills']      = $this->track->getSkillInfo();
        $data['description'] = "Global records set for {$data['skills'][$skillid]} on 2007 Tracker.";
        $data['type']        = $type;
        $data['title']       = $data['skills'][$skillid] . " " . ucwords($type) . " Records" ;
        $data['curUser']     = ucwords( str_replace( '%20', ' ', $this->input->cookie( 'rsname' ) ) );
        $this->load->view('records', $data);
    }

    public function maintenance() {
        redirect('/');
    }

}

/* End of file home.php */
/* Location: ./application/controllers/home.php */
