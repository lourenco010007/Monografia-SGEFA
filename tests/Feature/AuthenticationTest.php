<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_login_page_is_available_to_guests(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_user_table_has_only_the_specified_columns(): void
    {
        $columns = Schema::getColumnListing('utilizadores');
        sort($columns);

        $expected = ['activo', 'email', 'id', 'nome', 'password', 'perfil'];
        sort($expected);

        $this->assertSame($expected, $columns);
    }

    public function test_active_user_can_sign_in_and_sign_out(): void
    {
        $user = User::factory()->create([
            'email' => 'utente@example.test',
            'password' => 'correct-password',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'correct-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get('/painel')->assertOk()->assertSee($user->nome);

        $this->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_invalid_or_inactive_accounts_cannot_sign_in(): void
    {
        $inactiveUser = User::factory()->inactive()->create([
            'email' => 'inactivo@example.test',
            'password' => 'correct-password',
        ]);

        $this->post('/login', [
            'email' => $inactiveUser->email,
            'password' => 'correct-password',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', [
            'email' => $inactiveUser->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_session_is_ended_when_accessing_the_panel(): void
    {
        $user = User::factory()->inactive()->create();

        $this->actingAs($user)
            ->get('/painel')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_profile_middleware_restricts_admin_area(): void
    {
        $agent = User::factory()->agent()->create();

        $this->actingAs($agent)->get('/usuarios')->assertForbidden();
    }

    public function test_administrator_can_create_a_user_with_a_profile(): void
    {
        $administrator = User::factory()->admin()->create();

        $this->actingAs($administrator)
            ->get('/usuarios')
            ->assertOk()
            ->assertSee('Utilizadores');

        $this->get('/usuarios/create')
            ->assertOk()
            ->assertSee('Criar utilizador');

        $this->actingAs($administrator)
            ->post('/usuarios', [
                'nome' => 'Agente de teste',
                'email' => 'agente@example.test',
                'password' => 'secure-pass-123',
                'password_confirmation' => 'secure-pass-123',
                'perfil' => User::PROFILE_AGENT,
                'activo' => '1',
            ])
            ->assertRedirect(route('usuarios.index'));

        $user = User::query()->where('email', 'agente@example.test')->firstOrFail();
        $this->assertSame('Agente de teste', $user->nome);
        $this->assertSame(User::PROFILE_AGENT, $user->perfil);
        $this->assertTrue($user->activo);
        $this->assertTrue(Hash::check('secure-pass-123', $user->password));
    }

    public function test_administrator_can_deactivate_users_without_disabling_the_last_admin(): void
    {
        $administrator = User::factory()->admin()->create();
        $tenant = User::factory()->create();

        $this->actingAs($administrator)
            ->patch(route('usuarios.status', $tenant), ['activo' => '0'])
            ->assertRedirect(route('usuarios.index'));

        $this->assertFalse($tenant->fresh()->activo);

        $this->patch(route('usuarios.status', $administrator), ['activo' => '0'])
            ->assertSessionHasErrors('activo');

        $this->assertTrue($administrator->fresh()->activo);
    }

    public function test_user_policy_only_allows_a_tenant_to_view_their_own_account(): void
    {
        $tenant = User::factory()->create();
        $otherTenant = User::factory()->create();
        $administrator = User::factory()->admin()->create();

        $this->assertTrue(Gate::forUser($tenant)->allows('view', $tenant));
        $this->assertFalse(Gate::forUser($tenant)->allows('view', $otherTenant));
        $this->assertTrue(Gate::forUser($administrator)->allows('view', $otherTenant));
    }
}
