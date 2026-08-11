<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'section_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function managesSection(int|string|null $sectionId): bool
    {
        return $this->role === 'admin'
            && $this->section_id !== null
            && (int) $this->section_id === (int) $sectionId;
    }

    public function managesCategory(Category $category): bool
    {
        return $this->managesSection($category->section_id);
    }

    public function managesProduct(Product $product): bool
    {
        $product->loadMissing('category');

        return $this->managesCategory($product->category);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
