<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

###########################################################################
###########         Begin
###########################################################################
/*

# Link : https://www.youtube.com/watch?v=TzAJfjCn7Ks&pp=ygUfbGFyYXZlbCBzYW5jdHVtICBDb2RlIFdpdGggRGFyeQ%3D%3D
# Link on Laravel Docs : https://laravel.com/docs/10.x/sanctum

    composer require laravel/sanctum

    php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

    php artisan migrate


    //////////////////////
    make in app folder the (Traits) Folder and make a file (HttpResponses.php) code that in it :

<?php

namespace App\Traits ;

trait HttpResponses {

    protected function success($data ,$message=null ,$code=200)
    {
        return response()->json([

            'status' => 'Request was successful',
            'message' => $message,
            'data' => $data,

        ],$code);
    }

    protected function error($data ,$message=null ,$code)
    {
        return response()->json([

            'status' => 'Error has occoured !',
            'message' => $message,
            'data' => $data,

        ],$code);
    }
}


    //////////////////////
    make controller AuthController

    php artisan make:controller AuthController

    go to controller AuthController and code that :

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;

class AuthController extends Controller
{
    use HttpResponses ; // this is my trait


    public function login(LoginUserRequest $request)
    {
        $request -> validated($request->all() );

        if (!Auth::attempt($request->only(['email','password']))) {
            return $this->error('','Credentials dont match (Unauthorized)',401);
        }

        $user = User::where('email',$request->email)->first();

        return $this->success([
            'user' => $user,
            'token'=> $user->createToken('API Token of'.$user->name)->plainTextToken
        ]);


    }

    public function register(StoreUserRequest $request)
    {
        $request -> validated($request->all() );

        $user = User::create([

            'name' => $request -> name ,
            'email' => $request -> email ,
            'password' => Hash::make($request -> password) ,

        ]);

        return $this -> success([
            'user' => $user,
            'token'=> $user->createToken('API Token of ' .$user->name)->plainTextToken ,
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();

        return $this -> success([
            'message' => 'You have successfuly logged out and your token has been deleted',
        ]);
    }
}

    //////////////////////
    go to api.php  :

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// public Routes

Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);

// protected Routes (With Auth)

// Route::prefix()-> group(['middleware'=>['auth:sanctum']],function () {} //to implement prefix

Route::group(['middleware'=>['auth:sanctum']],function () {

Route::resource('/tasks', TaskController::class);
Route::post('/logout', [AuthController::class,'logout']);

});


    //////////////////////

    php artisan make:model Task -a

    //////////////////////

    TO show all routes in project code the command :

    php artisan route:list

    ////////////////////////

    ensure that in the user Model you code :

    use Laravel\Sanctum\HasApiTokens;

        use HasApiTokens ; //write it in the class

    /////////////////////////
    We Make StoreUserRequest.php by command :

    php artisan make:request StoreUserRequest

    make authorize function is true // not false

<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [

            'name' => ['required','string','max:255'],
            'email' => ['required','string','email','max:255','unique:users'],
            'password' => ['required','confirmed',Password::defaults()],

        ];
    }
}

    //////////////////////////
    PostMan :
    IN ALL REQUESTS USE THE 2 HEADERS BELOW
    in register request :

    always with api < Go to Headers and type these two things in it > :

    key           |    Value
    Accept        |   application/vnd.api+json
    Content-Type  |   application/vnd.api+json

    - now go to Body (form data) code that (Right side is an example)

    name                   CodeWithDary
    email                  CodeWithDary@gmail.com
    password               CodeWithDary
    password_confirmation  CodeWithDary

    -- The Tokens are stored in pesonal access tokens Table


    //////////////////////////
    Login User Request (LoginUserRequest):

    php artisan make:request LoginUserRequest

    code in it :


<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [

            'email' => ['required','string','email','max:255',],
            'password' => ['required','min:6'],
        ];
    }
}


    //////////////////////////
    //////////////////////////
    PostMan :
        IN ALL REQUESTS USE THE 2 HEADERS BELOW
    in Login request :

    always with api < Go to Headers and type these two things in it > :

    key           |    Value
    Accept        |   application/vnd.api+json
    Content-Type  |   application/vnd.api+json

    - now go to Body (form data) code that (Right side is an example)


    email                  CodeWithDary@gmail.com
    password               CodeWithDary


    -- The Tokens are stored in pesonal access tokens Table


    //////////////////////////
    Postman :
    Tasks Requests index (get all tasks)

    IN ALL REQUESTS USE THE 2 HEADERS BELOW
    in register request :

    always with api < Go to Headers and type these two things in it > :

    key           |    Value
    Accept        |   application/vnd.api+json
    Content-Type  |   application/vnd.api+json

    Use Athentication To apply Token as here :

    Type :  Bearer Token
    Token : 5|5VMnqWt2a95K0LwgPSJ6A4x0pitO8Qq0YvqhTVZa   // as an example
    // you can set the token as a variable in postman

    - now go to Body (form data) code that (Right side is an example)

    name                   CodeWithDary
    email                  CodeWithDary@gmail.com
    password               CodeWithDary
    password_confirmation  CodeWithDary

    //////////////////////////
    Task Migration Table  :

    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('priority')->default('medium');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }
    //////////////////////////
    Task Model :

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'priority',
        'user_id',
    ] ;

    public function user()
    {
        return $this->belongsTo(User::class) ;
    }
}

    ///////////////////////////
    Dont Forget to go to user and define relations
    //////////////////////////
    Task Controller :

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


    //////////////////////////
    Task Factory
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class TaskFactory extends Factory
{

    public function definition(): array
    {
        return [

            'user_id' => User::all()->random()->id ,
            'name'   => $this->faker->unique()->sentence(),
            'description' => $this->faker->text(),
            'priority' =>$this->faker->randomElement(['low','medium','high'])
        ];
    }
}

    //////////////////////////
    Tinker : to Add Users :

    php artisan tinker

    User::factory()->times(25)->create();

    // this will create 25 users

    //////////////////////////
    Tinker : to Add Tasks :

    php artisan tinker

    \App\Models\Task::factory(250)->create();

    // this will create 25 users

    //////////////////////////
    Task Resource : (Collection in json )

    php artisan make:resource TasksResource

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TasksResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this-> id ,
            'attributes' => [
                'name'=>$this->name,
                'description'=>$this->description,
                'priority'=>$this->priority,
                'created_at'=>$this->created_at,
                'updated_at'=>$this->updated_at,
            ],
            'relationships' => [
                'id'=>(string)$this->user->id,
                'user name'=>$this->user->name,
                'user email'=>$this->user->email,


            ]
        ];
    }
}

    //////////////////////////
    Store Task Request :

    php artisan make:request StoreTaskRequest

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => ['required','max:255'],
            'description' => ['required','string'],
            'priority' => ['required','in:low,medium,high'],

        ];
    }
}

    //////////////////////////
    Update Task Request :
public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'name' => ['max:255'],
            'description' => ['string'],
            'priority' => ['in:low,medium,high'],
        ];
    }

    //////////////////////////
    Postman Logout Request :
    Tasks Requests index (get all tasks)

    logout Request has no body and its post method

    always with api < Go to Headers and type these two things in it > :

    key           |    Value
    Accept        |   application/vnd.api+json
    Content-Type  |   application/vnd.api+json

    Use Athentication To apply Token as here :

    Type :  Bearer Token
    Token : 5|5VMnqWt2a95K0LwgPSJ6A4x0pitO8Qq0YvqhTVZa   // as an example
    // you can set the token as a variable in postman


    ///////////////////////////
    Last thing :

    Token Expirations : NOT[]WORKED[]WITH[]ME[]}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}}} }} }}}

    By Default in Sanctum : the Token Expirations (when login) is will not expired

    to config Token Expiration Go to

    config -> Sanctum.php -> make the

    'expiration' => null,      --->   'expiration' => 43800, // means expires after 30 days
    'expiration' => null,      --->   'expiration' => 1460, // means expires after 1 days
                                    // this is in minutes

    - But its recommended to add schedular by this 👇🏻 :

    go to app -> console -> kernel.php -> the schedule function

    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command();
        // $schedule->command('inspire')->hourly(); // just a comment example y default

        // $schedule->command('sanctum:prune-expired --hours=24')->daily();
        //                                              |             |
        //                                              |             |
        //                                              |             |
        //          |------------------------------------             |
        //          |                                                 👇🏻
        //          👇🏻                                            schedular will restart function
        //          tokens ends in the database                   after ...

        }

    - CHECK SCHEDULE WORK AT the command :

    php artisan schedule:list

    php artisan schedule:work
    ////////////////////////
    php artisan schedule:

   ERROR  Command "schedule:" is ambiguous. Did you mean one of these?

  ⇂ schedule:list
  ⇂ schedule:run
  ⇂ schedule:test
  ⇂ schedule:work
  ⇂ schedule:clear-cache

  For more info about commands and task scheduling : https://youtu.be/dA0YFhGUPIQ
  For more info about commands and task scheduling : https://youtu.be/fUqrE9ZBH_Q

    ////////////////////////

php artisan make:command TokenExpiredDate

go to  App\Console\Commands\TokenExpiredDate :


*/
###########################################################################
############         End
###########################################################################
