<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 12/15/17
 * Time: 12:28 PM
 */

namespace App\Repository\Traits;

use App\Exceptions\GeneralException;
use App\Package;
use App\Permission;
use App\Repository\Helper\MobileFactory;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use MongoDB\BSON\UTCDateTime;

trait RegisterUser
{

    /**
     * @param Request $request
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function insertUser(Request $request)
    {
        $package = Package::where('default', true)->first()->toArray();
        $package['start_date'] = new UTCDateTime(Carbon::now());
        $user = User::create([
            'legal' => $request->get('legal') ? true : false,
            'active' => false,
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'package' => $package
        ]);

        $this->defaultPermissions($user);
        return $user;
    }

    /**
     * @param Request $request
     * @return void
     * @throws GeneralException
     */
    public function validateRegisterUser(Request $request)
    {
        $messages = [
            'email.required' => 'لطفا ایمیل را وارد کنید',
            'email.email' => 'لطفا ایمیل را درست وارد کنید',
            'email.unique' => 'این ایمیل قبلا ثبت شده است',
            'password.required' => 'لطفا رمزعبور را وارد کنید',
            'password.min' => 'رمز عبور حداقل باید ۶ کارکتر باشد',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ], $messages);

        if ($validator->fails())
            throw new GeneralException($validator->errors()->first(), GeneralException::VALIDATION_ERROR);
        return;
        // Todo delete
        if ($request->has('legal') && $request->get('legal') == 1) {
            $this->validateRegisterLegal($request);
        } else {
            $this->validateRegisterReal($request);
        }

    }

    private function defaultPermissions(User $user)
    {
        $permissions = DB::collection('permissions')->get()->map(function ($item, $key) {
            $item['_id'] = (string)($item['_id']);
            return $item;
        });
        foreach ($permissions as $permission)
            Permission::create([
                'user_id' => $user['_id'],
                'permission' => $permission
            ]);
    }
}