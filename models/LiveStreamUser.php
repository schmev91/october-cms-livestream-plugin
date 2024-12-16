<?php namespace Pi\Livestream\Models;

use Model;

/**
 * LiveStreamUser Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class LiveStreamUser extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string table name
     */
    public $table = 'pi_livestream_users';

    /**
     * @var array rules for validation
     */
    public $rules = [];

     /**
     * @var array fillable fields
     */
    protected $fillable = [
        'agora_token',
        'collected_diamond',
        'fullname',
        'host_identity',
        'joined_users',
        'user_id',
        'watching_count',
        'status',
    ];

    protected $casts = [
        'joined_users' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(config('pi.livestream::config.user_class'), 'user_id', 'id');
    }



}
