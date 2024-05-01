<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'todo.*' => 'required'
        ]);
        foreach($fields['todo'] as $todoName){
            $todo = new Todo();
            $todo->todo_group_id = $todoName['todo_group_id'];
            $todo->name = $todoName['name'];
            $todo->is_done = $todoName['is_done'];
            $todo->save();
        }
        return response($todoName, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todo $todo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Todo $todo, $id)
    {
        $fields = $request->validate([
            'is_done' => 'required|boolean'
        ]);
        $todos = Todo::where('id', $id)->firstOrFail();
        $todos->update($request->all());
        return response($todos);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Todo $todo)
    {
        $fields = $request->validate([
            'ids' => 'required|array'
        ]);
        foreach($fields['ids'] as $id){
            $todo = Todo::findOrFail($id);
            $todo->delete();
        }
        return response('Deleted');
    }
}
