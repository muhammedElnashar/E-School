<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Scopes;
use App\Http\Controllers\Controller;
use App\Models\EducationStage;
use App\Models\EducationStageSubject;
use App\Models\MarketplaceItem;
use App\Enums\MarketplaceItemType;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class DigitalAssetController extends Controller
{
    public function index()
    {
        $digitalAssets = MarketplaceItem::where('type', MarketplaceItemType::DigitalAsset->value)
            ->with(['subject', 'educationStage'])
            ->paginate(15);
        $subjects = Subject::all();
        $educationStages = EducationStage::all();

        return view('admin.digital_assets.index', compact('digitalAssets', 'subjects', 'educationStages'));
    }

    public function create()
    {
        $subjects = Subject::all();
        $educationStages = EducationStage::all();
        return view('admin.digital_assets.create', compact('subjects', 'educationStages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'education_stage_id' => ['nullable', 'exists:education_stages,id'],
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,csv|max:20480',
        ]);

        $filePath = $request->file('file')->store('digital_assets', 'public');
        $validated['type'] = MarketplaceItemType::DigitalAsset->value;
        $validated['file_path'] = $filePath;

        MarketplaceItem::create($validated);

        return redirect()->route('digital-assets.index')->with('success', 'Digital Asset created successfully.');
    }

    public function edit(MarketplaceItem $digitalAsset)
    {
       //
    }

    public function update(Request $request, MarketplaceItem $asset)
    {

        $validated =   $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'education_stage_id' => ['nullable', 'exists:education_stages,id'],
            'name' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,csv|max:20480', // 20MB max
            'price' => 'required|numeric|min:0',
        ]);

        $validated['type'] = MarketplaceItemType::DigitalAsset->value;

        if ($request->hasFile('file')) {
            if ($asset->file_path && Storage::disk('public')->exists($asset->file_path)) {
                Storage::disk('public')->delete($asset->file_path);
            }
            $file_path = $request->file('file')->store('digital_assets', 'public');

            $validated['file_path'] = $file_path;
        }
        $asset->update($validated);
        return redirect()->route('digital-assets.index')->with('success', __('Digital asset updated successfully.'));
    }

    public function destroy($id)
    {
        $digitalAsset = MarketplaceItem::findOrFail($id);
        if ($digitalAsset->type->value !== MarketplaceItemType::DigitalAsset->value) {
            abort(404);
        }

        if ($digitalAsset->file_path) {
            Storage::disk('public')->delete($digitalAsset->file_path);
        }

        $digitalAsset->delete();

        return back()->with('success', 'Digital Asset deleted successfully.');
    }
}
