<?php

if ( isset($_GET['find']) ) {
	if ( isset($_GET['set']) ) {
		$card_url = get_source_from_name( $_GET['find'], $_GET['set'] );
	} else {
		$card_url = get_source_from_name( $_GET['find'] );
	}
	echo "<img src=\"$card_url\" />";
	die();
}

function get_source_from_name( $name, $set = '' ) {
	$name = htmlspecialchars( str_replace( "&#8217;", "%27", urldecode( $name ) ) );
	if ( $set ) {
		$set = '&set=' . translate_set_abbreviations($set);
	}
	return "http://gatherer.wizards.com/Handlers/Image.ashx?size=small&type=card&name=" . $name . $set;
}

function translate_set_abbreviations( $set ) {
	$set = strtoupper( $set );
	$translations = array(
		'LEA' => '1E',
		'LEB' => '2E'
	);
	if ( isset($translations[$set]) ) {
		return $translations[$set];
	} else {
		return $set;
	}
}
