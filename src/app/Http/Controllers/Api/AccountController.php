<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserEditPasswordRequest;
use App\Http\Requests\UserEditRequest;
use App\Services\AccountService;
use Illuminate\Support\Facades\Auth;
use \Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{

    private AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function detail() {
        return $this->accountService->getById(Auth::user()->id);
    }

    public function register(UserCreateRequest $request)
    {
        $this->accountService->create($request->name, $request->email, $request->password);
        return response()->json('User registration completed', Response::HTTP_OK);
    }

    public function edit(UserEditRequest $request)
    {
        return response()->json($this->accountService->edit(Auth::user()->id, $request->name, $request->email), Response::HTTP_OK);
    }

    public function changePassword(UserEditPasswordRequest $request)
    {
        $currentPassword = $request->currentPassword;
        $newPassword = $request->newPassword;
        $newPassword2 = $request->newPassword2;

        if ( !($newPassword === $newPassword2) ) {
            return response()->json('Error 1', Response::HTTP_BAD_REQUEST);
        }

        if ( !$this->accountService->checkPassword(Auth::user()->id, $currentPassword) ) {
            return response()->json('Error 2', Response::HTTP_BAD_REQUEST);
        }

        $this->accountService->changePassword(Auth::user()->id, $newPassword);
        return response()->json('Complete change', Response::HTTP_OK);
    }
}
