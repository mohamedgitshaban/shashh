<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.verified_title') }}</title>
    <style>
        @font-face {
            font-family: 'Gelion';
            src: url('{{ asset('fonts/Gelion-Regular.otf') }}') format('opentype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Gelion';
            src: url('{{ asset('fonts/Gelion-SemiBold.otf') }}') format('opentype');
            font-weight: 600;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'SST Arabic';
            src: url('{{ asset('fonts/SSTArabic-Roman.ttf') }}') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'SST Arabic';
            src: url('{{ asset('fonts/SSTArabic-Bold.ttf') }}') format('truetype');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }

        :root {
            /* Same palette as the Shashh home page (navbar, banner, buttons) */
            --brand: #0032d6;
            --brand-dark: #001147;
            --brand-deep: #0028aa;
            --bg-start: #f4f7ff;
            --bg-end: #e7edff;
            --card: #ffffff;
            --text: #0f1f54;
            --subtext: #53608e;
            --accent-soft: #e7edff;
            --ring: rgba(0, 50, 214, 0.22);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: 'Gelion', "Segoe UI", sans-serif;
            color: var(--text);
            background: radial-gradient(circle at 18% 18%, #d9e6ff 0%, transparent 45%),
                        radial-gradient(circle at 82% 12%, #dce8ff 0%, transparent 40%),
                        linear-gradient(160deg, var(--bg-start), var(--bg-end));
            overflow: hidden;
            padding: 24px;
        }

        html[dir="rtl"] body {
            font-family: 'SST Arabic', sans-serif;
        }

        .wrap {
            width: min(92vw, 520px);
            padding: 30px 26px;
            border-radius: 26px;
            background: color-mix(in srgb, var(--card) 92%, #eef3ff 8%);
            box-shadow: 0 24px 56px rgba(0, 17, 71, 0.16);
            text-align: center;
            animation: cardIn 700ms ease both;
        }

        .logo {
            height: 34px;
            margin-bottom: 22px;
        }

        .icon {
            width: 110px;
            height: 110px;
            margin: 8px auto 18px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: radial-gradient(circle, var(--accent-soft), #f5f8ff);
            box-shadow: 0 0 0 0 var(--ring);
            animation: pulse 2.2s infinite;
        }

        .check {
            width: 56px;
            height: 56px;
            stroke: var(--brand);
            stroke-width: 7;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .check path {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: draw 900ms ease forwards 250ms;
        }

        h1 {
            margin: 0;
            font-size: clamp(1.5rem, 2vw, 2rem);
            letter-spacing: 0.2px;
            color: var(--brand-dark);
            font-weight: 600;
        }

        p {
            margin: 12px 0 0;
            color: var(--subtext);
            line-height: 1.55;
            font-size: 1rem;
        }

        .email {
            margin-top: 10px;
            display: inline-block;
            padding: 8px 14px;
            border-radius: 999px;
            background: #f1f4ff;
            color: var(--brand-dark);
            font-weight: 600;
            max-width: 100%;
            overflow-wrap: anywhere;
        }

        .hint {
            margin-top: 18px;
            font-size: 0.92rem;
        }

        .cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 24px;
            padding: 13px 30px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--brand), var(--brand-deep));
            color: #ffffff;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            box-shadow: 0 10px 28px rgba(0, 40, 170, 0.3);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 34px rgba(0, 40, 170, 0.38);
            color: #ffffff;
        }

        .cta svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            stroke-width: 2.4;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: transform 0.25s ease;
        }

        .cta:hover svg {
            transform: translateX(3px);
        }

        html[dir="rtl"] .cta:hover svg {
            transform: translateX(-3px) scaleX(-1);
        }

        html[dir="rtl"] .cta svg {
            transform: scaleX(-1);
        }

        @keyframes draw {
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 var(--ring);
                transform: scale(1);
            }
            70% {
                box-shadow: 0 0 0 22px rgba(0, 50, 214, 0);
                transform: scale(1.03);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 50, 214, 0);
                transform: scale(1);
            }
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(14px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>
</head>
<body>
    <main class="wrap" role="main" aria-live="polite">
        <img class="logo" src="{{ asset('images/logo.png') }}" alt="Shashh">

        <div class="icon" aria-hidden="true">
            <svg class="check" viewBox="0 0 64 64">
                <path d="M16 34 L27 45 L48 22"></path>
            </svg>
        </div>

        <h1>{{ __('messages.verified_title') }}</h1>
        <p>{{ __('messages.verified_desc') }}</p>
        <p class="email">{{ $email }}</p>
        <p class="hint">{{ __('messages.verified_hint') }}</p>

        @if($isOwner)
            <a class="cta" href="https://owner.shashh.com/login">
                {{ __('messages.verified_go_owner') }}
                <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
            </a>
        @else
            <a class="cta" href="https://client.shashh.com/">
                {{ __('messages.verified_go_client') }}
                <svg viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
            </a>
        @endif
    </main>
</body>
</html>
