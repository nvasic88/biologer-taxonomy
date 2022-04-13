<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $fillable = ['name', 'code', 'url', 'active'];

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
     * Referenced red lists
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function redLists()
    {
        return $this->belongsToMany(
            RedList::class,
            'country_red_list',
            'country_id',
            'red_list_id'
        )
            ->withPivot('ref_id');
    }

    /**
     * Referenced conservation legislations
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function conservationLegislations()
    {
        return $this->belongsToMany(
            ConservationLegislation::class,
            'country_conservation_legislation',
            'country_id',
            'leg_id'
        )
            ->withPivot('ref_id');
    }

    /**
     * Referenced conservation documents
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function conservationDocuments()
    {
        return $this->belongsToMany(
            ConservationDocument::class,
            'country_conservation_document',
            'country_id',
            'doc_id'
        )
            ->withPivot('ref_id');
    }

    /**
     * Find country by its code
     * @param $code
     * @return mixed
     */
    public static function findByCode($code)
    {
        return static::where('code', $code)->first();
    }


    public function setActive()
    {
        $this->active = true;
        $this->save();
    }


    public function setDeactive()
    {
        $this->active = false;
        $this->save();
    }
}
