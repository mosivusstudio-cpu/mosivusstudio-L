<?php
session_start();

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: /admin/index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === 'mosivusstudio@gnail.com' && $password === 'Abhishek@2061') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: /admin/index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mosivus Admin - Login</title>
    <!-- Favicon rules per PART 4 -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#0A1628">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #050A1F;
            font-family: 'Inter', sans-serif;
            color: #F0F4FF;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card {
            background: rgba(61,240,255,0.04);
            backdrop-filter: blur(24px) saturate(200%);
            -webkit-backdrop-filter: blur(24px) saturate(200%);
            border: 1px solid rgba(61,240,255,0.12);
            border-radius: 18px;
            padding: 40px;
            width: 100%;
            max-width: 380px;
            text-align: center;
            box-sizing: border-box;
            box-shadow: 0 8px 32px rgba(0,0,0,0.45), 0 0 60px rgba(61,240,255,0.03), inset 0 1px 0 rgba(61,240,255,0.08), inset 0 -1px 0 rgba(0,0,0,0.3);
        }
        h2 { margin: 0 0 10px; font-size: 28px; font-weight: 700; font-family: sans-serif; }
        p.subtitle { color: #8B9EC4; font-size: 14px; margin-bottom: 24px; }
        input {
            background: rgba(255,255,255,0.04);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(61,240,255,0.1);
            border-radius: 10px;
            padding: 14px 18px;
            color: #F0F4FF;
            font-size: 15px;
            outline: none;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 16px;
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.04);
        }
        button {
            padding: 14px;
            background: #3DF0FF;
            color: #050A1F;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            width: 100%;
            font-family: sans-serif;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .14em;
            margin-top: 10px;
            transition: all 0.2s;
        }
        button:hover {
            background: #fff;
            box-shadow: 0 0 15px rgba(61,240,255,0.4);
        }
        .error { color: #f87171; font-size: 13px; margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Mosivus Admin</h2>
        <p class="subtitle">Enter credentials to continue</p>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <?php if($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <button type="submit">ACCESS DASHBOARD</button>
        </form>
    </div>
</body>
</html>
