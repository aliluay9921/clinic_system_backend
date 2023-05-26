<?php

namespace App\Http\Controllers;

use App\Traits\Filter;
use App\Traits\Search;
use App\Traits\OrderBy;
use App\Traits\Pagination;
use App\Traits\UploadImage;
use App\Models\orderPharmcy;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use App\Models\PharmacyStore;
use App\Models\orderDoctorPharmcy;
use App\Http\Requests\PharmcyRequest;
use Illuminate\Support\Facades\Schema;

class PharmacyController extends Controller
{
    use SendResponse, Pagination, Search, UploadImage, Filter, OrderBy;


    public function random_code()
    {
        $code = substr(str_shuffle("0123456789ABCDEFG"), 0, 6);
        $get = orderPharmcy::where('order_code', $code)->first();
        if ($get) {
            return $this->random_code();
        } else {
            return $code;
        }
    }


    public function getPharmacy()
    {
        $medicean = PharmacyStore::with('represntatives')->where("clinic_id", auth()->user()->clinic_id);
        if (isset($_GET["query"])) {
            $this->search($medicean, 'pharmacy_stores');
        }
        if (isset($_GET['filter'])) {
            $this->filter($medicean, $_GET["filter"]);
        }
        if (isset($_GET)) {
            $this->order_by($medicean, $_GET);
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($medicean->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب الادوية في المخزن بنجاح', [], $res["model"], null, $res["count"]);
    }

    public function addToPharmacy(PharmcyRequest $request)
    {
        $request = $request->json()->all();


        $data = [];
        if (array_key_exists('image', $request)) {
            $data['image'] = $this->uploadPicture($request['image'], '/images/pharmacy/');
        }
        $data['barCode'] = $request['barCode'] ?? null;
        $data['medicen_name'] = $request['medicen_name'];
        $data['quantity'] = $request['quantity'];
        $data['price'] = $request['price'];
        $data['expaired'] = $request['expaired'];
        $data['company'] = $request['company'] ?? null;
        $data['note'] = $request['note'] ?? null;
        $data['description'] = $request['description'] ?? null;
        $data['side_effect'] = $request['side_effect'] ?? null;
        $data['representative_id'] = $request['representative_id'] ?? null;
        $data['clinic_id'] = auth()->user()->clinic_id;
        $pharmacy_store = PharmacyStore::create($data);
        return $this->send_response(200, 'تم اضافة الدواء بنجاح', [], PharmacyStore::with('represntatives')->find($pharmacy_store));
    }

    public function editPharmacy(PharmcyRequest $request)
    {
        $request = $request->json()->all();
        $pharmacy_store = PharmacyStore::find($request['id']);

        if (auth()->user()->clinic_id === $pharmacy_store->clinic_id) {
            if (array_key_exists('image', $request)) {
                $request['image'] = $this->uploadPicture($request['image'], '/images/pharmacy/');
            }

            $pharmacy_store->update($request);
            return $this->send_response(200, 'تم تعديل الدواء بنجاح', [], PharmacyStore::find($pharmacy_store->id));
        }
        return $this->send_response(403, 'غير مسموح لك بالتعديل على  هذا الدواء', [], []);
    }

    public function deletePharmacy(Request $request)
    {
        $pharmacy_store = PharmacyStore::find($request->id);
        if (auth()->user()->clinic_id === $pharmacy_store->clinic_id) {
            $pharmacy_store->delete();
            return $this->send_response(200, 'تم حذف الدواء بنجاح', [], []);
        }
        return $this->send_response(403, 'غير مسموح لك  بحذف  هذا الدواء', [], []);
    }

    public function getOrderDoctorToPharmcy()
    {
        $orders = orderDoctorPharmcy::where("clinic_id", auth()->user()->clinic_id);
        if (isset($_GET["query"])) {
            // $orders = $this->search($orders, 'order_doctor_pharmcies');
            $orders->where(function ($q) {
                $columns = Schema::getColumnListing('order_doctor_pharmcies');
                $q->whereHas("doctor", function ($query) {
                    $query->Where('user_name', 'LIKE', '%' . $_GET['query'] . '%');
                });


                $q->orwhereHas("booking", function ($query) {
                    $query->Where('booking_code', 'LIKE', '%' . $_GET['query'] . '%');
                });
                foreach ($columns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
                }
            });
        }
        if (isset($_GET["filter"])) {
            $orders = $this->filter($orders, $_GET["filter"]);
        }
        if (isset($_GET["order_by"])) {
            $orders = $this->order_by($orders, $_GET);
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;

        $res = $this->paging($orders->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب الطلبات بنجاح', [], $res["model"], null, $res["count"]);
    }

    public function makeOrderPharmcy(Request $request)
    {
        $request = $request->json()->all();
        $data = [];
        $total_cost = 0;
        $data['clinic_id'] = auth()->user()->clinic_id;
        $data['patint_name'] = $request["patint_name"] ?? null;
        $data['patint_age'] = $request["patint_age"] ?? null;
        $data['gender'] = $request["gender"] ?? null;
        $data['order_code'] = $this->random_code();

        foreach ($request["medicans"] as $medican) {
            $pharmacy_store = PharmacyStore::find($medican["id"]);
            if ($pharmacy_store->quantity < $medican["quantity"]) {
                return $this->send_response(403, 'الكمية المطلوبة اكبر من الكمية المتوفرة', [], []);
            }
            $total_cost += $pharmacy_store->price * $medican["quantity"];
            $pharmacy_store->update([
                "quantity" => $pharmacy_store->quantity - $medican["quantity"]
            ]);
        }
        $data["total_cost"] = $total_cost;
        $data['note'] = $request['note'] ?? null;
        $order = orderPharmcy::create($data);
        foreach ($request["medicans"] as $medican) {
            $current_medican = PharmacyStore::find($medican["id"]);

            $order->medicans()->attach($medican["id"], ['quantity' => $medican['quantity'], 'price' => $current_medican->price]);
        }
        return $this->send_response(200, 'تم ارسال الطلب بنجاح', [], $order);
    }

    public function getOrderPharmcy()
    {
        $orders = orderPharmcy::where("clinic_id", auth()->user()->clinic_id);
        if (isset($_GET["query"])) {
            $orders = $this->search($orders, 'order_pharmcies');
        }
        if (isset($_GET["filter"])) {
            $orders = $this->filter($orders, $_GET["filter"]);
        }
        if (isset($_GET["order_by"])) {
            $orders = $this->order_by($orders, $_GET);
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;

        $res = $this->paging($orders->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب الطلبات بنجاح', [], $res["model"], null, $res["count"]);
    }
}
