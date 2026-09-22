<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCmsContentRequest;
use App\Http\Requests\UpdateCmsContentRequest;
use App\Models\CmsContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CmsContentController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('settings.manage'), 403);

        return Inertia::render('Cms/Index', [
            'contents' => CmsContent::query()->latest()->get()->map(fn (CmsContent $content) => $this->contentData($content)),
        ]);
    }

    public function store(StoreCmsContentRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('cms', 'public');
        }

        CmsContent::create($data);

        return to_route('cms.index')->with('success', 'Contenido creado correctamente.');
    }

    public function update(UpdateCmsContentRequest $request, CmsContent $cmsContent): RedirectResponse
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('cms', 'public');
        }

        $cmsContent->update([...$data, 'version' => $cmsContent->version + 1]);

        return to_route('cms.index')->with('success', 'Contenido actualizado correctamente.');
    }

    private function contentData(CmsContent $content): array
    {
        return [...$content->toArray(), 'image_url' => $content->image_path ? asset('storage/'.$content->image_path) : null];
    }
}
