<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class ChatWindow extends Model {

    use SoftDeletes;

    protected $table = 'chat_window';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * to remove two way blocked users from rating
     * @param type $user_id
     * @return type object
     */
    public static function removeCurrentThreadUsers($user_id) {
        $first = ChatWindow::select('user_id')
                ->where('user_id', $user_id)
                ->orWhere('winner_user_id', $user_id);

        return ChatWindow::select(DB::raw('winner_user_id as user_id'))
                        ->where('user_id', $user_id)
                        ->orWhere('winner_user_id', $user_id)
                        ->union($first)
                        ->pluck('user_id')->toArray();
    }

    /**
     * function to create new chat window
     * @param type $user_id
     * @param type $winner_user_id
     * @param type $tomorrow
     * @param type $day_after_tomorrow
     * @return type
     */
    public static function createChatWindow($user_id, $winner_user_id, $tomorrow, $day_after_tomorrow) {
        $thread = ChatWindow::select('id')
                ->where(function ($query) use ($user_id, $winner_user_id) {
                    $query->where('user_id', $user_id)
                    ->where('winner_user_id', $winner_user_id);
                })
                ->orWhere(function ($query) use ($user_id, $winner_user_id) {
                    $query->where('user_id', $winner_user_id)
                    ->where('winner_user_id', $user_id);
                })
                ->first();
        if (is_object($thread)) {
            ChatWindow::where('id', $thread->id)
                    ->update(['updated_at' => DB::raw('now()')]);
            $thread_id = $thread->id;
        } else {
            $chat_window = array(
                'user_id' => $user_id,
                'winner_user_id' => $winner_user_id,
                'start_time_for_request' => $tomorrow,
                'end_time_for_response' => $day_after_tomorrow
            );
            $thread_id = ChatWindow::insertGetId($chat_window);
        }
        return $thread_id;
    }

    /**
     * to fetch number of unread thread
     * @param type $user_id
     * @return type
     */
    public static function unReadChatThreadCount($user_id) {
        $count = DB::select("select
                            (select count(*) from `chat_window` where 
                            `chat_window`.`user_id` = $user_id  and 
                            `chat_window`.`user_offline_batch` > 0  and 
                            `chat_window`.`deleted_at` is null )
                            +
                            (select count(*) from `chat_window` where
                            `chat_window`.`winner_user_id` = $user_id and 
                            `chat_window`.`winner_offline_batch` > 0  and
                            `chat_window`.`deleted_at` is null)
                        as selectSum");
        return $count[0]->selectSum;
    }

    /**
     * count total number of new chats
     * @param type $user_id
     * @return type
     */
    public static function newChatCount($user_id) {
        $count = DB::select("select
                       (select count(*) from `chat_window` where 
                            `chat_window`.`user_id` = $user_id  and 
                            `chat_window`.`start_time_for_request` is not null and 
                            `chat_window`.`deleted_at` is null )
                        +
                        (select count(*) from `chat_window` where 
                            `chat_window`.`winner_user_id` = $user_id and
                            `chat_window`.`end_time_for_response` is not null and
                            `chat_window`.`deleted_at` is null)
                    as selectSum");
        return $count[0]->selectSum;
    }

}
