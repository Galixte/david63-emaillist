<?php
/**
*
* @package Email List Extension
* @copyright (c) 2015 david63
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace david63\emaillist\migrations;

class version_1_0_0 extends \phpbb\db\migration\migration
{
	public function update_data()
	{
		$update_data = array();

		$update_data[] = array('config.add', array('version_emaillist', '1.0.0'));

		if ($this->module_check())
		{
			$update_data[] = array('module.add', array('acp', 'ACP_CAT_USERGROUP', 'ACP_USER_UTILS'));
		}

		$update_data[] = array('module.add', array(
			'acp', 'ACP_USER_UTILS', array(
				'module_basename'	=> '\david63\emaillist\acp\emaillist_module',
				'modes'				=> array('main'),
			),
		));

		return $update_data;
	}

	protected function module_check()
	{
		$sql = 'SELECT module_id
				FROM ' . $this->table_prefix . "modules
    			WHERE module_class = 'acp'
        			AND module_langname = 'ACP_USER_UTILS'
        			AND right_id - left_id > 1";

		$result		= $this->db->sql_query($sql);
		$module_id	= (int) $this->db->sql_fetchfield('module_id');
		$this->db->sql_freeresult($result);

		// return true if module is empty, false if has children
		return (bool) !$module_id;
	}
}