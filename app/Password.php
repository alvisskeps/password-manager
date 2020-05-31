<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


/**
 * Class Password
 * @property integer $id
 * @property integer $user_id
 * @property string $service
 * @property string $user_name
 * @property string $password
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @package App
 */

class Password extends Model
{
}
