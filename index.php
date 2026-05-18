<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>UtilityTrack — Enterprise Resource System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    :root {
      --navy: #001F54;
      --blue: #0056b3;
      --light-blue: #4fc3f7;
      --accent: #27ae60;
      --bg: #f8fafc;
      --text: #1e293b;
      --muted: #64748b;
      --border: #e2e8f0;
      --white: #ffffff;
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    html { scroll-behavior:smooth; }
    body { font-family:'Segoe UI',system-ui,sans-serif; background:var(--bg); color:var(--text); }

    /* ── NAV ── */
    nav {
      position:fixed; top:0; left:0; right:0; z-index:1000;
      background:rgba(0,31,84,0.97); backdrop-filter:blur(10px);
      display:flex; align-items:center; justify-content:space-between;
      padding:0 5%; height:64px;
      box-shadow:0 2px 20px rgba(0,0,0,0.2);
    }
    .nav-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
    .nav-brand .logo-icon {
      width:36px; height:36px; background:var(--light-blue);
      border-radius:8px; display:flex; align-items:center; justify-content:center;
      font-size:1.1rem; color:var(--navy);
    }
    .nav-brand span { font-size:1.2rem; font-weight:700; color:#fff; }
    .nav-links { display:flex; align-items:center; gap:8px; }
    .nav-links a {
      color:rgba(255,255,255,0.8); text-decoration:none;
      padding:8px 14px; border-radius:8px; font-size:0.9rem;
      transition:background .2s, color .2s;
    }
    .nav-links a:hover { background:rgba(255,255,255,0.1); color:#fff; }
    .nav-links .btn-nav {
      background:var(--light-blue); color:var(--navy);
      font-weight:700; padding:8px 20px;
    }
    .nav-links .btn-nav:hover { background:#81d4fa; }
    .nav-toggle { display:none; background:none; border:none; color:#fff; font-size:1.4rem; cursor:pointer; }

    /* ── HERO ── */
    .hero {
      min-height:100vh;
      background: linear-gradient(135deg, var(--navy) 0%, #0a3a8a 50%, #1565c0 100%);
      display:flex; align-items:center; justify-content:center;
      text-align:center; padding:100px 5% 60px;
      position:relative; overflow:hidden;
    }
    .hero::before {
      content:''; position:absolute; inset:0;
      background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .hero-content { position:relative; max-width:700px; }
    .hero-badge {
      display:inline-flex; align-items:center; gap:8px;
      background:rgba(79,195,247,0.15); border:1px solid rgba(79,195,247,0.3);
      color:var(--light-blue); padding:6px 16px; border-radius:20px;
      font-size:0.82rem; font-weight:600; margin-bottom:24px;
    }
    .hero h1 {
      font-size:clamp(2.2rem, 5vw, 3.5rem);
      font-weight:800; color:#fff; line-height:1.15;
      margin-bottom:20px;
    }
    .hero h1 span { color:var(--light-blue); }
    .hero p {
      font-size:1.1rem; color:rgba(255,255,255,0.75);
      line-height:1.7; margin-bottom:36px; max-width:560px; margin-left:auto; margin-right:auto;
    }
    .hero-btns { display:flex; gap:14px; justify-content:center; flex-wrap:wrap; }
    .btn-hero-primary {
      background:var(--light-blue); color:var(--navy);
      padding:14px 32px; border-radius:10px; font-size:1rem;
      font-weight:700; text-decoration:none; transition:transform .2s, box-shadow .2s;
      display:inline-flex; align-items:center; gap:8px;
    }
    .btn-hero-primary:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(79,195,247,0.4); }
    .btn-hero-secondary {
      background:rgba(255,255,255,0.1); color:#fff;
      padding:14px 32px; border-radius:10px; font-size:1rem;
      font-weight:600; text-decoration:none; border:1px solid rgba(255,255,255,0.25);
      transition:background .2s; display:inline-flex; align-items:center; gap:8px;
    }
    .btn-hero-secondary:hover { background:rgba(255,255,255,0.18); }
    .hero-stats {
      display:flex; gap:40px; justify-content:center; margin-top:56px;
      flex-wrap:wrap;
    }
    .hero-stat .num { font-size:2rem; font-weight:800; color:var(--light-blue); }
    .hero-stat .lbl { font-size:0.8rem; color:rgba(255,255,255,0.6); margin-top:2px; }

    /* ── SECTIONS ── */
    section { padding:80px 5%; }
    .section-label {
      display:inline-block; background:#e8f4fd; color:var(--blue);
      padding:4px 14px; border-radius:20px; font-size:0.78rem;
      font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:14px;
    }
    .section-title { font-size:clamp(1.6rem,3vw,2.2rem); font-weight:800; color:var(--text); margin-bottom:12px; }
    .section-sub { font-size:1rem; color:var(--muted); max-width:560px; line-height:1.7; }
    .text-center { text-align:center; }
    .text-center .section-sub { margin:0 auto; }

    /* ── FEATURES ── */
    #features { background:#fff; }
    .features-grid {
      display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr));
      gap:24px; margin-top:48px;
    }
    .feature-card {
      background:var(--bg); border-radius:16px; padding:28px 24px;
      border:1px solid var(--border); transition:transform .2s, box-shadow .2s;
    }
    .feature-card:hover { transform:translateY(-4px); box-shadow:0 12px 30px rgba(0,0,0,0.08); }
    .feature-icon {
      width:52px; height:52px; border-radius:12px;
      display:flex; align-items:center; justify-content:center;
      font-size:1.3rem; margin-bottom:18px;
    }
    .feature-card h3 { font-size:1rem; font-weight:700; margin-bottom:8px; }
    .feature-card p { font-size:0.88rem; color:var(--muted); line-height:1.6; }

    /* ── ABOUT ── */
    #about { background:var(--bg); }
    .about-grid { display:grid; grid-template-columns:1fr 1fr; gap:60px; align-items:center; margin-top:48px; }
    .about-visual {
      background:linear-gradient(135deg,var(--navy),#1565c0);
      border-radius:20px; padding:40px; color:#fff;
      display:flex; flex-direction:column; gap:20px;
    }
    .about-visual .av-item { display:flex; align-items:center; gap:16px; }
    .about-visual .av-icon {
      width:44px; height:44px; background:rgba(255,255,255,0.15);
      border-radius:10px; display:flex; align-items:center; justify-content:center;
      font-size:1.1rem; flex-shrink:0;
    }
    .about-visual .av-text .av-title { font-weight:700; font-size:0.95rem; }
    .about-visual .av-text .av-desc { font-size:0.8rem; color:rgba(255,255,255,0.65); margin-top:2px; }
    .about-text p { font-size:0.95rem; color:var(--muted); line-height:1.8; margin-bottom:16px; }
    .about-text ul { list-style:none; padding:0; }
    .about-text ul li {
      display:flex; align-items:center; gap:10px;
      font-size:0.9rem; color:var(--text); padding:6px 0;
    }
    .about-text ul li i { color:var(--accent); }

    /* ── SECURITY ── */
    #security { background:#fff; }
    .security-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:20px; margin-top:48px; }
    .security-card {
      border-radius:14px; padding:24px; border:1px solid var(--border);
      display:flex; flex-direction:column; gap:12px;
    }
    .security-card .sec-icon {
      width:44px; height:44px; border-radius:10px;
      display:flex; align-items:center; justify-content:center; font-size:1.1rem;
    }
    .security-card h4 { font-size:0.95rem; font-weight:700; }
    .security-card p { font-size:0.83rem; color:var(--muted); line-height:1.6; }

    /* ── PRIVACY ── */
    #privacy { background:var(--bg); }
    .privacy-content { max-width:760px; margin:40px auto 0; }
    .privacy-item { margin-bottom:32px; }
    .privacy-item h3 {
      font-size:1rem; font-weight:700; color:var(--text);
      display:flex; align-items:center; gap:10px; margin-bottom:10px;
    }
    .privacy-item h3 i { color:var(--blue); }
    .privacy-item p { font-size:0.9rem; color:var(--muted); line-height:1.8; }

    /* ── CTA ── */
    .cta-section {
      background:linear-gradient(135deg,var(--navy),#1565c0);
      text-align:center; padding:80px 5%;
    }
    .cta-section h2 { font-size:2rem; font-weight:800; color:#fff; margin-bottom:14px; }
    .cta-section p { color:rgba(255,255,255,0.75); font-size:1rem; margin-bottom:32px; }

    /* ── FOOTER ── */
    footer {
      background:var(--navy); color:rgba(255,255,255,0.6);
      padding:40px 5%; text-align:center;
    }
    footer .footer-brand { font-size:1.1rem; font-weight:700; color:#fff; margin-bottom:12px; }
    footer .footer-links { display:flex; gap:20px; justify-content:center; flex-wrap:wrap; margin-bottom:20px; }
    footer .footer-links a { color:rgba(255,255,255,0.6); text-decoration:none; font-size:0.85rem; }
    footer .footer-links a:hover { color:#fff; }
    footer .footer-copy { font-size:0.8rem; }

    @media(max-width:768px) {
      .about-grid { grid-template-columns:1fr; }
      .nav-links { display:none; flex-direction:column; position:absolute; top:64px; left:0; right:0; background:var(--navy); padding:16px; gap:4px; }
      .nav-links.open { display:flex; }
      .nav-toggle { display:block; }
    }
  </style>
</head>
<body>

<!-- NAV -->
<nav>
  <a class="nav-brand" href="index.php">
    <div class="logo-icon"><i class="fas fa-bolt"></i></div>
    <span>UtilityTrack</span>
  </a>
  <div class="nav-links" id="navLinks">
    <a href="#features">Features</a>
    <a href="#about">About</a>
    <a href="#security">Security</a>
    <a href="#privacy">Privacy</a>
    <a href="login.php" class="btn-nav"><i class="fas fa-sign-in-alt"></i> Login</a>
  </div>
  <button class="nav-toggle" id="navToggle"><i class="fas fa-bars"></i></button>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-content">
    <div class="hero-badge"><i class="fas fa-shield-alt"></i> Trusted Enterprise Platform</div>
    <h1>Manage Your Operations with <span>UtilityTrack</span></h1>
    <p>A complete enterprise resource system for managing work orders, personnel, tasks, and reports — all in one secure, modern platform.</p>
    <div class="hero-btns">
      <a href="login.php" class="btn-hero-primary"><i class="fas fa-sign-in-alt"></i> Get Started</a>
      <a href="#about" class="btn-hero-secondary"><i class="fas fa-info-circle"></i> Learn More</a>
    </div>
    <div class="hero-stats">
      <div class="hero-stat"><div class="num">100%</div><div class="lbl">Secure & Encrypted</div></div>
      <div class="hero-stat"><div class="num">Real-time</div><div class="lbl">Notifications</div></div>
      <div class="hero-stat"><div class="num">Full</div><div class="lbl">Audit Trail</div></div>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section id="features">
  <div class="text-center">
    <span class="section-label">Features</span>
    <h2 class="section-title">Everything you need to run operations</h2>
    <p class="section-sub">From work order management to personnel tracking, UtilityTrack covers all your enterprise needs.</p>
  </div>
  <div class="features-grid">
    <div class="feature-card">
      <div class="feature-icon" style="background:#e8f4fd;color:#2980b9;"><i class="fas fa-clipboard-list"></i></div>
      <h3>Work Order Management</h3>
      <p>Create, assign, and track work orders from start to finish with real-time status updates and due date tracking.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#e8f8f0;color:#27ae60;"><i class="fas fa-users"></i></div>
      <h3>Personnel Management</h3>
      <p>Manage your team — add, edit, activate or deactivate personnel with role-based access control.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#fef9e7;color:#f39c12;"><i class="fas fa-tasks"></i></div>
      <h3>Task Assignment</h3>
      <p>Assign tasks to personnel, monitor progress, and get notified when tasks are completed.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#fdf2f8;color:#8e44ad;"><i class="fas fa-chart-bar"></i></div>
      <h3>Reports & Analytics</h3>
      <p>Submit and review reports with date tracking. Admins can manage and delete reports as needed.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#fef5ec;color:#e67e22;"><i class="fas fa-bell"></i></div>
      <h3>Real-time Notifications</h3>
      <p>Stay informed with instant bell notifications for task assignments, work order updates, and system alerts.</p>
    </div>
    <div class="feature-card">
      <div class="feature-icon" style="background:#fdf2f2;color:#e74c3c;"><i class="fas fa-history"></i></div>
      <h3>Audit Logging</h3>
      <p>Every action is logged with timestamps and user attribution for full accountability and compliance.</p>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section id="about">
  <div class="about-grid">
    <div class="about-visual">
      <h2 style="font-size:1.4rem;font-weight:800;margin-bottom:8px;">Built for Enterprise</h2>
      <p style="font-size:0.85rem;color:rgba(255,255,255,0.65);margin-bottom:16px;">UtilityTrack is designed to streamline operations for organizations of all sizes.</p>
      <div class="av-item">
        <div class="av-icon"><i class="fas fa-bolt"></i></div>
        <div class="av-text">
          <div class="av-title">Fast & Responsive</div>
          <div class="av-desc">Works on desktop, tablet, and mobile</div>
        </div>
      </div>
      <div class="av-item">
        <div class="av-icon"><i class="fas fa-lock"></i></div>
        <div class="av-text">
          <div class="av-title">Secure by Design</div>
          <div class="av-desc">Password hashing, session management, CSRF protection</div>
        </div>
      </div>
      <div class="av-item">
        <div class="av-icon"><i class="fas fa-users-cog"></i></div>
        <div class="av-text">
          <div class="av-title">Role-Based Access</div>
          <div class="av-desc">Separate admin and user workflows</div>
        </div>
      </div>
      <div class="av-item">
        <div class="av-icon"><i class="fas fa-database"></i></div>
        <div class="av-text">
          <div class="av-title">Reliable Data Storage</div>
          <div class="av-desc">MySQL with full relational integrity</div>
        </div>
      </div>
    </div>
    <div class="about-text">
      <span class="section-label">About Us</span>
      <h2 class="section-title">What is UtilityTrack?</h2>
      <p>UtilityTrack is an enterprise resource planning (ERP) system built to help organizations manage their day-to-day operations efficiently. From tracking work orders to managing personnel and monitoring tasks, everything is centralized in one platform.</p>
      <p>Our system provides separate, secure portals for administrators and regular users, ensuring that each person sees only what they need to do their job effectively.</p>
      <ul>
        <li><i class="fas fa-check-circle"></i> Centralized work order and task management</li>
        <li><i class="fas fa-check-circle"></i> Real-time notifications and alerts</li>
        <li><i class="fas fa-check-circle"></i> Full audit trail for compliance</li>
        <li><i class="fas fa-check-circle"></i> Secure role-based access control</li>
        <li><i class="fas fa-check-circle"></i> Feedback and reporting system</li>
        <li><i class="fas fa-check-circle"></i> Mobile-responsive design</li>
      </ul>
    </div>
  </div>
</section>

<!-- SECURITY -->
<section id="security">
  <div class="text-center">
    <span class="section-label">Security</span>
    <h2 class="section-title">Your data is protected</h2>
    <p class="section-sub">We take security seriously. UtilityTrack is built with multiple layers of protection to keep your data safe.</p>
  </div>
  <div class="security-grid">
    <div class="security-card">
      <div class="sec-icon" style="background:#e8f4fd;color:#2980b9;"><i class="fas fa-key"></i></div>
      <h4>Password Hashing</h4>
      <p>All passwords are hashed using PHP's <code>PASSWORD_DEFAULT</code> (bcrypt) algorithm. Plain-text passwords are never stored.</p>
    </div>
    <div class="security-card">
      <div class="sec-icon" style="background:#e8f8f0;color:#27ae60;"><i class="fas fa-shield-alt"></i></div>
      <h4>CSRF Protection</h4>
      <p>All forms include CSRF tokens to prevent cross-site request forgery attacks from malicious third-party sites.</p>
    </div>
    <div class="security-card">
      <div class="sec-icon" style="background:#fef9e7;color:#f39c12;"><i class="fas fa-user-lock"></i></div>
      <h4>Session Security</h4>
      <p>Sessions are regenerated on login, destroyed on logout, and validated on every protected page to prevent hijacking.</p>
    </div>
    <div class="security-card">
      <div class="sec-icon" style="background:#fdf2f2;color:#e74c3c;"><i class="fas fa-code"></i></div>
      <h4>SQL Injection Prevention</h4>
      <p>All database queries use prepared statements with parameterized inputs — no raw user input is ever passed to SQL.</p>
    </div>
    <div class="security-card">
      <div class="sec-icon" style="background:#fdf2f8;color:#8e44ad;"><i class="fas fa-filter"></i></div>
      <h4>XSS Prevention</h4>
      <p>All user-generated content is escaped with <code>htmlspecialchars()</code> before being rendered in the browser.</p>
    </div>
    <div class="security-card">
      <div class="sec-icon" style="background:#fef5ec;color:#e67e22;"><i class="fas fa-history"></i></div>
      <h4>Audit Logging</h4>
      <p>Every sensitive action is logged with user ID, action description, and timestamp for accountability and forensics.</p>
    </div>
  </div>
</section>

<!-- PRIVACY -->
<section id="privacy">
  <div class="text-center">
    <span class="section-label">Privacy Policy</span>
    <h2 class="section-title">How we handle your data</h2>
    <p class="section-sub">We are committed to protecting your privacy and being transparent about how your information is used.</p>
  </div>
  <div class="privacy-content">
    <div class="privacy-item">
      <h3><i class="fas fa-database"></i> Data We Collect</h3>
      <p>We collect only the information necessary to operate the system: your username, hashed password, role, and activity logs. We do not collect personal identifying information beyond what you voluntarily provide in your profile (name, email).</p>
    </div>
    <div class="privacy-item">
      <h3><i class="fas fa-cogs"></i> How We Use Your Data</h3>
      <p>Your data is used solely to operate the UtilityTrack platform — to authenticate you, display your tasks and work orders, and maintain audit logs for system integrity. We do not sell, share, or transfer your data to third parties.</p>
    </div>
    <div class="privacy-item">
      <h3><i class="fas fa-lock"></i> Data Storage & Security</h3>
      <p>All data is stored in a secured MySQL database on a local server. Passwords are hashed and never stored in plain text. Access to the database is restricted to authorized system administrators only.</p>
    </div>
    <div class="privacy-item">
      <h3><i class="fas fa-cookie-bite"></i> Sessions & Cookies</h3>
      <p>We use PHP sessions to maintain your login state. Session data is stored server-side and a session cookie is placed in your browser. Sessions are invalidated when you log out or after a period of inactivity.</p>
    </div>
    <div class="privacy-item">
      <h3><i class="fas fa-user-shield"></i> Your Rights</h3>
      <p>You have the right to access, update, or request deletion of your personal data. Contact your system administrator to exercise these rights. Audit logs may be retained for compliance purposes even after account deletion.</p>
    </div>
    <div class="privacy-item">
      <h3><i class="fas fa-envelope"></i> Contact</h3>
      <p>For privacy-related concerns, please contact your system administrator or reach out via the feedback form after logging in.</p>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <h2>Ready to get started?</h2>
  <p>Log in to your account or register to access the UtilityTrack platform.</p>
  <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
    <a href="login.php" class="btn-hero-primary"><i class="fas fa-sign-in-alt"></i> Login</a>
    <a href="register.php" class="btn-hero-secondary"><i class="fas fa-user-plus"></i> Register</a>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-brand"><i class="fas fa-bolt"></i> UtilityTrack</div>
  <div class="footer-links">
    <a href="#features">Features</a>
    <a href="#about">About</a>
    <a href="#security">Security</a>
    <a href="#privacy">Privacy</a>
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
  </div>
  <div class="footer-copy">&copy; <?= date('Y') ?> UtilityTrack. All rights reserved. Built for enterprise operations management.</div>
</footer>

<script>
  document.getElementById('navToggle').addEventListener('click', () => {
    document.getElementById('navLinks').classList.toggle('open');
  });
  // Close mobile nav on link click
  document.querySelectorAll('#navLinks a').forEach(a => {
    a.addEventListener('click', () => document.getElementById('navLinks').classList.remove('open'));
  });
</script>
</body>
</html>
