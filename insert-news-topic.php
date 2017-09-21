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
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

try
{
	$loginNofrag = "nofrag";
	$pwdNofrag = "HRJqIKwu"; // test : "HRJqIKwu" prod : "S7yFKDIO"
	$keynofrag = "usagee5BahNgah1u";
	$forum_id = 16 ; // 16 pour test, 11 pour prod
	/*
	test fait avec : http://nomdedomaine/insert-news-topic.php?content=Un+certain+Zeng+Xiancheng+pr%C3%A9sentait+en+juin+dernier+Bright+Memory%2C+un+projet+de+FPS+sous+l%27Unreal+Engine+4+qu%27il+d%C3%A9veloppe+en+solitaire.+La+premi%C3%A8re+bande-annonce+montrait+un+gameplay+m%C3%AAlant+armes+%C3%A0+feu+et+combat+au+corps+%C3%A0+corps%2C+un+peu+semblable+%C3%A0+Shadow+Warrior+dans+un+style+plus+bord%C3%A9lique.+La+vid%C3%A9o+avait+alors+%C3%A9t%C3%A9+mise+en+avant+par+Epic+Games.+Deux+mois+plus+tard%2C+un+nouvel+extrait+de+cinq+minutes+de+gameplay+est+sorti+%3A%0D%0A%0D%0ARegarder+la+vid%C3%A9o+sur+Youtube%0D%0AQuand+on+consid%C3%A8re+que+c%27est+un+seul+homme+qui+a+r%C3%A9alis%C3%A9+tout+%C3%A7a%2C+oui+c%27est+impressionnant.+Mais+une+fois+pass%C3%A9+le+stade+de+l%27admiration%2C+Bright+Memory+ressemble+surtout+%C3%A0+un+FPS+sans+grande+inspiration+ni+coh%C3%A9rence%2C+bricol%C3%A9+avec+des+assets+g%C3%A9n%C3%A9riques+et+une+IA+%C3%A0+la+ramasse.+Le+HUD+et+le+syst%C3%A8me+de+combo+peuvent+malgr%C3%A9+tout+s%27av%C3%A9rer+int%C3%A9ressants%2C+%C3%A0+condition+que+Zeng+trouve+une+m%C3%A9canique+plus+originale+que+celle+de+martyriser+son+clic+gauche+comme+un+demeur%C3%A9+en+attendant+le+prochain+%C3%A9v%C3%A9nement+script%C3%A9.+L%C3%A0%2C+on+a+surtout+l%27impressio&title=Bright+Memory%2C+un+FPS+d%C3%A9velopp%C3%A9+par+une+seule+personne
	*/
		//Login sur phpbb via l'api avec ces identifiants
		$user->session_begin();
		$auth->acl($user->data);
		$user->setup();	
		// login 
		$result = $auth->login($loginNofrag,$pwdNofrag);
		// si connexion ok
		if ($result['status'] == LOGIN_SUCCESS)
		{
			$key =  urldecode($request->variable('key', "",true));
			$url =  urldecode($request->variable('url', "",true));
			$my_text = urldecode($request->variable('content', "",true));
			$my_subject = urldecode($request->variable('title', '',true));
			
			if (strlen($my_text) > 0 && strlen($my_subject)>0 && $key==$keynofrag){
				$my_text =$my_text."\n\n\n[url=".$url."]Lire toute la news sur Nofrag.com...[/url]";
				$my_text = utf8_normalize_nfc($my_text);
				$uid = $bitfield = $options = ''; // will be modified by generate_text_for_storage
				$allow_bbcode = $allow_urls = $allow_smilies = true;
				generate_text_for_storage($my_text, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
				$datecreation = new DateTime();
				$oldid = $details['id']	;
				// variables to hold the parameters 
				$poll = $uid = $bitfield = $options = ''; 
				$data = array( 
				'forum_id'		    => $forum_id,
				'topic_id'            =>0,
				'icon_id'       	=> false,
				'enable_bbcode'     => true,
				'enable_smilies'    => false,
				'enable_urls'       => true,
				'enable_sig'        => false,
				'message'       	=> $my_text,
				'message_md5'   	=> md5($my_text),
				'bbcode_bitfield'   => $bitfield,
				'bbcode_uid'        => $uid,
				'post_edit_locked'  => 1,
				'topic_title'       => $my_subject,
				'notify_set'        => false,
				'notify'            => false,
				'post_time'         => $datecreation->getTimestamp(),
				'forum_name'        => '',
				'enable_indexing'   => true,
				'force_visibility'  => true,
				);

				$url = submit_post('post', $my_subject, '', POST_NORMAL, $poll, $data);
				echo $url;
			}
			// déconnexion pour réinitialiser la session au prochain post.
			$user->session_kill();
		}
		else
		{
			// probleme d'authentification
			echo $result['error_msg'];
			exit(1);
		}
	}
	catch (Exception $e)
	{
		
		die('Erreur : ' . $e->getMessage());
	}



?>