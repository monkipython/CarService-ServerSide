<?php
/**
 * XMPPHP: XMPP库
 */

define("XMPP_SERVER_IP","121.40.92.53");
define("XMPP_SERVER_PORT", 5222);
define("XMPP_SERVER_RESOURCE", C('XMPP_SERVER_RESOURCE'));
define("XMPP_SERVER_DOMAIN",C("XMPP_SERVER_DOMAIN"));

/** XMPPHP_XMLStream */
require_once dirname(__FILE__) . "/XMLStream.php";
require_once dirname(__FILE__) . "/Roster.php";

/**
 * XMPPHP 主类
 * 
 * @category xmpphp
 */
class XMPPHP_XMPP extends XMPPHP_XMLStream {
	/**
	 * @var string
	 */
	public $server;

	/**
	 * @var string
	 */
	public $user;
	
	/**
	 * @var string
	 */
	protected $password;
	
	/**
	 * @var string
	 */
	protected $resource;
	
	/**
	 * @var string
	 */
	protected $fulljid;
	
	/**
	 *@var string
	 */
	protected $vcard;
	
	/**
	 *@var string
	 */
	protected $roster_vcard;
	 
	/**
	 * @var string
	 */
	protected $basejid;
	
	/**
	 * @var boolean
	 */
	protected $authed = false;
	protected $session_started = false;
	
	/**
	 * @var boolean
	 */
	protected $auto_subscribe = false;
	
	/**
	 * @var boolean
	 */
	protected $use_encryption = true;
	
	/**
	 * @var boolean
	 */
	public $track_presence = true;
	
	/**
	 * @var object
	 */
	public $roster;

	/**
	 * Constructor
	 *
	 * @param string  $host
	 * @param integer $port
	 * @param string  $user
	 * @param string  $password
	 * @param string  $resource
	 * @param string  $server
	 * @param boolean $printlog
	 * @param string  $loglevel
	 */
	public function __construct($host, $port, $user, $password, $resource, $server = null, $printlog = false, $loglevel = null) {
		parent::__construct($host, $port, $printlog, $loglevel);
		
		$this->user	 = $user;
		$this->password = $password;
		$this->resource = $resource;
		if(!$server) $server = $host;
		$this->basejid = $this->user . '@' . $this->host;

		$this->roster = new Roster();
		$this->track_presence = true;

		$this->stream_start = '<stream:stream to="' . $server . '" xmlns:stream="http://etherx.jabber.org/streams" xmlns="jabber:client" version="1.0">';
		$this->stream_end   = '</stream:stream>';
		$this->default_ns   = 'jabber:client';
		
		$this->addXPathHandler('{http://etherx.jabber.org/streams}features', 'features_handler');
		$this->addXPathHandler('{urn:ietf:params:xml:ns:xmpp-sasl}success', 'sasl_success_handler');
		$this->addXPathHandler('{urn:ietf:params:xml:ns:xmpp-sasl}failure', 'sasl_failure_handler');
		$this->addXPathHandler('{urn:ietf:params:xml:ns:xmpp-tls}proceed', 'tls_proceed_handler');
		$this->addXPathHandler('{jabber:client}message', 'message_handler');
		$this->addXPathHandler('{jabber:client}presence', 'presence_handler');
		$this->addXPathHandler('iq/{jabber:iq:roster}query', 'roster_iq_handler');
		//DIGEST-MD5
		$this->addXPathHandler('{urn:ietf:params:xml:ns:xmpp-sasl}challenge', 'sasl_challenge_handler');
	}
	
	/**
	 * 当前用户JID
	 */
	public function getFullJID(){
		return $this->fulljid;
	}
	
	/**
	 * 启动加密 on/ff
	 *
	 * @param boolean $useEncryption
	 */
	public function useEncryption($useEncryption = true) {
		$this->use_encryption = $useEncryption;
	}
	
	/**
	 * 启动自动关注状态请求.
	 *
	 * @param boolean $autoSubscribe
	 */
	public function autoSubscribe($autoSubscribe = true) {
		$this->auto_subscribe = $autoSubscribe;
	}

