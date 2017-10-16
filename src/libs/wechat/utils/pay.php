<script type="text/javascript">
<?php
//your code
// window.__LPAY=window.__LPAY||{};
// window.__LPAY.succ=function(url){
// 	alert('支付成功');
// 	window.location.href=url;
// };
// window.__LPAY.fail=function(msg){
// 	alert('支付失败:'+msg);
// }
//  手动触发支付 
// 	window.__LPAY.wechat_obj.pay();
?>
(function(w){
	function lpay(param){
		this._js_param=param.js_param||{};
		this.return_url=param.return_url||null;
		this.cancel_url=param.cancel_url||null;
		this._ready_call(function(){
			WeixinJSBridge.call('hideOptionMenu');
		});
	}
	lpay.prototype.success=function(){
		WeixinJSBridge.call('showOptionMenu');
		if(w.__LPAY.succ)w.__LPAY.succ(this.return_url);
		else w.location.href=this.return_url;
	}
	lpay.prototype.cancel=function(){
		WeixinJSBridge.call('showOptionMenu');
		w.location.href=this.cancel_url;
	}
	lpay.prototype.fail=function(msg){
		WeixinJSBridge.call('showOptionMenu');
		if(w.__LPAY.fail)w.__LPAY.fail(msg);
		else alert(msg);
	}
	lpay.prototype._ready_call=function(call){
		var self=this;
		if (typeof WeixinJSBridge == "undefined"){
				var _call=function(){
					call.call(self); 
			    };
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady',_call, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', _call); 
		        document.attachEvent('onWeixinJSBridgeReady', _call);
		    }
		}else{
			call.call(self);  
		}
	}
	lpay.prototype._pay=function(is_cancel){
		var self=this;
		self._ready_call(function(){
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				self._js_param,
				function(res){
					if(/request:ok/.test(res.err_msg)){
						return self.success();
					}
					if(/request:cancel/.test(res.err_msg)){
						return is_cancel&&self.cancel();
					}
					return self.fail(res.err_msg);
				}
			);
		});
	}
	lpay.prototype.pay=function(){
		this._pay(false);
	}
	w.__LPAY=w.__LPAY||{};
	w.__LPAY.wechat=lpay;
})(window);
//run...
(function(w){
	w.__LPAY=w.__LPAY||{};
	var param=w.__LPAY.param||{
		auto:('1'=='<?php echo $auto_pay?>'||'true'=='<?php echo $auto_pay?>')
	};
	param.return_url='<?php echo $return_url?>';
	param.cancel_url='<?php echo $cancel_url?>';
	param.js_param=<?php echo $jsApiParameters; ?>;
	w.__LPAY.wechat_obj = new w.__LPAY.wechat(param);
	if(param.auto) w.__LPAY.wechat_obj._pay(true);
})(window);
</script>





