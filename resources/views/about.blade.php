<!DOCTYPE html><!--[if IE]>  <html class="pdf24_ie"> <![endif]-->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css"
        integrity="sha512-2bBQCjcnw658Lho4nlXJcc6WkV/UxpE/sAokbXPxQNGqmNdQrWqtw26Ns9kFF/yG792pKR1Sx8/Y1Lf1XN4GKA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css"
        integrity="sha512-Ho3VxMNyeg+kwjXcdC3pBZD+u0S32rqnHWe4RbBk4W0gq8WooooW/4cFuNgs0ItyqzlsqGfMO0+AjtsqJVDaMw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @if (app()->getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.rtl.min.css"
            crossorigin="anonymous">
    @endif
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" data-aos='fade-down'>
        <div class="container">
            <a class='navbar-brand' href='/'><img src="{{ asset('images/logo.png') }}" alt='Shashh Logo'
                    style="height: 40px;"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav"
                aria-controls="main-nav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars" style="color: #0032d6;"></i>
            </button>
            <div class="collapse navbar-collapse" id="main-nav">
                <ul class="navbar-nav mb-2 mb-lg-0 w-100 justify-content-end">
                    <li class="nav-item"><a class="nav-link" href='/'>{{ __('messages.home') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href='/about'>{{ __('messages.about') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href='/contact'>{{ __('messages.contact') }}</a></li>
                    <li class="nav-item"><a class="nav-link"
                            href='https://owner.shashh.com/login'>{{ __('messages.join_owner') }}</a></li>
                    <li class="nav-item"><a class="nav-link"
                            href='https://client.shashh.com/'>{{ __('messages.start_campaign') }}</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-globe"></i> {{ strtoupper(app()->getLocale()) }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('locale', 'en') }}">English</a></li>
                            <li><a class="dropdown-item" href="{{ route('locale', 'ar') }}">العربية</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class='about-hero d-flex align-items-center' data-aos='fade-up'>
        <div class='banner-overlay'></div>
        <div class='container position-relative text-center text-md-start' style="z-index: 50;">
            <div class='about-hero-content'>
                <h1>{{ __('messages.about_hero_title') }}</h1>
                <h2>{{ __('messages.about_hero_subtitle') }}</h2>
                <p>{{ __('messages.about_hero_desc') }}</p>
                <a class='btn btn-primary btn-large book' href='https://client.shashh.com/login'>{{ __('messages.about_hero_cta') }}</a>
            </div>
        </div>
    </section>

    <div class='Process' data-aos='fade-up'>
        <div class='container process-shell'>
            <h1 data-aos="fade-up" data-aos-delay="150">{{ __('messages.about_process_title') }}
                <span>{{ __('messages.about_process_title_highlight') }}</span></h1>
            <p class='process-subtitle' data-aos="fade-up" data-aos-delay="220">
                {{ __('messages.about_process_subtitle') }}
            </p>
            <div class='row justify-content-center g-4 process-row'>
                <div class='col-12 col-md-4 process-item' data-aos="fade-up" data-aos-delay="280">
                    <div class='process-icon'>@include('locate')</div>
                    <h3 class='process-title'>{{ __('messages.about_process_item1_title') }}</h3>
                    <p class='process-copy'>{{ __('messages.about_process_item1_copy') }}</p>
                </div>
                <div class='col-12 col-md-4 process-item' data-aos="fade-up" data-aos-delay="360">
                    <div class='process-icon'>@include('book')</div>
                    <h3 class='process-title'>{{ __('messages.about_process_item2_title') }}</h3>
                    <p class='process-copy'>{{ __('messages.about_process_item2_copy') }}</p>
                </div>
                <div class='col-12 col-md-4 process-item' data-aos="fade-up" data-aos-delay="440">
                    <div class='process-icon'>@include('display')</div>
                    <h3 class='process-title'>{{ __('messages.about_process_item3_title') }}</h3>
                    <p class='process-copy'>{{ __('messages.about_process_item3_copy') }}</p>
                </div>
            </div>
        </div>
    </div>

    <section class='revolution founder-revolution' data-aos='fade-up'>
        <div class='container w-80'>
            <div class='row align-items-center g-4'>
                <div class='col-lg-6 text-center' data-aos='fade-left'>
                    <img src='{{ asset('images/f83cde0139d0f040fcd743e796072e1a.jpg') }}'
                        alt='Shashh revolution illustration' class="img-fluid founder-image" />
                </div>
                <div class='col-lg-6 mb-5 mb-lg-0' data-aos='fade-right'>
                    <div class='revolution-card founder-card'>
                        <h1>{{ __('messages.about_founder_title') }}</h1>
                        <p class='founder-name'>{{ __('messages.about_founder_name') }}</p>
                        <div class='d-flex flex-column gap-3'>
                            <p>{{ __('messages.about_founder_p1') }}</p>
                            <p>{{ __('messages.about_founder_p2') }}</p>
                            <p>{{ __('messages.about_founder_p3') }}</p>
                        </div>
                        <a class='btn btn-primary btn-lg mt-3' href='https://client.shashh.com/login'>{{ __('messages.book_signages') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class='footer' data-aos='fade-up'>
        <div class='container-fluid'>
            <div class='row g-4'>
                <div class='col-12 col-md-6 col-lg-3'>
                    <div class='footer-panel'>
                        <a class='brand mb-3 d-inline-block' href='/'><img
                                src="{{ asset('images/logo.png') }}" alt='Shashh Logo'></a>
                        <p>{{ __('messages.footer_desc') }}</p>
                    </div>
                </div>
                <div class='col-12 col-md-6 col-lg-3'>
                    <div class='footer-panel'>
                        <h5 class='footer-title'>{{ __('messages.footer_quick_links') }}</h5>
                        <ul class='footer-links'>
                            <li><a href='/'>{{ __('messages.home') }}</a></li>
                            <li><a href='/about'>{{ __('messages.about') }}</a></li>
                            <li><a href='/contact'>{{ __('messages.contact') }}</a></li>
                            <li><a href='https://owner.shashh.com/login'>{{ __('messages.join_owner') }}</a></li>
                            <li><a href='https://client.shashh.com/'>{{ __('messages.start_campaign') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class='col-12 col-md-6 col-lg-3'>
                    <div class='footer-panel'>
                        <h5 class='footer-title'>{{ __('messages.footer_contact_us') }}</h5>
                        <p>{{ __('messages.footer_email') }}: <a href='mailto:info@shashh.com'>info@shashh.com</a></p>
                        <p>{{ __('messages.footer_phone') }}: <a href='tel:+1234567890'>+1 (234) 567-890</a></p>
                        <p>{{ __('messages.footer_address') }}: 123 Main Street, Anytown, USA</p>
                    </div>
                </div>
                <div class='col-12 col-md-6 col-lg-3'>
                    <div class='footer-panel'>
                        <h5 class='footer-title'>{{ __('messages.footer_policies') }}</h5>
                        <ul class='footer-links mb-3'>
                            <li><a href='/terms'>{{ __('messages.footer_terms') }}</a></li>
                            <li><a href='/privacy'>{{ __('messages.footer_privacy') }}</a></li>
                            <li><a href='/cookies'>{{ __('messages.footer_cookies') }}</a></li>
                        </ul>

                    </div>
                </div>
            </div>
            <div class='footer-bottom mt-5'>
                <p class='mb-0'>{{ __('messages.footer_copyright') }}</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js"
        integrity="sha512-6BTOlkauINO65nLhXhthZMtepgJSghyimIalb+crKRPhvhmsCdnIuGcVbR5/aQY2A+260iC1OPy1oCdB6pSSwQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"
        integrity="sha512-A7AYk1fGKX6S2SsHywmPkrnzTZHrgiVT7GcQkLGDe2ev0aWb8zejytzS8wjo7PGEXKqJOrjQ4oORtnimIRZBtw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <style>
        [dir="rtl"] * {
            text-align: right;
        }
        .navbar {
            background-color: transparent;
            transition: background-color 0.3s ease-in-out;
        }

        .navbar.scrolled,
        .navbar.navbar-mobile-open {
            background-color: #ffffff;
        }

        .navbar.scrolled a,
        .navbar.navbar-mobile-open a {
            color: #0032d6 !important;
            transition: all 0.3s ease-in-out;
        }

        .navbar.scrolled a:hover,
        .navbar.navbar-mobile-open a:hover {
            color: #001147;
        }



        .about-hero {
            min-height: 100svh;
            position: relative;
            padding-top: 110px;
        }

        .about-hero .banner-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(115deg, rgba(0, 0, 0, 0.795) 12%, rgba(0, 0, 0, 0.479) 48%, rgba(0, 0, 0, 0.192) 100%);
        }

        .about-hero-content {
            max-width: 700px;
            position: relative;
            z-index: 2;
            margin-top: clamp(1rem, 10vh, 5rem);
        }

        .about-hero-content h1 {
            font-size: clamp(2rem, 4.8vw, 3.3rem);
            color: #fff;
            margin-bottom: 0.8rem;
        }

        .about-hero-content h2 {
            font-size: clamp(1.1rem, 2.3vw, 1.75rem);
            color: #ffffff;
            margin-bottom: 1rem;
        }

        .about-hero-content p {
            color: #ffffff;
            line-height: 1.75;
            font-size: clamp(0.95rem, 1.2vw, 1.08rem);
            margin-bottom: 1.4rem;
        }

        .book {
            box-shadow: 0 10px 28px rgba(0, 40, 170, 0.28);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .book:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 34px rgba(0, 40, 170, 0.34);
        }

        .Process {
            margin-top: 100px;
            padding: 88px 0 84px;
        }

        .process-shell {
            max-width: 1120px;
        }

        .Process h1 {
            text-align: center;
            margin-bottom: 14px;
            font-size: clamp(2rem, 4vw, 3.2rem);
            font-weight: 500;
            color: #1946d7;
        }

        .Process h1 span {
            font-weight: 800;
            color: #1b45d2;
        }

        .process-subtitle {
            text-align: center;
            margin: 0 auto;
            max-width: 760px;
            color: #42464f;
            font-size: clamp(1rem, 1.25vw, 1.08rem);
            line-height: 1.45;
        }

        .process-row {
            margin-top: 56px;
        }

        .process-item {
            text-align: center;
            padding: 0 14px;
        }

        .process-icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .process-icon svg {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .process-title {
            margin: 0 0 8px;
            font-size: clamp(1.55rem, 1.95vw, 2rem);
            font-weight: 500;
            color: #373a40;
            line-height: 1.15;
            text-align: center;
        }

        .process-copy {
            margin: 0;
            font-size: clamp(1.02rem, 1.18vw, 1.15rem);
            color: #474b52;
            line-height: 1.35;
            text-align: center;
        }

        .founder-revolution {
            margin-top: 100px;
            padding: 48px 0;
        }

        .founder-image {
            border-radius: 18px;
            box-shadow: 0 14px 38px rgba(0, 0, 0, 0.14);
        }

        .founder-card {
            height: 100%;
        }

        .founder-name {
            color: #5f6786;
            font-weight: 600;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                border-radius: 0.5rem;
                margin-top: 1rem;
            }

            .about-hero {
                min-height: 70vh;
            }

            .Process {
                padding: 70px 0;
            }

            .process-row {
                margin-top: 42px;
            }
        }

        @media (max-width: 575.98px) {
            .about-hero {
                padding-top: 92px;
            }

            .process-title {
                font-size: 1.48rem;
                text-align: center;
            }

            .process-icon {
                width: 64px;
                height: 64px;
            }

            .process-copy {
                font-size: 1rem;
                text-align: center;

            }
        }


    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 1000,
                offset: 120,
            });

            const navbar = document.querySelector('.navbar');
            const navbarToggler = document.querySelector('.navbar-toggler');

            function handleScroll() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    if (!navbar.classList.contains('navbar-mobile-open')) {
                        navbar.classList.remove('scrolled');
                    }
                }
            }

            function handleToggle() {
                navbar.classList.toggle('navbar-mobile-open');
                if (navbar.classList.contains('navbar-mobile-open')) {
                    navbar.classList.add('scrolled');
                } else {
                    handleScroll();
                }
            }

            window.addEventListener('scroll', handleScroll);
            if (navbarToggler) {
                navbarToggler.addEventListener('click', handleToggle);
            }
        });
    </script>
</body>

</html>
