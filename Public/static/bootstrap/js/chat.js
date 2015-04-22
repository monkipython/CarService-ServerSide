/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
var FACE_MAP = {
    '[开心]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/1.png"/>',
    '[委屈]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/2.png"/>',
    '[礼物]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/3.png"/>',
    '[惊恐]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/4.png"/>',
    '[咖啡]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/5.png"/>',
    '[疑问]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/6.png"/>',
    '[生气]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/7.png"/>',
    '[很强]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/8.png"/>',
    '[狂吐]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/9.png"/>',
    '[呼呼]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/10.png"/>',
    '[可爱]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/11.png"/>',
    '[鄙视]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/12.png"/>',
    '[很弱]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/13.png"/>',
    '[真棒]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/14.png"/>',
    '[钱币]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/15.png"/>',
    '[勉强]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/16.png"/>',
    '[流汗]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/17.png"/>',
    '[睡觉]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/18.png"/>',
    '[爱你]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/19.png"/>',
    '[灯泡]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/20.png"/>',
    '[我喷]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/21.png"/>',
    '[阴险]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/22.png"/>',
    '[好酷]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/23.png"/>',
    '[大怒]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/24.png"/>',
    '[玫瑰]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/25.png"/>',
    '[笑靥]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/26.png"/>',
    '[滑稽]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/27.png"/>',
    '[亲你]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/28.png"/>',
    '[好冷]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/29.png"/>',
    '[黑线]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/30.png"/>',
    '[狂汗]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/31.png"/>',
    '[惊哭]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/32.png"/>',
    '[大哭]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/33.png"/>',
    '[金钱]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/34.png"/>',
    '[哈哈]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/35.png"/>',
    '[音乐]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/36.png"/>',
    '[难过]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/37.png"/>',
    '[乖乖]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/38.png"/>',
    '[胜利]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/39.png"/>',
    '[吐舌]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/40.png"/>',
    '[彩虹]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/41.png"/>',
    '[呵呵]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/42.png"/>',
    '[咦咦]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/43.png"/>',
    '[惊讶]' : '<img class="emoji-message" src="http://www.caryu.net/Uploads/Emoji/44.png"/>'
};

var FACE = {
    '[开心]' : '1',
    '[委屈]' : '2',
    '[礼物]' : '3',
    '[惊恐]' : '4',
    '[咖啡]' : '5',
    '[疑问]' : '6',
    '[生气]' : '7',
    '[很强]' : '8',
    '[狂吐]' : '9',
    '[呼呼]' : '10',
    '[可爱]' : '11',
    '[鄙视]' : '12',
    '[很弱]' : '13',
    '[真棒]' : '14',
    '[钱币]' : '15',
    '[勉强]' : '16',
    '[流汗]' : '17',
    '[睡觉]' : '18',
    '[爱你]' : '19',
    '[灯泡]' : '20',
    '[我喷]' : '21',
    '[阴险]' : '22',
    '[好酷]' : '23',
    '[大怒]' : '24',
    '[玫瑰]' : '25',
    '[笑靥]' : '26',
    '[滑稽]' : '27',
    '[亲你]' : '28',
    '[好冷]' : '29',
    '[黑线]' : '30',
    '[狂汗]' : '31',
    '[惊哭]' : '32',
    '[大哭]' : '33',
    '[金钱]' : '34',
    '[哈哈]' : '35',
    '[音乐]' : '36',
    '[难过]' : '37',
    '[乖乖]' : '38',
    '[胜利]' : '39',
    '[吐舌]' : '40',
    '[彩虹]' : '41',
    '[呵呵]' : '42',
    '[咦咦]' : '43',
    '[惊讶]' : '44'
};

function showEmojiBox(){ 
	var lists = "";
	for(var i in FACE){
		lists += '<img class="emoji-face-icon" onClick="javascript:sendEmo(this);" title="'+i+'" src="http://www.caryu.net/Uploads/Emoji/'+FACE[i]+'.png"/>';
	}
/* 	console.log(lists); */
	$('.emoji-box').append(lists);
}

function sendEmo(obj){
	var preText = $("textarea[name=send_message]").val();
	var emo_icon = $(obj).attr('title');
	preText += emo_icon;	
	$("#mytTextField").val(preText);
}
 
function getEmo(str){
	var reg = /\[.+?\]/g; 
	str = str.replace(reg,function(a,b){ //这里是获取到的文本域的value，简洁起见，直接使用了字符串。
/* 		console.log(FACE_MAP[a]); */
		return FACE_MAP[a]; 
	}); 
	return str;
}

