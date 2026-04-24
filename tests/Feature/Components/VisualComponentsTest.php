<?php

namespace Tests\Feature\Components;

use Tests\TestCase;

class VisualComponentsTest extends TestCase
{
    /**
     * Test que verifica que todos los componentes visuales existen
     */
    public function test_all_visual_components_exist(): void
    {
        $components = [
            'AnimatedCounter',
            'StatCard',
            'ProgressBar',
            'SkeletonLoader',
        ];

        foreach ($components as $component) {
            $path = resource_path("js/Components/{$component}.vue");
            $this->assertFileExists($path, "El componente {$component} no existe");
        }
    }

    /**
     * Test que verifica que los componentes tienen estructura Vue 3 válida
     */
    public function test_components_have_valid_vue3_structure(): void
    {
        $components = [
            'AnimatedCounter',
            'StatCard',
            'ProgressBar',
            'SkeletonLoader',
        ];

        foreach ($components as $component) {
            $path = resource_path("js/Components/{$component}.vue");
            $content = file_get_contents($path);

            // Verificar script setup
            $this->assertStringContainsString(
                '<script setup',
                $content,
                "{$component} no usa Vue 3 script setup"
            );

            // Verificar template
            $this->assertStringContainsString(
                '</template>',
                $content,
                "{$component} no tiene template"
            );

            // Verificar TypeScript
            $this->assertStringContainsString(
                'lang="ts"',
                $content,
                "{$component} no usa TypeScript"
            );
        }
    }

    /**
     * Test que verifica que los componentes usan Tailwind CSS
     */
    public function test_components_use_tailwind_css(): void
    {
        $components = [
            'AnimatedCounter' => ['bg-gradient-to-r', 'from-rose-500'],
            'StatCard' => ['rounded-3xl', 'backdrop-blur-xl'],
            'ProgressBar' => ['rounded-full', 'bg-gradient-to-r'],
            'SkeletonLoader' => ['bg-gray-200'],
        ];

        foreach ($components as $component => $classes) {
            $path = resource_path("js/Components/{$component}.vue");
            $content = file_get_contents($path);

            foreach ($classes as $class) {
                $this->assertStringContainsString(
                    $class,
                    $content,
                    "{$component} no usa la clase Tailwind '{$class}'"
                );
            }
        }
    }

    /**
     * Test que verifica animaciones personalizadas
     */
    public function test_components_have_custom_animations(): void
    {
        $animations = [
            'AnimatedCounter' => 'easeOutQuart',
            'ProgressBar' => 'animate-shine',
            'SkeletonLoader' => 'animate-shimmer',
        ];

        foreach ($animations as $component => $animation) {
            $path = resource_path("js/Components/{$component}.vue");
            $content = file_get_contents($path);

            $this->assertStringContainsString(
                $animation,
                $content,
                "{$component} no tiene la animación '{$animation}'"
            );
        }
    }

    /**
     * Test que verifica responsive design
     */
    public function test_components_are_responsive(): void
    {
        $components = ['StatCard', 'ProgressBar'];

        foreach ($components as $component) {
            $path = resource_path("js/Components/{$component}.vue");
            $content = file_get_contents($path);

            // Verificar clases responsive o flexbox/grid
            $hasResponsive = 
                str_contains($content, 'flex') ||
                str_contains($content, 'grid') ||
                str_contains($content, 'w-full') ||
                str_contains($content, 'md:') ||
                str_contains($content, 'lg:');

            $this->assertTrue(
                $hasResponsive,
                "{$component} no parece tener diseño responsive"
            );
        }
    }
}
