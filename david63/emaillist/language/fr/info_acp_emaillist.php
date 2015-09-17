<?php
/**
*
* Email list extension for the phpBB Forum Software package.
* French translation by Galixte (http://www.galixte.com)
*
* @copyright (c) 2015 david63
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ « » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_USER_UTILS'		=> 'Outils utilisateur',
	'ALL'					=> 'Tous les caractères',

	'EMAIL_LIST'			=> 'Liste des adresses e-mails',
	'EMAIL_LIST_EXPLAIN'	=> 'Sur cette page se trouve la liste des adresses e-mails des membres.',

	'FILTER_BY'				=> 'Filter par',
	'FILTER_EMAIL'			=> 'E-mail',
	'FILTER_USERNAME'		=> 'Nom d’utilisateur',

	'JABBER_ADDRESS'		=> 'Adresse Jabber',

	'OTHER'					=> 'Autres caractères',

	'SELECT_CHAR'			=> 'Sélectionner des caractères',
	'SORT_BY'				=> 'Trier par',
	'SORT_EMAIL'			=> 'E-mail',
	'SORT_JABBER'			=> 'Adresse Jabber',
	'SORT_USERNAME'			=> 'Nom d’utilisateur',

	'TOTAL_USERS'			=> 'Total des utilisateurs',
));