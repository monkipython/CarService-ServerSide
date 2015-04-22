<?php
namespace App\Controller;
use Think\Controller;

class CommonController extends Controller{

	/**
	 * 获取商家id 所拥有的子项目name 
	 * @param int $merchant_id
	 * @return string
	 */
	static function getMerchantServerListName($merchant_id){
		
		$db = M('Service');
		$data = $db ->table(C('DB_PREFIX').'service as a ')->field('b.name')
		->join(C('DB_PREFIX').'category as b on a.cat_id = b.id')->where("a.merchant_id = $merchant_id")
		->group('a.cat_id')->limit(5)->select();
		if($data){
			foreach ($data as $row){
				$string[] = $row['name'];
			}
		   $string  = implode(',',$string);
		}
		return empty($string)?'':$string;
	}
	
	/**
	 * 获取商家服务id的array
	 * @param int $merchant_id
	 * @return 商家服务id array
	 */	
	static  public function getServerListByMer($merchant_id){
		$db = M('Service');
		$data =$db ->where(array('merchant_id'=>$merchant_id,'effect'=>1))->field('cat_id')->group('cat_id')->select();
		foreach ($data as $row){
			$string[] = $row['cat_id'];
		}

		return $string;
	
	}
  /**
   * 获取指定项目的项目名，已都好形式隔开
   * @param array $category_ids
   * @return string
   */
	static  function getCategoryNames($category_ids){
		$db = M('Category');
		$date = $db->where(array('id'=>array('in',$category_ids)))->field('name')->select();
		
		foreach ($date as $key =>$row){
			$name [] = $row['name'];
		}
		$name = implode('、', $name);
		return $name;
	}
	/**
	 * 用户确认需求 更新需求操作
	 * @param int $demand_id
	 * @param int $merchant_id
	 */
	static public function order_done($demand_id,$merchant_id){
		$db = M('MemberDemand');
		$data = $db ->where(array('id'=>$demand_id))->save(array('merchant_id'=>$merchant_id,'status'=>1));
		return $data;
	}
	/**
	 * 获取指定商家 的经纬度
	 * @param unknown_type $id
	 */
    static public function getMerchantPosition($id){
    	$db = M('Merchant');
    	$data = $db ->where(array('id'=>$id))->field('province_id,city_id,area_id')->find();
    	
    	return $data;
    }
    /**
     * 注册用户唯一标识 绑定在注册用户和注册商户之前
     * 每秒1/8999 的概率出错，出错后提示重新提交一次即可
     * @param int $sub_id
     * @param int $type // 0 普通用户 2商户
     */
    static public function BeforeRegisterUser($sub_id,$name,$header,$phone,$type=0){
    	$db  = M('SystemUser');
//     	$userToken = time().rand(1000,9999);
    	$add ['type'] = $type;
    	$add ['name'] = $name;
    	$add ['header'] = $header;
    	$add ['sub_id'] = $sub_id;
    	$add ['phone'] = $phone;
    	$data = $db ->add ($add);
    	if($data == false){
    		return false;
    	}else{
    		$db  = M('AnswerUser');
    		$rel = $db->add(array('system_user_id'=>$data));
    		if($rel){
    			$xmpp = new \App\Model\XmppApiModel();
    			$xmpp ->register($data,$data,$name,null);
    			if(!empty($header)){
	    			$xmpp = new \App\Model\XmppApiModel($data,$data);
	    			$xmpp->updataHeader($header);
    			}
    			return true;
    		}else{
    			return false;
    		}
    		
    			
    	}
    }
    /**
     * 登录获取jid
     */
    static public function getJid($id,$type){
    	$db  = M('SystemUser');
    	$data = $db ->where("type=$type and sub_id=$id")->getField('id');
    	return $data;
    } 
    static public function updateUnique($jid,$device,$devNum){
    	$db = M('SystemUser');
    	
    }
    /**
     * 修改用户名 密码 更新system——user 表
     * 
     */
    static public function saveHeader($sub_id,$type,$header){
    	$db  = M('SystemUser');
    	$id = $db ->where(array('type'=>$type,'sub_id'=>$sub_id))->getField('id');
    	$data = $db ->where(array('id'=>$id))->save(array('header'=>$header));
    	set_time_limit(20);
    	$xmpp = new \App\Model\XmppApiModel($id,$id);
    	$xmpp->updataHeader($header);
//     	dump('123');
    	return $data;
    }
    /**
     * 修改header 更新system——user 表
     */
    static public function saveName($sub_id,$type,$name){
    	$db  = M('SystemUser');
    	$id = $db ->where(array('type'=>$type,'sub_id'=>$sub_id))->getField('id');
    	$data = $db ->where(array('id'=>$id))->save(array('name'=>$name));
    	$rel = M('');
    	$sql= "update ofUser set name ='$name' where username = $id ";
    	$row = $rel->db(1,"mysql://".C('DB_USER').":".C('DB_PWD')."@localhost:3306/chatDB")->execute($sql);
    	return $data;
    }
    
    
    /**
     * 验证system 表
     * @param int $id
     * @param int $type
     */
    static public function  getSystemUserid($id,$type,$errorLevel = 1){
    	$id = M('SystemUser')->where(array('type'=>$type,'sub_id'=>$id))->getField('id');
    	if($errorLevel == 1){
	    	if($id === false){
	    		die(json_encode(array('code'=>26,'systemid 查询出错')));
	    	}
	    	if(!$id){
	    		die(json_encode(array('code'=>27,'systemid 不存在')));
	    	}
	    	return $id;
    	}else{
    		return 0;
    	}
    }
    function test(){
//     	$xmpp = new \App\Model\XmppApiModel();
//     	$xmpp ->register(0,0,'123',null);
    	 
//     	$xmpp = new \App\Model\XmppApiModel(0,0);
//     	$xmpp->updataHeader('');
    	
    }

}
?>