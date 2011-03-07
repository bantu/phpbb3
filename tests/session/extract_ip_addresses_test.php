<?php
/**
*
* @package testing
* @copyright (c) 2011 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

require_once dirname(__FILE__) . '/../../phpBB/includes/functions.php';
require_once dirname(__FILE__) . '/../../phpBB/includes/session.php';

class phpbb_session_extract_ip_addresses_test extends phpbb_test_case
{
	/**
	* @dataProvider clean_data
	*/
	public function test_clean_data($ips, $expected)
	{
		$result = session::extract_ip_addresses($ips, 'break');
		$this->assertEquals($expected, $result);

		$result = session::extract_ip_addresses($ips, 'continue');
		$this->assertEquals($expected, $result);

		$result = session::extract_ip_addresses($ips, 'return');
		$this->assertEquals($expected, $result);
	}

	static public function clean_data()
	{
		return array(
			array(
				'1.2.3.4',
				array('1.2.3.4'),
			),
			array(
				'1.2.3.4 1.2.3.5',
				array('1.2.3.4', '1.2.3.5'),
			),
			array(
				'1.2.3.4  1.2.3.5',
				array('1.2.3.4', '1.2.3.5'),
			),
			array(
				'1.2.3.4   1.2.3.5',
				array('1.2.3.4', '1.2.3.5'),
			),
			array(
				'1.2.3.4,1.2.3.5',
				array('1.2.3.4', '1.2.3.5'),
			),
			array(
				'1.2.3.4, 1.2.3.5',
				array('1.2.3.4', '1.2.3.5'),
			),
			array(
				'1.2.3.4 , 1.2.3.5',
				array('1.2.3.4', '1.2.3.5'),
			),
			array(
				'1.2.3.4  ,  1.2.3.5  ,  1.2.3.6',
				array('1.2.3.4', '1.2.3.5', '1.2.3.6'),
			),
			array(
				'1.2.3.4,1.2.3.5, 1.2.3.6 1.2.3.7',
				array('1.2.3.4', '1.2.3.5', '1.2.3.6', '1.2.3.7'),
			),
			array(
				' 1.2.3.4,1.2.3.5, 1.2.3.6 1.2.3.7 ',
				array('1.2.3.4', '1.2.3.5', '1.2.3.6', '1.2.3.7'),
			),
			array(
				'1.2.3.4, 2001:db8:85a3:0:0:8a2e:370:1337',
				array('1.2.3.4', '2001:db8:85a3:0:0:8a2e:370:1337'),
			),
		);
	}

	/**
	* @dataProvider totally_broken_data
	*/
	public function test_totally_broken_data($ips)
	{
		$result = session::extract_ip_addresses($ips, 'break');
		$this->assertEquals(false, $result);

		$result = session::extract_ip_addresses($ips, 'continue');
		$this->assertEquals(false, $result);

		$result = session::extract_ip_addresses($ips, 'return');
		$this->assertEquals(false, $result);
	}

	static public function totally_broken_data()
	{
		return array(
			array('1.2.3.41.2.3.5, 1.2.3.2.3.5'),
			array('blargh'),
			array(''),
			array('2001:db8:85a3:0:0:8a2e:370:1337-1.2.2.3.5,,')
		);
	}

	/**
	* @dataProvider partially_broken_data
	*/
	public function test_partially_broken_data($ips, $mode, $expected)
	{
		$result = session::extract_ip_addresses($ips, $mode);
		$this->assertEquals($expected, $result);
	}

	static public function partially_broken_data()
	{
		return array(
			array(
				'1.2.3.4 | 1.2.3.5', 'return',
				false,
			),
			array(
				'1.2.3.4 | 1.2.3.5', 'continue',
				array('1.2.3.4', '1.2.3.5'),
			),
			array(
				'1.2.3.4 | 1.2.3.5', 'break',
				array('1.2.3.4'),
			),
		);
	}
}
