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
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'ewallet_name',
        'ewallet_account_number',
        'ewallet_account_holder',
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

    public function getEffectiveBankNameAttribute(): ?string
    {
        return $this->bank_name ?: static::whereNotNull('bank_name')->where('bank_name', '!=', '')->value('bank_name');
    }

    public function getEffectiveBankAccountNumberAttribute(): ?string
    {
        return $this->bank_account_number ?: static::whereNotNull('bank_account_number')->where('bank_account_number', '!=', '')->value('bank_account_number');
    }

    public function getEffectiveBankAccountHolderAttribute(): ?string
    {
        return $this->bank_account_holder ?: static::whereNotNull('bank_account_holder')->where('bank_account_holder', '!=', '')->value('bank_account_holder');
    }

    public function getEffectiveEwalletNameAttribute(): ?string
    {
        return $this->ewallet_name ?: static::whereNotNull('ewallet_name')->where('ewallet_name', '!=', '')->value('ewallet_name');
    }

    public function getEffectiveEwalletAccountNumberAttribute(): ?string
    {
        return $this->ewallet_account_number ?: static::whereNotNull('ewallet_account_number')->where('ewallet_account_number', '!=', '')->value('ewallet_account_number');
    }

    public function getEffectiveEwalletAccountHolderAttribute(): ?string
    {
        return $this->ewallet_account_holder ?: static::whereNotNull('ewallet_account_holder')->where('ewallet_account_holder', '!=', '')->value('ewallet_account_holder');
    }

    public function getEffectiveBankInfoAttribute(): ?string
    {
        if ($this->effective_bank_name || $this->effective_bank_account_number) {
            $parts = array_filter([$this->effective_bank_name, $this->effective_bank_account_number]);
            $str = implode(' ', $parts);
            if ($this->effective_bank_account_holder) {
                $str .= ' a/n ' . $this->effective_bank_account_holder;
            }
            return $str;
        }
        return $this->bank_info ?: static::whereNotNull('bank_info')->where('bank_info', '!=', '')->value('bank_info');
    }

    public function getEffectiveEwalletInfoAttribute(): ?string
    {
        if ($this->effective_ewallet_name || $this->effective_ewallet_account_number) {
            $parts = array_filter([$this->effective_ewallet_name, $this->effective_ewallet_account_number]);
            $str = implode(' ', $parts);
            if ($this->effective_ewallet_account_holder) {
                $str .= ' a/n ' . $this->effective_ewallet_account_holder;
            }
            return $str;
        }
        return $this->ewallet_info ?: static::whereNotNull('ewallet_info')->where('ewallet_info', '!=', '')->value('ewallet_info');
    }

    public function getEffectiveQrisInfoAttribute(): ?string
    {
        return $this->qris_info ?: static::whereNotNull('qris_info')->where('qris_info', '!=', '')->value('qris_info');
    }
}
