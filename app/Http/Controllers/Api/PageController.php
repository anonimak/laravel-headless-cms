<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePageRequest;
use App\Http\Requests\Api\UpdatePageRequest;
use App\Http\Resources\Api\PageResource;
use App\Models\Page;
use App\Services\PageService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct(private PageService $pageService) {}

    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $pages = $this->pageService->getPaginatedPublished($search, $perPage);

        return PageResource::collection($pages);
    }

    public function show($id)
    {
        $page = Page::where('status', 'published')
            ->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))
            ->firstOrFail();

        return new PageResource($page);
    }

    public function store(StorePageRequest $request)
    {
        $page = $this->pageService->create($request->validated());
        return new PageResource($page);
    }

    public function update(UpdatePageRequest $request, Page $page)
    {
        $this->pageService->update($page, $request->validated());
        return new PageResource($page);
    }

    public function destroy(Page $page)
    {
        $this->pageService->delete($page);
        return response()->json(['message' => 'Page deleted']);
    }
}
