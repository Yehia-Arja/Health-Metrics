<?php

namespace App\Http\Controllers;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use App\Models\Dashboard;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DashboardRequest;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller {
    public function index() {
        //$user = Auth::user();
        $data = DashboardService::getDashboardDataForUser(1);
        return ApiResponseService::success('Data returned successfully', $data);
    }
    public function store(DashboardRequest $request) {
        $data = $request->validated();
        $path = $data['file']->store('uploads');
        DashboardService::processCsvAndStore($path);

        return ApiResponseService::success('Dashboard data created successfully');
    }
    public function getActivity() {
        $columns = Schema::getColumnListing('dashboards');
        $data = array_values(array_diff($columns, ['id', 'user_id', 'date']));
        return ApiResponseService::success('Data returned successfully',$data);
    }

}
