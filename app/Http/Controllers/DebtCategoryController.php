<?php

namespace App\Http\Controllers;

use App\Models\DebtCategory;
use Illuminate\Http\Request;

class DebtCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $debtCategories = DebtCategory::when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%$search%");
        })
            ->paginate(10);

        return view('debtCategories.index', compact('debtCategories'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        DebtCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => color($request->color_index),
        ]);

        return redirect()->route('debtCategories.index')
            ->with('success', 'Categoría registrada correctamente');
    }

    public function update(Request $request, $id)
    {
        $category = DebtCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|string',
            'color_index' => 'required|integer|min:0|max:19',
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => color($request->color_index),
        ]);

        return redirect()->route('debtCategories.index')
            ->with('success', 'Categoría actualizada correctamente');
    }

    public function destroy($id)
    {
        $category = DebtCategory::findOrFail($id);

        $category->delete();

        return redirect()->route('debtCategories.index')
            ->with('success', 'Categoría eliminada correctamente');
    }
}
