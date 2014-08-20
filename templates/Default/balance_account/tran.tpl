<!-- Перевод средств -->
  <table width="100%" class="table table-normal">
   <tr>
    <td>Пользователь:</td>
    <td><input name="tran_user" id="tran_user" type="text" style="width:160px;" class="f_input" /></td>
    <td rowspan="4" align="left" valign="middle" width="30%"><font size="16" color="green">$&rArr;</font><br /><font size="4" color="green">{balance} {payment_cont}</font></td>
   </tr>
   <tr>
    <td>Сумма:</td>
    <td><input name="tran_unit" id="tran_unit" type="text" value="100.00" style="width:160px;" class="f_input" /> {payment_cont}</td>
   </tr>
  [sec_code]
   <tr>
    <td>Введите код:</td>
    <td><input name="tran_captcha" id="tran_captcha" type="text" value=""  style="width:160px;" class="f_input" /></td>
   </tr>
   <tr>
    <td></td>
    <td>{payment_captcha}</td>
   </tr>
  [/sec_code]
   <tr>
    <td></td>
    <td colspan="2"><button type="submit" onClick="kgba_tran()" name="tran_do" name="submit" class="fbutton">Далее</button></td>
   </tr>
  </table>