<?php

namespace App\Http\Controllers\Api;

use App\Enums\MarketplaceItemType;
use App\Http\Controllers\Controller;
use App\Http\Resources\AssetsResource;
use App\Http\Resources\PackageResource;
use App\Http\Resources\SubjectResource;
use App\Models\MarketplaceItem;
use App\Models\Subject;
use Illuminate\Http\Request;

class MarketplaceItemController extends Controller
{
    public function getPackages()
    {
        $packages = MarketplaceItem::with('subject', 'educationStage')
            ->where('type', MarketplaceItemType::Package->value)
            ->get();
        return response()->json([
           'packages'=> PackageResource::collection($packages)
        ]);
    }

    public function getDigitalAssets()
    {
        $digitalAssets = MarketplaceItem::with('subject', 'educationStage')
            ->where('type', MarketplaceItemType::DigitalAsset->value)
            ->get();

        return response()->json([
           'assets'=> AssetsResource::collection($digitalAssets)
        ]);
    }
    public function getAllSubject()
    {
        $subjects=Subject::with('stages')->get();
        return response()->json([
            'subjects' => SubjectResource::collection($subjects)
        ]);
    }
}
