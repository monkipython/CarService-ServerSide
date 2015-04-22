<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {
	function _initialize(){
		parent::_initialize();
	}
    public function index(){
    	$model = M('city');
    	$province = $model->where('pid=0')->getField('id,name');
    	$this->assign("province", $province);
        $this->display();
    }
    public function getcity() {
    	$id = $_REQUEST['id'];
    	$model = M('city');
    	$city = $model->where('pid=' . $id)->getField('id,name');
    	$this->ajaxReturn($city, "JSON");
    }
 
    public function download(){
    	$this->display();
    }
    public function AboutUs(){
    	$this->display();
    }
    public function ContactUs(){
    	$this->display();
    }
    public function down(){
    	
    	header("Content-type:text/html;charset=utf-8");
    	if( empty($_GET['File'])){
    		echo'<script> alert("非法连接 !"); location.replace ("index.php") </script>'; exit();
    	}
    	$file_name=$_GET['File'];
    	if   (!file_exists($file_name))   {   //检查文件是否存在
    		echo   "文件找不到";
    		exit;
    	}   else   {
    		$file = fopen($file_name,"r"); // 打开文件
    		// 输入文件标签
    		Header("Content-type: application/octet-stream");
    		Header("Accept-Ranges: bytes");
    		Header("Accept-Length: ".filesize( $file_name));
    		Header("Content-Disposition: attachment; filename=" . $file_name);
    		// 输出文件内容
    		echo fread($file,filesize( $file_name));
    		fclose($file);
    		exit();
    	}
    
    }
    public function project(){
	    $pages = new \Org\Util\Paginator('10','p');
    	$pages->set_total(100); 
    	$paginate = $pages->page_links();
    	$this->assign('pages',$paginate);
    	$this->display("Activity/project");
    }
    
    public function addProject(){
	    $this->display("Activity/addProject");
    }
    
    public function updateProject(){
	    $this->display("Activity/updateProject");
    }
    


  
    public function orderIncomplete(){
	    $date = date("Y-m-d H:i:s");
    	$pages = new \Org\Util\Paginator('10','p');
    	$pages->set_total(100); 
    	$paginate = $pages->page_links();
    	$this->assign('date',$date);
    	$this->assign('pages',$paginate);
	    $this->display("OrderHistory/incomplete");
    }
    
    public function orderIncompleteDetail(){
	    $date = date("Y-m-d H:i:s");
    	$this->assign('date',$date);
	    $this->display("OrderHistory/incompleteDetail");
    }
    
    public function orderFail(){
	    $date = date("Y-m-d H:i:s");
    	$pages = new \Org\Util\Paginator('10','p');
    	$pages->set_total(100); 
    	$paginate = $pages->page_links();
    	$this->assign('date',$date);
    	$this->assign('pages',$paginate);
	    $this->display("OrderHistory/fail");
    }
    
    public function orderFailDetail(){
	    $date = date("Y-m-d H:i:s");
    	$this->assign('date',$date);
	    $this->display("OrderHistory/failDetail");
    }
    
    public function orderComplete(){
	    $date = date("Y-m-d H:i:s");
    	$pages = new \Org\Util\Paginator('10','p');
    	$pages->set_total(100); 
    	$paginate = $pages->page_links();
    	$this->assign('date',$date);
    	$this->assign('pages',$paginate);
	    $this->display("OrderHistory/complete");
    }
    
    public function orderCompleteDetail(){
	    $date = date("Y-m-d H:i:s");
    	$this->assign('date',$date);
	    $this->display("OrderHistory/completeDetail");
    }
    
    public function event(){
	    $date = date("Y-m-d")." ～ ".date("Y-m-d",strtotime("2014-11-30"));
    	$pages = new \Org\Util\Paginator('10','p');
    	$pages->set_total(100); 
    	$paginate = $pages->page_links();
    	$this->assign('date',$date);
    	$this->assign('pages',$paginate);
	    $this->display("EventHistory/event");
    }
    
    public function activeEvent(){
	    $date = date("Y-m-d")." ～ ".date("Y-m-d",strtotime("2014-11-30"));
    	$pages = new \Org\Util\Paginator('10','p');
    	$pages->set_total(100); 
    	$paginate = $pages->page_links();
    	$this->assign('date',$date);
    	$this->assign('pages',$paginate);
	    $this->display("EventHistory/activeEvent");
    }
    
    public function inActiveEvent(){
	    $date = date("Y-m-d")." ～ ".date("Y-m-d",strtotime("2014-11-30"));
    	$pages = new \Org\Util\Paginator('10','p');
    	$pages->set_total(100); 
    	$paginate = $pages->page_links();
    	$this->assign('date',$date);
    	$this->assign('pages',$paginate);
	    $this->display("EventHistory/inActiveEvent");
    }
    
    public function completeEvent(){
	    $date = date("Y-m-d")." ～ ".date("Y-m-d",strtotime("2014-11-30"));
    	$pages = new \Org\Util\Paginator('10','p');
    	$pages->set_total(100); 
    	$paginate = $pages->page_links();
    	$this->assign('date',$date);
    	$this->assign('pages',$paginate);
	    $this->display("EventHistory/completeEvent");
    }
    
    public function addEvent(){
	    $this->display("EventHistory/addEvent");
    }
    
    public function eventDetail(){
    	$date = date("Y-m-d")." ～ ".date("Y-m-d",strtotime("2014-11-30"));
    	$this->assign('date',$date);
	    $this->display("EventHistory/eventDetail");
    }
    
    public function message(){
    	$pages = new \Org\Util\Paginator('10','p');
    	$pages->set_total(100); 
    	$paginate = $pages->page_links();
    	$date = date("Y-m-d H:i:s");
    	$this->assign('date',$date);
    	$this->assign('pages',$paginate);
	    $this->display("MessageHistory/main");
    }
    
    public function messageRead(){
    	$pages = new \Org\Util\Paginator('10','p');
    	$pages->set_total(100); 
    	$paginate = $pages->page_links();
    	$date = date("Y-m-d H:i:s");
    	$this->assign('date',$date);
    	$this->assign('pages',$paginate);
	    $this->display("MessageHistory/read");
    }
    
    public function messageUnread(){
    	$pages = new \Org\Util\Paginator('10','p');
    	$pages->set_total(100); 
    	$paginate = $pages->page_links();
    	$date = date("Y-m-d H:i:s");
    	$this->assign('date',$date);
    	$this->assign('pages',$paginate);
	    $this->display("MessageHistory/unread");
    }
    
    public function comment(){
	    $pages = new \Org\Util\Paginator('10','p');
    	$pages->set_total(100); 
    	$paginate = $pages->page_links();
    	$date = date("Y-m-d H:i:s");
    	$this->assign('date',$date);
    	$this->assign('pages',$paginate);
	    $this->display("CommentHistory/fromBuyer");
    }
    
    public function commentTo(){
	    $pages = new \Org\Util\Paginator('10','p');
    	$pages->set_total(100); 
    	$paginate = $pages->page_links();
    	$date = date("Y-m-d H:i:s");
    	$this->assign('date',$date);
    	$this->assign('pages',$paginate);
	    $this->display("CommentHistory/toBuyer");
    }
    
    public function profile(){
	    $this->display("ProfileSetting/main");

		
    }
    
    public function updateProfile(){
    	$date = date("Y-m-d H:i:s");
    	$this->assign('date',$date);
	    $this->display("ProfileSetting/updateProfile");
    }
    
    public function updatePassword(){
    	$date = date("Y-m-d H:i:s");
    	$this->assign('date',$date);
	    $this->display("ProfileSetting/updatePassword");
    }
    
    public function login(){
	    $this->display("Authentication/login");
    }
    
    public function register(){
	    $this->display("Authentication/register");
    }
    
    public function registerComplete(){
	    $this->display("Authentication/registerComplete");
    }
    
//     public function aboutUs(){
// 	    $this->display("Index/aboutus");
//     }
    public function test(){
    	$this->display('Index/index1');
    }

  
}