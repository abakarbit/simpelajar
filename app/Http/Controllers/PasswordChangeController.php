<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordChangeController extends Controller
{
    /**
     * Display the password change form.
     */
    public function edit(Request $request): View
    {
        return view('password-change.edit', [
            'user' => $request->user(),
        ]);
    }
}
