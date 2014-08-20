<!-- Модальное окно, оплата счёта -->
<table width="100%" class="table table-normal">
 <tr>
  <td>Счёт ID:</td>
  <td>{shot_id}</td>
 </tr>
 <tr>
  <td>К оплате:</td>
  <td>{shot_money} {shot_money_cont}</td>
 </tr>
 <tr>
  <td>Платёжная система:</td>
  <td>{shot_payment}</td>
 </tr>
 <tr>
  <td colspan="2" align="center"><button type="submit" onClick="removeBill('{shot_id}')" name="submit" class="fbutton">Удалить</button> {shor_pay}</td>
 </tr>
</table>