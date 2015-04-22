<?php

return array(
    'URL_MODEL' => 1, // 如果你的环境不支持PATHINFO 请设置为3
    //数据库配置
    'DB_TYPE' => 'mysqli',
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'ycbb_db',
//     'DB_USER' => 'debian-sys-maint',
//     'DB_PWD' => 'wY1NtUP7IsNvXGvm',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'ycbb_',
    'VAR_PAGE' => 'pageNum',
    'PAGE_LISTROWS' => 10, //分页 每页显示多少条
    'PAGE_NUM_SHOWN' => 10, //分页 页标数字多少个
    'SESSION_AUTO_START' => true,
    'TMPL_ACTION_ERROR' => 'Public:success', 
    'TMPL_ACTION_SUCCESS' => 'Public:success', 
    'USER_AUTH_ON' => true,
    'USER_AUTH_TYPE' => 2, 
    'USER_AUTH_KEY' => 'authId', 
    'ADMIN_AUTH_KEY' => 'administrator',
    'USER_AUTH_MODEL' => 'User', 
    'AUTH_PWD_ENCODER' => 'md5',
    'USER_AUTH_GATEWAY' => '/Admin/Public/login', 
    'NOT_AUTH_MODULE' => '/Admin/Public', 
    

    'DB_LIKE_FIELDS' => 'title|remark',
    
    'SHOW_PAGE_TRACE' => true, //显示调试信息

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static'
    ),
    
    'APP_USE_NAMESPACE' => false,
	/*auth权限认证配置*/
	'AUTH_ON'           => true,                      // 认证开关
    'AUTH_TYPE'         => 1,                         // 认证方式，1为实时认证；2为登录认证。
    'AUTH_GROUP'        => 'auth_group',        // 用户组数据表名
    'AUTH_GROUP_ACCESS' => 'auth_group_access', // 用户-用户组关系表
    'AUTH_RULE'         => 'auth_rule',         // 权限规则表
    'AUTH_USER'         => 'user'             // 用户信息表
);
