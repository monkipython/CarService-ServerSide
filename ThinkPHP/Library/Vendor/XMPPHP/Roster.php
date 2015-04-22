<?php

/**
 * 关注设置
 */
//已删除
define("SUBSCRIBE_REMOVE", "remove");
//无关注
define("SUBSCRIBE_NONE", "none");
//关注谁
define("SUBSCRIBE_TO", "to");
//被关注
define("SUBSCRIBE_FROM", "from");
//互相关注
define("SUBSCRIBE_BOTH", "both");

/**
 * Roster
 */
class Roster {
	/**
	 * Roster array, 处理联系人和状态.  使用jid查询.
	 * 数组中包含两个值一是 contact 联系人 和 presence 状态
	 * @var array
	 */
	protected $roster_array = array();
	/**
	 * Constructor
	 * 
	 */
	public function __construct($roster_array = array()) {
		if ($this->verifyRoster($roster_array)) {
			$this->roster_array = $roster_array; //拿到已关注的用户
		} else {
			$this->roster_array = array();
		}
	}

	/**
	 * 检测Roster Array是否有效结构（如果为空还是“有效”的）
	 *
	 * @param array $roster_array
	 */
	protected function verifyRoster($roster_array) {
		return True;
	}

	/**
	 *
	 * 添加友好到Roster
	 *
	 * @param string $jid
	 * @param string $subscription
	 * @param string $name
	 * @param array $groups
	 */
		public function addContact($jid, $subscription, $name='', $groups=array("frds")) {
		$contact = array('jid' => $jid, 'subscription' => $subscription, 'name' => $name, 'groups' => $groups);
		if ($this->isContact($jid)) {
			$this->roster_array[$jid]['contact'] = $contact;
		} else {
			$this->roster_array[$jid] = array('contact' => $contact);
		}
	}
	

	
	
	/**
	 *
	 * 互相关注请求
	 * @param String $send_jid
	 * @param String $receive_jid
	 * @param String $send_name
	 * @param String $receive_name
	 */
	 public function subscribed($send_jid, $receive_jid, $send_name="", $receive_name=""){
		 $out = "<presence from='$send_jid' to='$receive_jid' type='subscribed'>";
		 $out .= "<nick xmlns='http://jabber.org/protocol/nick'>$send_name</nick>";
		 $out .= "</presence>";
		 $out .= "<presence from='$receive_jid' to='$send_jid' type='subscribed'>";
		 $out .= "<nick xmlns='http://jabber.org/protocol/nick'>$receive_name</nick>";
		 $out .= "</presence>";
		 return $out;
	 }
	 
	 /**
	 *
	 * 互相取消关注请求
	 * @param String $send_jid
	 * @param String $receive_jid
	 * @param String $send_name
	 * @param String $receive_name
	 */
	 public function unsubscribed($send_jid, $receive_jid, $send_name="", $receive_name=""){
		 $out = "<presence from='$send_jid' to='$receive_jid' type='unsubscribed'>";
		 $out .= "<nick xmlns='http://jabber.org/protocol/nick'>$send_name</nick>";
		 $out .= "</presence>";
		 $out .= "<presence from='$receive_jid' to='$send_jid' type='unsubscribed'>";
		 $out .= "<nick xmlns='http://jabber.org/protocol/nick'>$receive_name</nick>";
		 $out .= "</presence>";
		 return $out;
	 }
	 
	/**
	 * 
	 * 获取联系人JID
	 *
	 * @param string $jid
	 */
	public function getContact($jid) {
		if ($this->isContact($jid)) {
			return $this->roster_array[$jid]['contact'];
		}
	}

	/**
	 *
	 * Discover if a contact exists in the roster via jid
	 *
	 * @param string $jid
	 */
	public function isContact($jid) {
		return (array_key_exists($jid, $this->roster_array));
	}

	/**
	 *
	 * 设置状态
	 *
	 * @param string $presence
	 * @param integer $priority
	 * @param string $show
	 * @param string $status
	*/
	public function setPresence($presence, $priority, $show, $status) {
		list($jid, $resource) = explode("/", $presence);
		if ($show != 'unavailable') {
			if (!$this->isContact($jid)) {
				$this->addContact($jid, 'not-in-roster');
			}
			$resource = $resource ? $resource : '';
			$this->roster_array[$jid]['presence'][$resource] = array('priority' => $priority, 'show' => $show, 'status' => $status);
		} else { 
			unset($this->roster_array[$jid]['resource'][$resource]);
		}
	}

	/*
	 *
	 * 返回好友状态
	 *
	 * @param string $jid
	 */
	public function getPresence($jid) {
		$split = split("/", $jid);
		$jid = $split[0];
		if($this->isContact($jid)) {
			$current = array('resource' => '', 'active' => '', 'priority' => -129, 'show' => '', 'status' => ''); 
			//Priorities can only be -128 = 127
			foreach($this->roster_array[$jid]['presence'] as $resource => $presence) {
				//Highest available priority or just highest priority
				if ($presence['priority'] > $current['priority'] and (($presence['show'] == "chat" or $presence['show'] == "available") or ($current['show'] != "chat" or $current['show'] != "available"))) {
					$current = $presence;
					$current['resource'] = $resource;
				}
			}
			return $current;
		}
	}
	
	/**
	 *
	 * 获取 Roster
	 *
	 */
	public function getRoster() {
		return $this->roster_array;
	}
	 
}
?>
