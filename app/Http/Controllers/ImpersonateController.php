<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ImpersonateController extends Controller
{
    public function impersonate($id)
    {
        $originalUserId = auth()->user()->id;
        session()->put('impersonate', $originalUserId);

        auth()->loginUsingId($id);

        return redirect()->route('clients.index');
    }

    public function leaveImpersonate()
    {
        if (!session()->has('impersonate')) {
            abort(403);
        }

        auth()->loginUsingId(session()->get('impersonate'));
        session()->remove('impersonate');
        session()->remove('company_id');

        return redirect()->route('clients.index');
    }
}
