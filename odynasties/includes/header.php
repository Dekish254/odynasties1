<?php require_once __DIR__.'/../config/config.php'; ?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= e($title ?? 'Odynasties') ?></title><link rel="stylesheet" href="/odynasties/assets/css/style.css"></head><body>
<header class="top"><div class="container nav"><a class="brand" href="/odynasties/"><img src="/odynasties/assets/images/logo.svg" alt="Odynasties"> <span>Odynasties<small>ONE BLOOD GROUP. ONE COMMUNITY. ONE DYNASTY.</small></span></a><button class="menu" onclick="document.body.classList.toggle('open')">☰</button><nav>
<?php if(is_admin()): ?>
  <a href="/odynasties/admin/">Dashboard</a>
  <a href="/odynasties/admin/members.php">Members</a>
  <a href="/odynasties/admin/online-users.php">Online Users</a>
  <a href="/odynasties/admin/administrators.php">Administrators</a>
  <a href="/odynasties/admin/reports.php">Reports</a>
  <a href="/odynasties/admin/support.php">Support Requests</a>
  <a href="/odynasties/admin/donations.php">Donations</a>
  <a href="/odynasties/admin/events.php">Events</a>
  <a href="/odynasties/admin/news.php">News</a>
  <a href="/odynasties/admin/messages.php">Messages</a>
  <a href="/odynasties/logout.php?role=admin">Logout</a>
<?php elseif(is_member()): ?>
  <a href="/odynasties/about.php">About</a>
  <a href="/odynasties/members.php">Members</a>
  <a href="/odynasties/donate.php">Blood Donation</a>
  <a href="/odynasties/support.php">Support Requests</a>
  <a href="/odynasties/events.php">Events</a>
  <a href="/odynasties/news.php">News</a>
  <a href="/odynasties/contact.php">Contact</a>
  <a href="/odynasties/profile.php">My Profile</a>
  <a href="/odynasties/logout.php?role=member">Logout</a>
<?php else: ?>
  <a href="/odynasties/">Login</a>
  <a class="join" href="/odynasties/register.php">Join Now</a>
<?php endif; ?></nav></div></header>
<?php show_flash(); ?>
