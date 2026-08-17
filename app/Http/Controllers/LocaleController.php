<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function setLocale(Request $request, string $locale): RedirectResponse
    {
        $allowedLocales = ['en', 'ar'];

        if (! in_array($locale, $allowedLocales, true)) {
            return redirect()->back();
        }

        Session::put('locale', $locale);
        App::setLocale($locale);

        return redirect()->back();
    }
}
