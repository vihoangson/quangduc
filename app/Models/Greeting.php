<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Greeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'message',
        'image_path',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function getInitialAttribute(): string
    {
        return Str::upper(Str::substr($this->name, 0, 1));
    }

    public function getRenderedMessageAttribute(): string
    {
        $text = e($this->message);
        $text = nl2br($text);
        // YouTube embed
        $ytPattern = '/(https?:\\/\\/(?:www\\.)?(?:youtube\\.com\\/watch\\?v=|youtu\\.be\\/)([A-Za-z0-9_\\-]{11}))/i';
        $text = preg_replace_callback($ytPattern, function ($m) {
            $vid = $m[2];
            return '<div class="ratio ratio-16x9 my-2"><iframe src="https://www.youtube.com/embed/' . $vid . '" title="YouTube video" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe></div>';
        }, $text);
        // Inline image embed syntax !URL!
        $imgPattern = '/!(https?:\\/\\/[^!\\s]+?\\.(?:jpe?g|png|gif|webp|svg))(?:!)?/i';
        $text = preg_replace_callback($imgPattern, function ($m) {
            $url = e($m[1]);
            return '<div class="my-2"><img src="' . $url . '" alt="embedded image" class="img-fluid rounded border" loading="lazy"></div>';
        }, $text);
        return $text;
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function getShortNameAttribute(): string
    {
        $displayName = (string) $this->name;
        $len = mb_strlen($displayName, 'UTF-8');
        // Rule: if longer than 10 chars, keep first 7 + ... + last 2
        if ($len > 10) {
            $displayName = mb_substr($displayName, 0, 7, 'UTF-8') . '...' . mb_substr($displayName, -2, null, 'UTF-8');
        }
        return $displayName;
    }
}
