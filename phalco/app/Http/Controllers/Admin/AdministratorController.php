<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Administrator\StoreAdministratorRequest;
use App\Http\Requests\Admin\Administrator\UpdateAdministratorRequest;
use App\Models\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdministratorController extends Controller
{
    public function __construct()
    {
        Gate::authorize('any', Administrator::class);
    }

    public function index(Request $request)
    {
        $administrator_id = $request->input('administrator_id');
        $name = $request->input('name');
        $sort_by = $request->input('sort_by', 'id');
        $desc = $request->boolean('desc');

        $administrators = Administrator::query()
            ->when(filled($administrator_id), fn ($query) => $query->whereKey($administrator_id))
            ->when(filled($name), fn ($query) => $query->where('name', 'LIKE', "%{$name}%"))
            ->orderBy($sort_by, $desc ? 'desc' : 'asc')
            ->paginate(10);

        return inertia('Admin/Administrator/Index', compact(
            'administrators',
            'administrator_id',
            'name',
            'sort_by',
            'desc',
        ));
    }

    public function create(Request $request)
    {
        return inertia('Admin/Administrator/Edit');
    }

    public function store(StoreAdministratorRequest $request)
    {
        Administrator::create([
            ...$request->validated(),
        ]);

        return to_route('admin.administrators.index');
    }

    public function edit(Request $request, Administrator $administrator)
    {
        return inertia('Admin/Administrator/Edit', compact(
            'administrator',
        ));
    }

    public function update(UpdateAdministratorRequest $request, Administrator $administrator)
    {
        if (blank($request->input('password'))) {
            $administrator->update($request->except('password'));
        } else {
            $administrator->update($request->validated());
        }

        return to_route('admin.administrators.index');
    }
}
