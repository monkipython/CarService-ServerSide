<?php
namespace App\Controller;
use Think\Controller;
use Think\Model;
/**
 * 首页相关接口 --遗留
 */
class GameController extends Controller {



    public function index(){
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>[ 您现在访问的是App模块的Index控制器 ]</div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
   
       
    }

   public function  test(){
   	$result=get_html_context('<div class="contJ">', '<div class="share">', '../d/20722.html');
   	echo $result."111";
   	echo_json_data("0","ok",$result);
   }

   
   /**
     * 游戏试玩详情
     * @return [type] [description]
     */
    public function getgame(){
      import( '@.ORG.Util.ThumbHandler');
      //$jsonUtils=new JsonUtils();
      $app=M('ecms_app','www_92game_net_');
      $app_data1=M('ecms_data_1','www_92game_net_');
      $model=new Model();
      $auth_param=$_POST['auth_param'];
      $id=$_GET['id'];
      $platform=$_POST['platform'];
     /* if(empty($auth_param)){
        echo_json_msg(4,'认证参数为空');
        return;
      }*/
      if(empty($id)){
        echo_json_msg(4,'id参数为空');
        return;
      }
     /* if($auth_param!=md5($id.$platform.'getGame')){
         $jsonUtils->echo_json_msg(6,'认证错误！');
         return;
      }*/

      $arr=$model->query(" select a.id , a.title as game_name , titlepic_thumb as game_img ,softsay as game_info,apk ,apkbag,ipabag, a.filesize,a.ipafilesize,a.version ,a.pic1,a.pic2,a.pic3,b.pic4,a.pic5 ,a.pic1_thumb,a.pic2_thumb,a.pic3_thumb,b.pic4_thumb,a.pic5_thumb from www_92game_net_ecms_app a left join www_92game_net_ecms_app_data_1 b on a.id=b.id where a.id=$id");
     
     if(!empty($arr)&&count($arr)>0){
          $url_prefix='http://www.8477.com';
          if(empty($arr[0]['pic1_thumb'])&&empty($arr[0]['pic2_thumb'])&&empty($arr[0]['pic3_thumb'])){
             echo "1";
            $pic1_thumb=$this->thumb($arr[0]['pic1']);
            $pic2_thumb=$this->thumb($arr[0]['pic2']);
            $pic3_thumb=$this->thumb($arr[0]['pic3']);

            unset($arr[0]['pic1']);
            unset($arr[0]['pic1_thumb']);
            unset($arr[0]['pic2']);
            unset($arr[0]['pic2_thumb']);
            unset($arr[0]['pic3']);
            unset($arr[0]['pic3_thumb']);

            $arr[0]['list'][0]=$url_prefix.$pic1_thumb;
            $arr[0]['list'][1]=$url_prefix.$pic2_thumb;
            $arr[0]['list'][2]=$url_prefix.$pic3_thumb;

            $data['pic1_thumb']=$pic1_thumb;
            $data['pic2_thumb']=$pic2_thumb;
            $data['pic3_thumb']=$pic3_thumb;
            $app->where("id=$id")->save($data);//保存游戏截图1-3到主表里
           
          }else{
          
            $arr[0]['list'][0]=$url_prefix.$arr[0]['pic1_thumb'];
            $arr[0]['list'][1]=$url_prefix.$arr[0]['pic2_thumb'];
            $arr[0]['list'][2]=$url_prefix.$arr[0]['pic3_thumb'];
            unset($arr[0]['pic1_thumb']);
            unset($arr[0]['pic1']);
             unset($arr[0]['pic2_thumb']);
            unset($arr[0]['pic2']);
             unset($arr[0]['pic3_thumb']);
            unset($arr[0]['pic3']);
          }
          if(empty($arr[0]['pic4_thumb'])&&!empty($arr[0]['pic4'])){
          
            $pic4_thumb=$this->thumb($arr[0]['pic4']);
            unset($arr[0]['pic4']);
            unset($arr[0]['pic4_thumb']);
            $arr[0]['list'][3]=$url_prefix.$pic4_thumb;

            $data1['pic4_thumb']=$pic4_thumb;
            $app_data1->where("id=$id")->save($data1);//保存图片路径到副表里
             
          }else{
            if(!empty($arr[0]['pic4_thumb'])){
             $arr[0]['list'][3]=$url_prefix.$arr[0]['pic4_thumb'];
           }
            unset($arr[0]['pic4_thumb']);
            unset($arr[0]['pic4']);
          }
          
          if(empty($arr[0]['pic5_thumb'])&&!empty($arr[0]['pic5'])){
            
            $pic5_thumb=$this->thumb($arr[0]['pic5']);
             unset($arr[0]['pic5']);
             unset($arr[0]['pic5_thumb']);
            $arr[0]['list'][4]=$url_prefix.$pic5_thumb;
            $data5['pic5_thumb']=$pic5_thumb;
            $app_data1->where("id=$id")->save($data5);

          }else{
            
            if(!empty($arr[0]['pic5_thumb'])){
            $arr[0]['list'][4]=$url_prefix.$arr[0]['pic5_thumb'];
           }
            unset($arr[0]['pic5_thumb']);
            unset($arr[0]['pic5']);
          }
          
        
        
          echo_json_data(0,'ok',$arr[0]);
      }else{
           echo_json_msg(1,'找不到该游戏!');
        return;
      }
        

     

   }
     //根据图片路径压缩图片比例
   function thumb($pic){
      import( '@.ORG.Util.ThumbHandler');
      $t=new \Org\Util\ThumbHandler();
      $title_pic=str_replace("/zsbs", "", $pic);//
      $t->setSrcImg("../.".$title_pic); 
      $arr_ex=explode(".", $title_pic);
      $titlepic_thumb=$arr_ex[0]."_thumb.".$arr_ex[1];
     // echo $titlepic_thumb;
      $t->setDstImg("../.".$titlepic_thumb); 
      $t->dst_w=300;
      $t->dst_h=200;
      $t->_setNewImgSize(200,300);
      // 指定固定宽高
      $t->createImg(50); 
      return $titlepic_thumb;
   }

}