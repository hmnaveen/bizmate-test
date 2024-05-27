<?php

    namespace App\Traits;

    use App\Models\TransactionCollections;
    use Illuminate\Http\Request;
    use Carbon\Carbon;
    use DB;

    trait InvoiceAndExpenseGraph {
        
        public function getDates($request)
        {
            $transaction_dates = [];
            $weekly_transaction = [];

            //Get Min and Max date
            $dates = TransactionCollections::GetMinAndMaxDate()->first();

            //Current week date
            $start_of_current_week = Carbon::parse(Carbon::now()->startOfWeek())->toDateString();
            $end_of_current_week = Carbon::parse(Carbon::now()->endOfWeek())->toDateString();

            //Previous week date
            $start_of_prev_week = Carbon::parse(Carbon::now()->subDays(7)->startOfWeek())->toDateString();
            $end_of_prev_week = Carbon::parse(Carbon::now()->subDays(7)->endOfWeek())->toDateString();

            //Next week date
            $start_of_next_week = Carbon::parse(Carbon::now()->addDays(7)->startOfWeek())->toDateString();
            $end_of_next_week = Carbon::parse(Carbon::now()->addDays(7)->endOfWeek())->toDateString();

            //Older week date
            $start_of_older_week = !empty($dates) && $dates->start_date ? $dates->start_date : Carbon::parse(Carbon::now()->subDays(14)->startOfWeek())->toDateString();
            $end_of_older_week =  Carbon::parse(Carbon::now()->subDays(14)->endOfWeek())->toDateString();

            //Future week date
            $start_of_future_week = Carbon::parse(Carbon::now()->addDays(14)->startOfWeek())->toDateString();
            $end_of_future_week = !empty($dates) && $dates->end_date ? $dates->end_date : Carbon::parse(Carbon::now()->addDays(14)->endOfWeek())->toDateString();

            $transaction_dates[] = ['start_date' => $start_of_older_week, 'end_date'=> $end_of_older_week];
            $transaction_dates[] = ['start_date' => $start_of_prev_week, 'end_date'=> $end_of_prev_week];
            $transaction_dates[] = ['start_date' => $start_of_current_week, 'end_date'=> $end_of_current_week];
            $transaction_dates[] = ['start_date' => $start_of_next_week, 'end_date'=> $end_of_next_week];
            $transaction_dates[] = ['start_date' => $start_of_future_week, 'end_date'=> $end_of_future_week];

            $invoice_transaction_years = [];
            //Current year
            $invoice_transaction_years[] = ['year' => Carbon::parse(Carbon::now())->format('Y')];
            
            //Previous year
            $invoice_transaction_years[] = ['year' => Carbon::parse(Carbon::now()->subYears(1))->format('Y')];
            
            return ['years' => $invoice_transaction_years, 'weeks' => $transaction_dates];
        }

        public function getInvoiceAndExpenseGraphs(Request $request, $type)
        {
            $userinfo = $request->get('userinfo');

            $transaction_dates = [];
            $weekly_transaction = [];
            $transaction_dates = $this->getDates($request);
            
            if($type == 'invoice')
            {
                foreach($transaction_dates['years'] as $transaction_year)
                {
                    $monthly_transaction[] = $this->invoiceTransactionsMothly($request, $userinfo, $transaction_year['year'], $type);
                }

                foreach($transaction_dates['weeks'] as $transaction_date)
                {
                    $weekly_transaction[] = $this->transactionsWeekly($request, $userinfo, $transaction_date['start_date'], $transaction_date['end_date'], $type == 'invoice' ? ['invoice'] : ['invoice', 'expense'] );
                }

                return [ 'weekly_transaction' => $weekly_transaction, 'monthly_transaction' => $monthly_transaction ];
            }
            if($type == 'expense')
            {
                foreach($transaction_dates['years'] as $transaction_year)
                {
                    $monthly_transaction[] = $this->expenseTransactionsMothly($request, $userinfo, $transaction_year['year'], $type);
                }
                
                foreach($transaction_dates['weeks'] as $transaction_date)
                {
                    $weekly_transaction[] = $this->transactionsWeekly($request, $userinfo, $transaction_date['start_date'], $transaction_date['end_date'], $type == 'expense' ? ['expense'] : ['invoice', 'expense'] );
                }

                return [ 'weekly_transaction' => $weekly_transaction, 'monthly_transaction' => $monthly_transaction ];
            }

            if($type == 'dashboard')
            {
                foreach($transaction_dates['years'] as $transaction_year)
                {
                    $expense_monthly_transaction[] = $this->expenseTransactionsMothly($request, $userinfo, $transaction_year['year'], $type);
                }

                foreach($transaction_dates['years'] as $transaction_year)
                {
                    $invoice_monthly_transaction[] = $this->invoiceTransactionsMothly($request, $userinfo, $transaction_year['year'], $type);
                }
                
                foreach($transaction_dates['weeks'] as $transaction_date)
                {
                    $weekly_transaction[] = $this->transactionsWeekly($request, $userinfo, $transaction_date['start_date'], $transaction_date['end_date'], $type == 'expense' ? ['expense'] : ['invoice', 'expense'] );
                }
                return [ 'weekly_transaction' => $weekly_transaction, 'invoice_monthly_transaction' => $invoice_monthly_transaction, 'expense_monthly_transaction' => $expense_monthly_transaction ];
            }
           
        }

        private function transactionsWeekly($request, $userinfo, $start_date, $end_date, $type)
        {
            $total_invoice_bar_chart = TransactionCollections::groupBy('due_date')->groupBy('transaction_type')
                ->select( DB::raw('due_date, transaction_type, sum(total_amount+amount_paid) as total') )
                ->where('is_active', 1)
                ->whereIn('transaction_type', $type)
                ->whereIn('status', ['Unpaid', 'Paid', 'PartlyPaid'])
                ->whereBetween('due_date', [$start_date, $end_date])
                ->where('user_id', $userinfo[0])
                ->orderBy('due_date')
                ->get();

            return ['start_date' => $start_date, 'end_date' => $end_date, 'weekly_transactions' => !empty($total_invoice_bar_chart) ? $total_invoice_bar_chart->toArray() : ''];
        }
    
        private function invoiceTransactionsMothly($request, $userinfo, $year, $type)
        {
            $mothly_data = TransactionCollections::select(DB::raw("MONTH(due_date) as month, sum(total_amount+amount_paid) as total"))
                ->whereYear('due_date', $year)
                ->where('is_active', 1)
                ->where('transaction_type', 'invoice')
                ->whereIn('status', ['Unpaid', 'Paid', 'PartlyPaid'])
                ->where('user_id', $userinfo[0])
                ->groupBy('month')
                ->get();
    
            $allMonths = [1,2,3,4,5,6,7,8,9,10,11,12];
    
            $currentMonths = array_column($mothly_data->toArray(), 'month');
    
            $notIncludedMonth = array_diff($allMonths,$currentMonths);
    
            foreach ($notIncludedMonth as $month) {
                $mothly_data[] = [
                    'total' => 0.00,
                    'month' => $month,
                ];
            }
            $sort_months = $mothly_data->toArray();
            $this->sortArrayByMonth($sort_months, 'month');
            
            return ['year' => $year, 'data'=> !empty($sort_months) ? $sort_months : ''];
        }

        private function expenseTransactionsMothly($request, $userinfo, $year, $type)
        {
            $mothly_data = TransactionCollections::select(DB::raw("transaction_type, MONTH(due_date) as month, sum(total_amount+amount_paid) as total"))
                ->whereYear('due_date', $year)
                ->where('is_active', 1)
                ->where('transaction_type', 'expense')
                ->whereIn('status', ['Unpaid', 'Paid', 'PartlyPaid'])
                ->where('user_id', $userinfo[0])
                ->groupBy('month')
                ->groupBy('transaction_type')
                ->get();

            $allMonths = [1,2,3,4,5,6,7,8,9,10,11,12];

            $currentMonths = array_column($mothly_data->toArray(), 'month');

            $notIncludedMonth = array_diff($allMonths,$currentMonths);

            foreach ($notIncludedMonth as $month) {
                $mothly_data[] = [
                    'total' => 0.00,
                    'month' => $month,
                ];
            }
            $sort_months = $mothly_data->toArray();
            $this->sortArrayByMonth($sort_months, 'month');
            
            return ['year' => $year, 'data'=>!empty($sort_months) ? $sort_months : ''];
        }
    
        public function sortArrayByMonth(&$array, $subfield)
        {
            $sortarray = array();
            foreach ($array as $key => $row)
            {
                $sortarray[$key] = $row[$subfield];
            }
        
            array_multisort($sortarray, SORT_ASC, $array);
        }
    
    }
?>