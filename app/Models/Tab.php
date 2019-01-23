<?php

namespace Uccello\Core\Models;

use Uccello\Core\Database\Eloquent\Model;

class Tab extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tabs';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'object',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'module_id',
        'label',
        'icon',
        'sequence',
        'data',
    ];

    protected function initTablePrefix()
    {
        $this->tablePrefix = env('UCCELLO_TABLE_PREFIX', 'uccello_');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function blocks()
    {
        return $this->hasMany(Block::class)->orderBy('sequence');
    }

    public function relatedlists()
    {
        return $this->hasMany(Relatedlist::class)->orderBy('sequence');
    }
}
