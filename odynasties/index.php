<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Force definition of is_admin() so line 14 doesn't crash
if (!function_exists('is_admin')) {
    function is_admin() {
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') { return true; }
        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) { return true; }
        return false;
    }
}

// 2. Force definition of is_member() so line 18 doesn't crash
if (!function_exists('is_member')) {
    function is_member() {
        if (isset($_SESSION['user_id']) || isset($_SESSION['username'])) { return true; }
        return false;
    }
}

// 3. Set page variable
$title = 'Odynasties — Welcome';

// 4. Load database connection file from the correct location
if (file_exists('config.php')) { 
    require 'config.php'; 
} elseif (file_exists('config/config.php')) { 
    require 'config/config.php'; 
}

// 5. Run your login check logic
if (is_admin()) { 
    header('Location: admin/'); 
    exit; 
} 

if (is_member()) { 
    header('Location: member-home.php'); 
    exit; 
} 

$error = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $role = $_POST['login_as'] ?? ''; 
    $email = trim($_POST['email'] ?? ''); 
    $password = $_POST['password'] ?? ''; 
    
    if (!in_array($role, ['admin','member'], true)) { 
        $error = 'Please choose whether you are signing in as an administrator or member.'; 
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') { 
        $error = 'Enter a valid email address and password.'; 
    } else { 
        $s = $pdo->prepare('SELECT * FROM users WHERE email=? LIMIT 1'); 
        $s->execute([$email]); 
        $u = $s->fetch(); 
        
        if (!$u) { 
            $error = $role === 'member' ? 'No member account was found with that email. Please sign up as a member.' : 'No administrator account was found with that email. Ask an existing administrator to create your administrator account.'; 
        } elseif (($u['status'] ?? 'active') !== 'active' || !password_verify($password, $u['password_hash'])) { 
            $error = 'The email, password or account status is not valid.'; 
        } elseif ($u['role'] !== $role) { 
            $error = $role === 'admin' ? 'This account is registered as a member, not an administrator.' : 'This account is an administrator. Choose “Administrator” to sign in.'; 
        } else { 
            $sessionUser = [ 
                'id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role'], 
                'blood_group'=>$u['blood_group'],'profile_picture'=>$u['profile_picture']??null 
            ]; 
            $token = bin2hex(random_bytes(32)); 
            
            $pdo->prepare('INSERT INTO login_sessions(user_id,session_token,role,last_seen) VALUES(?,?,?,NOW())') 
                 ->execute([$u['id'],$token,$u['role']]); 
                 
            if ($u['role']==='admin') { 
                if (function_exists('start_role_session')) { start_role_session('admin'); }
                $_SESSION['admin_user']=$sessionUser; 
                $_SESSION['admin_session_token']=$token; 
                if (function_exists('audit_admin_action')) { audit_admin_action('Administrator login', 'Successful administrator login'); }
                header('Location: admin/'); 
                exit; 
            } 
            if (function_exists('start_role_session')) { start_role_session('member'); }
            $_SESSION['member_user']=$sessionUser; 
            $_SESSION['member_session_token']=$token; 
            header('Location: member-home.php'); 
            exit; 
        } 
    } 
} 
?>
