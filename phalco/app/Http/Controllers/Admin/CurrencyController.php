<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Currency\UpdateCurrencyRequest;
use App\Jobs\JobsUpdateCurrencyRates;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CurrencyController extends Controller
{

    public function __construct(private readonly Currency $currency) {
        Gate::authorize('any', Currency::class);
    }

    /**
     * 通貨一覧
     *
     * @param Request $request
     * @return inertia
     */
    public function index(Request $request)
    {
        $currency_id = $request->input('currency_id');
        $name = $request->input('name');
        $sort_by = $request->input('sort_by', 'id');
        $desc = $request->boolean('desc');

        $currencies = $this->currency->query()
            ->when(filled($currency_id), fn ($query) =>
                $query->whereKey($currency_id)
            )
            ->when(filled($name), fn ($query) =>
                $query->where('name', 'LIKE', "%{$name}%")
            )
            ->orderBy($sort_by, $desc ? 'desc' : 'asc')
            ->paginate(10);

        return inertia('Admin/Currency/Index', compact(
            'currencies',
            'currency_id',
            'name',
            'sort_by',
            'desc',
        ));
    }

    /**
     * 通貨登録画面
     *
     * @param Request $request
     * @return inertia
     */
    public function create()
    {

        return inertia('Admin/Currency/Edit');
    }

    /**
     * 通貨登録処理
     *
     * @param UpdateCurrencyRequest $request
     * @return void
     */
    public function store(UpdateCurrencyRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->currency->create([
                'name' => $request->name,
                'code' => $request->code,
                'scale' => $request->scale,
                'min_amount' => $request->min_amount,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

        // 通貨レート登録バッチ実行
        JobsUpdateCurrencyRates::dispatch();

        return to_route('admin.currency.index');
    }

    /**
     * 編集画面
     *
     * @param Request $request
     * @param Currency $currency
     * @return inertia
     */
    public function edit(Request $request, Currency $currency)
    {
        $currency = $currency->load('currencyRates');

        return inertia('Admin/Currency/Edit', compact(
            'currency'
        ));
    }

    /**
     * 更新処理
     *
     * @param UpdateCurrencyRequest $request
     * @param Currency $currency
     * @return void
     */
    public function update(UpdateCurrencyRequest $request, Currency $currency)
    {

        DB::beginTransaction();
        try {
            $currency->name = $request->name;
            $currency->code = $request->code;
            $currency->scale = $request->scale;
            $currency->min_amount = $request->min_amount;
            $currency->save();

            // currency_rateテーブル更新
            if ($request->id && $request->currency_rate) {
                foreach ($request->currency_rate as $key => $rate) {
                    $currency->currencyRates()->updateExistingPivot(
                        $rate['pivot']['target_id'],
                        ['fixed_rate' => (double)$rate['pivot']['fixed_rate'] ?? null],
                        false
                    );
                }

            }
            DB::commit();
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
        }
        return to_route('admin.currency.index');
    }
}
