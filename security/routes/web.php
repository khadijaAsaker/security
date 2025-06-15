<?php
use App\Models\User;
use App\Models\adminlogin;
use App\Models\service;
use App\Models\contact;
use App\Models\insuranceServ;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\Order2Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
Route::get('/', function () {
    $services = service::all();
    return view('home',['services'=>$services]);
});
Route::get('/x', function () {
    return view('x');
});
Route::get('/login', function () {

    return view('login');
});

Route::get('/contact', function () {

    return view('contact');
}); 
Route::get('/userresponses', function () {

    return view('userresponses',['messages'=>contact::all()]);
}); 
Route::get('/adminlogout', function () {

    return redirect('/')->with('success', 'User registered successfully!');
}); 
Route::get('/adminresponse', function () {

    return view('adminresponse',['messages'=>contact::all()]);
}); 

Route::get('/addinsurance', function () {

    return view('addinsurance');
}); 

Route::get('/removeinsurance', function () {

    return view('removeinsurance');
});

Route::post('/removeinsurancepost', function () {
    if(request("insurance-type")=="health-insurance"){
        $num =1;
    }elseif(request("insurance-type")=="travel-insurance"){
        $num=2;
    }elseif(request("insurance-type")=="car-insurance"){
        $num=3;
    }else{
        $num=4;
    }
    $item = insuranceServ::where('name',request('name'));
    $item->delete();
    return view('admindash');
}); 

Route::post('/addinsurancepost', function (Request $request) {
    $photo = $request->file('photo');
    $photoName = request('name') . '.' . $photo->getClientOriginalExtension(); 
    $photoPath = $photo->storeAs('img', $photoName, 'public');
    if(request("insurance-type")=="health-insurance"){
        $num =1;
    }elseif(request("insurance-type")=="travel-insurance"){
        $num=2;
    }elseif(request("insurance-type")=="car-insurance"){
        $num=3;
    }else{
        $num=4;
    }
    insuranceServ::create([
        'insurance_id'=>$num,
        'name'=>request('name'),
        'company'=>request('company'),
        'thing'=>request('company'),
    ]);
    return view('admindash');
}); 


Route::post('/adminresponsepost', function () {

    $message =  contact::where('id', request('id'));
    $message->update([
        'respone'=>request('response'),
        'checkrespone'=>TRUE

    ]);

    return redirect('/adminresponse')->with('success', 'User registered successfully!');

}); 

Route::post('/usercontact', function () {
    // Create a new user instance
    Contact::create([
        'name'=> request('name'),
        'email'=>request('email'),
        'message'=>request('message'),
        'respone'=>'empty',
        'checkrespone'=>FALSE
       ]);
    return redirect('/')->with('success', 'User registered successfully!');
}); 

Route::post('/login', function () {

    request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|max:8|confirmed',// This checks
        // if password matches confirm_password
    ]);

    // Create a new user instance
    $user= user::create([
        'name'=> request('name'),
        'email'=>request('email'),
        'password'=>Hash::make(request('password'))
       ]);
    Auth::login($user);
     Session::put('email',request('email'));
    return redirect('/')->with('success', 'User registered successfully!');

});
Route::get('/signout', function () {
     Session::flush();
    Auth::logout();
    return redirect('/')->with('success', 'User registered successfully!');
});
Route::get('/signin', function () {

    return view('signin');
});
Route::post('/signin', function () {
    
    $log =request()->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);
    $email = request('email');
    $password = request('password');
    
    $admin = AdminLogin::where('email', $email)->first(); // Get the first matching admin by email

    if ($admin) {
        // Check if the provided password matches the hashed password in the database
        if ( $admin->password === request('password')) {
            Session::put('email',request('email'));
            return view('admindash');
           
        } else {
            if(!Auth::attempt($log)){
                throw ValidationException::withMessages([
                   'email' =>'sorry',
                   'password'=>'sorry'
                ]);
            };
        
            request()->session()->regenerate();
            Session::put('email',request('email'));
            return redirect('/')->with('success', 'User registered successfully!');
        }
    } else {
        if(!Auth::attempt($log)){
            throw ValidationException::withMessages([
               'email' =>'sorry',
               'password'=>'sorry'
            ]);
        };
    
        request()->session()->regenerate();
        Session::put('email',request('email'));
        return redirect('/')->with('success', 'User registered successfully!');
     }

    

});

Route::get('/Show-All-Insurances', [ServiceController::class,'show'])->name('/Show-All-Insurances');
Route::get('/Cancel-Insurance', [ServiceController::class,'cancel']);
Route::delete('/Cancel-Insurance/delete', [ServiceController::class,'canceldelete']);
Route::get('/Show-All-Insurances/health-insurance', [ServiceController::class,'showhealth']);
Route::get('/Show-All-Insurances/Car-insurance-based-on-mileage', [ServiceController::class,'showcar']);
Route::Post('/Show-All-Insurances/add1', [Order2Controller::class,'add']);
Route::get('/admindash',function(){
    return view('admindash');
});

