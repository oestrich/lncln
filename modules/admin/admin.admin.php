<?php
/**
 * admin.php
 * 
 * Admin panel
 * 
 * @copyright (C) 2009 Eric Oestrich
 * @version 0.14.0 $Id$
 * @license license.txt GNU General Public License version 3
 * 
 * @package lncln
 */

/**
 * Admin's Admin class
 * @since 0.13.0
 * 
 * @package lncln
 */
class AdminAdmin extends Admin{	
	/**
	 * Sets things that the parent class has
	 * @since 0.13.0
	 */
	public function set_vars(){
		$this->actions = $this->lncln->modules['admin']->actions;
		$this->info = $this->lncln->modules['admin']->info;
		$this->modules = $this->lncln->modules['admin']->modules;
	}
	
	/**
	 * Registers actions that will be used in the admin panel
	 * @since 0.13.0
	 * 
	 * @return array Keys: url 
	 */
	public function actions(){
		$actions = array(
			'urls' => array(
				'Main' => array(
					'info' => 'Currently installed modules',
					'manage' => 'Manage modules',
					),
				),
			'quick' => array(
				'info', 'manage',
				),
			);
		
		return $actions;
	}

	/**
	 * Prints out the info for modules
	 * @since 0.13.0
	 */
	public function info(){
		$this->set_vars();
		
		foreach($this->info as $info){
			$requires = join(" ", $info['requires']);
			
			if($requires == ""){
				$requires = "No requirements";
			}
			
			echo "<span class='admin_link'>";
			if($this->check_module($info['name'])){
				echo "<a href='" . URL . "admin/" . $info['name']. "/'>" . $info['name'] . "</a>";
			}
			else{
				echo $info['name'];
			}
			echo "</span>\n";
			echo "<p class='admin_description'>" . $info['description'] . "</p>\n";
			echo "<table>\n";
			echo "<tr><td>Version:</td><td>" . $info['version'] . "</td></tr>\n";
			echo "<tr><td>Package:</td><td>" . $info['package'] . "</td></tr>\n";
			echo "<tr><td>Requires:</td><td>" . $requires . "</td></tr>\n";
			echo "</table>\n";
			echo "<br />";
		}
	}
	
	/**
	 * Enable/disable modules
	 * @since 0.14.0
	 * 
	 * @todo Seperate the sections in "------"
	 */
	public function manage(){
		$query = array(
			'type' => 'SELECT',
			'fields' => array('*'),
			'table' => 'modules',
			'order' => array('ASC', array('package', 'name')),
			);
		
		$this->db->query($query);
		
		$modules = array();
		
		$modules['scanned'] = $this->scan_modules_dir();

		foreach($this->db->fetch_all() as $module){
			$modules['db'][$module['name']] = $module;
			
			$modules['package'][$module['package']][] = $module['name'];
		}
		
		// -------------------------------------------		
		
		// Searching for modules not in the database and adding them to the list
		foreach($modules['scanned'] as $module){
			if(array_key_exists($module['package'], $modules['package'])){
				if(in_array($module['name'], $modules['package'][$module['package']]) == false){
					$modules['package'][$module['package']][] = $module['name'];
					$modules['db'][$module['name']] = $module;
				}
				else{
					if($modules['db'][$module['name']]['version'] != $module['version']){
						echo "<div class='admin_warning'>";
						echo "Bad version number on " . $module['name'] . ". ";
						echo "lncln thinks it's " . $modules['db'][$module['name']]['version'];
						echo ", but is actually " . $module['version'] . ". ";
						echo "Please replace " . $module['name'] . " with version ";
						echo $modules['db'][$module['name']]['version'] . ".";
						echo "</div>";
					}
				}
			}
			else{
				$this->add_module(strtolower($module['class']));
				
				$modules['package'][$module['package']][] = $module['name'];
				$modules['db'][$module['name']] = $module;
			}
		}
		
		// -------------------------------------------
		
		$scanned_keys = array_keys($modules['scanned']);
		$db_keys = array_keys($modules['db']);
		
		foreach(array_diff($db_keys, $scanned_keys) as $module){
			$package = $modules['db'][$module]['package'];
			$key = array_search($module, $modules['package'][$package]);
						
			$this->remove_module($module);
			unset($modules['db'][$module]);
			unset($modules['package'][$package][$key]);
		}
		
		// -------------------------------------------
				
		// Free up some memory
		unset($modules['scanned']);
		// Sort packages alphabetically
		ksort($modules['package']);
		
		echo "<form style='width: 714px;'>\n";
		echo "\t<div>\n";

		foreach($modules['package'] as $name => $package){
			if(count($package) == 0)
				continue;
			
			echo "\t\t<fieldset>\n";
			echo "\t\t\t<legend>" . ucwords($name) . ":</legend>\n";
			echo "\t\t\t<table style='width: 714px;'>\n";
			echo "\t\t\t\t<tr>\n";
          	echo "\t\t\t\t\t<td class='column'>Enable</td>\n";
          	echo "\t\t\t\t\t<td class='column'>Module</td>\n";
          	echo "\t\t\t\t\t<td class='column'>Version</td>\n";
          	echo "\t\t\t\t\t<td class='column description'>Description</td>\n";
          	echo "\t\t\t\t\t<td class='column'>Requirements</td>\n";
        	echo "\t\t\t\t</tr>\n";
        	echo "\t\t\t\t<tr>\n";
          	echo "\t\t\t\t\t<td colspan='6'><hr /></td>\n";
        	echo "\t\t\t\t</tr>\n";
			foreach($package as $module){
				if($name == "Core"){
					$modules['db'][$module]['disabled'] = true;
				}
				
				$modules['db'][$module]['requires'] = unserialize($modules['db'][$module]['requires']);
				
				foreach($modules['db'][$module]['requires'] as $required){
					$this->disabled_modules($modules['db'], $required);
				}
				
				if(count($modules['db'][$module]['requires']) == 0){
					$modules['db'][$module]['requires'] = "None";
				}
				else{
					$modules['db'][$module]['requires'] = join($modules['db'][$module]['requires'], ", ");
				}
				
				$checkbox = $modules['db'][$module]['enabled'] == true ? "checked " : "";
				$checkbox .= $modules['db'][$module]['disabled'] == true ? "disabled" : "";
				
				echo "\t\t\t\t<tr>\n";
				echo "\t\t\t\t\t<td class='column'><input type='checkbox' " . $checkbox . "/></td>\n";
				echo "\t\t\t\t\t<td class='column'>" . $modules['db'][$module]['name'] . "</td>\n";
				echo "\t\t\t\t\t<td class='column'>" . $modules['db'][$module]['version'] . "</td>\n";
				echo "\t\t\t\t\t<td class='column'>" . $modules['db'][$module]['description'] . "</td>\n";
				echo "\t\t\t\t\t<td class='column'>" . $modules['db'][$module]['requires'] . "</td>\n";
				
				echo "\t\t\t\t</tr>\n";
			}
			echo "\t\t\t</table>\n";
			echo "\t\t</fieldset>\n";
		}
		echo "\t\t<br />\n";
		echo "\t\t<input type='submit' value='Enable Modules' />\n";
		echo "\t</div>\n";
		echo "</form>";
	}
	
