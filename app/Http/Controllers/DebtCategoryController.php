<?php

namespace App\Http\Controllers;

use App\Models\DebtCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DebtCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $authUser = auth()->user();

        $debtCategories = DebtCategory::where('locality_id', $authUser->locality_id)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('debtCategories.index', compact('debtCategories'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('debt_categories')
                    ->where(fn($q) => $q->where('locality_id', $user->locality_id)->whereNull('deleted_at'))
            ],
            'description' => 'nullable|string',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        $name = ucfirst(strtolower(trim($request->name)));

        DebtCategory::create([
            'name' => $name,
            'description' => $request->description,
            'color' => color($request->color_index),
            'created_by' => $user->id,
            'locality_id' => $user->locality_id,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => 'Categoría creada correctamente'
            ], 201);
        }

        return redirect()->route('debtCategories.index')
            ->with('success', 'Categoría registrada correctamente');
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $category = DebtCategory::findOrFail($id);

        if (strtolower($category->name) === 'servicio de agua') {
            return back()->with('error', 'No puedes modificar esta categoría.');
        }

        $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('debt_categories')
                    ->ignore($category->id)
                    ->where(fn($q) => $q->where('locality_id', $user->locality_id)->whereNull('deleted_at')
                    )
            ],
            'description' => 'nullable|string',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        $name = ucfirst(strtolower(trim($request->name)));

        $category->update([
            'name' => $name,
            'description' => $request->description,
            'color' => color($request->color_index),
        ]);

        return redirect()->route('debtCategories.index')
            ->with('success', 'Categoría actualizada correctamente');
    }

    public function destroy($id)
    {
        $category = DebtCategory::findOrFail($id);

        if (strtolower($category->name) === 'servicio de agua') {
            return back()->with('error', 'No puedes eliminar esta categoría.');
        }

        $category->delete();

        return redirect()->route('debtCategories.index')
            ->with('success', 'Categoría eliminada correctamente');
    }
}