//TIMESTAMP TIME AGO
//document.writeLn('<br/>' + weibo_timestamps(now                  ) == "刚刚");
//document.writeLn('<br/>' + weibo_timestamps(now - ONE_SECOND     ) == "刚刚");
//document.writeLn('<br/>' + weibo_timestamps(now - ONE_SECOND * 10) == "10秒前");
//document.writeLn('<br/>' + weibo_timestamps(now - ONE_SECOND * 13) == "10秒前");
//document.writeLn('<br/>' + weibo_timestamps(now - ONE_SECOND * 20) == "20秒前");
//document.writeLn('<br/>' + weibo_timestamps(now - ONE_SECOND * 43) == "40秒前");
//document.writeLn('<br/>' + weibo_timestamps(now - ONE_MINUTE     ) == "1分钟前");
//document.writeLn('<br/>' + weibo_timestamps(now - ONE_MINUTE * 3 ) == "3分钟前");
//document.writeLn('<br/>' + weibo_timestamps(now - ONE_MINUTE * 54) == "54分钟前");
//document.writeLn('<br/>' + weibo_timestamps(now - ONE_HOUR       )); // 今天**:**
//document.writeLn('<br/>' + weibo_timestamps(now - ONE_HOUR   * 12)); // 今天**:**
//document.writeLn('<br/>' + weibo_timestamps(now - ONE_DAY        )); // *月*日 **:**
//document.writeLn('<br/>' + weibo_timestamps(now - ONE_DAY    *564)); // ****年*月*日 **:**

Date.prototype.Format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1, //月份 
        "d+": this.getDate(), //日 
        "h+": this.getHours(), //小时 
        "m+": this.getMinutes(), //分 
        "s+": this.getSeconds(), //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
    if (new RegExp("(" + k + ")").test(fmt)) 
    	fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}
Array.remove = function(array, from, to) {  
    var rest = array.slice((to || from) + 1 || array.length);  
    array.length = from < 0 ? array.length + from : from;  
    return array.push.apply(array, rest);  
};  

//基本设置
var BOSH_SERVICE = 'http://121.40.92.53/xmpp-httpbind';
//var BOSH_SERVICE = 'http://115.159.48.225/xmpp-httpbind';
var connection = null; //链接
var resource = "@caryu.net/Smack";

var url = window.location.href;
var image_api = "http://www.caryu.net/index.php/App/Chat/uploadPic";
var image_src = "http://www.caryu.net/Uploads"; 

var db = WebSQL('chat_db');

//用户头像信息
var userInfo = null;
//当前时间
var time_str = "";
//五分钟心跳
var five_min = (1000 * 60) * 5;
$.cookie.json = true;

/******************
 * xmpp strophe 
 * 连接
 * @param Object status
 ******************/
function onConnect(status){    
    if (status == Strophe.Status.CONNECTING) {
            //console.log('Strophe is connecting.');
            $("<li class='non-msg'><div class='na-msg'><img src='http://www.caryu.net/Public/static/bootstrap/img/loading.gif'/></div></li>").insertBefore('.push-msg');
            $("<li class='non'><div class='na-msg'><img src='http://www.caryu.net/Public/static/bootstrap/img/loading.gif'/></div></li>").insertBefore('.push-contact');
    } else if (status == Strophe.Status.CONNFAIL) {
            /*
			window.alert("链接有误! ");
            $.ajax({
 				type: 'POST',
 				url:'/Acount/loginOut',
 				data:'',
 				success:function(json){
 					if(json.code==0){
 						location.href = "/";
 					}else{
 						alert('登出失败');
 					}
 				},
 		 	  dataType: 'json'
 			});
 			*/
            //console.log('Strophe failed to connect.');
    } else if (status == Strophe.Status.DISCONNECTING) {
            //console.log('Strophe is disconnecting.');
            window.alert("你的账号已被登录！");
            $.ajax({
 				type: 'POST',
 				url:'/Acount/loginOut',
 				data:'',
 				success:function(json){
 					if(json.code==0){
 						location.href = "/";
 					}else{
 						alert('登出失败');
 					}
 				},
 		 	  dataType: 'json'
 			});
    } else if (status == Strophe.Status.AUTHENTICATING) {
            //console.log('Strophe is authenticating.');
    } else if (status == Strophe.Status.AUTHFAIL) {
    		//console.log('Strophe is authfail.');
    		window.alert('信息链接有误：XMPP用户或密码不正确。请联系驾遇客服电话：0592-5021243。感谢您使用驾遇～');
			$.ajax({
 				type: 'POST',
 				url:'/Acount/loginOut',
 				data:'',
 				success:function(json){
 					if(json.code==0){
 						location.href = "/";
 					}else{
 						alert('登出失败');
 					}
 				},
 		 	  dataType: 'json'
 			});
    } else if (status == Strophe.Status.ERROR) {
            //console.log(Strophe.LogLevel.ERROR);
    } else if (status == Strophe.Status.DISCONNECTED) {
    		var time_str = new Date().Format("yyyy-MM-dd hh:mm:ss");
            //console.log('Strophe is disconnected. time:' + time_str );
            
    } else if (status == Strophe.Status.CONNECTED) {
    
    		//console.log('Strophe is connected.');
    		openDB();
			
    		$('.non').fadeOut().hide();
    		
    		time_str = new Date().Format("yyyy-MM-dd hh:mm:ss");
    		
/*             console.log("Strophe is connected. time:" + time_str); */
            //监听关注
            connection.addHandler(onSubscribe, null, "presence", "subscribed");
            //监听来信信息
            connection.addHandler(onMessage, null, 'message', 'chat');
            
            connection.sendIQ($iq({type:'get'}).c('query', {xmlns:'jabber:iq:roster'}), onRoster);

			connection.send($pres().tree());
			
			showEmojiBox();
			
            // 首先要发送一个<presence>给服务器（initial presence）
			setInterval(function(){ connection.send($pres().tree());}, min); 
			
     			       
    }
}


