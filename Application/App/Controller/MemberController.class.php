<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;

/**
 * 菜单栏目
 */
class MemberController extends Controller {
	
	private $jsonUtils;
	private $dao;
	private $session_handle; // session 处理类
	private $session_dao;
	function _initialize(){
		$ver = !empty($_POST['ver'])?htmlspecialchars($_POST['ver']):'';
		//ver 为空 则不路由，即用原始版本接口
		if(in_array($ver, C('VER_ARRAY'))){
			//在使用版本内 执行路由 反之原始版本接口
			$functionName = ACTION_NAME.$ver;
			if(method_exists($this, $functionName)){
				$this->$functionName();
				die();
			}
				
		}
	}
	public function __construct() {
		
		$this->jsonUtils = new \Org\Util\JsonUtils ();
		$this->session_handle = new \Org\Util\SessionHandle ();
		$this->dao = M ( 'Member' );
		$this->session_dao = M ( 'Member_session' );
		parent::__construct();
	
	}
	/**
	 * 用户注册
	 */
	public function register() {
		$password = isset ( $_POST ['password'] ) ? htmlspecialchars ( $_POST ['password'] ) : '';
		$mobile = isset ( $_POST ['mobile'] ) ? htmlspecialchars ( $_POST ['mobile'] ) : '';
		$code_verify = isset ( $_POST ['code_verify'] ) ? htmlspecialchars ( $_POST ['code_verify'] ) : '';
		if (empty ( $mobile )) {
			$this->jsonUtils->echo_json_msg ( 4, '手机号码为空！' );
			exit ();
		}
		$mobile_exits = $this->dao->where ( "mobile='$mobile'" )->select ();
		if ($mobile_exits) {
			$this->jsonUtils->echo_json_msg ( 1, '此手机已经注册过...' );
			exit ();
		}
		$sms = new \Org\Util\Sms ();
		if (empty ( $_POST ['session_id'] )) {
			$msg = $sms->send_sms ( $mobile, 1, 0 );
			if ($msg ['code'] == 2) {
				$this->jsonUtils->echo_json_data ( 0, '成功发送短信，请查收..', $msg ['session_id'] );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 1, $msg ['msg'] );
				exit ();
			}
		} else {
			if (empty ( $_POST ['session_id']  )) {
				$this->jsonUtils->echo_json_msg ( 4, 'session_id为空...' );
				exit ();
			}
			$code = $sms->getVerifyCode ( $_POST ['session_id'] ,$mobile);
			if (empty ( $password )) {
				$this->jsonUtils->echo_json_msg ( 4, '密码为空...' );
				exit ();
			}
			if ($code == $code_verify) {
				$data ["pwd"] = md5 ( $password );
				$data ["mobile"] = $mobile;
				$data ["nick_name"] = substr_replace($mobile,'****',3,4);
				$data ['add_time'] = time ();
				$result = $this->dao->add ( $data );
				if ($result) {
					CommonController::BeforeRegisterUser($result, $mobile,'',$mobile);
					$this->jsonUtils->echo_json_msg ( 0, '注册成功');
					exit ();
				} else {
					$this->jsonUtils->echo_json_msg ( 1, '注册失败！' );
					exit ();
				}
			} else {
				$this->jsonUtils->echo_json_msg ( 1, '验证码错误或者验证码过期...' );
				exit ();
			}
		}
	
	}
	/**
	 * 用户登录
	 * type = 0 标识 用户
	 * type = 2 标识 商户
	 */
	public function login() {
		$password = isset ( $_POST ['password'] ) ? htmlspecialchars ( $_POST ['password'] ) : '';
		$mobile = isset ( $_POST ['mobile'] ) ? htmlspecialchars ( $_POST ['mobile'] ) : '';

		if (empty ( $mobile ) || empty ( $password )) {
			$this->jsonUtils->echo_json_msg ( 4, '用户名或者密码为空...' );
			exit ();
		}
	//	$map ['nick_name'] = $mobile;
		$map ['mobile'] = $mobile;
	//	$map ['_logic'] = 'or';
		$is_exits = $this->dao->where ( $map )->select ();
		if (! $is_exits) {
			$this->jsonUtils->echo_json_msg ( 1, '用户名或者密码错误' );
			exit ();
		}
		$password = md5 ( $password );
		$arr = $this->dao->query ( "select id,nick_name,header from " . C ( 'DB_PREFIX' ) . "member where  mobile='$mobile' and pwd='$password'" );
		if ($arr) {
			$member_id = $arr [0] ['id'];
			
			$jid = CommonController::getJid($member_id, 0);
			if(empty($jid)){
				$this->jsonUtils->echo_json_msg(4, 'jid出错');
			}
			$s_arr = $this->session_dao->where ( "type=0 and userid=$member_id" )->find ();
			if ($s_arr) { // 是否登录过
				$data ['member_session_id'] = $s_arr ['sessionid'];
				
				$data ['jid'] = $jid;
				$data ['header'] = imgUrl($arr[0]['header']);
				$data ['name'] = $arr[0]['nick_name'];
				$this->session_handle->save($s_arr['sessionid']);
				$this->jsonUtils->echo_json_data ( 0, 'ok', $data );
				exit ();
			} else {
				session_start ();
				$session_id = session_id () . time ();
				session_destroy ();
				$this->session_handle->write ( $member_id, $session_id, '', 0 ); // 记录
                // 用户登录id
                // $data['login_times']=array('exp','login_times+1');
                // $data['point']=array('exp','point+1');
                // $this->dao->where("id=$member_id")->save($data);//增加积分和记录登录次数
				$json_data ['header'] = imgUrl($arr[0]['header']);
				$json_data ['name'] = $arr[0]['nick_name'];
				$json_data ['member_session_id'] = $session_id;
				$json_data ['jid'] = $jid;
			}
			
			$this->jsonUtils->echo_json_data ( 0, 'ok', $json_data );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '用户名或者密码错误...' );
			exit ();
		}
	
	}
	/**
	 * 用户登出
	 */
	public function loginout() {
		$session_id = $_POST ['member_session_id'];
		if (empty ( $session_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '会员会话iD为空' );
			exit ();
		}
		$result = $this->session_handle->destroy ( $session_id );
		if ($result) {
			$this->jsonUtils->echo_json_msg ( 0, '退出成功!' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '退出失败！' );
			exit ();
		}
	
	}
	
	/**
	 * 用户上传修改头像
	 */
	public function uploadHeader() {
		$member_session_id = $_REQUEST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id,1 );
		if ($_FILES) {
			$result = mul_upload ( '/Header/' ,3);
			
			if ($result) {
				$data ['header'] = $result [0];
				$id = $member_id['id'];
				$this->dao->where ( "id=$id" )->save ( $data );
				CommonController::saveHeader($member_id['id'], $member_id['type'], $result [0]);
				$this->jsonUtils->echo_json_msg ( 0, '上传成功！' );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 1, '上传失败！' );
				exit ();
			}
		}
	}
	/**
	 * 获取简单会员详情
	 */
	public function get_short_member() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id ,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$arr = $this->dao->field ( "nick_name,header,mobile" )->where ( array('id'=>$member_id['id']) )->find ();
		$arr ['header'] = imgUrl ( $arr  ['header'] );
		$arr ['system_user_id']  = $systemid;
		if ($arr) {
			$this->jsonUtils->echo_json_data ( 0, 'ok', $arr );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '获取个人信息失败...' );
			exit ();
		}
	
	}
	/**
	 * 获取会员全部详情
	 */
	public  function get_member(){
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$arr = $this->dao->field ( "nick_name,header,mobile,signature,driving_exp,gender" )->where ( "id=$member_id" )->find ();
		$arr['header'] = imgUrl ( $arr ['header'] );
		if($arr){
			$this->jsonUtils->echo_json_data ( 0, 'ok', $arr );
			exit ();
		}else{
			$this->jsonUtils->echo_json_msg ( 1, '获取个人信息失败...' );
			exit ();
		}
		
	}
	/**
	 * 修改会员信息
	 */
	public function mod_member() {
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id ,1);
		$nick_name = isset ( $_POST ['nick_name'] ) ? htmlspecialchars ( $_POST ['nick_name'] ) : '';
		$email = isset ( $_POST ['email'] ) ? htmlspecialchars ( $_POST ['email'] ) : '';
		$signature = isset ( $_POST ['signature'] ) ? htmlspecialchars ( $_POST ['signature'] ) : '';
		$driving_exp = isset ( $_POST ['driving_exp'] ) ? htmlspecialchars ( $_POST ['driving_exp'] ) : '';
		$gender = isset ( $_POST ['gender'] ) ? htmlspecialchars ( $_POST ['gender'] ) : '';
		if (empty ( $nick_name )) {
			$this->jsonUtils->echo_json_msg ( 4, '用户昵称为空...' );
			exit ();
		}
		/*
		 * if(empty($email)){
		 * $this->JsonUtils->echo_json_msg(4,'email为空...');exit(); }
		 */
		$data ['nick_name'] = $nick_name;
		if (! empty ( $email )) {
			$data ['email'] = $email;
		}
		if (! empty ( $signature )) {
			$data ['signature'] = $signature;
		}
		if (! empty ( $driving_exp )) {
			$data ['driving_exp'] = $driving_exp;
		}
		if (isset ( $gender )) {
			$data ['gender'] = (int)$gender;
		}
		
		if ($_FILES) {
			$img = mul_upload ( '/Header/' ,3);
				
			if ($img) {
				$data ['header'] = $img [0];
			}
		}	
		$result = $this->dao->where ( "id={$member_id['id']}" )->save ( $data );
		if ($result === false ) {
		
			$this->jsonUtils->echo_json_msg ( 1, '修改失败' );
			exit ();
		} else {
			if(!empty($img[0])){
				CommonController::saveHeader($member_id['id'], $member_id['type'], $img [0]);
			}
			CommonController::saveName($member_id['id'], $member_id['type'], $nick_name);
			$this->jsonUtils->echo_json_msg ( 0, '修改成功!' );
			exit ();
		} 
	
	}

	/**
	 * 用户反馈
	 */
	public function feedback() {
		$content = isset ( $_POST ['content'] ) ? htmlspecialchars ( $_POST ['content'] ) : '';
		
		if (empty ( $content )) {
			$this->jsonUtils->echo_json_msg ( 4, '反馈内容为空...' );
			exit ();
		}
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$data ['member_id'] = $member_id;
		$data ['status'] = 0;
		$data ['type'] = 0;
		$data ['addtime'] = time ();
		$data ['content'] = $content;
		$complain = M ( "Feedback" );
		$result = $complain->add ( $data );
		if ($result) {
			$this->jsonUtils->echo_json_msg ( 0, '反馈成功...' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '反馈失败...' );
			exit ();
		}
	
	}
	/**
	 * 举报分类
	 */
	public function report_category(){
		$arr = array(
				array('1'=>'色情'),
				array('2'=>'暴力'),
				array('3'=>'反动'),
				array('4'=>'广告'),
				array('5'=>'骚扰信息'),
				array('6'=>'资料不当'),
				array('7'=>'侵犯版权'),
				array('8'=>'其他')
		);
		$this->jsonUtils->echo_json_data(0,'ok', $arr);exit();
	
	}
