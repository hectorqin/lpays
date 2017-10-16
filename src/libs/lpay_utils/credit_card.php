<!-- plase use add css style -->
<form method="POST" id="_LSYS_PP_PAY_" action="javascript:void(0);">
	<input type="hidden" name="key" value="<?php echo $key?>">
	<div class="lpay_params">
		<div class="lpay_param_name">Card type</div>
		<div class="lpay_param_value">
			<select name="creditCardType" >
				<option value="Visa" selected="selected">Visa</option>
				<option value="MasterCard">MasterCard</option>
				<option value="Discover">Discover</option>
				<option value="Amex">American Express</option>
			</select>				
		</div>
	</div>
	<div class="lpay_params">
		<div class="lpay_param_name">Card number</div>
		<div class="lpay_param_value">
			<input required="required" type="number" size="19" maxlength="19" name="creditCardNumber">
		</div>
	</div>
	<div class="lpay_params">
		<div class="lpay_param_name">Expiry date</div>
		<div class="lpay_param_value">
			<select name="expDateMonth">
				<option value="01">01</option>
				<option value="02">02</option>
				<option value="03">03</option>
				<option value="04">04</option>
				<option value="05">05</option>
				<option value="06">06</option>
				<option value="07">07</option>
				<option value="08">08</option>
				<option value="09">09</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
			</select>
			<select name="expDateYear" id="lpay_expDateYear">					
			</select>
		</div>
	</div>
	<div class="lpay_params">
		<div class="lpay_param_name">CVV</div>
		<div class="lpay_param_value">
			<input required="required" type="number" size="3" maxlength="4" name="cvv2Number" value="">
		</div>
	</div>
	
	<div class="lpay_params">
		<div class="lpay_param_name">First name</div>
		<div class="lpay_param_value">
			<input required="required" type="text" name="firstName" value=""/>
		</div>
	</div>
	<div class="lpay_params">
		<div class="lpay_param_name">Last name</div>
		<div class="lpay_param_value">
			<input required="required" type="text" name="lastName" value=""/>
		</div>
	</div>
	<div class="lpay_section_header">Billing address</div>
	<div class="lpay_params">
		<div class="lpay_param_name">Country</div>
		<div class="lpay_param_value">
			<input required="required" type="text" size="10" maxlength="10" name="country" value="" placeholder="US">
		</div>
	</div>
	<div class="lpay_params">
		<div class="lpay_param_name">State</div>
		<div class="lpay_param_value">
			<input required="required" type="text" size="2" maxlength="2" name="state" value="" placeholder="NY">
		</div>
	</div>
	<div class="lpay_params">
		<div class="lpay_param_name">City</div>
		<div class="lpay_param_value">
			<input required="required" type="text" size="25" maxlength="40" name="city" value="" placeholder="your city">
		</div>
	</div>
	<div class="lpay_params">
		<div class="lpay_param_name">Address 1</div>
		<div class="lpay_param_value">
			<input required="required" type="text" size="25" maxlength="100" name="address1" value="">
		</div>
	</div>
	<div class="lpay_params">
		<div class="lpay_param_name">Address 2 (optional)</div>
		<div class="lpay_param_value">
			<input type="text" size="25" maxlength="100" name="address2" value="" >
		</div>
	</div>
	<div class="lpay_params">
		<div class="lpay_param_name">Zip code</div>
		<div class="lpay_param_value">
			<input required="required" type="number" size="10" maxlength="10" name="zip" value="" placeholder="(5 or 9 digits)">
		</div>
	</div>
	<div class="lpay_params">
		<div class="lpay_param_name">Phone</div>
		<div class="lpay_param_value">
			<input required="required" type="number" size="10" maxlength="10" name="phone" value="">
		</div>
	</div>
	<div class="submit">
		<input type="submit" value="Payment" />
	</div>							