var audio ;

window.onload = function(){
	initAudio();
}
var initAudio = function(){
	//audio =  document.createElement("audio")
	//audio.src='Never Say Good Bye.ogg'
	audio = document.getElementById('message-alert');
	audio.src = "http://www.caryu.net/Public/static/bootstrap/audio/alert.wav";
    audio.loop = false; //歌曲循环
}
 
function playAlert(){
	audio.play();
	audio.stop();
	return true;
}


/******************
 * xmpp strophe 
 * 监听来电信息
 * @param Object msg
 ******************/
function onMessage(msg) {
    var me = msg.getAttribute('to').split("/")[0];
    var from = msg.getAttribute('from').split("/")[0];
    var conversationID = from.split("@")[0];
    var type = msg.getAttribute('type');
    var elems = msg.getElementsByTagName('body');
    var body = $(elems[0]).text();
    time_str = new Date().Format("yyyy-MM-dd hh:mm:ss");
    userInfo = getUserImg(from);
    
    //console.log(me +":"+ from + "=" + $.cookie('latest_chat'));
    
    if(from != "admin@caryu.net"){
    	if($.cookie('latest_chat') == from){
    		insertSQL(from.split("@")[0], from, me, time_str, body, 1);
	    }else{
		    insertSQL(from.split("@")[0], from, me, time_str, body, 0);
	    }
	    if($('#chat-box-minimize').text() == "显示"){
	    	$('.msg-count-icon').show();
	    }else{
		    $('.msg-count-icon').hide();
	    }
    }else if(from == "admin@caryu.net"){
    	//admin 推送的信息
    	var d = JSON.parse(body.substring(13));
    	//console.log(d);
    	//别的页面可以显示订单数量变化
    	if(d.type === 3){
    		old = parseInt($('#order-num').text()) + 1;
	    	$('#order-num').text(old);
    	}
    	
    	if(window.location.href === "http://www.caryu.net/Plateform/unbid"){
			insertDemand(d.demand_id);	
	    }
	    
	    $('.msg-count-icon').hide();
    }
    
    if($.cookie('latest_chat') != from){
		$("span[data~='"+from+"']").find('.notification').show();
    }
/*     playAlert(); */
    
    // 返回true为保持链接.  
    // 返回false为完毕后直接断开连接.
    return true;
}

var demand = new Array(
	'<span class="color-green">正常</span>',
	'<span class="color-red">需求被抢</span>',
	'<span class="color-grep">取消需求</span>',
	'<span class="color-grep">需求过期</span>');

var demand_confirm = new Array(
	'<span class="color-green">等待用户确认</span>',
	'<span class="color-red">需求被抢</span>',
	'<span class="color-grep">取消需求</span>',
	'<span class="color-grep">需求过期</span>');
// get merchant session id	
var mer_session_id = $.session.get('merchant_session_id');
console.log(mer_session_id);


function autoRefresh(type){
	var min = five_min / 30;
	//console.log(min);
	var suto_refresh = setInterval(function(){getDemand(type)}, min);
	/*
var auto_refresh = setInterval(function (){
			$('#frame-view').html(html_str);
			console.log(html_str);
	}, 5000);
*/
	
}

// get demand count
function getDemandCount(type){
	$.ajax({
		url:"http://www.caryu.net/index.php/App/MerDemand/member_demand_list", 
		type: "POST",
		data: {type:type, mer_session_id: mer_session_id},
		dataType: "json",
		success: function(d){
			var dataCount = d.data.count;
			if(dataCount != 0 || dataCount != null){
				$('#demand-num').show().text(dataCount);
			}
		}
	});
}

