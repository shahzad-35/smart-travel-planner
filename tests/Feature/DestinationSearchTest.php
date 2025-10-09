<?php

namespace Tests\Feature;

use App\Livewire\DestinationSearch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DestinationSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_destinations_page_requires_authentication()
    {
        $response = $this->get('/destinations');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_destinations_page()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/destinations');
        $response->assertStatus(200);
        $response->assertSeeLivewire(DestinationSearch::class);
    }

    public function test_search_query_updates_component_state()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(DestinationSearch::class)
            ->set('searchQuery', 'United States')
            ->assertSet('searchQuery', 'United States')
            ->assertSet('isLoading', true);
    }

    public function test_clear_search_functionality()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(DestinationSearch::class)
            ->set('searchQuery', 'United States')
            ->call('clearSearch')
            ->assertSet('searchQuery', '')
            ->assertSet('searchResults', [])
            ->assertSet('isLoading', false);
    }

    public function test_component_renders_correctly()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(DestinationSearch::class)
            ->assertSee('Discover Destinations')
            ->assertSee('Search for countries, capitals, or regions...')
            ->assertSee('Start exploring destinations');
    }
}