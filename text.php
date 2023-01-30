<?php

include("simple_html_dom.php");


$status = file_get_html('https://eztvstatus.com');

foreach( $status->find('a') as $domain){
	if($domain->class == 'domainLink'){
		$available = @file_get_html($domain->href);
		if($available != false){
			$dom = $domain->href;
			break;
		}
	}
}

$show = $_REQUEST['show'];
$q = $_REQUEST['quality'];

//TEST
//$show = "488560/the-last-of-us";
//$q = 2160;

$html = file_get_html( $dom . '/shows/'. $show);


header("content-type:text/text");

echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE torrent PUBLIC "-//bitTorrent//DTD torrent 0.1//EN" "http://xmlns.ezrss.io/0.1/dtd/">
<rss version="2.0">
	<channel>
		<title>ezRSS - ' . $show . ' ' .  $q . 'p</title>
	<description>Custom RSS feed based off search filters.</description>', PHP_EOL;

$found = array();

foreach ($html->find('a') as $element) {
	if ($element->class == 'magnet') { //download_1
		$title = $element->parent()->prev_sibling()->first_child()->title;
		if (str_contains($title, $q . 'p') || str_contains($title, $q . 'P')) {
			preg_match('/S\d{2}E\d{2}/', $title, $matches);
			$ep = $matches[0];

			$seedText = $element->parent()->next_sibling()->next_sibling()->next_sibling()->first_child();
			if (!is_null($seedText)) {
				$seeds = substr($seedText->__toString(), 20);
				$seeds = str_replace(",", "", $seeds);
				$seeds = intval($seeds);
			} else $seeds = 0;

			if (array_key_exists($ep, $found)) {
				$e = array('title' => $title, 'magnet' => $element->href, 'seeds' => $seeds);
				$found[$ep][] = $e;
			} else {
				$found[$ep] = array(array('title' => $title, 'magnet' => $element->href, 'seeds' => $seeds));
			}

			// echo "<item>";
			// echo '<title><![CDATA['.$element->parent()->prev_sibling()->first_child()->title.']]></title>
			// 	<link><![CDATA['.$element->href.']]></link>';
			// echo "</item>";
			// echo('      ....     ');
		}
	}
}

//var_dump($found);

foreach ($found as $key => $value) {

	$maxs = array_keys(array_map("findseed", $value), max(array_map("findseed", $value)))[0];
	$element = $value[$maxs];
	echo "<item>", PHP_EOL;
	echo '<title>'.$element["title"].'</title>', PHP_EOL,
			'<description>Seeds: '. $element['seeds'] . '</description>', PHP_EOL,
			'<link><![CDATA['.$element['magnet'].']]></link>', PHP_EOL;
	echo "</item>";
}

function findseed($v)
{
	return $v['seeds'];
}

echo '</channel>
</rss>';
