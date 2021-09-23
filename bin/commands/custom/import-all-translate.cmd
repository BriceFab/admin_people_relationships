REM import tout
php bin/console d1f:tra:i:all

REM import que le domain messages
php bin/console d1f:tra:i:all messages

REM import que le domain messages et remet toutes en base de données, même si elles existent deja !!!Ne pas utiliser en prod
php bin/console d1f:tra:i:all messages force
