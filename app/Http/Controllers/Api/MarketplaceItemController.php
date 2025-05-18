<?php

namespace App\Http\Controllers\Api;

use App\Enums\MarketplaceItemType;
use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Models\MarketplaceItem;
use Illuminate\Http\Request;

class MarketplaceItemController extends Controller
{
    public function getPackages()
    {
        $packages = MarketplaceItem::with('educationStageSubject.educationStage', 'educationStageSubject.subject')
            ->where('type', MarketplaceItemType::Package->value)
            ->get();
        return response()->json([
                 PackageResource::collection($packages)

        ]);
    }

    public function getDigitalAssets()
    {
        $digitalAssets = MarketplaceItem::with('educationStageSubject.educationStage', 'educationStageSubject.subject')
            ->where('type', MarketplaceItemType::DigitalAsset->value)
            ->get();

        return response()->json($digitalAssets);
    }
}
