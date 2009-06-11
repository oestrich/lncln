<?php
/**
 * class.module.php
 * 
 * Class that Modules inherit
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.13.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Class which all modules are descended from
 * @since 0.13.0
 * 
 * @package lncln
 */
class Module{
	/**
	 * @var Database Reference to the Database instance
	 */
	public $db = null;
	
	/**
	 * @var lncln Reference to the lncln instance
	 */
	public $lncln = null;

	/**
	 * Construct to pass the reference of lncln so that modules 
	 * can access permissions and settings
	 * @since 0.13.0
	 * 
	 * @global Database Instance of Database
	 * @global lncln Main lncln instance
	 */
	public function __construct(){
		$this->db = get_db();
		
		$this->lncln = get_lncln();
	}
}
