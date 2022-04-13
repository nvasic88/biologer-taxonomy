<?php

namespace App;

use App\Concerns\HasTranslatableAttributes;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConservationLegislation extends Model
{
    use HasFactory;
    use Translatable;
    use HasTranslatableAttributes;

    protected $translationForeignKey = 'leg_id';

    protected $hidden = [
        'created_at', 'updated_at',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['translations'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name', 'description'];

    public $translatedAttributes = ['name', 'description'];

    /**
     * Taxa that is listed.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taxa()
    {
        return $this->belongsToMany(Taxon::class);
    }

    /**
     * Countries for reference local id's
     */
    public function countries()
    {
        return $this->belongsToMany(
            Country::class,
            'country_conservation_legislation',
            'leg_id',
            'country_id'
        )
            ->withPivot('ref_id');
    }

    public function getNameAttribute()
    {
        return $this->translateOrNew($this->locale())->name;
    }

    public function getDescriptionAttribute()
    {
        return $this->translateOrNew($this->locale())->description;
    }

    public function loadReferenceId(Country $country)
    {
        dd($this->countries()->where(['id' => 4])->first());
        # return $this->countries()->('country_id', $country->id)->get();
    }
}
