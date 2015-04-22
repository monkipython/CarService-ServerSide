//基本设置
var BOSH_SERVICE = '/xmpp-httpbind';
var connection = null; //链接
var resource = "@caryu.net/Smack";

/*
function notifyUser(msg){
    var elems = msg.getElementsByTagName('body');
    var body = elems[0];
    $('#msgHistory').append("<p>"+Strophe.getText(body)+"</p>");
    return true;
}
*/

//状态信息
function log(msg){	
	$('#notifications').append('<p>'+msg+'</p>');
}

//连接状态
function onConnect(status){    
	if (status == Strophe.Status.CONNECTING) {
		log('Strophe is connecting.');
    } else if (status == Strophe.Status.CONNFAIL) {
		log('Strophe failed to connect.');
    } else if (status == Strophe.Status.DISCONNECTING) {
		log('Strophe is disconnecting.');
	} else if (status == Strophe.Status.AUTHENTICATING) {
		log('Strophe is authenticating.');
    } else if (status == Strophe.Status.AUTHFAIL) {
    	log('Strophe is authfail.')
    } else if (status == Strophe.Status.ERROR) {
    	log(Strophe.LogLevel.ERROR);
    } else if (status == Strophe.Status.DISCONNECTED) {
		log('Strophe is disconnected.');
    } else if (status == Strophe.Status.CONNECTED) {
		log('Strophe is connected.');
		log(connection.jid+'连接成功,等待消息');
		connection.addHandler(onMessage, null, 'message', null, null,  null);
		connection.send($pres().tree());
	    connection.sendIQ($iq({type: 'get'}).c('query', {xmlns: 'jabber:iq:roster'}), your_roster_callback_function);
    }
}

//获取好友
function your_roster_callback_function(iq){
  $(iq).find('item').each(function(){
    var jid = $(this).attr('jid'); //你的联系人
    var fid = jid.split("@")[0];
    $('#fdHistory').append("<p>"+fid+"</p>");
  });
  connection.addHandler(on_presence, null, "presence");
  connection.send($pres());
}

function on_presence(presence){
  var presence_type = $(presence).attr('type'); // unavailable, subscribed, 等等...
  var from = $(presence).attr('from'); 
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

//获取留言
function onMessage(msg) {
    var to = msg.getAttribute('to');
    var from = msg.getAttribute('from');
    var type = msg.getAttribute('type');
    var elems = msg.getElementsByTagName('body');
    if (type == "chat" && elems.length > 0) {
	var body = elems[0];
/* 	console.log(body); */
	if(Strophe.getText(body).match("base64") != null){
		var content = Strophe.getText(body).substring(6);
		$.ajax({
			url:"../webim/save2file.php",
			type: "POST",
			data: {from:from, to:to, type:type ,content:content},
			success:function(data){
	 				
			}
		});
	}else{
		console.log(Strophe.getText(body));
    	$('#msgHistory').append("<p>"+from+" : "+Strophe.getText(body)+"</p>");
    }
    
	/*var reply = $msg({to: from, from: to, type: 'chat'})
            .cnode(Strophe.copyElement(body));
	connection.send(reply.tree());

	log(from + ': ' + Strophe.getText(body));*/
    }

    // 返回true为保持链接.  
    // 返回false为完毕后直接断开连接.
    return true;
}

//发送留言
function Say(from,to,v){
    var reply = $msg({to: to, from: from, type: 'chat'}).cnode(Strophe.xmlElement('body', '' ,v));
	connection.send(reply.tree());
}


$(document).ready(function () {

	$("#connect").click(function(){
		var user = $("#username").val()+resource;
		var pass = $("#password").val();
		console.log(user + " : " + pass);
		connection = new Strophe.Connection(BOSH_SERVICE);	
	    connection.connect(user,pass,onConnect);
	});
	
	$('#disconnect').click(function(){
		connection.disconnect();
	});
	
	$('#sendMsgBtn').click(function(){
		
		var from = $("#username").val()+resource;
		var to = $('input[name=toWhom]').val()+resource;
		var v = $('#MsgTextField').val();
		Say(from,to,v);
		
		$('#MsgTextField').val("");
		
		$('#msgHistory').append("<p>"+from+" : "+v+"</p>");
		
	});

      
});
	