<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::with('section')
            ->orderBy('section_id')
            ->orderBy('sort_order')
            ->get();

        $sections = Section::where('is_active', true)
            ->orderBy('id')
            ->get();

        return view('admin.categories.index', compact(
            'categories',
            'sections'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'name_ar' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        Category::create($validated);

        return back()->with(
            'success',
            'تمت إضافة الفئة بنجاح.'
        );
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'section_id' => ['required', 'exists:sections,id'],
            'name_ar' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $category->update($validated);

        return back()->with(
            'success',
            'تم تحديث الفئة بنجاح.'
        );
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->withErrors([
                'category' => 'لا يمكن حذف فئة تحتوي على منتجات.',
            ]);
        }

        $category->delete();

        return back()->with(
            'success',
            'تم حذف الفئة بنجاح.'
        );
    }
}