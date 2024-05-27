<?php

namespace App\Http\Controllers\Account;

use Carbon\Carbon;
use App\Models\SumbUsers;
use Illuminate\Http\Request;
use App\Http\Services\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UserDetailsRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\CreateUserProfileRequest;
use App\Notifications\VerificationNotification;

class AccountController extends Controller {

    public function __construct(SumbUsers $userModel){

        $this->userModel = $userModel;

    }

    public function show($id)
    {

        $user = $this->userModel::find( decrypt($id) );

        // $path = Storage::disk('local');

        // if($path->exists($user->profilepic)){

        //     $path->url($user->profilepic);

        // }

        if(!$user)

            return response()->json([

                'message' => 'User Not Found'

            ], 404 );


        return response()->json( [

            'data' => $user

        ], 200 );

    }
    public function index(Request $request)
    {
        $pagedata = [

            'userinfo' => $request->get('userinfo')

        ];
        $user = SumbUsers::where('id', $request->get('userinfo')[0])->first();

        $pagedata['password'] = $user['password'];
        return view('account.user-account', $pagedata);

    }
    public function update(UserDetailsRequest $request, $id)
    {
        $payload = $request->payload();
        $id = decrypt($id);
        try{

            $user = $this->userModel->find( $id );

            if(!empty($payload['user']['password']))
            {
                if(($user->password != $payload['user']['password']) && $payload['user']['password'] != $payload['user']['password_confirmation']){
                    return response()->json([

                        'message' => 'Invalid password or password confirmation must match',

                    ],422);
                }else{
                    $payload['user']['password'] = md5($payload['user']['password']);
                }
            }

            $payload['user']['email_verified_at'] = !empty($user) && $user['email'] == $payload['user']['email'] ? $user['email_verified_at'] : null;
            \DB::beginTransaction();

            $user->update($payload['user']);

            if(empty($payload['user']['email_verified_at']))
            {
                try{
                    $sumbUser = $this->userModel::GetEmail($payload['user']['email'])->first();

                    $sumbUser->notify( new VerificationNotification() );
                }catch(\Exceptions $e){

                    \Log::error($e);

                }
            }

            AuthService::storeUserSession($user::find($id));

            \DB::commit();

            return response()->json([

                'message' => 'User Succesfully updated',

            ],200);


        } catch(\Exceptions $e){

            \DB::rollback();
            // if(\Storage::disk('local')->exists($path))

            //     \Storage::disk('local')->deleteDirectory($path);

        }

    }

    public function updateUserProfile(CreateUserProfileRequest $request, $id)
    {
        $payload = $request->payload();
        $id = decrypt($id);
        try{

            if( $request->file('photo') ){
                if(Storage::disk('local')->exists('public/'.$id.'_images'))
                    Storage::disk('local')->deleteDirectory('public/'.$id.'_images');

                $payload['user']['profilepic'] = $request->file('photo')->store("public/{$id}_images");

                $user = $this->userModel->find( $id );

                \DB::beginTransaction();
                //
                $user->update($payload['user']);
                //
                AuthService::storeUserSession($user::find($id));

                \DB::commit();
            }

        } catch(\Exceptions $e){

            \DB::rollback();
        }
    }

    public function deactivateUserAccount(Request $request, $id)
    {
        $id = decrypt($id);
        try{

            \DB::beginTransaction();

                $this->userModel->where('id', $id)->where('active', 1)->update(['active' => 0]);

            \DB::commit();
            return redirect()->route('logout');

        } catch(\Exceptions $e){

            \DB::rollback();

        }
    }

}
