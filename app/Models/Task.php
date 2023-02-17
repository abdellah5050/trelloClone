<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'project_id',
        'status_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_tasks', 'task_id', 'user_id');
    }

    public static function mapUserTask($userArray,$task){

        $userTasks =  collect($userArray)->map(function ($user) use ($task) {
            $existingUser = User::find($user['id']);
            if ($existingUser) {
                return [
                    'user_id' => $existingUser->id,
                    'task_id' => $task->id,

                ];
            }
        });
        return $userTasks = $userTasks->filter();
    }


}
