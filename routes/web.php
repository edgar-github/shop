<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\frontend\ShopController;
use App\Http\Controllers\frontend\OrderController;
use App\Http\Controllers\frontend\PaymentController;
use App\Http\Controllers\frontend\ContactUsController;
use App\Http\Controllers\frontend\ProductCommentsController;
use App\Http\Controllers\frontend\SubscriptionController;
use \App\Http\Controllers\frontend\CouponController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\AuthController;
use App\Http\Controllers\admin\ProductCommentsController as AdminBookCommentsController;
use App\Http\Controllers\admin\BooksController;
use App\Http\Controllers\admin\AccessorController;
use App\Http\Controllers\admin\AuthorsController;
use App\Http\Controllers\admin\TranslatorsController;
use App\Http\Controllers\admin\CategoriesController;
use App\Http\Controllers\admin\PostsController;
use App\Http\Controllers\admin\MediaController;
use App\Http\Controllers\admin\OrderController as AdminOrderController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\admin\SettingsController;
use App\Http\Middleware\SetSettingDataServiceMiddleware;
use App\Http\Controllers\admin\CouponController as AdminCouponController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/fail', [PaymentController::class, 'fail'])->name('payment.fail');
Route::get('/payment/telcellRedirect', [PaymentController::class, 'telcellRedirect'])->name('payment.telcell_redirect');
Route::get('/payment/arcaCallback', [PaymentController::class, 'arcaCallback'])->name('payment.arca_callback');

Route::middleware([SetSettingDataServiceMiddleware::class])->group(function () {
    Route::group(
        [
            'prefix' => LaravelLocalization::setLocale()
        ], function () {
        /** Home Route */
        Route::get('/', '\App\Http\Controllers\frontend\HomeController@index')->name('index');

        /** Books Routers */
        Route::get('category/{slug}', 'App\Http\Controllers\frontend\BooksController@booksByCategory')->name('category.books');
        Route::get('books', 'App\Http\Controllers\frontend\BooksController@books')->name('books');
        Route::get('book/{slug}', 'App\Http\Controllers\frontend\BooksController@view')->name('book.view');
        Route::post('books/search', 'App\Http\Controllers\frontend\BooksController@searchBooks')->name('books.search');
        Route::post('book/comment', [ProductCommentsController::class, 'store'])->name('book.comment');

        /** Order Router */
        Route::get('/order', [OrderController::class, 'index'])->name('order');
        Route::post('/order', [OrderController::class, 'create'])->name('order.create');
        Route::get('/order/success', [OrderController::class, 'success'])->name('order.success');
        Route::get('/order/fail', [OrderController::class, 'fail'])->name('order.fail');
        /** Cart Router */
        Route::post('/add-to-cart', [ShopController::class, 'addToCart'])->name('addToCart');
        Route::post('/remove-from-card', [ShopController::class, 'removeFromCart'])->name('removeFromCart');
        Route::post('/cart/update', [ShopController::class, 'updateCart'])->name('updateCart');
        Route::post('/coupon/check/', [CouponController::class, 'checkCoupon'])->name('coupon.check');
        Route::post('/subscription', [SubscriptionController::class, 'store'])->name('subscription.store');

        /*************************** ADMIN ROUTES **************************/
        Route::group([
            'name' => 'admin.',
            'prefix' => 'fs-admin',
        ], function () {

            Route::get('login', [AuthController::class, 'loginView'])->name('admin.loginView');
            Route::post('login', [AuthController::class, 'login'])->name('login');

            Route::group(['middleware' => ['auth:sanctum']], function () {
                Route::get('/', function () {
                    return view('admin.dashboard.index');
                });

                Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.index');
                Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
                /** Book Comments */
                Route::get('comment/index', [AdminBookCommentsController::class, 'index'])->name('admin.bookComment.index')->middleware('can:isAdmin');
                Route::get('comment/view/{id}/{name?}', [AdminBookCommentsController::class, 'view'])->name('admin.bookComment.view')->middleware('can:isAdmin');
                Route::match(array('PUT', 'PATCH'), 'comment/update/{id}', [AdminBookCommentsController::class, 'update'])->name('admin.bookComment.update')->middleware('can:isAdmin');
                Route::delete('comment/destroy/{id}', [AdminBookCommentsController::class, 'destroy'])->name('admin.bookComment.destroy')->middleware('can:isAdmin');
                Route::resource('books', BooksController::class)->middleware('can:isAdmin');
                Route::resource('coupons', AdminCouponController::class)->middleware('can:isAdmin');
            });
        });
    });
});
