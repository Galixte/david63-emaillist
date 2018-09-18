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

	'E_CLEAR_FILTER'		=> 'Annuler les filtres',
	'E_CSV'					=> 'Exporter la liste des adresses e-mail au format CSV',
	'E_SORT'				=> 'Trier',
	'EMAIL_LIST'			=> 'Liste des adresses e-mails',
	'EMAIL_LIST_EXPLAIN'	=> 'Depuis cette page il est possible de consulter et exporter la liste des adresses e-mails des membres.',

	'FILTER_BY'				=> 'Filter par',
	'FILTER_EMAIL'			=> 'Adresse e-mail',
	'FILTER_USERNAME'		=> 'Nom d’utilisateur',

	'JABBER_ADDRESS'		=> 'Adresse Jabber',

	'OTHER'					=> 'Autres caractères',

	'SELECT_CHAR'			=> 'Sélectionner des caractères',
	'SORT_BY'				=> 'Trier par',
	'SORT_EMAIL'			=> 'Adresse e-mail',
	'SORT_JABBER'			=> 'Adresse Jabber',
	'SORT_USERNAME'			=> 'Nom d’utilisateur',

	'TOTAL_EMAIL_USERS'		=> 'Total d’utilisateurs : <strong>%s</strong>',

	// Translators - set these to whatever is most appropriate in your language
	// These are used to populate the filter keys
	'START_CHARACTER'		=> 'A',
	'END_CHARACTER'			=> 'Z',
));
