<?php
//imports des api phpbb
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'phpbb/passwords/manager.' . $phpEx);

function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

//gestionnaire de passwords
$passwords_manager = $phpbb_container->get('passwords.manager');
try
{
	$bdd = new PDO('mysql:host=localhost;dbname=wefrag;charset=utf8', 'root', '');
	$reponse = $bdd->query('select * from users');
	while ($donnees = $reponse->fetch())
	{
		// traiter chaque ligne de la table wefrag_users
		$old_userid = $donnees['id'];
		$login = $donnees['login'];
		$password = randomPassword();
		$hash = $passwords_manager->hash($password);
		$email = $donnees['email'];
		$birthdate = new DateTime($donnees['birthdate']);
		$birthdate = $birthdate->format('Y-m-d');
		$regdate = new DateTime($donnees['created_at']);
		$regdate = $regdate->getTimestamp();
		$group_id = 2;
		$isAdmin = $donnees['is_admin'];
		if ($isAdmin=="1"){
			$group_id = 5;
		}
		$user_row = array(
			'username'              => $login,
			'user_password'         => $hash,
			'user_email'            => $email,
			'group_id'              => $group_id,
			'user_birthday'			=> $birthdate,
			'user_regdate'			=> $regdate,
			'user_type'             => USER_NORMAL,
		);
		$user_id = user_add($user_row);
		$log = $old_userid.";".$user_id.";".$email.";".$password ;
		$myfile = file_put_contents('users.log', $log.PHP_EOL , FILE_APPEND | LOCK_EX);

		
	}
}
catch (Exception $e)
{
        die('Erreur : ' . $e->getMessage());
}
?>