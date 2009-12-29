=== Shashin ===
Contributors: toppa
Donate link: http://www.toppa.com/shashin-wordpress-plugin
Tags: Picasa, Highslide, image, images, photo, photos, picture, pictures, gallery, widget, widgets, video
Requires at least: 2.1
Tested up to: 2.9
Stable tag: 2.5

Shashin is a powerful WordPress plugin that lets you display Picasa photos, albums, and videos anywhere in your WordPress site.

== Description ==

**Overview**

Shashin has many features for displaying your Picasa photos and videos in your Wordpress posts and pages:

* Show a gallery of your Picasa albums, and all the photos and videos in each album.
* Show your photos and vidoes with your favorite image viewer. Highslide is included with Shashin, but you ay also use a different viewer of your choice.
* Pick individual photos or videos to display, in any size supported by Picasa, including captions and EXIF data.
* Pick photos and videos from any combination of albums to display in groups of thumbnails.
* Show thumbnails of your newest photos and videos, from one or more albums.
* Display album thumbnails for albums you choose, or all your albums, sorted however you like. Includes links to Google Maps.
* Display any number of random photos and videos, from one or more albums. You can also choose to exclude certain photos or albums from random display.
* Use widgets for all of the above!
* Customize the Shashin and Highslide stylesheets to suit the theme of your site.
* Internationalization: Shashin supports translations into other languages (please contribute a translation if you're bilingual!)
* Schedule daily automatic synchronization of Shashin with your Picasa albums.

**New in Shashin 2.5**

* jQuery based WYSIWYG browser for adding Shashin photos to your posts. You can now browse albums from within the editor, and click on photos to add them to your post (it will write simage and sthumbs tags for you). To use it, click the Picasa icon in the editor's media button bar. This feature was contributed by [Sune Pedersen](http://kibs.dk/)
* Option to automatically sync albums several times per day, now that Picasa video URLs expire every 11 hours
* Bug fix: album title links in the album thumbnails sidebar widget now point to the correct URL
* Bug fix: "next" and "previous" links for album photos display now work in Google Chrome and Safari (actually a workaround for a webkit bug)

**Shashin 3.0 is under development!**

Shashin 3.0 will include support for Flickr and Twitpic. It will also come with two built in viewers to choose from: jQuery Lightbox 2 and Highslide. The development work will take a while so please be patient.

== Installation ==

**Upgrade Instructions**

1. Click the "upgrade automatically" link on your plugin menu, or: download the Shashin zip file, unzip it, and upload to your plugins directory, then deactivate and reactivate Shashin in your plugin panel.
1. If you have any albums with videos, go to the Shashin Settings menu and select "yes" for "Sync all albums every 10 hours." Starting in early December, all Picasa video URLs now automatically expire after 11 hours. Shashin needs to regularly retrieve fresh URLs so that Picasa videos embedded in your site will always work.

**First-Time Installation Instructions**

1. Download the zip file, unzip it, and upload the "shashin" folder to your plugins directory. Then activate it from your plugin panel. After successful activation, Shashin will appear in your "Tools" menu and your "Settings" menu.
1. Go to the Shashin Settings menu and adjust the options as you like. It's important that you provide the correct URL for your Picasa server. For example, if you're in the UK, it would be http://picasaweb.google.co.uk
1. Also on the Settings menu, if you have any albums with videos, select "yes" for "Sync all albums every 10 hours." All Picasa video URLs automatically expire after 11 hours. Shashin needs to regularly retrieve fresh URLs so that Picasa videos embedded in your site will always work.
1. If you use an image viewer other than the version of Highslide included with Shashin, you need to set it up and configure it yourself. The Shashin settings menu provides several options for image and link IDs, classes, titles, etc. in order to support a variety of image viewers.
1. Go to the Shashin Tools menu and follow the directions to add your first album!
1. Note that Shashin will add two tables to your WordPress database, named wp\_shashin\_album and wp\_shashin\_photo. **It's important to include these tables when making backups of your WordPress tables.** The Shashin tags rely on key numbers from these tables that you won't get back if you lose the data in these tables.

== Frequently Asked Questions ==

Please go to [the Shashin page on my site](http://www.toppa.com/shashin-wordpress-plugin) for a Usage Guide and other information.

For troubleshooting help, please [post a comment in my latest Shashin post](http://www.toppa.com/category/wordpress-and-web-programming/shashin/).

== Changelog ==

= 2.5 =
* jQuery based WYSIWYG browser for adding Shashin photos to your posts
* Option to automatically sync albums several times per day, now that Picasa video URLs expire every 11 hours
* Bug fix: album title links in the album thumbnails sidebar widget now point to the correct URL
* Bug fix: "next" and "previous" links for album photos display now work in Google Chrome and Safari (actually a workaround for a webkit bug)