	/**
	 * 发送 XMPP 聊天信息
	 *
	 * @param string $to 
	 * @param string $body content
	 * @param string $type 
	 * @param string $subject 
	 */
	public function message($to, $body, $type = 'chat', $subject = null, $payload = null) {
	    if(is_null($type))
	    {
	        $type = 'chat';
	    }
	  
		$to	  = htmlspecialchars($to);
		$body	= htmlspecialchars($body);
		$subject = htmlspecialchars($subject);
		$out = "<message from=\"{$this->fulljid}\" to=\"$to\" type='$type'>";
		if($subject) $out .= "<subject>$subject</subject>";
		$out .= "<body>$body</body>";
		if($payload) $out .= $payload;
		$out .= "</message>";
		
		$this->send($out);
	}
	
	/**
	 * 设置用户状态
	 * show (away, chat, dnd ＝ do not disturb, xa ＝ extended)
	 * status 心情状态
	 * @param string $status 
	 * @param string $show 
	 * @param string $to
	 */
	public function presence($status = null, $show = 'available', $to = null, $type='available', $priority=0) {
		if($type == 'available') $type = '';
		$to	 = htmlspecialchars($to);
		$status = htmlspecialchars($status);
		if($show == 'unavailable') $type = 'unavailable';
		
		$out = "<presence";
		if($to) $out .= " to=\"$to\"";
		if($type) $out .= " type='$type'";
		if($show == 'available' and !$status and $priority !== null) {
			$out .= "/>";
		} else {
			$out .= ">";
			if($show != 'available') $out .= "<show>$show</show>";
			if($status) $out .= "<status>$status</status>";
			if($priority !== null) $out .= "<priority>$priority</priority>";
			$out .= "</presence>";
		}
		
		$this->send($out);
	}
	/**
	 * 发送好友验证请求
	 *
	 * @param string $jid
	 */
	public function subscribe($jid) {
		$this->send("<presence type='subscribe' to='{$jid}' from='{$this->fulljid}' />");
		//$this->send("<presence type='subscribed' to='{$jid}' from='{$this->fulljid}' />");
	}

	/**
	 * 消息处理
	 *
	 * @param string $xml
	 */
	public function message_handler($xml) {
		if(isset($xml->attrs['type'])) {
			$payload['type'] = $xml->attrs['type'];
		} else {
			$payload['type'] = 'chat';
		}
		$payload['from'] = $xml->attrs['from'];
		$payload['body'] = $xml->sub('body')->data;
		$payload['xml'] = $xml;
		$this->log->log("Message: {$xml->sub('body')->data}", XMPPHP_Log::LEVEL_DEBUG);
		$this->event('message', $payload);
	}

	/**
	 * 用户状态处理
	 *
	 * @param string $xml
	 */
	public function presence_handler($xml) {
		$payload['type'] = (isset($xml->attrs['type'])) ? $xml->attrs['type'] : 'available';
		$payload['show'] = (isset($xml->sub('show')->data)) ? $xml->sub('show')->data : $payload['type'];
		$payload['from'] = $xml->attrs['from'];
		$payload['status'] = (isset($xml->sub('status')->data)) ? $xml->sub('status')->data : '';
		$payload['priority'] = (isset($xml->sub('priority')->data)) ? intval($xml->sub('priority')->data) : 0;
		$payload['xml'] = $xml;
		if($this->track_presence) {
			$this->roster->setPresence($payload['from'], $payload['priority'], $payload['show'], $payload['status']);
		}
		$this->log->log("Presence: {$payload['from']} [{$payload['show']}] {$payload['status']}",  XMPPHP_Log::LEVEL_DEBUG);
		if(array_key_exists('type', $xml->attrs) and $xml->attrs['type'] == 'subscribe') {
			if($this->auto_subscribe) {
				$this->send("<presence type='subscribed' to='{$xml->attrs['from']}' from='{$this->fulljid}' />");
				$this->send("<presence type='subscribe' to='{$xml->attrs['from']}' from='{$this->fulljid}' />");
			}
			$this->event('subscription_requested', $payload);
		} elseif(array_key_exists('type', $xml->attrs) and $xml->attrs['type'] == 'subscribed') {
			$this->event('subscription_accepted', $payload);
		} else {
			$this->event('presence', $payload);
		}
	}

