<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\VirtualCategory\VirtualCategoryRepositoryInterface;
use App\Repositories\VirtualCategory\VirtualCategoryRepository;
use App\Repositories\Sku\SkuRepositoryInterface;
use App\Repositories\Sku\SkuRepository;
use App\Repositories\Item\ItemRepositoryInterface;
use App\Repositories\Item\ItemRepository;
use App\Repositories\DetailPositiveExtra\DetailPositiveExtraRepositoryInterface;
use App\Repositories\DetailPositiveExtra\DetailPositiveExtraRepository;
use App\Repositories\DetailPassiveExtra\DetailPassiveExtraRepositoryInterface;
use App\Repositories\DetailPassiveExtra\DetailPassiveExtraRepository;
use App\Repositories\Requirement\RequirementRepositoryInterface;
use App\Repositories\Requirement\RequirementRepository;
use App\Repositories\RequirementMemo\RequirementMemoRepository;
use App\Repositories\RequirementMemo\RequirementMemoRepositoryInterface;
use App\Repositories\ReceivingAddress\ReceivingAddressRepository;
use App\Repositories\ReceivingAddress\ReceivingAddressRepositoryInterface;
use App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface;
use App\Repositories\PaymentMethod\PaymentMethodRepository;
use App\Repositories\Buyer\BuyerRepositoryInterface;
use App\Repositories\Buyer\BuyerRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\MainOrder\MainOrderRepositoryInterface;
use App\Repositories\MainOrder\MainOrderRepository;
use App\Repositories\SubOrder\SubOrderRepositoryInterface;
use App\Repositories\SubOrder\SubOrderRepository;
use App\Repositories\Seller\SellerRepositoryInterface;
use App\Repositories\Seller\SellerRepository;
use App\Repositories\DeliveryInfo\DeliveryInfoRepositoryInterface;
use App\Repositories\DeliveryInfo\DeliveryInfoRepository;
use App\Repositories\Employee\EmployeeRepository;
use App\Repositories\Employee\EmployeeRepositoryInterface;
use App\Repositories\Activity\ActivityRepository;
use App\Repositories\Activity\ActivityRepositoryInterface;
use App\Repositories\GroupItem\GroupItemRepository;
use App\Repositories\GroupItem\GroupItemRepositoryInterface;
use App\Repositories\ItemTag\ItemTagRepositoryInterface;
use App\Repositories\ItemTag\ItemTagRepository;
use App\Repositories\SecKill\SecKillRepositoryInterface;
use App\Repositories\SecKill\SecKillRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );

        $this->app->bind(
            VirtualCategoryRepositoryInterface::class,
            VirtualCategoryRepository::class
        );

        $this->app->bind(
            SkuRepositoryInterface::class,
            SkuRepository::class
        );

        $this->app->bind(
            ItemRepositoryInterface::class,
            ItemRepository::class
        );

        $this->app->bind(
            DetailPositiveExtraRepositoryInterface::class,
            DetailPositiveExtraRepository::class
        );

        $this->app->bind(
            DetailPassiveExtraRepositoryInterface::class,
            DetailPassiveExtraRepository::class
        );

        $this->app->bind(
            RequirementRepositoryInterface::class,
            RequirementRepository::class
        );

        $this->app->bind(
            ReceivingAddressRepositoryInterface::class,
            ReceivingAddressRepository::class
        );

        $this->app->bind(
            PaymentMethodRepositoryInterface::class,
            PaymentMethodRepository::class
        );

        $this->app->bind(
            BuyerRepositoryInterface::class,
            BuyerRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        $this->app->bind(
            MainOrderRepositoryInterface::class,
            MainOrderRepository::class
        );

        $this->app->bind(
            RequirementMemoRepositoryInterface::class,
            RequirementMemoRepository::class
        );

        $this->app->bind(
            SellerRepositoryInterface::class,
            SellerRepository::class
        );

        $this->app->bind(
            SubOrderRepositoryInterface::class,
            SubOrderRepository::class
        );

        $this->app->bind(
            DeliveryInfoRepositoryInterface::class,
            DeliveryInfoRepository::class
        );

        $this->app->bind(
            EmployeeRepositoryInterface::class,
            EmployeeRepository::class
        );

        $this->app->bind(
            ActivityRepositoryInterface::class,
            ActivityRepository::class
        );

        $this->app->bind(
            GroupItemRepositoryInterface::class,
            GroupItemRepository::class
        );

        $this->app->bind(
            ItemTagRepositoryInterface::class,
            ItemTagRepository::class
        );

        $this->app->bind(
            SecKillRepositoryInterface::class,
            SecKillRepository::class
        );
    }
}