<!-- Личный кабинет, меню -->
<style type="text/css">
 #setmenu {
	border: 1px solid #8ca3ab;
	color: #000;
	margin: 2px;
	padding: 5px;
	cursor:pointer;
	width: 180px;
 }
 #setmenu:hover {
  background-color: #EDEDED;
 }
</style>

<p style="float:left;padding:10px;padding-left:30px;">на счету:<br /><b><font size="5" color="green">{balance} {balance_cont}</font></b></p>

<table width="60%" align="center">
 <tr>
  <td align="center"><div id="setmenu" onClick="kgba_select_payment('select')">Пополнить баланс</div></td>
  <td align="center"><div id="setmenu" onClick="load_page_pay('moneyback')">Вывести средства</div></td>
 </tr>
 <tr>
  <td align="center"><div id="setmenu" onClick="load_page_pay('tran')">Сделать перевод</div></td>
  <td align="center"><div id="setmenu" onClick="load_page_pay('log')">Отчёты</div></td>
 </tr>
 <tr>
  <td align="center"><div id="setmenu" onClick="load_page_pay('bills')">Мои счета</div></td>
  <td></td>
 </tr>
</table>