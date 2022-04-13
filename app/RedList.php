<?php

namespace App;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RedList extends Model
{
    use HasFactory;
    use Translatable;

    /**
     * Red List categories.
     *
     * @var array
     */
    public const CATEGORIES = ['EX', 'EW', 'CR', 'CR (PE)', 'CR (PEW)', 'RE', 'EN', 'VU', 'NT', 'LC', 'DD', 'NE'];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['translations'];

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name'];

    public $translatedAttributes = ['name'];

    /**
     * Taxa on the list.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taxa()
    {
        return $this->belongsToMany(Taxon::class)->withPivot('category');
    }

    /**
     * Countries for reference local id's
     */
    public function countries()
    {
        return $this->belongsToMany(
            Country::class,
            'country_red_list',
            'red_list_id',
            'country_id'
        )
            ->withPivot('ref_id');
    }

    /**
     * Get translated name.
     *
     * @return string|null
     */
    public function getNameAttribute()
    {
        return $this->translateOrNew($this->locale())->name;
    }
}
