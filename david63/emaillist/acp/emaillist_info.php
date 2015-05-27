<?php
/**
*
* @package Email List Extension
* @copyright (c) 2015 david63
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace david63\emaillist\acp;

class emaillist_info
{
	function module()
	{
		return array(
			'filename'	=> '\david63\emaillist\acp\emaillist_module',
			'title'		=> 'EMAIL_LIST',
			'modes'		=> array(
				'main'		=> array('title' => 'EMAIL_LIST', 'auth' => 'ext_david63/emaillist && acl_a_user', 'cat' => array('ACP_CAT_USERS')),
			),
		);
	}
}
