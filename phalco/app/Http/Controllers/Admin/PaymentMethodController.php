<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentMethod\StorePaymentMethodRequest;
use App\Http\Requests\Admin\PaymentMethod\UpdatePaymentMethodRequest;
use App\Models\Currency;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PaymentMethodController extends Controller
{
    public function __construct()
    {
        Gate::authorize('any', PaymentMethod::class);
    }

    public function index(Request $request)
    {
        $payment_method_id = $request->input('payment_method_id');
        $name = $request->input('name');
        $sort_by = $request->input('sort_by', 'id');
        $desc = $request->boolean('desc');

        $paymentMethods = PaymentMethod::query()
            ->when(filled($payment_method_id), fn ($query) => $query->whereKey($payment_method_id))
            ->when(filled($name), fn ($query) => $query->where('name', 'LIKE', "%{$name}%"))
            ->orderBy($sort_by, $desc ? 'desc' : 'asc')
            ->paginate(10);

        return inertia('Admin/PaymentMethod/Index', compact(
            'paymentMethods',
            'payment_method_id',
            'name',
            'sort_by',
            'desc',
        ));
    }

    public function create(Request $request)
    {
        $currencies = Currency::all();

        return inertia('Admin/PaymentMethod/Edit', compact(
            'currencies',
        ));
    }

    public function store(StorePaymentMethodRequest $request)
    {
        DB::transaction(function () use ($request) {
            $paymentMethod = PaymentMethod::create([
                ...$request->validated(),
                'logo_path' => $request->file('logo')->store('payment_methods'),
            ]);

            foreach ($request->collect('payment_items') as $item) {
                $paymentItem = $paymentMethod->paymentItems()->create($item);
                foreach ($item['payment_options'] as $option) {
                    $paymentItem->paymentOptions()->create($option);
                }
            }
        });

        return to_route('admin.payment_methods.index');
    }

    public function edit(Request $request, PaymentMethod $paymentMethod)
    {
        $currencies = Currency::all();
        $paymentMethod->load(['paymentItems.paymentOptions']);

        return inertia('Admin/PaymentMethod/Edit', compact(
            'paymentMethod',
            'currencies',
        ));
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        DB::transaction(function () use ($request, $paymentMethod) {
            $paymentMethod->fill($request->validated());

            if (filled($request->file('logo'))) {
                $paymentMethod->logo_path = $request->file('logo')->store('payment_methods');
            }

            $paymentMethod->save();

            if ($request->boolean('change_payment_items')) {
                foreach ($paymentMethod->paymentItems as $paymentItem) {
                    $paymentItem->paymentOptions()->delete();
                }
                $paymentMethod->paymentItems()->delete();

                foreach ($request->collect('payment_items') as $item) {
                    $paymentItem = $paymentMethod->paymentItems()->create($item);
                    foreach ($item['payment_options'] as $option) {
                        $paymentItem->paymentOptions()->create($option);
                    }
                }
            }
        });

        return to_route('admin.payment_methods.index');
    }
}
