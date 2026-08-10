<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy([TenantScope::class])]
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'supplier_id',
        'name',
        'barcode',
        'image',
        'hpp',
        'harga_jual',
        'stock',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'hpp' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public static function getPlaceholderUrl(): string
    {
        return 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="%2394a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="background-color:%23f1f5f9"><rect x="3" y="3" width="18" height="18" rx="4" ry="4"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>';
    }

    public function getImageUrlAttribute(): string
    {
        if (empty($this->image)) {
            return self::getPlaceholderUrl();
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL) || str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        $cleanPath = ltrim($this->image, '/');
        if (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }

        if (file_exists(storage_path('app/public/' . $cleanPath)) || file_exists(public_path('storage/' . $cleanPath))) {
            return asset('storage/' . $cleanPath);
        }

        return self::getPlaceholderUrl();
    }
}