	/**
	 * 特征处理
	 *
	 * @param string $xml
	 */
	protected function features_handler($xml) {
		if($xml->hasSub('starttls') and $this->use_encryption) {
			$this->send("<starttls xmlns='urn:ietf:params:xml:ns:xmpp-tls'><required /></starttls>");
		} elseif($xml->hasSub('bind') and $this->authed) {
			$id = $this->getId();
			$this->addIdHandler($id, 'resource_bind_handler');
			$this->send("<iq xmlns=\"jabber:client\" type=\"set\" id=\"$id\"><bind xmlns=\"urn:ietf:params:xml:ns:xmpp-bind\"><resource>{$this->resource}</resource></bind></iq>");
		} else {
			$this->log->log("Attempting Auth...");
			if ($this->password) {
			$this->send("<auth xmlns='urn:ietf:params:xml:ns:xmpp-sasl' mechanism='PLAIN'>" . base64_encode("\x00" . $this->user . "\x00" . $this->password) . "</auth>");
			} else {
              $this->send("<auth xmlns='urn:ietf:params:xml:ns:xmpp-sasl' mechanism='ANONYMOUS'/>");
			}	
		}
	}

	/**
	 * SASL 验证处理
	 *
	 * @param string $xml
	 */
	protected function sasl_success_handler($xml) {
		$this->log->log("Auth success!");
		$this->authed = true;
		$this->reset();
	}
	
	/**
	 * SASL 特征处理
	 *
	 * @param string $xml
	 */
	protected function sasl_failure_handler($xml) {
		$this->log->log("Auth failed!",  XMPPHP_Log::LEVEL_ERROR);
		$this->disconnect();
		
		throw new XMPPHP_Exception('Auth failed!');
	}

	/**
	 * 资源绑定处理
	 *
	 * @param string $xml
	 */
	protected function resource_bind_handler($xml) {
		if($xml->attrs['type'] == 'result') {
			$this->log->log("Bound to " . $xml->sub('bind')->sub('jid')->data);
			$this->fulljid = $xml->sub('bind')->sub('jid')->data;
			$jidarray = explode('/',$this->fulljid);
			$this->jid = $jidarray[0];
		}
		$id = $this->getId();
		$this->addIdHandler($id, 'session_start_handler');
		$this->send("<iq xmlns='jabber:client' type='set' id='$id'><session xmlns='urn:ietf:params:xml:ns:xmpp-session' /></iq>");
	}

	/**
	* 获取好友
	*
	*/
	public function getRoster() {
		$id = $this->getID();
		$this->send("<iq xmlns='jabber:client' type='get' id='$id'><query xmlns='jabber:iq:roster' /></iq>");
	}

	/**
	* 好友iq的处理
	* 获取所有信息包中的 XPath "iq/{jabber:iq:roster}query'
	*
	* @param string $xml
	*/
	protected function roster_iq_handler($xml) {
		$status = "result";
		$xmlroster = $xml->sub('query');
		foreach($xmlroster->subs as $item) {
			$groups = array();
			if ($item->name == 'item') {
				$jid = $item->attrs['jid']; //REQUIRED
				$name = $item->attrs['name']; //MAY
				$subscription = $item->attrs['subscription'];
				foreach($item->subs as $subitem) {
					if ($subitem->name == 'group') {
						$groups[] = $subitem->data;
					}
				}
				$contacts[] = array($jid, $subscription, $name, $groups); //Store for action if no errors happen
			} else {
				$status = "error";
			}
		}
		if ($status == "result") { //No errors, add contacts
			foreach($contacts as $contact) {
				$this->roster->addContact($contact[0], $contact[1], $contact[2], $contact[3]);
			}
		}
		if ($xml->attrs['type'] == 'set') {
			$this->send("<iq type=\"reply\" id=\"{$xml->attrs['id']}\" to=\"{$xml->attrs['from']}\" />");
		}
	}

