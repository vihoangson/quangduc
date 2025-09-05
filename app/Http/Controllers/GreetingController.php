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
            'parent_id' => ['nullable','integer','exists:greetings,id']
        ]);

        Greeting::create($data);
        return redirect()->route('greetings.index')->with('status', 'Gửi lời chúc thành công!');
    }
}
