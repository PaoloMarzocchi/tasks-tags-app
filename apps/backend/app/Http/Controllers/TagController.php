<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tags = Tag::with('parent')->get();
        return response()->json(TagResource::collection($tags));
    }

    public function show(string $id): JsonResponse
    {
        $tag = Tag::with('parent')->find($id);
        if (!$tag) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return response()->json(new TagResource($tag));
    }

    public function store(StoreTagRequest $request): JsonResponse
    {
        $data = $request->validated();

        $parentId = null;
        if (!empty($data['parent_slug'])) {
            $parent = Tag::where('slug', $data['parent_slug'])->first();
            if (!$parent) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => ['parent_slug' => ['Parent tag not found']]
                ], 422);
            }
            $parentId = $parent->id;
        }

        $tag = Tag::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'color' => $data['color'] ?? null,
            'parent_id' => $parentId,
        ]);

        $tag->load('parent');
        return response()->json(new TagResource($tag), 201);
    }

    public function update(UpdateTagRequest $request, string $id): JsonResponse
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $data = $request->validated();

        if (array_key_exists('parent_slug', $data)) {
            if ($data['parent_slug']) {
                $parent = Tag::where('slug', $data['parent_slug'])->first();
                if (!$parent) {
                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => ['parent_slug' => ['Parent tag not found']]
                    ], 422);
                }
                $tag->parent_id = $parent->id;
            } else {
                $tag->parent_id = null;
            }
        }

        if (array_key_exists('name', $data)) $tag->name = $data['name'];
        if (array_key_exists('slug', $data)) $tag->slug = $data['slug'];
        if (array_key_exists('color', $data)) $tag->color = $data['color'];

        $tag->save();
        $tag->load('parent');
        return response()->json(new TagResource($tag));
    }

    public function destroy(string $id): JsonResponse
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $tag->delete();
        return response()->json([], 204);
    }
}
