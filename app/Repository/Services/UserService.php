<?php
/**
 * Created by PhpStorm.
 * User: Sajjad Rahnama
 * Date: 8/12/17
 * Time: 12:39 PM
 */

namespace App\Repository\Services;

use App\Exceptions\AuthException;
use App\Exceptions\GeneralException;
use App\Repository\Traits\RegisterUser;
use App\Repository\Traits\UpdateUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Excel;
use MongoDB\BSON\UTCDateTime;

class UserService
{
    use RegisterUser;
    use UpdateUser;

    /**
     * @param array $credentials
     * @param array $claims
     * @return string
     * @throws AuthException
     */
    public function generateAccessTokenByCredentials(array $credentials, array $claims = []): string
    {
        // verify user with db and generate access token
        $token = auth()->claims($claims)->attempt($credentials);
        if (!$token) {
            throw new AuthException(AuthException::M_INVALID_CREDENTIALS, AuthException::UNAUTHORIZED);
        }

        return $token;
    }

    /**
     * @param string $id
     * @param array $claims
     * @return string
     */
    public function generateAccessTokenByID(string $id, array $claims = []): string
    {
        $token = auth()->claims($claims)->tokenById($id);
        return $token;
    }

    /**
     * @param string $sub is a refresh token subject
     * @return string
     */
    public function generateRefreshToken(string $sub): string
    {
        $token = auth()->setTTL(7200)->tokenById($sub); // token is valid for 5 days!
        return $token;
    }


    /**
     * Generate impersonate token based on given user identification for authenticated user
     * @param string $userID
     * @return array
     * @throws GeneralException
     */
    public function activateImpersonate(string $userID)
    {
        $main_user_claims = auth()->payload();
        if ($main_user_claims['impersonate_id']) {
            throw new GeneralException('حالت سوم شخص فعال است', 400);
        } else {
            $main_user = Auth::user()['_id'];
        }
        $access_token = $this->generateAccessTokenByID($userID, ['impersonate_id' => $main_user]);
        $refresh_token = $this->generateRefreshToken($userID);
        return ['access_token' => $access_token, 'refresh_token' => $refresh_token];
    }

    /**
     * @return array
     * @throws GeneralException
     */
    public function deactivateImpersonate()
    {
        $main_user_claims = auth()->payload();
        if ($main_user_claims['impersonate_id']) {
            $main_user = User::where('_id', $main_user_claims['impersonate_id'])->first();
        } else {
            throw new GeneralException('حالت سوم شخص فعال است', 400);
        }
        $main_user['impersonated'] = false;
        return [
            'user' => $main_user,
            'access_token' => $this->generateAccessTokenByID($main_user['_id'], ['impersonate_id' => null]),
            'refresh_token' => $this->generateRefreshToken($main_user['_id']),
        ];
    }


    /**
     * @return string
     */
    public function refreshToken(string $sub): string
    {
        return auth()->tokenById($sub);
    }

    public function updatePackage(User $user, $package)
    {
        $remaining = ($user['package']['time'] - Carbon::createFromTimestamp($user['package']['start_date']->toDateTime()->getTimestamp())->diffInDays()) * $user['package']['node_num'];
        $package['time'] += (int)$remaining / $package['node_num'];
        $package['start_date'] = new UTCDateTime(Carbon::now());
        $user['package'] = $package;
        $user->save();
    }


    public function toExcel($users)
    {
        $excel = resolve(Excel::class);
        $res = [[
            '#',
            'نام کاربر',
            'ایمیل',
            'تعداد پروژه',
            'تعداد اشیا',
            'نوع کاربر',
            'تاریخ ثبت نام',
            'وضعیت',
            'تلفن همراه',
            'تلفن ثابت',
        ]];
        $res = array_merge($res, $users->map(function ($item, $key) {
            return [
                $key + 1,
                $item['name'],
                $item['email'],
                $item['project_num'],
                $item['node_num'],
                $item['legal'] ? 'حقوقی' : 'حقیقی',
                $item['created_at'],
                $item['active'] ? 'فعال' : 'غیر فعال',
                $item['mobile'] ?: '',
                $item['phone'] ?: '',
            ];
        })->toArray());

        return response(
            $excel->create(
                'invoices.xls',
                function ($excel) use ($res) {
                    $excel->sheet(
                        'Invoices',
                        function ($sheet) use ($res) {
                            $sheet->fromArray($res, null, 'A1', false, false);
                        }
                    );
                }
            )->string('xls')
        )
            ->header('Content-Disposition', 'attachment; filename="things.xls"')
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
    }

}