function getDemand(type){
	$.ajax({
		url:"http://www.caryu.net/index.php/App/MerDemand/member_demand_list", 
		type: "POST",
		data: {type:type, mer_session_id: mer_session_id, num: 10},
		dataType: "json",
		success: function(d){
			var dd = d.data;
			//console.log(dd);
			var dd_str = ""
			var reach_time = "";
			if(type == 1){
				dd_str = "<div class='panel panel-default col-md-12' id='ycbb-project-list-panel'> <table class='table' id='project-table'><thead><tr><th>车主称呼</th><th>发布时间</th><th>项目内容</th><th>报价情况</th><th>状态</th><th>距离</th><th colspan='2'>操作</th></tr></thead><tbody>";
			}else{
				dd_str = "<div class='panel panel-default col-md-12' id='ycbb-project-list-panel'> <table class='table' id='project-table'><thead><tr><th>车主称呼</th><th>发布时间</th><th>项目内容</th><th>报价情况</th><th>距离</th><th>你的报价</th><th>状态</th><th colspan='2'>操作</th></tr></thead><tbody>"
			}
			for(var i = 0; i < dd.list.length; i++){
				if(type == 2){
					reach_time = dd.list[i].addtime;
				}else{
					reach_time = dd.list[i].addtime.substring(5);
				}
				dd_str += "<tr class='new_demand'>"
				dd_str += "<td style='padding-top: 8px; padding-bottom: 21px'>"+dd.list[i].nick_name+"</td><td>"+reach_time+"</td>";
				dd_str += "<td>";
				dd_str += dd.list[i].service_name;	
				dd_str += "</td>";
				dd_str += "<td>"+dd.list[i].offer_price_num+"家报价</td>";
				if(type == 2){
					dd_str += "<td>"+dd.list[i].distance+"公里</td>";
					dd_str += "<td>"+dd.list[i].price+"元</td>";
					dd_str += "<td>"+demand_confirm[dd.list[i].demand_status]+"</td>";
					dd_str += "<td>";
					dd_str += "<a href='/Plateform/updateBid/demand/"+dd.list[i].id+"/type/2' class='btn btn-info btn-sm'>查看</a>";
				}else{
					dd_str += "<td>"+demand[dd.list[i].demand_status]+"</td>";
					dd_str += "<td>"+dd.list[i].distance+"公里</td>";
					dd_str += "<td>";
					dd_str += (dd.list[i].demand_status == 0) ? "<a href='/Plateform/updateBid/demand/"+dd.list[i].id+"/type/1' class='btn btn-info btn-sm'>抢单</a>" : "<a href='javascript:;' class='btn btn-info btn-sm disabled'>抢单</a>";
				}
				dd_str += "</tr>";
			}
			dd_str += "</tbody></table></div>"
			$('#ycbb-project-list-panel').remove();
			$(dd_str).insertBefore('#pagination');
		}
	});
	getOrderInComplete(0);
}


// get demand info
function insertDemand(id){
	
	$.ajax({
		url:"http://www.caryu.net/index.php/App/MerDemand/get_member_demand", 
		type: "POST",
		data: {id: id, mer_session_id: mer_session_id},
		dataType: "json",
		success: function(d){
			var dd = d.data;
			var reach_time = dd.reach_time.substring(5);
			//console.log(dd);
			var dd_str = "<tr class='new_demand'>"
			dd_str += "<td style='padding-top: 8px; padding-bottom: 21px'>"+dd.nick_name+"</td><td>"+reach_time+"</td>";
			dd_str += "<td>";
			for(var i = 0; i < dd.list.length; i++){
				dd_str += dd.list[i].server_name;	
				dd_str += ((i+1) < dd.list.length ) ? "、" : "";
			}
			dd_str += "</td>";
			dd_str += "<td>"+dd.publish+"家报价</td>";
			dd_str += "<td>"+demand[dd.demand_status]+"</td>";
			dd_str += "<td>"+dd.distance+"公里</td>";
			dd_str += "<td>";
			dd_str += (dd.demand_status == 0) ? "<a href='/Plateform/updateBid/demand/"+dd.id+"/type/1' class='btn btn-info btn-sm'>抢单</a>" : "<a href='javascript:;' class='btn btn-info btn-sm disabled'>抢单</a>";
			dd_str += "</tr>";
			//console.log(dd_str);
			$(dd_str).insertBefore('#project-table tbody tr:first').fadeIn('slow');
			$('#project-table tbody tr:last').fadeOut();
		}
	});
}

// get order incomplete
function getOrderInComplete(type){
	//console.log(mer_session_id);
	$.ajax({
		url:"http://www.caryu.net/index.php/App/MerOrder/merchant_order_list",
		type: "POST",
		data: {mer_session_id: mer_session_id, type: type},
		dataType: "json",
		async: false,
		success: function(d){
//			console.log(d);
			var dataCount = d.data.count;
			if(dataCount != 0 || dataCount != null){
				$('#order-num').show().text(dataCount);
			}
		}
	});
}

function onArchive(stanza){
	console.log(stanza);
}

/******************
 * xmpp strophe 
 * 监听关注
 * @param XML stanza
 ******************/
function onSubscribe(stanza){
    if(stanza.getAttribute("type") == "subscribe" && is_friend(stanza.getAttribute("from")))
    {
        // Send a 'subscribed' notification back to accept the incoming
        // subscription request
        conn.send($pres({ to: connection.jid.split("/")[0], type: "subscribed" }));
    }
    return true;
}

function on_presence(presence){
  var presence_type = $(presence).attr('type'); // unavailable, subscribed, 等等...
  var from = $(presence).attr('from'); 
  console.log($(presence));
  if (presence_type != 'error'){
    if (presence_type === 'unavailable'){
      //标注离线用户
    }else if(presence_type === 'available'){
      var show = $(presence).find("show").text(); 
      if (show === 'chat' || show === ''){
        //标注在线用户
      }
    }else if(presence_type === 'subscribed'){
    
	    //标注已关注
    }else if(presence_type === 'unsubscribed'){
	    //标注未关注
    }
  }
  
}

