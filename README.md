# migrationforumwefragv2

L'objectif est d'envoyer les utilisateurs et messages postés sur le forum de wefrag (https://github.com/Conardo/wefrag/) vers une solution sous phpbb.


Copier les fichiers à la racine de phpbb.
1. Installer les tables de transpo sur la base wefrag.
2. Interdire via .htaccess l'accès au répertoire migration à toute machine sauf localhost
3. Appeler migrationUser.php (via curl).
4. Appeler migrationPost.php (via curl).