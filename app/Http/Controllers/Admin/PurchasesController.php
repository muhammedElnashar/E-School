<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MarketplaceItemType;
use App\Enums\PaymentStatusEnum;
use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ManualPurchasesRequest;
use App\Models\MarketplaceItem;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\Request;

class PurchasesController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['user', 'marketplaceItem'])->where("status",PaymentStatusEnum::Completed->value)->paginate(5);
        return view('admin.purchases.index', compact('purchases'));

    }
    public function create()
    {
      $students=  User::where('role', RoleEnum::Student->value)->get();
      $marketplaceItems= MarketplaceItem::where('type',MarketplaceItemType::Package->value)->get();
        return view('admin.purchases.create',compact('students','marketplaceItems'));
    }

    public function store(ManualPurchasesRequest $request)
    {
        try {
            $data = $request->validated();
            $data['status'] = PaymentStatusEnum::Completed->value;
            Purchase::create($data);
            return redirect()->route('purchases.index')->with('success', 'Purchase created successfully.');

        }catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to create purchase: ' . $e->getMessage());
        }

    }

    public function update(Request $request , Purchase $purchase)
    {
        $validated = $request->validate([
            'remaining_credits' => ['required', 'integer', 'min:0'],
        ]);
        $purchase->update($validated);
        return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully.');
    }

    public function destroy(Purchase $purchase)
    {
        try {
            $purchase->delete();
            return redirect()->route('purchases.index')->with('success', 'Purchase deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to delete purchase: ' . $e->getMessage());
        }

    }
}
