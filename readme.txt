=== Shashin ===
Contributors: toppa
Donate link: http://www.toppa.com/shashin-wordpress-plugin
Tags: images, photos, Picasa
Requires at least: 2.0.2
Tested up to: 2.2.2
Stable tag: 1.0.3

Shashin is a powerful WordPress plugin that lets you display Picasa images anywhere in your WordPress site.

== Description ==

Use Shashin to display individual Picasa images in your posts and pages, as well as tables of thumbnails, random images, your most recently uploaded pictures, and album thumbnails. You can do all this with Shashin's custom tags, which are documented in the FAQ section. Shashin stores data for your albums in local tables, which you can sync with Picasa's RSS feeds on demand. Shashin does not download your Picasa images - instead it displays them directly from Picasa. Shashin includes some nice extra features too, such as the ability to exclude specific albums or images from random displays, and a link to Google Maps for albums where you've specified a location in Picasa. There's also a stylesheet, which you are welcome to customize for your site. You can use Shashin in your sidebar as well - see the FAQ section for instructions. Note that Shashin currently supports only public Picasa albums (I hope to add support for private albums soon).

== Installation ==

Download the zip file, extract it into your plugin directory, and then activate it from your plugin panel. After successful activation, Shashin will appear under your "Manage" tab and under your "Options" tab.

Go to the "Options" tab first and take a look at the default options, which for many people will not require any changes. If your Picasa server is outside the US, then change the server (e.g. to picasaweb.google.co.uk). As explained on the screen, you only need to adjust the other options if you make certain changes to shashin.css.

Now go to the "Manage" tab and follow the directions to add your first album!

Note that Shashin will add two tables to your WordPress database, named wp\_shashin\_album and wp\_shashin\_photo. You should include these tables when making backups of your WordPress tables.

**Special Note to Upgraders:** 1. Deactivate your old installation, upload the new version, and then reactivate. This is necessary to set values for new options. 2. The srandom and sthumbs tags are not backwards compatible. You will need to change your markup anywhere you're currently using these tags (I won't do this again, now that Shashin is done with its initial beta testing). See the FAQ section for the new markup for these tags.

== Frequently Asked Questions ==

= I added my Picasa album to Shashin, and then after that I made a bunch of changes to the album. I changed some captions, added some new pictures, and deleted some pictures. How do I let Shashin know about the changes? =

In the Shashin admin panel, you'll want to click the icon to sync an album whenever you update it in Picasa. This will synchronize your Shashin tables with the Picasa RSS feed. If you delete a photo from Picasa, it will get deleted from the Shashin tables as well when you sychronize the album. 

I've noticed that the Picasa RSS feed seems to not always update immediately. If you synchronize your album right after making changes to it, and Shashin doesn't reflect the changes, wait a few minutes and try again. (Shashin doesn't cache the RSS feed, so that's not the reason).

= What image sizes can I use? =

Picasa supports only a specific set of image sizes. If you try to use a size not listed, your image will not display at all. The sizes are:

32, 48, 64, 72, 144, 160, 200, 288, 320, 400, 512, 576, 640, 720, 800

Note that these sizes represent a "maximum dimension." This means if you pick 640, and your picture has a landscape orientation, then it will be 640 pixels wide. If it has a portrait orientation, then it will be 640 tall. Shashin automatically calculates the correct size for the other dimension.

Note that 32, 48, 64, and 160 are special sizes. Picasa will crop them to a square shape. This makes them good sizes for displaying tables of thumbnails.

= What general advise do you have for using Shashin tags? =

There are a number of options to remember in Shashin tags. The easiest thing to do is to keep two windows or tabs open in your browser - one for writing your post and one for the Shashin admin page. The admin pages have markup for Shashin tags that you can copy and paste into your post. After pasting them you can then edit the options as needed.

