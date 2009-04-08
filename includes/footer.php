<?
/**
 * footer.php
 * 
 * Should be included in every page after all output is sent.
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.10.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */
?>

		</div>
		<div id="footer">
			<?if($lncln->isAdmin):?>
				<div style="text-align: center; left: -50px; position: relative;"><a href="admin/moderate.php">Moderate Images</a></div>
			<?endif;?>		
			If you find something that belongs to you, and would like it taken down, please contact <a href="mailto:mazra[at]boomboxlincoln[dot]org">mazra</a><br />
			Designed for Firefox - Powered by <a href="http://lncln.com">lncln <?echo VERSION;?></a>
		</div>
	</div>
<?require_once("googleAnalytics.html");?> 
</body>
</html>
