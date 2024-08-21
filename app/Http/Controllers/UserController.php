<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function deleteUser(Request $request, string $userId) {
		$user = UserModel::find($userId);
		$user->delete();
	}
}