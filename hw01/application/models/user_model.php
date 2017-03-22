<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

	/**
	 * __construct function.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		parent::__construct();
		$this->load->database();		
	}
	
	/**
	 * create_user function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @param mixed $email
	 * @param mixed $password
	 * @return bool true on success, false on failure
	 */
	public function create_user($username, $email, $password) {
		
		$data = array(
			'username'   => $username,
			'email'      => $email,
			'password'   => $password,
			'checkcode'  => md5($username.$password),
			//'password'   => $this->hash_password($password),
			//'created_at' => date('Y-m-j H:i:s'),
		);
		
		$this->SEmail($email,$username,$checkcode);
		return $this->db->insert('users', $data);		
	}
	
	/**
	 * resolve_user_login function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @param mixed $password
	 * @return bool true on success, false on failure
	 */
	public function resolve_user_login($username, $password) {
		$query = $this->db->query("SELECT * FROM users where username='$username' AND password='$password' AND active=1 ; ");
		
		return $query;

		/*

		$this->db->select('password');
		$this->db->from('users');
		$this->db->where('username', $username);
		$query = $this->db->get()->row('password');
		
		return $query->result();
		*/
	}
	
	public function resolve_user_logup($username, $password) {
			$query = $this->db->query("SELECT * FROM users where username='$username'; ");
			
			return $query;

	}

	/**
	 * get_user_id_from_username function.
	 * 
	 * @access public
	 * @param mixed $username
	 * @return int the user id
	 */
	public function get_user_id_from_username($username) {
		
		$this->db->select('id');
		$this->db->from('users');
		$this->db->where('username', $username);

		return $this->db->get()->row('id');
		
	}
	
	/**
	 * get_user function.
	 * 
	 * @access public
	 * @param mixed $user_id
	 * @return object the user object
	 */
	public function get_user($user_id) {
		
		$this->db->from('users');
		$this->db->where('id', $user_id);
		return $this->db->get()->row();
		
	}
	
	/**
	 * hash_password function.
	 * 
	 * @access private
	 * @param mixed $password
	 * @return string|bool could be a string on success, or bool false on failure
	 */
	private function hash_password($password) {
		
		return password_hash($password, PASSWORD_BCRYPT);
		
	}
	
	/**
	 * verify_password_hash function.
	 * 
	 * @access private
	 * @param mixed $password
	 * @param mixed $hash
	 * @return bool
	 */
	private function verify_password_hash($password, $hash) {
		
		return password_verify($password, $hash);
		
	}
	

	public function SEmail($email,$username,$checkcode)
    {
        // Set SMTP Configuration
        $emailConfig = [
            'protocol' => 'smtp', 
            'smtp_host' => 'ssl://smtp.googlemail.com', 
            'smtp_port' => 465, 
            'smtp_user' => 'zx1813zx@gmail.com', 
            'smtp_pass' => 'sbzfzokfzelomddy', 
            'mailtype' => 'html', 
            'charset' => 'iso-8859-1'
        ];

        // Set your email information
        $from = [
            'email' => 'zx1813zx@gmail.com',
            'name' => 'zx1813zx'
        ];

        $message="This is the HTML message body <b>from mail!</b> please click the following messge to activate your register. CodeIgniter Test.<br>";
        $message=$message . '<a href=' . base_url().'homeC/setActive?user='.$username.'&msg='.$checkcode.'> Please Click Here. </a>';
       
        $to = array($email);
        $subject = 'Please check verification from mail!!!';
      	//$message = 'Type your gmail message here';
        //$message =  $this->load->view('welcome_message',[],true);
        // Load CodeIgniter Email library
        $this->load->library('email');
        $this->email->initialize($emailConfig);//mail初始化
        // Sometimes you have to set the new line character for better result
        $this->email->set_newline("\r\n");
        // Set email preferences
        $this->email->from($from['email'], $from['name']);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
        // Ready to send email and check whether the email was successfully sent
        if (!$this->email->send()) {
            // Raise error message
            show_error($this->email->print_debugger());
        } else {
            // Show success notification or other things here
            echo 'Success to send email';
        }
    }


	public function user_activate($user,$hash){
		
		$query = $this->db->query("SELECT username , password , active FROM users where username='$user' AND checkcode = '$hash' ");
		
		if (!$query){
			return 'user in Null';
		}
		else {
			//
			$data = array(
				'active' => true
			);
			$this->db->where('username', $user);
			return $this->db->update('users', $data);

			return $query;

		}
	}
}

