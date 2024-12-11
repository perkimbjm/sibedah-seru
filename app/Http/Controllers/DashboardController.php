<?php

namespace App\Http\Controllers;

use App\Models\Rtlh;
use App\Models\House;
use App\Models\Village;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Mendefinisikan variabel
        $rtlhsCount = Rtlh::where('is_renov', false)->count();

        $renovatedHousesCount = House::count() + 525;
        $housesByDistrict = House::selectRaw('district, count(*) as count')
            ->groupBy('district')
            ->pluck('count', 'district');

        $topVillages = House::selectRaw('houses.village_id, count(*) as count')
            ->groupBy('houses.village_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->pluck('count', 'houses.village_id');
        
        $villageIds = $topVillages->keys();
        $villages = Village::whereIn('id', $villageIds)->distinct()->get();

        $statusCounts = Rtlh::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Hitung jumlah RTLH berdasarkan district_id
        $rtlhCounts = Rtlh::selectRaw('district_id, count(*) as count')
            ->where('is_renov', false)
            ->groupBy('district_id')
            ->pluck('count', 'district_id');

        // Mengembalikan view dengan data
        return view('app.dashboard', compact('rtlhsCount', 'rtlhCounts','renovatedHousesCount', 'housesByDistrict', 'topVillages', 'villageIds', 'villages', 'statusCounts'));
    }
}