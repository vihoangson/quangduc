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
        // Convert new lines first
        $text = nl2br($text);
        // Regex to detect YouTube links (watch or youtu.be)
        $pattern = '/(https?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_\-]{11}))/i';
        $text = preg_replace_callback($pattern, function ($m) {
            $vid = $m[2];
            $embed = '<div class="ratio ratio-16x9 my-2"><iframe src="https://www.youtube.com/embed/' . $vid . '" title="YouTube video" allowfullscreen loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe></div>';
            return $embed;
        }, $text);
        return $text;
    }
}

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
