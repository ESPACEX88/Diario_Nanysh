<?php

namespace App\Http\Controllers;

use App\Models\MotivationalQuote;
use Inertia\Inertia;

class MotivationalQuoteController extends Controller
{
    public function daily()
    {
        $quote = MotivationalQuote::getDailyQuote();

        return response()->json([
            'quote' => $quote,
        ]);
    }
}
