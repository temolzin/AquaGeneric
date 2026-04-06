<?php

namespace App\Http\Controllers;

use App\Models\DebtCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class DebtCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:viewDebtCategories')->only(['index']);
        $this->middleware('can:createDebtCategories')->only(['store']);
        $this->middleware('can:editDebtCategories')->only(['update']);
        $this->middleware('can:deleteDebtCategories')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = DebtCategory::query();

        $query->where(function ($q) use ($user) {
            $q->whereNull('locality_id');

            if ($user && $user->locality_id) {
                $q->orWhere('locality_id', $user->locality_id);
            }
        });

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }

                $q->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('name')
            ->paginate(10)
            ->appends($request->query());

        return view('debtCategories.index', compact('categories'));
    }


    protected function rules($localityId, $ignoreId = null)
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('debt_categories')
                    ->where(function ($q) use ($localityId) {
                        $q->where('locality_id', $localityId)->whereNull('deleted_at');
                    })
                    ->ignore($ignoreId),
            ],
            'description' => ['nullable', 'string'],
            'color_index' => ['required', 'integer', 'min:0', 'max:19'],
        ];
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if (! $user) {
            return $this->respondError($request, 'Usuario no autenticado', 401);
        }
        $localityId = $user->locality_id;
        $name = Str::title(trim($request->input('name')));
        if (strcasecmp($name, DebtCategory::NAME_SERVICE) === 0) {
            return $this->respondError($request, 'No se puede crear la categoría "' . DebtCategory::NAME_SERVICE . '".', 422);
        }
        $data = $request->only(['description']);
        $data['name'] = $name;
        $data['color_index'] = $request->input('color_index');
        $validator = Validator::make($data, $this->rules($localityId));
        if ($validator->fails()) {
            return $this->validationError($request, $validator);
        }
        $category = DebtCategory::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'color' => color($data['color_index']),
            'locality_id' => $localityId,
            'created_by' => $user->id,
        ]);
        if ($request->ajax()) {
            return response()->json(['success' => 'Categoría creada con éxito.', 'category' => $category]);
        }
        return redirect()->back()->with('success', 'Categoría creada con éxito.');
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        if (! $user) {
            return $this->respondError($request, 'Usuario no autenticado', 401);
        }
        $category = DebtCategory::findOrFail($id);
        if ($category->isService()) {
            return $this->respondError($request, 'La categoría "' . DebtCategory::NAME_SERVICE . '" no puede editarse.', 403);
        }
        if ($category->locality_id !== $user->locality_id) {
            return $this->respondError($request, 'Acceso denegado.', 403);
        }
        $name = Str::title(trim($request->input('name')));
        if (strcasecmp($name, DebtCategory::NAME_SERVICE) === 0) {
            return $this->respondError($request, 'No se puede renombrar a "' . DebtCategory::NAME_SERVICE . '".', 422);
        }
        $data = $request->only(['description']);
        $data['name'] = $name;
        $data['color_index'] = $request->input('color_index');
        $validator = Validator::make($data, $this->rules($user->locality_id, $id));
        if ($validator->fails()) {
            return $this->validationError($request, $validator);
        }
        $category->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'color' => color($data['color_index']),
        ]);
        if ($request->ajax()) {
            return response()->json(['success' => 'Categoría actualizada con éxito.', 'category' => $category]);
        }
        return redirect()->back()->with('success', 'Categoría actualizada con éxito.');
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();
        if (! $user) {
            return $this->respondError($request, 'Usuario no autenticado', 401);
        }
        $category = DebtCategory::findOrFail($id);
        if ($category->isService()) {
            return $this->respondError($request, 'La categoría "' . DebtCategory::NAME_SERVICE . '" no puede eliminarse.', 403);
        }
        if ($category->locality_id !== $user->locality_id) {
            return $this->respondError($request, 'Acceso denegado.', 403);
        }
        if ($category->hasDependencies()) {
            return $this->respondError($request, 'No se puede eliminar: existen deudas asociadas a esta categoría.', 422);
        }
        $category->delete();
        if ($request->ajax()) {
            return response()->json(['success' => 'Categoría eliminada con éxito.']);
        }
        return redirect()->back()->with('success', 'Categoría eliminada con éxito.');
    }

    private function respondError(Request $request, string $message, int $code = 403)
    {
        if ($request->ajax()) {
            return response()->json(['error' => $message], $code);
        }
        return redirect()->back()->with('error', $message);
    }

    private function validationError(Request $request, $validator)
    {
        if ($request->ajax()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        return redirect()->back()->withErrors($validator)->withInput();
    }
}