/******************
 * xmpp strophe 
 * 获取好友聊天记录
 * @param String me
 * @param String rosterUser
 * @param String fromIndex
 * @param String rows
 ******************/
function getChatLog(me, roster){
	//console.log(me + " : " + roster);
	userInfo = getUserImg(roster);
	meInfo = getUserImg(me);
	if(roster == "" || roster == null || roster == undefined){
		$('.current-chat-user').text("～I am "+meInfo.data.name);
		$('.non-msg').fadeOut().hide();
		$("<li class='non'><div class='na-msg'>暂无消息</div></li>").insertBefore('.push-msg');
        $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
	}else{
		$('.current-chat-user').text("～和“"+userInfo.data.name+"”聊天中");
	}
	selectSQL(roster.split("@")[0]);

}
/******************
 * xmpp strophe 
 * 选择好友
 * @param Object obj
 ******************/
 
function chooseRoster(obj){
	$("<li class='non-msg'><div class='na-msg'><img src='http://www.caryu.net/Public/static/bootstrap/img/loading.gif'/></div></li>").insertBefore('.push-msg');
	$('li.non .na-msg').hide();
    var me = $.trim(connection.jid.split("/")[0]);
    var another = $.trim($(obj).find('.userJID').attr('data'));
    $.cookie('latest_chat', another, { expires: 7, path: '/' });
    console.log("me: " + me + " you: "+ another);
	if($(obj).find('.notification').text() == "新消息"){
		$(obj).find('.notification').css('display','none');
	}
	userInfo = getUserImg(another);
	$('.current-chat-user').text("～和“"+userInfo.data.name+"”聊天中");
	
	selectSQL(another.split("@")[0]);
/*     getChatLog(me, another, 0, 15); */
}


/******************
 * xmpp strophe 
 * 删除好友
 * @param Object obj
 * @param String fid
 ******************/

function remove_contact(obj, fid){
    var _p = $(obj).parent();
    _p.remove()
    connection.send($pres({to : fid, type: "unsubscribe"}));
}


/******************
 * xmpp strophe 
 * 获取返回好友信息
 * @param XML iq
 ******************/


//获取好友
function onRoster(iq){
  var roster_name = "";
  var limitLetter = 8;
/*   console.log("latest-chat: " + $.cookie('latest_chat')); */
  if($.cookie('latest_chat') != "" || $.cookie('latest_chat') != null){
/*   	  console.log("getchat" + connection.jid.split("/")[0] + " : " + $.cookie('latest_chat')); */
	  getChatLog($.trim(connection.jid.split("/")[0]), $.cookie('latest_chat'));
  }else{
	  $.cookie('latest_chat', "～暂未有任何人聊天", { expires: 7, path: '/' });
  }
  $(iq).find('item').each(function(){
    jid = $(this).attr('jid'); 
    //你的联系人
	//console.log($(this));
    var fid = jid.split("@")[0];
	//$.removeCookie('', { path: '/' });
    userInfo = getUserImg(fid);    
/*     console.log(userInfo.data.name.length+":"+limitLetter); */
	roster_name = (userInfo.data.name.length > limitLetter) ? userInfo.data.name.substr(0,limitLetter) + "..." : userInfo.data.name;
	
    $("<li id='myroster' class='another' onclick='javascript:chooseRoster(this)'><div class='avatar-icon'><img src='"+userInfo.data.header+"'></div><div class='contact-status'><span class='userJID' data='"+jid+"'> " + roster_name + " <span class='notification badge badge-danger'>新消息<span></span></div>").insertBefore('.push-contact');
	 //console.log(userInfo);
  });
  //上线状态提心
  connection.addHandler(onPresence, null, "presence");
  connection.send($pres());
  connection.send($pres().tree());
}

function getUserImg(fid){
	var v = null;
	$.ajax({
	    url: "http://www.caryu.net/index.php/App/Chat/getChatUserData",
	    type: "POST",
	    async:false,
	    data: {jid: fid},
	    success: function(data){
	    	v = JSON.parse(data);
	    	v.data.header = (v.data.header == "") ? "http://www.caryu.net/Public/img/default_user.jpg" : v.data.header;
	    }
    });
    return v;
}

function enlargeImg(obj){
	$('.enlarge-img').html("<img class='origin-img' src='"+$(obj).attr('src')+"'/>");
	
}

/******************
 * xmpp strophe 
 * 获取返回好友信息
 * @param String from
 * @param String to
 * @param String v
 ******************/
 
function Say(from,to,v){
    var reply = $msg({to: to, from: from, type: 'chat'}).cnode(Strophe.xmlElement('body', '' ,v));
    connection.send(reply.tree());
    $('.non').hide();
}

function addUserToRoster(jid){
	connection.send($pres({ to: jid, type: "subscribe" }));
}

