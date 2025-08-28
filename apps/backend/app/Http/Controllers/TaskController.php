<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Tag;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Task::query()->with(['tags']);

        if ($status = $request->query('status')) {
            if (in_array($status, ['todo', 'doing', 'done'])) {
                $query->where('status', $status);
            }
        }

        if ($q = $request->query('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }

        if ($tag = $request->query('tag')) {
            $query->whereHas('tags', function ($tq) use ($tag) {
                $tq->where('slug', $tag)->orWhere('id', $tag);
            });
        }

        $tasks = $query->get();

        return response()->json(TaskResource::collection($tasks));
    }

    public function show(string $id): JsonResponse
    {
        $task = Task::with('tags')->find($id);
        if (!$task) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return response()->json(new TaskResource($task));
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $data = $request->validated();

        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'todo',
            'assignee' => $data['assignee'] ?? null,
            'due_date' => $data['due_date'] ?? null,
        ]);

        // tags by slug array
        if (!empty($data['tags'])) {
            $tagIds = [];
            foreach ($data['tags'] as $slug) {
                $tag = Tag::where('slug', $slug)->first();
                if (!$tag) {
                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => ['tags' => ["Unknown tag: $slug"]]
                    ], 422);
                }
                $tagIds[] = $tag->id;
            }
            $task->tags()->sync($tagIds);
        }

        $task->load('tags');
        return response()->json(new TaskResource($task), 201);
    }

    public function update(UpdateTaskRequest $request, string $id): JsonResponse
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $data = $request->validated();

        foreach (['title', 'description', 'assignee', 'due_date'] as $field) {
            if (array_key_exists($field, $data)) {
                $task->{$field} = $data[$field];
            }
        }
        if (array_key_exists('status', $data)) {
            if (in_array($data['status'], ['todo', 'doing', 'done'])) {
                $task->status = $data['status'];
            }
        }
        $task->save();

        if (array_key_exists('tags', $data)) {
            $tagIds = [];
            foreach ($data['tags'] as $slug) {
                $tag = Tag::where('slug', $slug)->first();
                if (!$tag) {
                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => ['tags' => ["Unknown tag: $slug"]]
                    ], 422);
                }
                $tagIds[] = $tag->id;
            }
            $task->tags()->sync($tagIds);
        }

        $task->load('tags');
        return response()->json(new TaskResource($task));
    }

    public function destroy(string $id): JsonResponse
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        $task->delete();
        return response()->json([], 204);
    }
}
