<?php

namespace Tests\Feature;

use App\Models\Contador;
use App\Models\Factura;
use App\Models\Leitura;
use App\Models\Tarifa;
use App\Models\User;
use App\Models\Utente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LeituraAndInvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_phase_three_tables_have_only_the_required_columns(): void
    {
        $expectedColumns = [
            'leituras' => [
                'id', 'contador_id', 'agente_id', 'leitura_anterior', 'leitura_actual', 'consumo_m3', 'data_leitura',
            ],
            'facturas' => [
                'id', 'leitura_id', 'utente_id', 'tarifa_id', 'numero', 'valor_total', 'data_emissao',
                'data_vencimento', 'estado',
            ],
        ];

        foreach ($expectedColumns as $table => $expected) {
            $actual = Schema::getColumnListing($table);
            sort($expected);
            sort($actual);

            $this->assertSame($expected, $actual, "Unexpected columns in {$table}.");
        }
    }

    public function test_first_reading_uses_zero_and_generates_an_invoice(): void
    {
        $agent = User::factory()->agent()->create();
        $contador = $this->createActiveMeter();
        $this->createTariff('2026-01-01', '12.50', 30);

        $this->actingAs($agent)
            ->get('/leituras/create')
            ->assertOk()
            ->assertSee('A primeira leitura de um contador começa em zero.');

        $this->post('/leituras', [
            'contador_id' => $contador->id,
            'leitura_actual' => '10.000',
            'data_leitura' => today()->toDateString(),
            'leitura_anterior' => '9.000',
        ])->assertRedirect();

        $leitura = Leitura::query()->firstOrFail();
        $factura = Factura::query()->firstOrFail();

        $this->assertSame('0.000', $leitura->leitura_anterior);
        $this->assertSame('10.000', $leitura->leitura_actual);
        $this->assertSame('10.000', $leitura->consumo_m3);
        $this->assertSame($agent->id, $leitura->agente_id);
        $this->assertSame($contador->utente_id, $factura->utente_id);
        $this->assertSame('125.00', $factura->valor_total);
        $this->assertSame('emitida', $factura->estado);
        $this->assertSame('F-'.str_pad((string) $leitura->id, 8, '0', STR_PAD_LEFT), $factura->numero);
        $this->assertSame(today()->toDateString(), $factura->data_emissao->toDateString());
        $this->assertSame(today()->addDays(30)->toDateString(), $factura->data_vencimento->toDateString());

        $this->get('/leituras')->assertOk()->assertSee($factura->numero);
    }

    public function test_next_reading_uses_the_previous_actual_and_tariff_effective_on_reading_date(): void
    {
        $agent = User::factory()->agent()->create();
        $contador = $this->createActiveMeter();
        $this->createTariff('2026-01-01', '5.00', 10);
        $effectiveTariff = $this->createTariff('2026-10-01', '20.00', 25);

        Leitura::query()->create([
            'contador_id' => $contador->id,
            'agente_id' => $agent->id,
            'leitura_anterior' => '0.000',
            'leitura_actual' => '10.250',
            'consumo_m3' => '10.250',
            'data_leitura' => '2026-10-10',
        ]);

        $this->actingAs($agent)
            ->post('/leituras', [
                'contador_id' => $contador->id,
                'leitura_actual' => '12.750',
                'data_leitura' => '2026-10-20',
            ])
            ->assertRedirect();

        $leitura = Leitura::query()->orderByDesc('id')->firstOrFail();
        $factura = $leitura->factura()->firstOrFail();

        $this->assertSame('10.250', $leitura->leitura_anterior);
        $this->assertSame('2.500', $leitura->consumo_m3);
        $this->assertSame($effectiveTariff->id, $factura->tarifa_id);
        $this->assertSame('50.00', $factura->valor_total);
        $this->assertSame(today()->addDays(25)->toDateString(), $factura->data_vencimento->toDateString());
    }

    public function test_reading_below_previous_value_is_rejected_without_creating_records(): void
    {
        $agent = User::factory()->agent()->create();
        $contador = $this->createActiveMeter();
        $this->createTariff('2026-01-01', '10.00', 30);
        Leitura::query()->create([
            'contador_id' => $contador->id,
            'agente_id' => $agent->id,
            'leitura_anterior' => '0.000',
            'leitura_actual' => '20.000',
            'consumo_m3' => '20.000',
            'data_leitura' => today()->subDay()->toDateString(),
        ]);

        $this->actingAs($agent)
            ->from('/leituras/create')
            ->post('/leituras', [
                'contador_id' => $contador->id,
                'leitura_actual' => '19.999',
                'data_leitura' => today()->toDateString(),
            ])
            ->assertRedirect('/leituras/create')
            ->assertSessionHasErrors('leitura_actual');

        $this->assertDatabaseCount('leituras', 1);
        $this->assertDatabaseCount('facturas', 0);
    }

    public function test_reading_date_must_be_after_previous_reading_and_not_before_installation(): void
    {
        $agent = User::factory()->agent()->create();
        $contador = $this->createActiveMeter();
        $this->createTariff('2026-01-01', '10.00', 30);
        Leitura::query()->create([
            'contador_id' => $contador->id,
            'agente_id' => $agent->id,
            'leitura_anterior' => '0.000',
            'leitura_actual' => '20.000',
            'consumo_m3' => '20.000',
            'data_leitura' => today()->subDay()->toDateString(),
        ]);

        $this->actingAs($agent)
            ->from('/leituras/create')
            ->post('/leituras', [
                'contador_id' => $contador->id,
                'leitura_actual' => '21.000',
                'data_leitura' => today()->subDay()->toDateString(),
            ])
            ->assertSessionHasErrors('data_leitura');

        $this->assertDatabaseCount('leituras', 1);
        $this->assertDatabaseCount('facturas', 0);
    }

    public function test_reading_date_cannot_precede_meter_installation(): void
    {
        $agent = User::factory()->agent()->create();
        $contador = $this->createActiveMeter();
        $this->createTariff('2026-01-01', '10.00', 30);

        $this->actingAs($agent)
            ->from('/leituras/create')
            ->post('/leituras', [
                'contador_id' => $contador->id,
                'leitura_actual' => '1.000',
                'data_leitura' => '2025-12-31',
            ])
            ->assertSessionHasErrors('data_leitura');

        $this->assertDatabaseCount('leituras', 0);
        $this->assertDatabaseCount('facturas', 0);
    }

    public function test_reading_without_an_effective_tariff_is_rejected_atomically(): void
    {
        $agent = User::factory()->agent()->create();
        $contador = $this->createActiveMeter();
        $this->createTariff(today()->addDay()->toDateString(), '10.00', 30);

        $this->actingAs($agent)
            ->from('/leituras/create')
            ->post('/leituras', [
                'contador_id' => $contador->id,
                'leitura_actual' => '5.000',
                'data_leitura' => today()->toDateString(),
            ])
            ->assertSessionHasErrors('data_leitura');

        $this->assertDatabaseCount('leituras', 0);
        $this->assertDatabaseCount('facturas', 0);
    }

    public function test_invoice_total_rounds_half_up_to_cents(): void
    {
        $agent = User::factory()->agent()->create();
        $contador = $this->createActiveMeter();
        $this->createTariff('2026-01-01', '1.00', 30);

        $this->actingAs($agent)->post('/leituras', [
            'contador_id' => $contador->id,
            'leitura_actual' => '0.005',
            'data_leitura' => today()->toDateString(),
        ])->assertRedirect();

        $this->assertSame('0.01', Factura::query()->firstOrFail()->valor_total);
    }

    public function test_tenant_can_only_list_and_view_their_own_invoices(): void
    {
        $tenantUser = User::factory()->create();
        $otherTenantUser = User::factory()->create();
        $tenant = $this->createTenant($tenantUser);
        $otherTenant = $this->createTenant($otherTenantUser);
        $agent = User::factory()->agent()->create();
        $tariff = $this->createTariff('2026-01-01', '10.00', 30);
        $ownInvoice = $this->createInvoice($tenant, $agent, $tariff, 'F-OWN-0001');
        $otherInvoice = $this->createInvoice($otherTenant, $agent, $tariff, 'F-OTHER-01');

        $this->actingAs($tenantUser)
            ->get('/facturas')
            ->assertOk()
            ->assertSee($ownInvoice->numero)
            ->assertDontSee($otherInvoice->numero);

        $this->get(route('facturas.show', $ownInvoice))->assertOk()->assertSee($ownInvoice->numero);
        $this->get(route('facturas.show', $otherInvoice))->assertForbidden();
    }

    public function test_tenant_without_linked_account_sees_no_invoices(): void
    {
        $tenantUser = User::factory()->create();
        $tenant = $this->createTenant();
        $agent = User::factory()->agent()->create();
        $tariff = $this->createTariff('2026-01-01', '10.00', 30);
        $invoice = $this->createInvoice($tenant, $agent, $tariff, 'F-NO-ACCOUNT');

        $this->actingAs($tenantUser)
            ->get('/facturas')
            ->assertOk()
            ->assertDontSee($invoice->numero);

        $this->get(route('facturas.show', $invoice))->assertForbidden();
    }

    public function test_tenant_cannot_register_readings_or_view_reading_history(): void
    {
        $tenantUser = User::factory()->create();

        $this->actingAs($tenantUser)
            ->get('/leituras')
            ->assertForbidden();

        $this->post('/leituras', [
            'contador_id' => 1,
            'leitura_actual' => '4.000',
            'data_leitura' => today()->toDateString(),
        ])->assertForbidden();
    }

    public function test_readings_and_invoices_have_no_edit_or_delete_routes(): void
    {
        $agent = User::factory()->agent()->create();

        $this->actingAs($agent)
            ->put('/leituras/1')
            ->assertNotFound();

        $this->delete('/leituras/1')->assertNotFound();
        $this->post('/facturas')->assertStatus(405);
        $this->put('/facturas/1')->assertStatus(405);
        $this->delete('/facturas/1')->assertStatus(405);
    }

    public function test_meter_cannot_be_transferred_after_readings_exist(): void
    {
        $administrator = User::factory()->admin()->create();
        $firstUtente = $this->createTenant();
        $secondUtente = $this->createTenant();
        $contador = $firstUtente->contadores()->create([
            'numero_serie' => 'METER-HISTORY-01',
            'localizacao' => 'Pátio',
            'data_instalacao' => '2026-01-01',
            'estado' => Contador::STATE_ACTIVE,
        ]);
        Leitura::query()->create([
            'contador_id' => $contador->id,
            'agente_id' => User::factory()->agent()->create()->id,
            'leitura_anterior' => '0.000',
            'leitura_actual' => '1.000',
            'consumo_m3' => '1.000',
            'data_leitura' => today()->toDateString(),
        ]);

        $this->actingAs($administrator)
            ->from(route('contadores.edit', $contador))
            ->put(route('contadores.update', $contador), [
                'utente_id' => $secondUtente->id,
                'numero_serie' => $contador->numero_serie,
                'localizacao' => $contador->localizacao,
                'data_instalacao' => $contador->data_instalacao->toDateString(),
                'estado' => $contador->estado,
            ])
            ->assertSessionHasErrors('utente_id');

        $this->assertSame($firstUtente->id, $contador->fresh()->utente_id);
    }

    private function createActiveMeter(): Contador
    {
        $utente = $this->createTenant();

        return Contador::query()->create([
            'utente_id' => $utente->id,
            'numero_serie' => 'METER-'.fake()->unique()->numerify('#####'),
            'localizacao' => 'Pátio',
            'data_instalacao' => '2026-01-01',
            'estado' => Contador::STATE_ACTIVE,
        ]);
    }

    private function createTenant(?User $utilizador = null): Utente
    {
        return Utente::query()->create([
            'utilizador_id' => $utilizador?->id,
            'nome' => fake()->name(),
            'telefone' => fake()->numerify('9########'),
            'bairro' => 'Central',
            'morada' => 'Rua de teste',
            'estado' => Utente::STATE_ACTIVE,
        ]);
    }

    private function createTariff(string $date, string $price, int $deadline): Tarifa
    {
        return Tarifa::query()->create([
            'descricao' => 'Tarifa de teste '.$date,
            'preco_m3' => $price,
            'taxa_multa_pct' => '10.00',
            'prazo_dias' => $deadline,
            'vigente_desde' => $date,
        ]);
    }

    private function createInvoice(Utente $utente, User $agent, Tarifa $tariff, string $number): Factura
    {
        $contador = $utente->contadores()->create([
            'numero_serie' => 'INVOICE-METER-'.fake()->unique()->numerify('#####'),
            'localizacao' => 'Pátio',
            'data_instalacao' => '2026-01-01',
            'estado' => Contador::STATE_ACTIVE,
        ]);
        $leitura = Leitura::query()->create([
            'contador_id' => $contador->id,
            'agente_id' => $agent->id,
            'leitura_anterior' => '0.000',
            'leitura_actual' => '1.000',
            'consumo_m3' => '1.000',
            'data_leitura' => today()->toDateString(),
        ]);

        return Factura::query()->create([
            'leitura_id' => $leitura->id,
            'utente_id' => $utente->id,
            'tarifa_id' => $tariff->id,
            'numero' => $number,
            'valor_total' => '10.00',
            'data_emissao' => today()->toDateString(),
            'data_vencimento' => today()->addDays(30)->toDateString(),
            'estado' => 'emitida',
        ]);
    }
}
