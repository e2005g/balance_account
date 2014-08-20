<!-- Вывод средств, форма запроса -->
<table width="100%">
 <tr>
  <td>Сумма:</td>
  <td><input name="mb_user" id="mb_money" type="text" value="{outmoney_min}" style="width:60px;" class="f_input" /> {payment_cont}, <i>минимум {outmoney_min}, комиссия: {outmoney_com}</i></td>
 </tr>
 <tr>
  <td>Реквезиты для вывода:</td>
  <td><textarea rows="4" cols="35" name="mb_info" id="mb_info" class="f_input" /></textarea></td>
 </tr>
[sec_code]
 <tr>
  <td>Введите код:</td>
  <td><input name="mb_captcha" id="mb_captcha" type="text" style="width:150px;" class="f_input" /></td>
 </tr>
  <tr>
  <td></td>
  <td>{payment_captcha}</td>
 </tr>
[/sec_code]
 <tr>
  <td></td>
  <td><button type="submit" onClick="kgba_mb()" name="mb_do" name="submit" class="fbutton">Далее</button></td>
 </tr>
</table>