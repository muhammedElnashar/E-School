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
        'education_stage_subject_id',
        'name',
        'description',
        'type',
        'package_scope',
        'price',
        'lecture_credits',
        'file_path',
    ];

    public function educationStageSubject()
    {
        return $this->belongsTo(EducationStageSubject::class);
    }
    protected $casts = [
        'type' => MarketplaceItemType::class,
        'package_scope' => Scopes::class,

    ];
    public function isPackage(): bool
    {
        return $this->type === MarketplaceItemType::Package;
    }

    public function isDigitalAsset(): bool
    {
        return $this->type === MarketplaceItemType::DigitalAsset;
    }


}
