<?php

// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2007 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: common.php 2601 2012-01-15 04:59:14Z liu21st $
//公共函数
function toDate($time, $format = 'Y-m-d H:i:s') {
    if (empty($time)) {
        return '';
    }
    $format = str_replace('#', ':', $format);
    return date($format, $time);
}

function getStatus($status, $imageShow = true) {
    switch ($status) {
        case 0 :
            $showText = '禁用';
            $showImg = '<IMG SRC="/Public/Images/locked.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="禁用">禁用';
            break;
        case 2 :
            $showText = '已处理';
            $showImg = '<IMG SRC="/Public/Images/prected.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="已处理">已处理';
            break;
        case - 1 :
            $showText = '删除';
            $showImg = '<IMG SRC="/Public/Images/del.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="删除">删除';
            break;
        case 1 :
        default :
            $showText = '正常';
            $showImg = '<IMG SRC="/Public/Images/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="正常">正常';
    }
    return ($imageShow === true) ? $showImg : $showText;
}

function getAreasName($areasId) {
	if(!$areasId){
		return '无';
	}
    $model = M('City');
    $area = $model->find($areasId);
    if(!$area){
    	return '无';
    }
    $city = $model->find($area['pid']);
    $prov = $model->find($city['pid']);
    return $prov['name'] . ',' . $city['name'] . ',' . $area['name'];
}

function getCateName($id) {
    $model = M("Category");
    $arr = json_decode($id, true);
    if (!is_array($arr)) {
        $cate = $model->find($id);
        return $cate['name'];
    } else {
        $ids = join(",", $arr);
        $cate = $model->where(" id in (" . $ids . ")")->select();
        $str = '';
        foreach ($cate as $val) {
            $str.=$val['name'] . ",";
        }
        return $str;
    }
}

function getMemberName($id) {
    if (!$id) {
        return "无";
    }
    $model = M("Member");
    $member = $model->find($id);
    return $member['nick_name'];
}

function getMerchantName($id) {
    if (!$id) {
        return "无";
    }
    $model = M("Merchant");
    $member = $model->find($id);
    return $member['merchant_name'];
}

function getNodeGroupName($id) {
    if (empty($id)) {
        return '未分组';
    }
    if (isset($_SESSION ['nodeGroupList'])) {
        return $_SESSION ['nodeGroupList'] [$id];
    }
    $Group = D("Group");
    $list = $Group->getField('id,title');
    $_SESSION ['nodeGroupList'] = $list;
    $name = $list [$id];
    return $name;
}

//囚鸟先生
function showStatus($status, $id, $callback = "", $url, $dwz) {
    switch ($status) {
        case 0 :
            $info = '<a href="' . $url . '/resume/id/' . $id . '/navTabId/' . $dwz . '" target="ajaxTodo" callback="' . $callback . '">恢复</a>';
            break;
        case 2 :
            $info = '<a href="' . $url . '/checkPass/id/' . $id . '/navTabId/' . $dwz . '" target="ajaxTodo" callback="' . $callback . '">批准</a>';
            break;
        case 1 :
            $info = '<a href="' . $url . '/forbid/id/' . $id . '/navTabId/' . $dwz . '" target="ajaxTodo" callback="' . $callback . '">禁用</a>';
            break;
        case - 1 :
            $info = '<a href="' . $url . '/recycle/id/' . $id . '/navTabId/' . $dwz . '" target="ajaxTodo" callback="' . $callback . '">还原</a>';
            break;
    }
    return $info;
}

function getGroupName($id) {
    if ($id == 0) {
        return '无上级组';
    }
    if ($list = F('groupName')) {
        return $list [$id];
    }
    $dao = D("Role");
    $list = $dao->select(array('field' => 'id,name'));
    foreach ($list as $vo) {
        $nameList [$vo ['id']] = $vo ['name'];
    }
    $name = $nameList [$id];
    F('groupName', $nameList);
    return $name;
}

function pwdHash($password, $type = 'md5') {
    return hash($type, $password);
}

function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = & $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = & $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = & $refer[$parentId];
                    $parent[$child][] = & $list[$key];
                }
            }
        }
    }
    return $tree;
}

//CommonModel 自动继承
function CM($name) {
    static $_model = array();
    if (isset($_model[$name])) {
        return $_model[$name];
    }
    $class = $name . "Model";
    import('@.Model.' . $class);
    if (class_exists($class)) {
        $return = new $class();
    } else {
        $return = M("CommonModel:" . $name);
    }
    $_model[$name] = $return;

    return $return;
}
function get_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}
function getRuleGroup($type){
	$db = M('AuthRuleGroup');
	$data =$db ->where(array('id'=>$type))->getField('auth_rule_group');
	return $data;
}

?>
