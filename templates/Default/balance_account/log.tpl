<!-- Отчёты -->
<script type="text/javascript">
$(function () {
        $('#container').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'Текущий баланс: {balance} {balance_cont}'
            },
            subtitle: {
                text: '{start_diag} - {end_diag}'
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: { // don't display the dummy year
                    month: '%e. %b',
                    year: '%b'
                },
                title: {
                    text: 'Дата и время'
                }
            },
            yAxis: {
                title: {
                    text: '{balance_cont}'
                },
            },
            tooltip: {
                headerFormat: '<b>{point.y} {balance_cont}</b><br>',
                pointFormat: '{point.x:%e %b}'
            },

            series: [{
                name: '{user_name}',
                data: [
                    {set_jquery}
                ]
            }]
        });
    });


                </script>

<div id="container" style="width: 660px; height: 300px; margin: 0 auto"></div>
<table width="100%" class="table table-normal">
 <tr>
  <td width="25%"><b>Дата и время</b></td>
  <td width="25%"><b>Изменение</b></td>
  <td><b>Комментарий</b></td>
 </tr>
 {log}
 <tr>
  <td colspan="3" align="center">{paging}</td>
 </tr>
</table>