<!-- Пополнение баланса, заполнение данных -->
  <table width="100%"  class="table table-normal">
   <tr>
    <td>Платёжная система:</td>
    <td><a href="{payment_info_link}" target="_blank">{payment_name}</a></td>
    <td rowspan="5" align="center" valign="top" width="30%"><img src="{payment_info_icon}" title="{payment_info_desc}"><br />&#8592; <a href="#" onClick="kgba_select_payment('select')">выбрать другой способ</a></td>
   </tr>
   <tr>
    <td>Введите сумму:</td>
    <td><input name="pay_unit" id="pay_unit" type="text" value="100.00" style="width:160px;" class="f_input" /> {payment_info_cont}</td>
   </tr>
  [sec_code]
   <tr>
    <td>Введите код:</td>
    <td><input name="pay_captcha" id="pay_captcha" type="text" value="" style="width:160px;" class="f_input" /></td>
   </tr>
   <tr>
    <td></td>
    <td>{payment_captcha}</td>
   </tr>
  [/sec_code]
   <tr>
    <td></td>
    <td><button type="submit" onClick="kgba_select_payment('end', '{payment_select}')" name="pay_do" name="submit" class="fbutton">Далее</button></td>
   </tr>
  </table>