<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Traits\Search;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ClinicRequest;
use App\Traits\Filter;
use App\Traits\OrderBy;
use Illuminate\Support\Facades\Schema;

class ClinicController extends Controller
{
    use SendResponse, Pagination, Search, Filter, OrderBy;

    public function getClinics()
    {
        $clinics = Clinic::select("*");
        if (isset($_GET["query"])) {
            $this->search($clinics, 'clinics');
        }
        if (isset($_GET['filter'])) {
            $this->filter($clinics, $_GET["filter"]);
        }
        if (isset($_GET)) {
            $this->order_by($clinics, $_GET);
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($clinics->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب العيادات بنجاح', [], $res["model"], null, $res["count"]);
    }

    public function addClinic(ClinicRequest $request)
    {
        $request = $request->json()->all();
        if (array_key_exists('logo', $request)) {
            $request['logo'] = $this->uploadPicture($request['logo'], '/images/logos/');
        }
        $clinic = Clinic::create($request);
        return $this->send_response(200, 'تم إضافة العيادة بنجاح', [], Clinic::find($clinic->id));
    }


    public function editClinic(ClinicRequest $request)
    {
        $request = $request->json()->all();
        $clinic = Clinic::find($request['id']);
        if (array_key_exists('logo', $request)) {
            $request['logo'] = $this->uploadPicture($request['logo'], '/images/logos/');
        }
        $clinic->update($request);
        return $this->send_response(200, 'تم تعديل العيادة بنجاح', [], Clinic::find($clinic->id));
    }

    public function deleteClinic(ClinicRequest $request)
    {
        Clinic::find($request["id"])->delete();
        return $this->send_response(200, 'تم حذف العيادة بنجاح', [], []);
    }
}
