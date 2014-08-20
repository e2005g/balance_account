<!-- Создание счёта, инфо -->
  Для Вас был создан счёт #{shot_id}.
  <br />Для пополнения баланса Вам необходимо оплатить счёт на сумму {shot_money} {shot_money_cont} в системе {shot_payment}.
  <p align="center">
    <button type="submit" onClick="showBill('{shot_id}')" class="fbutton">Оплатить</button>
    <div id="winBill" title="Оплата счёта" style="display:none;"></div>
  </p>