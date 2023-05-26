<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Debt;
use App\Traits\Filter;
use App\Traits\Search;
use App\Models\Archive;
use App\Models\Booking;
use App\Traits\OrderBy;
use App\Traits\Pagination;
use App\Traits\UploadImage;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use App\Models\orderDoctorPharmcy;
use Illuminate\Support\Facades\DB;
use App\Traits\SendWhatsappMessage;
use App\Http\Requests\BookingRequest;
use App\Models\Clinic;
use Illuminate\Support\Facades\Schema;

class BookingController extends Controller
{
    use SendResponse, Pagination, UploadImage, Search, Filter, OrderBy, SendWhatsappMessage;

    public function random_code()
    {
        $code = substr(str_shuffle("0123456789ABCDEFG"), 0, 6);
        $get = Booking::where('booking_code', $code)->first();
        if ($get) {
            return $this->random_code();
        } else {
            return $code;
        }
    }
    public function getBookings()
    {
        if (isset($_GET["booking_id"])) {
            $booking = Booking::with("debts")->find($_GET["booking_id"]);
            return $this->send_response(200, 'تم جلب سجل الديون الخاص بهذا الحجز بنجاح', [], $booking);
        }
        $bookings = Booking::where("clinic_id", auth()->user()->clinic_id);

        if (isset($_GET["query"])) {

            $bookings = $this->search($bookings, 'bookings');
        }

        if (isset($_GET["filter"])) {
            $filter = json_decode($_GET["filter"]);
            $bookings = $this->filter($bookings, $_GET["filter"]);
        }
        if (isset($_GET["filter_date"])) {

            $filter = json_decode($_GET["filter_date"]);
            $bookings = $bookings->whereBetween("created_at", [$filter->start_date, $filter->end_date]);
        }

        if (isset($_GET["order_by"])) {
            $bookings = $this->order_by($bookings, $_GET);
        }

        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;

        $res = $this->paging($bookings->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم الحصول على الحجوزات بنجاح', [], $res["model"], null, $res["count"]);
    }

    public function addBooking(BookingRequest $request)
    {
        $request = $request->json()->all();
        $data = [];
        $data['patint_name'] = $request['patint_name'];
        $data["address"] = $request["address"];
        $data["age"] = $request["age"];
        $data["gender"] = $request["gender"];
        $data["booking_date"] = $request["booking_date"];
        $data["booking_time"] = $request["booking_time"];
        $data["doctor_id"] = $request["doctor_id"];
        $data["booking_type"] = $request["booking_type"];
        $data["booking_note"] = $request["booking_note"] ?? null;
        $data["primary_diagonses"] = $request["primary_diagonses"];
        $data["price"] = $request["price"];
        $data["phone_number"] = $request["phone_number"];
        $data["booking_code"] = $this->random_code();
        $data["payment_method"] = $request["payment_method"];
        $data["clinic_id"] = auth()->user()->clinic_id;
        $data["payment_method"] == 0 ? $data["status_paid"] = 0 : $data["status_paid"] = 1;

        $booking = Booking::create($data);
        if ($request["payment_method"] == 0) {
            Log::create([
                "clinic_id" => auth()->user()->clinic_id,
                "target_id" => $booking->id,
                "value" => $request["price"],
                "note" => "دفع أجور الحجز",
                "status" => 1,
                "log_type" => 4
            ]);
        }
        if ($request["payment_method"] == 1) {
            // في حالة تم تقسيط المبلغ
            Debt::create([
                "clinic_id" => auth()->user()->clinic_id,
                "booking_id" => $booking->id,
                "value_paid" => $request["value_paid"],
                "all_value_paid" => 0,  // مجموع الدفعات 
                "value_remaining" => $request["price"] - $request["value_paid"],
                "note" => "دفعة مقدمة",
                "payment_date" => date("Y-m-d"),
            ]);

            Log::create([
                "clinic_id" => auth()->user()->clinic_id,
                "target_id" => $booking->id,
                "value" => $request["value_paid"],
                "note" => "دفعة مقدمة حجز",
                "status" => 1,
                "log_type" => 4
            ]);
        }


        return $this->send_response("200", 'تم عملية الحجز بنجاح', [], Booking::find($booking->id));
    }

    public function editBooking(BookingRequest $request)
    {
        $request = $request->json()->all();
        $booking = Booking::find($request["id"]);
        $data = [];
        $data['patint_name'] = $request['patint_name'];
        $data["address"] = $request["address"];
        $data["age"] = $request["age"];
        $data["gender"] = $request["gender"];
        $data["booking_date"] = $request["booking_date"];
        $data["booking_time"] = $request["booking_time"];
        $data["doctor_id"] = $request["doctor_id"];
        $data["booking_type"] = $request["booking_type"];
        $data["booking_note"] = $request["booking_note"] ?? $booking->booking_note;
        $data["primary_diagonses"] = $request["primary_diagonses"];
        $data["price"] = $request["price"];
        $data["phone_number"] = $request["phone_number"];
        $data["payment_method"] = $request["payment_method"];
        $data["clinic_id"] = auth()->user()->clinic_id;
        $data["payment_method"] == 0 ? $data["status_paid"] = 0 : $data["status_paid"] = 1;

        if ($booking->payment_method == 0) {
            if ($booking->price == $request["price"]) {
                Log::create([
                    "clinic_id" => auth()->user()->clinic_id,
                    "target_id" => $booking->id,
                    "value" => $request["price"],
                    "note" => "دفع أجور الحجز",
                    "status" => 1,
                    "log_type" => 4
                ]);
            }
        }
        if ($request["payment_method"] == 1) {
            // في حالة تم تقسيط المبلغ  

            if ($booking->price == $request["price"]) {
                Log::create([
                    "clinic_id" => auth()->user()->clinic_id,
                    "target_id" => $booking->id,
                    "value" => $request["value_paid"],
                    "note" => "دفع اقساط الحجز",
                    "status" => 1,
                    "log_type" => 4
                ]);
            }

            Debt::create([
                "clinic_id" => auth()->user()->clinic_id,
                "booking_id" => $booking->id,
                "value_paid" => $request["value_paid"],
                "all_value_paid" => 0,
                "value_remaining" => $request["price"] - $request["value_paid"],
                "note" => "دفعة مقدمة",
                "payment_date" => date("Y-m-d"),
            ]);
        }
        $booking->update($data);

        return $this->send_response("200", 'تم تعديل الحجز بنجاح', [], Booking::find($booking->id));
    }

    public function deleteBooking(BookingRequest $request)
    {
        $booking = Booking::find($request->id);
        $booking->delete();
        return $this->send_response("200", 'تم حذف الحجز بنجاح', [], []);
    }

    public function makeDebt(Request $request)
    {
        $request = $request->json()->all();
        $booking = Booking::find($request["booking_id"]);
        $get_debt = Debt::where("clinic_id", auth()->user()->clinic_id)->where("booking_id", $request["booking_id"])->sum("value_paid");
        // return $get_debt;
        $data = [];
        $data["clinic_id"] = auth()->user()->clinic_id;
        $data["booking_id"] = $request["booking_id"];

        $data["value_paid"] = $request["value_paid"];
        $data["all_value_paid"] = $get_debt;
        $data["value_remaining"] = $booking->price - ($request["value_paid"] + $get_debt);
        $data["note"] = $request["note"];
        $data["payment_date"] = date("Y-m-d");
        if ($data["value_paid"] <= ($booking->price - $get_debt)) {
            $debt = Debt::create($data);

            if ($debt->value_remaining == 0) {
                $booking->update([
                    "status_paid" => 0
                ]);
            }

            Log::create([
                "clinic_id" => auth()->user()->clinic_id,
                "target_id" => $booking->id,
                "value" => $request["value_paid"],
                "note" => "دفعة اقساط حجز",
                "status" => 1,
                "log_type" => 4
            ]);
            return $this->send_response(200, 'تم عملية  التسديد الدين بنجاح', [], $debt);
        } else {
            return $this->send_response(403, "تم ادخال سعر تسديد اكبر من قيمة الطلب ", [], []);
        }
    }
    public function deleteDebt(Request $request)
    {
        $debt = Debt::find($request->id);
        $debt->delete();
        return $this->send_response("200", 'تم حذف الدين بنجاح', [], []);
    }


    public function getArchives()
    {
        $archives = Archive::where("clinic_id", auth()->user()->clinic_id);
        if (isset($_GET["query"])) {
            $archives = $this->search($archives, 'archives');
        }
        if (isset($_GET["filter"])) {
            $archives = $this->filter($archives, $_GET["filter"]);
        }
        if (isset($_GET["order_by"])) {
            $archives = $this->order_by($archives, $_GET);
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;

        $res = $this->paging($archives->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب الارشيف بنجاح', [], $res["model"], null, $res["count"]);
    }
    public function addArchive(Request $request)
    {
        $request = $request->json()->all();
        $booking = Booking::find($request["booking_id"]);
        $booking->update([
            "booking_status" => 1
        ]);
        $data = [];
        $data["clinic_id"] = auth()->user()->clinic_id;
        $data["booking_id"] = $request["booking_id"];
        $data["note"] = $request["note"] ?? null;
        $data["another_time_booking"] = $request["another_time_booking"] ?? null;
        $data["diagnosis"] = $request["diagnosis"] ?? $booking->primary_diagonses;
        $archive = Archive::create($data);
        return $this->send_response("200", 'تم اضافة الارشيف بنجاح', [], $archive);
    }
    public function orderDoctorToPharmcy(Request $request)
    {
        $request = $request->json()->all();
        $data = [];
        $data["clinic_id"] = auth()->user()->clinic_id;
        $data["doctor_id"] = auth()->user()->id;
        $data["booking_id"] = $request["booking_id"];
        $data["medicens"] = json_encode($request["medicens"]);
        $order = orderDoctorPharmcy::create($data);
        return $this->send_response("200", ' تم التحويل الى الصيدلية', [], $order);
    }

    public function getDebts()
    {
        $debts = Debt::where("clinic_id", auth()->user()->clinic_id);


        if (isset($_GET["query"])) {
            $debts->where(function ($q) {
                $columns = Schema::getColumnListing('debts');
                $q->orwhereHas("booking", function ($query) {
                    $query->Where('booking_code', 'LIKE', '%' . $_GET['query'] . '%');
                });
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
                }
            });
        }
        if (isset($_GET["filter"])) {
            $debts = $this->filter($debts, $_GET["filter"]);
        }
        if (isset($_GET["order_by"])) {
            $debts = $this->order_by($debts, $_GET);
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;

        $res = $this->paging($debts->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب الديون بنجاح', [], $res["model"], null, $res["count"]);
    }

    public function sendMessage(Request $request)
    {
        $request = $request->json()->all();
        $clinic = Clinic::find($request["id"]);

        return   $this->sendChatMessage($clinic->api_key, '07700459826', $request["message"]);
    }
}
