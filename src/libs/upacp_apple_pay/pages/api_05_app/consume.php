<form class="api-form" method="post" action="demo/api_05_app/Form_6_2_AppConsume.php" target="_blank">
<p>
<label>商户号：</label>
<input id="merId" type="text" name="merId" placeholder="" value="777290058110048" title="默认商户号仅作为联调测试使用，正式上线还请使用正式申请的商户号" required="required"/>
</p>
<p>
<label>交易金额：</label>
<input id="txnAmt" type="text" name="txnAmt" placeholder="交易金额" value="1000" title="单位为分 " required="required"/>
</p>
<p>
<label>订单发送时间：</label>
<input id="txnTime" type="text" name="txnTime" placeholder="订单发送时间，YYYYMMDDhhmmss格式" value="<?php echo date('YmdHis')?>" title="取北京时间" required="required"/>
</p>
<p>
<label>商户订单号：</label>
<input id="orderId" type="text" name="orderId" placeholder="商户订单号" value="<?php echo date('YmdHis')?>" title="自行定义，8-32位数字字母 " required="required"/>
</p>
<p>
<label>&nbsp;</label>
<input type="submit" class="button" value="获取tn" />
<input type="button" class="showFaqBtn" value="遇到问题？" />
</p>
</form>

<div class="question">
<hr />
<h4>获取tn您可能会遇到...</h4>
<p class="faq">
<a href="https://open.unionpay.com/ajweb/help/faq/list?id=4&level=0&from=0" target="_blank">测试卡信息</a><br>
<a href="https://open.unionpay.com/ajweb/help/faq/list?id=24&level=0&from=0" target="_blank">http400错误</a><br>
<a href="https://open.unionpay.com//ajweb/help/respCode/respCodeList?respCode=9100004" target="_blank">测试环境9100004</a><br>
<a href="https://open.unionpay.com/ajweb/help/faq/list?id=40&level=0&from=0" target="_blank">正式环境9100004</a><br>
</p>
<hr />
<?php include $_SERVER ['DOCUMENT_ROOT'] . '/upacp_demo_app/pages/more_faq.php';?>
</div>
