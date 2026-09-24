<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Services\Rules\EssentialBookAssembler;
use Tests\TestCase;

class EssentialBookAssemblerTest extends TestCase
{
    public function test_assembles_pages_in_menu_order_and_strips_krefs(): void
    {
        $path = sys_get_temp_dir().'/krosmoz-essentiel-'.uniqid('', true).'.php';
        file_put_contents($path, <<<'PHP'
<?php
return [
    'z' => [
        'title' => 'Deuxième',
        'slug' => 'essentiels-deux',
        'menu_order' => 20,
        'intro_title' => 'Intro 2',
        'intro_html' => '<p>Suite.</p>',
        'sections' => [],
    ],
    'a' => [
        'title' => 'Bien démarrer',
        'slug' => 'essentiels-bien-demarrer',
        'menu_order' => 10,
        'intro_title' => 'À quoi ça sert',
        'intro_html' => '<p>Voir [[kref:page:regles-1-introduction|Règles]].</p>',
        'sections' => [
            [
                'slug' => 'jets',
                'title' => 'Jets',
                'html' => '<p>DD [[kref:characteristic:action_points_creature|PA]].</p>',
            ],
        ],
    ],
];
PHP);

        try {
            $html = (new EssentialBookAssembler($path))->toHtml();
            $this->assertStringContainsString('Krosmoz JDR — L’Essentiel', $html);
            $this->assertStringContainsString('<h1>Bien démarrer</h1>', $html);
            $this->assertStringContainsString('<h2>Jets</h2>', $html);
            $this->assertStringContainsString('Voir Règles.', $html);
            $this->assertStringContainsString('DD PA.', $html);
            $this->assertStringNotContainsString('[[kref:', $html);
            $this->assertLessThan(
                strpos($html, 'Deuxième'),
                strpos($html, 'Bien démarrer')
            );
        } finally {
            @unlink($path);
        }
    }
}
