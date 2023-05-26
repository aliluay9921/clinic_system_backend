<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\RepresentativeController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



route::post("login", [AuthController::class, "login"]);
// route::post("register", [AuthController::class, "register"]);

Route::middleware(['auth:api'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('get_users', 'getUsers');
        Route::post('add_user', 'addUser');
        Route::put('edit_user', 'editUser');
        Route::delete('delete_user', 'deleteUser');
    });
    Route::controller(RepresentativeController::class)->group(function () {
        Route::get('get_representatives', 'getRepresentatives');
        Route::post('add_representative', 'addRepresentative');
        Route::put('edit_representative', 'editRepresentative');
        Route::delete('delete_representative', 'deleteRepresentative');
    });

    Route::middleware('manager')->group(function () {
        Route::controller(ClinicController::class)->group(function () {
            Route::get('get_clinics', 'getClinics');
            Route::post('add_clinic', 'addClinic');
            Route::put('edit_clinic', 'editClinic');
            Route::delete('delete_clinic', 'deleteClinic');
        });
    });

    Route::middleware('admin_clinic')->group(function () {
        Route::controller(EmployeeController::class)->group(function () {
            Route::get('get_employees', 'getEmployees');
            Route::post('add_employee', 'addEmployee');
            Route::put('edit_employee', 'editEmployee');
            Route::delete('delete_employee', 'deleteEmployee');
        });
    });
    Route::controller(StoreController::class)->group(function () {

        Route::get('get_stores', 'getStores');
        Route::post('add_to_store', 'addToStore');
        Route::put('edit_store', 'updateStore');
        Route::delete('delete_store', 'deleteStore');
    });

    Route::controller(BookingController::class)->group(function () {

        Route::get('get_bookings', 'getBookings');
        Route::post('add_booking', 'addBooking');
        Route::post("make_debt", "makeDebt");
        Route::put('edit_booking', 'editBooking');
        Route::delete('delete_booking', 'deleteBooking');
        Route::delete('delete_debt', 'deleteDebt');
        Route::post("add_archive", 'addArchive');
        Route::get("get_archives", "getArchives");
        Route::post("order_doctor_to_pharmcy", "orderDoctorToPharmcy");
        Route::get("get_debts", "getDebts");
        Route::post("send_message", 'sendMessage');
    });
    Route::controller(PharmacyController::class)->group(function () {
        Route::get('get_pharmacy', 'getPharmacy');
        Route::post('add_to_pharmacy', 'addToPharmacy');
        Route::put('edit_pharmacy', 'editPharmacy');
        Route::delete('delete_pharmacy', 'deletePharmacy');

        Route::get("get_orders_doctor_to_paharmcy", "getOrderDoctorToPharmcy");
        Route::post("make_order_pharmcy", "makeOrderPharmcy");
        Route::get("get_orders_pharmcy", "getOrderPharmcy");
    });


    Route::post("get_image_clinic", [ClinicController::class, 'getImageClinic']);
    Route::post("add_to_log", [LogController::class, 'addToLog']);
    Route::get("get_logs", [LogController::class, 'getLogs']);
    Route::get("get_statistics", [LogController::class, "getStatistics"]);
});
