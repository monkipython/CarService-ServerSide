<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>驾遇 － 首页</title>

    <!-- Bootstrap Core CSS - Uses Bootswatch Flatly Theme: http://bootswatch.com/flatly/ -->
    <link href="/Public/static/bootstrap/css/frontend.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/Public/static/bootstrap/css/freelancer.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="/Public/static/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    </head>
    <body id="page-top" class="index">

    <!-- Navigation -->
    <nav class="navbar navbar-default">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">导航</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#page-top">
	                <p style="font-size:35px;margin-top:-10px;">驾遇</p>
	                <p style="padding-left:1px;">CARYU</p>
                </a>
            </div>
			
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-left">
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li class="page-scroll">
                        <a href="#portfolio">产品介绍</a>
                    </li>
                    <li class="page-scroll">
                        <a href="#download">产品下载</a>
                    </li>
                    <li class="page-scroll">
                        <a href="#about">关于我们</a>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li>
	                <form class="navbar-form" role="search">
			        <div class="form-group">
			          <input type="text" class="form-control" name="user" placeholder="手机号">
			          <input type="password" class="form-control" name="pass" placeholder="密码">
			        </div>
					</form>
                    </li>
                    <li class="page-scroll">
                        <a href="#" id="login-submit">登录</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
		<div class="container" style="text-align:center">
			<div class="col-xs-offset-3 col-md-6" style="text-align:center; margin-top:50px; margin-bottom:50px;">
				<div class="panel panel-default register-panel">
				  <div class="panel-body">
				  		<h3 class="h h-lg" style="color:#666;">快速注册</h3>
				  		<div class="col-md-12" style="text-align:left; padding-top:25px;">
							<div class="form-group">
								<div class="col-md-12">
									<label for="mobile" style="color:#666;margin-top:5px;">用户名</label>
						    		<input type="tel" class="form-control" id="register-login" name="registerMobile" placeholder="移动电话号码（11位数）" value="18559208033"/><span class="register-username-error"></span>
								</div>
								<div class="col-md-12">
									<label for="password" style="color:#666;margin-top:5px;">密码</label>
							    	<input type="password" class="form-control" id="register-login" name="registerPass" placeholder="密码" />
								</div>
								<div class="col-md-12">
									<label for="company" style="color:#666;margin-top:5px;">确认密码</label>
						    		<input type="password" class="form-control" id="register-login" name="registerConfirmPass" placeholder="确认密码" /><span class="register-password-error"><i class="glyphicon glyphicon-ok"></i></span>
								</div>
								<div class="col-md-12">
									<label for="password" style="color:#666;margin-top:5px;">手机短信验证码</label>
									<button class="btn btn-danger btn-sm register-countdown">获取验证码</button>
								    <input type="text" class="form-control" id="register-login"  maxlength="6" style="width:75%" name="code_verify" placeholder="6位数验证码" /><span class="register-safecode-error"></span>
								</div>
							</div>
				  		</div>
				  		<div class="row">
					  	<div class="col-md-12" style="padding:20px 0 10px 0;">
					  		<input type="button" id="register-submit" style="border-radius:0px; padding:15px 15px; font-size:18px;" class="btn btn-success btn-md col-lg-offset-1 col-md-4" value="提交"/>
					  		<input type="button" style="border-radius:0px; padding:15px 15px; font-size:18px;" class="btn btn-success btn-md col-lg-offset-2 col-md-4" id="register-cancel" value="取消"/>
					  	</div>
					  </div>
				  	  </div>
				  </div>
				</div>
			</div>
		</div>
		
		
	<!-- Footer -->
    <footer class="text-center">
        <div class="footer-below">
            <div class="container">
                <div class="row">
                	
                    <div class="col-lg-12">
                    	<p class="copy-right">版权所有：厦门日后科技有限公司</p>
                    	<p>联系地址：厦门市思明区莲前街道洪莲中路明发工业园1462号4F401 </p>
						<p>客服电话：0592-5021243 传真：0592-5021243 邮编：361000</p>
						<p>Copyright &copy; caryu.com 2013-2014</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
	<!-- footer end-->
	<script src="/Public/static/jquery-2.0.3.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="/Public/static/bootstrap/js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="/Public/static/bootstrap/js/jquery.easing.min.js"></script>
    <script src="/Public/static/bootstrap/js/classie.js"></script>
    <script src="/Public/static/bootstrap/js/cbpAnimatedHeader.js"></script>

    <!-- Contact Form JavaScript -->
    <script src="/Public/static/bootstrap/js/jqBootstrapValidation.js"></script>
    <script src="/Public/static/bootstrap/js/contact_me.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="/Public/static/bootstrap/js/script.js"></script>
    </body>
</html>