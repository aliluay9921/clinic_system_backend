<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Filter;
use App\Traits\Search;
use App\Traits\OrderBy;
use App\Traits\Pagination;
use App\Traits\UploadImage;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    use SendResponse, Pagination, UploadImage, Search, Filter, OrderBy;
    public function getUsers()
    {
        $users = User::select("*");
        if (isset($_GET["query"])) {
            $this->search($users, 'users');
        }
        if (isset($_GET['filter'])) {
            $this->filter($users, $_GET["filter"]);
        }
        if (isset($_GET)) {
            $this->order_by($users, $_GET);
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($users->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب المستخدمين بنجاح', [], $res["model"], null, $res["count"]);
    }

    public function addUser(UserRequest $request)
    {
        $request = $request->json()->all();
        $request["password"] = bcrypt($request["password"]);
        $user = User::create($request);
        return $this->send_response(200, 'تم إضافة المستخدم بنجاح', [], User::find($user->id));
    }

    public function editUser(UserRequest $request)
    {
        $request = $request->json()->all();
        $user = User::find($request["id"]);
        $user->update($request);
        return $this->send_response(200, 'تم تعديل المستخدم بنجاح', [], User::find($user->id));
    }
    public function deleteUser(UserRequest $request)
    {
        $request = $request->json()->all();
        $user = User::find($request["id"]);
        $user->delete();
        return $this->send_response(200, 'تم حذف المستخدم بنجاح', [], User::find($user->id));
    }
}
