=== Shashin ===
Contributors: toppa
Donate link: http://www.toppa.com/shashin-wordpress-plugin
Tags: Picasa, Highslide, image, images, photo, photos, picture, pictures, gallery, widget, widgets, video
Requires at least: 2.1
Tested up to: 2.7
Stable tag: 2.3.3

Shashin is a powerful WordPress plugin that lets you display Picasa images anywhere in your WordPress site.

== Description ==

Shashin has many features for displaying your Picasa photos in a variety of ways in your Wordpress posts and pages:

* Embed a gallery of your Picasa albums, and all the photos and videos in each album.
* Show your photos and vidoes in Highslide slideshows.
* Pick individual photos or videos to display, in any size supported by Picasa.
* Pick photos and videos from any combination of albums to display in groups of thumbnails.
* Show thumbnails of your newest photos and videos, from one or more albums.
* Display album thumbnails for albums you choose, or all your albums, sorted however you like. Includes links to Google Maps.
* Display any number of random photos and videos, from one or more albums. You can also choose to exclude certain photos or albums from random display.
* Use widgets for all of the above!
* Customize the Shashin and Highslide stylesheets to suit the theme of your site.
* Internationalization: Shashin supports translations into other languages (please contribute a translation if you're bilingual!)
* Schedule daily automatic synchronization of Shashin with your Picasa albums.

Shashin 2.3 is a complete rewrite. It includes more robust error handling, improved security, and these new features:

* Internationalization
* Daily automatic synchronization of Shashin with your Picasa albums
* Greatly simplified use of the [salbumthumbs] tag when you want to show all the photos in an album after its thumbnail is clicked
* Show photos from an album without having to click on an album thumbnail first, using the [salbumphotos] tag
* Not exactly a feature, but <a href="http://www.toppa.com/2009/workaround-for-using-unlisted-picasa-albums-in-shashin/">read this post on how to get unlisted albums into Shashin</a>
* Improved usabilty for the Shashin admin screens
* The [srandom] and [snewest] tags now let you specify multiple albums
* Uninstall option

Please see <a href="http://www.toppa.com/2009/shashin-23-beta-is-here/">this post about the previous Shashin 2.3 beta for a full description of the new features</a>.

== Installation ==

**Installation Instructions**

Download the zip file, unzip it, and copy the "shashin" folder to your plugins directory. Then activate it from your plugin panel (if you are upgrading, deactivate and then reactivate). After successful activation, Shashin will appear in your "Tools" menu and your "Settings" menu.

Go to the "Settings" menu first and take a look at the default options, which for many people will not require any changes. It's particularly important that you provide the correct URL for your Picasa server. For example, if you're in the UK, it would be http://picasaweb.google.co.uk

Now go to the "Tools" menu and follow the directions to add your first album!

Note that Shashin will add two tables to your WordPress database, named wp\_shashin\_album and wp\_shashin\_photo. **I't simportant to include these tables when making backups of your WordPress tables.** The Shashin tags rely on key numbers from these tables that you won't get back if you lose the data in these tables.

== Frequently Asked Questions ==

Please go to <a href="http://www.toppa.com/shashin-wordpress-plugin">the Shashin page for detailed instructions on how to use Shashin</a>.

