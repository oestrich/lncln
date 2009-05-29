<?php
/**
 * upload.class.php
 * 
 * Main file for the Upload module
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

class Upload{
	/**
	 * @var string Name of module, Used in forms
	 */
	public $name = "Upload";
	
	/**
	 * @var string Display name of module
	 */
	public $displayName = "Upload";
	
	/**
	 * @var Database Reference to the Database instance
	 */
	public $db = null;
	
	/**
	 * @var lncln Reference to the lncln instance
	 */
	public $lncln = null;
	
	/**
	 * Sets db and lncln class variables
	 * @since 0.13.0
	 */
	public function __construct(){
		$this->db = get_db();
		
		$this->lncln = get_lncln();
	}
}