#国内统一支付接口
> 配置及使用示例参考 ./dome 目录 

1. 添加新支付实现接口 LPAY\Pay\PayAdapter 接口
	a. 当支付接口有回调页面 实现 LPAY\Pay\PayAdapterCallback 接口
	b. 当支付接口有后台通知 实现 LPAY\Pay\PayAdapterNotify 接口

2. 支付接口支持直接状态查询 实现 LPAY\Pay\Query 接口

3. 已集成支付接口
	1. 支付宝 [包含 PC WAP 及 APP]
	2. 百付宝[百度] [包含 PC WAP 银行直连]
	3. 京东 [包含 PC WAP]
	4. Palpay [货币可传美元,或内部自动转换] [包含 PC WAP 信用卡直付]
	5. 财付通 [包含 PC WAP]
	6. 银联[包含apple pay] [包含 PC WAP]
	7. 微信支付 [包含WAP 扫码 及 APP]
	
4. 已集成支付接口都已实现并统一 *退款接口* 及 *订单查询接口*

5. 实现转账接口
	1. 支付宝[需跳页面输入支付密码]
	2. 微信

6. 对账单下载
        1. 支付宝
        2. 微信