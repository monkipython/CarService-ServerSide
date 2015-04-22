<?php
/**
* session写入数据库
*/
namespace Org\Util;
class SessionHandle {
	
	 protected $lifeTime=604800 ,$dao;


     function __construct(&$params='') {
		$this->lifeTime = C('EXPIRE_TIME') ?  C('EXPIRE_TIME') : 24*3600*30;
		//$this->sessionid = substr(MD5(session_id()), 0, 32);

		$this->dao = M('member_session');
       
		//$this->write($this->sessionid);
		//$this->gc(0);
    }

     function open($savePath, $sessName) {
       return true; 
    } 

    function close() { 
	   return $this->gc($this->lifetime);
   } 

    function read($sessID) { 
	   $r = $this->dao->find($sessID);
		return $r ? $r['data'] : '';
   } 

    function write($userid,$sessID,$sessData,$type=0) {
		
		$sessiondata = array(
                'userid'=>$userid,//ptlogin2.qq.com/jump?uin=554573404&skey=@Cg9ON5SLM&u1=http%3A%2F%2Fuser.qzone.qq.com%2F554573404%2Finfocenter%3Fqz_referrer%3Dqqtipsid,
				'sessionid'=>$sessID,
				'session_data'=>$sessData,
				'session_expires'=>time()+$this->lifeTime,//保存一个月
				'adddotime'=>time(),
				'type'=>$type,
		);

		 $result=$this->dao->add($sessiondata);
		 return $result;
		 
   } 
   function save ($session_id){
   		$result=$this->dao->where(array('sessionid'=>$session_id))->save(array('session_expires'=>time()+$this->lifeTime));
   		return $result;
   }

 
    function destroy($sessID) { 
	   $arr = $this->dao->delete($sessID);

	   
	   return $arr;
   } 

//     function gc($sessMaxLifeTime) { 
// 	   $expiretime = time() -$sessMaxLifeTime; 
// 		$r =  $this->dao->where(" session_expires < $expiretime")->delete();
// 		return $r;
//    } 


    /**
     * 根据sessioniD获取对应登录的会员ID
     * @param  [type] $session_id [description]
     * @return [type]  $type      [description] 0获取养护对应的 uid 1 获取聊天的uid
     */
     function getsession_userid($session_id,$type=0){

        if(empty($session_id)){
           $data['code']=4;
           $data['msg']='请先登录';
           echo json_encode($data);
             exit();
         }
        $s_arr=$this->dao->where(array('sessionid'=>$session_id))->field('userid,session_expires,type')->find();
        
        $member_id=$s_arr['userid'];
         if(empty($member_id) || time() > $s_arr['session_expires']){
            $data['code']=2;
            $data['msg']='请先登录';
            echo json_encode($data);die();
        }
        $this->save($session_id);
       	if($type ==0 ){
       		return $member_id;
       	}else{
//        		$db = M('ChatUser');
//        		$data = $db ->where(array('type'=>$s_arr['type'],'sub_id'=>$s_arr['userid']))->getField('id');
       	
//        		if(empty($data)){
//        			$data['code']=24;
//        			$data['msg']='系统聊天关联不存在';
//        			echo json_encode($data);exit();
//        		}
//        		return $data;
			$arr['id'] = $s_arr['userid'];
			$arr['type'] = $s_arr['type'];
			return $arr;
       	}
      
    }



}

?>