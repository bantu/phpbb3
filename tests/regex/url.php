<?php
/**
*
* @package testing
* @copyright (c) 2010 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

require_once 'test_framework/framework.php';
require_once '../phpBB/includes/functions.php';

class phpbb_regex_url_test extends phpbb_test_case
{
	protected $regex;

	public function setUp()
	{
		$this->regex = '#^' . get_preg_expression('url') . '$#i';
	}

	public function positive_match_data()
	{
		return array(
			array('http://www.phpbb.com:81/community/'),
			array('http://www.phpbb.com/path/file.ext#section'),
			array('ftp://ftp.phpbb.com/'),
			array('sip://bantu@phpbb.com'),

			// IPv4
			array('http://192.168.1.1:81/community/'),
			array('http://192.168.1.1/path/file.ext#section'),
			array('ftp://192.168.1.1/'),
			array('sip://bantu@192.168.1.1'),

			// IPv6 - from rfc2732
			array('http://[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:80/index.html'),
			array('http://[1080:0:0:0:8:800:200C:417A]/index.html'),
			array('http://[3ffe:2a00:100:7031::1]'),
			array('http://[1080::8:800:200C:417A]/foo'),
			array('http://[::192.9.5.5]/ipng'),
			array('http://[::FFFF:129.144.52.38]:80/index.html'),
			array('http://[2010:836B:4179::836B:4179]'),
		);
	}

	public function negative_match_data()
	{
		return array(
			array('www.phpbb.com/community/'),
		);
	}

	/**
	* @dataProvider positive_match_data
	*/
	public function test_positive_match($string)
	{
		$this->assertEquals(1, preg_match($this->regex, $string));
	}

	/**
	* @dataProvider negative_match_data
	*/
	public function test_negative_match($string)
	{
		$this->assertEquals(0, preg_match($this->regex, $string));
	}
}
