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
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
include($phpbb_root_path . 'includes/message_parser.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

/**
permet de découper une url et obtenir un tableau des arguments.
**/
function my_parse_query($str) {
    if (($champs = explode('&', $str)) === false) {
        return array();
    }
    $resultat = array();
    foreach($champs as $champ) {
        if (($zone = explode('=', $champ, 2)) === false) {
            $nom = urldecode($champ);
            $valeur = '';
        } else {
            $nom = urldecode($zone[0]);
            if (isset($zone[1])) {
                $valeur = urldecode($zone[1]);
            } else {
                $valeur = '';
            }
        }
        if (empty($resultat[$nom])) {
            $resultat[$nom] = $valeur;
        } elseif (!empty($valeur)) {
            $resultat[$nom] .= ' ' . $valeur;
        }
    }
    return $resultat;
}

/**
Permet d'obtenir un identifiant et mot de passe de forum à partir de l'id wefrag
**/
function get_user($oldid) {
	$host = "localhost";
	$dbname = "wefrag";
	$login_db = "root";
	$pwd_db = "";
	
	$bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $login_db , $pwd_db);
	$reponse = $bdd->query('select * from transpo_users where wefrag_id=\''.$oldid.'\';');
	$donnees = $reponse->fetch();
	$data = array(
	'password'      => $donnees['password'],
    'id'       => $donnees['id'],
	);
	return $data;
}

/**
Permet d'obtenir un id de topic à partir de l'id  du forum et du topic sur wefrag.
**/
function get_topic($forum_id,$oldid) {
	$host = "localhost";
	$dbname = "wefrag";
	$login_db = "root";
	$pwd_db = "";
	
	$bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $login_db , $pwd_db);
	$query = 'select * from transpo_topics where forum_id=\''.$forum_id.'\' and oldid=\''.$oldid.'\';';
	$reponse = $bdd->query($query);
	$donnees = $reponse->fetch();
	$data = $donnees['id'];
	return $data;
}

/**
Permet d'insérer un topic dans la table de correspondance
**/
function insert_topic($forum_id,$id,$oldid) {
	$host = "localhost";
	$dbname = "wefrag";
	$login_db = "root";
	$pwd_db = "";
	
	$bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $login_db , $pwd_db);
	echo 'insert into transpo_topics values(\''.$forum_id.'\',\''.$id.'\',\''.$oldid.'\')';
	$reponse = $bdd->query('insert into transpo_topics values(\''.$forum_id.'\',\''.$id.'\',\''.$oldid.'\')');
	$donnees = $reponse->fetch();
	$data = array(
    'id'       => $donnees['id'],
	);
	return $data;
}

try
{
	$host = "localhost";
	$dbname = "wefrag";
	$login_db = "root";
	$pwd_db = "";
	
	$bdd = new PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $login_db , $pwd_db);
	$reponse = $bdd->query('select * from posts order by created_at asc');
	//Pour chaque message de la table wefrag_posts triés par date ascendante :
	while ($donnees = $reponse->fetch())
	{	
		$oldid = $donnees['user_id'];
		// Récupérer login et mdp provisoire de l'auteur du message
		$credentials = get_user($oldid);
		//Login sur phpbb via l'api avec ces identifiants
		$user->session_begin();
		$auth->acl($user->data);
		$user->setup();	
		$username_ary = array();
		$user_id_ary = array($credentials['id']);
		user_get_id_name($user_id_ary, $username_ary);
		$result = $auth->login($username_ary[$credentials['id']],$credentials['password']);
		// si connexion ok
		if ($result['status'] == LOGIN_SUCCESS)
		{
			$url = "";
			$my_text = $donnees['body']	;
			$datecreation = new Datetime($donnees['created_at']);
			$oldid = $donnees['id']	;
			// variables to hold the parameters 
			$poll = $uid = $bitfield = $options = ''; 
			if(strlen(($donnees['topic_id']))==0){
				echo "topic";
				//si premier message de topic, message de type post
				$my_subject =$donnees['title'];
				$poll = $uid = $bitfield = $options = ''; 
				$data = array( 
				'forum_id'		    => $donnees['forum_id'],
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
				);

				$url = submit_post('post', $my_subject, '', POST_NORMAL, $poll, $data);
				$url = my_parse_query($url);
				var_dump($url);
				// insertion dans table de correspondance des topics wefrag avec ancien et nouvel id
				insert_topic($url['../viewtopic.php?f'],$url['amp;t'],$oldid);
				$log = $url['../viewtopic.php?f'].';'.$oldid.";".$oldid.";".$url['amp;t'] ;
				$myfile = file_put_contents('posts.log', $log.PHP_EOL , FILE_APPEND | LOCK_EX);
			}
			else{
				echo "réponse";
				//sinon, message de type reply
				$topic_id = get_topic($donnees['forum_id'],$donnees['topic_id']);
				$data = array( 
				'forum_id'		    => $donnees['forum_id'],
				'topic_id'            => $topic_id,
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
				'topic_title'       => '',
				'notify_set'        => false,
				'notify'            => false,
				'post_time'         => $datecreation->getTimestamp(),
				'forum_name'        => '',
				'enable_indexing'   => true,
				);
				
				$url = submit_post('reply', '', '', POST_NORMAL, $poll, $data);
				$url = my_parse_query($url);
				
				//on stocke dans un fichier l'url renvoyée par la méthode de création avec l'id de l'ancien topic pour l'utiliser sur la table de tracking de lecture (table de correspondance et fichier htaccess).
				$log = $url['../viewtopic.php?f'].';'.$oldid.";".$oldid.";".$url['amp;t'] ;
				$myfile = file_put_contents('posts.log', $log.PHP_EOL , FILE_APPEND | LOCK_EX);
			}
			// déconnexion pour réinitialiser la session au prochain post.
			$user->session_kill();
		}
		else
		{
			// probleme d'authentification
			echo $result['error_msg'];
		}
		
		
		}
	}
	catch (Exception $e)
	{
		die('Erreur : ' . $e->getMessage());
	}



?>