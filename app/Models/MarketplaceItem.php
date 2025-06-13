<?php

namespace App\Models;

use App\Enums\MarketplaceItemType;
use App\Enums\Scopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'education_stage_id',
        'name',
        'description',
        'type',
        'package_scope',
        'price',
        'lecture_credits',
        'file_path',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     */
    public function educationStage()
    {
        return $this->belongsTo(EducationStage::class);
    }
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    public function hasPurchased($userId)
    {
        return $this->purchases()
            ->where('user_id', $userId)
            ->whereHas('marketplaceItem', function ($query) {
                $query->where('type', MarketplaceItemType::DigitalAsset->value);
            })
            ->exists();
    }

    protected $casts = [
        'type' => MarketplaceItemType::class,
        'package_scope' => Scopes::class,

    ];


}
