<?php

namespace Tests\Feature\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CardComponentTest extends TestCase
{
    /** @test */
    public function card_component_renders_correctly()
    {
        $view = $this->component('components.ui.card', [], 'Card Content');

        $view->assertSee('Card Content', false);
        $view->assertContains('bg-white', false);
        $view->assertContains('rounded-lg', false);
    }

    /** @test */
    public function card_component_renders_with_title()
    {
        $view = $this->component('components.ui.card', [
            'title' => 'Test Card Title',
        ], 'Card Content');
        
        $view->assertSee('Test Card Title', false);
        $view->assertSee('Card Content', false);
    }

    /** @test */
    public function card_component_renders_with_subtitle()
    {
        $view = $this->component('components.ui.card', [
            'title' => 'Test Card Title',
            'subtitle' => 'Test Card Subtitle',
        ], 'Card Content');
        
        $view->assertSee('Test Card Title', false);
        $view->assertSee('Test Card Subtitle', false);
        $view->assertSee('Card Content', false);
    }

    /** @test */
    public function card_component_renders_with_different_padding()
    {
        // Small padding
        $view = $this->component('components.ui.card', [
            'padding' => 'sm',
        ], 'Small Padding Card');
        
        $view->assertSee('Small Padding Card', false);
        $view->assertContains('p-4', false);
        
        // Large padding
        $view = $this->component('components.ui.card', [
            'padding' => 'lg',
        ], 'Large Padding Card');
        
        $view->assertSee('Large Padding Card', false);
        $view->assertContains('p-8', false);
        
        // No padding
        $view = $this->component('components.ui.card', [
            'padding' => 'none',
        ], 'No Padding Card');
        
        $view->assertSee('No Padding Card', false);
        $view->assertDontContain('p-6', false);
    }

    /** @test */
    public function card_component_renders_with_border()
    {
        $view = $this->component('components.ui.card', [
            'border' => true,
        ], 'Bordered Card');
        
        $view->assertSee('Bordered Card', false);
        $view->assertContains('border', false);
        $view->assertContains('border-neutral-200', false);
    }

    /** @test */
    public function card_component_renders_with_shadow_variations()
    {
        // Default shadow
        $view = $this->component('components.ui.card', [
            'shadow' => true,
        ], 'Shadow Card');
        
        $view->assertSee('Shadow Card', false);
        $view->assertContains('shadow-card', false);
        
        // Large shadow
        $view = $this->component('components.ui.card', [
            'shadow' => 'lg',
        ], 'Large Shadow Card');
        
        $view->assertSee('Large Shadow Card', false);
        $view->assertContains('shadow-lg', false);
        
        // No shadow
        $view = $this->component('components.ui.card', [
            'shadow' => false,
        ], 'No Shadow Card');
        
        $view->assertSee('No Shadow Card', false);
        $view->assertDontContain('shadow-', false);
    }

    /** @test */
    public function card_component_renders_with_header_and_footer()
    {
        $view = $this->component('components.ui.card', [
            'header' => true,
            'footer' => true,
        ], 'Card with Header and Footer');
        
        $view->assertSee('Card with Header and Footer', false);
        // Check for header and footer div structures
        $view->assertContains('card-header', false);
        $view->assertContains('card-footer', false);
    }
}
