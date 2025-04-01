<?php

namespace App\Http\Controllers;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use App\Models\Dashboard;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DashboardRequest;

class DashboardController extends Controller {
    public function index() {
        $user = Auth::user();
        
        $data = DashboardService::getDashboardDataForUser($user->id);
        return ApiResponseService::success('Data returned successfully',$data);
    }
    public function store(DashboardRequest $request) {
        $data = $request->validated();
        $path = $data['file']->store('uploads');
        DashboardService::processCsvAndStore($path);

        return ApiResponseService::success('Dashboard data created successfully');
    }

}
