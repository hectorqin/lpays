<form class="api-form" method="post" action="demo/api_05_app/Form_6_7_2_PreauthUndo.php" target="_blank">
<p>
<label>商户号：</label>
<input id="merId" type="text" name="merId" placeholder="" value="777290058110048" title="默认商户号仅作为联调测试使用，正式上线还请使用正式申请的商户号" required="required"/>
</p>
<p>
<label>订单发送时间：</label>
<input id="txnTime" type="text" name="txnTime" placeholder="订单发送时间" value="<?php echo date('YmdHis')?>" title="取北京时间，YYYYMMDDhhmmss格式" required="required"/>
</p>
<p>
<label>商户订单号：</label>
<input id="orderId" type="text" name="orderId" placeholder="商户订单号" value="<?php echo date('YmdHis')?>" title="自行定义，8-32位数字字母" required="required"/>
</p>
<p>
<label>交易金额：</label>
<input id="txnAmt" type="text" name="txnAmt" placeholder="交易金额" value="" title="单位分，需与原预授权一致" required="required"/>
</p>
<p>
<label>原交易流水号：</label>
<input id="origQryId" type="text" name="origQryId" placeholder="原交易流水号" value="" title="原交易流水号，从原预授权的查询或通知接口中获取 " required="required"/>
</p>
<p>
<label>&nbsp;</label>
<input type="submit" class="button" value="提交" />
<input type="button" class="showFaqBtn" value="遇到问题？" />
</p>
</form>

<div class="question">
<hr />
<h4>预授权撤销您可能会遇到...</h4>
<p class="faq">
测试的人不多，木有足量样本/(ㄒoㄒ)/~~<br>
<br>
</p>
<hr />
<?php include $_SERVER ['DOCUMENT_ROOT'] . '/upacp_demo_app/pages/more_faq.php';?>
</div>