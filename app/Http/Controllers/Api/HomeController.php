<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Click;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:أخبار,رياضة,ثقافة,اقتصاد,فيديو و صور,آراء',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user_id = auth()->user()->id;
        $clicks = new Click();
        $clicks->user_id = $user_id;
        $clicks->type = $request->type;
        $clicks->clicks = '1';
        $clicks->save();

        return response()->json(['message' => 'Success']);
    }
    public function clicks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:أخبار,رياضة,ثقافة,اقتصاد,فيديو,صور',
            'period' => 'required|in:today,week,month,year',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        
        $today = Carbon::now()->startOfDay();
        $lastWeek = Carbon::now()->startOfWeek();
        $lastMonth = Carbon::now()->startOfMonth();
        $lastYear = Carbon::now()->startOfYear();

        $user_id = auth()->user()->id;
        $type = $request->type;
        $startDate = $this->getStartDate($request->input('period'));

        $clicks = Click::select('type')
            ->selectRaw('count(*) as total_clicks')
            ->where('user_id', $user_id)
            ->where('type', $type)
            ->where('created_at', '>=', $startDate)
            ->groupBy('type')
            ->get();

        return response()->json(['clicks' => $clicks]);
    }

    private function getStartDate($period)
    {
        switch ($period) {
            case 'today':
                return Carbon::now()->subDay()->startOfDay();
            case 'week':
                return Carbon::now()->subWeek()->startOfWeek();
            case 'month':
                return Carbon::now()->subMonth()->startOfWeek();
            case 'year':
                return Carbon::now()->subYear()->startOfWeek();
            default:
                return null;
        }
    }
}
