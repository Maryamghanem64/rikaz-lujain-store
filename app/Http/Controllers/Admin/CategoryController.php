<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->section_id !== null, 403);

        $categories = Category::with('section')
            ->where('section_id', $request->user()->section_id)
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.index', compact(
            'categories'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->section_id !== null, 403);

        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')
                    ->where('section_id', $request->user()->section_id),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['section_id'] = $request->user()->section_id;
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
        abort_unless($request->user()->managesCategory($category), 403);

        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')
                    ->where('section_id', $request->user()->section_id)
                    ->ignore($category->id),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['section_id'] = $request->user()->section_id;
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
        abort_unless(request()->user()->managesCategory($category), 403);

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
