<?php
/**
*
* @package migration
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License v2
*
*/

class phpbb_db_migration_data_310_pluploadv extends phpbb_db_migration
{
	public function effectively_installed()
	{
		return isset($this->config['plupload_last_gc']) &&
			isset($this->config['plupload_salt']);
	}

	static public function depends_on()
	{
		return array('phpbb_db_migration_data_310_dev');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('plupload_last_gc', 0)),
			array('config.add', array('plupload_salt', unique_id())),
		);
	}
}
