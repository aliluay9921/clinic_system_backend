<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Traits\Filter;
use App\Traits\OrderBy;
use App\Traits\Pagination;
use App\Traits\Search;
use App\Traits\SendResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    use SendResponse, Pagination, Search, Filter, OrderBy;


    public function getEmployees()
    {
        $employees = Employee::where('clinic_id', auth()->user()->clinic_id);
        if (isset($_GET["query"])) {
            $this->search($employees, 'employees');
        }
        if (isset($_GET['filter'])) {
            $this->filter($employees, $_GET["filter"]);
        }
        if (isset($_GET)) {
            $this->order_by($employees, $_GET);
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($employees->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب الموظفين بنجاح', [], $res["model"], null, $res["count"]);
    }

    public function addEmployee(EmployeeRequest $request)
    {
        $request = $request->json()->all();
        $request["clinic_id"] = auth()->user()->clinic_id;
        $employee = Employee::create($request);
        return $this->send_response(200, 'تمت العملية بنجاح', [], Employee::find($employee->id));
    }

    public function editEmployee(EmployeeRequest $request)
    {
        $request = $request->json()->all();
        $employee = Employee::find($request["id"]);
        if (auth()->user()->clinic_id === $employee->clinic_id) {
            $employee->update($request);
            return $this->send_response(200, 'تمت العملية بنجاح', [], Employee::find($employee->id));
        }
        return $this->send_response(403, 'غير مسموح لك بالتعديل على هذا الموظف', [], []);
    }

    public function deleteEmployee(EmployeeRequest $request)
    {
        $request = $request->json()->all();
        $employee = Employee::find($request["id"]);
        if (auth()->user()->clinic_id === $employee->clinic_id) {
            $employee->delete();
            return $this->send_response(200, 'تمت العملية بنجاح', [], []);
        }
        return $this->send_response(403, 'غير مسموح لك بحذف هذا الموظف', [], []);
    }
}
