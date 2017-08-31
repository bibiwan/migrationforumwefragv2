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
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
include($phpbb_root_path . 'includes/message_parser.' . $phpEx);

$username = "root";
$password = "root123";

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();


$result = $auth->login($username, $password);

if ($result['status'] == LOGIN_SUCCESS)
{
  // note that multibyte support is enabled here 
$my_subject = 'test de sujet';
$my_text    = 'lorem ipsum';

// variables to hold the parameters for submit_post
$poll = $uid = $bitfield = $options = ''; 

$backup_auth = $auth;

generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
generate_text_for_storage($my_text, $uid, $bitfield, $options, true, true, true);

$data = array( 
    'forum_id'      => 2,
    'icon_id'       => false,

    'enable_bbcode'     => true,
    'enable_smilies'    => true,
    'enable_urls'       => true,
    'enable_sig'        => true,

    'message'       => $my_text,
    'message_md5'   => md5($my_text),
                
    'bbcode_bitfield'   => $bitfield,
    'bbcode_uid'        => $uid,

    'post_edit_locked'  => 0,
    'topic_title'       => $my_subject,
    'notify_set'        => false,
    'notify'            => false,
    'post_time'         => 0,
    'forum_name'        => '',
    'enable_indexing'   => true,
);

submit_post('post', $my_subject, '', POST_NORMAL, $poll, $data);
}
else
{
   echo $result['status'];
}	



?>