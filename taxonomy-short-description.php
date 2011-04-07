<?php
/*
Plugin Name:       Taxonomy Short Description
Plugin URI:        http://wordpress.mfields.org/plugins/taxonomy-short-description/
Description:       Shortens the description shown in the administration panels for all categories, tags and custom taxonomies.
Version:           1.3.1
Author:            Michael Fields
Author URI:        http://wordpress.mfields.org/
License:           GPLv2

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
 * Actions.
 *
 * Create actions for all taxonomies that have a UI.
 *
 * @return    void
 *
 * @author    Michael Fields
 * @since     2010-05-31
 * @alter     2011-04-07
 */
function taxonomy_short_description_actions() {
	$taxonomies = get_taxonomies();
	foreach ( $taxonomies as $taxonomy ) {
		$config = get_taxonomy( $taxonomy );
		if ( isset( $config->show_ui ) && true == $config->show_ui ) {
			add_action( 'manage_' . $taxonomy . '_custom_column', 'taxonomy_short_description_rows', 10, 3 );
			add_action( 'manage_edit-' . $taxonomy . '_columns',  'taxonomy_short_description_columns' );
			add_filter( 'manage_edit-' . $taxonomy . '_sortable_columns', 'taxonomy_short_description_columns' );
		}
	}
}
add_action( 'admin_init', 'taxonomy_short_description_actions' );


/**
 * Term Columns.
 *
 * Filter the taxonomy table's columns.
 *
 * Remove the default "Description" column.
 * Add a custom "Short Description" column.
 * 
 * @param     array     Unfiltered columns for the taxonomy's edit screen.
 * @return    array     Modified columns for the taxonomy's edit screen.
 *
 * @author    Michael Fields
 * @since     2010-05-31
 * @alter     2011-02-25
 */
function taxonomy_short_description_columns( $columns ) {
	$position = 0;
	$iterator = 1;
	foreach( $columns as $column => $display_name ) {
		if ( 'name' == $column ) {
			$position = $iterator;
		}
		$iterator++;
	}
	if ( 0 < $position ) {
		/* Store all columns up to and including "Name". */
		$before = $columns;
		array_splice( $before, $position );

		/* All of the other columns are stored in $after. */
		$after  = $columns;
		$after = array_diff ( $columns, $before );

		/* Prepend a custom column for the short description. */
		$after = array_reverse( $after, true );
		$after['mfields_short_description'] = $after['description'];
		$after = array_reverse( $after, true );

		/* Remove the original description column. */
		unset( $after['description'] );

		/* Join all columns back together. */
		$columns = $before + $after;
	}
	return $columns;
}


/**
 * Term Rows.
 *
 * Display the shortened description in each row's custom column.
 *
 * The description will be shortened to 40 characters. If a user
 * finds this length unsatisfactory, a filter has been provided
 * for adjustments. The following code can be added to any theme
 * or plugin to customize the length of term descriptions:
 *
 * <code>
 * <?php
 * function mytheme_taxonomy_short_description_length( $length ) {
 *     return 100;
 * }
 * add_filter( 'mfields_taxonomy_short_description_length', 'mytheme_taxonomy_short_description_length' );
 * ?>
 * </code>
 *
 * @param     string    Should be empty.
 * @param     string    Name of the column.
 * @param     string    Term id. Integer represented as string.
 * @return    string    Shortend taxonomy description. Empty if no description
 *
 * @author    Michael Fields
 * @since     2010-05-31
 * @alter     2011-02-25
 */
function taxonomy_short_description_rows( $string, $column_name, $term ) {
	if ( 'mfields_short_description' == $column_name ) {
		global $taxonomy;
		$string = term_description( $term, $taxonomy );
		$string = taxonomy_short_description_shorten( $string, apply_filters( 'mfields_taxonomy_short_description_length', 40 ) );
	}
	return $string;
}


/**
 * Shorten.
 *
 * Shorten a string to a given length.
 *
 * @param     string    The string to shorten.
 * @param     int       Number of characters allowed in $string. Default value is 23.
 * @param     string    Text to append to the shortened string.
 * @return    string    Shortened string.
 *
 * @author    Michael Fields
 * @author    Thomas Scholz
 * @since     2010-05-31
 * @alter     2011-03-01
 */
function taxonomy_short_description_shorten( $string, $max_length = 23, $append = '&#8230;', $encoding = 'utf8' ) {

	/* Sanitize $string. */
	$string = strip_tags( $string );
	$string = trim( $string );
	$string = html_entity_decode( $string, ENT_QUOTES, 'UTF-8' );
	$string = rtrim( $string, '-' );

	/* Sanitize $max_length */
	if ( 0 == abs( (int) $max_length ) ) {
		$max_length = 23;
	}

	/* Return early if the php "mbstring" extension is not installed. */
	if ( ! function_exists( 'mb_substr' ) ) {
		$length = strlen( $string );
		if ( $length > $max_length ) {
			return substr_replace( $string, $append, $max_length );
		}
		return $string;
	}

	/* Count how many characters are in the string. */
	$length = strlen( utf8_decode( $string ) );

	/* String is longer than max-length. It needs to be shortened. */
	if ( $length > $max_length ) {

		/* Shorten the string to max-length */
		$short = mb_substr( $string, 0, $max_length, $encoding );

		/*
		 * A word has been cut in half during shortening.
		 * If the shortened string contains more than one word
		 * the last word in the string will be removed.
		 */
		if ( 0 !== mb_strpos( $string, $short . ' ', 0, $encoding ) ) {
			$pos = mb_strrpos( $short, ' ', $encoding );
			if ( false !== $pos ) {
				$short = mb_substr( $short, 0, $pos, $encoding );
			}
		}

		/* Append shortened string with the value of $append preceeded by a non-breaking space. */
		$string = $short . ' ' . $append;
	}

	return $string;
}