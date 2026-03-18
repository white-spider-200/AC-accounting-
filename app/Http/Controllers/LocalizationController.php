<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\App;

class LocalizationController extends Controller
{
    public function lang($locale)
    {
        if (! in_array($locale, ['en', 'ar'], true)) {
            $locale = 'en';
        }

        App::setLocale($locale);
        session()->put('locale', $locale);
        return redirect()->back();
    }
}
