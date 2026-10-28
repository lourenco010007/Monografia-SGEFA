<?php

namespace Tests\Feature;

use App\Models\Contador;
use App\Models\Tarifa;
use App\Models\User;
use App\Models\Utente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ResourceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_phase_two_tables_have_only_the_specified_columns(): void
    {
        $expectedColumns = [
            'utentes' => ['id', 'utilizador_id', 'nome', 'telefone', 'bairro', 'morada', 'estado'],
            'contadores' => ['id', 'utente_id', 'numero_serie', 'localizacao', 'data_instalacao', 'estado'],
            'tarifas' => ['id', 'descricao', 'preco_m3', 'taxa_multa_pct', 'prazo_dias', 'vigente_desde'],
        ];

        foreach ($expectedColumns as $table => $expected) {
            $actual = Schema::getColumnListing($table);
            sort($expected);
            sort($actual);

            $this->assertSame($expected, $actual, "Unexpected columns in {$table}.");
        }
    }

    public function test_administrator_can_open_each_phase_two_catalogue(): void
    {
        $administrator = User::factory()->admin()->create();

        $this->actingAs($administrator)->get('/utentes')->assertOk()->assertSee('Utentes');
        $this->get('/contadores')->assertOk()->assertSee('Contadores');
        $this->get('/tarifas')->assertOk()->assertSee('Tarifas');
    }

    public function test_administrator_can_register_and_update_a_tenant(): void
    {
        $administrator = User::factory()->admin()->create();
        $portalUser = User::factory()->create();

        $this->actingAs($administrator)
            ->get('/utentes/create')
            ->assertOk()
            ->assertSee('Registar utente');

        $this->post('/utentes', [
            'utilizador_id' => $portalUser->id,
            'nome' => 'Maria Utente',
            'telefone' => '991234567',
            'bairro' => 'Central',
            'morada' => 'Rua da Escola, casa 12',
            'estado' => Utente::STATE_ACTIVE,
        ])->assertRedirect(route('utentes.index'));

        $utente = Utente::query()->where('nome', 'Maria Utente')->firstOrFail();
        $this->assertSame($utente->id, $portalUser->fresh()->utente->id);

        $this->put(route('utentes.update', $utente), [
            'utilizador_id' => (string) $portalUser->id,
            'nome' => 'Maria Actualizada',
            'telefone' => '991234568',
            'bairro' => 'Novo Bairro',
            'morada' => 'Rua Nova, casa 7',
            'estado' => Utente::STATE_INACTIVE,
        ])->assertRedirect(route('utentes.index'));

        $this->assertSame('Maria Actualizada', $utente->fresh()->nome);
        $this->assertSame(Utente::STATE_INACTIVE, $utente->fresh()->estado);
    }

    public function test_portal_account_can_only_be_linked_to_one_tenant(): void
    {
        $administrator = User::factory()->admin()->create();
        $portalUser = User::factory()->create();
        $this->actingAs($administrator);

        $tenantData = [
            'utilizador_id' => $portalUser->id,
            'nome' => 'Utente Um',
            'telefone' => '991111111',
            'bairro' => 'Bairro A',
            'morada' => 'Rua A',
            'estado' => Utente::STATE_ACTIVE,
        ];

        $this->post('/utentes', $tenantData)->assertRedirect(route('utentes.index'));

        $tenantData['nome'] = 'Utente Dois';

        $this->post('/utentes', $tenantData)
            ->assertSessionHasErrors('utilizador_id');

        $this->assertDatabaseCount('utentes', 1);
    }

    public function test_administrator_can_register_and_update_a_meter(): void
    {
        $administrator = User::factory()->admin()->create();
        $utente = Utente::query()->create([
            'nome' => 'Utente de teste',
            'telefone' => '990000001',
            'bairro' => 'Bairro',
            'morada' => 'Rua de teste',
            'estado' => Utente::STATE_ACTIVE,
        ]);

        $this->actingAs($administrator)
            ->get('/contadores/create')
            ->assertOk()
            ->assertSee('Registar contador');

        $this->post('/contadores', [
            'utente_id' => $utente->id,
            'numero_serie' => 'SERIE-0001',
            'localizacao' => 'Pátio',
            'data_instalacao' => '2026-05-01',
            'estado' => Contador::STATE_ACTIVE,
        ])->assertRedirect(route('contadores.index'));

        $contador = Contador::query()->where('numero_serie', 'SERIE-0001')->firstOrFail();
        $this->assertSame($utente->id, $contador->utente->id);

        $this->put(route('contadores.update', $contador), [
            'utente_id' => $utente->id,
            'numero_serie' => 'SERIE-0001',
            'localizacao' => 'Garagem',
            'data_instalacao' => '2026-05-01',
            'estado' => Contador::STATE_INACTIVE,
        ])->assertRedirect(route('contadores.index'));

        $this->assertSame('Garagem', $contador->fresh()->localizacao);
        $this->assertSame(Contador::STATE_INACTIVE, $contador->fresh()->estado);
    }

    public function test_meter_serial_number_must_be_unique(): void
    {
        $administrator = User::factory()->admin()->create();
        $utente = Utente::query()->create([
            'nome' => 'Utente de teste',
            'telefone' => '990000002',
            'bairro' => 'Bairro',
            'morada' => 'Rua de teste',
            'estado' => Utente::STATE_ACTIVE,
        ]);
        Contador::query()->create([
            'utente_id' => $utente->id,
            'numero_serie' => 'SERIE-DUPLICADA',
            'localizacao' => 'Pátio',
            'data_instalacao' => '2026-05-01',
            'estado' => Contador::STATE_ACTIVE,
        ]);

        $this->actingAs($administrator)
            ->post('/contadores', [
                'utente_id' => $utente->id,
                'numero_serie' => 'SERIE-DUPLICADA',
                'localizacao' => 'Garagem',
                'data_instalacao' => '2026-05-02',
                'estado' => Contador::STATE_ACTIVE,
            ])
            ->assertSessionHasErrors('numero_serie');

        $this->assertDatabaseCount('contadores', 1);
    }

    public function test_administrator_can_register_a_tariff_with_a_unique_effective_date(): void
    {
        $administrator = User::factory()->admin()->create();

        $this->actingAs($administrator)
            ->get('/tarifas/create')
            ->assertOk()
            ->assertSee('Registar tarifa');

        $tariffData = [
            'descricao' => 'Tarifa geral',
            'preco_m3' => '25.50',
            'taxa_multa_pct' => '10.00',
            'prazo_dias' => '30',
            'vigente_desde' => '2026-10-28',
        ];

        $this->post('/tarifas', $tariffData)->assertRedirect(route('tarifas.index'));

        $tarifa = Tarifa::query()->firstOrFail();
        $this->assertSame('25.50', $tarifa->preco_m3);
        $this->assertSame('10.00', $tarifa->taxa_multa_pct);
        $this->assertSame(30, $tarifa->prazo_dias);

        $this->post('/tarifas', $tariffData)
            ->assertSessionHasErrors('vigente_desde');

        $this->assertDatabaseCount('tarifas', 1);
    }

    public function test_non_admin_profiles_cannot_manage_phase_two_entities(): void
    {
        $agent = User::factory()->agent()->create();

        foreach (['/utentes', '/contadores', '/tarifas'] as $path) {
            $this->actingAs($agent)->get($path)->assertForbidden();
        }
    }

    public function test_admin_policy_does_not_authorize_deleting_historical_entities(): void
    {
        $administrator = User::factory()->admin()->create();
        $utente = Utente::query()->create([
            'nome' => 'Utente protegido',
            'telefone' => '990000003',
            'bairro' => 'Bairro',
            'morada' => 'Rua de teste',
            'estado' => Utente::STATE_ACTIVE,
        ]);
        $contador = Contador::query()->create([
            'utente_id' => $utente->id,
            'numero_serie' => 'SERIE-PROTEGIDA',
            'localizacao' => 'Pátio',
            'data_instalacao' => '2026-05-03',
            'estado' => Contador::STATE_ACTIVE,
        ]);
        $tarifa = Tarifa::query()->create([
            'descricao' => 'Tarifa histórica',
            'preco_m3' => '10.00',
            'taxa_multa_pct' => '5.00',
            'prazo_dias' => 20,
            'vigente_desde' => '2026-01-01',
        ]);

        $this->assertFalse(Gate::forUser($administrator)->allows('delete', $utente));
        $this->assertFalse(Gate::forUser($administrator)->allows('delete', $contador));
        $this->assertFalse(Gate::forUser($administrator)->allows('delete', $tarifa));
    }
}
