<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceItem extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'type', 'price', 'lecture_credits', 'file_path'];

    public function isPackage()
    {
        return $this->type === 'package';
    }

    public function isDigitalAsset()
    {
        return $this->type === 'digital_asset';
    }
}
