<?php
/**
 * Содержание для больших постов.
 * Автор: Тимур Камаев - http://wp-kama.ru доработал Андрей Морковин — http://www.sdelaysite.com
 * Версия 0.2-1.4
 */
add_filter('the_content', 'make_contents');
function make_contents($content){
	global $_mc_contain, $left_adw_contents, $right_adw_contents, $auto_insert_contents, $noindex_contents, $min_amount_title_contents, $content_title_type;

	if($auto_insert_contents == 1 or $auto_insert_contents == 2){
		if( !is_singular() )
			return $content;
	}else{
		if( !is_singular() || strpos($content, '[contents')===false )
			return $content;
	}

	$_mc_contain = array();

	// получаем данные о содержании
	if($auto_insert_contents == 1 or $auto_insert_contents == 2){
		$content_morkovin = "[contents $content_title_type]".$content;
		preg_match('~\[contents\s*([^\]]*)\](.*)~s', $content_morkovin, $m);
	}else{
		preg_match('~\[contents\s*([^\]]*)\](.*)~s', $content, $m);
	}

	$hds = $m[1] ? trim($m[1]) : 'h2';
	$hds = explode(' ', $hds);
	$hds = array_map('trim', $hds);
	$h = implode('|', $hds);

	// заменяем заголовки в контенте на ссылки к меню
	$_content = $m[2];
	// считаем общее количество
	preg_match_all('@</('.$h.')>@i', $_content, $n);
	$_mc_contain['all'] = count($n[0]);
	$_content = preg_replace_callback('@<(?:'.$h.')[^>]*>(.*?)</('.$h.')>@is', '_make_contents', $_content);

	// вставляем содержание

	if(!is_front_page() and !is_page() and $_mc_contain['contents']) $contents = implode( "", $_mc_contain['contents'] );

	if($auto_insert_contents == 1 or $auto_insert_contents == 2){
		$_content1 = "";
	}else{
		$_content1 = $_content;
	}

	$out = '';
	if($left_adw_contents != "" and $right_adw_contents != "" and $_mc_contain['contents']){
		$out = '<!--noindex--><div class="table-of-content"><div class="table-of-content__title">Содержание</div>';
		$out .= "\n<ul id='с_menu' class='contents'>\n". $contents ."</ul>\n</div><!--/noindex-->". $_content1;	
	}
	elseif($left_adw_contents != "" and $_mc_contain['contents']){	
		$out = '<div class="contents-block"><div class="left-adw-contents-block">'.$left_adw_contents.'</div>';
		$out .= '<!--noindex--><div class="table-of-content content-right"><div class="table-of-content__title">Содержание</div>';
		$out .= "\n<ul id='с_menu' class='contents'>\n". $contents ."</ul>\n</div><!--/noindex--></div><!--.contents-block-->". $_content1;
	}elseif($right_adw_contents != "" and $_mc_contain['contents']){
		$out = '<div class="contents-block"><!--noindex--><div class="table-of-content content-left"><div class="table-of-content__title">Содержание</div>';
		$out .= "\n<ul id='с_menu' class='contents'>\n". $contents ."</ul>\n</div><!--/noindex-->";
		$out .= '<div class="right-adw-contents-block">'.$right_adw_contents.'</div></div><!--.contents-block-->'. $_content1;
	}
	elseif($_mc_contain['contents']){
		$out = '<!--noindex--><div class="table-of-content"><div class="table-of-content__title">Содержание</div>';
		$out .= "\n<ul id='с_menu' class='contents'>\n". $contents ."</ul>\n</div><!--/noindex-->". $_content1;	
	}

	if($auto_insert_contents == 1 and is_single() and $_mc_contain['all'] >= $min_amount_title_contents){
		$first_img_url = catch_that_image();
		$alt_img = get_the_title();
		$condition = '/<img.*? src=\"'.preg_quote($first_img_url, "/").'\"[^>]*>/i';
		$replace = '<div class="first-single-img"><img src="'.$first_img_url.'" alt="'.$alt_img.'"></div>'.$out;
		$content = preg_replace($condition, $replace, $_content);
	}elseif($auto_insert_contents == 2 and is_single() and $_mc_contain['all'] >= $min_amount_title_contents){
		$content = $out.$_content;
	}
	else{
		$content = str_replace($m[0], $out, $content);
	}


	unset($_mc_contain);

	return $content;
}
function _make_contents($match){
	global $_mc_contain;

        $_mc_contain ['n'] = 'n'; // инициализациия элемента массива n.
        $_mc_contain['open'] = 'open'; // инициализациия элемента массива open.

	$anchor = $match[2] .'_'. ++$_mc_contain['n'] ;

	$li = "\t<li><a href=\"#$anchor\">$match[1]</a></li>\n";
	$index = (int) preg_replace("/[^0-9]/", '', $match[2]);
	$prev_index = ($r =(int) @end($_mc_contain['index'])) ? $r : null;
	$_mc_contain['index'][] = $index;

	// условия вывода содержания
	$close = $element = '';
	if( !isset($prev_index) || $prev_index == $index )
		$element = $li;

	elseif( $prev_index < $index ){
		++$_mc_contain['open'];
		$element = "\t<ul>\n$li";
	}
	else {
		if( $prev_index > $index ){
			$close = "\t</ul>\n$li";
			// убираем одну открытую
			--$_mc_contain['open'];
		} 
	}
	// закрываем если надо
	if( $_mc_contain['n'] == $_mc_contain['all'] && $_mc_contain['open'] ){
		$close = "\t</ul>\n";
	}

	$_mc_contain['contents'][] = "$element $close"; 

	$out = $_mc_contain['n'] == 1 ? '' : "";
	$out .= "<$match[2] id=\"$anchor\">$match[1]</$match[2]>";

	return $out;
}
function catch_that_image() {
	global $post, $posts;
	$first_img = '';
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = $matches [1] [0];
	if(!empty($first_img)) return $first_img;
}

//Выводить содержание автоматически после первой картинки ($auto_insert_contents = 1), автоматически в начало поста ($auto_insert_contents = 2) или не выводить автоматически ($auto_insert_contents = 3). 
//Если автоматический вывод содержания выключен — нужно использовать шорткод [contents].
$auto_insert_contents = 2;

//Реклама слева от содержания
$left_adw_contents = '';

//Реклама справа от содержания
$right_adw_contents = '';

//Минимальное количество заголовков в статье для отображения содержания
$min_amount_title_contents = 3;

//Типы заголовков, попадающие в содержание
//Через пробел нужно перечислить типы заголовков, попадающих в содержание ($content_title_type = "h2"; или $content_title_type = "h2 h3";)
$content_title_type = "h2 h3";
?>