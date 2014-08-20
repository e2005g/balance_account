<?php
/*
=====================================================
 Баланс пользователя
-----------------------------------------------------
 evgeny.tc@gmail.com
-----------------------------------------------------
 Данный код защищен авторскими правами
=====================================================
*/

    /* Select */
    function makeDropDown($options, $name, $selected) {
        $output = "<select class=\"uniform\" style=\"min-width:100px;\"  name=\"$name\">\r\n";
        foreach ( $options as $value => $description ) {
            $output .= "<option value=\"$value\"";
            if( $selected == $value ) {
                $output .= " selected ";
            }
            $output .= ">$description</option>\n";
        }
        $output .= "</select>";

        return $output;
    }

  /* Переключатель */
	function makeCheckBox($name, $selected) {
		$selected = $selected ? "checked" : "";
		return "<input class=\"iButton-icons-tab\" type=\"checkbox\" name=\"$name\" value=\"1\" {$selected}>";
	}
	
/* Paging */
function kgPaging( $all_count, $this_page, $link, $per_page = 10) {
  global $ba_lang;

		$enpages_count = @ceil( $all_count / $per_page );
		$enpages_start_from = 0;
		$enpages = "";
		
		for($j = 1; $j <= $enpages_count; $j ++) {
			if( $this_page != $j ) {
				$enpages .= str_replace("{p}", $j, $link);
			} else {
				$enpages .= " {$j} ";
			}
			$enpages_start_from += $per_page;
		}
		
		return str_replace("{paging}", $enpages, $ba_lang['bills_pageing']);
}

/* Сохранить настройки */
function save_setting($file, $save_con, $title_arr) {

	$handler = fopen( ENGINE_DIR . '/modules/balance_account/data/'.$file.'.php', "w" );

    fwrite( $handler, "<?PHP \n\n//Настройки модуля \n\n\${$title_arr} = array (\n\n" );
    foreach ( $save_con as $name => $value ) {

        $value = str_replace( "$", "&#036;", $value );
        $value = str_replace( "{", "&#123;", $value );
        $value = str_replace( "}", "&#125;", $value );
        
        $name = str_replace( "$", "&#036;", $name );
        $name = str_replace( "{", "&#123;", $name );
        $name = str_replace( "}", "&#125;", $name );
        
        fwrite( $handler, "'{$name}' => \"{$value}\",\n\n" );
    
    }

    fwrite( $handler, ");\n\n?>" );
    fclose( $handler );
    
    return true;
}

/* Генерация шифра */
function genCode($length = 8){
  $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
  $numChars = strlen($chars);
  $string = '';
  for ($i = 0; $i < $length; $i++) {
  $string .= substr($chars, rand(1, $numChars) - 1, 1);
  }
  return $string;
}

/* Select */
function get_selects($onarray, $selects) {

	$returnstring = "";
	
	foreach ( $selects as $id=>$name ) {
		$returnstring .= '<option value="' . $id . '" ';
		
		 if( in_array($id, $onarray) ) $returnstring .= 'SELECTED';
		
		$returnstring .= ">" . $name . "</option>\n";
	}
	
	return $returnstring;
}

/* Статус */
function switch_status($type) {

 switch($type) {
  case 1: return "<font color=green>Вкл.</font>"; break;
  case 0: return "<font color=red>Выкл.</font>"; break;
 }
 
}

?>