<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Basic\InvoiceController as BasicInvoiceController;
use App\Http\Controllers\Basic\ExpenseController as BasicExpenseController;
use App\Http\Controllers\Basic\InvoiceSettingsController as BasicInvoiceSettingsController;
use App\Http\Controllers\Basic\ProfitAndLossController as BasicProfitAndLossController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\InvoiceSettingsController;
use App\Http\Controllers\ChartAccountController;
use App\Http\Controllers\ProfitAndLossController;
use App\Http\Controllers\ActivityStatement\BasAndTaxSettingsController;
use App\Http\Controllers\ActivityStatement\BasAndTaxStatementController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\BankAccountsController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankTransactionController;
use App\Http\Controllers\ReconcileTransactionsController;
use App\Http\Controllers\ReconcileDiscussionController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//main page
Route::group(['middleware' => ['guest']], function () {
    Route::controller(MainController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/signup', 'signup')->name('signup');
        //form post section
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register')->name('register');
        Route::post('/google-login', 'loginGoogle')->name('google-login');

    });
    Route::controller(BankAccountController::class)->group(function () {
        Route::post('/basiq/notification', 'accountNotification')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    });

    //forgot-password
    Route::post('/verify-email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'verify']);
    //social media logins
    // Route::post('/fb-login', [App\Http\Controllers\MainController::class, 'loginFb'])->name('google-login');
    //
});

Route::get('/verification/{encId}', [App\Http\Controllers\MainController::class, 'verify'])->name('verify');
Route::get('/logout', [App\Http\Controllers\MainController::class, 'logout'])->name('logout');


//Route::middleware(['SumbAuth'])->group(function () {
//    Route::get('/dashboard', [App\Http\Controllers\ModuleDashboard::class, 'index'])->name('Dashboard');
//});

