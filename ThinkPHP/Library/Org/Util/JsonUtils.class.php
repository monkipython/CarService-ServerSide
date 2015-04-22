<?php
/**
 +------------------------------------------------------------------------------
 * JSON工具类
 * 提供一系列的JSON打印方法
 +------------------------------------------------------------------------------
 * @category   ORG
 * @package  ORG
 * @subpackage  
 * @author    
 * @version   
 +------------------------------------------------------------------------------
 */
namespace Org\Util;
class JsonUtils   {
	

    //******************************************JSON打印*****************************************************//
/**
 * 打印提示信息和代号json形式     {  'code':'x','msg':'x'}
 * @param  [type] $msg  [description]  信息
 * @param  [type] $code [description]   
 * @return [type]       [description]
 */
function echo_json_msg($code,$msg){
   
   $arr['code']=$code;
   $arr['msg']=$msg;
   echo json_encode($arr);
}

/**
 * 打印JSON list  $list数组  {  'code':'x','msg':'x','data':{'list':[x,x,x,x
 *                                                                   ]}}
 * @param  [type] $code [description]
 * @param  [type] $msg  [description]
 * @return [type]       [description]
 */
function echo_json_list($code,$msg,$list){
   $arr['code']=$code;
   $arr['msg']=$msg;
   $arr['data']=$list;

   echo json_encode($arr);

}
/**
 * 打印 {'code':'x','msg':'x' ,'data':{ '1','2'}}格式
 * @return [type] [description]
 */
function echo_json_data($code,$msg,$data){
	
	$arr['code']=$code;
    $arr['msg']=$msg;
    $arr['data']=$data;

    echo json_encode($arr);
   
}


/**
 * 打印JSON  { 'code':'x','msg':'x', ‘data’:{'timestamp':'23335532','list':[{'x,x,x,x}]
 *                 }}
 * @param  [type] $code      [description]
 * @param  [type] $msg       [description]
 * @param  [type] $timestamp [description]
 * @param  [type] $list      [description]
 * @return [type]            [description]
 */
function echo_json_timestampAndList($code,$msg,$timestamp,$list){
    $arr['code']=$code;
    $arr['msg']=$msg;
    $arr['data']['timestamp']=$timestamp;
    $arr['data']=$list;

  echo json_encode($arr);
}




//******************************************************************************************************************//




}