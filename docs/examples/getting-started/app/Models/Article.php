<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Nvl\Comments\Contracts\HasComments;
use Nvl\Comments\Traits\InteractsWithComments;

/** Host-owned public article with a native integer primary key.
 * @property int $id
 * @property string $title
 * @property bool $is_public
 */
final class Article extends Model implements HasComments
{
    use InteractsWithComments;

    protected $fillable = ['title', 'is_public'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['is_public' => 'boolean'];
    }
}
