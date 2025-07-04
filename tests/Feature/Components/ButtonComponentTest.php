<?php

namespace Tests\Feature\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ButtonComponentTest extends TestCase
{
    /** @test */
    public function button_component_renders_correctly()
    {
        $view = $this->component('components.ui.button', [
            'type' => 'button',
            'variant' => 'primary',
        ], 'Click Me');

        $view->assertSee('Click Me', false);
        $view->assertContains('bg-primary-600', false);
        $view->assertContains('text-white', false);
    }

    /** @test */
    public function button_component_renders_with_different_variants()
    {
        // Secondary variant
        $view = $this->component('components.ui.button', [
            'variant' => 'secondary',
        ], 'Secondary Button');
        
        $view->assertSee('Secondary Button', false);
        $view->assertContains('bg-secondary-600', false);
        
        // Danger variant
        $view = $this->component('components.ui.button', [
            'variant' => 'danger',
        ], 'Danger Button');
        
        $view->assertSee('Danger Button', false);
        $view->assertContains('bg-danger-600', false);
        
        // Outline variant
        $view = $this->component('components.ui.button', [
            'variant' => 'outline-primary',
        ], 'Outline Button');
        
        $view->assertSee('Outline Button', false);
        $view->assertContains('border-primary-600', false);
        $view->assertContains('text-primary-600', false);
    }

    /** @test */
    public function button_component_renders_with_different_sizes()
    {
        // Small size
        $view = $this->component('components.ui.button', [
            'size' => 'sm',
        ], 'Small Button');
        
        $view->assertSee('Small Button', false);
        $view->assertContains('px-3 py-2 text-sm', false);
        
        // Large size
        $view = $this->component('components.ui.button', [
            'size' => 'lg',
        ], 'Large Button');
        
        $view->assertSee('Large Button', false);
        $view->assertContains('px-5 py-3 text-base', false);
    }

    /** @test */
    public function button_component_renders_with_rounded_corners()
    {
        $view = $this->component('components.ui.button', [
            'rounded' => true,
        ], 'Rounded Button');
        
        $view->assertSee('Rounded Button', false);
        $view->assertContains('rounded-full', false);
    }

    /** @test */
    public function button_component_renders_with_disabled_state()
    {
        $view = $this->component('components.ui.button', [
            'disabled' => true,
        ], 'Disabled Button');
        
        $view->assertSee('Disabled Button', false);
        $view->assertContains('disabled', false);
        $view->assertContains('opacity-65', false);
    }

    /** @test */
    public function button_component_renders_as_link()
    {
        $view = $this->component('components.ui.button', [
            'href' => '/test-url',
        ], 'Link Button');
        
        $view->assertSee('Link Button', false);
        $view->assertSee('href="/test-url"', false);
    }

    /** @test */
    public function button_component_renders_with_full_width()
    {
        $view = $this->component('components.ui.button', [
            'fullWidth' => true,
        ], 'Full Width Button');
        
        $view->assertSee('Full Width Button', false);
        $view->assertContains('w-full', false);
    }
}