function userExist(jid){
	var result = false;
	$(".contact-box .userJID").each(function(){
/* 		console.log($(this).attr('data') + "     " + jid); */
		if($(this).attr('data') === jid){
			result = true;
		}
	});
	return result;
}

/******************
 * xmpp strophe 
 * 获取返回好友信息
 ******************/



/******************
 * WEBSQL
 * 网页端数据库
 ******************/
 
function openDB(){
	db.query(
	    'CREATE TABLE IF NOT EXISTS ChatHistory(id INTEGER PRIMARY KEY AUTOINCREMENT, conversationID INTEGER, fromJID TEXT,toJID TEXT,sentDate TEXT, message TEXT, isRead INTEGER)'
	).fail(function (tx, err) {
	    throw new Error(err.message);
	}).done(function (products) {
		console.log(products);
	});
}

function insertSQL(conversationID, fromJID, toJID, sentDate, message, isRead){
		//获取我的留言
	if(message.indexOf("[--text--]") != -1){
            var pos = 10;  
            rawstr = getEmo(message.substring(pos));
            //console.log(rawstr);
            userInfo = getUserImg(fromJID);
            $("<li class='me'><div class='avatar-icon'><img src='"+userInfo.data.header+"'></div><div data='" + fromJID + "' class='messages'>" + rawstr + "<br/><time >" + sentDate  + "</time></div></li>").insertBefore('.push-msg');
                $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
	}else if(message.indexOf("[--voice--]") != -1){
            var pos = 11;
	        //获取我的留言
            userInfo = getUserImg(fromJID);
            $("<li class='me'><div class='avatar-icon'><img src='"+userInfo.data.header+"'></div><div data='" + fromJID + "' class='messages'><label class='label label-default'>对不起， 网页版暂不支持语音。</label><br/><time >" + sentDate  + "</time></div></li>").insertBefore('.push-msg');
            $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
                 
	}else if(message.indexOf("[--image--]") != -1){
            var pos = 11;
            console.log(message.substring(pos));
	        //获取我的留言
            userInfo = getUserImg(fromJID);
            $("<li class='me'><div class='avatar-icon'><img src='"+userInfo.data.header+"'></div><div data='" + fromJID + "' class='messages'><a class='image-link' href='"+message.substring(pos)+"' data-lightbox='"+sentDate+"'><img class='shrink-img' onclick='javascript: enlargeImg(this);' src='" +message.substring(pos) + "'/></a><br/><time >" + sentDate  + "</time></div></li>").insertBefore('.push-msg');
            $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
            
    }
	db.query(
		'INSERT INTO ChatHistory(conversationID, fromJID, toJID, sentDate, message, isRead) values (?,?,?,?,?,?)',
		[conversationID, fromJID, toJID, sentDate, message, isRead]
	).fail(function (tx, err) {
	    throw new Error(err.message);
	}).done(function (products) {
		console.log(products['insert']);
	});
}

