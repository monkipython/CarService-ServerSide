<?php
namespace App\Controller;
use Think\Log;
use Think\Controller;
use Think\Model;

/**
 * 商家控制类
 */
class MerchantController extends Controller {
	
	private $jsonUtils;
	private $dao;
	private $service_dao;
	private $session_handle; // session 处理类
	private $session_dao;
	private $activity_dao;
	
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
		$this->dao = M ( 'merchant' );
		$this->service_dao = M ( 'service' );
		$this->session_dao = M ( 'member_session' );
		$this->activity_dao = M ( 'activity' );
		parent::__construct();
	}
	
	/**
	 * 商家注册
	 * 
	 * @return [type] [description]
	 */
	public function register() {
		$username = isset ( $_POST ['username'] ) ? htmlspecialchars ( trim ( $_POST ['username'] ) ) : '';
		$password = isset ( $_POST ['password'] ) ? htmlspecialchars ( trim ( $_POST ['password'] ) ) : '';
		if (empty ( $username )) {
			$this->jsonUtils->echo_json_msg ( 1, '请输入账号!' );
			exit ();
		} else {
			if (! preg_match ( '|^\d{11}$|', $username )) {
				$this->jsonUtils->echo_json_msg ( 2, '手机号码不符合格式!' );
				exit ();
			}
		}
		$isexist = $this->dao->where ( array (
				'mobile' => $username 
		) )->getField ( 'id' );
		
		if ($isexist) {
			$this->jsonUtils->echo_json_msg ( 5, "$username,已经注册过!" );
			exit ();
		}
		$sms = new \Org\Util\Sms ();
		if (empty ( $_POST ['session_id'] )) {
			$msg = $sms->send_sms ( $username, 1 ,2);
			
			if ($msg ['code'] == 2) {
				$this->jsonUtils->echo_json_data ( 0, '成功发送短信，请查收..', $msg ['session_id'] );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 1, $msg ['msg'] );
				exit ();
			}
		
		} else {
		
			$code_verify = isset ( $_POST ['code_verify'] ) ? htmlspecialchars ( trim ( $_POST ['code_verify'] ) ) : '';
			if (empty ( $code_verify )) {
				$this->jsonUtils->echo_json_msg ( 43, '验证码为空...' );
				exit ();
			}
			if (empty ( $password )) {
				$this->jsonUtils->echo_json_msg ( 44, '密码为空!' );
				exit ();
			}
			if (! preg_match ( '|^[0-9a-zA-z]{6,16}$|', $password )) {
				$this->jsonUtils->echo_json_msg ( 5, '请输入6-16位数字和字母密码!' );
				exit ();
			}

			$code = $sms->getVerifyCode($_POST['session_id'],$username);
			if ($code== $code_verify) {
				$data ['mobile'] = $username;
				$data ['merchant_name'] = substr_replace($username,'****',3,4);
				$data ['pwd'] = md5 ( $password );
				$data ['addtime'] = time ();
				$data ['modtime'] = time ();
				$data ['status'] = 0;
				$result = $this->dao->add ( $data );
				
				if ($result) {
					CommonController::BeforeRegisterUser($result, $username, '',$username,2);
					$this->jsonUtils->echo_json_data ( 0, '注册成功！', $result );
					exit ();
				} else {
					$this->jsonUtils->echo_json_msg ( 1, '注册失败！' );
					exit ();
				}
			} else {
				$this->jsonUtils->echo_json_msg ( 8, '验证码错误或者已过期...' );
				exit ();
			}
		
		}
	
	}
	
	/**
	 * 商家登录
	 * 
	 * @return [type] [description]
	 */
	public function login() {
		$username = isset ( $_POST ['username'] ) ? htmlspecialchars ( trim  ($_POST ['username'] ) ): '';
		$password = isset ( $_POST ['password'] ) ? htmlspecialchars ( trim ( $_POST ['password'] ) ) : '';
		if (empty ( $username )) {
			$this->jsonUtils->echo_json_msg ( 1, '用户名为空' );
			exit ();
		}
		if (empty ( $password )) {
			$this->jsonUtils->echo_json_msg ( 4, '密码为空' );
			exit ();
		}
// 		if (! preg_match ( '|^\d{11}$|', $username )) {
// 			$this->jsonUtils->echo_json_msg ( 2, '手机号码不符合格式!' );
// 			exit ();
// 		}
		if (! preg_match ( '|^[0-9a-zA-z]{6,16}$|', $password )) {
			$this->jsonUtils->echo_json_msg ( 5, '请输入6-16位数字和字母密码!' );
			exit ();
		}
		$password = md5 ( $password );
		//$condition ['merchant_name'] = $username;
		$condition ['mobile'] = $username;
	//	$condition ['_logic'] = 'OR';
		$arr = $this->dao->where ( $condition )->getField ( 'id' );
		if (! $arr) {
			$this->jsonUtils->echo_json_msg ( 3, '输入的手机号未注册!' );
			exit ();
		}
		$result = $this->dao->query ( "select id as merchant_id,mobile,header,merchant_name,is_salesman,is_check,status from " . C ( 'DB_PREFIX' ) . "merchant where   (mobile='$username' and pwd='$password' ) " );
		
		if ($result) {
			$result [0] ['header'] = imgUrl ( $result [0] ['header'] );
			
			if (! $result [0] ['is_check']) {
				$this->jsonUtils->echo_json_msg ( 7, '账号未激活，请联系我们' );
				die ();
			}
			if ($result [0] ['status'] == -1) {
				$this->jsonUtils->echo_json_msg ( 4, '账号已被封停，请联系我们' );
				die ();
			}
			if ($result [0] ['is_salesman']) {
				$loginType = 1; // 业务端
			} else {
				$loginType = 2; // 商务端
			}
			
			$id = $result [0] ['merchant_id']; // 商家ID
			
			$s_arr = $this->session_dao->where ( "userid=$id and type = $loginType" )->find ();
			if($loginType == 2 ||$loginType ==  1){
				$jid = CommonController::getJid($id, $loginType);
				if($loginType == 1 && empty($jid)){
					$jid = '';
				}else{
					if(empty($jid)){
						$this->jsonUtils->echo_json_msg(4, 'jid出错');exit();
					}
				}
			}
			if ($s_arr) {
				
				$result [0] ['mer_session_id'] = $s_arr ['sessionid'];
				$result [0] ['jid'] = $jid;
				$this->session_handle->save($s_arr['sessionid']);
				// $_SESSION['merchant']=$result;
				$this->jsonUtils->echo_json_data ( 0, '已经登录!', $result[0] );
				exit ();
			} else {
				//session_start ();
				$session_id = session_id () . time ();
				//session_destroy ();
				$this->session_handle->write ( $id, $session_id, '', $loginType ); // session保存数据库
				$result [0] ['mer_session_id'] = $session_id;
				$result [0] ['jid'] = $jid;
				// $_SESSION['merchant']=$result;
				$this->jsonUtils->echo_json_data ( 0, '登录成功!', $result[0] );
				exit ();
			}
		
		} else {
			$this->jsonUtils->echo_json_msg ( 6, '用户名或者密码错误！' );
			exit ();
		}
	
	}
	/**
	 * 修改商务密码
	 */
	public function modPassword() {
		$mobile = isset ( $_POST ['username'] ) ? htmlspecialchars ( $_POST ['username'] ) : '';
		$code_verify = isset ( $_POST ['code_verify'] ) ? htmlspecialchars ( trim ( $_POST ['code_verify'] ) ) : '';
		$password = isset ( $_POST ['password'] ) ? htmlspecialchars ( trim ( $_POST ['password'] ) ) : '';
		$repassword = isset ( $_POST ['repassword'] ) ? htmlspecialchars ( trim ( $_POST ['repassword'] ) ) : '';
		if (empty ( $mobile )) {
			$this->jsonUtils->echo_json_msg ( 1, '手机号码为空' );
			exit ();
		}
		if (! preg_match ( '|^\d{11}$|', $mobile )) {
			$this->jsonUtils->echo_json_msg ( 2, '手机号码不符合格式!' );
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
			
			$msg = $sms->send_sms ( $mobile, 2,2 );
			if ($msg ['code'] == 2) {
				$this->jsonUtils->echo_json_data ( 0, '成功发送短信，请查收..', $msg ['session_id'] );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 1, $msg ['msg'] );
				exit ();
			}
		
		} else {
			if (! preg_match ( '|^[0-9a-zA-z]{6,16}$|', $password )) {
				$this->jsonUtils->echo_json_msg ( 5, '请输入6-16位数字和字母密码!' );
				exit ();
			}
			$code = $sms->getVerifyCode($_POST['session_id'],$mobile);
			if (empty ( $code_verify )) {
				
				$this->jsonUtils->echo_json_msg ( 43, '验证码为空...' );
				exit ();
			}
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
					$this->jsonUtils->echo_json_msg ( 9, '两次输入密码不一致' );
					exit ();
				}
			} else {
				$this->jsonUtils->echo_json_msg ( 8, '验证码失效或者过期' );
				exit ();
			}
		
		}
	
	}
	
	/**
	 * 修改手机号
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
			
			$msg = $sms->send_sms ( $orgmobile, 2,2);
			// Log::write(json_encode($_SESSION),'1');
			if ($msg ['code'] == 2) {
				$this->jsonUtils->echo_json_data ( 0, '第一次成功发送短信，请查收..', $msg ['session_id'] );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 3, $msg ['msg'] );
				exit ();
			}
		
		} else {
			if (empty ( $orgcode_verify )) {
				
				$this->jsonUtils->echo_json_msg ( 4, '第一次验证码为空...' );
				exit ();
			}
			$code = $sms->getVerifyCode($_POST['session_id'],$orgmobile);
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
					
					$msg = $sms->send_sms ( $username, 2, 2 );
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
				$new_code = $sms->getVerifyCode($_POST['now_session_id'],$username);
				if ( $new_code == $now_code_verify) {
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
	 * 正常修改密码
	 */
	public function modPasswordByNor() {
		$mer_session_id = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( trim ( $_POST ['mer_session_id'] ) ) : '';
		$oldpwd = isset ( $_POST ['oldpwd'] ) ? htmlspecialchars ( trim ( $_POST ['oldpwd'] ) ) : '';
		$newpwd = isset ( $_POST ['newpwd'] ) ? htmlspecialchars ( trim ( $_POST ['newpwd'] ) ) : '';
		$renewpwd = isset ( $_POST ['renewpwd'] ) ? htmlspecialchars ( trim ( $_POST ['renewpwd'] ) ) : '';
		
		if (empty ( $oldpwd )) {
			$this->jsonUtils->echo_json_msg ( 4, '旧密码为空' );
			exit ();
		}
		if (empty ( $newpwd )) {
			$this->jsonUtils->echo_json_msg ( 3, '新密码为空' );
			exit ();
		}
		if (empty ( $renewpwd )) {
			$this->jsonUtils->echo_json_msg ( 4, '重复新密码为空' );
			exit ();
		}
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		$data = $this->dao->where ( array (
				'id' => $merchant_id 
		) )->getField ( 'pwd' );
		
		if ($data) {
			if ($data == md5 ( $oldpwd )) {
				if ($newpwd == $renewpwd) {
					$re = $this->dao->where ( array (
							'id' => $merchant_id 
					) )->save ( array (
							'pwd' => md5 ( $newpwd ) 
					) );
					if ($re) {
						$this->jsonUtils->echo_json_msg ( 0, 'ok' );
						exit ();
					} else {
						$this->jsonUtils->echo_json_msg ( 7, '修改失败' );
						exit ();
					}
				} else {
					$this->jsonUtils->echo_json_msg ( 6, '两次密码不一致' );
					exit ();
				}
			} else {
				$this->jsonUtils->echo_json_msg ( 5, '密码错误' );
				exit ();
			}
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '账户不存在' );
			exit ();
		}
	}
	
	// 用户反馈
	public function feedback() {
		$content = $_POST ['content'];
		if (empty ( $content )) {
			$this->jsonUtils->echo_json_msg ( 4, '反馈内容为空...' );
			exit ();
		}
		$member_session_id = $_POST ['mer_session_id'];
		$member_id = $this->session_handle->getsession_userid ( $member_session_id );
		$data ['merchant_id'] = $member_id;
		$data ['status'] = 0;
		$data ['type'] = 2;
		$data ['addtime'] = time ();
		$data ['content'] = $content;
		$complain = M ( "feedback" );
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
	 * 商家退出
	 * 
	 * @return [type] [description]
	 */
	public function loginout() {
		$session_id = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( trim ( $_POST ['mer_session_id'] ) ) : '';
		
		if (empty ( $session_id )) {
			$this->jsonUtils->echo_json_msg ( 4, '商家会话iD为空' );
			exit ();
		}
		$result = $this->session_handle->destroy ( $session_id );
		if ($result ===false) {
			$this->jsonUtils->echo_json_msg ( 1, '退出失败！' );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 0, '退出成功!' );
			exit ();
		}
	
	}
	
	/**
	 * 商家详情
	 * 
	 * @return [type] [description]
	 */
	public function getMerchant() {
		$mer_session_id = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( trim ( $_POST ['mer_session_id'] ) ) : '';
		$merchant_id = isset ( $_POST ['merchant_id'] ) ? htmlspecialchars ( $_POST ['merchant_id']) : '';
		if (empty ( $merchant_id )) {
			if (! empty ( $mer_session_id )) {
				$merchant = $this->session_handle->getsession_userid ( $mer_session_id ,1);
				$merchant_id  =$merchant['id'];
				
			} else {
				$this->jsonUtils->echo_json_msg ( 6, '商家id为空' );
			}
		}
		$arr = $this->dao->field ( " id as merchant_id,wifi_enable,merchant_name,header,manager,business_time,tel,intro,address,pics,collect_count,comment_count,business_time,mobile,area_id " )->where ( "id=$merchant_id" )->select ();
		if ($arr) {
			$systemid = CommonController::getSystemUserid($merchant_id,2);
			if ($arr [0] ['pics']) {
				$json_obj = json_decode ( $arr [0] ['pics'],true );
				$arr [0] ['pics'] = imgUrl ( $json_obj );
			}
			$arr [0] ['header'] = imgUrl ( $arr [0] ['header'] );
			
			$star = MerchantController::getMerCommentStar ( $merchant_id );
			
			$arr [0] ['system_user_id'] = $systemid;
			$arr [0] ['service_quality'] = $star ['service_quality'];
			$arr [0] ['service_attitude'] = $star ['service_attitude'];
			$arr [0] ['merchant_setting'] = $star ['merchant_setting'];
			
			$this->jsonUtils->echo_json_data ( 0, "ok", $arr [0] );
			exit ();
		} else {
			$this->jsonUtils->echo_json_msg ( 1, '获取商家信息错误！' );
			exit ();
		}
	
	}
	/**
	 * 获取商家评星等级，avg()
	 * 
	 * @param int $merchant_id        	
	 * @return array();
	 */
	static public function getMerCommentStar($merchant_id) {
		$comment = M ( 'comment' );
		$c_arr = $comment->query ( "select avg(service_attitude) as service_attitude,avg(service_quality) as service_quality,avg(merchant_setting) as merchant_setting  from  " . C ( 'DB_PREFIX' ) . "comment where merchant_id=$merchant_id and type = 0" );
		$arr ['service_quality'] = empty ( $c_arr [0] ['service_quality'] ) ? '0.0' : number_format ( $c_arr [0] ['service_quality'], 1 );
		$arr ['service_attitude'] = empty ( $c_arr [0] ['service_attitude'] ) ? '0.0' : number_format ( $c_arr [0] ['service_attitude'], 1 );
		$arr ['merchant_setting'] = empty ( $c_arr [0] ['merchant_setting'] ) ? '0.0' : number_format ( $c_arr [0] ['merchant_setting'], 1 );
		
		return $arr;
	}
	/**
	 * 获取商家店名
	 * 
	 * @param int $merchant_id        	
	 */
	static public function getMerName($merchant_id) {
		$db = M ( 'Merchant' );
		$data = $db->where ( array (
				'id' => $merchant_id 
		) )->getField ( 'merchant_name' );
		return $data;
	}
	/**
	 */
	
	/**
	 * 商家修改
	 * 
	 * @return [type] [description]
	 */
	public function modMerchant() {
		$style = isset ( $_POST ['style'] ) ? htmlspecialchars ( trim ( $_POST ['style'] ) ) : '0';
		$mer_session_id = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( trim ( $_POST ['mer_session_id'] ) ) : '';
		if($style == 0){
			$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		}else{
			$mobile = isset ( $_POST ['moblie'] ) ? htmlspecialchars ( $_POST ['moblie'] ) : '';
			//根据 手机号 session_id 验证是否能编辑商户
			$ver = M('Verifycode')->where(array('mobile'=>$mobile,'session_id'=>$mer_session_id))->getField('expire');
			if($ver >= time()){
				$merchant_id = $this->dao->where(array('mobile'=>$mobile))->getField('id');
			}else{
				$this->jsonUtils->echo_json_msg(4, '注册后半小时，无法在编辑资料');exit();
			}
		}
		$wifi_enable = isset ( $_POST ['wifi_enable'] ) ? htmlspecialchars ( trim ( $_POST ['wifi_enable'] ) ) : '';
		$tel = isset ( $_POST ['tel'] ) ? htmlspecialchars ( trim ( $_POST ['tel'] ) ) : '';
		$area_id = isset ( $_POST ['area_id'] ) ? htmlspecialchars ( trim ( $_POST ['area_id'] ) ) : '';
		$device = isset ( $_POST ['device'] ) ? htmlspecialchars ( trim ( $_POST ['device'] ) ) : '';
		$intro = isset ( $_POST ['intro'] ) ? htmlspecialchars ( trim ( $_POST ['intro'] ) ) : '';
		$address = isset ( $_POST ['address'] ) ? htmlspecialchars ( trim ( $_POST ['address'] ) ) : '';
		$business_time = isset ( $_POST ['business_time'] ) ? htmlspecialchars ( trim ( $_POST ['business_time'] ) ) : '';
		$mod_img = isset ( $_POST ['mod_img'] ) ? htmlspecialchars ( trim ( $_POST ['mod_img'] ) ) : '0';
		if (! empty ( $tel )) {
			$data ['tel'] = $tel;
		}
		if (! empty ( $intro )) {
			$data ['intro'] = $intro;
		}
		if (! empty ( $address )) {
			$data ['address'] = $address;
		}
		if (! empty ( $business_time )) {
			$data ['business_time'] = $business_time;
		}
		if (isset ( $wifi_enable )) {
			$data ['wifi_enable'] = $wifi_enable;
		}
		if(!empty($area_id) && $device =='web' ){
			$area = CityController::getAreaIdPreId ( $area_id );
			$data ['province_id'] = $area ['province'];
			$data ['city_id'] = $area ['city'];
			$data ['area_id'] = $area_id;
		}
		if($mod_img){
			if ($_FILES) {
				$f_arr = mul_upload ( '/Merchant/',1 );
				if ($f_arr) {
					$data ['pics'] = json_encode ( $f_arr ); // 把多张图片数组格式转json保存数据库
				}
					
			}else{
					$data ['pics'] = "[]";
			}
		}
		
		
		$result = $this->dao->where ( "id=$merchant_id" )->save ( $data );
		
		$this->jsonUtils->echo_json_msg ( 0, '修改成功！' );
	
	}
	
	/**
	 * 商家头像上传
	 * 
	 * @return [type] [description]
	 */
	public function merchantHeader() {
		$mer_session_id = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( trim ( $_POST ['mer_session_id'] ) ) : '';
		$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id,1 );
		
		if ($_FILES) {
			$result = mul_upload ( '/Header/',3 );
// 			Log::write(json_encode($result),'ERR');
			if ($result) {
				$data ['header'] = $result [0];
				$id = $merchant_id['id'];
				$this->dao->where ( array('id'=>$merchant_id['id']) )->save ( $data );
				CommonController::saveHeader($merchant_id['id'], $merchant_id['type'], $result [0]);
				$header = imgUrl($result[0]);
				$this->jsonUtils->echo_json_data ( 0, '上传成功！',array('header'=>$header) );
				exit ();
			} else {
				$this->jsonUtils->echo_json_msg ( 1, '上传失败！' );
				exit ();
			}
		}else{
			$this->jsonUtils->echo_json_msg ( 1, '无文件上传！' );
			exit ();
		}
	
	}
	
	/*******************************************************************************************
	* Ver V2
	* @第二版本接口
	******************************************************************************************/
	/**
	 * 商家修改
	 *
	 * @return [type] [description]
	 */
	public function modMerchantV2() {
		$style = isset ( $_POST ['style'] ) ? htmlspecialchars ( trim ( $_POST ['style'] ) ) : '0';//为了web能修改资料
		$mer_session_id = isset ( $_POST ['mer_session_id'] ) ? htmlspecialchars ( trim ( $_POST ['mer_session_id'] ) ) : '';
		if($style == 0){
			$merchant_id = $this->session_handle->getsession_userid ( $mer_session_id );
		}else{
			$mobile = isset ( $_POST ['moblie'] ) ? htmlspecialchars ( $_POST ['moblie'] ) : '';
			//根据 手机号 session_id 验证是否能编辑商户
			$ver = M('Verifycode')->where(array('mobile'=>$mobile,'session_id'=>$mer_session_id))->getField('expire');
			if($ver >= time()){
				$merchant_id = $this->dao->where(array('mobile'=>$mobile))->getField('id');
			}else{
				$this->jsonUtils->echo_json_msg(4, '注册后半小时，无法在编辑资料');exit();
			}
		}
		$wifi_enable = isset ( $_POST ['wifi_enable'] ) ? htmlspecialchars ( trim ( $_POST ['wifi_enable'] ) ) : '0';
		$tel = isset ( $_POST ['tel'] ) ? htmlspecialchars ( trim ( $_POST ['tel'] ) ) : '';
		$area_id = isset ( $_POST ['area_id'] ) ? htmlspecialchars ( trim ( $_POST ['area_id'] ) ) : '';
		$device = isset ( $_POST ['device'] ) ? htmlspecialchars ( trim ( $_POST ['device'] ) ) : '';
		$intro = isset ( $_POST ['intro'] ) ? htmlspecialchars ( trim ( $_POST ['intro'] ) ) : '';
		$address = isset ( $_POST ['address'] ) ? htmlspecialchars ( trim ( $_POST ['address'] ) ) : '';
		$business_time = isset ( $_POST ['business_time'] ) ? htmlspecialchars ( trim ( $_POST ['business_time'] ) ) : '';
		$pics = isset ( $_POST ['pics'] ) ?  ( trim ( $_POST ['pics'] ) ) : '';
		if (! empty ( $tel )) {
			$data ['tel'] = $tel;
		}
		if (! empty ( $intro )) {
			$data ['intro'] = $intro;
		}
		if (! empty ( $address )) {
			$data ['address'] = $address;
		}
		if (! empty ( $business_time )) {
			$data ['business_time'] = $business_time;
		}
		if (isset ( $wifi_enable )) {
			$data ['wifi_enable'] = $wifi_enable;
		}
		if(!empty($area_id) && $device =='web' ){
			$area = CityController::getAreaIdPreId ( $area_id );
			$data ['province_id'] = $area ['province'];
			$data ['city_id'] = $area ['city'];
			$data ['area_id'] = $area_id;
		}
		Log::write($pics,'ERR');
		if(!empty($pics)){
			$arr_decode = json_decode($pics,true);
			if(!empty($arr_decode)){
				foreach ($arr_decode as $key =>$row){
					if(!empty($row)){
						$temp[$key]['hs'] = str_replace(C('ROOT_UPLOADS'), '', $row['hs']);
						$temp[$key]['hb'] = str_replace(C('ROOT_UPLOADS'), '', $row['hb']); 
					}				
				}
				foreach ($temp as $row){
					$data['pics'][] = $row;
				}
			}else{
				$data['pics'] = array();
			}
		}else{
			$data['pics'] = array();
		}
		if ($_FILES) {
			$f_arr = mul_upload ( '/Merchant/',1 );
			if ($f_arr) {
				$data ['pics'] =array_merge($data['pics'],$f_arr); // 把多张图片数组格式转json保存数据库
			}
				
		}
		$data['pics'] = json_encode($data['pics']);
	
	
		$result = $this->dao->where ( "id=$merchant_id" )->save ( $data );
	
		$this->jsonUtils->echo_json_msg ( 0, '修改成功！' );exit();
	
	}
	
	


}
?>