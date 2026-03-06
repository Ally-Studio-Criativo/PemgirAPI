<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingPageText;

class LandingPageTextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $texts = [
            // Seção: Hero Video
            [
                'section' => 'hero_video',
                'key' => 'title',
                'text_pt' => 'A Maior variedade em malhas, com a agilidade que você precisa!',
                'text_en' => 'The widest variety in fabrics, with the agility you need!',
                'text_es' => '¡La mayor variedad en telas, con la agilidad que necesitas!',
            ],
            [
                'section' => 'hero_video',
                'key' => 'button',
                'text_pt' => 'Conheça agora nossos produtos',
                'text_en' => 'Check out our products now',
                'text_es' => 'Conozca ahora nuestros productos',
            ],

            // Seção: Sobre a Empresa (About Company)
            [
                'section' => 'about_company',
                'key' => 'subtitle',
                'text_pt' => 'CONHEÇA MAIS SOBRE A PEMGIR',
                'text_en' => 'LEARN MORE ABOUT PEMGIR',
                'text_es' => 'CONOZCA MÁS SOBRE PEMGIR',
            ],
            [
                'section' => 'about_company',
                'key' => 'title',
                'text_pt' => 'SOBRE A EMPRESA',
                'text_en' => 'ABOUT THE COMPANY',
                'text_es' => 'SOBRE LA EMPRESA',
            ],
            [
                'section' => 'about_company',
                'key' => 'description',
                'text_pt' => 'Com sede em Brusque (SC), a PEMGIR é uma das maiores distribuidoras de malhas em pronta entrega da região. Conta com um parque fabril de mais de 7 mil m² e uma rede de mais de 40 representantes em todo o Brasil.',
                'text_en' => 'Based in Brusque (SC), PEMGIR is one of the largest ready-to-deliver fabric distributors in the region. It has a manufacturing facility of more than 7,000 m² and a network of more than 40 representatives throughout Brazil.',
                'text_es' => 'Con sede en Brusque (SC), PEMGIR es uno de los mayores distribuidores de telas en entrega inmediata de la región. Cuenta con un parque fabril de más de 7 mil m² y una red de más de 40 representantes en todo Brasil.',
            ],

            // Seção: Mais de 130 Produtos (About Products)
            [
                'section' => 'about_products',
                'key' => 'subtitle',
                'text_pt' => 'VARIEDADE, QUALIDADE E AGILIDADE',
                'text_en' => 'VARIETY, QUALITY AND AGILITY',
                'text_es' => 'VARIEDAD, CALIDAD Y AGILIDAD',
            ],
            [
                'section' => 'about_products',
                'key' => 'title',
                'text_pt' => 'MAIS DE 130 PRODUTOS',
                'text_en' => 'MORE THAN 130 PRODUCTS',
                'text_es' => 'MÁS DE 130 PRODUCTOS',
            ],
            [
                'section' => 'about_products',
                'key' => 'description',
                'text_pt' => 'Com um portfólio de mais de 130 produtos, a empresa oferece variedade, qualidade e agilidade para confeccionistas e revendedores que buscam soluções rápidas e confiáveis.',
                'text_en' => 'With a portfolio of more than 130 products, the company offers variety, quality and agility for manufacturers and resellers seeking fast and reliable solutions.',
                'text_es' => 'Con un portafolio de más de 130 productos, la empresa ofrece variedad, calidad y agilidad para confeccionistas y revendedores que buscan soluciones rápidas y confiables.',
            ],
        ];

        foreach ($texts as $text) {
            LandingPageText::updateOrCreate(
                ['section' => $text['section'], 'key' => $text['key']],
                $text
            );
        }
    }
}
