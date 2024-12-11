<?php

namespace App\Http\Controllers;

use App\Models\Rtlh;
use Inertia\Inertia;
use App\Models\House;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function getData()
    {
        $uniqueVillages = House::distinct()->count('village_id');
        $totalRtlh = Rtlh::where('is_renov', false)->count();
        $renovatedHouses = House::count() + 525;

        return [
            [
                'value' => $uniqueVillages,
                'label' => 'Kelurahan / Desa',
                'icon' => 'map-pin-house',
            ],
            [
                'value' => $renovatedHouses,
                'label' => 'Rumah yang Diperbaiki',
                'icon' => 'house-plus',
            ],
            [
                'value' => $totalRtlh,
                'label' => 'RTLH Tersisa',
                'icon' => 'house-logo',
            ]
        ];
}
}
