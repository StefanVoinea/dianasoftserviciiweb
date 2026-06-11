<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
   
    public function user(Request $request){
        $user=User::where('id',$request->user()->id)->with(['companies','dianasoftmenuoptions','permissions','gestiuni'])->get()->first();
        
        return $user;
    }
    public function login(Request $request)
    {
       
    	//$http = new \GuzzleHttp\Client;
       
        
        try {
            // $response = $http->post(config('services.passport.login_endpoint'), [
            //     'form_params' => [
            //         'grant_type' => 'password',
            //         'client_id' => config('services.passport.client_id'),
            //         'client_secret' => config('services.passport.client_secret'),
            //         'username' => $request->username,
            //         'password' => $request->password,
            //         ]
            //         ]);
            $request = Request::create('/oauth/token', 'POST', [
                'grant_type'    => 'password',
                'client_id'     => config('passport.password_client.id'),
                'client_secret' => config('passport.password_client.secret'),
                'username'      => $request->email,
                'password'      => $request->password,
                'scope'         => '',
            ]);
            $response = app()->handle($request);        
            
    		return $response;
    	
    	} catch (\Exception $e) // catch (\GuzzleHttp\Exception\BadResponseException $e) 
        {
    		if($e->getCode()==400) {
    			return response()->json('Invalid Request, Please enter a username or a password.',$e->getCode());
    		} else if ($e->getCode()==401) {
    			return response()->json('Your credentials are incorrect. Please try again',$e->getCode());
    		}
           
    		return response()->json('Something went wrong on the server.', $e->getCode() );
    	};
    
    }
    public function register(Request $request)
    {
        $request->validate( [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8']

        ]);
         return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'user_type'=>'user',
            'password' => Hash::make($request->password),
        ]);
    
    }
    public function logout()
    {
        auth()->user()->tokens->each(function($token,$key){
            $token->delete();
        });
        return response()->json('Logged out successfully',200);
    }
}
