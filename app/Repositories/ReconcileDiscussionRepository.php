<?php

namespace App\Repositories;

use App\Interfaces\ReconcileDiscussionRepositoryInterface;
use App\Models\ReconcileDiscuss;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ReconcileDiscussionRepository implements ReconcileDiscussionRepositoryInterface
{
    public function __construct(ReconcileDiscuss $discussModel){
        $this->discussModel = $discussModel;
    }

    public function create($request, $userId)
    {
        try{

            if(!empty($request['existing_discussion']))
            {

                if((!empty($request['discussions'][0]) && empty($request['discussions'][1])) || (!empty($request['discussions'][0]) && !empty($request['discussions'][1])))
                {
                    $request['discuss_history'] = trim($request['current_discussion']);
                    $request['discuss'] = trim($request['current_discussion']);

                    $this->storeDiscussion($request, $userId);

                }else if(empty($request['discussions'][0]) && !empty($request['discussions'][1]))
                {
                    $request['discuss_history'] = trim($request['discussions'][1]);
                    $request['discuss'] = trim($request['current_discussion']);

                    $this->storeDiscussion($request, $userId);

                }
                else if(empty($request['current_discussion']))
                {
                    $request['discuss_history'] = trim($request['current_discussion']);
                    $request['discuss'] = trim($request['current_discussion']);

                    $this->storeDiscussion($request, $userId);
                }
            }else{

                $request['discuss_history'] = trim($request['current_discussion']);
                $request['discuss'] = trim($request['current_discussion']);

                $this->storeDiscussion($request, $userId);
            }

            return response()->json([
                'discussion' => trim($request['current_discussion']),
            ],201);
        }
        catch(\Exceptions $e)
        {
            \Log::error($e);
        }
    }

    public function index($userId, $paymentId)
    {
            return $this->discussModel::with('discussionPayment')
            ->whereHas('discussionPayment', function ($query) use($paymentId) {
                $query->where('payment_id', $paymentId);
            })
            ->where('user_id', $userId)->get();

    }
    public function getDiscussionIds($userId, $bankTransactionId)
    {
        return $this->discussModel::where('user_id', $userId)->where('transaction_id', $bankTransactionId)->get();
    }

    private function storeDiscussion($discussions, $userId)
    {
        $dateTime = Carbon::now()->toDateTimeString();

        return  $this->discussModel::create([
            'transaction_id' => $discussions['transaction_id'],
            'user_id' => $userId,
            'date_time' => $dateTime,
            'discuss_history' => trim($discussions['discuss_history']),
            'discuss' => trim($discussions['discuss']),
            'is_active' => 1
        ]);
    }
}
