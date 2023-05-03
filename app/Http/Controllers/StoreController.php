<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Traits\Filter;
use App\Traits\Search;
use App\Traits\OrderBy;
use App\Traits\Pagination;
use App\Traits\UploadImage;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRequest;

class StoreController extends Controller
{
    use SendResponse, Pagination, UploadImage, Search, Filter, OrderBy;

    public function getStores()
    {
        $stores = Store::with('represntatives')->where("clinic_id", auth()->user()->clinic_id);
        if (isset($_GET["query"])) {
            $this->search($stores, 'stores');
        }
        if (isset($_GET['filter'])) {
            $this->filter($stores, $_GET["filter"]);
        }
        if (isset($_GET)) {
            $this->order_by($stores, $_GET);
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($stores->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب المنتجات في المخزن بنجاح', [], $res["model"], null, $res["count"]);
    }

    public function addToStore(StoreRequest $request)
    {
        $request = $request->json()->all();

        $data = [];
        if (array_key_exists('image', $request)) {
            $data['image'] = $this->uploadPicture($request['image'], '/images/stores/');
        }


        $data['product_name'] = $request['product_name'];
        $data['quantity'] = $request['quantity'];
        $data['price'] = $request['price'];
        $data['expaired'] = $request['expaired'];

        $data['representative_id'] = $request['representative_id'] ?? null;
        $data['company'] = $request['company'] ?? null;
        $data['note'] = $request['note'] ?? null;
        $data['description'] = $request['dexdescription'] ?? null;
        $data['clinic_id'] = auth()->user()->clinic_id;

        $store = Store::create($data);
        return $this->send_response(200, 'تمت أضافة منتج بنجاح', Store::find($store->id));
    }

    public function updateStore(StoreRequest $request)
    {
        $request = $request->json()->all();
        $store = Store::find($request['id']);

        $data = [];
        if (array_key_exists('image', $request)) {
            $data['image'] = $this->uploadPicture($request['image'], 'stores');
        }

        $data['product_name'] = $request['product_name'];
        $data['quantity'] = $request['quantity'];
        $data['price'] = $request['price'];
        $data['expaired'] = $request['expaired'];

        $data['representative_id'] = $request['representative_id'] ?? $store->representative_id;
        $data['company'] = $request['company'] ?? $store->company;
        $data['note'] = $request['note'] ?? $store->note;
        $data['description'] = $request['dexdescription'] ?? $store->description;
        $data['clinic_id'] = auth()->user()->clinic_id;

        $store->update($data);
        return $this->send_response(200, 'تمت تعديل منتج بنجاح', Store::find($store->id));
    }

    public function deleteStore(StoreRequest $request)
    {
        $request = $request->json()->all();
        $store = Store::find($request['id']);
        $store->delete();
        return $this->send_response(200, 'تمت حذف منتج بنجاح', [], []);
    }
}