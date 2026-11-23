<?php
function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
function redirect($url){ header("Location: $url"); exit; }
function flash($msg, $type='success'){ $_SESSION['flash'] = [$msg,$type]; }
function show_flash(){
  if (!empty($_SESSION['flash'])) {
    [$m,$t] = $_SESSION['flash']; unset($_SESSION['flash']);
    echo "<div class='alert alert-$t'>".e($m)."</div>";
  }
}
function require_login(){ if (empty($_SESSION['user_id'])) redirect('login.php'); }
function require_admin(){
  if (($_SESSION['role'] ?? '') !== 'admin') redirect('../login.php');
}
function csrf(){ if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function check_csrf(){ if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) die('Invalid CSRF token'); }