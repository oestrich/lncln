<?
/**
 * module.php
 * 
 * Contains the interface for modules
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.12.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Module interface
 * 
 * @since 0.13.0
 * @package lncln
 */
interface Module{
	/**
	 * Used for printing out in forms
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public $name;
	
	/**
	 * Called after a successful upload
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @param $id int ID of new image
	 * @param $data array Extra material needed, tag information, etc
	 */
	public function add($id, $data);
	
	/**
	 * Edits an image with the data provided
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @param $id int ID of image
	 * @param $data array Extra material needed, tag information, etc
	 */	
	public function edit($id, $data);
	
	/**
	 * Called during the upload screen. Contains the form information needed,
	 * will be passed to add() after successful upload
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function upload();
	
	/**
	 * Creates the form information needed during moderation
	 * 
	 * @since 0.13.0
	 * @package lncln
	 * 
	 * @param $id int Image to gather information about and populate the input
	 */
	public function moderate($id);
	
	/**
	 * Creates the link in the header
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function headerLink();
	
	/**
	 * Creates the icon underneath images
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function icon();
	
	/**
	 * Creates text underneath the image.  May contain a form
	 * 
	 * @since 0.13.0
	 * @package lncln
	 */
	public function underImage();	
}
?>
