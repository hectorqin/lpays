<img src="<?php echo $qrcode_url;?>" class="lpay_wechat_pay_qrcode" />
<script type="text/javascript">
//<!--
	//your code : window.__LPAY={key:'callback',succ:function(url){},fail:function(status,msg){status&&alert(msg);}} //success callback
	(function(w){
        function getjson(timeout){
            this.timeout=timeout||8000;
            this._src=null;
            this._t=null;
        }
        getjson.prototype.set=function(url,callback,fail,keyname){
            var callfn='__lpay_fn_'+parseInt(Math.random()*100000000),
            	op = url.indexOf('?')>=0?'&':'?',
            	_keyname=keyname?keyname:'callback',
                self=this;
            self.url=url+op+_keyname+"="+callfn;
            self.fail_fn=fail;
            try{
                eval(callfn+"=function(data){\n"+
                "callback(data);\n"+
                'delete '+callfn+';}');
            }catch(e){return;}
            this.request();
            this._t&&clearTimeout(this._t);
            this._t=setTimeout(function(){
            	 try{
                 	self._src.parentNode.removeChild(self._src);
                 }catch(e){}
                 self.fail_fn(0,'timeout');
            }, this.timeout);
            delete this.url;
        }
        getjson.prototype.request=function(){
            var script=document.createElement("script");
            script.src=this.url;
            var load=false,
            	self=this;
            script.onload = script.onreadystatechange = function() {
                if(this.readyState === "loaded" || this.readyState === "complete"){
                	self._t&&clearTimeout(self._t);
                    load=true;
                    script.onload = script.onreadystatechange=null;
                }
            };
            script.onerror = function(e) {
            	self._t&&clearTimeout(self._t);
            	self.fail_fn(1,'check error:'.e);
            };
            var head=document.getElementsByTagName("head")[0];
            head.insertBefore(script,head.firstChild);
            self._src=script;
        }
        w.__LPAY=w.__LPAY||{};
        w.__LPAY.getjson=getjson;
    })(window);
    //run ...
    (function(w){
    	var return_url='<?php echo $return_url?>',
    		check_url='<?php echo $check_url?>',
    		jsonobj=new w.__LPAY.getjson(),
    		check_fn=function(){
    			jsonobj.set(check_url,function(data){
	       		   if(data.status){
	       				if(w.__LPAY.succ)w.__LPAY.succ(return_url);
	       				else w.location.href=return_url;
		       		}else{
		       			w.__LPAY.fail(0,data.data);
			       	 	setTimeout(check_fn,1500);
		       		}
	       	    },function(status,msg){
	       	    	if(w.__LPAY.fail)w.__LPAY.fail(status,msg);
	       			setTimeout(check_fn,1500);
	       	    },w.__LPAY.key);
        	};
        w.__LPAY=w.__LPAY||{};
        setTimeout(check_fn,3000);//first check
    })(window);
//-->
</script>
