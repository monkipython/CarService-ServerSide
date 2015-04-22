<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
body, html,#allmap {width: 100%;height: 100%;overflow: hidden;margin:0;}
#l-map{height:100%;width:78%;float:left;border-right:2px solid #bcbcbc;}
#r-result{height:100%;width:20%;float:left;}
</style>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=4TGAqmofi6LcGeNYVFlOTOQG"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.4.1.min.js"></script>
<title>点击地图获取当前经纬度</title>
</head>
<body> 
<div id="allmap"></div>
</body>
</html>
<script type="text/javascript">
	var selectIndex,selectText;
	//alert(<?php echo $_GET['id'];?>);
var c =  window.opener.navTab.getCurrentPanel();
console.log(c);
	 var selectText = $('#city_id',c).find("option:selected").html();//获得是第几个被选中了
	// var selectText = c.document.getElementById("city_id").options[selectIndex].text //获得被选中的项目
	 console.log(selectText);
 if(selectText=="请选择城市"||selectText==undefined||selectText ==''){
	 selectText="厦门";
 }
var map = new BMap.Map("allmap");
map.centerAndZoom(selectText, 12);
map.enableScrollWheelZoom(true);

var point;
var marker;

var $longitude  = $('#longitude',c).val();
var $latitude   = $('#latitude',c).val();

var oldlong = $longitude;
var oldlat = $latitude;

 if (oldlong!="" && oldlat!="")
{
	point = new BMap.Point(oldlong, oldlat);	
	marker = new BMap.Marker(point); 
	map.addOverlay(marker);
}
  
function showInfo(e){
	
// alert(e.point.lng + ", " + e.point.lat);

 if(confirm('是否确定选择了该点？，点击确定后才会修改对应的经纬度,并在1秒后关闭')){
	 point = new BMap.Point(e.point.lng, e.point.lat);
	 marker = new BMap.Marker(point); 
	 map.clearOverlays();
	 map.addOverlay(marker);
	 $('#longitude',c).val(e.point.lng);
	 $('#latitude',c).val(e.point.lat);
	 setTimeout(function(){
		 window.opener=null;window.open('','_self');window.close();
		 
	 },1000);
 }else{
	 return false;
 }
}

map.addEventListener("click", showInfo);

</script>