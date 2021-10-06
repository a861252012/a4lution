<?php
/*

=========================================================
* Argon Dashboard PRO - v1.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard-pro-laravel
* Copyright 2018 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)

* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

*/

namespace App\Http\Controllers;

use Gate;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $allMenu = DB::table('role_assignment')
            ->join('view_permission', 'role_assignment.role_id', '=', 'view_permission.role_id')
            ->join('views', 'view_permission.view_id', '=', 'views.id')
            ->where('role_assignment.user_id', '=', Auth::user()->roleAssignment->role_id)
            ->where('views.active', '=', 1)
            ->select('views.module', 'views.level', 'views.order', 'views.menu_title', 'views.menu_slug', 'views.path')
            ->orderByRaw('views.module , views.level , views.order')
            ->get();

        $parentMenu = $allMenu->filter(function ($val) {
            return $val->level === 1;
        });

        $childMenu = $allMenu->diffKeys($parentMenu)->values()->all();

        $parentMenus = $parentMenu->values()->all();

        return view('profile.edit', ["parentMenus" => $parentMenus, 'childMenu' => $childMenu]);
    }

    /**
     * Update the profile
     *
     * @param \App\Http\Requests\ProfileRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
//        if (Gate::denies('update', auth()->user())) {
//            return back()->withErrors(['not_allow_profile' => __('You are not allowed to change data for a default user.')]);
//        }

        auth()->user()->update($request->only(['full_name', 'email', 'phone_number', 'company_name', 'address']));

        return back()->withStatus(__('Profile successfully updated.'));
    }

    /**
     * Change the password
     *
     * @param \App\Http\Requests\PasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function password(PasswordRequest $request)
    {
//        if (Gate::denies('update', auth()->user())) {
//            return back()->withErrors([
//                'not_allow_password' => __('You are not allowed to change the password for a default user.')
//            ]);
//        }

        auth()->user()->update(['password' => Hash::make($request->get('password'))]);

        return back()->withPasswordStatus(__('Password successfully updated.'));
    }
}