</form>
<script type="text/javascript">
//<!--
//your code: 
//window.__LPAY={
//	succ:function(url){alert('支付成功');window.location.href=url;},
//	tips:function(status){status?alert('显示加载中'):alert('隐藏加载中');},
//	fail:function(msg){alert(msg);}
//};
//add ajax
(function(w){
	function createXHR() {
		if (w.XMLHttpRequest) {	
			 return new XMLHttpRequest();
		} else if (w.ActiveXObject) {
			var versions = ['MSXML2.XMLHttp','Microsoft.XMLHTTP'];
			for (var i = 0,len = versions.length; i<len; i++) {
				try {
					return new ActiveXObject(version[i]);
					break;
				} catch (e) {
				}	
			}
		} else {
			throw new Error('not support ajax');
		}
	}
	function params(data) {
		var arr = [];
		for (var i in data) {
			if(data[i]==null||data[i]==undefined)data[i]='';
			arr.push(encodeURIComponent(i) + '=' + encodeURIComponent(data[i]));
		}
		return arr.join('&');
	}
	function ajax(obj) {
		var xhr = createXHR(),
			data=typeof obj.data =='object'?params(obj.data):obj.data;
		obj.url = obj.url;
		obj.data = data;
		if (obj.method === 'get') {
			obj.url += obj.url.indexOf('?') == -1 ? ('?' +data) : ('&' +data); 
		}
		if (obj.async === true) {  
			xhr.onreadystatechange = function () {
				if (xhr.readyState == 4) {  
					callback();
				}
			};
		}
		xhr.open(obj.method, obj.url, obj.async);
		if (obj.method === 'post') {
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.send(obj.data);		
		} else {
			xhr.send(null);		
		}
		if (obj.async === false) {  
			callback();
		}
		function callback() {
			if (xhr.status == 200) {  
				obj.success(xhr.responseText);			
			} else {
				obj.error(xhr.status,xhr.statusText);
			}	
		}
	}
	//utils
	var bind_event=function(ele,event,func){
			if(ele.addEventListener){
				ele.addEventListener(event,func,false);
			}else if(ele.attachEvent){
				ele.attachEvent('on'+event,func);
			}else{
				ele['on'+event]=func;
			};
	   	},
	   	serialize_form=function(form){
	   		function nodeEach(list){ //将NodeList转换为Array  
	   		    var arr = new Array();  
	   		    for( var i = 0 ; list.length > i ; i++ ){  
	   		        var node = list[i];   
	   		        arr.push(node);  
	   		    }  
	   		    return arr;  
	   		}  
	   		function in_array(search,array){  
	   		    for(var i in array){  
	   		        if(array[i]==search){  
	   		            return true;  
	   		        }  
	   		    }  
	   		    return false;  
	   		}  
	   		function getJsonObjLength(jsonObj) {  
	   		    var Length = 0;  
	   		    for (var item in jsonObj) {  
	   		        Length++;  
	   		    }  
	   		    return Length;  
	   		}  
	   		function serialize(formDom){  
	   		    var valueList = [];
	   		    var data = {};
	   		    var type1List = new Array();  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='color']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='date']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='datetime']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='datetime-local']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='email']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='hidden']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='month']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='number']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='password']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='range']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='search']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='tel']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='text']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='time']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='url']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='week']") ) );  
	   		    type1List = type1List.concat( nodeEach( formDom.querySelectorAll("input[type='radio']:checked") ) );  
	   		    for( var i = 0 ; type1List.length > i ; i++ ){  
	   		        var dom = type1List[i];  
	   		        var name = dom.getAttribute("name");//键名  
	   		        var value = dom.value;//值  
	   		        valueList.push( { name: name, value: value} );  
	   		          
	   		    }  
	   		    var type3List = formDom.querySelectorAll("input[type='checkbox']:checked");  
	   		    var existCheckbox = new Array();  
	   		    for( var i = 0 ; type3List.length > i ; i++ ){  
	   		        if( in_array( type3List[i].getAttribute("name"), existCheckbox ) )//判断是否已处理  
	   		            continue;  
	   		        var dom = type3List[i];  
	   		        var name = dom.getAttribute("name");//键名  
	   		        var value = dom.value;//值  
	   		        var cache = { name: name, value: [] };  
	   		        var l = formDom.querySelectorAll("input[type='checkbox'][name='"+name+"']:checked");  
	   		        for( var j = 0 ; l.length > j ; j++ ){  
	   		            cache.value.push( l[j].value );  
	   		        }  
	   		        valueList.push( cache );  
	   		    }  
	   		    var type4List = formDom.querySelectorAll("select");  
	   		    for( var i = 0 ; type4List.length > i ; i++ ){  
	   		        var name = type4List[i].getAttribute("name");//键名  
	   		        var value = type4List[i].options[type4List[i].options.selectedIndex].getAttribute("value"); //值  
	   		        valueList.push( { name: name, value: value} );  
	   		          
	   		    }  
	   		    for( var i = 0 ; valueList.length > i ; i++ ){  
	   		        var row = valueList[i];  
	   		        var name = row.name;  
	   		        if( !name ){  
	   		            continue;  
	   		        }  
	   		        var value = row.value?row.value:null;  
	   		        var kArr = name.split("[");//是否是数组  
	   		        var cDatas = "data";  
	   		        for( var j = 0; j < kArr.length; j++ ){  
	   		            var cn = kArr[j].replace(/\]/g, "").trim();//去除右方括号  
	   		            if(cn){  
	   		                  
	   		                if( !isNaN(cn) ){  
	   		                    cDatas += "[" + cn + "]";  
	   		                }else{  
	   		                    cDatas += "." + cn;  
	   		                }  
	   		                if( eval(cDatas+" == null") ) {  
	   		                    eval( cDatas + "= {};" );  
	   		                }  
	   		            }else{//追加  
	   		                cDatas += "["+ eval( "getJsonObjLength("+cDatas + ")" )+"]";  
	   		                eval( cDatas + " = {};" );  
	   		            }  
	   		        }  
	   		        eval( cDatas + " = value;" );  
	   		    }  
	   		    return data;  
	   		}  
			return serialize(form);
		},
		str_to_json=function(str){
			try{
				if(str.parseJSON) return str.parseJSON();
				else return eval('(' + str + ')');
			}catch(e){
				return {data:str};
			}
		};
	w.__LPAY=w.__LPAY||{};
	w.__LPAY.utils=w.__LPAY.utils||{};
	w.__LPAY.utils.ajax=ajax;
	w.__LPAY.utils.on=bind_event;
	w.__LPAY.utils.to_json=str_to_json;
	w.__LPAY.utils.serialize_form=serialize_form;
})(window);
(function(w){
	var form=document.getElementById('_LSYS_PP_PAY_'),
		pay_url='<?php echo $pay_url?>',
		return_url='<?php echo $return_url?>',
		fn_fail=function(msg){
			if(w.__LPAY.fail)w.__LPAY.fail(msg);
			else alert(msg);
		},
		fn_succ=function(){
			if(w.__LPAY.succ)w.__LPAY.succ(return_url);
			else window.location.href=return_url;
		},
		in_ajax=false,
		tips=function(status){
			if(w.__LPAY.tips)w.__LPAY.tips(status);
			if(in_ajax){
				in_ajax=status;				
				return false;
			}else{
				in_ajax=status;
				return true;
			}
		},
		utils=w.__LPAY.utils;
	utils.on(form,'submit',function(){
		var els=document.getElementsByTagName('input'),
			i=0;
		for(;i<els.length;i++){
			var t=els[i].getAttribute('type');
			if(t=='hidden'||t=='checkbox'||t=='radio'||t=='button'||t=='submit')continue;
			if(!/^\s*$/.test(els[i].value))continue;
			if(els[i].getAttribute('required')=='required'||els[i].getAttribute('required')=='true'){
				els[i].focus();return ;
			}
		}
		if(!tips(true)) return ;
		utils.ajax({
			method : 'post',
			url :pay_url+(pay_url.indexOf('?')==-1?'?':'&')+Math.random(), 
			async : true,
			data :w.__LPAY.utils.serialize_form(form),
			success : function (data) {
				tips(false);
				var _data=w.__LPAY.utils.to_json(data);
				if(_data.status)fn_succ();
				else fn_fail(_data.data);
			},
			error:function(status,msg){
				tips(false);
				fn_fail(msg);
			}
		});
	});
})(window);
//fill form
(function(w){
	var create_op=function(name,val){
			var op=document.createElement('option');
			op.value=val;
			op.text=name;
			return op;
		};
	var se=document.getElementById('lpay_expDateYear'),
		year=(new Date()).getFullYear(),
		max=10,
		select=2;
	;
	for(var i=0;i<max;i++){
		var op=create_op(year+i,year+i);
		se.add(op);
		if(i+1==select)op.selected=true;
	}
})(window);
//-->
</script>