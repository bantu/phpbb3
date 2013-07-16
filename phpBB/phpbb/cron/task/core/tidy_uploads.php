<?php
/**
*
* @package phpBB3
* @copyright (c) 2012 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* Tidy plupload temporary directory cron task.
*
* @package phpBB3
*/
class phpbb_cron_task_core_tidy_uploads extends phpbb_cron_task_base
{
	/**
	* How old a file must be before it's deleted (24 hours)
	*/
	const MAX_FILE_AGE = 86400;

	/**
	* How often we run the cron
	*/
	const CRON_FREQUENCY = 86400;

	/**
	* Config object
	* @var phpbb_config
	*/
	protected $config;

	/**
	* phpBB's installed root path
	* @var string
	*/
	protected $phpbb_root_path;

	/**
	* Constructor.
	*
	* @param string $phpbb_root_path The root path
	* @param phpbb_config $config The config
	*/
	public function __construct($phpbb_root_path, phpbb_config $config)
	{
		$this->phpbb_root_path = $phpbb_root_path;
		$this->config = $config;
	}

	/**
	* Runs this cron task.
	*
	* @return void
	*/
	public function run()
	{
		// Remove old temporary file (perhaps failed uploads?)
		$dir = $this->config['upload_path'] . '/plupload';
		$time_difference = time() - self::MAX_FILE_AGE;
		try
		{
			$iterator = new DirectoryIterator($dir);
			foreach ($iterator as $file)
			{
				if ($file->getBasename() === 'index.htm')
				{
					continue;
				}

				if ($file->getMTime() < $time_difference)
				{
					@unlink($file->getPathname());
				}
			}
		}
		catch (UnexpectedValueException $e)
		{
			add_log(
				'admin',
				'LOG_PLUPLOAD_TIDY_FAILED',
				$dir,
				$e->getMessage(),
				$e->getTraceAsString()
			);
		}

		$this->config->set('plupload_last_gc', time(), true);
	}

	/**
	* Returns whether this cron task can run, given current board configuration.
	*
	* This cron runs only if the plupload subdirectory exists
	*
	* @return bool
	*/
	public function is_runnable()
	{
		return is_dir($this->phpbb_root_path . $this->config['upload_path'] . '/plupload');
	}

	/**
	* Returns whether this cron task should run now, because enough time
	* has passed since it was last run.
	*
	* The interval between cache tidying is specified in board
	* configuration.
	*
	* @return bool
	*/
	public function should_run()
	{
		return $this->config['plupload_last_gc'] < time() - self::CRON_FREQUENCY;
	}
}
