<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\SumbUsers;
use Illuminate\Http\Request;
use App\Http\Services\AuthService;
use App\Models\SumbInvoiceSettings;
use App\Models\SumbExpenseSettings;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use App\Http\Requests\CreateUserRequest;
use App\Notifications\VerificationNotification;
use App\Models\TransactionCollections;
use DB;

class UserAdminController extends Controller {
    // :(
    public function __construct(SumbUsers $userModel, TransactionCollections $transactionCollectionsModel, Request $request){

        $this->userModel = $userModel;
        $this->transactionCollectionsModel = $transactionCollectionsModel;
        $this->request = $request;

    }

    public function create(CreateUserRequest $req){
        $userinfo = $this->request->get('userinfo');
        $payload = $req->payload();
        $payload['createdBy'] = $userinfo[3];
        $sumbUser = (new AuthService())->register($payload,true);

        return response()->json([

            'redirect_uri' => '\\user-tab'

        ], 200);

    }
    public function delete($id) {

        try{
            \DB::beginTransaction();
            $user = $this->userModel::NotAdmin()->where('id',decrypt($id))->delete();

            if(!$user){

                return response()->json( [

                    'message' => "Cant Delete Admin User"

                ], 401);

            }

            SumbInvoiceSettings::where('user_id',decrypt($id))->delete();
            SumbExpenseSettings::where('user_id',decrypt($id))->delete();
            \DB::commit();
            return response()->json([

                'message' => 'successfully deleted'

            ], 200 );

        }catch(\Exceptions $e){
            \DB::rollback();
            return response()->json([

                'message' => 'Something went wrong!'

            ], 500 );

        }catch(QueryException $q){
            \DB::rollback();
            return response()->json([
                'error' => $q,
                'message' => 'Something went wrong!'

            ], 500 );

        }

    }
    public function update(CreateUserRequest $req, $userId){

        $payload = $req->payload();

        $emailExists = $this->userModel::GetEmail($req->email)->where('id', decrypt($userId))->first();

        $user = $this->userModel::where('id', decrypt($userId))->first();

        $payload['email'] = !empty($user) && $user->accountype == 'admin' ? $user->email : $req->email;
        $payload['email_verified_at'] = !empty($emailExists) ? $emailExists['email_verified_at'] : null;

        $user->update($payload);

        if(!$emailExists){
            if($this->userModel::NotAdmin()->where('id', decrypt($userId))->first()){

                try{
                    $sumbUser = $this->userModel::GetEmail($req->email)
                                ->whereNull('email_verified_at')->first();

                    $sumbUser->notify( new VerificationNotification() );
                }catch(\Exceptions $e){

                    \Log::error($e);

                }
            }else{
                return response()->json([

                    'message' => "Cant Update Admin User Email",

                ], 401);

            }
        }

        return response()->json([

            'message' => "Succesfully Updated",

        ], 200 );

    }

    public function updateStatus(Request $req, $id){
        $status = $req->active ? 0 : 1;
        $req->validate(['active' => 'required|min:1|max:1']);
        $sumbUser = tap($this->userModel::NotAdmin()->where('id',decrypt($id)) )
        ->update([ 'active' => $status ])->first();

        if(!$sumbUser)

            return response()->json([

                'message' => "Cant Update Admin User",


            ], 401);


        return response()->json( [

            'message' => $status ? "You have succesfully Activated {$sumbUser->fullname}" : "You have succesfully deactivated {$sumbUser->fullname}"
        ], 200 );

    }

    public function sendNewPassword($id){

        $user = $this->userModel::NotAdmin()->where('id', decrypt($id) );
        if(!$user->first())
            return response()->json([

                'message' => 'Cant generate admin user password'

            ], 422 );

        $auth = ( new AuthService() )->resendNewGeneratedPassword(
            $user,
            AuthService::generateRandomString(16)
        );

        return response()->json([
            'message' => 'You Have Succesfully send a new password'
        ],200);

    }
    public function index(Request $request)
    {
        $filter = $request->query();
        $ipp = isset($filter['ipp']) ? $filter['ipp'] : 25;
        $orderBy = isset($filter['orderBy']) ? $filter['orderBy'] : 'created_at';
        $direction = isset($filter['direction']) ? $filter['direction'] : 'Desc';

        $userId = $request->get('userinfo')[0];

        $users = $this->userModel::with(['transactionCollections' => function ($query) {
                $query->whereIn('transaction_type', ['invoice','expense'])->where('is_active', 1);
            }])
            ->when(isset($filter['date-created']), function($q) use ($filter){
                $q->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $filter['date-created'])->format('Y-m-d'));
            })
            ->when(isset($filter['account-type']), function($q) use ($filter){
                $q->where( 'accountype', $filter['account-type'] );
            })
            ->when(isset($filter['search-name']), function($q) use ($filter){
                $q->where( 'fullname', 'LIKE',  $filter['search-name'] . '%' );
            })
            ->when(isset($filter['search-email']), function($q) use ($filter){
                $q->where('email',$filter['search-email']);
            })
            ->where('id','<>',$request->get('userinfo')[0])
            ->orderBy($orderBy,$direction)
            ->paginate($ipp)->toArray();

        //Get the users groupBy account type and total users active and inactive count
        $dahsboardUsersCount = $this->userModel::where('id','<>',$request->get('userinfo')[0])
                    ->whereIn('accountype', ['user','user_reg','user_pro','accountant'])
                    ->get()
                    ->groupBy('accountype');

        $active=0;$inactive=0;
        $groupWithCount = $dahsboardUsersCount->map(function ($user) use(&$active, &$inactive) {
            $active += $user->where('active', 1)->count();
            $inactive += $user->where('active', 0)->count();
            return [
                'active' => $user->where('active', 1)->count(),
                'inactive' => $user->where('active', 0)->count(),
            ];
        });

        $pagedata = [
            'userinfo' => $request->get('userinfo'),
            'filterData' => $filter,
            'users' => $users,
            'direction' => $direction,
            'orderBy' => $orderBy,
            'ipp' => $ipp,
            'users_count' => ['account_type_count' => $groupWithCount, 'active_users' => $active, 'inactive_users' => $inactive ]
        ];

        $pagedata['p'] = [ 'page' => isset($filter['page']) ? ($users['total'] == $filter['page']) ? $users['total'] : $filter['page'] + 1 : 2 ];

        $pagedata['next'] = url()->current().'?'.http_build_query($pagedata['p']);
        $pagedata['prev'] = url()->current().'?'.http_build_query(['page' => (isset($filter['page']) ? $filter['page']-1 : $users['current_page'] > 1) ? $users['current_page']-1 : 1]);

        return view('admin.admin-user', $pagedata);

    }
}
