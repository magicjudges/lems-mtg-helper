<?php

//cardlinking per jTip
function mtgh_card($content){

	//preload all image-files
	preg_match_all('/(\[CARD\])(.*)(\[\/CARD\])/iU',$content,$result);
	$preload = cache_cards($result[2]);

	//replace the [card]-tags with the referer for the card images
	$content = preg_replace_callback(
	'/(\[CARD\])(.*)(\[\/CARD\])/iU',
	'parse_card_url',
	$content);

	return $content.$preload;
	
}

function parse_card_url ($card_names){

	return '<a href="' . get_bloginfo('wpurl') . LEMS_MTG_DIR 
	.'/lems-mtg-helper-cardfinder.php?find=' . urlencode($card_names[2]) . '&width=223&height=310" class="jTip" name="">'
	. $card_names[2] . '</a>';
}