	/**
	 * Add a module to the database
	 * @since 0.14.0
	 * 
	 * @param string $name Module's name/folder
	 */
	protected function add_module($name){
		$module = $name . "_info";
		$module = $module();
		
		$query = array(
			'type' => 'INSERT',
			'table' => 'modules',
			'fields' => array(
				'name',
				'description',
				'class',
				'folder',
				'enabled',
				'package',
				'version',
				'lncln_version',
				'requires',
				),
			'values' => array(
				array(
					$module['name'],
					$module['description'],
					$module['class'],
					$name,
					0,
					$module['package'],
					$module['version'],
					$module['lncln_version'],
					serialize($module['requires']),
					),
				),
			);
		
		$this->db->query($query);
	}
	
	/**
	 * Disable changing modules that are required
	 * @since 0.14.0
	 * 
	 * @param array &$packages List of modules
	 */
	protected function disabled_modules(&$packages, $name){
		foreach($packages as &$module){
			if($module['name'] == $name && $module['disabled'] == false){
				$module['disabled'] = true;
			}
			unset($module);
		}
	}
	
	/**
	 * Remove a module from the database
	 * @since 0.14.0
	 * 
	 * @param string $name Module name
	 */
	protected function remove_module($name){
		$query = array(
			'type' => 'DELETE',
			'table' => 'modules',
			'where' => array(
				'AND' => array(
					array(
						'field' => 'name',
						'compare' => '=',
						'value' => ucwords($name),
						),
					array(
						'field' => 'enabled',
						'compare' => '=',
						'value' => 0,
						),
					),
				),
			'limit' => array(1),
			);
		
		$this->db->query($query);
	}
	
	/**
	 * Scan the modules directory for new modules
	 * @since 0.14.0
	 * 
	 * @return array Information on all scanned in modules
	 */
	protected function scan_modules_dir(){
		$scanned = scandir(ABSPATH . "modules");
		
		array_shift($scanned);
		array_shift($scanned);
		
		foreach($scanned as $module){
			if(file_exists(ABSPATH . "modules/" . $module . "/" . $module . ".info.php")){
				// Include new modules' info file
				include_once ABSPATH . "modules/" . $module . "/" . $module . ".info.php";
			
				$module = $module . "_info";
				$module = $module();
				
				$modules[$module['name']] = $module;
			}
		}
		
		return $modules;
	}
}
