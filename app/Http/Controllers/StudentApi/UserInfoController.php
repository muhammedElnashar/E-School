<?php

namespace App\Http\Controllers\StudentApi;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserInfoController extends Controller
{
    public function getUserInfo($id)
    {
        $user=User::whereNot('role',RoleEnum::Admin)->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json([
            'status' => 'success',
           "data"=> new UserResource($user),
        ]);
    }
    public function userCreditsGroupedByPackage()
    {
        $user = auth()->user();

        $purchases = $user->purchases()
            ->with(['marketplaceItem.subject', 'marketplaceItem.educationStage'])
            ->get();

        $grouped = $purchases->groupBy(function ($purchase) {
            return implode('-', [
                $purchase->marketplaceItem->subject_id,
                $purchase->marketplaceItem->education_stage_id ?? 'null',
                $purchase->marketplaceItem->package_scope->value,
            ]);
        });

        $result = $grouped->map(function ($group) {
            $first = $group->first();
            return [
                'subject' => $first->marketplaceItem->subject->name ?? null,
                'education_stage' => $first->marketplaceItem->educationStage->name ?? null,
                'lesson_type' => $first->marketplaceItem->package_scope->value?? null,
                'total_credits' => $group->sum('remaining_credits'),
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

}
