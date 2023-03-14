<?php


namespace App\Services;


use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AccountService
{

    public function getById(int $id)
    {
        return User::select(['name', 'email'])->find($id);
    }

    public function create(string $name, string $email, string $password)
    {
        User::create([
            'name' =>  $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }

    public function edit(int $id, string $name, string $email)
    {
        $user = User::find($id);
        $user->name = $name;
        $user->email = $email;
        $user->save();
        return User::select(['name', 'email'])->find($id);
    }

    public function checkPassword(int $id, string $password): bool
    {
        if ( Hash::check($password, User::find($id)->password) ) {
            return true;
        }
        return false;
    }

    public function changePassword(int $id, string $newPassword): bool
    {
        $user = User::find($id);
        $user->password = Hash::make($newPassword);
        return $user->save();
    }

}
