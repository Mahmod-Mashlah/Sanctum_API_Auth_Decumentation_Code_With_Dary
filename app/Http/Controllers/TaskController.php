<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TasksResource;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use HttpResponses ;

    public function index()
    {
        #  return TasksResource::collection(
        #   Task::where('user_id',Auth::user()->id)->get() ) ; // get tasks thats users are authenticated

        return TasksResource::collection(
            Task::where('user_id',Auth::user()->id)->get() ) ; // get tasks thats users are authenticated

    }

    public function store(StoreTaskRequest $request)
    {
        $request -> validated($request->all() );

        $task = Task::create([
            'user_id' => Auth::user()->id ,
            'name' => $request->name ,
            'description' => $request->description ,
            'priority' => $request->priority ,
        ]);

        return new TasksResource($task) ;
    }

    //  public function BadShow($id)
    //  {
    //         $task = Task::where('id',$id)->get();
    //     return response()->json($task, 200);
    //  }

     // This below is better show
    // public function show(Task $task)
    // {
    //     return new TasksResource($task);
    // }
     // This below is best show
    public function show(Task $task)
    {
        if (Auth::user()->id !== $task->user_id) {
            return $this->error('','You are not Authorized to make this request',403);
            // you should use the trait   (use HttpResponses ;) above
        }
        return new TasksResource($task);
    }

    public function update(UpdateTaskRequest $request,Task $task)  // this work correctly
        // in postman make the method : Post (not patch not put)
        // and make in request body : _method = PUT
    {
        $task=Task::find($task->id);
        if (Auth::user()->id !== $task->user_id) {
            return $this->error('','You are not Authorized to make this request',403);
            // you should use the trait   (use HttpResponses ;) above

        }

        $task->update($request->all());
        $task->save();

        return new TasksResource($task);
    }

    public function update2(UpdateTaskRequest $request, Task $task)
    {

        $task = Task::findOrFail($task->id);

        $task->name          = $request->name;
        $task->description  = $request->description;
        $task->priority      = $request->priority;

        if ($task->save()) {
            return new TasksResource($task) ;
        }

    }


public function update3(UpdateTaskRequest $request, Task $task )
{   dd($task);
    $task =Task::where('id', $task)->update($request->all());

    return new TasksResource($task) ;

}

public function update4(UpdateTaskRequest $request, Task $task)
    {
      // check if currently authenticated user is the owner of the book
      if ($request->user()->id !== $task->user_id) {
        return response()->json(['error' => 'You can only edit your own books.'], 403);
      }

      $task->update($request->only(['name', 'description','priority']));

      return new TasksResource($task);
    }

public function update5(UpdateTaskRequest $request, $id)
    {
        $task = Task::find($id);
        $tmp = [
        'name'  => $request->name,
        'description'  => $request->description,
        'priority'      => $request->priority,
                    ];
        $task->update($tmp);
    }
    public function destroy(Task $task)
    {
        // way 1 :
            // $task->delete();
            // return $this->success('Task was Deleted Successfuly ',null,204);

        // way 2 : (it is best to do it in Show & Update functions [Implement Private function below] )

        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : $task->delete();
        // return true (1) if the delete successfuly occoured
    }

    private function isNotAuthorized($task)
    {
        if (Auth::user()->id !== $task->user_id) {
            return $this->error('','You are not Authorized to make this request',403);
            }
        }

}
