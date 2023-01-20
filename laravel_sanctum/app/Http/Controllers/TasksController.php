<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\TasksResource;
use App\Traits\HttpResponses;

class TasksController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TasksResource::collection(
            Task::where('user_id', Auth::user()->id)->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        // validate incoming data
        $request->validated($request->all());
        // create a task
        $task = Task::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'priority' => $request->priority,
        ]);
        // uotput newly created data to user

        return new TasksResource($task);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        // $task = Task::where('id', $id)->get();
        // if(Auth::user()-> id != $task->user_id) {
        //     return $this->error('', 'You are not authorized to make this request', 403);
        // }
        // return new TasksResource($task);

        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : new TasksResource($task);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        if(Auth::user()-> id != $task->user_id) {
            return $this->error('', 'You are not authorized to make this request', 403);
        }
        $task->update($request->all());
        return new TasksResource($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {

        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : $task->delete();

    }

    private function isNotAuthorized(Task $task)
    {
        if(Auth::user()-> id != $task->user_id) {
            return $this->error('', 'You are not authorized to make this request', 403);
        }
    }
}
