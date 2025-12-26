<?php
session_start();

// Initialize variables with default values to avoid undefined warnings
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Check if form was submitted and both fields have values
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($name) && !empty($password)) {
    $server = 'localhost';
    $userid2 = 'root';
    $password2 = '';
    $dbname = 'kovos';

    $conn = mysqli_connect($server, $userid2, $password2, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Use prepared statements to prevent SQL injection
    $stmt = mysqli_prepare($conn, "SELECT name, password FROM admin WHERE name = ?");
    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $dbusername = $row['name'];
        $dbpassword = $row['password'];

        // Fixed: Use $name instead of undefined $username
        if ($name == $dbusername && $password == $dbpassword) {
            $_SESSION['name'] = $name;
            header('Location: http://localhost/KoVoS/admin/dashboard.php');
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Invalid username or password.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KoVoS Portal - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            position: relative;
            overflow-x: hidden;
        }

        /* Geometric background elements */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 60%;
            height: 100%;
            background: linear-gradient(135deg, rgba(108, 117, 125, 0.1) 0%, rgba(173, 181, 189, 0.05) 100%);
            clip-path: polygon(30% 0%, 100% 0%, 100% 100%, 0% 100%);
            z-index: -1;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50%;
            height: 40%;
            background: linear-gradient(45deg, rgba(108, 117, 125, 0.08) 0%, transparent 70%);
            clip-path: polygon(0% 100%, 100% 100%, 0% 0%);
            z-index: -1;
        }

        .header {
            padding: 2rem 3rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 4px solid rgba(0, 0, 0, 0.1);
        }

        .Logo {
            display: flex;
            align-items: center;
            gap: 80px;
            justify-content: center;
        }

        .Logo img {
            height: 90px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        /* Make KoVoS and KVS logos 1.2x bigger */
        .logo-kovos,
        .logo-kvs {
            transform: scale(1.6);
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 4rem 2rem;
            position: relative;
        }

        .portal-title {
            font-size: 3.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4rem;
            letter-spacing: -1px;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease-out;
            border-bottom: 3px solid rgba(22, 28, 34, 0.4);
            padding-bottom: 1rem;
            display: inline-block;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            padding: 3rem 4rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 500px;
            width: 100%;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border: 1px solid #f5c6cb;
            text-align: center;
        }

        .form-group {
            margin-bottom: 2rem;
            text-align: left;
        }

        .form-label {
            display: block;
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.5rem;
            font-size: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: rgb(49, 54, 59);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            background: white;
        }

        .form-input:hover {
            border-color: #6c757d;
        }

        .login-actions {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 2.5rem;
        }

        .login-btn {
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
            color: white;
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(45, 50, 55, 0.4);
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(45, 50, 55, 0.4);
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
        }

        .login-btn:active {
            transform: translateY(-1px);
        }

        .footer {
            background: rgba(52, 58, 64, 0.95);
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            padding: 1.5rem;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Accessibility */
        .login-btn:focus {
            outline: 3px solid linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
            outline-offset: 2px;
        }


        /* Logo placeholder styles */
        .logo-placeholder {
            width: 120px;
            height: 60px;
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 0.8rem;
            text-align: center;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="Logo">
            <img src="image/Logo_BPLTV.png" alt="Logo BPLTV"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="logo-placeholder" style="display: none;">BPLTV Logo</div>

            <img src="image/Logo_KoVoS.png" alt="Logo KoVoS" class="logo-kovos"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="logo-placeholder" style="display: none;">KoVoS Logo</div>

            <img src="image/Logo_KVS.png" alt="Kolej Vokasional" class="logo-kvs"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="logo-placeholder" style="display: none;">KVS Logo</div>
        </div>
    </header>

    <main class="main-content">
        <h1 class="portal-title">KoVoS Portal</h1>

        <div class="login-container">
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form class="login-form" method="POST" action="">
                <div class="form-group">
                    <label for="name" class="form-label">Name :</label>
                    <input type="text" id="name" name="name" class="form-input"
                        value="<?php echo htmlspecialchars($name); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password :</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>

                <div class="login-actions">
                    <button type="submit" class="login-btn">Login</button>
                </div>
            </form>
        </div>
    </main>

    <footer class="footer">
        Copyright ©2025 KoVoS [406400-X]. All rights reserved.
    </footer>
</body>

</html>