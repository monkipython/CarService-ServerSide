
	//路径
	var mainPath = "/index.php/",
	loginPath = mainPath + "Home/Acount/login",
	resetPasswordPath = mainPath + "App/Merchant/modPassword",
	safeCodePath = mainPath + "Home/Acount/safeCode",
	registerPath = mainPath + "App/Merchant/register",
	registerEditPath = mainPath + "App/Merchant/modMerchant",
	userProfilePath = mainPath + "Home/Index/profile",
	getProfilePath = mainPath + "Home/Index/profile",
	
	userAlreadyExistsPath = mainPath + "Home/Acount/userAlreadyExists",
	orderIncompletePath = mainPath + "Home/Index/orderIncomplete",
	
	getMerchantPath = mainPath + "App/Merchant/getMerchant",
	modMerchantPath = mainPath + "App/Merchant/modMerchant",
	merOrderList = mainPath + "App/MerOrder/merchant_order_list",
	merOrderDetail = mainPath + "App/MerOrder/get_order";
	userDemandList = mainPath + "App/MerOrder/member_demand_list";

	function loginIn(type){
		var container ;
		var btn;
		if(type == 0){
			container ='#merchant-login';
			btn = '#login_btn'
		}else{
			container ='#member-login';
			btn = '#login_btn_mer';
		}
    	var username = $(container+' input[name=username]').val();
		var password = $(container+' input[name=password]').val();
		var checkbox = $(container+' input[name=checkbox]').is(":checked");
		var ret = $('#nostatus input[name=return]').val();
	
		if(ret =='' || ret == undefined){
			ret = 0;
		}
		if(username ==''||username ==undefined){
			$(btn).attr('data-content','用户名为空').popover('show');
			return false;
		}
		if(isNaN(username) || username.length !=11){
			$(btn).attr('data-content','用户名不符合手机格式').popover('show');
			return false;
		}
		if(password =='' || password == undefined){
			$(btn).attr('data-content','密码为空').popover('show');
			return false;
		}
		$.ajax({
		    type: 'POST',
		    url: loginPath ,
		    data: {'username':username,'password':password,'autologin':checkbox,'type':type} ,
		    success: function(json){
		    	$.session.set('merchant_session_id',json.data.mer_session_id);
		    	if(json['code'] == 0){
		    		if(ret == 0){
		    			location.href = "/Plateform/unbid";
		    			if($.cookie('latest_chat') !== "" || $.cookie('latest_chat') != undefined 
		    			|| $.cookie('latest_chat') != null){						
		    				$.removeCookie('latest_chat', { path: '/' });
						}
		    		}else{
		    			window.location.reload();
		    		}
		    		
		    		//$('#login_btn').attr('data-content','登录成功，跳转页面正在开发').popover('show');;
		    	}else{
		    		
		    		$(btn).attr('data-content',json['msg']).popover('show');
		    		
		    	}
		    } ,
		    dataType: 'json'
		});
	
    
	}

