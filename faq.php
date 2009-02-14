<?
/**
 * faq.php
 *  
 * @author Eric Oestrich
 * @version 0.6.0
 * 
 * @package lncln
 */

require_once('config.php');
require_once('functions.php');

connect();

require_once('header.php');

?>
	1. Where did my images go?
		If you're logged in and they didn't upload, chances are you exceeded the maximum upload for the day and they're now waiting in the queue for an administrator to moderate them.  If you are not logged in
		then they are waiting in the queue for admin to moderate them.
		
		There is also a chance you messed something up during upload.  If you forgot to add a tag, for example, it will not upload it.
		
	2. Do I need a log in?
		No, you can upload images perfectly fine with out one.  User accounts are rarely given out; for the sole reason that it posts directly to the front page.
		
	3. Why do I have to tag things?
		Tagging things makes it easier to search for later, so please tag it correctly.
<?

require_once('footer.php');

?>