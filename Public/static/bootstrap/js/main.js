$(document).ready(function(){
	
	//路径
	var loginPath = "/jiake/php_jiake/ycbb/Acount/login",
		registerPath = "/jiake/php_jiake/ycbb/Acount/register",
		safeCodePath = "/jiake/php_jiake/ycbb/Acount/safeCode",
		userAlreadyExistsPath = "/jiake/php_jiake/ycbb/Acount/userAlreadyExists",
		orderIncompletePath = "/jiake/php_jiake/ycbb/Index/orderIncomplete";
		
	
	
	//登录 - 提交
	$('#login_submit').click(function(){
		var user = $('input[name=user]').val(), pass = $('input[name=pass]').val();
		$.ajax({
			url:loginPath,
			type:"POST",
			data:{username:user,password:pass},
			success: function(data){
				window.location.href = orderIncompletePath;
			}
		});
	});	
	
	//注册 － 提交
	$('#register-submit').click(function(){
		var username = $('input[name=registerMobile]').val(), password = $('input[name=registerConfirmPass]').val(), 
		session_id = $('input[name=session_id]').val(), code_verify = $('input[name=code_verify]').val();
		$.ajax({
			url:registerPath,
			type:"POST",
			data:{username:username, password:password, session_id:session_id, code_verify:code_verify},
			success: function(data){
				console.log(data);
			}
		});
		
		
	});
	//注册 － 可用用户
	$('input[name=registerMobile]').blur(function(){
		var registerMobile = $(this).val();
		if(registerMobile != ""){
			$.ajax({
				url: userAlreadyExistsPath,
				type:"POST",
				data:{username:registerMobile},
				success: function(data){
					var d = JSON.parse(data);
					var msg = d.msg;
					$(".register-username-error").text(msg);
				}
			});
		}else{
			$(".register-username-error").text("手机号不能为空!");
		}
	});	
	//注册 － 密码确认
	$('input[name=registerConfirmPass]').keyup(function(){
		var confirmPass = $(this).val();
		setTimeout(function(){
			if(confirmPass == $('input[name=registerPass]').val()){
				$('.register-password-error').css("display","inline");	
			}else{
				$('.register-password-error').css("display","none");	
			}
		},500);
	});
	//注册 － 获取验证码
	$('button.register-countdown').click(function(){
		var mobile = $('input[name=registerMobile]').val();
		var session_id = $('input[name=session_id]').val();
			$.ajax({
				url:safeCodePath,
				type:"POST",
				data:{username:mobile,session_id:session_id},
				success: function(data){
					var d = JSON.parse(data);
					var msg = d.msg, code = d.code;
					if(code == 0){
						//倒数60时间
						var mins = 60;
						var count = setInterval(function(){
							mins--;
							if(mins == 0){
								clearInterval(count);
								$('button.register-countdown').text("重新获取").removeAttr('disabled');
								$('.register-safecode-error').text("");
							}else{
								$('button.register-countdown').text(mins).attr('disabled',"true");
								$('.register-safecode-error').text(msg);
							}
						}, 1000);					
					}else{
						$('.register-safecode-error').text(msg);
					}
				}
			});
	});
	
	
	
});