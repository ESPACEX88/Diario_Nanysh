<?php

namespace Tests\Unit\Components;

use Tests\TestCase;

class StatCardTest extends TestCase
{
    /**
     * Test que verifica la existencia del componente StatCard
     */
    public function test_stat_card_component_exists(): void
    {
        $componentPath = resource_path('js/Components/StatCard.vue');
        
        $this->assertFileExists($componentPath);
        
        $content = file_get_contents($componentPath);
        
        $this->assertStringContainsString('script setup', $content);
        $this->assertStringContainsString('StatCardProps', $content);
    }

    /**
     * Test que verifica las props requeridas
     */
    public function test_stat_card_has_required_props(): void
    {
        $componentPath = resource_path('js/Components/StatCard.vue');
        $content = file_get_contents($componentPath);

        $requiredProps = ['title', 'value', 'icon', 'trend', 'color'];
        
        foreach ($requiredProps as $prop) {
            $this->assertStringContainsString($prop, $content, "La prop '{$prop}' no está definida");
        }
    }

    /**
     * Test que verifica los colores disponibles
     */
    public function test_stat_card_has_color_variants(): void
    {
        $componentPath = resource_path('js/Components/StatCard.vue');
        $content = file_get_contents($componentPath);

        $colors = ['rose', 'purple', 'blue', 'green', 'orange'];
        
        foreach ($colors as $color) {
            $this->assertStringContainsString($color, $content, "El color '{$color}' no está disponible");
        }
    }

    /**
     * Test que verifica el indicador de tendencia
     */
    public function test_stat_card_has_trend_indicator(): void
    {
        $componentPath = resource_path('js/Components/StatCard.vue');
        $content = file_get_contents($componentPath);

        $this->assertStringContainsString('trendColor', $content);
        $this->assertStringContainsString('↑', $content);
        $this->assertStringContainsString('↓', $content);
    }
}
