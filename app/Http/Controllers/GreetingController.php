<?php

namespace App\Http\Controllers;

use App\Models\Greeting;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;

class GreetingController extends Controller
{
    /**
     * Display list of root greetings with nested children.
     */
    public function index()
    {
        $greetings = null;
        $loadError = false;
        $errorMessage = null;
        $needMigration = false;

        try {
            if (!Schema::hasTable('greetings')) {
                $needMigration = true;
                $greetings = new LengthAwarePaginator([], 0, 15, 1, ['path' => request()->url()]);
            } else {
                $greetings = Greeting::whereNull('parent_id')
                    ->with('childrenRecursive')
                    ->orderByDesc('created_at')
                    ->paginate(15);
            }
        } catch (\Throwable $e) {
            report($e);
            $loadError = true;
            $errorMessage = config('app.debug') ? $e->getMessage() : 'Không thể tải danh sách lời chúc.';
            $greetings = new LengthAwarePaginator([], 0, 15, 1, ['path' => request()->url()]);
        }

        return view('greetings.index', compact('greetings','loadError','errorMessage','needMigration'));
    }

    /**
     * Store a new greeting or a reply.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:80'],
            'message' => ['required','string','max:4000'],
            'parent_id' => ['nullable','integer','exists:greetings,id'],
            'image' => ['nullable','image','mimes:jpg,jpeg,png,webp,gif','max:5120'],
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            try {
                $ext = strtolower($file->getClientOriginalExtension());
                $relativeDir = 'greetings';
                $filename = uniqid('gr_') . '.' . $ext;
                $storageDir = storage_path('app/public/' . $relativeDir);
                if (!is_dir($storageDir)) {@mkdir($storageDir, 0775, true);}
                $target = $storageDir . '/' . $filename;

                $tempPath = $file->getPathname();
                [$width, $height] = @getimagesize($tempPath) ?: [0,0];

                $needsResize = $width > 1000 && $height > 0 && function_exists('imagecreatetruecolor');

                // Skip resize for GIF to preserve animation
                if ($ext === 'gif') { $needsResize = false; }

                if ($needsResize) {
                    $newWidth = 900;
                    $ratio = $newWidth / $width;
                    $newHeight = (int) max(1, round($height * $ratio));

                    $srcImg = null;
                    switch ($ext) {
                        case 'jpg':
                        case 'jpeg': $srcImg = @imagecreatefromjpeg($tempPath); break;
                        case 'png': $srcImg = @imagecreatefrompng($tempPath); break;
                        case 'webp': if (function_exists('imagecreatefromwebp')) $srcImg = @imagecreatefromwebp($tempPath); break;
                        case 'gif': $srcImg = @imagecreatefromgif($tempPath); break; // will not hit due to skip
                    }

                    if ($srcImg) {
                        $dstImg = imagecreatetruecolor($newWidth, $newHeight);
                        if (in_array($ext, ['png','webp'])) {
                            imagealphablending($dstImg, false); imagesavealpha($dstImg, true);
                            $transparent = imagecolorallocatealpha($dstImg, 0,0,0,127);
                            imagefilledrectangle($dstImg,0,0,$newWidth,$newHeight,$transparent);
                        }
                        imagecopyresampled($dstImg, $srcImg, 0,0,0,0, $newWidth,$newHeight,$width,$height);
                        switch ($ext) {
                            case 'jpg':
                            case 'jpeg': imagejpeg($dstImg, $target, 88); break;
                            case 'png': imagepng($dstImg, $target, 6); break;
                            case 'webp': if (function_exists('imagewebp')) { imagewebp($dstImg, $target, 85); } else { imagejpeg($dstImg, $target, 88); } break;
                            default: imagejpeg($dstImg, $target, 88); break;
                        }
                        imagedestroy($dstImg); imagedestroy($srcImg);
                    } else {
                        // Fallback store original
                        $file->storeAs($relativeDir, $filename, 'public');
                    }
                } else {
                    // Simply move original
                    $file->storeAs($relativeDir, $filename, 'public');
                }
                $data['image_path'] = $relativeDir . '/' . $filename;
            } catch (\Throwable $e) {
                report($e); // Ignore failure, continue without image
            }
        }

        Greeting::create($data);
        return redirect()->route('greetings.index')->with('status','Gửi lời chúc thành công!');
    }
}
