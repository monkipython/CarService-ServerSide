var wxDownloadUrl = "";
var iosDownloadUrl = "https://itunes.apple.com/cn/app/jia-yu-che-sheng-huo-fu-wu/id967208797?l=en&mt=8";
var androidDownloadUrl = "http://www.caryu.net/down.php?File=JiaYuClient.apk";
var MY = {
//		  var ua = navigator.userAgent.toLowerCase();
//          var isWeixin = ua.indexOf('micromessenger') != -1;
//          var isAndroid = ua.indexOf('android') != -1;
//          var isIos = (ua.indexOf('iphone') != -1) || (ua.indexOf('ipad') != -1);
    isIOS : function(){
        var u = ua = navigator.userAgent.toLowerCase();
        return (ua.indexOf('iphone') != -1) || (ua.indexOf('ipad') != -1);
    },

    isAndroid : function(){
        var u = navigator.userAgent;
        return u.indexOf('Android') != -1 || u.indexOf('Linux') != -1;
    },

    isInWX : function(){
        var u = navigator.userAgent;
        return u.indexOf('MicroMessenger') > -1;
    },

    isMobile : function(){
        var u = navigator.userAgent;
        return u.indexOf("Mobile") > -1;
    },

    download : function(){
        if(MY.isInWX()){
	    location.href = wxDownloadUrl;
        }else {
            if(MY.isIOS()){
                location.href = iosDownloadUrl;
            }else{
                location.href = androidDownloadUrl;
            }
        }
    },

    downloadIOS : function(){
        if(MY.isInWX()){
	    location.href = wxDownloadUrl;
        }else{
            location.href = iosDownloadUrl;
        }
    },
    
    downloadAndroid : function(){
        if(MY.isInWX()){
	    location.href = wxDownloadUrl;
        }else{
            location.href = androidDownloadUrl;
        }
    }
};
