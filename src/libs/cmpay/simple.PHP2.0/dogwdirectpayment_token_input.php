<html>
	<head>
		<title>��ʱ����(��ͨ)��DODIRECTPAYMENT</title>
		<link href="css/sdk.css" rel="stylesheet" type="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
	</head>

	<body>
      <form name="form1" method="post" action="dogwdirectpayment_token.php">
         <br>
         <center>
            <input type="hidden" name="merCert" value="">
            <font size=2 color=black face=Verdana><b>ֱ��֧��(��������)��GWDirectPay</b>
            </font>
            <br>
            <br>
            <table class="api">
               <tr>
                  <td class="field">�������</td>
                  <td>
                     <input type="text" name="amount" maxlength='20' value="1000">
                     <font color="red">*�������Է�Ϊ��λ</font>
                  </td>
               </tr>
               <tr>
                  <td class="field">���д���</td>
                  <td>
                     <select name="bankAbbr">
                        <option value="ICBC">��������</option>
												<option value="CMB">��������</option>
												<option value="CCB">��������</option>
												<option value="ABC">ũҵ����</option>
												<option value="BOC">�й�����</option>
												<option value="SPDB">�Ϻ��ֶ���չ����</option>
												<option value="BCOM">��ͨ����</option>
												<option value="CMBC">��������</option>
										    <option value="CEBB">�������</option>
												<option value="GDB">�㶫��չ����</option>
												<option value="ECITIC">��������</option>
												<option value="HXB">��������</option>
												<option value="CIB">��ҵ����</option>
                        <option value="PSBC">������������</option>
                     </select>
                  </td>
               </tr>
               <tr>
                  <td class="field">����</td>
                  <td>
                     <select name="currency">
                         <option value="00">
                           CNY-������
                        </option>
                   
                     </select>
                  </td>
               </tr>
               <tr>
                  <td class="field">��������</td>
                  <td>
                     <input type="text" name="orderDate" value="<?php echo date('Ymd')?>">
                     <font color="red">*�̻����������ʱ��; ����������������</font>
                  </td>
               </tr>
               <tr>
                  <td class="field">�̻�������</td>
                  <td>
                     <input type="text" name="orderId" value="<?php $dt=date('Ymd');$tm=time(); echo $dt.$tm;?>">
                     <font color="red">*</font>
                  </td>
               </tr>
               <tr>
               		<td>�̻��������</td>
               		<td><input type="text" name="merAcDate" value="<?php $dt=date('Ymd') ; echo $dt;?>"/></td>
              </tr>
               <tr>
                  <td class="field">��Ч������</td>
                  <td>
                     <input type="text" name="period" value="01">
                     <font color="red">*����</font>
                  </td>
               </tr>
               <tr>
                  <td class="field">��Ч�ڵ�λ</td>
                  <td>
                     <select name="periodUnit">
                       <option value="00">
                           00-��
                        </option>
                        <option value="01">
                           01-Сʱ
                        </option>
                        <option value="02" selected>
                           02-��
                        </option>
                        <option value="03">
                           03-��
                        </option>
                     </select>
                  </td>
               </tr>
               <tr>
               		<td>
               			�̻�չʾ����
               		</td>	
               		<td>
               				<input type="text" name="merchantAbbr" />
               		</td>
              </tr>
               <tr>
                  <td class="field">��Ʒ����</td>
                  <td>
                     <input type="text" name="productDesc" value="��Ʒ����01">
                  </td>
               </tr>
               <tr>
                  <td class="field">��Ʒ���</td>
                  <td>
                     <input type="text" name="productId" value="��Ʒ���01">
                  </td>
               </tr>
               <tr>
                  <td class="field">��Ʒ����</td>
                  <td>
                     <input type="text" name="productName" value="������Ʒ01">
                     <font color="red">*</font>
                  </td>
               </tr>
               <tr>
                  <td class="field">��Ʒ����</td>
                  <td>
                     <input type="text" name="productNum" value="1"/>
                  </td>
               </tr>
               <tr>
                  <td class="field">�����ֶ�1</td>
                  <td>
                     <input type="text" name="reserved1" value="��������1">
                  </td>
               </tr>
               <tr>
                  <td class="field">�����ֶ�2</td>
                  <td>
                     <input type="text" name="reserved2" value="��������2">
                  </td>
               </tr>
               <tr>
                  <td class="field">�û���ʶ</td>
                  <td>
                     <input type="text" name="userToken" value="13548649407">
                     <font color="red">*</font>
                  </td>
               </tr>
               <tr>
                  <td class="field">��Ʒչʾ��ַ</td>
                  <td>
                     <input type="text" name="showUrl" value=""/>
                  </td>
               </tr>
               <tr>
                  <td class="field">Ӫ������ʹ�ÿ���</td>
                  <td>
                     <input type="text" name="couponsFlag" value=""/>
                  </td>
               </tr>
               <tr>
                  <td class="field">
                  </td>
                  <td>
                     <input type="Submit" value="�ύ" id="Submit" name="submit" />
                  </td>
               </tr>
            </table>
         </center>
         <a id="HomeLink" class="home" href="index.php">��ҳ</a>
      </form>
	</body>
</html>
