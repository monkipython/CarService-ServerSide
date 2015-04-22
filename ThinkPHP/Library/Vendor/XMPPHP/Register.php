<?php

error_reporting(E_ALL);

include 'XMLStream.php';

class Register extends XMPPHP_XMLStream{
	/*
	 * @param string  $username
	 * @param string  $password
	 * @param string  $email	 
	 */
	 
	public function __construct($username, $password, $email){
		parent::__construct($host="121.40.92.53", $port=5222, $printlog=true, $loglevel=XMPPHP_Log::LEVEL_INFO);
		$this->stream_start = '<stream:stream to="' . $host . '" xmlns:stream="http://etherx.jabber.org/streams" xmlns="jabber:client" version="1.0">';
		$this->stream_end   = '</stream:stream>';
		$this->default_ns   = 'jabber:client';
		$out = "";
		if(!$this->isDisconnected())
		$out .= "<iq type='set' id='uid2'>
				<query xmlns='jabber:iq:register'>
				<username>$username</username>
				<password>$password</password>
				<email>$email</email>
				</query>
				</iq>";
				
		$this->send($out);
	}
}

?>