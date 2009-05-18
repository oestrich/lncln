<?
/**
 * footer.php
 * 
 * Should be included in every page after all output is sent.
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 */
global $db;
?>

		</div>
		<div id="footer">
			Powered by <a href="http://lncln.com">lncln <?echo $lncln->display->settings['version'];?></a>
			<br /><? echo $db->get_queries();?>
		</div>
	</div>
<?include_once(ABSPATH . "includes/googleAnalytics.html");?> 
</body>
</html>
