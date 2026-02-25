<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function __construct()
    {
        // صلاحيات دقيقة لكل عملية
        $this->middleware('permission:pages.view')->only(['index']);
        $this->middleware('permission:pages.create')->only(['create', 'store']);
        $this->middleware('permission:pages.edit')->only(['edit', 'update']);
        $this->middleware('permission:pages.delete')->only(['destroy']);
    }

    public function index()
    {
        $pages = Page::query()
            ->orderBy('sort_order')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // حماية من إدخال scripts (اختياري الآن، مهم لاحقاً)
        $data = $this->sanitizeContent($data);

        Page::create($data);

        return redirect()->route('admin.pages.index')->with('success', 'تم إنشاء الصفحة بنجاح');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $this->validated($request, $page->id);
        $data = $this->sanitizeContent($data);

        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', 'تم تحديث الصفحة بنجاح');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return back()->with('success', 'تم حذف الصفحة');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'slug' => [
                'required',
                'string',
                'max:120',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('pages', 'slug')->ignore($id),
            ],

            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],

            'content_ar' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],

            'meta_title_ar' => ['nullable', 'string', 'max:255'],
            'meta_title_en' => ['nullable', 'string', 'max:255'],

            'meta_description_ar' => ['nullable', 'string', 'max:500'],
            'meta_description_en' => ['nullable', 'string', 'max:500'],

            'is_public' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);
    }

    private function sanitizeContent(array $data): array
    {
        // حل بسيط الآن: نمنع script tags على الأقل
        foreach (['content_ar', 'content_en'] as $key) {
            if (!empty($data[$key])) {
                $data[$key] = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $data[$key]);
            }
        }
        return $data;
    }
}