// 	public function report_category(){
// 		$arr = array(
// 				'色情',
// 				'暴力',
// 				'反动',
// 				'广告',
// 				'骚扰信息',
// 				'资料不当',
// 				'侵犯版权',
// 				'其他'
// 				);
// 		$this->jsonUtils->echo_json_data(0,'ok', $arr);exit();
		
// 	}
	/**
	 * 举报  system_user_id
	 * type 1 色情 2暴力 3 发动 4 广告 5 骚扰信息 6资料不当 7 侵犯版权 8 其他
	 * 
	 * position 1 举报问题 2 举报问题回复 3举报问题评论 4举报遇见 5 举报遇见的评论 6举报个人 
	 * obj_id  1 问题id 2 回复id 3评论id 4 遇见id 5遇见评论id 6 system_user_id
	 */
	public function report(){
		$session_id = isset($_POST['session_id'])?htmlspecialchars($_POST['session_id']):'';
		$member_id = $this->session_handle->getsession_userid($session_id,1);
		$systemid = CommonController::getSystemUserid($member_id['id'],$member_id['type']);
		$position =  isset($_POST['position'])?htmlspecialchars($_POST['position']):'';
		$type =  isset($_POST['type'])?htmlspecialchars($_POST['type']):'';//
		$obj_id =  isset($_POST['obj_id'])?htmlspecialchars($_POST['obj_id']):'';
		if (empty ( $position )) {
			$this->jsonUtils->echo_json_msg ( 4, '举报来源为空...' );
			exit ();
		}
		if (empty ( $type )) {
			$this->jsonUtils->echo_json_msg ( 4, '举报分类为空...' );
			exit ();
		}
		if (empty ( $obj_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '举报对象为空...' );
			exit ();
		}
		
		
		$db = M('Report');
		$unique = $db->where(array('reporter'=>$systemid,'position'=>$position,'obj_id'=>$obj_id))->getField('id');
		if($unique){
			$this->jsonUtils->echo_json_msg(0, '你已举报过');exit();
		}else{
			$data = $db->add(array('reporter'=>$systemid,'position'=>$position,'obj_id'=>$obj_id,'type'=>$type,'addtime'=>time()));
			if($data){
				$this->jsonUtils->echo_json_msg(0, '举报成功');exit();
			}else{
				$this->jsonUtils->echo_json_msg(1, '举报失败');exit();
			}
		}
		
	}
	/**
	 * 修改手机号码
	 */
	public function modUsername() {
		
		$orgmobile = isset ( $_POST ['orgname'] ) ? htmlspecialchars ( $_POST ['orgname'] ) : '';
		$orgcode_verify = isset ( $_POST ['orgcode_verify'] ) ? htmlspecialchars ( trim ( $_POST ['orgcode_verify'] ) ) : '';
		$username = isset ( $_POST ['username'] ) ? htmlspecialchars ( trim ( $_POST ['username'] ) ) : '';
		$now_code_verify = isset ( $_POST ['now_code_verify'] ) ? htmlspecialchars ( trim ( $_POST ['now_code_verify'] ) ) : '';
		
		if (empty ( $orgmobile )) {
			$this->jsonUtils->echo_json_msg ( 1, '第一次手机号码为空' );
			exit ();
		}
		$checkMobile = $this->dao->where ( array (
				'mobile' => $orgmobile 
		) )->getField ( 'id' );
		if (! $checkMobile) {
			$this->jsonUtils->echo_json_msg ( 4, $orgmobile . "该手机号未注册" );
			exit ();
		}
		$sms = new \Org\Util\Sms ();
		if (empty ( $_POST ['session_id'] )) {
			
			$msg = $sms->send_sms ( $orgmobile, 2, 0 );
			// Log::write(json_encode($_SESSION),'1');
			if ($msg ['code'] == 2) {
				$this->jsonUtils->echo_json_data ( 0, '第一次成功发送短信，请查收..', $msg ['session_id'] );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 3, $msg ['msg'] );
				exit ();
			}
		
		} else {
			// 兼容ios和andriod 切换session
			// if(empty($_SESSION['code_verify'])){
			// session_unset();
			// session_destroy();
			// session_id($_POST['session_id']);
			// session_start();
			// }
			$code = $sms->getVerifyCode ( $_POST ['session_id'] ,$orgmobile);
			if (empty ( $orgcode_verify )) {
				
				$this->jsonUtils->echo_json_msg ( 4, '第一次验证码为空...' );
				exit ();
			}
			if ($code == $orgcode_verify) {
				if (empty ( $username )) {
					$this->jsonUtils->echo_json_msg ( 5, '第二次手机号码为空' );
					exit ();
				}
				// 检测手机号是否已被注册
				$checkMobile = $this->dao->where ( array (
						'mobile' => $username 
				) )->getField ( 'id' );
				if ($checkMobile) {
					
					$this->jsonUtils->echo_json_msg ( 6, $username . "该手机号已注册" );
					exit ();
				}
				
				if (empty ( $_POST ['now_session_id'] )) {
					
					$msg = $sms->send_sms ( $username, 2, 0 );
					// Log::write(json_encode($_SESSION),'2');
					if ($msg ['code'] == 2) {
						$this->jsonUtils->echo_json_data ( 0, '第二次成功发送短信，请查收..', $msg ['session_id'] );
						exit ();
					} else {
						$this->jsonUtils->echo_json_msg ( 7, $msg ['msg'] );
						exit ();
					}
				}
				if (empty ( $now_code_verify )) {
					
					$this->jsonUtils->echo_json_msg ( 8, '第二次验证码为空...' );
					exit ();
				}
				$new_code = $sms->getVerifyCode ( $_POST ['now_session_id'],$username );
				if ($new_code == $now_code_verify) {
					$rel = $this->dao->where ( array (
							'mobile' => $orgmobile 
					) )->save ( array (
							'mobile' => $username 
					) );
					if ($rel === false) {
						$this->jsonUtils->echo_json_msg ( 9, '修改用户名失败' );
						exit ();
					} else {
						
						$this->jsonUtils->echo_json_msg ( 0, '修改用户名成功' );
						exit ();
					}
				} else {
					$this->jsonUtils->echo_json_msg ( 10, "手机号：" . $username . "的验证码失效或者过期" );
					exit ();
				}
			
			} else {
				$this->jsonUtils->echo_json_msg ( 11, "手机号：" . $orgmobile . "的验证码失效或者过期" );
				exit ();
			}
		
		}
	
	}
	/**
	 * 修改密码
	 */
	public function modPasswordByNor() {
		$member_session_id = isset ( $_POST ['member_session_id'] ) ? htmlspecialchars ( trim ( $_POST ['member_session_id'] ) ) : '';
		$oldpwd = isset ( $_POST ['oldpwd'] ) ? htmlspecialchars ( trim ( $_POST ['oldpwd'] ) ) : '';
		$newpwd = isset ( $_POST ['newpwd'] ) ? htmlspecialchars ( trim ( $_POST ['newpwd'] ) ) : '';
		$renewpwd = isset ( $_POST ['renewpwd'] ) ? htmlspecialchars ( trim ( $_POST ['renewpwd'] ) ) : '';
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		
		if (empty ( $oldpwd )) {
			$this->jsonUtils->echo_json_msg ( 4, '请输入旧密码' );
			exit ();
		}
		if (empty ( $newpwd )) {
			$this->jsonUtils->echo_json_msg ( 4, '请输入新密码' );
			exit ();
		}
		if (empty ( $renewpwd )) {
			$this->jsonUtils->echo_json_msg ( 4, '请输入重复密码' );
			exit ();
		}
		$data = $this->dao->where ( array (
				'id' => $member_id 
		) )->getField ( 'pwd' );
		if ($newpwd != $renewpwd) {
			$this->jsonUtils->echo_json_msg ( 4, '两次密码不一致' );
			exit ();
		}
		if ($data == md5 ( $oldpwd )) {
			$rel = $this->dao->where ( array (
					'id' => $member_id 
			) )->save ( array (
					'pwd' => md5 ( $newpwd ) 
			) );
			if ($rel) {
				$this->jsonUtils->echo_json_msg ( 0, '修改成功' );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 4, '修改失败' );
				exit ();
			}
		} else {
			$this->jsonUtils->echo_json_msg ( 4, '密码错误' );
			exit ();
		}
	
	}
	/**
	 * 找回密码
	 */
	public function modPassword() {
		$mobile = isset ( $_POST ['username'] ) ? htmlspecialchars ( $_POST ['username'] ) : '';
		$code_verify = isset ( $_POST ['code_verify'] ) ? htmlspecialchars ( trim ( $_POST ['code_verify'] ) ) : '';
		$password = isset ( $_POST ['password'] ) ? htmlspecialchars ( trim ( $_POST ['password'] ) ) : '';
		$repassword = isset ( $_POST ['repassword'] ) ? htmlspecialchars ( trim ( $_POST ['repassword'] ) ) : '';
		
		if (empty ( $mobile )) {
			$this->jsonUtils->echo_json_msg ( 4, '手机号码为空' );
			exit ();
		}
		$checkMobile = $this->dao->where ( array (
				'mobile' => $mobile 
		) )->getField ( 'id' );
		if (! $checkMobile) {
			$this->jsonUtils->echo_json_msg ( 4, '该手机号未注册' );
			exit ();
		}
		$sms = new \Org\Util\Sms ();
		if (empty ( $_POST ['session_id'] )) {
			
			$msg = $sms->send_sms ( $mobile, 2, 0 );
			if ($msg ['code'] == 2) {
				$this->jsonUtils->echo_json_data ( 0, '成功发送短信，请查收..', $msg ['session_id'] );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 1, $msg ['msg'] );
				exit ();
			}
		
		} else {
			if (empty ( $code_verify )) {
				
				$this->jsonUtils->echo_json_msg ( 43, '验证码为空...' );
				exit ();
			}
			$code = $sms->getVerifyCode ( $_POST ['session_id'] ,$mobile);
			if ($code == $code_verify) {
				if ($password == $repassword) {
					$dat = $this->dao->where ( array (
							'mobile' => $mobile 
					) )->save ( array (
							'pwd' => md5 ( $password ) 
					) );
					
					if ($dat === false) {
						$this->jsonUtils->echo_json_msg ( 4, '修改密码失败' );
						exit ();
					} else {
						
						$this->jsonUtils->echo_json_msg ( 0, '修改密码成功' );
						exit ();
					}
				} else {
					$this->jsonUtils->echo_json_msg ( 4, '两次输入密码不一致' );
					exit ();
				}
			} else {
				$this->jsonUtils->echo_json_msg ( 4, '验证码失效或者过期' );
				exit ();
			}
		
		}
	
	}
	/**
	 * 诚信记录
	 */
	public function honestyRecord(){
		$member_session_id = $_REQUEST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$page = isset ( $_POST ['page'] ) ? htmlspecialchars ( $_POST ['page'] ) : '1';
		$num = isset ( $_POST ['num'] ) ? htmlspecialchars ( $_POST ['num'] ) : '6';
		
		$db = M('Order');
		$doneCount = $db ->where(array('member_id'=>$member_id,'status'=>1))->count();
		$failedCount = $db ->where(array('member_id'=>$member_id,'status'=>2))->count();
		$data = $db->table(C('DB_PREFIX')."order as a ")->field('a.order_no,a.addtime,b.header,b.merchant_name,a.service_name,a.status,a.total_price')
		->join(C('DB_PREFIX')."merchant as b on a.merchant_id = b.id")
		->where("a.member_id = $member_id and  a.status = 2")->limit($num)->page($page)->order("a.addtime desc")->select();
		if($data===false){
			$this->jsonUtils->echo_json_msg ( 4, '系统出错' );
			exit ();
		}else{
			if(!empty($data)){
				 foreach ($data as $key =>$row){
				 	$data[$key]['header'] =imgUrl($row['header']);
				 	$data[$key]['addtime'] = date("Y-m-d H:i:s",$row['addtime']);
				 }
			}else{
				$data =array();
			}
			 $arr['list'] = $data;
			 $honesty = number_format(100-ceil(($failedCount/($failedCount+$doneCount)*100)),0);
			 $arr['success'] = $doneCount;
			 $arr['failed'] = $failedCount;
			 $arr['honesty'] = $honesty;
			 
			 $this->jsonUtils->echo_json_data(0, 'ok', $arr);
			 exit();
		}
	}
	
	/**
	 * 用户注册
	 */