Route::middleware(['sumbauth'])->group(function() {

    Route::get('/dashboard', [App\Http\Controllers\ModuleDashboard::class, 'index'])->name('dashboard');
    Route::get('/forgotpass', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'index']);
    Route::put('/forgotpass/{id}', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'submitPassword']);
    Route::post('/auth/email-verification', [App\Http\Controllers\MainController::class, 'sendNewVerification'] );

    Route::controller(AccountController::class)->group(function () {
        Route::get('/user-account', 'index')->name('user-account');
        Route::get('/{id}/user', 'show')->name('get-user');
        Route::post('/{id}/user-details', 'update')->name('user-account');
        Route::post('/{id}/user-profile', 'updateUserProfile')->name('user-account');
        Route::post('/{id}/deactivate-user-account', 'deactivateUserAccount')->name('deactivate-user-account');

    });


    //Pages
    Route::get('/membership', [App\Http\Controllers\PagesController::class, 'membershippage'])->name('membershipPage');

    //Basic User Api's
    Route::group(["middleware" => "role:admin"],function(){
        Route::controller(UserAdminController::class)->group(function () {
            Route::get('/user-tab', 'index')->name('user-tab');
            Route::put('/user/{userId}', 'update')->name('admin-update-user');
            Route::put('/user/status/{id}', 'updateStatus')->name('update-status');
            Route::delete('/user/{id}', 'delete')->name('delete-user');
            Route::get('/{id}/generated-password', 'sendNewPassword')->name('generate-password');
            Route::post('/admin-add-user', 'create')->name('admin-add-user');
        });
    });

    Route::group(["middleware" => "role:user,accountant,admin"], function() {
        // :(
            Route::prefix('/basic')->group(function () {
                Route::controller(BasicInvoiceController::class)->group(function () {
                    Route::get('/invoice', 'index')->name('basic/invoice');
                    Route::get('/invoice/create', 'store')->name('basic/invoice/create');
                    Route::post('/invoice-create-save', 'createInvoice')->name('basic/invoice-create-save');
                    Route::post('/add-invoice-item', 'invoiceItemForm')->name('basic/add-invoice-item');
                    Route::post('/invoice-items', 'invoiceItemFormList')->name('basic/invoice-items');
                    Route::get('/invoice-items/{id}', 'invoiceItemFormListById')->name('basic/invoice-items/{id}');
                    Route::get('/invoice/{id}/edit', 'update')->name('basic/invoice/{id}/edit');
                    Route::post('/invoice/send-email', 'sendInvoice')->name('basic/invoice/send-email');
                    Route::get('/status-change', 'statusUpdate')->name('basic/status-change');
                    Route::get('/invoice/{id}/delete', 'delete')->name('basic/invoice/{id}/delete');
                    Route::get('/invoice-tax-rates', 'invoiceTaxRates')->name('basic/invoice-tax-rates');
                    Route::get('/clone-invoice', 'cloneInvoice')->name('basic/clone-invoice');
                    Route::get('/invoice/{id}/recall', 'recallInvoice')->name('basic/invoice/{id}/recall');
                    Route::get('/invoice/{id}/email-status', 'updateInvoiceSentStatus')->name('basic/invoice/{id}/email-status');
                });

                Route::controller(BasicExpenseController::class)->group(function () {
                    Route::get('/expense', 'index')->name('basic/expense');
                    Route::get('/expense-tax-rates/{id}', 'expenseTaxRatesById')->name('basic/expense-tax-rates/{id}');
                    Route::get('/expense-tax-rates', 'expenseTaxratesList')->name('basic/expense-tax-rates');
                    Route::get('/expense-create', 'createExpense')->name('basic/expense-create');
                    Route::put('/expense/{id}/update', 'updateExpense')->name('basic/update-expense');
                    Route::get('/expense/{id}/edit', 'editExpense')->name('basic/edit-expense');
                    Route::get('/expense/{id}/view', 'viewExpense')->name('basic/view-expense');
                    Route::get('/expense/{id}/delete', 'deleteExpense')->name('basic/delete-expense');
                    Route::post('/expense-save', 'saveExpense')->name('basic/expense-create-save');
                    Route::get('/expense-void', 'expenseVoid')->name('basic/expense-void');
                    Route::get('/expense-status-change/', 'statusChange')->name('basic/expense-status-change');
                    Route::get('/expense-chart-accounts', 'expenseChartAccounts')->name('basic/expense-chart-accounts');
                    Route::post('/expense-logo-upload', 'logoUpload')->name('basic/expense-logo-upload');

                });

                Route::controller(BasicInvoiceSettingsController::class)->group(function () {
                    Route::get('/invoice/settings', 'invoiceSettingsForm')->name('basic/invoice/settings');
                    Route::post('/invoice/settings/add', 'store')->name('basic/invoice/settings/add');
                    Route::post('/invoice-logo-upload', 'logoUpload')->name('basic/invoice-logo-upload');
                    Route::post('/invoice/settings/edit', 'update')->name('basic/invoice/settings/edit');

                });

                Route::controller(BasicProfitAndLossController::class)->group(function () {
                    Route::get('/profit-loss', 'index')->name('basic/profit-loss');
                    Route::get('/reports', 'reports')->name('basic/reports');
                    Route::get('/profit-loss/export-report', 'exportProfitAndLoss')->name('profit-loss/export-report');
                    Route::get('/business-activity/export-report', 'exportTransactions')->name('business-activity/export-report');
                });

                Route::post('/upgrade', [App\Http\Controllers\MainController::class, 'upgrade'])->name('upgrade');

            });
        });


    //Pro User Api's
    Route::group(["middleware" => "role:user_pro,user_reg,accountant,admin"], function() {

        //Expense API's
        Route::controller(ExpenseController::class)->group(function () {
            Route::get('/expense', 'index')->name('expense');
            Route::get('/expense-create', 'createExpense')->name('expense-create');
            Route::put('/expense/{id}/update', 'updateExpense')->name('update-expense');
            Route::get('/expense/{id}/edit', 'editExpense')->name('edit-expense');
            Route::get('/expense/{id}/view', 'viewExpense')->name('view-expense');
            Route::get('/expense/{id}/delete', 'deleteExpense')->name('delete-expense');
            Route::post('/expense-save', 'saveExpense')->name('expense-create-save');
            Route::get('/expense-void', 'expenseVoid')->name('expense-void');
            Route::get('/expense-status-change/', 'statusChange')->name('expense-status-change');
            Route::get('/expense-tax-rates/{id}', 'expenseTaxRatesById')->name('expense-tax-rates/{id}');
            Route::get('/expense-tax-rates', 'expenseTaxratesList')->name('expense-tax-rates');
            Route::get('/expense-chart-accounts', 'expenseChartAccounts')->name('expense-chart-accounts');
            Route::post('/expense-logo-upload', 'logoUpload')->name('expense-logo-upload');
        });

        //Invoice API's
        Route::controller(InvoiceController::class)->group(function () {
            Route::get('/invoice', 'index')->name('invoice');
            Route::get('/invoice/create', 'store')->name('invoice/create');
            Route::post('/invoice-create-save', 'createInvoice')->name('invoice-create-save');
            Route::post('/search-client', 'searchClient')->name('search-client');
            Route::post('/search-invoice-item', 'searchInvoiceItem')->name('search-invoice-item');
            Route::post('/add-invoice-item', 'invoiceItemForm')->name('add-invoice-item');
            Route::post('/invoice-items', 'invoiceItemFormList')->name('invoice-items');
            Route::get('/invoice-items/{id}', 'invoiceItemFormListById')->name('invoice-items/{id}');
            Route::get('/invoice/{id}/edit', 'update')->name('/invoice/{id}/edit');
            Route::post('/invoice/send-email', 'sendInvoice')->name('/invoice/send-email');
            Route::get('/status-change', 'statusUpdate')->name('status-change');
            Route::get('/invoice/{id}/delete', 'delete')->name('/invoice/{id}/delete');
            Route::get('/invoice-tax-rates', 'invoiceTaxRates')->name('invoice-tax-rates');
            Route::get('/clone-invoice', 'cloneInvoice')->name('clone-invoice');
            Route::get('/invoice/{id}/recall', 'recallInvoice')->name('/invoice/{id}/recall');
            Route::get('/invoice/{id}/email-status', 'updateInvoiceSentStatus')->name('/invoice/{id}/email-status');
        });

        Route::controller(InvoiceSettingsController::class)->group(function () {
            Route::get('/invoice/settings', 'invoiceSettingsForm')->name('invoice/settings');
            Route::post('/invoice/settings/add', 'store')->name('invoice/settings/add');
            Route::post('/invoice/settings/edit', 'update')->name('invoice/settings/edit');
            Route::post('/invoice-logo-upload', 'logoUpload')->name('invoice-logo-upload');
        });

        Route::controller(ChartAccountController::class)->group(function () {
            Route::get('/chart-accounts-parts/{id}', 'chartAccountsPartsById')->name('chart-accounts-parts/{id}');
            Route::get('/chart-accounts-parts', 'chartAccountsPartsList')->name('chart-accounts-parts');
            Route::get('/chart-accounts', 'index')->name('chart-accounts');
            Route::post('/chart-account/create', 'create')->name('chart-account/create');
            Route::post('/chart-account/edit/{id}', 'update')->name('chart-account/edit/{id}');
        });


        Route::controller(ProfitAndLossController::class)->group(function () {
            //Profit&loss And Reports
            Route::get('/profit-loss', 'index')->name('profit-loss');
            Route::get('/reports', 'reports')->name('reports');

            //Export to CSV/EXCEL (Profit&loss And Reports)
            Route::get('/profit-loss/export-report', 'exportProfitAndLoss')->name('profit-loss/export-report');
            Route::get('/business-activity/export-report', 'exportTransactions')->name('business-activity/export-report');

        });

        Route::controller(BasAndTaxSettingsController::class)->group(function () {
            Route::get('/bas/settings', 'index')->name('bas/settings');
            Route::post('/bas/settings', 'create')->name('bas/settings');
            Route::post('/bas/settings/verify', 'verify')->name('bas/settings/verify');
        });

        Route::controller(BasAndTaxStatementController::class)->group(function () {
            Route::get('/bas/statement', 'index')->name('bas/statement');
            Route::post('/bas/statement', 'store')->name('bas/statement');
            Route::get('/bas/statement/edit', 'edit')->name('bas/statement/edit');
            Route::delete('/bas/statement/delete', 'destroy')->name('bas/statement/delete');
        });

        Route::get('/invoice/history', [App\Http\Controllers\InvoiceHistoryController::class, 'index'])->name('/invoice/history');

        Route::get('/bas/overview', [App\Http\Controllers\ActivityStatement\BasAndTaxOverviewController::class, 'index'])->name('bas/overview');

        Route::get('/balance-sheet', [App\Http\Controllers\BalanceSheetController::class, 'index'])->name('balance-sheet');

        // Route::get('/bank/cash-receipt', [App\Http\Controllers\CashReceiptController::class, 'showCashReceipt'])->name('bank/cash-receipt');
        // Route::post('/bank/cash-receipt/edit', [App\Http\Controllers\CashReceiptController::class, 'updateCashReceipt'])->name('bank/cash-receipt/edit');

        Route::get('/bank/cash-receipt', [App\Http\Controllers\CashReceiptController::class, 'showCashReceipt'])->name('bank/cash-receipt');
        Route::post('/bank/cash-receipt/edit', [App\Http\Controllers\CashReceiptController::class, 'updateCashReceipt'])->name('bank/cash-receipt/edit');

    //------------UnUsed and Not sure about these routes----Start----------//

        Route::post('/invoice-particulars-add', [App\Http\Controllers\InvoiceController::class, 'invoice_particulars_add'])->name('invoice-particulars-add');
        Route::post('/invoice-particulars-delete', [App\Http\Controllers\InvoiceController::class, 'invoice_particulars_delete'])->name('invoice-particulars-delete');
        Route::get('/invoice-logo-upload', [App\Http\Controllers\InvoiceController::class, 'invoice_logo_upload'])->name('invoice-logo-upload');
        Route::post('/invoice-logo-process', [App\Http\Controllers\InvoiceController::class, 'invoice_logo_process'])->name('invoice-logo-process');
        Route::get('/invoice-particulars-add', [App\Http\Controllers\InvoiceController::class, 'invoice_particulars_add'])->name('invoice-particulars-add2');


        //File upload
        Route::prefix('documents')->group(function () {
            Route::get('/', [App\Http\Controllers\DocumentUploadController::class, 'index']);
            Route::post('/', [App\Http\Controllers\DocumentUploadController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\DocumentUploadController::class, 'docedit'])->name('doc-edit');
        });

        Route::get('/doc-upload', [App\Http\Controllers\DocumentUploadController::class, 'index'])->name('doc-upload');
        Route::post('/store', [App\Http\Controllers\DocumentUploadController::class, 'store'])->name('store');
        Route::get('/doc-edit', [App\Http\Controllers\DocumentUploadController::class, 'docedit'])->name('doc-edit');
        Route::patch('/doc-edit-process/{id}', [App\Http\Controllers\DocumentUploadController::class, 'doceditprocess'])->name('DocumentUploadController.doc-edit-process');
        Route::delete('/destroy', [App\Http\Controllers\DocumentUploadController::class, 'destroy'])->name('DocumentUploadController.destroy');
        Route::get('/downloadfile', [App\Http\Controllers\DocumentUploadController::class, 'downloadFile'])->name('DocumentUploadController.downloadfile');
        Route::get('/docview', [App\Http\Controllers\DocumentUploadController::class, 'docview'])->name('docview');

        //testing
        Route::get('/testing', [App\Http\Controllers\InvoiceController::class, 'testing'])->name('testing');
        Route::get('/testpdf', [App\Http\Controllers\InvoiceController::class, 'testpdf'])->name('testpdf');
        Route::get('/testformat', [App\Http\Controllers\InvoiceController::class, 'testformat'])->name('testformat');
    //----------------------END----------------------------------//

    });

    Route::group(["middleware" => "role:user_pro,accountant,admin"], function() {
        //Banking Account and Transactions

        Route::controller(BankAccountsController::class)->group(function () {
            Route::get('/bankAccounts', 'index')->name('bankAccounts');
            Route::get('/bankTransactions', 'transactions')->name('bankTransactions');
            Route::get('/reconcileTransactions', 'reconcileTransactions')->name('reconcileTransactions');
            Route::get('/get-reconcile-matches', 'getReconcileMatches')->name('get-reconcile-matches');
            Route::get('/add-bank-account', 'addBankAccount')->name('add-bank-account');
            Route::put('/reconcile-transaction', 'reconcileTransaction')->name('reconcile-transaction');
            Route::post('/create/transaction', 'saveTransaction')->name('create/transaction');
            Route::get('/match-transactions', 'getMatchTransaction')->name('add-bank-account');
            Route::post('/create-reconcile-transaction', 'createAndReconcileTransaction')->name('create-reconcile-transaction');
            Route::post('/bank-account/{id}/disable', 'disableBankAccount')->name('bank-account/{id}/disable');

        });


        //----------------------Basiq new implementation---------------------
        Route::controller(BankAccountController::class)->group(function () {
            Route::get('/bank/accounts', 'index')->name('bank/accounts');
            Route::get('/bank/accounts/add', 'store')->name('bank/accounts/add');
            Route::delete('/bank/accounts/{id}', 'disableBankAccount')->name('bank/accounts/{id}');
        });
        Route::controller(BankTransactionController::class)->group(function () {
            Route::get('/bank/account/transactions', 'index')->name('bank/account/transactions');
        });
        Route::controller(ReconcileTransactionsController::class)->group(function () {
            Route::get('/bank/transaction/reconcile', 'index')->name('bank/transaction/reconcile');
            Route::post('/transaction/create', 'storeTransaction')->name('transaction/create');
            Route::post('/transaction/create-reconcile', 'createAndReconcileTransaction')->name('transaction/create-reconcile');
            Route::get('/transaction/match', 'matchTransaction')->name('transaction/match');
            Route::put('/transaction/reconcile', 'reconcileTransaction')->name('transaction/reconcile');
            Route::get('/account/transactions', 'accountTransactions')->name('account/transactions');
            Route::get('/accounts/{accountId}/transactions/{transactionId}', 'showAccountTransaction')->name('account/transaction/view');
            Route::put('/accounts/{accountId}/transactions/{transactionId}/unreconcile', 'unReconcileTransaction')->name('account/transactions/unreconcile');

        });

        Route::controller(ReconcileDiscussionController::class)->group(function () {
            Route::post('/transaction/{id}/discuss', 'store')->name('transaction/discuss');
            Route::get('/transaction/{id}/history', 'getHistoryAndDiscussion')->name('history');
        });
    });
});
