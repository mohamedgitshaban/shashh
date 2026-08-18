<!DOCTYPE html><!--[if IE]>  <html class="pdf24_ie"> <![endif]-->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Shashh</title>

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
    <style>
        .navbar {
            background-color: #ffffff !important;
            transition: background-color 0.3s ease-in-out;
        }
        .nav-link {
            color: #0032d6 !important;
        }

        .nav-link:hover {
            color: #001147;
        }
    </style>
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

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card border-0">
                        <h2  class="primary">{{ __('messages.contact_form_title') }}</h2>
                        <p class="text-muted">{{ __('messages.contact_form_subtitle') }}</p>
                        <form action="/contact" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('messages.contact_form_name') }}</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">{{ __('messages.contact_form_email') }}</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">{{ __('messages.contact_form_phone') }}</label>
                                    <input type="tel" class="form-control" id="phone" name="phone">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">{{ __('messages.contact_form_message') }}</label>
                                <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">{{ __('messages.contact_form_submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                        <iframe style="width: 100%;
    height: 100%;" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d13807.691931047362!2d31.3398!3d30.096392!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x145815f1026341ad%3A0xc4ed2f9fe11d252c!2s10%20Srabis%2C%20Almazah%2C%20Heliopolis%2C%20Cairo%20Governorate%204460376!5e0!3m2!1sen!2seg!4v1696926107761!5m2!1sen!2seg" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </section>

    <footer class='footer' data-aos='fade-up'>
        <div class='container-fluid'>
            <div class='row g-4'>
                <div class='col-12 col-md-6 col-lg-3'>
                    <div class='footer-panel'>
                        <a class='brand mb-3 d-inline-block' href='/'><img src="{{ asset('images/logo.png') }}"
                                alt='Shashh Logo'></a>
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
                        <p>{{ __('messages.footer_phone') }}: <a href='tel:+966542594202'>+966 54 259 4202</a></p>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.min.js"
        integrity="sha512-nKXmKvJyiGQy343jatQlzDprflyB5c+tKCzGP3Uq67v+lmzfnZUi/ZT+fc6ITZfSC5HhaBKUIvr/nTLCV+7F+Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