// 	public function register_admin() {
// 		$password = isset ( $_POST ['password'] ) ? htmlspecialchars ( $_POST ['password'] ) : '';
// 		$mobile = isset ( $_POST ['mobile'] ) ? htmlspecialchars ( $_POST ['mobile'] ) : '';
// 		$ver = 'sadaih32409485hsjao012308opa7845a';
// 		if($_POST['ver'] != $ver) die('123');
// 		dump($_POST);
// 		if (empty ( $mobile )) {
// 			$this->jsonUtils->echo_json_msg ( 4, '手机号码为空！' );
// 			exit ();
// 		}
// 		$mobile_exits = $this->dao->where ( "mobile='$mobile'" )->find ();
// 		if ($mobile_exits) {
// 			$this->jsonUtils->echo_json_msg ( 1, '此手机已经注册过...' );
// 			exit ();
// 		}
// 			if (empty ( $password )) {
// 				$this->jsonUtils->echo_json_msg ( 4, '密码为空...' );
// 				exit ();
// 			}
// 				$data ["pwd"] = md5 ( $password );
// 				$data ["mobile"] = $mobile;
// 				$data ["nick_name"] = substr_replace($mobile,'****',3,4);
// 				$data ['add_time'] = time ();
// 				$result = $this->dao->add ( $data );
// 				if ($result) {
// 					CommonController::BeforeRegisterUser($result, $mobile,'',$mobile);
// 					$this->jsonUtils->echo_json_msg ( 0, '注册成功');
// 					exit ();
// 				} else {
// 					$this->jsonUtils->echo_json_msg ( 1, '注册失败！' );
// 					exit ();
// 				}
			
		
	
