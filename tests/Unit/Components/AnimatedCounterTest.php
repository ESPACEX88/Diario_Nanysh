<?php

namespace Tests\Unit\Components;

use Tests\TestCase;

class AnimatedCounterTest extends TestCase
{
    /**
     * Test que verifica la existencia del componente AnimatedCounter
     */
    public function test_animated_counter_component_exists(): void
    {
        $componentPath = resource_path('js/Components/AnimatedCounter.vue');
        
        $this->assertFileExists($componentPath);
        
        $content = file_get_contents($componentPath);
        
        $this->assertStringContainsString('script setup', $content);
        $this->assertStringContainsString('defineProps', $content);
        $this->assertStringContainsString('animated', strtolower($content));
    }

    /**
     * Test que verifica las props del componente
     */
    public function test_animated_counter_has_required_props(): void
    {
        $componentPath = resource_path('js/Components/AnimatedCounter.vue');
        $content = file_get_contents($componentPath);

        $requiredProps = ['value', 'duration', 'prefix', 'suffix', 'decimals'];
        
        foreach ($requiredProps as $prop) {
            $this->assertStringContainsString($prop, $content, "La prop '{$prop}' no está definida");
        }
    }

    /**
     * Test que verifica la animación easeOutQuart
     */
    public function test_animated_counter_has_easing_function(): void
    {
        $componentPath = resource_path('js/Components/AnimatedCounter.vue');
        $content = file_get_contents($componentPath);

        $this->assertStringContainsString('easeOutQuart', $content);
        $this->assertStringContainsString('requestAnimationFrame', $content);
    }
}
