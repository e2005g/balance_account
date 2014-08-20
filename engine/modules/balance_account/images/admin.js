/*
=====================================================
 Баланс пользователя
-----------------------------------------------------
 evgeny.tc@gmail.com
-----------------------------------------------------
 Данный код защищен авторскими правами
=====================================================
*/

/* Просмотр счетов, визуальный выбор */
function select_sort( sort ) {

	var arr = [ 'select_all', 'select_ok', 'select_no' ]

	for(i=0; i<=2; i++) {
		if( arr[i] == sort ) {
			document.getElementById(arr[i]).className='select';
		} else {
			document.getElementById(arr[i]).className='select_n';
		}
	}

	document.getElementById('selectsort').value=sort;
	load_list( '1' );

	return false;
};

/* Просмотр счетов, загрузка списка */
function load_list( page ) {

	var selectsort = document.getElementById('selectsort').value;

	$.get("engine/modules/balance_account/user/load_control.php", {page: "../admin/load_list_paybills", type: selectsort, paging: page}, function(data){
    $("#list_paybills").replaceWith( data );
  });

	return false;
};

/* Элементов в массиве */
function count(array) {
	var cnt=0;
	for (var i in array) {
		if (i) {
			cnt++
			}
		}
	return cnt
}

/* Проверка вхождения элемента в массив */
function in_array(needle, haystack, strict) {
	var found = false, key, strict = !!strict;
	for (key in haystack) {
		if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
			found = true;
			break;
		}
	}

	return found;
}

/* Просмотр счетов, отметить  ключ */
function add_for( key ) {

	var havelist = document.getElementById('selectfordo').value;
	var havelistarr = havelist.split(',');
	var setnewlist = "";
	var searchlist = 0; 

	if( in_array(key, havelistarr) ) {
		document.getElementById("selectbill_"+key).className='select_n';
		for(i=0;i<=count(havelistarr); i++) { if(havelistarr[i]!=key && havelistarr[i]) { setnewlist+=havelistarr[i]+","; } }
	} else {
		document.getElementById("selectbill_"+key).className='select';
		setnewlist += havelist+key+",";
	}

	document.getElementById('selectfordo').value = setnewlist;	
	return false;
};

/* Просмотр счетов, выполнить действие: оплатить/удалить */
function doact(wdo, info) {

	if(info==undefined) {
		info = document.getElementById('selectfordo').value;
	}
	

  $.get("engine/modules/balance_account/user/load_control.php", { page: "../admin/load_paybill_remove", act: wdo, keys: info }, function(data){
		load_list('1');
  });

	return false;
};