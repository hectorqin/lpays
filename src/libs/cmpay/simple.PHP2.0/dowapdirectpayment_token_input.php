<html>
	<head>
		<title>直接支付WAP(TOKEN)：WAPDirectPayConfirm</title>
		<link href="css/sdk.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	</head>

	<body>
      <form name="form1" method="post" action="dowapdirectpayment_token.php">
         <br>
         <center>
            <input type="hidden" name="merCert" value="">
            <font size=2 color=black face=Verdana><b>直接支付WAP(TOKEN)：WAPDirectPayConfirm</b>
            </font>
            <br>
            <br>
            <table class="api">
               <tr>
                  <td class="field">订单金额</td>
                  <td>
                     <input type="text" name="amount" maxlength='20' value="1000">
                     <font color="red">*订单金额，以分为单位</font>
                  </td>
               </tr>
               <tr>
                  <td class="field">银行代码</td>
                  <td>
                     <select name="bankAbbr">
                        <option name="ICBC"  value="ICBC" >工商银行</option>
                        <option name="CMB"   value="CMB"  >招商银行</option>
                        <option name="CCB"   value="CCB"  >建设银行</option>
                        <option name="ABC"   value="ABC"  >农业银行</option>
                        <option name="BOC"   value="BOC"  >中国银行</option>
                        <option name="SPDB"  value="SPDB" >上海浦东发展银行</option>
                        <option name="BCOM"  value="BCOM" >交通银行</option>
                        <option name="CMBC"  value="CMBC" >民生银行</option>
                        <option name="CEBB"   value="CEBB"  >光大银行</option>
                        <option name="GDB"   value="GDB"  >广东发展银行</option>
                        <option name="ECITIC" value="ECITIC">中信银行</option>
                        <option name="HXB"   value="HXB"  >华夏银行</option>
                        <option name="CIB"   value="CIB"  >兴业银行</option>
                        <option name="PSBC" value="PSBC">邮政储蓄银行</option>
                     </select>
                  </td>
               </tr>
               <tr>
                  <td class="field">币种</td>
                  <td>
                     <select name="currency">
                         <option value="00">
                           CNY-可提现
                        </option>
                    
                     </select>
                  </td>
               </tr>
               <tr>
                  <td class="field">订单日期</td>
                  <td>
                     <input type="text" name="orderDate" value="<?php echo date('Ymd')?>">
                     <font color="red">*商户发起请求的时间; 年年年年月月日日</font>

                  </td>
               </tr>
               <tr>
                  <td class="field">商户订单号</td>
                  <td>
                     <input type="text" name="orderId" value="<?php $dt=date('Ymd');$tm=time(); echo $dt.$tm;?>">
                     <font color="red">*</font>
                  </td>
               </tr>
               <tr>
               		<td>商户会计日期</td>
               		<td><input type="text" name="merAcDate" value="<?php $dt=date('Ymd') ; echo $dt;?>"/></td>
              </tr>
               <tr>
                  <td class="field">有效期数量</td>
                  <td>
                     <input type="text" name="period" value="07">
                     <font color="red">*数字</font>
                  </td>
               </tr>
               <tr>
                  <td class="field">有效期单位</td>
                  <td>
                     <select name="periodUnit">
                       <option value="00">
                           00-分
                        </option>
                        <option value="01">
                           01-小时
                        </option>
                        <option value="02" selected>
                           02-日
                        </option>
                        <option value="03">
                           03-月
                        </option>
                     </select>
                  </td>
               </tr>
               <tr>
               		<td>
               			商户展示名称
               		</td>	
               		<td>
               				<input type="text" name="merchantAbbr" />
               		</td>
              </tr>
               <tr>
                  <td class="field">商品描述</td>
                  <td>
                     <input type="text" name="productDesc" value="商品描述01">
                  </td>
               </tr>
               <tr>
                  <td class="field">商品编号</td>
                  <td>
                     <input type="text" name="productId" value="商品编号01">
                  </td>
               </tr>
               <tr>
                  <td class="field">商品名称</td>
                  <td>
                     <input type="text" name="productName" value="测试商品01">
                     <font color="red">*</font>
                  </td>
               </tr>
               <tr>
                  <td class="field">商品数量</td>
                  <td>
                     <input type="text" name="productNum" value="1"/>
                  </td>
               </tr>
               <tr>
                  <td class="field">保留字段1</td>
                  <td>
                     <input type="text" name="reserved1" value="保留数据1">
                  </td>
               </tr>
               <tr>
                  <td class="field">保留字段2</td>
                  <td>
                     <input type="text" name="reserved2" value="保留数据2">
                  </td>
               </tr>
               <tr>
                  <td class="field">用户标识</td>
                  <td>
                     <input type="text" name="userToken" value="13548649407">
                     <font color="red">*</font>
                  </td>
               </tr>
               <tr>
                  <td class="field">商品展示地址</td>
                  <td>
                     <input type="text" name="showUrl" value=""/>
                  </td>
               </tr>
               <tr>
                  <td class="field">营销工具使用控制</td>
                  <td>
                     <input type="text" name="couponsFlag" value=""/>
                  </td>
               </tr>
               <tr>
                  <td class="field">
                  </td>
                  <td>
                     <input type="Submit" value="提交" id="Submit" name="submit" />
                  </td>
               </tr>
            </table>
         </center>
         <a id="HomeLink" class="home" href="index.php">首页</a>
      </form>
	</body>
</html>
