<?php
/**
*
* @package testing
* @copyright (c) 2010 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class phpbb_cache_file_driver_test extends phpbb_database_test_case
{
	private $cache_dir;
	protected $driver;

	public function __construct()
	{
		$this->cache_dir = dirname(__FILE__) . '/../tmp/cache/';
	}

	public function getDataSet()
	{
		return $this->createXMLDataSet(dirname(__FILE__) . '/fixtures/config.xml');
	}

	protected function setUp()
	{
		parent::setUp();

		if (file_exists($this->cache_dir))
		{
			// cache directory possibly left after aborted
			// or failed run earlier
			$this->remove_cache_dir();
		}
		$this->create_cache_dir();

		$this->driver = new phpbb_cache_driver_file($this->cache_dir);
	}

	protected function tearDown()
	{
		if (file_exists($this->cache_dir))
		{
			$this->remove_cache_dir();
		}

		parent::tearDown();
	}

	private function create_cache_dir()
	{
		$this->get_test_case_helpers()->makedirs($this->cache_dir);
	}

	private function remove_cache_dir()
	{
		$iterator = new DirectoryIterator($this->cache_dir);
		foreach ($iterator as $file)
		{
			if ($file != '.' && $file != '..')
			{
				unlink($this->cache_dir . '/' . $file);
			}
		}
		rmdir($this->cache_dir);
	}

	public function test_cache_driver_file()
	{
		$this->driver->put('test_key', 'test_value');
		$this->driver->save();

		$this->assertEquals(
			'test_value',
			$this->driver->get('test_key'),
			'File ACM put and get'
		);
	}

	public function test_cache_sql_file()
	{
		global $db, $cache;
		$db = $this->new_dbal();
		$cache = new phpbb_cache_service($this->driver);

		$sql = "SELECT * FROM phpbb_config
			WHERE config_name = 'foo'";

		$cache_path = $this->cache_dir . 'sql_' . md5(preg_replace('/[\n\r\s\t]+/', ' ', $sql)) . '.php';
		$this->assertFileNotExists($cache_path);

		$result = $db->sql_query($sql, 300);
		$first_result = $db->sql_fetchrow($result);
		$expected = array('config_name' => 'foo', 'config_value' => '23', 'is_dynamic' => 0);
		$this->assertEquals($expected, $first_result);

		$this->assertFileExists($cache_path);

		$sql = 'DELETE FROM phpbb_config';
		$result = $db->sql_query($sql);

		$sql = "SELECT * FROM phpbb_config
			WHERE config_name = 'foo'";
		$result = $db->sql_query($sql, 300);

		$this->assertEquals($expected, $db->sql_fetchrow($result));

		$sql = "SELECT * FROM phpbb_config
			WHERE config_name = 'foo'";
		$result = $db->sql_query($sql);

		$no_cache_result = $db->sql_fetchrow($result);
		$this->assertSame(false, $no_cache_result);

		$db->sql_close();
	}
}