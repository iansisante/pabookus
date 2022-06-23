<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Routes extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
    }
    
	public function index()
	{
		$this->load->view('welcome_message');
	}
    
	public function newsfeed()
	{
        if(!empty($this->session->flashdata('signinSuccess')))
            echo $this->session->flashdata('signinSuccess');

        echo "<pre>";
        $this->load->model('User_model');
        $this->load->model('Service_model');
        $company_table = $this->User_model->get_table();
        $services = array();
        $x=0;

        foreach($company_table as $c){            
            foreach($this->Service_model->get_table_full($c['services_id']) as $tbs){
                $tbs["company_name"] = $c["company_name"];
                $tbs["company_id"] = $c["company_id"];
                $services[$x++] = $tbs;
            }
        }

        $this->load->view('inc/header');
        $this->load->view('inc/navbar');
		$this->load->view('newsfeed');
        $this->load->view('inc/footer');
	}

    public function profile()
    {
        
        if(!empty($this->session->flashdata('signinSuccess')))
            $this->session->unset_userdata('signinSuccess');

        $id_exist = isset($_GET['id']) ||
            ($this->session->userdata('UserLoginSession'));

        if($id_exist == false)
            redirect(base_url('home'));

        $this->load->model('User_model');
        $this->load->model('Industry_model');
        $this->load->model("Service_model");

        $id = $_GET['id'];

        // RETURNS INFO
        // 1 = client, 2 = company
        if($_GET['ut'] == 1) {
            $user_details = $this->User_model->get_client_id($id);
            $page = 'inc/profile_client';
        } else if($_GET['ut'] == 2) {

            // RETURN USER DATA
            $user_details = $this->User_model->get_company_id($id);
            $page = 'inc/profile_company';
            
            // RETURN INDUSTRY DATA
            $temp_i = $this->Industry_model->get_table();
            $industry = array();
            foreach($temp_i as $i){
                $industry[$i['name']] = $i;
            }
            
            // echo "<pre>";
            ksort($industry);     
            $data['key_industry'] = $industry;
            $data['key_industry_default'] = $temp_i;
            // print_r($data['key_industry_default']);

            // RETURN SERVICE DATA
            $service = $this->Service_model->get_table($id);
            $data['key_service'] = $service;
            
        } else redirect(base_url('home'));

        if(!isset($user_details)) 
            redirect(base_url('home'));

        $data['key_details'] = $user_details;

        $this->load->view('inc/header');
        $this->load->view('inc/navbar');
        $this->load->view($page, $data);
        //$this->load->view('inc/footer');
    }

    public function login()
    {
        $this->load->view('inc/header');
        $this->load->view('login');
        $this->load->view('inc/footer');
    }

    public function schedule()
    {
        $this->load->view('inc/header');
        $this->load->view('inc/navbar');
        $this->load->view('schedule');
        $this->load->view('inc/footer');
    }

    public function logout()
    {
        if($this->session->has_userdata('signinSuccess'))
            $this->session->unset_userdata('signinSuccess');
        $this->session->unset_userdata('UserLoginSession');
        $this->session->sess_destroy();
        redirect('home');
    }
}
