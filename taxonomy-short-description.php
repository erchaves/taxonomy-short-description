<?php
/*
Plugin Name: Taxonomy Short Description
Plugin URI: http://wordpress.mfields.org/plugins/taxonomy-short-description/
Description: Shortens the description shown in the administration panels for all categories, tags and custom taxonomies.
Version: 1.2
Author: Michael Fields
Author URI: http://wordpress.mfields.org/
License: GPLv2

Copyright 2011  Michael Fields  michael@mfields.org

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * Create actions for all taxonomies.
 *
 * @return    void
 *
 * @author    Michael Fields
 * @since     2010-05-31
 * @alter     2011-01-09
 */
function taxonomy_short_description_actions() {
	global $wp_taxonomies;
	foreach ( $wp_taxonomies as $taxonomy => $taxonomies ) {
		add_action( 'manage_' . $taxonomy . '_custom_column', 'taxonomy_short_description_rows', 10, 3 );
		add_action( 'manage_edit-' . $taxonomy . '_columns',  'taxonomy_short_description_columns' );
	}
}
add_action( 'admin_init', 'taxonomy_short_description_actions' );


/**
 * Filter the taxonomy tables columns.
 *
 * Remove the default "Description" column.
 * Add a custom "Short Description" column.
 * 
 * @param     array     Unfiltered columns for the taxonomy's edit screen.
 * @return    array     Modified columns for the taxonomy's edit screen.
 *
 * @author    Michael Fields
 * @since     2010-05-31
 * @alter     2011-01-09
 */
function taxonomy_short_description_columns( $c ) {
	$c['short_description'] = $c['description'];
	unset( $c['description'] );
	return $c;
}


/**
 * Display the shortened description in each row's custom column.
 *
 * @param     string    Should be empty.
 * @param     string    Name of the column.
 * @param     string    Term id. Integer represented as string.
 * @return    string    Shortend taxonomy description. Empty if no description
 *
 * @author    Michael Fields
 * @since     2010-05-31
 * @alter     2011-01-09
 */
function taxonomy_short_description_rows( $string, $column_name, $term ) {
	if ( 'short_description' == $column_name ) {
		global $taxonomy;
		$string = term_description( $term, $taxonomy );
		$string = taxonomy_short_description_shorten( $string, apply_filters( 'taxonomy_short_description_length', 40 ) );
	}
	return $string;
}


/**
 * Shorten a string to a given length.
 *
 * @param     string    The string to shorten.
 * @param     int       Number of characters allowed in $string. Defaults value is 23.
 * @param     string    Text to append to the shortened string.
 * @return    string    Shortened string.
 *
 * @author    Michael Fields
 * @author    Thomas Scholz
 * @since     2010-05-31
 * @alter     2011-01-09
 */
function taxonomy_short_description_shorten( $string, $max_length = 23, $append = '&#8230;' ) {
	
	/* Sanitize $string. */
	$string = strip_tags( $string );
	$string = trim( $string );
	$string = html_entity_decode( $string, ENT_QUOTES, 'UTF-8' );
	$string = rtrim( $string, '-' );

	/* Count how many characters are in the string. */
	$length = strlen( utf8_decode( $string ) );

	/* String is longer than max-length. It needs to be shortened. */
	if ( $length > $max_length ) {

		/* Shorten the string to max-length */
		$string = mb_substr( $string, 0, $max_length, 'utf-8' );

		/* Avoid breaks within words - find the last white space */
		$pos = mb_strrpos( $string, ' ', 'utf-8' );

		/* No space? One long word or chinese/korean/japanese text. Shorten the string to the last space. */
		if ( false !== $pos ) {
			$string = mb_substr( $string, 0, $pos, 'utf-8' );
		}

		/* Append shortened string with the value of $append preceeded by a non-breaking space. */
		$string.= "\xC2\xA0" . $append;
	}

	return $string;
}