A Shashin tag will be translated into an xhtml "div" container.  Always use a Shashin tag on a line by itself in your post or page. This is important because of how WordPress auto-formats your posts. If you put a Shashin tag on the same line as other text, then it will get wrapped in the paragraph tag that WordPress adds to the line. That is not valid xhtml (although in most cases browsers will still display it correctly).

All the Shashin tags have options that let you set a css "float" value and a "clear" value. Both are optional. If you set a float value, there's usually no need to set a clear value (e.g. floating left or right and then clearing margins typically defeats the point of the float). Remember that the purpose of a float is to let the floated container flow around other containers. If you float an image near the end of your post, it may flow down below the post. To avoid this, put a &lt;br clear="all" /&gt; tag at the very end of your post.

= How do I display a single image? =

In a post or page, use the simage tag: <code>[simage=photo_key,max_size,caption_yn,float,clear]</code>  
Example: <code>[simage=268,200,n,left]</code>

In your sidebar, use this code and substitute the desired values - note the quote marks are important:  
<pre><code>&lt;?php
$photo = new ShashinPhoto();
echo $photo-&gt;getPhotoMarkup(array(null,photo_key,max_size,'caption_yn','float','clear'));
?&gt;</code></pre>

* photo\_key: (required) the Photo Key listed for the image in its Shashin admin page
* max\_size: (required) the size you want, chosen from the list above
* caption\_yn: (optional, defaults to n) use y or n to indicate whether you want the caption to appear under the image
* float: (optional) a CSS float value
* clear: (optional) a CSS clear value

= How do I display an album thumbnail? =

With album thumbnails, the size is fixed by Picasa at 160x160, so adjusting the size is not an option. Shashin automatically displays the album title below the thumbnail. If you choose to display the location, it will appear under the title, and a linked Google Maps icon will appear next to the title as well.

In a post or page, use the salbum tag: <code>[salbum=album_key,location_yn,pubdate_yn,float,clear]</code>  
Example: <code>[salbum=2,y,n,left]</code>

In your sidebar, use this code and substitute the desired values - note the quote marks are important:  
<pre><code>&lt;?php
$album = new ShashinAlbum;
echo $album-&gt;getAlbumMarkup(array(null,album_key,'location_yn','pubdate_yn','float','clear'));
?&gt;</code></pre>

* album\_key: (required) the Album Key listed for the album on the Shashin admin page
* location\_yn: (optional, defaults to n) use y or n to indicate whether you want the location to appear under the title. A Google Maps icon will also be added
* pubdate\_yn: (optional, defaults to n) use y or n to indicate whether you want the album's pubdate to appear under the title
* float: (optional) a CSS float value
* clear: (optional) a CSS clear value

= How do I display a table of thumbnails or images? =

Shashin can generate a table of thumbnails, containing the images you specify. You can also control how many columns the table will have, and whether or not to display captions. Note that this tag is typically used for thumbnails, but since you can specify the image size, you can use it to display larger images as well.

In a post or page, use the sthumbs tag: <code>[sthumbs=photo_key1|photo_key2|etc,max_size,max_cols,caption_yn,float,clear]</code>  
Example: <code>[sthumbs=5|202|115|84|33|189,160,3,n,none,both]</code>

In your sidebar, use this code and substitute the desired values - note the quote marks are important:  
<pre><code>&lt;?php echo ShashinPhoto::getThumbsMarkup(array(null,'photo_key1|photo_key2|etc',max_size,max_cols,caption_yn,'float','clear')); ?&gt;</code></pre>

* photo\_key1|photo\_key2|etc: (required) as many photo keys as you want, separated by the | character
* max\_size: (required) the image size you want, chosen from the list above
* max\_cols: (required) how many columns the table should have
* caption\_yn: (optional, defaults to n) use y or n to indicate whether you want captions to appear under the images
* float: (optional) a CSS float value
* clear: (optional) a CSS clear value

= How do I display random images? =

