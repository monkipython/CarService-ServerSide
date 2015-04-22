<?php
require_once "XMPP.php";

	class XMPPHP_XMPPOld extends XMPPHP_XMPP {
	
		protected $session_id;

		public function __construct($host, $port, $user, $password, $resource, $server = null, $printlog = false, $loglevel = null) {
			parent::__construct($host, $port, $user, $password, $resource, $server, $printlog, $loglevel);
			if(!$server) $server = $host;
			$this->stream_start = '<stream:stream to="' . $server . '" xmlns:stream="http://etherx.jabber.org/streams" xmlns="jabber:client">';
			$this->fulljid = "{$user}@{$server}/{$resource}";
		}
		
		public function startXML($parser, $name, $attr) {
			if($this->xml_depth == 0) {
				$this->session_id = $attr['ID'];
				$this->authenticate();
			}
			parent::startXML($parser, $name, $attr);
		}
		
		public function authenticate() {
			$id = $this->getId();
			$this->addidhandler($id, 'authfieldshandler');
			$this->send("<iq type='get' id='$id'><query xmlns='jabber:iq:auth'><username>{$this->user}</username></query></iq>");
		}

		public function authFieldsHandler($xml) {
			$id = $this->getId();
			$this->addidhandler($id, 'oldAuthResultHandler');
			if($xml->sub('query')->hasSub('digest')) {
				$hash = sha1($this->session_id . $this->password);
				print "{$this->session_id} {$this->password}\n";
				$out = "<iq type='set' id='$id'><query xmlns='jabber:iq:auth'><username>{$this->user}</username><digest>{$hash}</digest><resource>{$this->resource}</resource></query></iq>";
			} else {
				$out = "<iq type='set' id='$id'><query xmlns='jabber:iq:auth'><username>{$this->user}</username><password>{$this->password}</password><resource>{$this->resource}</resource></query></iq>";
			}
			$this->send($out);

		}
		
		public function oldAuthResultHandler($xml) {
			if($xml->attrs['type'] != 'result') {
				$this->log->log("Auth failed!",  XMPPHP_Log::LEVEL_ERROR);
				$this->disconnect();
				throw new XMPPHP_Exception('Auth failed!');
			} else {
				$this->log->log("Session started");
				$this->event('session_start');
			}
		}
	}


?>