function selectSQL(conversationID){
	db.query(
		'SELECT * FROM ChatHistory WHERE conversationID = ? ORDER BY id ASC',
		[conversationID]
	).fail(function (tx, err) {
	    throw new Error(err.message);
	}).done(function (products) {
		$('.non-msg').fadeOut().hide();
/* 		console.log(products); */
		$('.chat-box').html('');
		$('<li class="push-msg"></li>').appendTo('.chat-box');
		if(products[0] == null || products[0] == undefined || products[0] == ""){
    		$("<li class='non'><div class='na-msg'>暂无记录</div></li>").insertBefore('.push-msg');
        	$('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
        	return false;
		}
		var myData = products;
/* 		console.log(products); */
		var rawstr = "";
		
/* 		console.log(myData.length / (myData.length % 10)); */
		for(var i = 0; i < myData.length; i++){
        if(myData[i].message.indexOf("[--text--]") != -1){
            var pos = 10;  
            rawstr = getEmo(myData[i].message.substring(pos));
/*             console.log(rawstr); */
            //获取我的留言
            if(myData[i].fromJID.match(connection.jid.split("/")[0])){
                userInfo = getUserImg(connection.jid.split("/")[0]);
                $("<li class='me'><div class='avatar-icon'><img src='"+userInfo.data.header+"'></div><div data='" + myData[i].fromJID + "' class='messages'>" + rawstr + "<br/><time >" + myData[i].sentDate  + "</time></div></li>").insertBefore('.push-msg');
                $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
            }else{
                userInfo = getUserImg(myData[i].fromJID);
                $("<li class='another'><div class='avatar-icon'><img src='"+userInfo.data.header+"'></div><div data='" + myData[i].fromJID + "' class='messages'>" + rawstr + "<br/><time >" + myData[i].sentDate  + "</time></div></li>").insertBefore('.push-msg');
                $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
            }
		}else if(myData[i].message.indexOf("[--voice--]") != -1){
            var pos = 11;
	        //获取我的留言
            if(myData[i].fromJID.match(connection.jid.split("/")[0])){
                 userInfo = getUserImg(connection.jid.split("/")[0]);
                 $("<li class='me'><div class='avatar-icon'><img src='"+userInfo.data.header+"'></div><div data='" + myData[i].fromJID + "' class='messages'><label class='label label-default'>对不起， 网页版暂不支持语音。</label><br/><time >" + myData[i].sentDate  + "</time></div></li>").insertBefore('.push-msg');
                 $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
            }else{
                 userInfo = getUserImg(myData[i].fromJID);
                 $("<li class='another'><div class='avatar-icon'><img src='"+userInfo.data.header+"'></div><div data='" + myData[i].fromJID + "' class='messages'><label class='label label-default'>对不起， 网页版暂不支持语音。</label><br/><time >" + myData[i].sentDate  + "</time></div></li>").insertBefore('.push-msg');
                 $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
            }
		}else if(myData[i].message.indexOf("[--image--]") != -1){
            var pos = 11;
/*             console.log(myData[i].message.substring(pos)); */
	        //获取我的留言
            if(myData[i].fromJID.match(connection.jid.split("/")[0])){
                userInfo = getUserImg(connection.jid.split("/")[0]);
                $("<li class='me'><div class='avatar-icon'><img src='"+userInfo.data.header+"'></div><div data='" + myData[i].fromJID + "' class='messages'><a class='image-link' href='"+myData[i].message.substring(pos)+"' data-lightbox='"+myData[i].sentDate+"'><img class='shrink-img' onclick='javascript: enlargeImg(this);' src='" + myData[i].message.substring(pos) + "'/></a><br/><time >" + myData[i].sentDate  + "</time></div></li>").insertBefore('.push-msg');
                $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
             }else{
                userInfo = getUserImg(myData[i].fromJID);
                $("<li class='another'><div class='avatar-icon'><img src='"+userInfo.data.header+"'></div><div data='" + myData[i].fromJID + "' class='messages'><a class='image-link' href='"+myData[i].message.substring(pos)+"' data-lightbox='"+myData[i].sentDate+"'><img class='shrink-img' onclick='javascript: enlargeImg(this);' src='" + myData[i].message.substring(pos) + "'/></a><br/><time >" + myData[i].sentDate  + "</time></li>").insertBefore('.push-msg');
                $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
             }
         }
     }
});
}

function deleteSQL(id){
	db.query(
		'DELETE FROM ChatHistory WHERE id = ?',
		[id]
	).fail(function (tx, err) {
	    throw new Error(err.message);
	}).done(function (products) {
		console.log(products);
	});
}

function clickToChat(obj){
	var system_user_id = $('#click-to-chat').attr('data') + resource.split("/")[0];   
	console.log("system_user_id:"+system_user_id);
	userInfo = getUserImg(system_user_id);
	
	$("<li id='myroster' class='another' onclick='javascript:chooseRoster(this)'><div class='avatar-icon'><img src='"+userInfo.data.header+"'></div><div class='contact-status'><span class='userJID' data='"+system_user_id+"'> " + userInfo.data.name + " <span class='notification badge badge-danger'>新消息</span></span></div>").insertBefore('li#myroster:first');
	console.log(userInfo);
	
	if($('#chat-box-minimize').text() == "显示"){
		$('.msg-count-icon').show();
	}
	
	$('.current-chat-user').text("～和“"+userInfo.data.name+"”聊天中");
	
	$("<li class='non-msg'><div class='na-msg'><img src='http://www.caryu.net/Public/static/bootstrap/img/loading.gif'/></div></li>").insertBefore('.push-msg');
	
	$('li.non .na-msg').hide();
    
    $.cookie('latest_chat', system_user_id, { expires: 7, path: '/' });
    
	if($(obj).find('.notification').text() == "新消息"){
		$(obj).find('.notification').css('display','none');
	}
	addUserToRoster(system_user_id);
	selectSQL(system_user_id.split("@")[0]);
		
}

/******************
 * WEBSQL
 * 网页端数据库
 ******************/

$(document).ready(function () {
	if($('span#username_id').attr("data") == "" || $('span#username_id').attr("data") == null || typeof $('span#username_id').attr("data") == undefined){
		$('.chat-container').hide();
		return false;
	}
	if(url.split("/")[4] === "unbid" || url.split("/")[6] === "unbid"){ 
		autoRefresh(1);
	}else if(url.split("/")[4] === "bid" || url.split("/")[6] === "bid"){
		autoRefresh(2);
	}
	getOrderInComplete(0);
	var userID = $('span#username_id').attr("data");
	//console.log(userID);
    $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
    var chatStatusText = $('#chat-box-minimize');
    var user = userID + resource;
    var pass = userID;
    //console.log(user + " : "+ pass);
    connection = new Strophe.Connection(BOSH_SERVICE);	
    connection.connect(user,pass,onConnect);
	
/*     connection.rawInput = function (data) { console.log('RECV: ' + data); }; */
/*     connection.rawOutput = function (data) { console.log('SEND: ' + data); }; */
    
    //缩小聊天框
    $('#chat-box-minimize').click(function(){
    	chatStatusText.text( chatStatusText.text() == "隐藏" ? "显示" : "隐藏" );
    	
    	if(chatStatusText.text() == "隐藏"){
	    	$('.msg-count-icon').hide();
    	
    	}
        $('.chat-minimize-container, .contact-list-container').slideToggle('slow');
    });
    
    //发送聊天信息
    $('textarea[name=send_message]').keyup( function(e){
        var from = connection.jid;
        var to = $.cookie('latest_chat');
        //console.log(to);
        if (e.keyCode == 13){
        	var msg = $(this).val();
        	var result = getEmo(msg);
        	var textMsg = "[--text--]" + msg;
	        var time_str = new Date().Format("yyyy-MM-dd hh:mm:ss");
	        if(msg != '' || msg != ' '){
	        	$(this).val('');
	        	insertSQL(to.split("@")[0], from, to, time_str, textMsg, 1);
		        Say(from,to,textMsg);
	        }else{
		        return false;
	        }
			
        }
    });
   
    $('.emoji-box').mouseleave(function(){
	    $('.emojicon').css('display', 'none');
    });
    
    $('.emoji-click img').click(function(){
	    $('.emojicon').toggle();
    });
    
    $('.add-user-btn').click(function(){
	    var phone = $('.contact-textfield').val();
	    $.ajax({
		    url: "http://www.caryu.net/index.php/App/Chat/searchFriend",
		    type: "POST",
		    async:false,
		    data: {phone: phone},
		    success: function(data){
		    	var d = JSON.parse(data);
		    	if(d.msg === "ok"){
			    	var users = d.data;
			    	console.log(typeof(users.length));
			    	if(userExist(users[0].jid.split("/")[0]) == false){
				    	if(users.length > 1){
					    	var fid = users[0].jid.split("@")[0];
							userInfo = getUserImg(users[0].jid.split("/")[0]);
							$("<li class='another' onclick='javascript:chooseRoster(this)'><div class='avatar-icon'><img src='"+userInfo.data.header+"'></div><div class='contact-status'><span class='userJID' data='"+users[0].jid.split("/")[0]+"'> " + userInfo.data.name + " <span class='notification badge badge-danger'>新消息</span></span></div>").insertBefore('.push-contact');
							addUserToRoster(users[0].jid.split("/")[0]);
				    	}else{
							var fid = users[0].jid.split("@")[0];
							userInfo = getUserImg(users[0].jid.split("/")[0]);
							$("<li class='another' onclick='javascript:chooseRoster(this)'><div class='avatar-icon'><img src='"+userInfo.data.header+"'></div><div class='contact-status'><span class='userJID' data='"+users[0].jid.split("/")[0]+"'> " + userInfo.data.name + " <span class='notification badge badge-danger'>新消息</span></span></div>").insertBefore('.push-contact');
							addUserToRoster(users[0].jid.split("/")[0]);
				    	}
				    }else{
					    alert("用户已存在！");
				    }
		    	}
		    }
		});
     });
     

	/*************
	 **上传图片处理
	 *************/
	 var files;

	 // Add events
	$('.select-image').on('change', prepareUpload);

	function prepareUpload(event){
		files = event.target.files;
		uploadFiles(event);
	}


	function uploadFiles(event){
		event.stopPropagation(); 
	    event.preventDefault(); 
	
		var data = new FormData();
		$.each(files, function(key, value)
		{
			data.append(key, value);
		});
	        
	    $.ajax({
	        url: 'http://www.caryu.net/index.php/App/Chat/uploadPic',
	        type: 'POST',
	        data: data,
	        cache: false,
	        dataType: 'json',
	        processData: false, 
	        contentType: false, 
	        success: function(data, textStatus, jqXHR)
	        {
	        	if(textStatus == "success"){
	        		time_str = new Date().Format("yyyy-MM-dd hh:mm:ss");
	        		var posted_img = data.data;
	        		var img_msg = "[--image--]"+posted_img; 
	        		var me = connection.jid.split("/")[0];
	        		userInfo = getUserImg(me);
	                $("<li class='me'><div class='avatar-icon'><img src='"+userInfo.data.header+"'></div><div data='" + me + "' class='messages'><a class='image-link' href='"+posted_img+"' data-lightbox='"+time_str+"'><img class='shrink-img' onclick='javascript: enlargeImg(this);' src='" + posted_img + "'/></a><br/><time >" + time_str  + "</time></div></li>").insertBefore('.push-msg');
	                $('.chat-box').scrollTop($('.chat-box')[0].scrollHeight);
	                console.log(me + ": "+$('.chat-box .another .messages').attr('data') + ":" + posted_img);
	                
	                Say(me,$('.chat-box .another .messages').attr('data'),img_msg);
	        	}
	        	
	            
	        },
	        error: function(jqXHR, textStatus, errorThrown)
	        {
	           	console.log('ERRORS: ' + textStatus);
	        }
	    });
	}
	
	
});

