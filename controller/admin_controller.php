<?php
/**
*
* @package Email List Extension
* @copyright (c) 2015 david63
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace david63\emaillist\controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use david63\emaillist\ext;

/**
* Admin controller
*/
class admin_controller implements admin_interface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\pagination */
	protected $pagination;

	/** @var phpbb\language\language */
	protected $language;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor for admin controller
	*
	* @param \phpbb\config\config				$config		Config object
	* @param \phpbb\db\driver\driver_interface	$db
	* @param \phpbb\request\request				$request	Request object
	* @param \phpbb\template\template			$template	Template object
	* @param \phpbb\pagination					$pagination
	* @param phpbb\language\language			$language
	* @param string 							$phpbb_root_path
	*
	* @return \david63\emaillist\controller\admin_controller
	* @access public
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\pagination $pagination, \phpbb\language\language $language, $phpbb_root_path)
	{
		$this->config			= $config;
		$this->db  				= $db;
		$this->request			= $request;
		$this->template			= $template;
		$this->pagination		= $pagination;
		$this->language			= $language;
		$this->phpbb_root_path	= $phpbb_root_path;
	}

	/**
	* Display the output for this extension
	*
	* @return null
	* @access public
	*/
	public function display_output()
	{
		// Add the language file
		$this->language->add_lang('acp_emaillist', 'david63/emaillist');

		// Start initial var setup
		$action			= $this->request->variable('action', '');
		$clear_filters	= $this->request->variable('clear_filters', '');
		$csv			= $this->request->variable('csv', false);
		$fce			= $this->request->variable('fce', '');
		$fcu			= $this->request->variable('fcu', '');
		$sort_key		= $this->request->variable('sk', 'u');
		$sd = $sort_dir	= $this->request->variable('sd', 'a');
		$start			= $this->request->variable('start', 0);

		if ($clear_filters)
		{
			$fce = $fcu		= '';
			$sd = $sort_dir	= 'a';
			$sort_key		= 'u';
		}

		$sort_dir = ($sort_dir == 'd') ? ' DESC' : ' ASC';

		$order_ary = array(
			'e'	=> 'user_email' . $sort_dir. ', user_email ASC',
			'j'	=> 'user_jabber' . $sort_dir. ', user_jabber ASC',
			'u'	=> 'username_clean' . $sort_dir,
		);

		$filter_by = '';
		if ($fcu == 'other')
		{
			for ($i = ord($this->language->lang('START_CHARACTER')); $i	<= ord($this->language->lang('END_CHARACTER')); $i++)
			{
				$filter_by .= ' AND username_clean ' . $this->db->sql_not_like_expression(utf8_clean_string(chr($i)) . $this->db->get_any_char());
			}
		}
		else if ($fcu)
		{
			$filter_by .= ' AND username_clean ' . $this->db->sql_like_expression(utf8_clean_string(substr($fcu, 0, 1)) . $this->db->get_any_char());
		}
		if ($fce == 'other')
		{
			for ($i = ord($this->language->lang('START_CHARACTER')); $i	<= ord($this->language->lang('END_CHARACTER')); $i++)
			{
				$filter_by .= ' AND user_email ' . $this->db->sql_not_like_expression(utf8_clean_string(chr($i)) . $this->db->get_any_char());
			}
		}
		else if ($fce)
		{
			$filter_by .= ' AND user_email ' . $this->db->sql_like_expression(utf8_clean_string(substr($fce, 0, 1)) . $this->db->get_any_char());
		}

		$order_by = ($sort_key == '') ? 'username_clean' : $order_ary[$sort_key];

	   	$sql = 'SELECT user_id, username, username_clean, user_colour, user_email, user_jabber
			FROM ' . USERS_TABLE . '
				WHERE user_type <> ' . USER_IGNORE . "
				$filter_by
			ORDER BY $order_by";

		$result = ($csv == true) ? $this->db->sql_query($sql) : $this->db->sql_query_limit($sql, $this->config['topics_per_page'], $start);

		// Create the CSV file
		if ($csv)
		{
			$filename	= $this->phpbb_root_path . '/store/phpBB_email_' . date('Ymd') . '.csv';
			$fp			= fopen($filename, 'w');
			$csv_data	= '';

			while ($row = $this->db->sql_fetchrow($result))
			{
				$csv_data .= '"' . $row['username'] . '","' . $row['user_email'] . '"' . "\r";
			}

			fwrite($fp, "\xEF\xBB\xBF"); // UTF-8 BOM
			fwrite($fp, $csv_data);
			fclose($fp);

			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-length: ' . filesize($filename));
			header("Content-disposition: attachment; filename=\"" . basename($filename) . "\"");
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');

			readfile($filename);
			// Delete the file
			unlink($filename);
			
			// We exit here as we do not want the rest of the page being output!
			exit;
		}
		else
		{
			while ($row = $this->db->sql_fetchrow($result))
			{
				$this->template->assign_block_vars('emaillist', array(
					'EMAIL'		=> $row['user_email'],
					'JABBER'	=> ($this->config['jab_enable']) ? $row['user_jabber'] : '',
					'USERNAME'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
		   		));
			}
		}
		$this->db->sql_freeresult($result);

		$sort_by_text = array('u' => $this->language->lang('SORT_USERNAME'), 'e' => $this->language->lang('SORT_EMAIL'));

		if ($this->config['jab_enable'])
		{
			$sort_by_text['j'] = $this->language->lang('SORT_JABBER');
		}

		$limit_days = array();
		$s_sort_key = $s_limit_days = $s_sort_dir = $u_sort_param = '';

		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sd, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);

		// Get total user count for pagination
		$sql = 'SELECT COUNT(user_id) AS total_users
			FROM ' . USERS_TABLE . '
				WHERE user_type <> ' . USER_IGNORE . "
				$filter_by
			ORDER BY $order_by";

		$result		= $this->db->sql_query($sql);
		$user_count	= (int) $this->db->sql_fetchfield('total_users');

		$this->db->sql_freeresult($result);

		$action = "{$this->u_action}&amp;sk=$sort_key&amp;sd=$sd";

		$start = $this->pagination->validate_start($start, $this->config['topics_per_page'], $user_count);
		$this->pagination->generate_template_pagination($action . "&amp;fce=$fce&amp;fcu=$fcu", 'pagination', 'start', $user_count, $this->config['topics_per_page'], $start);

		$first_characters		= array();
		$first_characters['']	= $this->language->lang('ALL');
		for ($i = ord($this->language->lang('START_CHARACTER')); $i	<= ord($this->language->lang('END_CHARACTER')); $i++)
		{
			$first_characters[chr($i)] = chr($i);
		}
		$first_characters['other'] = $this->language->lang('OTHER');

		foreach ($first_characters as $char => $desc)
		{
			$this->template->assign_block_vars('first_char_user', array(
				'DESC'		=> $desc,
				'U_FILTER'	=> $action . '&amp;fcu=' . $char . "&amp;fce=$fce",
			));

			$this->template->assign_block_vars('first_char_email', array(
				'DESC'		=> $desc,
				'U_FILTER'	=> $action . '&amp;fce=' . $char . "&amp;fcu=$fcu",
			));
		}

		$this->template->assign_vars(array(
			'EMAIL_LIST_VERSION'	=> ext::EMAIL_LIST_VERSION,
			'S_JAB_ENABLE'			=> $this->config['jab_enable'],
			'S_SORT_DIR'			=> $s_sort_dir,
			'S_SORT_KEY'			=> $s_sort_key,
			'TOTAL_USERS'			=> $this->language->lang('TOTAL_EMAIL_USERS', (int) $user_count),
			'U_ACTION'				=> $action . "&amp;fce=$fce&amp;fcu=$fcu",
		));
	}

	/**
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return null
	* @access public
	*/
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
