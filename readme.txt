=== Ninja Galleries ===
Contributors: kstover, jameslaws
Donate link: http://wpninjas.net
Tags: gallery, porfolio, images, galleries, image
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 1.0.18

Ninja Galleries lets you easily create image galleries by tagging your images and then assigning those tags to a gallery page.

== Description ==
- Version 1.0.4 fixes a bug related to themes not having post thumbnails enabled. (See note below about post thumbnails)
- Version 1.0.3 fixes a bug related to the onecolumn-page.php error.
- Version 1.0.2 fixes an activation error that some users were experiencing when activating the plugin.
- Version 1.0.1 fixes the PHP Warning that exists in v. 1.0. Please upgrade as soon as possible.

- Note - In order to use Ninja Galleries, your theme must have post thumbnails enabled. If you do not, you can still install Ninja Galleries, but there will be no functionality.

First off, Ninja Galleries is based upon the excellent Media Tags plugin by Paul Menard. If you are looking to simply tag your images, then Media Tags is the plugin you need.
Because Media Tags 3.0 is the tagging system used by this plugin, we strongly recommend that you do not use Media Tags and Ninja Galleries at the same time. Although we have gone
through great lengths to prevent any possible conflicts, it would be best to use one or the other. 

Ninja Galleries is designed to work with the Lighbox Plus plugin (v. 2.2.2). If you have this plugin installed, then Ninja Galleries will open all clicked images in a Lightbox Popup.
If you do not have this plugin installed, Ninja Announcements will simply link to the image.

Features:
	*Create image galleries featuring as many images as you would like.
	*Once a gallery is created and tags are selected, new tagged images show up automatically.
	*Outputs each gallery as a set of <dl><dt> HTML elements for easy styling.
	*Assign categories to each gallery for easy organization.
	*Each gallery has its own url that you can to link from anywhere on your site.
	*List your galleries by category using a simple shortcode.


Ninja Galleries uses http://www.yoursite.com/gallery/gallery-name as the url for gallery pages. This means that you can't have any custom post types with this url. Unfortunately, this is 
fairly set in stone at this release. Future versions will allow a custom url.
	
There are more screenshots and tutorials for Ninja Galleries at http://plugins.wpninjas.net.
	
== Screenshots ==

1. The add/edit gallery view.
2. Editing a gallery. Underneath the description you can select any number of media tags to pull into this gallery. You can also assign a category and give this gallery a featured image. The featured image will be the gallery thumbnail.
3. Editing a picture/media. At the bottom of the page, you can assign media tags that exist or create new ones.
4. The final product. The images are all inside <dl><dt> tags and styled accordingly.
5. Since we have Lightbox Plus (v. 2.2.2) installed, clicking on an image in the gallery opens this Lightbox Popup.

== Installation ==

Installing Ninja Galleries is very simple.

1. Upload the plugin folder (i.e. ninja_gallery) to the /wp-content/plugins/ directory of your WordPress installation.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Click on the 'Media' button at the Admin Panel to start tagging your images.
4. Click on the 'Ninja Galleries' button at the Admin Panel to create a gallery and select image tags for it.
4. Have a snack. You're done.

== Use ==
There are a few shortcodes included to make linking to your gallery or galleries easier.
	*If you would like to list all the galleries within a given category: [wpnj_gal_list cat="Category1"]
		*You can list multiple categories by putting in a comma-separated list: [wpnj_gal_list cat="Category1,Category2,Category3"]
	*If you want to put in a link to a specific gallery (which will use the featured image as the link): [wpnj_gal_list gallery="Gallery Name Here"]
	*If you want to list all of the galleries: [wpnj_gal_list]
	
== Advanced Styling ==
To get the gallery images to look the way you want, style <dl><dt> and <dd> elements.

== Requested Features ==
	*Ability to create a custom url. Currently, the user is restricted to /gallery/, future versions of Ninja Galleries will correct this.

== Changelog ==

= 1.0.18 =
Testing git to WordPress.org SVN script.

= 1.0.17 =
Testing git to WordPress.org SVN script.

= 1.0.16 =
Testing git to WordPress.org SVN script.

= 1.0.15 =
Testing git to WordPress.org SVN script.

= 1.0.14 =
Testing git to WordPress.org SVN script.

= 1.0.10 =
Testing git to WordPress.org SVN.

= 1.0.4 =
Fixed a bug caused by not having post thumbnails enabled. Please note that you MUST have post thumbnails enabled in your theme in order for Ninja Galleries to work.

= 1.0.3 =
Fixed a bug related to onecolumn-page.php.

= 1.0.2 =
Fixed an activation bug that some users were seeing.

= 1.0.1 =

* Fixed a PHP warning some users were seeing.
= 1.0 =
* First version of Ninja Galleries released.