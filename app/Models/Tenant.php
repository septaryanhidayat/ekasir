<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'bank_info',
        'ewallet_info',
        'qris_info',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function cashRegisters(): HasMany
    {
        return $this->hasMany(CashRegister::class);
    }

    public function cashFlows(): HasMany
    {
        return $this->hasMany(CashFlow::class);
    }

    public function getEffectiveBankInfoAttribute(): ?string
    {
        return $this->bank_info ?: static::whereNotNull('bank_info')->where('bank_info', '!=', '')->value('bank_info');
    }

    public function getEffectiveEwalletInfoAttribute(): ?string
    {
        return $this->ewallet_info ?: static::whereNotNull('ewallet_info')->where('ewallet_info', '!=', '')->value('ewallet_info');
    }

    public function getEffectiveQrisInfoAttribute(): ?string
    {
        return $this->qris_info ?: static::whereNotNull('qris_info')->where('qris_info', '!=', '')->value('qris_info');
    }
}
