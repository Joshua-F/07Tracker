<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

class Signature extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model( 'signatures' );
    }

    public function index() {
        $data['curUser'] = ucwords( str_replace( '+', ' ', $this->input->cookie( 'rsname' ) ) );
        $this->load->view('signaturegen', $data);
    }

    public function sig( $username='', $style='varrock' ) {
        $username = str_replace('.png', '', $username);
        $style    = str_replace('.png', '', $style);

        if (strpos($username, '%20') !== FALSE || strpos($username, '%2520') !== FALSE) {
            redirect( '/sig/' . str_replace(array('%20', '%2520'), '+', $username)  . '/' . $style . '.png', 'location' );
            exit;
        }

        $style = strtolower( $style );
        $this->signatures->showSignature($username, $style);
    }

}

/* End of file signature.php */
/* Location: ./application/controllers/signature.php */
