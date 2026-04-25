require_once 'core/Session.php';
Session::requireSuperAdmin();

require_once 'core/Database.php';
$db = Database::getConnection();
$sql = file_get_contents('database/schema.sql');
$db->exec($sql);
echo "Migrated!\n";
