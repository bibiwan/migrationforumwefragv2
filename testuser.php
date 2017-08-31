<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'phpbb/passwords/manager.' . $phpEx);

$passwords_manager = $phpbb_container->get('passwords.manager');
$hash = $passwords_manager->hash('test-123');
$user_row = array(
    'username'              => "test",
    'user_password'         => $hash,
    'user_email'            => "test@test.fr",
    'group_id'              => 2,
    'user_type'             => USER_NORMAL,
);
//var_dump($user_row)
// all the information has been compiled, add the user
// tables affected: users table, profile_fields_data table, groups table, and config table.
$user_id = user_add($user_row);
echo $user_id;
?>