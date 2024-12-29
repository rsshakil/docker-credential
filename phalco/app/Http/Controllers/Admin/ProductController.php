<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Models\Currency;
use App\Models\Offer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProductController extends Controller
{
    public function __construct()
    {
        Gate::authorize('any', Product::class);
    }

    public function index(Request $request)
    {
        $product_id = $request->input('product_id');
        $name = $request->input('name');
        $sort_by = $request->input('sort_by', 'id');
        $desc = $request->boolean('desc');

        $products = Product::query()
            ->when(filled($product_id), fn ($query) => $query->whereKey($product_id))
            ->when(filled($name), fn ($query) => $query->where('name', 'LIKE', "%{$name}%"))
            ->orderBy($sort_by, $desc ? 'desc' : 'asc')
            ->paginate(10);

        return inertia('Admin/Product/Index', compact(
            'products',
            'product_id',
            'name',
            'sort_by',
            'desc',
        ));
    }

    public function create(Request $request)
    {
        $currencies = Currency::all();

        return inertia('Admin/Product/Edit', compact(
            'currencies',
        ));
    }

    public function store(StoreProductRequest $request)
    {
        DB::transaction(function () use ($request) {
            $product = Product::create([
                ...$request->validated(),
                'logo_path' => $request->file('logo')->store('products'),
            ]);

            foreach ($request->collect('product_account_items') as $item) {
                $productAccountItem = $product->productAccountItems()->create($item);
                foreach (collect($item)->get('product_account_item_options', []) as $option) {
                    $productAccountItem->productAccountItemOptions()->create($option);
                }
            }
        });

        return to_route('admin.products.index');
    }

    public function edit(Request $request, Product $product)
    {
        $currencies = Currency::all();
        $product->load(['productAccountItems.productAccountItemOptions']);

        return inertia('Admin/Product/Edit', compact(
            'product',
            'currencies',
        ));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::transaction(function () use ($request, $product) {
            $product->fill($request->validated());

            if (filled($request->file('logo'))) {
                $product->logo_path = $request->file('logo')->store('products');
            }

            $product->save();

            // 項目定義変更時
            if ($request->boolean('change_product_account_items')) {
                // 掲載中のオファーをすべて休止する
                Offer::where('product_id', $product->id)
                    ->where('offer_status', OfferStatus::PUBLISHING__ACTIVE)
                    ->update(['offer_status' => OfferStatus::PUBLISHING__PAUSE]);

                // 関連した既存の運営アカウント、商品送付先をすべて論理削除
                $product->adminProductAccounts()->delete();
                $product->userProductAccounts()->delete();

                // 関連した既存の項目定義、オプション定義をすべて論理削除
                foreach ($product->productAccountItems as $productAccountItem) {
                    $productAccountItem->productAccountItemOptions()->delete();
                }
                $product->productAccountItems()->delete();

                foreach ($request->collect('product_account_items') as $item) {
                    $productAccountItem = $product->productAccountItems()->create($item);
                    foreach ($item['product_account_item_options'] as $option) {
                        $productAccountItem->productAccountItemOptions()->create($option);
                    }
                }
            }
        });

        return to_route('admin.products.index');
    }
}
