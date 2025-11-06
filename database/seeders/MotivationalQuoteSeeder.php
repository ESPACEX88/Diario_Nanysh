<?php

namespace Database\Seeders;

use App\Models\MotivationalQuote;
use Illuminate\Database\Seeder;

class MotivationalQuoteSeeder extends Seeder
{
    public function run(): void
    {
        $quotes = [
            [
                'quote' => 'Eres más fuerte de lo que crees y más valiente de lo que imaginas.',
                'author' => 'Anónimo',
                'category' => 'motivation',
            ],
            [
                'quote' => 'Cada día es una nueva oportunidad para ser la mejor versión de ti misma.',
                'author' => 'Anónimo',
                'category' => 'motivation',
            ],
            [
                'quote' => 'La belleza comienza en el momento en que decides ser tú misma.',
                'author' => 'Coco Chanel',
                'category' => 'beauty',
            ],
            [
                'quote' => 'Eres única, eres especial, eres increíble tal como eres.',
                'author' => 'Anónimo',
                'category' => 'love',
            ],
            [
                'quote' => 'No esperes el momento perfecto, comienza ahora.',
                'author' => 'Anónimo',
                'category' => 'motivation',
            ],
            [
                'quote' => 'Tu sonrisa es tu mejor maquillaje.',
                'author' => 'Anónimo',
                'category' => 'beauty',
            ],
            [
                'quote' => 'Cree en ti misma y todo será posible.',
                'author' => 'Anónimo',
                'category' => 'motivation',
            ],
            [
                'quote' => 'Eres hermosa por dentro y por fuera.',
                'author' => 'Anónimo',
                'category' => 'love',
            ],
            [
                'quote' => 'Cada pequeño paso te acerca a tus sueños.',
                'author' => 'Anónimo',
                'category' => 'motivation',
            ],
            [
                'quote' => 'Mereces todo lo bueno que la vida tiene para ofrecer.',
                'author' => 'Anónimo',
                'category' => 'love',
            ],
            [
                'quote' => 'Tu felicidad es tu responsabilidad, tómala en serio.',
                'author' => 'Anónimo',
                'category' => 'happiness',
            ],
            [
                'quote' => 'Eres suficiente, tal como eres, en este momento.',
                'author' => 'Anónimo',
                'category' => 'love',
            ],
            [
                'quote' => 'El amor propio no es egoísmo, es sabiduría.',
                'author' => 'Anónimo',
                'category' => 'love',
            ],
            [
                'quote' => 'Cada día es una página en blanco, escribe una historia hermosa.',
                'author' => 'Anónimo',
                'category' => 'motivation',
            ],
            [
                'quote' => 'Eres más valiente de lo que crees y más fuerte de lo que pareces.',
                'author' => 'A.A. Milne',
                'category' => 'motivation',
            ],
            [
                'quote' => 'Tu valor no disminuye por la incapacidad de otros de verte.',
                'author' => 'Anónimo',
                'category' => 'self-worth',
            ],
            [
                'quote' => 'Hoy es un buen día para tener un buen día.',
                'author' => 'Anónimo',
                'category' => 'happiness',
            ],
            [
                'quote' => 'Eres la única persona que puede cambiar tu vida.',
                'author' => 'Anónimo',
                'category' => 'motivation',
            ],
            [
                'quote' => 'La confianza en ti misma es el mejor accesorio.',
                'author' => 'Anónimo',
                'category' => 'beauty',
            ],
            [
                'quote' => 'Mereces amor, respeto y todo lo que deseas.',
                'author' => 'Anónimo',
                'category' => 'love',
            ],
        ];

        foreach ($quotes as $quote) {
            MotivationalQuote::firstOrCreate(
                ['quote' => $quote['quote']],
                array_merge($quote, ['is_active' => true])
            );
        }
    }
}
