<?php

namespace App\Http\Controllers;

use App\Traits\Filter;
use App\Traits\Search;
use App\Traits\OrderBy;
use App\Traits\Pagination;
use App\Traits\UploadImage;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use App\Models\Representative;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RepresentativeRequest;

class RepresentativeController extends Controller
{
    use SendResponse, Pagination, UploadImage, Search, Filter, OrderBy;

    public function getRepresentatives()
    {
        $representatives = Representative::where("clinic_id", auth()->user()->clinic_id);
        if (isset($_GET["query"])) {
            $this->search($representatives, 'representatives');
        }
        if (isset($_GET['filter'])) {
            $this->filter($representatives, $_GET["filter"]);
        }
        if (isset($_GET)) {
            $this->order_by($representatives, $_GET);
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($representatives->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب المندوبين بنجاح', [], $res["model"], null, $res["count"]);
    }

    public function addRepresentative(RepresentativeRequest $request)
    {
        $request = $request->json()->all();
        $data = [];
        $data['name'] = $request['name'];
        $data['phone_number'] = $request['phone_number'];
        $data['company_name'] = $request['company_name'];
        $data['clinic_id'] = auth()->user()->clinic_id;
        $representative = Representative::create($data);
        return $this->send_response(200, 'تم إضافة المندوب بنجاح', [], Representative::find($representative->id));
    }

    public function editRepresentative(RepresentativeRequest $request)
    {
        $representative = Representative::find($request->id);
        if (auth()->user()->clinic_id === $representative->clinic_id) {
            $representative->update($request->all());
            return $this->send_response(200, 'تم تعديل المندوب بنجاح', [], Representative::find($representative->id));
        }
        return $this->send_response(403, 'غير مسموح لك بالتعديل على  هذا المندوب', [], []);
    }
    public function deleteRepresentative(RepresentativeRequest $request)
    {
        $representative = Representative::find($request->id);
        if (auth()->user()->clinic_id === $representative->clinic_id) {
            $representative->delete();
            return $this->send_response(200, 'تم حذف المندوب بنجاح', [], []);
        }
        return $this->send_response(403, 'غير مسموح لك بحذف هذا المندوب', [], []);
    }
}