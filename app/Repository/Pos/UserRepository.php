<?php
/**
 * Created by PhpStorm.
 * User: jessie
 * Date: 2018/9/2星期日
 * Time: 上午10:23
 */

namespace App\Repository\Pos;


use App\Models\User;

class UserRepository extends BaseRepository
{
    const entity = User::class;


    public static function findUser($id)
    {
        return self::find(self::entity, $id);
    }


    public static function findUserByEmail($email)
    {
        return self::findByColumn(self::entity, 'email', $email);
    }



}