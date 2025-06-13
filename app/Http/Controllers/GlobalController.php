<?php

namespace App\Http\Controllers;

use App\Enums\MarketplaceItemType;
use App\Http\Resources\AssetsResource;
use App\Http\Resources\PackageResource;
use App\Http\Resources\SubjectResource;
use App\Models\MarketplaceItem;
use App\Models\Setting;
use App\Models\Subject;
use Illuminate\Http\Request;

class GlobalController extends Controller
{
    public function settings()
    {
        $settings= Setting::where('add_to_env', true)->get();
        $settingsArray = [];
        foreach ($settings as $setting) {
            $settingsArray[$setting->key] = $setting->value;
        }
        return response()->json([
            'settings' => $settingsArray,
            'message' => 'Settings retrieved successfully.'
        ],200);
    }

    public function guestMode()
    {
        $subject= Subject::all();
        $packages= MarketplaceItem::where('type',MarketplaceItemType::Package)->get();
        $digitalAssets=MarketplaceItem::where('type',MarketplaceItemType::DigitalAsset)->get();
        return response()->json([
            'subjects' => SubjectResource::collection($subject),
            'packages' => PackageResource::collection($packages),
            'digitalAssets' =>AssetsResource::collection($digitalAssets),
            'message' => 'Guest mode data retrieved successfully.'
        ],200);
    }
}