	/**
	 * Session start 的处理
	 *
	 * @param string $xml
	 */
	protected function session_start_handler($xml) {
		$this->log->log("Session started");
		$this->session_started = true;
		$this->event('session_start');
	}

	/**
	 * TLS 执行处理
	 *
	 * @param string $xml
	 */
	protected function tls_proceed_handler($xml) {
		$this->log->log("Starting TLS encryption");
		stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_SSLv23_CLIENT);
		$this->reset();
	}

	/**
	* 获取 vcard
	*
	*/
	public function getVCard() {
		$this->vcard = null;
		$id = $this->getID();
		$this->addIdHandler($id, 'vcard_get_handler');
		$this->send("<iq type='get' id='$id'><vCard xmlns='vcard-temp' /></iq>");
	}
	
	/**
	* 获取好友 vcard
	*
	*/
	public function getRosterVCard($toJID) {
		$this->roster_vcard = null;
		$myJID = $this->getFullJID();
		$id = $this->getID();
		$this->addIdHandler($id, 'roster_vcard_get_handler');
		$this->send("<iq type='get' from='$myJID' to='$toJID' id='$id'><vCard xmlns='vcard-temp' /></iq>");
	}
	
	/**
	* vcard 获取处理
	*
	* @param XML Object $xml
	*/
	protected function vcard_get_handler($xml) {
		$vcard_array = array();
		$vcard = $xml->sub('vcard');
		foreach ($vcard->subs as $sub) {
			$vcard_array[$sub->name] = $sub->data;
			if ($sub->subs) {
				$vcard_array[$sub->name] = array();
				foreach ($sub->subs as $sub_child) {
					$vcard_array[$sub->name][$sub_child->name] = $sub_child->data;
				}
			} 
			$this->vcard = $vcard_array;
			$this->event('vcard_received');
		}
		/*
		$vcard_array['from'] = $xml->attrs['from'];
		$this->event('vcard', $vcard_array);
		*/
	}
	
	/**
	* 好友 vcard 获取处理
	*
	* @param XML Object $xml
	*/
	protected function roster_vcard_get_handler($xml) {
		$vcard_array = array();
		$vcard = $xml->sub('vcard');
		
		foreach ($vcard->subs as $sub) {
			$vcard_array[$sub->name] = $sub->data;
			if ($sub->subs) {
				$vcard_array[$sub->name] = array();
				foreach ($sub->subs as $sub_child) {
					$vcard_array[$sub->name][$sub_child->name] = $sub_child->data;
				}
			} 
			$this->vcard = $vcard_array;
			$this->event('roster_vcard_received');
		}
		/*
		$vcard_array['from'] = $xml->attrs['from'];
		$this->event('vcard', $vcard_array);
		*/
	}
	
	
	/**
	* 更新vcard 
	*
	* Below is an example of the array structure being passed for sending VCards
	* $vcard = array();
	* $vcard['photo'] = '图片路径';
	* $vcard['n'] = array('名' => 'Test', '姓' => 'User');
	* $vcard['nickname'] = '名称';
	* @param array $vcard
	*/
	public function sendVCard($vcard){
		if(!is_array($vcard)){
			return FALSE;
		}
		$id = $this->getID();
		$xml = "<iq type='set' id='$id'>";
		$xml .= "<vCard xmlns='vcard-temp'>";
		foreach(array_keys($vcard) as $key){
			$xml .= "<$key>";
			if(is_array($vcard[$key])){
				foreach(array_keys($vcard) as $child_key){
					$xml .= "<$child_key>" . $vcard[$key][$child_key] . "</$child_key>";
				}	
			}else{
				$xml .= $vcard[$key];
			}
			$xml .= "</$key>";
		}
		$xml .= "</vCard></iq>";
		$this->addIdHandler($id, 'vcard_set_handler');
		$this->send($xml);
	}
	
	/**
	* 保存vcard处理器
	* 
	* @param XML Object $xml
	*/
	protected function vcard_set_handler($xml){
		$this->event('vcard_saved');
	}
	
	/**
	* 更新头像
	* Depercated - 请使用setVCard(array)方法
	* @param string $myJID
	* @param string $image_source
	*/
	/*
    public function updateMyPhoto($myJID, $image_source){
		$id = $this->getID();
		$xml = "<presence from='$myJID'>";
		$xml .= "<x xmlns='vcard-temp:x:update'>";
		$xml .= "<photo>$image_source</photo>";
		$xml .= "</x>";
		$xml .= "</presence>";;
		$this->send($xml);
	}
	*/
	
	/**
	* 获取头像
	* Depercated - 请使用getVCard()方法
	* @param string $myJID
	*/
	/*
	public function getMyPhoto($myJID){
		$id = $this->getID();
		$xml = "<iq from='$myJID' type='get' id='$id'>";
		$xml .= "<vCard xmlns='vcard-temp'/>";
		$xml .= "</iq>";
		$this->addIdHandler($id, "vcard_get_handler");
		$this->send($xml);
	}
	
	*/
	
	/**
	* 获取他人头像
	* Depercated - 请使用getRosterVCard(string)方法
	* @param string $myJID
	* @param string $toJID
	*/
	/*
	public function getRosterPhoto($myJID, $toJID){
		$id = $this->getID();
		$xml = "<iq from='$myJID' to='$toJID' type='get' id='$id'>";
		$xml .= "<vCard xmlns='vcard-temp'/>";
		$xml .= "</iq>";
		$this->send($xml);
	}
	*/
	
	/**
	* 注册
	*
	* @param String $username
	* @param String $password
	* @param String $email
	* @param String $name
	*/
	public function register($username, $password, $email = NULL, $name = NULL){
		$id = $this->getID();
		$out = "<iq type='set' id='$id'>";
		$out .= "<query xmlns='jabber:iq:register'>";
		$out .= "<username>$username</username>";
		$out .= "<password>$password</password>";
		$out .= "<email>$email</email>";
		$out .= "<name>$name</name>";
		$out .= "</query>";
		$out .= "</iq>";
		$this->send($out);
	}

	 /**
	 * @param XML $xml
	 */
	 protected function add_roster_contact_handler($xml) {
	    $this->event('contact_added');
	 }
	 
	  /**
	  *
	  * @param XML $xml
	  */
	  protected function delete_roster_contact_handler($xml) {
	    $this->event('contact_removed');
	  }
	  
	  /**  
	  * 删除好友
	  * @param $jid  
	  */   
	  public function deleteRosterContact($myJID) {   
	 	 $myID = $this->getID(); 
		 $xml = "<iq type='set' id='$myID'>";   
		 $xml .= "<query xmlns='jabber:iq:roster'>";   
		 $xml .= "<item jid='" . $myJID . "' subscription='remove' />";   
		 $xml .= "</query>";   
		 $xml .= "</iq>";   
		 $this->addIdHandler($id, 'remove_roster_contact_handler');
		 $this->send($xml);  
	  }
	 
	  /**  
	  * 添加好友
	  * @param $jid  
	  *
	  * $subs - subscription
	  * none  - 表示用户和contact之前没有任何的关系（虽然在server的buddy list中存在）
	  * to    - 表示用户能看到contact的presence，但是contact看不到用户的Presence
	  * from  - 和to的含义相反，指用户看不到contact的presence，但是contact可以看到
	  * both  - 表示相关之间都能看到对方的presence
	  */ 
	  public function addRosterContact($toJID, $subs, $name, $group) {
	     if(!$toJID) return;
	     if (!$name) { $name = $toJID; }
	     $myID = $this->getID();
	     $xml = "<iq type='set' id='$myID'>";
	     $xml .= "<query xmlns='jabber:iq:roster'>";
	     $xml .= "<item jid='$toJID' name='$name' subscription='$subs'>";
	     $xml .= "<group>$group</group>";
	     $xml .= "</item>";
	     $xml .= "</query>";
	     $xml .= "</iq>";
	     $xml .= "<presence to='" . $toJID . "' type='subscribe'/>";
	     $this->addIdHandler($myID, 'add_roster_contact_handler');
	     $this->send($xml); 
	  }
}
