<?

use Dotenv\Dotenv;

require($_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php');


$dotenv = Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
$dotenv->load();
