<?php

use Org\Util\Dir;

// 文件模块
class FileController extends CommonController {

    //文件上传
    public function upload() {
        $path = $_REQUEST['path'];
        if (!empty($path)) {
            $path = '/'.$path . "/";
        }else{
            $path='';
        }
        $upload = new \Think\Upload();
        $upload->maxSize = 1048576 * 3;
        $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->savePath = $path;
        $upload->saveRule = 'uniqid';
        $info = $upload->upload();
        if ($info) {
            die(json_encode($info));
        } else {
            die('上传文件失败');
        }
    }

}
