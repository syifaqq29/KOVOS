<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KoVoS - Know. Verify. Secure.</title>
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

        .kovos-title {
            font-size: 10rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
            letter-spacing: -2px;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease-out;
        }

        .tagline {
            font-size: 2.5rem;
            color: #6c757d;
            margin-bottom: 4rem;
            font-weight: 300;
            letter-spacing: 1px;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .action-buttons {
            display: flex;
            gap: 2rem;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .btn {
            display: inline-block;
            padding: 1.2rem 3rem;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-admin {
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
            color: white;
            box-shadow: 0 4px 15px rgba(217, 221, 225, 0.3);
        }

        .btn-admin:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(22, 28, 34, 0.4);
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
        }

        .btn-user {
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
            color: white;
            box-shadow: 0 4px 15px rgba(217, 221, 225, 0.3);
        }

        .btn-user:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(22, 28, 34, 0.4);
            background: linear-gradient(135deg, rgb(83, 93, 103), rgb(68, 72, 76));
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
        <h1 class="kovos-title">KoVoS</h1>
        <p class="tagline">"Know. Verify. Secure."</p>

        <section class="action-buttons">
            <a href="admin/login.php" class="btn btn-admin">Admin</a>
            <a href="user/search.php" class="btn btn-user">User</a>
        </section>
    </main>

    <footer class="footer">
        Copyright ©2025 KoVoS [406400-X]. All rights reserved.
    </footer>
</body>

</html>