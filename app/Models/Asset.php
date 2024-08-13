<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * The Asset Model.
 *
 * @property string $id
 * @property string $name
 * @property string $filename
 * @property int $offset
 * @property int $total_size
 * @property bool $is_completed
 * @property-read string $path
 */
class Asset extends Model
{
    use HasUuids;

    private const STORAGE_PATH_INCOMPLETE = 'uploads/incomplete/';

    private const STORAGE_PATH_COMPLETE = 'uploads/complete/';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'filename',
        'offset',
        'total_size',
        'is_completed'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'offset' => 'integer',
        'total_size' => 'integer',
        'is_completed' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'offset' => 0,
        'is_completed' => false,
    ];

    /**
     * An accessor for the file full path.
     *
     * @return Attribute
     */
    public function path(): Attribute
    {
        return Attribute::get(function () {
            if ($this->is_completed) {
                $path = self::STORAGE_PATH_COMPLETE;
            } else {
                $path = self::STORAGE_PATH_INCOMPLETE;
            }

            $path .= $this->filename;

            return storage_path($path);
        });
    }
}
