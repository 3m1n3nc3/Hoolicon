<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Curler {

    public $CI;

    function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->database();
    }

    public function fetch($url, $post = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        $data = curl_exec($ch);
        $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $data = array('http_code' => $resultStatus, 'curl_error' => curl_error($ch));
        } else {
        
            $data_merge = json_decode($data, true);
            $data_merge1 = array('http_code' => $resultStatus);

            if ($data_merge && $data_merge1) {
                $data = json_encode(array_merge($data_merge, $data_merge1), JSON_FORCE_OBJECT);
            }

        }

        $data = json_decode($data);
        curl_close($ch);
        return $data;
    }

    public function ssl_fetch($url, $post = array())
    {
        $ch = curl_init();   
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        $data = curl_exec($ch);
        $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $data = array('http_code' => $resultStatus, 'curl_error' => curl_error($ch));
        } else {
            $data_merge = json_decode($data, true);
            $data_merge1 = array('http_code' => $resultStatus);

            if ($data_merge && $data_merge1) {
                $data = json_encode(array_merge($data_merge, $data_merge1), JSON_FORCE_OBJECT);
            }            
        }

        $data = json_decode($data);  
        curl_close($ch);    
        return $data;
    }

    public function validate($url, $post = array())
    {   
        $username = url_title($this->CI->customlib->getAppName(), '', true);
        $data = $this->get_licence();
        $post = array(
            'key' => $data['purchase_code'], 
            'user' => $data['username']);
        $api = $this->fetch($url, $post);
        return array(
            'status'        => $api->status,
            'message'       => $api->message,
            'response'      => $api->response,
            'http_code'     => $api->http_code,
            'username'      => $data['username']
        );
    }

    public function get_licence() 
    {

        $this->CI->db->select('*')->from('x_license');  
        $this->CI->db->limit(1); 
        
        $this->refresh_licence_table();

        $query = $this->CI->db->get();
        return $query->row_array();
    }

    public function update_licence($data = null) 
    {   

        if ($lic = $this->get_licence()) {  
            $this->CI->db->update('x_license', $data);
        } else {
            $this->CI->db->insert('x_license', $data);
        } 

        $this->refresh_licence_table();

    }

    public function refresh_licence_table() 
    {  

        $sql = "DELETE FROM x_license WHERE `id` != '1'";
        $this->CI->db->query($sql);

    }
 
    function ApiUrl($query = '') { 
      $api_url = 'http://api.build.te/api/';
      if ($query && substr($query, -1) !== '/') {
        $query = $query.'/';
      }
      return $api_url.$query;
    }
}
