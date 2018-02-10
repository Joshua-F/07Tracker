<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // if ($_SERVER['REMOTE_ADDR'] != '-') show_error('The website is currently down for some testing, we will be back soon.', 503);

        $this->load->model('page');

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
        redirect('/', 'location');
    }

    public function playerCount() {
        $data['title']       = "RuneScape Player Count Tracker";
        $data['description'] = "Graph showing the RuneScape player count updated every 5 minutes.";
        $data['curUser']     = ucwords( str_replace( '+', ' ', $this->input->cookie( 'rsname' ) ) );
        $data['javascript']  = $this->page->getPlayerGraphData();
        $data['counters']    = $this->page->getPlayerCountData();
        $this->load->view('playerCount', $data);
    }

}

/* End of file pages.php */
/* Location: ./application/controllers/pages.php */