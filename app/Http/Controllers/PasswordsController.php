<?php


namespace App\Http\Controllers;


use App\Password;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class PasswordsController extends Controller
{

    /**
     * @param Request $request
     * @return Response
     */
    public function addNewPassword(Request $request)
    {
        $service = $request->input('passwordData.service');
        $userName = $request->input('passwordData.userName');
        $newPassword = $request->input('passwordData.password');

        if (!$service || !$newPassword || !$userName) {
            return response(['success' => 0], Response::HTTP_OK);
        }

        $password = new Password();
        $user = Auth::user();

        $password->user_id = $user->id;
        $password->service = $service;
        $password->user_name = $userName;
        $password->created_at = Carbon::now();
        $password->password = Crypt::encrypt($newPassword);

        $password->save();

        return response(['success' => 1], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function editPassword(Request $request)
    {
        $passwordId = $request->input('editedPasswordData.id');
        $userName = $request->input('editedPasswordData.userName');
        $newPassword = $request->input('editedPasswordData.password');

        $password = Password::find($passwordId);

        if (!$password) {
            return response(['success' => 0], Response::HTTP_BAD_REQUEST);
        }

        $password->user_name = $userName;
        $password->password = Crypt::encrypt($newPassword);

        $password->save();

        return response(['success' => 1], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function deletePassword(Request $request)
    {
        $passwordId = $request->input('id');

        $password = Password::find($passwordId);

        if (!$password) {
            return response(['success' => 0], Response::HTTP_BAD_REQUEST);
        }

        Password::destroy($passwordId);

        return response(['success' => 1], Response::HTTP_OK);
    }
}
