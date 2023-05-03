<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Log;
use App\Traits\Filter;
use App\Traits\Search;
use App\Traits\OrderBy;
use App\Traits\FilterDate;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;

class LogController extends Controller
{
    use SendResponse, Pagination, Search, Filter, OrderBy, FilterDate;

    public function getLogs()
    {
        $logs = Log::with("employees", "users", "representative")->where("clinic_id", auth()->user()->clinic_id)->select("*");
        if (isset($_GET["query"])) {
            $this->search($logs, 'logs');
        }
        if (isset($_GET['filter'])) {
            $this->filter($logs, $_GET["filter"]);
        }
        if (isset($_GET)) {
            $this->order_by($logs, $_GET);
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($logs->orderBy("created_at", "DESC"),  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب السجلات بنجاح', [], $res["model"], null, $res["count"]);
    }

    public function getStatistics()
    {
        $statistics = [];
        $profit_day = Log::whereDate("created_at", Carbon::today())->where("status", 1)->where("clinic_id", auth()->user()->clinic_id)->sum("value");
        $profit_week = Log::whereBetween("created_at", [Carbon::today()->subDays(6), Carbon::now()])->where("status", 1)->where("clinic_id", auth()->user()->clinic_id)->sum("value");
        $profit_month = Log::whereBetween("created_at", [Carbon::now()->startOfMonth(), Carbon::now()])->where("status", 1)->where("clinic_id", auth()->user()->clinic_id)->sum("value");
        $profit_year = Log::whereBetween("created_at", [Carbon::now()->startOfYear(), Carbon::now()])->where("status", 1)->where("clinic_id", auth()->user()->clinic_id)->sum("value");

        $loss_day = Log::whereDate("created_at", Carbon::today())->where("status", 0)->where("clinic_id", auth()->user()->clinic_id)->sum("value");
        $loss_week = Log::whereBetween("created_at", [Carbon::today()->subDays(6), Carbon::now()])->where("status", 0)->where("clinic_id", auth()->user()->clinic_id)->sum("value");
        $loss_month = Log::whereBetween("created_at", [Carbon::now()->startOfMonth(), Carbon::now()])->where("status", 0)->where("clinic_id", auth()->user()->clinic_id)->sum("value");
        $loss_year = Log::whereBetween("created_at", [Carbon::now()->startOfYear(), Carbon::now()])->where("status", 0)->where("clinic_id", auth()->user()->clinic_id)->sum("value");


        $statistics["profit_day"] = $profit_day;
        $statistics["profit_week"] = $profit_week;
        $statistics["profit_month"] = $profit_month;
        $statistics["profit_year"] = $profit_year;
        $statistics["loss_day"] = $loss_day;
        $statistics["loss_week"] = $loss_week;
        $statistics["loss_month"] = $loss_month;
        $statistics["loss_year"] = $loss_year;



        return $statistics;
    }


    public function addToLog(Request $request)
    {
        $request = $request->json()->all();
        $request["clinic_id"] = auth()->user()->clinic_id;
        $log = Log::create($request);
        return $this->send_response(200, 'تم إضافة السجل بنجاح', [], Log::find($log->id));
    }
}