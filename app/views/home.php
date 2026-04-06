<?php
/** @var $this \core\View */
?>

<section class="hero">
    <div class="hero-content">
        <span class="badge">Version 1.0 -- Now Public</span>
        <h1>Build Fast.<br><span class="gradient-text">Scale Limitlessly.</span></h1>
        <p class="subtitle">
            NexaPHP is a next-generation PHP framework designed for modern web applications. 
            Lightweight, flexible, and built with a developer-first mindset.
        </p>
        <div class="hero-actions">
            <a href="#" class="cta-btn primary">Get Started Free</a>
            <a href="https://github.com/nexaphp/framework" class="cta-btn secondary">View Documentation</a>
        </div>
    </div>
</section>

<section id="features" class="features-wrapper">
    <h2 class="section-title">Built for Performance</h2>
    <div class="features-grid">
        <div class="feature-card glass-card">
            <div class="icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
            </div>
            <h3>Performance First</h3>
            <p>Built for speed with minimal overhead. Every millisecond counts in modern web applications.</p>
        </div>
        <div class="feature-card glass-card">
            <div class="icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
            </div>
            <h3>Full Flexibility</h3>
            <p>No rigid rules. Build your architecture exactly how you want it, with power when you need it.</p>
        </div>
        <div class="feature-card glass-card">
            <div class="icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
            <h3>Secure by Default</h3>
            <p>Enterprise-grade headers, strict typing, and hardened database core out of the box.</p>
        </div>
    </div>
</section>

<section class="code-preview-wrapper">
    <div class="code-preview glass-card">
        <div class="code-header">
            <div class="dots">
                <span class="dot red"></span>
                <span class="dot yellow"></span>
                <span class="dot green"></span>
            </div>
            <span class="file-name">routes/web.php</span>
        </div>
<pre><code>
$router->get('/api/users', $authMid, function($req, $res) {
    $users = db()->fetchAll("SELECT * FROM users");
    return $res->status(200)->json($users);
});
</code></pre>
    </div>
</section>

<style>
    .badge {
        display: inline-block;
        background: rgba(108, 59, 255, 0.15);
        color: var(--primary);
        padding: 0.5rem 1.25rem;
        border-radius: 100px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 2rem;
        border: 1px solid rgba(108, 59, 255, 0.2);
    }

    .hero {
        text-align: center;
        padding: 8rem 0 6rem;
        background: radial-gradient(circle at center, rgba(108, 59, 255, 0.15) 0%, transparent 70%);
    }

    .hero h1 {
        font-size: 5rem;
        line-height: 1.1;
        margin-bottom: 2rem;
        letter-spacing: -2px;
    }

    .gradient-text {
        background: linear-gradient(135deg, var(--primary), var(--accent));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .subtitle {
        font-size: 1.35rem;
        color: var(--gray);
        max-width: 800px;
        margin: 0 auto 3.5rem;
    }

    .hero-actions {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
    }

    .cta-btn.secondary {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--glass);
        color: white !important;
    }

    .cta-btn.secondary:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    .features-wrapper {
        padding: 8rem 0;
    }

    .section-title {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 4rem;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }

    .feature-card {
        padding: 3rem 2rem;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.4s;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }

    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4), 0 0 20px rgba(108, 59, 255, 0.1);
        border-color: rgba(108, 59, 255, 0.3);
    }

    .icon-box {
        width: 60px;
        height: 60px;
        background: rgba(108, 59, 255, 0.1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        margin-bottom: 2rem;
        border: 1px solid rgba(108, 59, 255, 0.2);
        transition: transform 0.3s;
    }

    .feature-card:hover .icon-box {
        background: var(--primary);
        color: white;
        transform: scale(1.1);
    }

    .icon-box svg {
        width: 28px;
        height: 28px;
    }

    .feature-card h3 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: var(--light);
    }

    .feature-card p {
        color: var(--gray);
        font-size: 1rem;
        line-height: 1.6;
    }

    .code-preview-wrapper {
        padding: 4rem 0;
    }

    .code-preview {
        padding: 0;
        overflow: hidden;
        max-width: 900px;
        margin: 0 auto;
        box-shadow: 0 30px 60px rgba(0,0,0,0.5);
    }

    .code-header {
        background: rgba(255, 255, 255, 0.05);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--glass);
    }

    .dots {
        display: flex;
        gap: 8px;
    }

    .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }

    .file-name {
        font-size: 0.85rem;
        color: var(--gray);
        font-family: 'Source Code Pro', monospace;
    }

    .dot.red { background: #ff5f56; }
    .dot.yellow { background: #ffbd2e; }
    .dot.green { background: #27c93f; }

    code {
        display: block;
        padding: 2.5rem;
        font-family: 'Source Code Pro', 'Cascadia Code', monospace;
        color: #e0e0e0;
        font-size: 1.15rem;
        overflow-x: auto;
    }

    @media (max-width: 768px) {
        .hero { padding: 4rem 1rem; }
        .hero h1 { font-size: 3rem; margin-bottom: 2.5rem; }
        .subtitle { font-size: 1.15rem; margin-bottom: 4rem; }
        
        .hero-actions {
            flex-direction: column;
            gap: 1rem;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .cta-btn {
            width: 100%;
            text-align: center;
        }

        .feature-card {
            align-items: center;
            text-align: center;
        }

        .section-title {
            font-size: 2rem;
            margin-bottom: 3rem;
        }

        code {
            font-size: 1rem;
            padding: 1.5rem;
        }
    }
</style>
