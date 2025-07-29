<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Traits\SonnerNotifier;

class TestSonnerController extends Controller
{
    use SonnerNotifier;

    public function index(): View
    {
        return view('test-sonner');
    }

    public function test(Request $request)
    {
        $type = $request->input('type', 'success');
        $message = $request->input('message', 'Test de notification');

        return response()->json([
            'success' => true,
            'message' => $message,
            'notification' => [
                'type' => $type,
                'message' => $message
            ]
        ]);
    }
}
