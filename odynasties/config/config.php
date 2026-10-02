<?php
function ody_request_role(){
  $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
  if (strpos($path, '/odynasties/admin/') === 0 || rtrim($path,'/') === '/odynasties/admin') return 'admin';
  if (basename($path) === 'logout.php' && (($_GET['role'] ?? '') === 'admin')) return 'admin';
  return 'member';
}
function start_role_session($role){
  $role = $role === 'admin' ? 'admin' : 'member';
  if (session_status() === PHP_SESSION_ACTIVE) {
    if (session_name() === ($role === 'admin' ? 'ODY_ADMIN_SESSION' : 'ODY_MEMBER_SESSION')) return;
    session_write_close();
  }
  session_name($role === 'admin' ? 'ODY_ADMIN_SESSION' : 'ODY_MEMBER_SESSION');
  session_set_cookie_params([
    'lifetime'=>0,
    'path'=>'/odynasties',
    'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly'=>true,
    'samesite'=>'Lax'
  ]);
  session_start();
}
start_role_session(ody_request_role());
$host='127.0.0.1'; $db='odynasties'; $user='root'; $pass='';
$donation_mpesa_number='';
try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
  $pdo->exec("CREATE TABLE IF NOT EXISTS login_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token CHAR(64) NOT NULL UNIQUE,
    role ENUM('member','admin') NOT NULL,
    last_seen DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    revoked_at DATETIME NULL,
    INDEX idx_user_active (user_id, revoked_at, last_seen),
    CONSTRAINT fk_login_sessions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
  try { $pdo->exec("ALTER TABLE users ADD COLUMN must_change_password TINYINT(1) NOT NULL DEFAULT 0"); } catch (PDOException $ignored) {}
  try { $pdo->exec("ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) NULL"); } catch (PDOException $ignored) {}
  try { $pdo->exec("ALTER TABLE users ADD COLUMN bio TEXT NULL"); } catch (PDOException $ignored) {}
  try { $pdo->exec("ALTER TABLE users ADD COLUMN location VARCHAR(160) NULL"); } catch (PDOException $ignored) {}
  $pdo->exec("CREATE TABLE IF NOT EXISTS admin_activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NULL,
    admin_name VARCHAR(120) NULL,
    action VARCHAR(160) NOT NULL,
    details TEXT NULL,
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_admin_created(admin_id, created_at),
    INDEX idx_created(created_at),
    CONSTRAINT fk_admin_activity_user FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
  $pdo->exec("CREATE TABLE IF NOT EXISTS support_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    title VARCHAR(180) NOT NULL,
    category VARCHAR(80) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
    admin_note TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_support_status (status, created_at)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (PDOException $e) {
  http_response_code(500); die('Database connection failed. Create the odynasties database and import database/odynasties.sql first.');
}
function e($v){return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');}
function is_admin(){
  return session_name()==='ODY_ADMIN_SESSION' && !empty($_SESSION['admin_user']) && $_SESSION['admin_user']['role']==='admin' && current_session_valid('admin');
}
function is_member(){
  return session_name()==='ODY_MEMBER_SESSION' && !empty($_SESSION['member_user']) && $_SESSION['member_user']['role']==='member' && current_session_valid('member');
}
function current_session_valid($role){
  global $pdo;
  if (($role==='admin' && session_name()!=='ODY_ADMIN_SESSION') || ($role==='member' && session_name()!=='ODY_MEMBER_SESSION')) return false;
  $token = $_SESSION[$role.'_session_token'] ?? '';
  if (!$token || empty($_SESSION[$role.'_user']['id'])) return false;
  $stmt=$pdo->prepare("SELECT ls.id,ls.revoked_at,u.status,u.role FROM login_sessions ls JOIN users u ON u.id=ls.user_id WHERE ls.session_token=? AND ls.user_id=? AND ls.role=? LIMIT 1");
  $stmt->execute([$token,(int)$_SESSION[$role.'_user']['id'],$role]); $row=$stmt->fetch();
  if (!$row || $row['revoked_at'] !== null || $row['role'] !== $role || ($row['status'] ?? 'active')!=='active') {
    unset($_SESSION[$role.'_user'],$_SESSION[$role.'_session_token']);
    return false;
  }
  $pdo->prepare('UPDATE login_sessions SET last_seen=NOW() WHERE id=?')->execute([(int)$row['id']]);
  return true;
}
function require_admin(){

  if(!current_session_valid('admin')){
    header('Location: /odynasties/');
    exit;
  }
  static $logged = false;
  if (!$logged && $_SERVER['REQUEST_METHOD']==='POST') {
    $action = trim((string)($_POST['action'] ?? 'POST'));
    $safe = [];
    foreach (['id','user_id','session_id','title','status','category'] as $k) {
      if (isset($_POST[$k])) $safe[$k] = is_array($_POST[$k]) ? '' : substr((string)$_POST[$k], 0, 180);
    }
    audit_admin_action('Admin action: '.$action, json_encode($safe, JSON_UNESCAPED_SLASHES));
    $logged = true;
  }
}
function require_member(){if(!current_session_valid('member')){header('Location: /odynasties/login.php');exit;}}

function audit_admin_action($action, $details=''){
  global $pdo;
  if (!is_admin()) return;
  $admin = $_SESSION['admin_user'] ?? [];
  $ip = $_SERVER['REMOTE_ADDR'] ?? '';
  try {
    $s = $pdo->prepare("INSERT INTO admin_activity_logs(admin_id,admin_name,action,details,ip_address) VALUES(?,?,?,?,?)");
    $s->execute([(int)($admin['id'] ?? 0), (string)($admin['name'] ?? ''), $action, $details, $ip]);
  } catch (Throwable $e) {}
}
function require_login(){
  if (session_name()==='ODY_ADMIN_SESSION') {
    require_admin(); return;
  }
  require_member();
}

function flash($type,$msg){$_SESSION['flash']=[$type,$msg];}
function show_flash(){if(!empty($_SESSION['flash'])){[$t,$m]=$_SESSION['flash'];unset($_SESSION['flash']);echo '<div class="flash '.e($t).'">'.e($m).'</div>';}}
function profile_image_url($filename){return $filename ? '/odynasties/uploads/profiles/'.rawurlencode(basename($filename)) : '/odynasties/assets/images/profile-placeholder.svg';}
current_session_valid(session_name()==='ODY_ADMIN_SESSION'?'admin':'member');
?>