// 	}
	
	/**
	 * 商户注册
	 */
	
// 	public function register_Mer() {
// 		$username = isset ( $_POST ['username'] ) ? htmlspecialchars ( trim ( $_POST ['username'] ) ) : '';
// 		$password = isset ( $_POST ['password'] ) ? htmlspecialchars ( trim ( $_POST ['password'] ) ) : '';
// 		if (empty ( $username )) {
// 			$this->jsonUtils->echo_json_msg ( 1, '请输入账号!' );
// 			exit ();
// 		} else {
// 			if (! preg_match ( '|^\d{11}$|', $username )) {
// 				$this->jsonUtils->echo_json_msg ( 2, '手机号码不符合格式!' );
// 				exit ();
// 			}
// 		}
// 		$isexist = M('Merchant')->where ( array (
// 				'mobile' => $username
// 		) )->getField ( 'id' );
	
// 		if ($isexist) {
// 			$this->jsonUtils->echo_json_msg ( 5, "$username,已经注册过!" );
// 			exit ();
// 		}
// 			if (empty ( $password )) {
// 				$this->jsonUtils->echo_json_msg ( 44, '密码为空!' );
// 				exit ();
// 			}
// 			if (! preg_match ( '|^[0-9a-zA-z]{6,16}$|', $password )) {
// 				$this->jsonUtils->echo_json_msg ( 5, '请输入6-16位数字和字母密码!' );
// 				exit ();
// 			}
			
		
// 				$data ['mobile'] = $username;
// 				$data ['merchant_name'] = substr_replace($username,'****',3,4);
// 				$data ['pwd'] = md5 ( $password );
// 				$data ['addtime'] = time ();
// 				$data ['modtime'] = time ();
// 				$result =  M('Merchant')->add ( $data );
	
