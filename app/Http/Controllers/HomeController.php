<?php

namespace App\Http\Controllers;

use App\Password;
use App\Services\TestService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return Renderable
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return view('passwords')->with('passwords', null);
        }

        $passwords = $this->getPasswordData($user->id);

        return view('home')->with('passwords', json_encode($passwords));
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getPasswordData(int $userId): array
    {
        $passwordsData = Password::where('user_id', $userId)->get();
        $passwords = [];

        foreach ($passwordsData as $passwordData) {
            $passwords[] = [
                'id' => $passwordData->id,
                'service' => $passwordData->service,
                'userName' => $passwordData->user_name,
                'password' => Crypt::decrypt($passwordData->password)
            ];
        }

        return $passwords;
    }
}
