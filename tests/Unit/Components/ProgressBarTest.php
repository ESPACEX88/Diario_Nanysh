<?php

namespace Tests\Unit\Components;

use Tests\TestCase;

class ProgressBarTest extends TestCase
{
    /**
     * Test que verifica la existencia del componente ProgressBar
     */
    public function test_progress_bar_component_exists(): void
    {
        $componentPath = resource_path('js/Components/ProgressBar.vue');
        
        $this->assertFileExists($componentPath);
        
        $content = file_get_contents($componentPath);
        
        $this->assertStringContainsString('script setup', $content);
        $this->assertStringContainsString('ProgressBarProps', $content);
    }

    /**
     * Test que verifica las props requeridas
     */
    public function test_progress_bar_has_required_props(): void
    {
        $componentPath = resource_path('js/Components/ProgressBar.vue');
        $content = file_get_contents($componentPath);

        $requiredProps = ['progress', 'color', 'size', 'animated'];
        
        foreach ($requiredProps as $prop) {
            $this->assertStringContainsString($prop, $content, "La prop '{$prop}' no está definida");
        }
    }

    /**
     * Test que verifica los tamaños disponibles
     */
    public function test_progress_bar_has_size_variants(): void
    {
        $componentPath = resource_path('js/Components/ProgressBar.vue');
        $content = file_get_contents($componentPath);

        $sizes = ['sm', 'md', 'lg'];
        
        foreach ($sizes as $size) {
            $this->assertStringContainsString($size, $content, "El tamaño '{$size}' no está disponible");
        }
    }

    /**
     * Test que verifica el efecto de animación
     */
    public function test_progress_bar_has_animation(): void
    {
        $componentPath = resource_path('js/Components/ProgressBar.vue');
        $content = file_get_contents($componentPath);

        $this->assertStringContainsString('transition-all', $content);
        $this->assertStringContainsString('duration-1000', $content);
        $this->assertStringContainsString('animate-shine', $content);
    }

    /**
     * Test que verifica el cálculo de progreso clamp
     */
    public function test_progress_bar_clamps_values(): void
    {
        $componentPath = resource_path('js/Components/ProgressBar.vue');
        $content = file_get_contents($componentPath);

        $this->assertStringContainsString('clampedProgress', $content);
        $this->assertStringContainsString('Math.min', $content);
        $this->assertStringContainsString('Math.max', $content);
    }
}
