<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'uuid',
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_published',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Page $page): void {
            $baseSlug = $page->slug ?: $page->title;
            $page->slug = static::generateUniqueSlug($baseSlug, $page->id);
        });
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function setSlugAttribute(?string $value): void
    {
        $slug = Str::slug($value ?: ($this->attributes['title'] ?? ''));
        $this->attributes['slug'] = $slug ?: 'page';
    }

    public function setContentAttribute(?string $value): void
    {
        $this->attributes['content'] = static::sanitizeHtml($value ?? '');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(function (Builder $q): void {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function sanitizeHtml(string $html): string
    {
        $allowed = '<p><br><h1><h2><h3><h4><h5><h6><strong><b><em><i><u><s><blockquote><ul><ol><li><a><img><figure><figcaption><table><thead><tbody><tr><th><td><hr><span><div><code><pre>';

        return trim(strip_tags($html, $allowed));
    }

    protected static function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value) ?: 'page';
        $slug = $baseSlug;
        $counter = 1;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }
}
