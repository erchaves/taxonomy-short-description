Taxonomy Short Description
==========================

A [WordPress]: http://wordpress.org/ plugin that shortens the term description shown in the administration panels for all categories, tags and custom taxonomies. This plugin does not effect public views of term descriptions at all.

Known Issues
------------
* You will not be able to sort by description in WordPress 3.1. [#13650]: http://core.trac.wordpress.org/ticket/13650

Changelog
---------

=== 1.2
* Only create hooks for taxonomies that have a UI.
* Responsible handling of the custom column.
* Provide a fallback in case "mbstring" php extension is not installed.

=== 1.1
* Added support for multibyte strings in taxonomy_short_description_shorten().
* Better use of typography in taxonomy_short_description_shorten().
* Added [Thomas Scholz]: http://toscho.de/ueber-mich/ aka [toscho]: http://wordpress.org/support/profile/toscho as a contributer.
* Fixed bug in taxonomy_short_description_rows() - moved filter into conditional.

=== 1.0
* Original Release - Works With: wp 3.0 and 3.1RC2.
