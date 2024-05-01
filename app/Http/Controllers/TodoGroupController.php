<?php

namespace App\Http\Controllers;

use App\Models\TodoGroup;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todoGroup = TodoGroup::with('todo')->orderBy('deadline')->get();
        $groupedTodoGroups = $todoGroup->groupBy('deadline');
        return response($groupedTodoGroups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title' => 'required|string|max:16',
            'deadline' => 'required|date|after_or_equal:' . now()->toDateString(),
            'todo.*' => 'required|string|max:64'
        ]);
        $todoGroup = new TodoGroup();
        $todoGroup->title = $fields['title'];
        $todoGroup->deadline = $fields['deadline'];
        $todoGroup->save();
        
        foreach($fields['todo'] as $todoName){
            $todo = new Todo();
            $todo->todo_group_id = $todoGroup->id;
            $todo->name = $todoName;
            $todo->save();
        }
        return response($todoGroup, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TodoGroup $todoGroup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TodoGroup $todoGroup, $id)
    {
        $fields = $request->validate([
            'title' => 'required|string|max:16',
            'deadline' => 'required|date|after_or_equal:' . now()->toDateString(),
            'todo' => 'required|array'
        ]);
        $todoGroups = TodoGroup::findOrFail($id);
        $todoGroups->update($request->all());
        foreach($fields['todo'] as $todoName){
            if($todoName['todo_group_id']) {
                $todo = Todo::findOrFail($todoName['id']);
                $todo->update($todoName);
            }
        }
        return response($todoGroups, 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TodoGroup $todoGroup, $id)
    {
        $todoGroups = TodoGroup::findOrFail($id);
        $todoGroups->delete();
        return response('Deleted!');
    }
}