$().ready(function(){
	
	//登录	
		
	$('#login_btn').click(function() {
	
		loginIn(0);
	});
	$('#merchant-login input[name=password]').keypress(function (e) { //这里给function一个事件参数命名为e，叫event也行，随意的，e就是IE窗口发生的事件。
	    var key = e.which; //e.which是按键的值
	    if (key == 13) {
	    	loginIn(0);
	    }
	});
	
	$('#login_btn_mer').click(function() {
		
		loginIn(1);
	});
	$('#member-login input[name=password]').keypress(function (e) { //这里给function一个事件参数命名为e，叫event也行，随意的，e就是IE窗口发生的事件。
	    var key = e.which; //e.which是按键的值
	    if (key == 13) {
	    	loginIn(1);
	    }
	});
	//首页 忘记密码获取验证码
	$('#forgetPsq_v').click(function(){
		console.log('forgetPsq_v');
		var mobile = $('input[id=forgetphone]').val();
		if(mobile == '' || mobile == undefined){
			$('#forgetPsq_v').attr('data-content','您输入的手机号为空').popover('show');
			return false;
		}
		if(isNaN(mobile) || mobile.length !=11){
			$('#forgetPsq_v').attr('data-content','用户名不符合手机格式').popover('show');;
			return false;
		}
			$.ajax({
				url:resetPasswordPath,
				type:"POST",
				data:{'username':mobile},
				success: function(data){
					var d = data;
					var msg = d.msg, code = d.code;
					if(code == 0){
						//倒数60时间
						var mins = 60;
						var count = setInterval(function(){
							mins--;
							if(mins == 0){
								clearInterval(count);
								$('#forgetPsq_v').text("重新获取").removeAttr('disabled');
								
							}else{
								$('#forgetPsq_v').text(mins+"秒后可重新获取").attr('disabled',"true");
							}
						}, 1000);
						
						$('#forget_session_id').val(data['data']);
					}else{
						$('#forgetPsq_v').attr('data-content',msg).popover('show');
					}
				},
				  dataType: 'json'
			});
	});
	//提交修改密码
	$('#forgetPsd').click(function(){
		var mobile = $('input[id=forgetphone]').val();
		var ver = $('input[id=forgetverify]').val();
		var psd = $('input[id=forgetpsd]').val();
		var repsd = $('input[id=forgetpsdagain]').val();
		var session_id = $('input[id=forget_session_id]').val();
		if(mobile == '' || mobile == undefined){
			$('#forgetPsd').attr('data-content','您输入的手机号为空').popover('show');
			return false;
		}
		if(isNaN(mobile) || mobile.length !=11){
			$('#forgetPsd').attr('data-content','用户名不符合手机格式').popover('show');;
			return false;
		}
		if(ver == '' || ver == undefined){
			$('#forgetPsd').attr('data-content','验证码为空').popover('show');
			return false;
		}
		if(session_id == '' || session_id == undefined){
			$('#forgetPsd').attr('data-content','您发送的验证码已失效，请重新发送验证码').popover('show');
			return false;
		}
		if(psd == '' || psd == undefined){
			$('#forgetPsd').attr('data-content','请输入密码').popover('show');
			return false;
		}
		if(repsd == '' || repsd == undefined){
			$('#forgetPsd').attr('data-content','请再次输入密码').popover('show');
			return false;
		}
		if(psd != repsd){
			$('#forgetPsd').attr('data-content','两次输入密码不一致').popover('show');
			return false;
		}
		$.ajax({
			url:resetPasswordPath,
			type:"POST",
			data:{'username':mobile,'code_verify':ver,'password':psd,"repassword":repsd,"session_id":session_id},
			success: function(data){
				var d = data;
				var msg = d.msg, code = d.code;
				if(code == 0){
					
					$('#forgetPsd').attr('data-content','修改成功,2秒后自动关闭').popover('show');
					  setTimeout(function () { 
						  $('#myModal').modal('hide');
					    }, 2000);
				}else{
					$('#forgetPsd').attr('data-content',msg).popover('show');
				}
			},
			  dataType: 'json'
		});
	});

	//首页 注册获取验证码
	$('#registerPsq_v').click(function(){
		var mobile = $('input[id=registerphone]').val();
		if(mobile == '' || mobile == undefined){
			$('#registerPsq_v').attr('data-content','您输入的手机号为空').popover('show');
			return false;
		}
		if(isNaN(mobile) || mobile.length !=11){
			$('#registerPsq_v').attr('data-content','用户名不符合手机格式').popover('show');;
			return false;
		}
			$.ajax({
				url:safeCodePath,
				type:"POST",
				data:{'username':mobile},
				success: function(data){
					var d = data;
					var msg = d.msg, code = d.code;
					if(code == 0){
						//倒数60时间
						var mins = 60;
						var count = setInterval(function(){
							mins--;
							if(mins == 0){
								clearInterval(count);
								$('#registerPsq_v').text("重新获取").removeAttr('disabled');
								
							}else{
								$('#registerPsq_v').text(mins+"秒后可重新获取").attr('disabled',"true");
							}
						}, 1000);
						
						$('#register_session_id').val(data['data']);
					}else{
						$('#registerPsq_v').attr('data-content',msg).popover('show');
					}
				},
				  dataType: 'json'
			});
	});
	//注册商户  
	$('#register').click(function(){
		var mobile = $('input[id=registerphone]').val();
		var ver = $('input[id=registerverify]').val();
		var psd = $('input[id=registerpsd]').val();
		var repsd = $('input[id=registerpsdagain]').val();
		var session_id = $('input[id=register_session_id]').val();
		if(mobile == '' || mobile == undefined){
			$('#register').attr('data-content','您输入的手机号为空').popover('show');
			return false;
		}
		if(isNaN(mobile) || mobile.length !=11){
			$('#register').attr('data-content','用户名不符合手机格式').popover('show');;
			return false;
		}
		if(ver == '' || ver == undefined){
			$('#register').attr('data-content','验证码为空').popover('show');
			return false;
		}
		if(session_id == '' || session_id == undefined){
			$('#register').attr('data-content','您发送的验证码已失效，请重新发送验证码').popover('show');
			return false;
		}
		if(psd == '' || psd == undefined){
			$('#register').attr('data-content','请输入密码').popover('show');
			return false;
		}
		if(repsd == '' || repsd == undefined){
			$('#register').attr('data-content','请再次输入密码').popover('show');
			return false;
		}
		if(psd != repsd){
			$('#register').attr('data-content','两次输入密码不一致').popover('show');
			return false;
		}
		$.ajax({
			url:registerPath,
			type:"POST",
			data:{'username':mobile,'code_verify':ver,'password':psd,"session_id":session_id},
			success: function(data){
				var d = data;
				var msg = d.msg, code = d.code;
				if(code == 0){
					$('#register').attr('data-content','注册成功,1秒后进入修改资料页面').popover('show');
					  setTimeout(function () { 
						  $('#myModalRegister1').modal('hide');
						  $('#myModalRegister2').modal('show');
					    }, 1000);
				}else{
					$('#register').attr('data-content',msg).popover('show');
				}
			},
			  dataType: 'json'
		});
	});
	$('#register2').click(function(){
		var mobile = $('input[id=registerphone]').val();
		var companyname = $('input[id=companyname]').val();
		var charger = $('input[id=charger]').val();
		var wifi = $('input[name=wifi]:checked').val();
		var time1 = $('input[name=time1]').val();
		var time2 = $('input[name=time2]').val();
		var time3 = $('input[name=time3]').val();
		var time4 = $('input[name=time4]').val();
		var province = $('input[id=province]').val();
		var city_id = $('input[id=city_id]').val();
		var area_id = $('input[id=area_id]').val();
		
		var session_id = $('input[id=register_session_id]').val();
		var companyaddr = $('input[id=companyaddr]').val();

		
		
		if(companyname == '' || companyname == undefined){
			$('#register2').attr('data-content','请填写公司名称').popover('show');
			return false;
		}
		if(charger == '' || charger == undefined){
			$('#register2').attr('data-content','请填写负责人名称').popover('show');
			return false;
		}
		if(charger == '' || charger == undefined){
			$('#register2').attr('data-content','请选择是否支持wifi').popover('show');
			return false;
		}
		if(companyaddr == '' || companyaddr == undefined){
			$('#register2').attr('data-content','请填写公司地址').popover('show');
			return false;
		}
		if(session_id == '' || session_id == undefined || mobile == '' || mobile == undefined ){
			$('#register2').attr('data-content','注册成功后，仅在半小时内可以修改').popover('show');
			return false;
		}
		if(time1 == '' || time1 == undefined){
			$('#register2').attr('data-content','请填写营业时间').popover('show');
			return false;
		}
		if(time2 == '' || time2 == undefined){
			$('#register2').attr('data-content','请填写营业时间').popover('show');
			return false;
		}
		if(time3 == '' || time3 == undefined){
			$('#register2').attr('data-content','请填写营业时间').popover('show');
			return false;
		}
		if(time4 == '' || time4 == undefined){
			$('#register2').attr('data-content','请填写营业时间').popover('show');
			return false;
		}
		if(province == '' || province == undefined){
			$('#register2').attr('data-content','请选择省份').popover('show');
			return false;
		}
		if(city_id == '' || city_id == undefined){
			$('#register2').attr('data-content','请选择城市').popover('show');
			return false;
		}
		if(area_id == '' || area_id == undefined){
			$('#register2').attr('data-content','请选择区域').popover('show');
			return false;
		}
		
		var time = time1+':'+time2+"-"+time3+":"+time4;
		$.ajax({
			url:registerEditPath,
			type:"POST",
			data:{'style':1,'mer_session_id':session_id,'moblie':mobile,'wifi_enable':wifi,'address':companyaddr,"business_time":time,'area_id':area_id,'device':'web'},
			success: function(data){
				var d = data;
				var msg = d.msg, code = d.code;
				if(code == 0){
//					$('#register2').attr('data-content','已提交审核,2秒后自动关闭').popover('show');
					  setTimeout(function () { 
						  $('#myModalRegister2').modal('hide');
						  $('#myModalRegister3').modal('show');
					    }, 1000);
				}else{
					$('#register').attr('data-content',msg).popover('show');
				}
			},
			  dataType: 'json'
		});
		
		
		
	})


	$('.closeLogin').click(function(){
		$('#myModalLogin').modal('hide');
	})

	
})




