<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('User_model');
        $this->load->library('form_validation');
        $this->load->library('session');
    }

    public function check_account(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            
            
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            // USE THIS IF JS-ed
            // $account = $this->input->post('user_type');


            //================= CHECKING TRANSFORM INTO JS =======================

            $account = null;
            if($this->User_model->client_exist($email)) $account = 'client';
            else if($this->User_model->company_exist($email)) $account = 'company';
            
            //=================== JS-able UNTIL HERE =============================
            
            if($account == null)
                redirect(base_url("login")); // PRINT ACCOUNT NON EXISTENT FLASHDATA

            $u_inf = $this->User_model->check_pass($account, $email, $password);
            if($u_inf == FALSE)
                redirect(base_url("login")); // PRINT WRONG PASSWORD FLASHDATA

            $session_data = array(
                'id' => $u_inf[$account.'_id'],
                'user_type' => $account,
                'email' => $email
            );

            $this->session->set_userdata('UserLoginSession', $session_data);
            redirect(base_url('home'));
            
        }
    }
}