// 				if ($result) {
// 					CommonController::BeforeRegisterUser($result, $username, '',$username,2);
// 					$this->jsonUtils->echo_json_data ( 0, '注册成功！', $result );
// 					exit ();
// 				} else {
// 					$this->jsonUtils->echo_json_msg ( 1, '注册失败！' );
// 					exit ();
// 				}
			
// 			}
	
	
	
	
	
	/**
	 * 获取会员简短详情
 	 */
	public  function get_short_memberV2(){
		$member_session_id = $_POST ['member_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$arr = $this->dao->table(C('DB_PREFIX')."member as a")->field ( "a.id,a.nick_name,a.header,e.brand_icon,b.name as brand_name" )
		->join(C('DB_PREFIX')."cart as c on (c.member_id = a.id and c.default_cart = 1)",'LEFT')
		->join(C('DB_PREFIX')."car_brand as b on b.id = c.brand_id",'LEFT')
		->join(C('DB_PREFIX')."system_user as e on (e.sub_id = a.id and e.type = 0)",'LEFT')
		->where ( "a.id=$member_id" )->find ();
		$arr['header'] = imgUrl ( $arr ['header'] );
		$arr['brand_name'] =!empty ( $arr ['brand_name'] )?$arr['brand_name']:'';
		$arr['brand_icon'] = imgUrl ( $arr ['brand_icon'] );
		if($arr){
			$this->jsonUtils->echo_json_data ( 0, 'ok', $arr );
			exit ();
		}else{
			$this->jsonUtils->echo_json_msg ( 1, '获取个人信息失败...' );
			exit ();
		}
	
	}
	
	

}

?>