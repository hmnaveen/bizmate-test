<?php

namespace App\Http\Controllers;

use App\Interfaces\ReconcileDiscussionRepositoryInterface;
use App\Interfaces\InvoiceHistoryRepositoryInterface;
use App\Interfaces\ReconcileTransactionRepositoryInterface;
use Illuminate\Http\Request;

class ReconcileDiscussionController extends Controller
{
    private ReconcileDiscussionRepositoryInterface $reconcileDiscussionRepository;
    private InvoiceHistoryRepositoryInterface $invoiceHistoryRepository;
    private ReconcileTransactionRepositoryInterface $reconcileTransactionRepository;


    public function __construct(
        ReconcileDiscussionRepositoryInterface $reconcileDiscussionRepository,
        InvoiceHistoryRepositoryInterface $invoiceHistoryRepository,
        ReconcileTransactionRepositoryInterface $reconcileTransactionRepository,
    )
    {
        $this->middleware('invoice_seetings');
        $this->discussionRepository = $reconcileDiscussionRepository;
        $this->invoiceHistoryRepository = $invoiceHistoryRepository;
        $this->reconcileTransactionRepository = $reconcileTransactionRepository;
    }


    public function store(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $request->validate([
            'transaction_id' => 'required|exists:bank_transactions,id',
        ]);

        return $this->discussionRepository->create($request->all(), $userinfo[0]);
    }

    public function getHistoryAndDiscussion(Request $request)
    {
        $userinfo = $request->get('userinfo');
        $history = $this->invoiceHistoryRepository->index($userinfo[0], $request->transaction_collection_id);
        if(!empty($history))
        {
            $discussion = $this->discussionRepository->index($userinfo[0], $request->id);

                $discussionAndHistory = $discussion->merge($history)
                    ->transform( function ($item) use($userinfo){
                        if(!empty($item->date_time)) {
                            $item->created_at = $item->date_time;
                        }
                        if(!empty($item->discuss)) {
                            $item->action = 'Discussion';
                            $item->description = $item->discuss_history;
                            $item->user_name = $userinfo[1];
                        }
                        return $item;
                    })
                    ->sortByDesc('created_at');

                return response()->json( [

                    'data' => $discussionAndHistory

                ], 200);
        }
        return response()->json( [

            'data' => $history

        ], 200);

    }
}
