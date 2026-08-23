<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Rashi') }} — {{ __('messages.app_tagline') }}</title>
    <link rel="icon" href="{{ asset('default-logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root {
    /* Same tokens as the app shell (Electric Indigo / Slate) */
    --color-primary: #3525cd;
    --color-primary-container: #4f46e5;
    --color-on-primary: #ffffff;
    --color-secondary: #505f76;
    --color-secondary-container: #d0e1fb;
    --color-tertiary: #41485e;
    --color-success: #11998e;
    --color-success-container: #d4f4dd;
    --color-on-success-container: #047857;
    --color-warning: #b45309;
    --color-warning-container: #fef3c7;

    --color-background: #f7f9fb;
    --color-surface: #ffffff;
    --color-surface-container-low: #f2f4f6;
    --color-surface-container: #eceef0;
    --color-surface-container-high: #e6e8ea;
    --color-on-surface: #191c1e;
    --color-on-surface-variant: #464555;
    --color-outline: #777587;
    --color-outline-variant: #c7c4d8;
    --color-border: #e2e8f0;

    --radius-card: 16px;
    --radius-btn: 10px;

    --shadow-1: 0px 4px 6px -1px rgba(15, 23, 42, 0.05), 0px 2px 4px -2px rgba(15, 23, 42, 0.05);
    --shadow-2: 0px 10px 15px -3px rgba(15, 23, 42, 0.1);
  }

  * { box-sizing: border-box; }

  body {
    margin: 0;
    background: var(--color-background);
    color: var(--color-on-surface);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.55;
  }
  [dir="rtl"] body { line-height: 1.75; }

  h1, h2, h3 {
    font-weight: 700;
    letter-spacing: -0.01em;
    text-wrap: balance;
    margin: 0;
    color: var(--color-on-surface);
  }

  a { color: inherit; }

  .wrap { max-width: 1120px; margin: 0 auto; padding: 0 28px; }

  .eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 12.5px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--color-primary);
  }
  .eyebrow::before { content: ""; width: 16px; height: 2px; border-radius: 2px; background: var(--color-primary); }

  /* ---------- Nav ---------- */
  nav {
    position: sticky; top: 0; z-index: 40;
    background: rgba(247, 249, 251, 0.85);
    backdrop-filter: blur(8px);
    border-bottom: 1px solid var(--color-outline-variant);
  }
  nav .wrap { display: flex; align-items: center; justify-content: space-between; height: 68px; }
  .brand { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 20px; }
  .brand-mark {
    width: 34px; height: 34px; border-radius: 10px;
    object-fit: cover;
    box-shadow: var(--shadow-1);
  }
  .navlinks { display: flex; gap: 28px; font-size: 14.5px; color: var(--color-on-surface-variant); font-weight: 500; }
  .navlinks a { text-decoration: none; }
  .navlinks a:hover { color: var(--color-primary); }
  @media (max-width: 720px) { .navlinks { display: none; } }

  .btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 0.7rem 1.4rem;
    border-radius: var(--radius-btn);
    font-size: 14.5px; font-weight: 700;
    text-decoration: none; border: none; cursor: pointer;
    transition: filter 0.15s ease, transform 0.12s ease;
  }
  .btn:hover { filter: brightness(0.96); transform: translateY(-1px); }
  .btn-primary { background: var(--color-primary); color: var(--color-on-primary); box-shadow: var(--shadow-1); }
  .btn-ghost { border: 1px solid var(--color-border); color: var(--color-on-surface); background: transparent; }
  .btn-ghost:hover { background: var(--color-surface-container-high); }

  /* ---------- Hero ---------- */
  .hero { border-bottom: 1px solid var(--color-outline-variant); }
  .hero .wrap {
    display: grid; grid-template-columns: 1.05fr 0.95fr; gap: 56px; align-items: center;
    padding-top: 84px; padding-bottom: 84px;
  }
  @media (max-width: 900px) { .hero .wrap { grid-template-columns: 1fr; padding-top: 56px; padding-bottom: 48px; } }

  .hero h1 { font-size: clamp(2.3rem, 4.4vw, 3.4rem); line-height: 1.1; margin: 18px 0 22px; }
  .hero h1 em { font-style: normal; color: var(--color-primary); }
  .hero p.lead { font-size: 18px; color: var(--color-on-surface-variant); max-width: 46ch; margin: 0 0 30px; }
  .hero-cta { display: flex; gap: 12px; flex-wrap: wrap; }

  .hero-stats {
    display: flex; gap: 28px; margin-top: 40px; padding-top: 28px;
    border-top: 1px solid var(--color-outline-variant);
  }
  .stat-num { font-size: 26px; font-weight: 800; font-variant-numeric: tabular-nums; color: var(--color-primary); }
  .stat-label { font-size: 12.5px; color: var(--color-on-surface-variant); margin-top: 2px; }

  .hero-visual {
    border-radius: var(--radius-card);
    background: var(--color-surface);
    border: 1px solid var(--color-outline-variant);
    box-shadow: var(--shadow-2);
    padding: 22px;
  }
  .hero-visual header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
  .hero-visual header .tag {
    font-size: 11.5px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase;
    color: var(--color-on-success-container); background: var(--color-success-container);
    padding: 4px 9px; border-radius: 999px;
  }
  .hero-visual header .title { font-size: 14px; font-weight: 600; color: var(--color-on-surface-variant); }
  canvas#board { width: 100%; height: 230px; display: block; border-radius: 12px; }

  /* ---------- Sections ---------- */
  section { padding: 84px 0; }
  section.tight { padding: 64px 0; }
  .section-head { max-width: 620px; margin-bottom: 48px; }
  .section-head h2 { font-size: clamp(1.7rem, 2.6vw, 2.1rem); margin-top: 14px; }
  .section-head p { color: var(--color-on-surface-variant); font-size: 16px; margin-top: 12px; }
  .divider { border: none; border-top: 1px solid var(--color-outline-variant); margin: 0; }

  /* ---------- Feature grid ---------- */
  .features {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 1px;
    background: var(--color-outline-variant);
    border: 1px solid var(--color-outline-variant);
    border-radius: var(--radius-card);
    overflow: hidden;
  }
  @media (max-width: 960px) { .features { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 560px) { .features { grid-template-columns: 1fr; } }

  .feature { background: var(--color-surface); padding: 28px 24px; display: flex; flex-direction: column; gap: 12px; }
  .feature .icon {
    width: 36px; height: 36px; border-radius: var(--radius-btn);
    display: grid; place-items: center;
    background: rgba(53, 37, 205, 0.08); color: var(--color-primary);
  }
  .feature h3 { font-size: 16.5px; font-weight: 700; }
  .feature p { font-size: 14.5px; color: var(--color-on-surface-variant); margin: 0; }
  .feature .benefit { margin-top: auto; padding-top: 10px; font-size: 12.5px; color: var(--color-success); font-weight: 700; }

  /* ---------- Flow ---------- */
  .flow { display: grid; grid-template-columns: repeat(5, 1fr); position: relative; }
  @media (max-width: 900px) { .flow { grid-template-columns: 1fr; gap: 20px; } }
  .flow-step { position: relative; padding: 0 18px 0 0; }
  [dir="rtl"] .flow-step { padding: 0 0 0 18px; }
  .flow-step:not(:last-child)::after {
    content: ""; position: absolute; top: 19px; right: -1px; width: calc(100% - 38px);
    border-top: 1px dashed var(--color-outline-variant);
  }
  @media (max-width: 900px) { .flow-step:not(:last-child)::after { display: none; } }
  .flow-num {
    width: 38px; height: 38px; border-radius: 50%;
    border: 1px solid var(--color-outline-variant);
    background: var(--color-surface);
    display: grid; place-items: center;
    font-weight: 800; font-size: 15px; color: var(--color-primary);
    margin-bottom: 14px;
  }
  .flow-step h3 { font-size: 15px; margin-bottom: 6px; }
  .flow-step p { font-size: 13.5px; color: var(--color-on-surface-variant); margin: 0; }

  /* ---------- Benefit banner ---------- */
  .banner {
    background: var(--color-primary);
    color: #fff;
    border-radius: var(--radius-card);
    padding: 48px;
    display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 40px; align-items: center;
    box-shadow: var(--shadow-2);
  }
  @media (max-width: 900px) { .banner { grid-template-columns: 1fr; padding: 32px; } }
  .banner h2 { color: #fff; font-size: clamp(1.5rem, 2.4vw, 1.9rem); }
  .banner p { color: rgba(255,255,255,0.75); font-size: 15.5px; margin-top: 12px; }
  .banner-list { display: flex; flex-direction: column; gap: 16px; }
  .banner-item { display: flex; gap: 12px; align-items: flex-start; }
  .banner-item .dot { width: 7px; height: 7px; border-radius: 50%; background: #fff; margin-top: 8px; flex-shrink: 0; }
  .banner-item div { font-size: 14.5px; color: rgba(255,255,255,0.85); }
  .banner-item b { color: #fff; }

  /* ---------- Audience ---------- */
  .audience { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
  @media (max-width: 800px) { .audience { grid-template-columns: 1fr; } }
  .aud-card { border: 1px solid var(--color-outline-variant); border-radius: var(--radius-card); padding: 26px; background: var(--color-surface); box-shadow: var(--shadow-1); }
  .aud-card .eyebrow { margin-bottom: 12px; }
  .aud-card h3 { font-size: 18px; margin-bottom: 10px; }
  .aud-card ul { margin: 0; padding: 0; list-style: none; display: flex; flex-direction: column; gap: 8px; }
  .aud-card li { font-size: 14px; color: var(--color-on-surface-variant); padding-inline-start: 16px; position: relative; }
  .aud-card li::before {
    content: ""; position: absolute; inset-inline-start: 0; top: 8px; width: 6px; height: 2px;
    background: var(--color-success); border-radius: 2px;
  }

  /* ---------- CTA ---------- */
  .cta { text-align: center; padding: 90px 0 100px; }
  .cta h2 { font-size: clamp(1.7rem, 3.2vw, 2.3rem); max-width: 26ch; margin: 0 auto; }
  .cta p { color: var(--color-on-surface-variant); margin: 16px auto 30px; max-width: 46ch; }
  .cta .btn { padding: 0.85rem 1.7rem; font-size: 15.5px; }

  footer { border-top: 1px solid var(--color-outline-variant); padding: 28px 0; }
  footer .wrap { display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: var(--color-on-surface-variant); }
  @media (max-width: 600px) { footer .wrap { flex-direction: column; gap: 10px; text-align: center; } }

  @media (prefers-reduced-motion: reduce) { * { animation: none !important; transition: none !important; } }
</style>
</head>
<body>

<nav>
  <div class="wrap">
    <div class="brand"><img class="brand-mark" src="{{ asset('default-logo.png') }}" alt="{{ config('app.name', 'Rashi') }}"> {{ config('app.name', 'Rashi') }}</div>
    <div class="navlinks">
      <a href="#features">{{ __('messages.landing_features_nav') }}</a>
      <a href="#flow">{{ __('messages.landing_how_it_works_nav') }}</a>
      <a href="#audience">{{ __('messages.landing_who_its_for_nav') }}</a>
    </div>
    <div style="display:flex; align-items:center; gap: 14px;">
      <a class="btn btn-ghost" style="padding: 0.5rem 0.9rem;" href="{{ route('lang.switch', app()->isLocale('ar') ? 'en' : 'ar') }}">
        {{ app()->isLocale('ar') ? 'EN' : 'AR' }}
      </a>
      <a class="btn btn-primary" href="{{ route('loginPage') }}">{{ __('messages.login') }}</a>
    </div>
  </div>
</nav>

<header class="hero">
  <div class="wrap">
    <div>
      <span class="eyebrow">{{ __('messages.app_tagline') }}</span>
      @php
        $headline = str_replace(
            [':points', ':prizes'],
            ['<em>'.__('messages.landing_hero_points').'</em>', '<em>'.__('messages.landing_hero_prizes').'</em>'],
            __('messages.landing_hero_headline')
        );
      @endphp
      <h1>{!! $headline !!}</h1>
      <p class="lead">{{ str_replace(':app', config('app.name', 'Rashi'), __('messages.landing_hero_lead')) }}</p>
      <div class="hero-cta">
        <a class="btn btn-primary" href="{{ route('loginPage') }}">{{ __('messages.login') }} →</a>
        <a class="btn btn-ghost" href="#features">{{ __('messages.landing_see_whats_inside') }}</a>
      </div>
      <div class="hero-stats">
        <div><div class="stat-num">12</div><div class="stat-label">{{ __('messages.landing_stat_modules') }}</div></div>
        <div><div class="stat-num">{{ __('messages.landing_stat_leaderboards_value') }}</div><div class="stat-label">{{ __('messages.landing_stat_leaderboards') }}</div></div>
        <div><div class="stat-num">{{ __('messages.landing_stat_realtime_value') }}</div><div class="stat-label">{{ __('messages.landing_stat_realtime') }}</div></div>
      </div>
    </div>

    <div class="hero-visual">
      <header>
        <span class="tag">{{ __('messages.landing_live_standings') }}</span>
        <span class="title">{{ __('messages.landing_spring_competition') }}</span>
      </header>
      <canvas id="board" width="600" height="230"></canvas>
    </div>
  </div>
</header>

<section id="features">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow">{{ __('messages.landing_whats_inside_eyebrow') }}</span>
      <h2>{{ __('messages.landing_whats_inside_title') }}</h2>
      <p>{{ str_replace(':app', config('app.name', 'Rashi'), __('messages.landing_whats_inside_lead')) }}</p>
    </div>

    <div class="features">
      <div class="feature">
        <div class="icon" aria-hidden="true"><i class="fas fa-flag"></i></div>
        <h3>{{ __('messages.landing_feature_competitions_title') }}</h3>
        <p>{{ __('messages.landing_feature_competitions_desc') }}</p>
        <div class="benefit">{{ __('messages.landing_feature_competitions_benefit') }}</div>
      </div>

      <div class="feature">
        <div class="icon" aria-hidden="true"><i class="fas fa-trophy"></i></div>
        <h3>{{ __('messages.leaderboard') }}</h3>
        <p>{{ __('messages.landing_feature_leaderboard_desc') }}</p>
        <div class="benefit">{{ __('messages.landing_feature_leaderboard_benefit') }}</div>
      </div>

      <div class="feature">
        <div class="icon" aria-hidden="true"><i class="fas fa-gift"></i></div>
        <h3>{{ __('messages.landing_feature_rewards_title') }}</h3>
        <p>{{ __('messages.landing_feature_rewards_desc') }}</p>
        <div class="benefit">{{ __('messages.landing_feature_rewards_benefit') }}</div>
      </div>

      <div class="feature">
        <div class="icon" aria-hidden="true"><i class="fas fa-house-user"></i></div>
        <h3>{{ __('messages.landing_feature_groups_title') }}</h3>
        <p>{{ __('messages.landing_feature_groups_desc') }}</p>
        <div class="benefit">{{ __('messages.landing_feature_groups_benefit') }}</div>
      </div>

      <div class="feature">
        <div class="icon" aria-hidden="true"><i class="fas fa-exchange-alt"></i></div>
        <h3>{{ __('messages.point_transfers') }}</h3>
        <p>{{ __('messages.landing_feature_transfers_desc') }}</p>
        <div class="benefit">{{ __('messages.landing_feature_transfers_benefit') }}</div>
      </div>

      <div class="feature">
        <div class="icon" aria-hidden="true"><i class="fas fa-balance-scale"></i></div>
        <h3>{{ __('messages.bonus-penalties') }}</h3>
        <p>{{ __('messages.landing_feature_bonus_desc') }}</p>
        <div class="benefit">{{ __('messages.landing_feature_bonus_benefit') }}</div>
      </div>

      <div class="feature">
        <div class="icon" aria-hidden="true"><i class="fas fa-bell"></i></div>
        <h3>{{ __('messages.notifications') }}</h3>
        <p>{{ __('messages.landing_feature_push_desc') }}</p>
        <div class="benefit">{{ __('messages.landing_feature_push_benefit') }}</div>
      </div>

      <div class="feature">
        <div class="icon" aria-hidden="true"><i class="fas fa-history"></i></div>
        <h3>{{ __('messages.user_history') }}</h3>
        <p>{{ __('messages.landing_feature_history_desc') }}</p>
        <div class="benefit">{{ __('messages.landing_feature_history_benefit') }}</div>
      </div>
    </div>
  </div>
</section>

<hr class="divider" />

<section id="flow" class="tight">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow">{{ __('messages.landing_flow_eyebrow') }}</span>
      <h2>{{ __('messages.landing_flow_title') }}</h2>
    </div>
    <div class="flow">
      <div class="flow-step">
        <div class="flow-num">1</div>
        <h3>{{ __('messages.landing_flow_1_title') }}</h3>
        <p>{{ __('messages.landing_flow_1_desc') }}</p>
      </div>
      <div class="flow-step">
        <div class="flow-num">2</div>
        <h3>{{ __('messages.landing_flow_2_title') }}</h3>
        <p>{{ __('messages.landing_flow_2_desc') }}</p>
      </div>
      <div class="flow-step">
        <div class="flow-num">3</div>
        <h3>{{ __('messages.landing_flow_3_title') }}</h3>
        <p>{{ __('messages.landing_flow_3_desc') }}</p>
      </div>
      <div class="flow-step">
        <div class="flow-num">4</div>
        <h3>{{ __('messages.landing_flow_4_title') }}</h3>
        <p>{{ __('messages.landing_flow_4_desc') }}</p>
      </div>
      <div class="flow-step">
        <div class="flow-num">5</div>
        <h3>{{ __('messages.landing_flow_5_title') }}</h3>
        <p>{{ __('messages.landing_flow_5_desc') }}</p>
      </div>
    </div>
  </div>
</section>

<section>
  <div class="wrap">
    <div class="banner">
      <div>
        <span class="eyebrow" style="color: #fff;">{{ str_replace(':app', config('app.name', 'Rashi'), __('messages.landing_banner_eyebrow')) }}</span>
        <h2>{{ __('messages.landing_banner_title') }}</h2>
        <p>{{ __('messages.landing_banner_lead') }}</p>
      </div>
      <div class="banner-list">
        <div class="banner-item">
          <span class="dot"></span>
          <div><b>{{ __('messages.landing_banner_item1_b') }}</b> {{ __('messages.landing_banner_item1') }}</div>
        </div>
        <div class="banner-item">
          <span class="dot"></span>
          <div><b>{{ __('messages.landing_banner_item2_b') }}</b> {{ __('messages.landing_banner_item2') }}</div>
        </div>
        <div class="banner-item">
          <span class="dot"></span>
          <div><b>{{ __('messages.landing_banner_item3_b') }}</b> {{ __('messages.landing_banner_item3') }}</div>
        </div>
        <div class="banner-item">
          <span class="dot"></span>
          <div><b>{{ __('messages.landing_banner_item4_b') }}</b> {{ __('messages.landing_banner_item4') }}</div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="audience" class="tight">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow">{{ __('messages.landing_audience_eyebrow') }}</span>
      <h2>{{ __('messages.landing_audience_title') }}</h2>
    </div>
    <div class="audience">
      <div class="aud-card">
        <span class="eyebrow">{{ __('messages.landing_audience_member') }}</span>
        <h3>{{ __('messages.landing_audience_member_title') }}</h3>
        <ul>
          <li>{{ __('messages.landing_audience_member_1') }}</li>
          <li>{{ __('messages.landing_audience_member_2') }}</li>
          <li>{{ __('messages.landing_audience_member_3') }}</li>
          <li>{{ __('messages.landing_audience_member_4') }}</li>
        </ul>
      </div>
      <div class="aud-card">
        <span class="eyebrow">{{ __('messages.landing_audience_admin') }}</span>
        <h3>{{ __('messages.landing_audience_admin_title') }}</h3>
        <ul>
          <li>{{ __('messages.landing_audience_admin_1') }}</li>
          <li>{{ __('messages.landing_audience_admin_2') }}</li>
          <li>{{ __('messages.landing_audience_admin_3') }}</li>
          <li>{{ __('messages.landing_audience_admin_4') }}</li>
        </ul>
      </div>
      <div class="aud-card">
        <span class="eyebrow">{{ __('messages.landing_audience_owner') }}</span>
        <h3>{{ __('messages.landing_audience_owner_title') }}</h3>
        <ul>
          <li>{{ __('messages.landing_audience_owner_1') }}</li>
          <li>{{ __('messages.landing_audience_owner_2') }}</li>
          <li>{{ __('messages.landing_audience_owner_3') }}</li>
          <li>{{ __('messages.landing_audience_owner_4') }}</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="cta">
  <div class="wrap">
    <span class="eyebrow" style="justify-content:center;">{{ __('messages.landing_cta_eyebrow') }}</span>
    <h2 style="margin-top:14px;">{{ __('messages.landing_cta_title') }}</h2>
    <p>{{ str_replace(':app', config('app.name', 'Rashi'), __('messages.landing_cta_lead')) }}</p>
    <a class="btn btn-primary" href="{{ route('loginPage') }}">{{ __('messages.login') }} →</a>
  </div>
</section>

<footer>
  <div class="wrap">
    <div>© {{ date('Y') }} {{ str_replace([':app', ':tagline'], [config('app.name', 'Rashi'), __('messages.app_tagline')], __('messages.landing_footer_tagline')) }}</div>
    <div>{{ request()->getHost() }}</div>
  </div>
</footer>

<script>
(function () {
  var canvas = document.getElementById('board');
  var ctx = canvas.getContext('2d');
  var dpr = Math.min(window.devicePixelRatio || 1, 2);
  var W = canvas.clientWidth, H = canvas.clientHeight;
  canvas.width = W * dpr; canvas.height = H * dpr;
  ctx.scale(dpr, dpr);

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function tok(name, fallback) {
    var v = getComputedStyle(document.documentElement).getPropertyValue(name);
    return v ? v.trim() : fallback;
  }

  var members = [
    { name: 'Rashi', target: 980 },
    { name: 'Rafi', target: 890 },
    { name: 'Mina', target: 810 },
    { name: 'Mark', target: 720 },
    { name: 'Jounir', target: 610 },
    { name: 'Marina', target: 520 }
  ];
  var vals = members.map(function () { return 0; });

  var bar = 20, gap = 12, top = 12, leftLabel = 92, rightVal = 54;

  function draw(t) {
    var primary = tok('--color-primary', '#3525cd');
    var success = tok('--color-success', '#11998e');
    var ink = tok('--color-on-surface', '#191c1e');
    var inkSoft = tok('--color-on-surface-variant', '#464555');
    var line = tok('--color-outline-variant', '#c7c4d8');

    ctx.clearRect(0, 0, W, H);
    var maxVal = Math.max.apply(null, members.map(function (m) { return m.target; }));
    var trackW = W - leftLabel - rightVal - 10;

    members.forEach(function (m, i) {
      var y = top + i * (bar + gap);
      var progress = Math.min(1, t * 1.6 - i * 0.12);
      progress = Math.max(0, Math.min(1, progress));
      vals[i] = m.target * easeOutCubic(progress);

      ctx.fillStyle = inkSoft;
      ctx.font = '600 13px Inter, sans-serif';
      ctx.textBaseline = 'middle';
      ctx.fillText(m.name, 0, y + bar / 2);

      ctx.fillStyle = line;
      roundRect(ctx, leftLabel, y + bar / 2 - 4, trackW, 8, 4);
      ctx.fill();

      var w = trackW * (vals[i] / maxVal);
      var grad = ctx.createLinearGradient(leftLabel, 0, leftLabel + w, 0);
      grad.addColorStop(0, success);
      grad.addColorStop(1, primary);
      ctx.fillStyle = grad;
      roundRect(ctx, leftLabel, y + bar / 2 - 4, Math.max(6, w), 8, 4);
      ctx.fill();

      ctx.fillStyle = ink;
      ctx.font = '700 13px Inter, sans-serif';
      ctx.textAlign = 'right';
      ctx.fillText(Math.round(vals[i]).toLocaleString(), W, y + bar / 2);
      ctx.textAlign = 'left';
    });
  }

  function easeOutCubic(x) { return 1 - Math.pow(1 - x, 3); }
  function roundRect(ctx, x, y, w, h, r) {
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.arcTo(x + w, y, x + w, y + h, r);
    ctx.arcTo(x + w, y + h, x, y + h, r);
    ctx.arcTo(x, y + h, x, y, r);
    ctx.arcTo(x, y, x + w, y, r);
    ctx.closePath();
  }

  if (reduced) {
    draw(1);
  } else {
    var start = null;
    function frame(ts) {
      if (!start) start = ts;
      var elapsed = (ts - start) / 1000;
      var t = Math.min(1, elapsed / 1.4);
      draw(t);
      if (t < 1) requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  }

  window.addEventListener('resize', function () {
    W = canvas.clientWidth; H = canvas.clientHeight;
    canvas.width = W * dpr; canvas.height = H * dpr;
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    draw(1);
  });
})();
</script>
</body>
</html>
