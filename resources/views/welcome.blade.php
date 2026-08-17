<!DOCTYPE html><!--[if IE]>  <html class="pdf24_ie"> <![endif]-->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Shashh</title>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css"
        integrity="sha512-2bBQCjcnw658Lho4nlXJcc6WkV/UxpE/sAokbXPxQNGqmNdQrWqtw26Ns9kFF/yG792pKR1Sx8/Y1Lf1XN4GKA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- Removed duplicate AOS CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css"
        integrity="sha512-Ho3VxMNyeg+kwjXcdC3pBZD+u0S32rqnHWe4RbBk4W0gq8WooooW/4cFuNgs0ItyqzlsqGfMO0+AjtsqJVDaMw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    @if (app()->getLocale() == 'ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.rtl.min.css"
            crossorigin="anonymous">
    @endif
    <link rel="stylesheet" href="{{ asset('css/app.css') }}" />

</head>

<body>
    {{-- Replaced with Bootstrap 5 Responsive Navbar --}}
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
    <div class='banner d-flex flex-column justify-content-center' data-aos='fade-up'>
        <div class='banner-overlay'></div>
        <div class='header container'>
            <h1>{{ __('messages.banner_title') }}</h1>
            <h2>{{ __('messages.banner_subtitle') }}</h2>
            <p>{!! __('messages.banner_desc') !!}</p>
            <a class='btn btn-primary btn-large book' href='/contact'>{{ __('messages.book_signages') }}</a>
        </div>
        {{-- Using Bootstrap grid for responsiveness --}}
        <div class='container mt-auto position-relative' style="z-index: 50" data-aos="fade-up" data-aos-delay="0">
            <div class="row justify-content-center text-center gy-4">
                <div class="col-6 col-md-4 col-lg banner-flex-item banner-stat-card">
                    <img src='/images/banner1.png' alt='{{ __('messages.signages_count') }}' data-aos="fade-up" data-aos-delay="200" />
                    <h3>{{ __('messages.signages_count') }}</h3>
                </div>
                <div class="col-6 col-md-4 col-lg banner-flex-item banner-stat-card">
                    <img src='/images/banner2.png' alt='{{ __('messages.companies_count') }}' data-aos="fade-up" data-aos-delay="400" />
                    <h3>{{ __('messages.companies_count') }}</h3>
                    <span>{{ __('messages.companies_partnered') }}</span>
                </div>
                <div class="col-6 col-md-4 col-lg banner-flex-item banner-stat-card">
                    <img src='/images/banner3.png' alt='{{ __('messages.first_app') }}' data-aos="fade-up" data-aos-delay="600" />
                    <h3>{{ __('messages.first_app') }}</h3>
                </div>
                <div class="col-6 col-md-4 col-lg banner-flex-item banner-stat-card">
                    <img src='/images/banner4.png' alt='{{ __('messages.confirmed') }}' data-aos="fade-up" data-aos-delay="800" />
                    <h3>{{ __('messages.confirmed') }}</h3>
                    <span>{{ __('messages.confirmed_by') }}</span>
                </div>
                <div class="col-6 col-md-4 col-lg banner-flex-item banner-stat-card">
                    <img src='/images/banner5.png' alt='{{ __('messages.saudi_made') }}' data-aos="fade-up" data-aos-delay="1000" />
                    <h3>{{ __('messages.saudi_made') }}</h3>
                    <span>{{ __('messages.saudi_made_by') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class='Process container' data-aos='fade-up'>
        <h1 data-aos="fade-up" class="primary" data-aos-delay="200">{{ __('messages.process_title') }}</h1>
        <div class='row justify-content-center gy-5 gx-4'>
            <div class='col-12 col-sm-6 col-md-4 col-lg-3 process-card-wrapper' data-aos="fade-up" data-aos-delay="100">
                <div class='process-card' data-accent='#e84c89'>

                    <div class='process-card-body'>
                        <div class='process-icon'>@include('locate')</div>
                        <h3 class='process-title'>{{ __('messages.locate') }}</h3>
                        {{-- <p class='process-desc'>{{ __('messages.process_desc_1') ?? 'Discover and locate the perfect signage opportunities in your area.' }}</p>
                        <a href='/contact' class='process-btn' style='--btn-color: #e84c89;'>{{ __('messages.read_more') ?? 'Read More' }}</a> --}}
                    </div>
                </div>
            </div>
            <div class='col-12 col-sm-6 col-md-4 col-lg-3 process-card-wrapper' data-aos="fade-up" data-aos-delay="200">
                <div class='process-card' data-accent='#0090da'>

                    <div class='process-card-body'>
                        <div class='process-icon'>@include('book')</div>
                        <h3 class='process-title'>{{ __('messages.book') }}</h3>
                        {{-- <p class='process-desc'>{{ __('messages.process_desc_2') ?? 'Easily book your preferred signage slots with just a few clicks.' }}</p>
                        <a href='/contact' class='process-btn' style='--btn-color: #0090da;'>{{ __('messages.read_more') ?? 'Read More' }}</a> --}}
                    </div>
                </div>
            </div>
            <div class='col-12 col-sm-6 col-md-4 col-lg-3 process-card-wrapper' data-aos="fade-up" data-aos-delay="300">
                <div class='process-card' data-accent='#ffc107'>

                    <div class='process-card-body'>
                        <div class='process-icon'>@include('pay')</div>
                        <h3 class='process-title'>{{ __('messages.pay') }}</h3>
                        {{-- <p class='process-desc'>{{ __('messages.process_desc_3') ?? 'Secure payment options for your advertising campaigns.' }}</p>
                        <a href='/contact' class='process-btn' style='--btn-color: #ffc107;'>{{ __('messages.read_more') ?? 'Read More' }}</a> --}}
                    </div>
                </div>
            </div>
            <div class='col-12 col-sm-6 col-md-4 col-lg-3 process-card-wrapper' data-aos="fade-up" data-aos-delay="400">
                <div class='process-card' data-accent='#52b788'>

                    <div class='process-card-body'>
                        <div class='process-icon'>@include('display')</div>
                        <h3 class='process-title'>{{ __('messages.display') }}</h3>
                        {{-- <p class='process-desc'>{{ __('messages.process_desc_4') ?? 'Watch your content come to life on premium digital displays.' }}</p>
                        <a href='/contact' class='process-btn' style='--btn-color: #52b788;'>{{ __('messages.read_more') ?? 'Read More' }}</a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class='howitworks' data-aos='fade-up'>
        <h1 data-aos="fade-up" data-aos-delay="200">{{ __('messages.how_it_works_title') }}</h1>

        <div class='howitworks-showcase' data-aos="fade-up" data-aos-delay="250">
            <div class='row g-0 align-items-stretch howitworks-flex'>
                <div class='col-lg-6 order-2 order-lg-1 d-flex'>
                    <div class='howitworks-panel' data-aos="fade-right" data-aos-delay="350">
                        <span class='howitworks-kicker'>{{ __('messages.step1_title') }}</span>
                        <h3 class='howitworks-title'>{{ __('messages.step1_subtitle') }}</h3>
                        <p class='howitworks-desc'>{{ __('messages.step1_desc') }}</p>
                        <a class='howitworks-btn' href='/contact'>{{ __('messages.book_signages') }}</a>
                    </div>
                </div>
                <div class='col-lg-6 order-1 order-lg-2 d-flex'>
                    <div class='howitworks-media'>
                        <img src='/images/howitworks1.png' class='img-fluid object-fit-cover' alt='{{ __('messages.step1_title') }}' />
                    </div>
                </div>
            </div>
        </div>

        <div class='howitworks-showcase' data-aos="fade-up" data-aos-delay="300">
            <div class='row g-0 align-items-stretch howitworks-flex howitworks-reverse'>
                <div class='col-lg-6 d-flex'>
                    <div class='howitworks-media'>
                        <img src='/images/howitworks2.png' class='img-fluid object-fit-cover' alt='{{ __('messages.step2_title') }}' />
                    </div>
                </div>
                <div class='col-lg-6 d-flex'>
                    <div class='howitworks-panel' data-aos="fade-left" data-aos-delay="400">
                        <span class='howitworks-kicker'>{{ __('messages.step2_title') }}</span>
                        <h3 class='howitworks-title'>{{ __('messages.step2_subtitle') }}</h3>
                        <p class='howitworks-desc'>{{ __('messages.step2_desc') }}</p>
                        <a class='howitworks-btn' href='/contact'>{{ __('messages.book_signages') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class='howitworks-showcase' data-aos="fade-up" data-aos-delay="350">
            <div class='row g-0 align-items-stretch howitworks-flex'>
                <div class='col-lg-6 order-2 order-lg-1 d-flex'>
                    <div class='howitworks-panel' data-aos="fade-right" data-aos-delay="450">
                        <span class='howitworks-kicker'>{{ __('messages.step3_title') }}</span>
                        <h3 class='howitworks-title'>{{ __('messages.step3_subtitle') }}</h3>
                        <p class='howitworks-desc'>{{ __('messages.step3_desc') }}</p>
                        <a class='howitworks-btn' href='/contact'>{{ __('messages.book_signages') }}</a>
                    </div>
                </div>
                <div class='col-lg-6 order-1 order-lg-2 d-flex'>
                    <div class='howitworks-media'>
                        <img src='/images/howitworks3.png' class='img-fluid object-fit-cover' alt='{{ __('messages.step3_title') }}' />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <section class='revolution' data-aos='fade-up'>
        <div class='container w-80'>
            <div class='row align-items-center g-4'>
                <div class='col-lg-6 text-center' data-aos='fade-left'>
                    <img src='{{ asset('images/image.png') }}' alt='Shashh revolution illustration' class="img-fluid" />

                </div>
                <div class='col-lg-6 mb-5 mb-lg-0' data-aos='fade-right'>
                    <div class='revolution-card'>
                        <h1>{{ __('messages.revolution_title') }}</h1>
                        <p>{{ __('messages.revolution_desc') }}</p>
                        <div class='row gx-3 gy-4 mt-4'>
                            <div class='col-sm-6'>
                                <div class='revolution-item flex align-items-center'>
                                    <i class='fa-solid fa-network-wired'></i>
                                    <div>
                                        <h3>{{ __('messages.revolution_stat1_val') }}</h3>
                                        <p>{{ __('messages.revolution_stat1_label') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class='col-sm-6'>
                                <div class='revolution-item flex align-items-center'>
                                    <i class='fa-solid fa-sign-hanging'></i>
                                    <div>
                                        <h3>{{ __('messages.revolution_stat2_val') }}</h3>
                                        <p>{{ __('messages.revolution_stat2_label') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class='col-sm-6'>
                                <div class='revolution-item flex align-items-center'>
                                    <i class='fa-solid fa-user-check'></i>
                                    <div>
                                        <h3>{{ __('messages.revolution_stat3_val') }}</h3>
                                        <p>{{ __('messages.revolution_stat3_label') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class='col-sm-6'>
                                <div class='revolution-item flex align-items-center'>
                                    <i class='fa-solid fa-flag'></i>
                                    <div>
                                        <h3>{{ __('messages.revolution_stat4_val') }}</h3>
                                        <p>{{ __('messages.revolution_stat4_label') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class='mt-4'>{{ __('messages.revolution_footer') }}</p>
                        <a class='btn btn-primary btn-lg mt-3' href='/contact'>{{ __('messages.book_signages') }}</a>
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
                        <h5 class='footer-title'>{{ __('messages.footer_follow_us') }}</h5>
                        <div class='social-links'>
                            <a href='https://www.facebook.com/shashh' aria-label='Facebook'><i
                                    class='fa-brands fa-facebook'></i></a>
                            <a href='https://www.twitter.com/shashh' aria-label='Twitter'><i
                                    class='fa-brands fa-twitter'></i></a>
                            <a href='https://www.instagram.com/shashh' aria-label='Instagram'><i
                                    class='fa-brands fa-instagram'></i></a>
                            <a href='https://www.linkedin.com/company/shashh' aria-label='LinkedIn'><i
                                    class='fa-brands fa-linkedin'></i></a>
                        </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script> {{-- Bootstrap JS (includes Popper for dropdowns) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"
        integrity="sha512-A7AYk1fGKX6S2SsHywmPkrnzTZHrgiVT7GcQkLGDe2ev0aWb8zejytzS8wjo7PGEXKqJOrjQ4oORtnimIRZBtw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <style>
        /* 1. Remove body padding to let banner go under navbar */
                [dir="rtl"] * {
            text-align: right;
        }
        .banner {
            padding-top: 120px;
            /* Add padding to banner content to avoid being hidden by navbar */
        }

        .banner {
            min-height: 100svh;
            height: auto;
            display: flex;
            justify-content: flex-end;
            background-attachment: scroll;
            overflow: hidden;
        }

        .banner .banner-overlay {
            background: linear-gradient(115deg, rgba(255, 255, 255, 0.9) 8%, rgba(255, 255, 255, 0.72) 45%, rgba(255, 255, 255, 0.52) 100%);
        }

        .banner .header {
            position: relative;
            top: auto;
            left: auto;
            margin-top: clamp(2rem, 10vh, 8rem);
            margin-inline: auto;
            width: min(1120px, 92%);
            text-align: left;
            z-index: 101;
        }

        .banner .header h1 {
            font-size: clamp(2rem, 5vw, 3.6rem);
            line-height: 1.1;
            margin-bottom: 0.8rem;
        }

        .banner .header h2 {
            font-size: clamp(1.1rem, 2.4vw, 1.85rem);
            margin-bottom: 1rem;
            color: #1f2f6e;
        }

        .banner .header p {
            max-width: 58ch;
            font-size: clamp(0.95rem, 1.35vw, 1.1rem);
            line-height: 1.7;
            color: #3a4158;
            margin-bottom: 1.5rem;
        }

        .banner .book {
            box-shadow: 0 10px 28px rgba(0, 40, 170, 0.28);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .banner .book:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 34px rgba(0, 40, 170, 0.34);
        }

        .banner-stat-card {
            /* background: rgba(255, 255, 255, 0.82); */
            /* border: 1px solid rgba(0, 50, 214, 0.11); */
            border-radius: 18px;
            padding: 1rem 0.65rem;
            /* backdrop-filter: blur(5px); */
            /* box-shadow: 0 8px 26px rgba(9, 24, 74, 0.09); */
            transition: transform 0.26s ease, box-shadow 0.26s ease;
        }

        .banner-stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 28px rgba(9, 24, 74, 0.14);
        }

        .banner-stat-card img {
            width: 48px;
            height: 48px;
            object-fit: contain;
            margin-bottom: 0.6rem;
        }

        .banner-stat-card h3 {
            font-size: 1.03rem;
            margin-bottom: 0.25rem;
            color: #0f1f54;
            font-weight: 700;
        }

        .banner-stat-card span {
            font-size: 0.84rem;
            color: #53608e;
        }

        /* 2. Make navbar transparent initially */
        .navbar {
            background-color: transparent;
            transition: background-color 0.3s ease-in-out;
        }

        /* 3. Add a background to the navbar when it's scrolled or when the mobile menu is open */
        .navbar.scrolled ,
        .navbar.navbar-mobile-open {
            background-color: #ffffff;
            /* A dark color that matches your theme */
        }
                .navbar.scrolled a,
        .navbar.navbar-mobile-open a{
            color: #0032d6 !important;
            transition: all 0.3s ease-in-out;
            /* A dark color that matches your theme */
        }
                .navbar.scrolled a:hover,
        .navbar.navbar-mobile-open a:hover{
            color: #001147 !important;
            /* A dark color that matches your theme */
        }
        /* 4. Style the collapsed menu for mobile */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                /* background-color: #0c111b; */
                /* padding: 1rem; */
                border-radius: 0.5rem;
                margin-top: 1rem;
            }

            .banner {
                min-height: auto;
            }

            .banner .header {
                margin-top: 5.8rem;
            }

            .banner-stat-card {
                padding: 0.9rem 0.55rem;
            }

            .banner-stat-card h3 {
                font-size: 0.95rem;
            }

            .banner-stat-card span {
                font-size: 0.78rem;
            }
        }

        @media (max-width: 575.98px) {
            .banner {
                padding-top: 100px;
            }

            .banner .header {
                margin-top: 4.75rem;
            }

            .banner .header p {
                max-width: 100%;
            }


            .banner-stat-card {
                border-radius: 14px;
            }

            .banner-stat-card img {
                width: 40px;
                height: 40px;
            }
        }

        .Process {
            margin-top: 100px;
            padding: 72px 20px;
        }

        .Process h1 {
            text-align: center;
            margin-bottom: 48px;
            font-size: clamp(2rem, 4vw, 2.8rem);
            font-weight: 700;
        }

        .process-card-wrapper {
            display: flex;
            align-items: stretch;
        }

        .process-card {
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            position: relative;
            transition: transform 0.32s ease, box-shadow 0.32s ease;
        }

        .process-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.14);
        }

        .process-card-header {
            height: 5px;
            background-color: var(--btn-color, #0028aa);
            width: 100%;
        }

        .process-card-badge {
            position: absolute;
            top: 14px;
            left: 16px;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            color: rgba(0, 0, 0, 0.38);
            text-transform: uppercase;
        }

        .process-card-body {
            padding: 48px 28px 32px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .process-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
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
            margin: 0 0 14px;
            font-size: 1.18rem;
            font-weight: 700;
            color: #1f1f1f;
            text-align: center;
            letter-spacing: 0.01em;
        }

        .process-desc {
            margin: 0 0 28px;
            font-size: 0.95rem;
            line-height: 1.62;
            color: #555555;
            text-align: center;
            min-height: 50px;
        }

        .process-btn {
            align-self: center;
            display: inline-block;
            background-color: var(--btn-color, #0028aa);
            color: #ffffff;
            padding: 9px 24px;
            border: none;
            border-radius: 4px;
            font-size: 0.88rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-decoration: none;
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .process-btn:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.18);
        }

        @media (max-width: 991.98px) {
            .Process {
                padding: 56px 16px;
            }

            .Process h1 {
                margin-bottom: 40px;
            }

            .process-card-body {
                padding: 40px 20px 28px;
            }

            .process-card-badge {
                top: 12px;
                left: 12px;
            }
        }

        @media (max-width: 575.98px) {
            .process-card-body {
                padding: 36px 18px 24px;
            }

            .process-title {
                font-size: 1.08rem;
            }

            .process-icon {
                width: 70px;
                height: 70px;
            }
        }

        .howitworks {
            --hiw-card-bg: #f6f3ee;
            --hiw-accent: #2d6668;
            --hiw-copy: #2f3132;
            --hiw-muted: #5a6166;
            margin-top: 100px;
            padding: 72px 20px;
            background: radial-gradient(circle at 20% 20%, #f5f5f5 0%, var(--hiw-page-bg) 45%, #e5e6e8 100%);
        }

        .howitworks h1 {
            text-align: center;
            /* color: #193c3e; */
            margin-bottom: 42px;
        }

        .howitworks-showcase {
            max-width: 80%;
            margin: 0 auto 28px;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 14px 40px rgba(17, 24, 39, 0.08);
        }

        .howitworks .howitworks-flex {
            background: transparent;
            width: 100%;
            height: auto;
            margin: 0;
            border-radius: 0;
        }

        .howitworks-panel {
            width: 100%;
            /* background: var(--hiw-card-bg); */
            padding: clamp(2rem, 4vw, 4rem);
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 100%;
        }

        .howitworks-kicker {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: #1f2428;
            margin-bottom: 14px;
            display: inline-block;
        }

        .howitworks-title {
            color: #0028aa;
            font-size: clamp(1.7rem, 3vw, 2.65rem);
            line-height: 1.18;
            margin-bottom: 20px;
            text-transform: uppercase;
            font-weight: 500;
        }

        .howitworks-desc {
            color: var(--hiw-copy);
            font-size: 1.04rem;
            line-height: 1.75;
            max-width: 42ch;
            margin-bottom: 26px;
        }

        .howitworks-btn {
            width: fit-content;
            border: 2px solid #0028aa;
            color: #0028aa;
            background: transparent;
            font-weight: 600;
            letter-spacing: 0.03em;
            padding: 10px 22px;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .howitworks-btn:hover {
            background: #0028aa;
            color: #ffffff;
        }

        .howitworks-media {
            width: 100%;
            min-height: 100%;
            background: #d7c9b5;
        }

        .howitworks-media img {
            width: 100%;
            height: 100%;
            min-height: 330px;
            object-fit: cover;
            border-radius: 0;
        }

        @media (max-width: 991.98px) {
            .howitworks {
                padding: 56px 16px;
            }

            .howitworks-panel {
                padding: 2rem 1.5rem;
            }

            .howitworks-desc {
                max-width: 100%;
            }

            .howitworks-media img {
                min-height: 260px;
            }
        }

        [dir="rtl"] .banner .row,
        [dir="rtl"] .banner-stat-card,
        [dir="rtl"] .banner-stat-card h3,
        [dir="rtl"] .banner-stat-card span {
            text-align: center !important;
        }

        [dir="rtl"] .howitworks-panel {
            align-items: flex-start;
            text-align: right;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS (Animate On Scroll)
            AOS.init({
                duration: 1000,
                // once: true,
                offset: 120,
            });

            // JavaScript for Navbar background change on scroll and mobile toggle
            const navbar = document.querySelector('.navbar');
            const navbarToggler = document.querySelector('.navbar-toggler');

            // Function to handle scroll
            function handleScroll() {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    // Only remove scrolled class if the mobile menu is not open
                    if (!navbar.classList.contains('navbar-mobile-open')) {
                        navbar.classList.remove('scrolled');
                    }
                }
            }

            // Function to handle toggler click
            function handleToggle() {
                navbar.classList.toggle('navbar-mobile-open');
                // If we are at the top of the page, the 'scrolled' class might not be present
                // so we need to ensure the background appears when the menu is opened.
                if (navbar.classList.contains('navbar-mobile-open')) {
                    navbar.classList.add('scrolled');
                } else {
                    // If we close the menu and are at the top, remove the background
                    handleScroll();
                }
            }

            window.addEventListener('scroll', handleScroll);
            navbarToggler.addEventListener('click', handleToggle);
        });
    </script>
</body>

</html>
