<?php

namespace App\Jobs;

use App\Models\Currency;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JobsUpdateCurrencyRates implements ShouldQueue
{
    use Queueable;

    const API_URL = 'https://api-pub.bitfinex.com/v2/calc/fx';

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $target = [];
        $currencies = Currency::with('currencyRates')->get();

        DB::beginTransaction();
        try {
            foreach ($currencies as $currencyBase) {
                foreach ($currencies as $currencyTarget) {
                    // レート取得
                    $rate = Http::withHeaders([
                        'accept' => 'application/json',
                        'content-type' => 'application/json',
                    ])->post(self::API_URL,[
                        'ccy1' => $currencyBase['code'],
                        'ccy2' => $currencyTarget['code'],
                    ]);

                    $target[$currencyTarget['id']] = ['rate' => $rate->json()[0]];
                }
                $currencyBase->currencyRates()->sync($target);
            }
            DB::commit();

        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
        }
    }
}
