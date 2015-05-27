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

	/** @var \phpbb\user */
	protected $user;

	/** @var ContainerInterface */
	protected $container;

	/** @var string Custom form action */
	protected $u_action;

	/**
	* Constructor for admin controller
	*
	* @param \phpbb\config\config				$config		Config object
	* @param \phpbb\db\driver\driver_interface	$db
	* @param \phpbb\request\request				$request	Request object
	* @param \phpbb\template\template			$template	Template object
	* @param \phpbb\user						$user		User object
	* @param ContainerInterface					$container	Service container interface
	*
	* @return \david63\emaillist\controller\admin_controller
	* @access public
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, ContainerInterface $container)
	{
		$this->config		= $config;
		$this->db  			= $db;
		$this->request		= $request;
		$this->template		= $template;
		$this->user			= $user;
		$this->container	= $container;
	}

	/**
	* Display the output for this extension
	*
	* @return null
	* @access public
	*/
	public function display_output()
	{
		// Start initial var setup
		$action			= $this->request->variable('action', '');
		$start			= $this->request->variable('start', 0);
		$fc				= $this->request->variable('fc', '');
		$sort_key		= $this->request->variable('sk', 'u');
		$sd = $sort_dir	= $this->request->variable('sd', 'a');

		$sort_dir		= ($sort_dir == 'd') ? ' DESC' : ' ASC';

		$order_ary = array(
			'e'	=> 'user_email' . $sort_dir. ', user_email ASC',
			'j'	=> 'user_jabber' . $sort_dir. ', user_jabber ASC',
			'u'	=> 'username_clean' . $sort_dir,
		);

		$filter_by = '';
		if ($fc == 'other')
		{
			for ($i = 97; $i < 123; $i++)
			{
				$filter_by .= ' AND username_clean ' . $this->db->sql_not_like_expression(utf8_clean_string(chr($i)) . $this->db->get_any_char());
			}
		}
		else if ($fc)
		{
			$filter_by .= ' AND username_clean ' . $this->db->sql_like_expression(utf8_clean_string(substr($fc, 0, 1)) . $this->db->get_any_char());
		}

		$order_by = ($sort_key == '') ? 'username_clean' : $order_ary[$sort_key];

	   	$sql = 'SELECT user_id, username, username_clean, user_colour, user_email, user_jabber
			FROM ' . USERS_TABLE . '
				WHERE user_type <> ' . USER_IGNORE . "
				$filter_by
			ORDER BY $order_by";

		$result = $this->db->sql_query_limit($sql, $this->config['topics_per_page'], $start);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('emaillist', array(
				'EMAIL'		=> $row['user_email'],
				'JABBER'	=> ($this->config['jab_enable']) ? $row['user_jabber'] : '',
				'USERNAME'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
		   	));
		}
		$this->db->sql_freeresult($result);

		$sort_by_text	= array('u' => $this->user->lang('SORT_USERNAME'), 'e' => $this->user->lang('SORT_EMAIL'));
		if ($this->config['jab_enable'])
		{
			$sort_by_text['j'] = $this->user->lang('SORT_JABBER');
		}
		$limit_days		= array();
		$s_sort_key		= $s_limit_days = $s_sort_dir = $u_sort_param = '';

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

		$pagination	= $this->container->get('pagination');
		$start		= $pagination->validate_start($start, $this->config['topics_per_page'], $user_count);
		$pagination->generate_template_pagination($action, 'pagination', 'start', $user_count, $this->config['topics_per_page'], $start);

		$first_characters		= array();
		$first_characters['']	= $this->user->lang('ALL');
		for ($i = ord('a'); $i <= ord('z'); $i++)
		{
			$first_characters[chr($i)] = chr($i - 32);
		}
		$first_characters['other'] = $this->user->lang('OTHER');

		foreach ($first_characters as $char => $desc)
		{
			$this->template->assign_block_vars('first_char', array(
				'DESC'			=> $desc,
				'U_SORT'		=> $action . '&amp;fc=' . $char,
			));
		}

		$this->template->assign_vars(array(
			'S_JAB_ENABLE'	=> $this->config['jab_enable'],
			'S_SORT_DIR'	=> $s_sort_dir,
			'S_SORT_KEY'	=> $s_sort_key,
			'TOTAL_USERS'	=> $this->user->lang('TOTAL_USERS', (int) $user_count),

			'U_ACTION'		=> $action,
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
