<?php 
use LPAY\Pay\PayRender;
if(!isset($type))die();
?>
<?php if ($type==PayRender::OUT_QRCODE):?>
<script>
(function(){
	function succ(url){
		alert('支付成功');
		window.location.href=url;
	};
	function fail(status,msg){
		status&&alert(msg);
	};
	window.__LPAY=window.__LPAY||{};
	window.__LPAY.succ=succ;
	window.__LPAY.fail=fail;
})();
</script>
<?php elseif($type==PayRender::OUT_CREDITCARD):?>
<script>
(function(){
	function succ(url){
		alert('支付成功');
		window.location.href=url;
	};
	function fail(msg){
		alert(msg);
	};
	function tips(status){
		status?alert('加载中'):alert('加载完成');
	};
	window.__LPAY=window.__LPAY||{};
	window.__LPAY.succ=succ;
	window.__LPAY.fail=fail;
	window.__LPAY.tips=tips;
})();
</script>
<?php endif;?>