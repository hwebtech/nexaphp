<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= env('APP_NAME', 'NexaPHP') ?> -- Build Fast. Scale Limitlessly.</title>
    <!-- Google Fonts: Inter & Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Outfit:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6C3BFF;
            --dark: #0F0F0F;
            --dark-elevated: #1A1A1A;
            --light: #FFFFFF;
            --accent: #3BA1FF;
            --gray: #A0A0A0;
            --glass: rgba(255, 255, 255, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark);
            color: var(--light);
            line-height: 1.6;
            overflow-x: hidden;
        }

        body.menu-open {
            overflow: hidden;
        }

        h1, h2, h3 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 0 2rem;
        }

        nav {
            padding: 1.5rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--glass);
            position: relative;
            z-index: 1001;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(to right, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            z-index: 1002;
        }

        .nav-links {
            display: flex;
            align-items: center;
        }

        .nav-links a {
            color: var(--gray);
            text-decoration: none;
            margin-left: 2rem;
            transition: color 0.3s;
            font-size: 0.9rem;
        }

        .nav-links a:hover {
            color: var(--light);
        }

        /* Sidebar Menu Styles */
        .sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            width: 300px;
            height: 100%;
            background: var(--dark-elevated);
            z-index: 1000;
            padding: 6rem 2rem 2rem;
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: -10px 0 30px rgba(0,0,0,0.5);
            border-left: 1px solid var(--glass);
        }

        .sidebar.active {
            right: 0;
        }

        .sidebar-links {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .sidebar-links a {
            color: var(--light);
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(5px);
            z-index: 999;
            display: none;
        }

        .overlay.active {
            display: block;
        }

        /* Hamburger Styles */
        .menu-btn {
            display: none;
            flex-direction: column;
            gap: 6px;
            cursor: pointer;
            z-index: 1002;
            padding: 10px;
        }

        .menu-btn span {
            width: 25px;
            height: 2px;
            background: var(--light);
            transition: 0.3s;
        }

        .menu-btn.active span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
        .menu-btn.active span:nth-child(2) { opacity: 0; }
        .menu-btn.active span:nth-child(3) { transform: rotate(-45deg) translate(7px, -6px); }

        .cta-btn {
            background: var(--primary);
            color: #FFFFFF !important; /* Force Clear White Text */
            padding: 0.8rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .cta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(108, 59, 255, 0.3);
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .menu-btn { display: flex; }
        }

        footer {
            margin-top: 4rem;
            padding: 2rem 0;
            border-top: 1px solid var(--glass);
            text-align: center;
            color: var(--gray);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="overlay" id="overlay"></div>
    
    <div class="sidebar" id="sidebar">
        <div class="sidebar-links">
            <a href="#features" onclick="toggleMenu()">Features</a>
            <a href="https://github.com/nexaphp/framework" onclick="toggleMenu()">Docs</a>
            <a href="#" class="cta-btn" onclick="toggleMenu()">Get Started</a>
        </div>
    </div>

    <div class="container">
        <nav>
            <a href="/" class="logo">NexaPHP</a>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="https://github.com/nexaphp/framework" target="_blank">Docs</a>
                <a href="#" class="cta-btn">Get Started</a>
            </div>
            <div class="menu-btn" id="menuBtn" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>

        <main>
            {{content}}
        </main>

        <footer>
            &copy; <?= date('Y') ?> NexaPHP Framework. Build faster. Scale smarter.
        </footer>
    </div>

    <script>
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const menuBtn = document.getElementById('menuBtn');
            const body = document.body;

            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            menuBtn.classList.toggle('active');
            body.classList.toggle('menu-open');
        }

        document.getElementById('overlay').onclick = toggleMenu;
    </script>
</body>
</html>
