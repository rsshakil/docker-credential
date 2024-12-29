<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductAccountItemType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminProductAccount\StoreAdminProductAccountRequest;
use App\Http\Requests\Admin\AdminProductAccount\UpdateAdminProductAccountRequest;
use App\Models\AdminProductAccount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AdminProductAccountController extends Controller
{
    public function __construct()
    {
        Gate::authorize('any', AdminProductAccount::class);
    }
    public function index(Request $request)
    {
        $products = Product::all();
        $admin_product_account_id = $request->input('admin_product_account_id');
        $name = $request->input('name');
        $product_id = $request->filled('product_id') ? $request->integer('product_id') : null;
        $sort_by = $request->input('sort_by', 'id');
        $desc = $request->boolean('desc');

        $adminProductAccounts = AdminProductAccount::with(['product'])
            ->when(filled($admin_product_account_id), fn ($query) => $query->whereKey($admin_product_account_id))
            ->when(filled($name), fn ($query) => $query->where('name', 'LIKE', "%{$name}%"))
            ->when(filled($product_id), fn ($query) => $query->where('product_id', $product_id))
            ->orderBy($sort_by, $desc ? 'desc' : 'asc')
            ->paginate(10);

        return inertia('Admin/AdminProductAccount/Index', compact(
            'adminProductAccounts',
            'products',
            'admin_product_account_id',
            'name',
            'product_id',
            'sort_by',
            'desc',
        ));
    }

    public function create(Request $request)
    {
        $products = Product::with(['productAccountItems.productAccountItemOptions'])->get();

        return inertia('Admin/AdminProductAccount/Edit', compact(
            'products',
        ));
    }

    public function store(StoreAdminProductAccountRequest $request)
    {
        DB::transaction(function () use ($request) {
            if ($request->is_temporary) {
                Product::find($request->product_id)->adminProductAccounts()->update([
                    'is_temporary' => false,
                ]);
            }

            $adminProductAccount = AdminProductAccount::create($request->validated());
            foreach ($request->validated('admin_product_account_items') as $item) {
                switch (ProductAccountItemType::from($item['type'])) {
                    case ProductAccountItemType::TEXT:
                        $adminProductAccount->adminProductAccountItems()->create($item);
                        break;
                    case ProductAccountItemType::SELECT:
                    case ProductAccountItemType::CHECKBOX:
                    case ProductAccountItemType::RADIO:
                        $adminProductAccountItem = $adminProductAccount->adminProductAccountItems()->create([
                            ...$item,
                            'value' => null,
                        ]);
                        $adminProductAccountItem->productAccountItemOptions()->attach($item['value']);
                        break;
                    case ProductAccountItemType::IMAGE:
                        $adminProductAccount->adminProductAccountItems()->create([
                            ...$item,
                            'value' => $item['value']->store('product_account_item_images'),
                        ]);
                }
            }
        });

        return to_route('admin.admin_product_accounts.index');
    }

    public function edit(Request $request, AdminProductAccount $adminProductAccount)
    {
        $products = Product::all();

        $adminProductAccount->load([
            'product',
            'adminProductAccountItems.productAccountItem.productAccountItemOptions',
            'adminProductAccountItems.productAccountItemOptions'
        ]);

        return inertia('Admin/AdminProductAccount/Edit', compact(
            'adminProductAccount',
            'products',
        ));
    }

    public function update(UpdateAdminProductAccountRequest $request, AdminProductAccount $adminProductAccount)
    {
        DB::transaction(function () use ($request, $adminProductAccount) {
            $adminProductAccount->update($request->validated());
        });

        return to_route('admin.admin_product_accounts.index');
    }
}
