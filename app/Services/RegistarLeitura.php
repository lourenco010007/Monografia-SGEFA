<?php

namespace App\Services;

use App\Models\Contador;
use App\Models\Factura;
use App\Models\Leitura;
use App\Models\Tarifa;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistarLeitura
{
    public function execute(User $agente, int $contadorId, string $leituraActual, string $dataLeitura): Factura
    {
        return DB::transaction(function () use ($agente, $contadorId, $leituraActual, $dataLeitura): Factura {
            $contador = Contador::query()
                ->whereKey($contadorId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($contador->estado !== Contador::STATE_ACTIVE) {
                throw ValidationException::withMessages([
                    'contador_id' => 'O contador seleccionado não está activo.',
                ]);
            }

            $data = Carbon::createFromFormat('Y-m-d', $dataLeitura)->startOfDay();
            $instalacao = $contador->data_instalacao->startOfDay();

            if ($data->lt($instalacao)) {
                throw ValidationException::withMessages([
                    'data_leitura' => 'A data da leitura não pode ser anterior à instalação do contador.',
                ]);
            }

            $ultimaLeitura = Leitura::query()
                ->where('contador_id', $contador->getKey())
                ->orderByDesc('data_leitura')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if ($ultimaLeitura && $data->lte($ultimaLeitura->data_leitura->startOfDay())) {
                throw ValidationException::withMessages([
                    'data_leitura' => 'A nova leitura tem de ser posterior à última leitura registada.',
                ]);
            }

            $anteriorMilli = $ultimaLeitura
                ? $this->toMilliUnits($ultimaLeitura->leitura_actual)
                : 0;
            $actualMilli = $this->toMilliUnits($leituraActual);

            if ($actualMilli < $anteriorMilli) {
                throw ValidationException::withMessages([
                    'leitura_actual' => 'A leitura actual não pode ser inferior à leitura anterior.',
                ]);
            }

            $consumoMilli = $actualMilli - $anteriorMilli;
            $tarifa = Tarifa::query()
                ->whereDate('vigente_desde', '<=', $dataLeitura)
                ->orderByDesc('vigente_desde')
                ->first();

            if (! $tarifa) {
                throw ValidationException::withMessages([
                    'data_leitura' => 'Não existe tarifa vigente para a data desta leitura.',
                ]);
            }

            $valorTotal = $this->calcularValor($consumoMilli, (string) $tarifa->preco_m3);
            $leitura = Leitura::query()->create([
                'contador_id' => $contador->getKey(),
                'agente_id' => $agente->getKey(),
                'leitura_anterior' => $this->fromMilliUnits($anteriorMilli),
                'leitura_actual' => $this->fromMilliUnits($actualMilli),
                'consumo_m3' => $this->fromMilliUnits($consumoMilli),
                'data_leitura' => $dataLeitura,
            ]);

            $dataEmissao = today();

            return Factura::query()->create([
                'leitura_id' => $leitura->getKey(),
                'utente_id' => $contador->utente_id,
                'tarifa_id' => $tarifa->getKey(),
                'numero' => 'F-'.str_pad((string) $leitura->getKey(), 8, '0', STR_PAD_LEFT),
                'valor_total' => $valorTotal,
                'data_emissao' => $dataEmissao->toDateString(),
                'data_vencimento' => $dataEmissao->copy()->addDays($tarifa->prazo_dias)->toDateString(),
                'estado' => 'emitida',
            ]);
        });
    }

    private function toMilliUnits(string $value): int
    {
        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '');
        $fraction = str_pad($fraction, 3, '0');

        return ((int) $whole * 1000) + (int) substr($fraction, 0, 3);
    }

    private function fromMilliUnits(int $value): string
    {
        return intdiv($value, 1000).'.'.str_pad((string) ($value % 1000), 3, '0', STR_PAD_LEFT);
    }

    private function calcularValor(int $consumoMilli, string $precoM3): string
    {
        $precoCents = $this->toMinorUnits($precoM3);
        $maxTotalCents = 999999999999;

        if ($consumoMilli > intdiv($maxTotalCents * 1000, $precoCents)) {
            throw ValidationException::withMessages([
                'leitura_actual' => 'O valor calculado excede o limite suportado para uma factura.',
            ]);
        }

        $totalCents = intdiv(($consumoMilli * $precoCents) + 500, 1000);

        if ($totalCents > $maxTotalCents) {
            throw ValidationException::withMessages([
                'leitura_actual' => 'O valor calculado excede o limite suportado para uma factura.',
            ]);
        }

        return intdiv($totalCents, 100).'.'.str_pad((string) ($totalCents % 100), 2, '0', STR_PAD_LEFT);
    }

    private function toMinorUnits(string $value): int
    {
        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '');
        $fraction = str_pad($fraction, 2, '0');

        return ((int) $whole * 100) + (int) substr($fraction, 0, 2);
    }
}
