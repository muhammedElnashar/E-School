<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MarketplaceItemType;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\LessonOccurrence;
use App\Models\MarketplaceItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = User::where('role', RoleEnum::Teacher)->count();
        $student = User::where('role', RoleEnum::Student)->count();
        $package= MarketplaceItem::where('type', MarketplaceItemType::Package)->count();
        $assets= MarketplaceItem::where('type', MarketplaceItemType::DigitalAsset)->count();
        $todayLesson=LessonOccurrence::where('occurrence_date', today())->count();
        return view('dashboard',compact('teacher', 'student', 'package', 'assets', 'todayLesson'));
    }
}
