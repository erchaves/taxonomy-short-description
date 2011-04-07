Taxonomy Short Description
==========================

A [WordPress](http://wordpress.org/) plugin that shortens the term description shown in the administration panels for all categories, tags and custom taxonomies. This plugin does not effect public views of term descriptions at all.

Changelog
---------

__1.3.1__

* Fix readme inconsistancies.

__1.3__

* Allow terms to be sorted by description. Props [Scribu](http://scribu.net/).

__1.2__

* Only create hooks for taxonomies that have a UI.
* Responsible handling of the custom column.
* Provide a fallback in case "mbstring" php extension is not installed.

__1.1__

* Added support for multibyte strings in taxonomy_short_description_shorten().
* Better use of typography in taxonomy_short_description_shorten().
* Added [Thomas Scholz](http://toscho.de/ueber-mich/) aka [toscho](http://wordpress.org/support/profile/toscho) as a contributer.
* Fixed bug in taxonomy_short_description_rows() - moved filter into conditional.

__1.0__

* Original Release - Works With: wp 3.0 and 3.1RC2.