<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . 'third_party/paystack/src/autoload.php');

class Paystack extends Parent_Controller {

    function __construct() {
        parent::__construct(); 
        $this->setting = $this->setting_model->get(); 
    }

    public function index() {
        $this->session->set_userdata('top_menu', 'Library');
        $this->session->set_userdata('sub_menu', 'book/index');
        $data = $this->session->userdata('params');
        $data['setting'] = $this->setting;

        $this->load->view('parent/paystack', $data);
    }

    public function process() {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $session_data = $this->session->userdata('params');
            $pay_method = $this->paymentsetting_model->getActiveMethod(); 

            $paystack = new Yabacon\Paystack($pay_method->api_secret_key);
        
            $trx = $paystack->transaction->verify( [ 'reference' => $_GET['reference'] ] );
            if(!$trx->status){
                $Arr = array('status' => 0, 'header' => '', 'response' => $trx->message); 
            } elseif('success' == $trx->data->status){
                // use trx info including metadata and session info to confirm that cartid
                // matches the one for which we accepted payment
                $params = array(
                    'status' => $trx->data->status,
                    'reference' => $_GET['reference']
                );
                $response = array(
                    'message' => 'Your payment was successful, please don\'t close this page, we\'re redirecting you!',
                    'short_message' => 'Your payment was successful',
                    'error_message' => 'Your payment was successful, but an error occurred and we were unable to process your payment!'
                );
                
                $this->session->set_userdata("paystack", $params);
                $success = $this->success();
                if ($success) {
                    $Arr = array('status' => 1, 'header' => $success, 'response' => $response);
                } else {
                    $Arr = array('status' => 0, 'header' => '', 'response' => $response['error_message']);
                }
                $this->session->unset_userdata("paystack");
                $this->session->unset_userdata("params");
            } else {
                $Arr = array('status' => 0, 'header' => '', 'response' => $trx);
            }      
        } else {
            $Arr = array('status' => 0, 'header' => site_url('parent/parents/getfees/'), 'response' => '');
        }
        echo json_encode($Arr, JSON_UNESCAPED_SLASHES);
    } 

    public function success() {
        $status = $this->session->userdata('paystack');
        $params = $this->session->userdata('params');

        if ($status/*$status['status'] == 'success' && $status['reference'] == $params['reference']*/) {
   
            $pay_method = $this->paymentsetting_model->getActiveMethod(); 
            $json_array = array(
                'amount' => $params['total'],
                'date' => date('Y-m-d'),
                'amount_discount' => 0,
                'amount_fine' => 0,
                'description' => "Online fees payment through Paystack. TXN ID: " . $params['reference'],
                'payment_mode' => 'Paystack',
            );
            $data = array(
                'student_fees_master_id' => $params['student_fees_master_id'],
                'fee_groups_feetype_id' => $params['fee_groups_feetype_id'],
                'amount_detail' => $json_array
            );
            $send_to = $params['guardian_phone'];
            $inserted_id = $this->studentfeemaster_model->fee_deposit($data, $send_to, '');

            if ($inserted_id) {
                $invoice_detail = json_decode($inserted_id);
                // redirect(base_url("parent/payment/successinvoice/" . $invoice_detail->invoice_id . "/" . $invoice_detail->sub_invoice_id));
                return base_url("parent/payment/successinvoice/" . $invoice_detail->invoice_id . "/" . $invoice_detail->sub_invoice_id);
            } else {
                return false;
            } 
        }
    }

}
