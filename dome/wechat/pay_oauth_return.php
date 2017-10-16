<?php
use LPAY\PayUtils\Pay;
use LPAY\Adapter\Wechat\PayWap;
include __DIR__."/../Bootstarp.php";
include_once './WxPay.Config.php';
$pay_param=PayWap::get_pay_param();
if (!$pay_param){
	http_response_code(400);
	$msg="无法获取SESSION";
}else{
	function url_add_param($url,$param){
		if(strpos($url,"?")===false) return $url."?".$param;
		else return $url."&".$param;
	}
	$config=\LPAY\Adapter\Wechat\PayWapConfig::WxPayConfig_to_arr();
	$pay=Pay::wechat_wap($config);
	try{
		$js=$pay->get_pay_js($pay_param);
	}catch (\LPAY\Exception $e){
		$msg=$e->getMessage();
	}
	if (isset($js))$html=$pay->render_js($pay_param, $js,$is_auto_pay=0/*是否立即调用支付*/);
}
?>
<?php if (isset($html)):?>
<html>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
<title>微信安全支付</title>
<link href="//cdn.bootcss.com/weui/1.1.0/style/weui.min.css" rel="stylesheet">
<style>
.pay_page {
    margin: 20px;
}
.pay_title {
    font-size: 17px;
    color: #353535;
    line-height: 45px;
}
.pay_page .weui-form-preview{
	margin-top:20px;
}
.pay_btn{
	margin-top:30px;
}
</style>
<body>
	<?php echo $html?>
	<div class="pay_page">
		<div class="order_page">
        <h1 class="pay_title">订单:<?php echo $pay_param->get_sn()?></h1>
		<div class="weui-form-preview">
            <div class="weui-form-preview__hd">
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">付款金额</label>
                    <em class="weui-form-preview__value">¥<?php echo $pay_param->get_money()?></em>
                </div>
            </div>
            <div class="weui-form-preview__bd">
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">订单名称</label>
                    <span class="weui-form-preview__value"><?php echo $pay_param->get_title()?></span>
                </div>
               <?php 
               	$timeout=$pay_param->get_timeout();
               	if($timeout>0):
               	$timeout=date("Y-m-d H:i:s",time()+$timeout);
               ?>
                <div class="weui-form-preview__item">
                    <label class="weui-form-preview__label">请此前完成支付:</label>
                    <span class="weui-form-preview__value"><?php echo $timeout?></span>
                </div>
                <?php endif;?>
            </div>
            <a href="javascript:;" id="wx_button" class="weui-btn weui-btn_primary pay_btn">立即付款</a>
        </div>
         <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <p class="weui-footer__text">Copyright © 2017</p>
            </div>
        </div>	
        </div>	
	</div>
</body>
<script type="text/html" id="tpl_dialog">
	 	<div class="js_dialog" id="pay_error_tips" style="display: none;">
            <div class="weui-mask"></div>
            <div class="weui-dialog">
                <div class="weui-dialog__bd"></div>
                <div class="weui-dialog__ft">
                    <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary">知道了</a>
                </div>
            </div>
        </div>
</script>
   <script type="text/html" id="tpl_msg_success">
<div class="page succ_page" style="display: none;">
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-success weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">付款成功</h2>
            <p class="weui-msg__desc">你已成功付款,正在进入订单详细页面,请稍后...</p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <a href="javascript:;" class="weui-btn weui-btn_primary">查看订单</a>
            </p>
        </div>
        <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <p class="weui-footer__text">Copyright &copy; 2017</p>
            </div>
        </div>
    </div>
</div>
</script>
<script type="text/javascript" src="https://res.wx.qq.com/open/libs/zepto/1.1.6/zepto.js"></script>
<script type="text/javascript" src="./fx.js"></script>
<script src="https://res.wx.qq.com/open/libs/weuijs/1.0.0/weui.min.js"></script>
<script>
	(function(){
		var _init_tpl=function(id,init_fn){
			if($('.'+id).length>0) return ;
		 	var $page=$('.pay_page');
	    	var html = $('#'+id).html();
	        var $html = $(html).addClass('slideIn').addClass(id);
	        $html.on('animationend webkitAnimationEnd', function(){
	            $html.removeClass('slideIn').addClass('js_show');
	        });
	        $page.append($html);
	        init_fn&&init_fn($html);
		},
		dialog=function(){
			var callback=null,
				hide=function($html){
					$html.fadeOut(200,function(){
						callback&&callback();
						delete callback;
					});
				};
			_init_tpl('tpl_dialog',function($html){
				$('.weui-dialog__btn',$html).on('click',function(){
					hide($('#pay_error_tips'));
				});
			});
			return {
				show:function(body,_callback){
					var $html=$('#pay_error_tips');
					$('.weui-dialog__bd',$html).html(body);
					$html.fadeIn(200);
					callback=_callback;
				},
				hide:function(){
					hide($('#pay_error_tips'));
				}
			}
		},
		show_page=function(){
			return {
				succ:function(href){
					_init_tpl('tpl_msg_success');
					var $html=$('.succ_page');
					$('.order_page').fadeOut(100,function(){
						$html.fadeIn(200);
					});
					$('.weui-btn_primary',$html).attr('href',href);
				}
			}
		};
		window.wx_pay_utils={
			dialog:dialog,
			show_page:show_page
		};
	})();
    $(function(){
		var dialog= window.wx_pay_utils.dialog(), 
			show_page= window.wx_pay_utils.show_page(); 
		window.__LPAY=window.__LPAY||{};
		window.__LPAY.succ=function(url){
			show_page.succ(url);
			setTimeout(function(){
				window.location.href=url;
			},800);
		};
		window.__LPAY.fail=function(msg){
			dialog.show('支付失败:'+msg);
		}
		document.getElementById('wx_button').onclick=function(){
			window.__LPAY.wechat_obj.pay();	
		}
    });
</script>
</html>

<?php else:?>
<html>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
<title>微信安全支付</title>
<link href="//cdn.bootcss.com/weui/1.1.0/style/weui.min.css" rel="stylesheet">
<style>
.pay_page {
    margin: 20px;
}
</style>
<body>
<div class="pay_page">
<div class="page msg_warn js_show">
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">操作失败</h2>
            <p class="weui-msg__desc">无法完成支付,发生以下错误:<?php echo @$msg?></p>
        </div>
        <div class="weui-msg__opr-area">
            <p class="weui-btn-area">
                <a href="javascript:history.back();" class="weui-btn weui-btn_primary">返回上一页</a>
            </p>
        </div>
        <div class="weui-msg__extra-area">
            <div class="weui-footer">
                <p class="weui-footer__text">Copyright © 2017</p>
            </div>
        </div>
    </div>
</div>
</div>
</html>
<?php endif?>
