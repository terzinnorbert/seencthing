<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    const TYPE_SIMPLE = 1;
    const TYPE_GALLERY = 2;

    protected $fillable = [
        'directory_id',
        'type',
        'name',
        'hash',
    ];

    /**
     * @param $directory
     * @param $type
     * @return mixed
     */
    public static function generate($directory, $type)
    {
        do {
            $hash = str_random(64);
        } while (Share::where('hash', $hash)->count());

        return $directory->shares()->create(
            [
                'hash' => $hash,
                'type' => $type,
                'name' => $directory->name,
            ]
        );
    }

    /**
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'hash';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function directory()
    {
        return $this->belongsTo(Directory::class);
    }

    /**
     * @param $query
     * @param $type
     * @return mixed
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }
}