You can display a table of random images. You can specify how many images to include in the table, and how many columns the table will have. You can indicate whether the random images should come from a specific album, or from any album. If you want to display a single random image, simply indicate 1 image and 1 column for the table. Note that, if in the admin pages you change an album's "Include in Random" flag to "No," then its photos will not appear in random images displays. The same is true for individual images where you set the flag to "No."

In a post or page, use the srandom tag: <code>[srandom=album_key,max_size,max_cols,how_many,caption_yn,float,clear]</code>  
Example: <code>[srandom=any,288,2,6,n,none,both]</code>

In your sidebar, use this code and substitute the desired values - note the quote marks are important:  
<pre><code>&lt;?php echo $photo::getRandomMarkup(array(null,album_key,max_size,max_cols,how_many,'caption_yn','float','clear')); ?&gt;</code></pre>

* album\_key: (required) either the word "any" or the the Album Key listed for an album on the Shashin admin page
* max\_size: (required) the size you want, chosen from the list above
* max\_cols: (required) how many columns the table should have
* how\_many: (required) how many random images to display in the table
* caption\_yn: (optional, defaults to n) use y or n to indicate whether you want captions to appear under the images
* float: (optional) a CSS float value
* clear: (optional) a CSS clear value

= How do I display my most recently uploaded pictures? =

You can display a table of your most recently uploaded pictures. You can specify how many images to include in the table, and how many columns the table will have. You can indicate whether the images should come from a specific album, or from any album. If you want to display only your newest image, simply indicate 1 image and 1 column for the table.

In a post or page, use the snewest tag: <code>[snewest=album_key,max_size,max_cols,how_many,caption_yn,float,clear]</code>  
Example: <code>[snewest=any,288,2,6,n,none,both]</code>

In your sidebar, use this code and substitute the desired values - note the quote marks are important:  
<pre><code>&lt;?php echo $photo-&gt;getNewestMarkup(array(null,album_key,max_size,max_cols,how_many,'caption_yn','float','clear')); ?&gt;</code></pre>

* album\_key: (required) either the word "any" or the the Album Key listed for an album on the Shashin admin page
* max\_size: (required) the size you want, chosen from the list above
* max\_cols: (required) how many columns the table should have
* how\_many: (required) how many random images to display in the table
* caption\_yn: (optional, defaults to n) use y or n to indicate whether you want captions to appear under the images
* float: (optional) a CSS float value
* clear: (optional) a CSS clear value

= Why do the pictures always link to Picasa? =

Shashin displays your Picasa pictures directly from the Picasa servers. It does not store them on your web site. Shashin automatically links your photos to their regular size versions at Picasa. I'm not entirely sure of the legal requirements, but my guess is that it would be a violation of the Picasa user agreement to remove these links (as Google probably would not appreciate you using Picasa as an unacknowledged hard drive).

= How can I change the style that's applied to the pictures? =

Under your plugin directory, in Shashin/display/shashin.css you can edit the CSS for how Shashin styles its images. The commenting in that file explains which classes are applied where. **Important Note:** if you change the padding for ".shashin\_image img" or ".shashin\_thumb img" you will need to go to the Options menu for Shashin and adjust the "Image div padding" and "Thumbnail div padding."

= Why does Shashin use its own keys for photos and albums, instead of just using the IDs assigned by Picasa? =

Mainly just for the sake of practicality. Picasa IDs are now up to 20 digits long, which makes them difficult to deal with when writing a Shashin tag.

= I'm a programmer and I want to tweak your code - do you have documentation? =

I've <a href="http://www.toppa.com/shashin_phpdoc">thoroughly documented the Shashin code in PHPDoc</a>. Shashin is released under GPL, so feel free to extend it. <a href="http://www.toppa.com/contact">I'd like to hear about any features you add</a>.

= What does "Shashin" mean? =

I started working on this plugin while living in Tokyo. Shashin is the Japanese word for photograph, so it seemed fitting.
