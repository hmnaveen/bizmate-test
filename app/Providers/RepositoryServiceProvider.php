<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\TransactionCollectionRepositoryInterface;
use App\Repositories\TransactionCollectionRepository;
use App\Interfaces\ClientsRepositoryInterface;
use App\Repositories\ClientsRepository;
use App\Interfaces\PaymentHistoryRepositoryInterface;
use App\Repositories\PaymentHistoryRepository;
use App\Interfaces\InvoiceHistoryRepositoryInterface;
use App\Repositories\InvoiceHistoryRepository;
use App\Interfaces\BankRepositoryInterface;
use App\Repositories\BankRepository;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Interfaces\ChartAccountRepositoryInterface;
use App\Repositories\ChartAccountRepository;
use App\Interfaces\TaxRateRepositoryInterface;
use App\Repositories\TaxRateRepository;
use App\Interfaces\InvoiceItemRepositoryInterface;
use App\Repositories\InvoiceItemRepository;
use App\Interfaces\ReconcileTransactionRepositoryInterface;
use App\Repositories\ReconcileTransactionRepository;
use App\Interfaces\MinorAdjustmentRepositoryInterface;
use App\Repositories\MinorAdjustmentRepository;
use App\Interfaces\BankAccountsRepositoryInterface;
use App\Repositories\BankAccountsRepository;
use App\Interfaces\BankTransactionsRepositoryInterface;
use App\Repositories\BankTransactionsRepository;
use App\Interfaces\ReconcileDiscussionRepositoryInterface;
use App\Repositories\ReconcileDiscussionRepository;
use App\Interfaces\PaymentRepositoryInterface;
use App\Repositories\PaymentRepository;
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TransactionCollectionRepositoryInterface::class, TransactionCollectionRepository::class);
        $this->app->bind(ClientsRepositoryInterface::class, ClientsRepository::class);
        $this->app->bind(PaymentHistoryRepositoryInterface::class, PaymentHistoryRepository::class);
        $this->app->bind(InvoiceHistoryRepositoryInterface::class, InvoiceHistoryRepository::class);
        $this->app->bind(BankRepositoryInterface::class, BankRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ChartAccountRepositoryInterface::class, ChartAccountRepository::class);
        $this->app->bind(TaxRateRepositoryInterface::class, TaxRateRepository::class);
        $this->app->bind(InvoiceItemRepositoryInterface::class, InvoiceItemRepository::class);
        $this->app->bind(ReconcileTransactionRepositoryInterface::class, ReconcileTransactionRepository::class);
        $this->app->bind(MinorAdjustmentRepositoryInterface::class, MinorAdjustmentRepository::class);
        $this->app->bind(BankAccountsRepositoryInterface::class, BankAccountsRepository::class);
        $this->app->bind(BankTransactionsRepositoryInterface::class, BankTransactionsRepository::class);
        $this->app->bind(ReconcileDiscussionRepositoryInterface::class, ReconcileDiscussionRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);

    }
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