//登录 - 提交
$('#login-submit').click(function(){
	var user = $('input[name=username]').val(), pass = $('input[name=password]').val();
	$.ajax({
		url:loginPath,
		type:"POST",
		data:{username:user,password:pass},
		success: function(data){
			$.session.set('uid', data);
			var d = JSON.parse(data);
			if(d.code == 0){
				window.location.href = userProfilePath;
			}else{
				window.alert("用户和密码不正确");
			}
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

//用户设置 － 商家信息
//读取url
var p = window.location.href;
//判断是哪一个页面
//个人信息页面
if(p.split('/')[7] == "profile" || p.split('/')[6] == "profile"){
	//从session取出商家信息
	var uid = JSON.parse($.session.get('uid')), mer_session_id = uid.data[0].mer_session_id; 
		$.ajax({
			url: getMerchantPath,
			type: "POST",
			data: {mer_session_id:mer_session_id},
			success:function(data){
				var d = JSON.parse(data), c = d.code, v = d.data;
				if(c == 0){
					$('#username_id').text(v.mobile);
					$('#mer_name').text(v.merchant_name);
					$('#mer_intro').text(v.intro);
					$('#mer_header').attr('src',v.header);
					$('#mer_mobile').text(v.mobile);
					$('#mer_tel').text(v.tel);
					$('#mer_address').text(v.address);
					$('#mer_wifi').html(v.wifi == 1 ? "<i class='glyphicon glyphicon-signal'></i>":"<i class='glyphicon glyphicon-remove'></i>");		
					$('span#mer_intro').text(v.intro);
					$('#mer_hours').text(v.business_time);
				}
			}
		});
//更改密码
}else if(p.split('/')[7] == "updatePassword" || p.split('/')[6] == "updatePassword"){
	var uid = JSON.parse($.session.get('uid')),mer_session_id = uid.data[0].mer_session_id; 
	$.ajax({
		url: getMethcantPath,
		type: "POST",
		data: {mer_session_id:mer_session_id},
		success:function(data){
			var d = JSON.parse(data), c = d.code, v = d.data;
			if(c == 0){
				$('#username_id').text(v.mobile);
			}
		}
	});
	//获取更新验证码
	$('#update-verify-code').click(function(){
		$.ajax({
		url: resetPasswordPath,
		type: "POST",
		data: {username:uid.data[0].mobile},
		success:function(data){
			var d = JSON.parse(data);
			var msg = d.msg, code = d.code;
			//用户每天只能发5条短信
			$.session.set('passUpdateSession',d.data[0].session_id);
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
				window.alert(msg);
			}
		}
		});
		
		
	});
	//更新密码
	$('#updatePass-submit').click(function(){
		var username = $('input[name=updatePass]').val(), 
			mer_pass = $('input[name=update_pass]').val(), 
			mer_confirm_pass = $('input[name=update_confirm_Pass]').val(), 
			code_verify = $('input[name=code_verify]').val(),
			session_id = $.session.get('passUpdateSession');
		$.ajax({
		url: resetPasswordPath,
		type: "POST",
		data: {username:uid.data[0].mobile,
			   password:mer_pass,
			   repassword:mer_confirm_pass,
			   code_verify:code_verify,
			   session_id:session_id
			   },
		success:function(data){
			var d = JSON.parse(data);
			var msg = d.msg, code = d.code;
			alert(msg);
		}
		});
	});
}else if(p.split('/')[7] == "updateProfile" || p.split('/')[6] == "updateProfile"){
	//从session取出商家信息
		var uid = JSON.parse($.session.get('uid')), mer_session_id = uid.data[0].mer_session_id; 
		$.ajax({
			url: getMerchantPath,
			type: "POST",
			data: {mer_session_id:mer_session_id},
			success:function(data){
				var d = JSON.parse(data), c = d.code, v = d.data;
				var daily_hrs = v.business_time;
				var hrs = daily_hrs.split("-");
				if(c == 0){
					$('#username_id').text(v.mobile);
				    $('input[name=merchant_name]').val(v.merchant_name);
				    $('input[name=manager]').val(v.manager);
				    $('#header').attr('src',v.header);
				    $('input[name=mobile]').val(v.mobile);
				    $('input[name=tel]').val(v.tel);
				    $('input[name=address]').val(v.address);
				    $('input[name=start]').val(hrs[0]);$('input[name=end]').val(hrs[1]);
				    $('#wifi_enable').val(v.wifi_enable == 0 ? 0: 1);
				    $('#intro').val(v.intro);
				}
			}
		});
	  
	  $('#mer_save').click(function(){
		var uid = JSON.parse($.session.get('uid')),
		 	merchant_name = $('input[name=merchant_name]').val(),
		 	manager = $('input[name=manager]').val(),
		 	header = $('#header').attr('src'),
		 	mobile = $('input[name=mobile]').val(),
		 	tel = $('input[name=tel]').val(),
		 	address = $('input[name=address]').val(),
		 	business_time = $('input[name=start]').val() + "-" + $('input[name=end]').val(),
		 	wifi_enable =  $('#wifi_enable').val(),
		 	intro = $('#intro').val();
		 
/* 		 console.log(uid.data[0].mer_session_id); */
	  	 $.ajax({
		  	 url: modMerchantPath,
		  	 type: "POST",
		  	 data:{mer_session_id: uid.data[0].mer_session_id, merchant_name: merchant_name, manager:manager, header:header,
		  	 	mobile:mobile, tel:tel, address:address, business_time:business_time, wifi_enable:wifi_enable, intro:intro},
		  	 success:function(data){
			  	 var updateResult = JSON.parse(data);
				 alert(updateResult.msg);
		  	 }
	  	 });  
	   });
//未完成订单
}else if(p.split('/')[7] == "orderIncomplete" || p.split('/')[6] == "orderIncomplete"){
	   var uid = JSON.parse($.session.get('uid')), type = 1, page = "";
	   var pagenum = page = (p.split('?')[1] == "") ? 1 : (page.split('=')[1] == null) ?1:page.split('=')[1];
	   $('#username_id').text(uid.data[0].mobile);
	   $.ajax({
		  	 url: merOrderList,
		  	 type: "POST",
		  	 data:{mer_session_id: uid.data[0].mer_session_id, type: type, pagenum:pagenum},
		  	 success:function(data){
		  	 	var d = JSON.parse(data);
		  	 	if(d.code == 0){
			  		$("#order-list").append("<tr style='text-align:center'><td colspan='8'>"+d.msg+"</td></tr>");
			  	}else{
				  	$("#order-list").append("<tr style='text-align:center'><td colspan='8'>"+d.msg+"</td></tr>");
			  	}
		  	 }
	  	}); 
//已完成订单
}else if(p.split('/')[7] == "orderComplete" || p.split('/')[6] == "orderComplete"){
	   var uid = JSON.parse($.session.get('uid')), type = 1, page = "";
	   var pagenum = page = (p.split('?')[1] == "") ? 1 : (page.split('=')[1] == null) ?1:page.split('=')[1];
	   $('#username_id').text(uid.data[0].mobile);
	   $.ajax({
		  	 url: merOrderList,
		  	 type: "POST",
		  	 data:{mer_session_id: uid.data[0].mer_session_id, type: type, pagenum:pagenum},
		  	 success:function(data){
		  	 	var d = JSON.parse(data);
		  	 	if(d.code == 0){
			  		$("#order-list").append("<tr style='text-align:center'><td colspan='10'>"+d.msg+"</td></tr>");
			  	}else{
				  	$("#order-list").append("<tr style='text-align:center'><td colspan='10'>"+d.msg+"</td></tr>");
			  	}
		  	 }
	  	}); 
//失败成订单
}else if(p.split('/')[7] == "orderFail" || p.split('/')[6] == "orderFail"){
	   var uid = JSON.parse($.session.get('uid')), type = 1, page = "";
	   var pagenum = page = (p.split('?')[1] == "") ? 1 : (page.split('=')[1] == null) ?1:page.split('=')[1];
	   $('#username_id').text(uid.data[0].mobile);
	   $.ajax({
		  	 url: merOrderList,
		  	 type: "POST",
		  	 data:{mer_session_id: uid.data[0].mer_session_id, type: type, pagenum:pagenum},
		  	 success:function(data){
		  	 	var d = JSON.parse(data);
		  	 	if(d.code == 0){
			  		$("#order-list").append("<tr style='text-align:center'><td colspan='10'>"+d.msg+"</td></tr>");
			  	}else{
				  	$("#order-list").append("<tr style='text-align:center'><td colspan='10'>"+d.msg+"</td></tr>");
			  	}
		  	 }
	  	}); 
}else if(p.split('/')[7] == "unbid" || p.split('/')[6] == "unbid"){
	   var uid = JSON.parse($.session.get('uid')), type = 1, page = "";
	   var pagenum = page = (p.split('?')[1] == "") ? 1 : (page.split('=')[1] == null) ?1:page.split('=')[1];
	   $('#username_id').text(uid.data[0].mobile);
	   $.ajax({
		  	 url: merOrderList,
		  	 type: "POST",
		  	 data:{mer_session_id: uid.data[0].mer_session_id, type: type, pagenum:pagenum},
		  	 success:function(data){
		  	 	var d = JSON.parse(data);
		  	 	if(d.code == 0){
			  		$("#order-list").append("<tr style='text-align:center'><td colspan='10'>"+d.msg+"</td></tr>");
			  	}else{
				  	$("#order-list").append("<tr style='text-align:center'><td colspan='10'>"+d.msg+"</td></tr>");
			  	}
		  	 }
	  	}); 
}
			
/*
$(function() {
    $('.page-scroll a').bind('click', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: $($anchor.attr('href')).offset().top
        }, 1500, 'easeInOutExpo');
        event.preventDefault();
    });
});
*/

$(function() {
    $("body").on("input propertychange", ".floating-label-form-group", function(e) {
        $(this).toggleClass("floating-label-form-group-with-value", !! $(e.target).val());
    }).on("focus", ".floating-label-form-group", function() {
        $(this).addClass("floating-label-form-group-with-focus");
    }).on("blur", ".floating-label-form-group", function() {
        $(this).removeClass("floating-label-form-group-with-focus");
    });
});


$('body').scrollspy({
    target: '.navbar-fixed-top'
})


$('.navbar-collapse ul li a').click(function() {
    $('.navbar-toggle:visible').click();
});