<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreeFermes;
use Tests\TestCase;

class AutorisationTest extends TestCase
{
    use RefreshDatabase, CreeFermes;

    public function test_un_agent_ne_peut_pas_creer_de_campagne(): void
    {
        $f = $this->ferme();

        $this->actingAs($f['agent'])->post(route('campagnes.store'), [])->assertForbidden();
    }

    public function test_un_agent_ne_voit_pas_les_finances(): void
    {
        $f = $this->ferme();

        $this->actingAs($f['agent'])->get(route('finance.index'))->assertForbidden();
        $this->actingAs($f['responsable'])->get(route('finance.index'))->assertOk();
    }

    public function test_seul_ladmin_gere_les_utilisateurs(): void
    {
        $f = $this->ferme();

        $this->actingAs($f['responsable'])->get(route('users.index'))->assertForbidden();
        $this->actingAs($f['admin'])->get(route('users.index'))->assertOk();
    }

    public function test_les_invites_sont_redirigees_vers_la_connexion(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_connexion_et_deconnexion(): void
    {
        $f = $this->ferme();

        $this->post(route('login.attempt'), ['email' => $f['agent']->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($f['agent']);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
