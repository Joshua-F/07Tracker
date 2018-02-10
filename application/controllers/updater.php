<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Updater extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // if ($_SERVER['REMOTE_ADDR'] != '-') show_error('The website is currently down for some testing, we will be back soon.', 503);

        $this->load->model('updatehelper');
    }

    public function index() {
        /*if ($this->input->get('cronkey') == "jhkbgfsdfgDFSF45") {
            $this->benchmark->mark('code_start');
            $this->updatehelper->updateUsers();
            $this->benchmark->mark('code_end');
            echo date('r') . " - " . $this->benchmark->elapsed_time('code_start', 'code_end') . PHP_EOL;
        } else {*/
            show_404();
        //}
    }

    public function add($user='') {
        if (empty($user)) {
            show_error('...');
        }

        if (strpos($user, '%20') !== FALSE || strpos($user, '%2520') !== FALSE) {
            redirect( '/updater/add/' . str_replace(array('%20', '%2520'), '+', $user), 'location' );
            exit;
        }

        $this->updatehelper->newUser($user);
    }

    public function update($user='') {
        if (empty($user)) {
            show_error('...');
        }

        if (strpos($user, '%20') !== FALSE || strpos($user, '%2520') !== FALSE) {
            redirect( '/updater/update/' . str_replace(array('%20', '%2520'), '+', $user), 'location' );
            exit;
        }

        $this->updatehelper->updateUser($user);
    }

}

/* End of file updater.php */
/* Location: ./application/controllers/updater.php */