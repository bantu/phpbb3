<?php
/**
*
* @package testing
* @copyright (c) 2014 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class travis_test extends phpbb_test_case
{
	public function testAssert()
	{
		assert ( 0 && "internal error: unhandled filter type" );
		$this->assertTrue(true);
	}
}
