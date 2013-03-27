<?php
/**
*
* @package testing
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_dbal_sql_insert_buffer_test extends phpbb_database_test_case
{
	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__).'/fixtures/config.xml');
	}

	protected function assert_config_count($db, $num_configs)
	{
		$sql = 'SELECT COUNT(*) AS num_configs
			FROM phpbb_config';
		$result = $db->sql_query($sql);
		$this->assertEquals($num_configs, $db->sql_fetchfield('num_configs'));
		$db->sql_freeresult($result);
	}

	public function test_multi_insert_disabled_insert_and_flush()
	{
		$db = $this->new_dbal();
		$db->multi_insert = false;

		$buffer = new phpbb_db_sql_insert_buffer($db, 'phpbb_config', 2);

		$this->assert_config_count($db, 2);

		// This call can be buffered
		$this->assertTrue($buffer->insert(array(
			'config_name'	=> 'name1',
			'config_value'	=> 'value1',
			'is_dynamic'	=> '0',
		)));

		$this->assert_config_count($db, 3);

		// Manually flush
		$this->assertFalse($buffer->flush());

		$this->assert_config_count($db, 3);
	}

	public function test_multi_insert_enabled_insert_and_flush()
	{
		$db = $this->new_dbal();

		if (!$db->multi_insert)
		{
			$this->markTestSkipped('Database does not support multi_insert');
		}

		$buffer = new phpbb_db_sql_insert_buffer($db, 'phpbb_config', 2);

		$this->assert_config_count($db, 2);

		// This call can be buffered
		$this->assertFalse($buffer->insert(array(
			'config_name'	=> 'name1',
			'config_value'	=> 'value1',
			'is_dynamic'	=> '0',
		)));

		$this->assert_config_count($db, 2);

		// Manually flush
		$this->assertTrue($buffer->flush());

		$this->assert_config_count($db, 3);
	}

	public function test_multi_insert_disabled_insert_with_flush()
	{
		$db = $this->new_dbal();
		$db->multi_insert = false;

		$buffer = new phpbb_db_sql_insert_buffer($db, 'phpbb_config', 2);

		$this->assert_config_count($db, 2);

		$this->assertTrue($buffer->insert(array(
			'config_name'	=> 'name1',
			'config_value'	=> 'value1',
			'is_dynamic'	=> '0',
		)));

		// This call flushes the values
		$this->assertTrue($buffer->insert(array(
			'config_name'	=> 'name2',
			'config_value'	=> 'value2',
			'is_dynamic'	=> '0',
		)));

		$this->assert_config_count($db, 4);
	}

	public function test_multi_insert_enabled_insert_with_flush()
	{
		$db = $this->new_dbal();

		if (!$db->multi_insert)
		{
			$this->markTestSkipped('Database does not support multi_insert');
		}

		$buffer = new phpbb_db_sql_insert_buffer($db, 'phpbb_config', 2);

		$this->assert_config_count($db, 2);

		$this->assertFalse($buffer->insert(array(
			'config_name'	=> 'name1',
			'config_value'	=> 'value1',
			'is_dynamic'	=> '0',
		)));

		// This call flushes the values
		$this->assertTrue($buffer->insert(array(
			'config_name'	=> 'name2',
			'config_value'	=> 'value2',
			'is_dynamic'	=> '0',
		)));

		$this->assert_config_count($db, 4);
	}

	public function test_multi_insert_disabled_insert_all_and_flush()
	{
		$db = $this->new_dbal();
		$db->multi_insert = false;

		$buffer = new phpbb_db_sql_insert_buffer($db, 'phpbb_config', 2);

		$this->assert_config_count($db, 2);

		$this->assertTrue($buffer->insert_all(array(
			array(
				'config_name'	=> 'name1',
				'config_value'	=> 'value1',
				'is_dynamic'	=> '0',
			),
			array(
				'config_name'	=> 'name2',
				'config_value'	=> 'value2',
				'is_dynamic'	=> '0',
			),
			array(
				'config_name'	=> 'name3',
				'config_value'	=> 'value3',
				'is_dynamic'	=> '0',
			),
		)));

		$this->assert_config_count($db, 5);
	}

	public function test_multi_insert_enabled_insert_all_and_flush()
	{
		$db = $this->new_dbal();

		if (!$db->multi_insert)
		{
			$this->markTestSkipped('Database does not support multi_insert');
		}

		$buffer = new phpbb_db_sql_insert_buffer($db, 'phpbb_config', 2);

		$this->assert_config_count($db, 2);

		$this->assertTrue($buffer->insert_all(array(
			array(
				'config_name'	=> 'name1',
				'config_value'	=> 'value1',
				'is_dynamic'	=> '0',
			),
			array(
				'config_name'	=> 'name2',
				'config_value'	=> 'value2',
				'is_dynamic'	=> '0',
			),
			array(
				'config_name'	=> 'name3',
				'config_value'	=> 'value3',
				'is_dynamic'	=> '0',
			),
		)));

		$this->assert_config_count($db, 4);

		// Manually flush
		$this->assertTrue($buffer->flush());

		$this->assert_config_count($db, 5);
	}
}
