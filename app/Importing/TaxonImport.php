<?php

namespace App\Importing;

use App\ConservationDocument;
use App\ConservationLegislation;
use App\Country;
use App\RedList;
use App\Stage;
use App\Support\Localization;
use App\Synonym;
use App\Taxon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class TaxonImport extends BaseImport
{
    /**
     * Available conservation documents.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $conservationDocuments;

    /**
     * Available conservation legislations.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $conservationLegislations;

    /**
     * Available red lists.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $redLists;

    /**
     * Available countries.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $countries;

    /**
     * Available stages.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    private $stages;

    /**
     * Create new importer instance.
     *
     * @param  \App\Import  $import
     * @return void
     */
    public function __construct($import)
    {
        parent::__construct($import);
        $this->fetchRelated();
    }

    /**
     * Fetch user that's creating taxa tree and other data related to taxa.
     *
     * @return void
     */
    private function fetchRelated()
    {
        $this->conservationDocuments = ConservationDocument::all();
        $this->conservationLegislations = ConservationLegislation::all();
        $this->redLists = RedList::all();
        $this->countries = Country::all();
        $this->stages = Stage::all();
    }

    /**
     * Definition of all calumns with their labels.
     *
     * @param  \App\User|null  $user
     * @return \Illuminate\Support\Collection
     */
    public static function columns($user = null)
    {
        $locales = collect(LaravelLocalization::getSupportedLocales());

        return collect(array_keys(Taxon::RANKS))->map(function ($rank) {
            return [
                'label' => trans("taxonomy.{$rank}"),
                'value' => $rank,
                'required' => false,
            ];
        })->concat([
            # TODO: Some column must be required, that is rank and name,
            #   but we have special fields for that, so ID should work for now
            [
                'label' => trans('labels.id'),
                'value' => 'id',
                'required' => true,
            ],
            [
                'label' => trans('labels.taxa.author'),
                'value' => 'author',
                'required' => false,
            ],
            [
                'label' => trans('labels.taxa.restricted'),
                'value' => 'restricted',
                'required' => false,
            ],
            [
                'label' => trans('labels.taxa.allochthonous'),
                'value' => 'allochthonous',
                'required' => false,
            ],
            [
                'label' => trans('labels.taxa.invasive'),
                'value' => 'invasive',
                'required' => false,
            ],
            [
                'label' => trans('labels.taxa.fe_old_id'),
                'value' => 'fe_old_id',
                'required' => false,
            ],
            [
                'label' => trans('labels.taxa.fe_id'),
                'value' => 'fe_id',
                'required' => false,
            ],
            [
                'label' => trans('labels.taxa.stages'),
                'value' => 'stages',
                'required' => false,
            ],
            [
                'label' => trans('labels.taxa.conservation_legislations'),
                'value' => 'conservation_legislations',
                'required' => false,
            ],
            [
                'label' => trans('labels.taxa.conservation_documents'),
                'value' => 'conservation_documents',
                'required' => false,
            ],
            [
                'label' => trans('labels.taxa.red_lists'),
                'value' => 'red_lists',
                'required' => false,
            ],
            [
                'label' => trans('labels.taxa.uses_atlas_codes'),
                'value' => 'uses_atlas_codes',
                'required' => false,
            ],
            [
                'label' => trans('labels.taxa.synonyms'),
                'value' => 'synonyms',
                'required' => false,
            ],
            [
                'label' => trans('labels.taxa.countries'),
                'value' => 'countries',
                'required' => false,
            ],
        ])->concat($locales->map(function ($locale, $localeCode) {
            $nativeName = trans('labels.taxa.native_name');
            $localeTranslation = trans('languages.'.$locale['name']);

            return [
                'label' => "{$nativeName} - {$localeTranslation}",
                'value' => 'native_name_'.Str::snake($localeCode),
                'required' => false,
            ];
        }))->concat($locales->map(function ($locale, $localeCode) {
            $description = trans('labels.taxa.description');
            $localeTranslation = trans('languages.'.$locale['name']);

            return [
                'label' => "{$description} - {$localeTranslation}",
                'value' => 'description_'.Str::snake($localeCode),
                'required' => false,
            ];
        }));
    }

    public function generateErrorsRoute()
    {
        return route('api.taxon-imports.errors', $this->model());
    }

    /**
     * Make validator instance.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function makeValidator(array $data)
    {
        $locales = collect(LaravelLocalization::getSupportedLocales())->reverse();
        $ranks = collect(array_keys(Taxon::RANKS));

        return Validator::make($data, [
            $locales->map(function ($locale) {
                $nativeName = trans('labels.taxa.native_name');
                $localeTranslation = trans('languages.' . $locale['name']);
                return [
                    "{$nativeName} - {$localeTranslation}" => ['nullable', 'string'],
                ];
            }),
            $ranks->map(function ($rank) {
                $label = trans("taxonomy.($rank}");
                return [
                    "{$label}" => ['nullable', 'string'],
                ];
            }),
            'id' => ['nullable', 'integer', 'min:1'],
            'author' => ['nullable', 'string'],
            'restricted' => ['nullable', 'string', Rule::in($this->yesNo())],
            'allochthonous' => ['nullable', 'string', Rule::in($this->yesNo())],
            'invasive' => ['nullable', 'string', Rule::in($this->yesNo())],
            'fe_old_id' => ['nullable', 'integer'],
            'fe_id' => ['nullable', 'string'],
            'uses_atlas_codes' => ['nullable', 'string', Rule::in($this->yesNo())],
            'synonyms' => ['nullable', 'string'],
        ], [
            $ranks->map(function ($rank) {
                return [
                    "{$rank}" => trans("taxonomy.($rank}")
                ];
            }),
            'id' => trans('labels.id'),
            'author' => trans('labels.taxa.author'),
            'restricted' => trans('labels.taxa.restricted'),
            'allochthonous' => trans('labels.taxa.allochthonous'),
            'invasive' => trans('labels.taxa.invasive'),
            'fe_old_id' => trans('labels.taxa.fe_old_id'),
            'fe_id' => trans('labels.taxa.fe_id'),
            'stages' => trans('labels.taxa.stages'),
            'conservation_legislations' => trans('labels.taxa.conservation_legislations'),
            'conservation_documents' => trans('labels.taxa.conservation_documents'),
            'red_lists' => trans('labels.taxa.red_lists'),
            'uses_atlas_codes' => trans('labels.taxa.uses_atlas_codes'),
            'synonyms' => trans('labels.taxa.synonyms'),
        ]);
    }

    /**
     * "Yes" and "No" options translated in language the import is using.
     *
     * @return array
     */
    protected function yesNo()
    {
        $lang = $this->model()->lang;

        return [__('Yes', [], $lang), __('No', [], $lang)];
    }

    /**
     * Store data from single CSV row.
     *
     * @param  array  $taxon
     * @return void
     */
    protected function storeSingleItem(array $taxon)
    {
        $this->addEntireTreeOfTheTaxon($taxon);
    }


    /**
     * Create taxon with ancestor tree using data from one row.
     *
     * @param  array  $taxon
     * @return void
     */
    private function addEntireTreeOfTheTaxon($taxon)
    {
        if ($tree = $this->buildWorkingTree($taxon)) {
            // We assume that the rest of available information describes the
            // lowest ranked taxon in the row.
            $last = end($tree);
            $last->fill($this->extractOtherTaxonData($taxon));

            $this->storeWorkingTree($tree);
            $this->saveRelations($last, $taxon);
        }
    }

    /**
     * Make taxa tree using data from a row.
     *
     * @param  array  $taxon
     * @return array
     */
    private function buildWorkingTree($taxon)
    {
        $tree = [];
        $taxa = $this->getRankNamePairsForTree($taxon);
        $existing = $this->getExistingTaxaForPotentialTree($taxa);

        foreach ($taxa as $taxon) {
            $tree[] = $existing->first(function ($existingTaxon) use ($taxon) {
                return $this->isSameTaxon($existingTaxon, $taxon);
            }, new Taxon($taxon));
        }

        return $tree;
    }

    /**
     * Check if it's the same taxon as existing one.
     *
     * @param  \App\Taxon  $existingTaxon
     * @param  array  $taxon
     * @return bool
     */
    private function isSameTaxon($existingTaxon, $taxon)
    {
        return $existingTaxon->rank === $taxon['rank'] &&
            strtolower($existingTaxon->name) === strtolower($taxon['name']);
    }

    /**
     * Get name and rank data for each taxon in the tree from the row.
     *
     * @param  array  $taxon
     * @return array
     */
    private function getRankNamePairsForTree($taxon)
    {
        $tree = [];
        $ranks = array_keys(Taxon::RANKS);

        foreach ($ranks as $rank) {
            $name = trim($this->getNameForRank($rank, $taxon));

            if (! $name) {
                continue;
            }

            $tree[] = [
                'name' => $name,
                'rank' => $rank,
            ];
        }

        return $tree;
    }

    /**
     * Get the name of the taxon for given rank, using the data from the row.
     * We might need to compose it if species and subspecies contains only suffix.
     *
     * @param  string  $rank
     * @param  array  $taxon
     * @return string|null
     */
    private function getNameForRank($rank, $taxon)
    {
        if ($this->isCompoundSpeciesName($rank, $taxon)) {
            return $this->buildCompoundSpeciesName($taxon);
        }


        return $taxon[$rank] ?? null;
    }

    /**
     * Check if we have compound name for species.
     *
     * @param  string  $rank
     * @param  array  $taxon
     * @return bool
     */
    private function isCompoundSpeciesName($rank, $taxon)
    {
        return $rank === 'species' &&
            ! empty($taxon['genus'] &&
                ! empty($taxon['species']));
    }

    /**
     * Build subspecies name from genus, species suffix and subspecies suffix.
     *
     * @param  array  $taxon
     * @return string
     */
    private function buildCompoundSpeciesName($taxon)
    {
        return implode(' ', array_filter([
            trim($taxon['genus']),
            empty($taxon['subgenus']) ? null : '('.$taxon['subgenus'].')',
            trim($taxon['species']),
        ]));
    }

    /**
     * If we already have some taxon in database, we don't need to create it againg,
     * we'll use the one we have.
     *
     * @param  array  $tree
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getExistingTaxaForPotentialTree(array $tree)
    {
        $query = Taxon::query()->with('ancestors');

        foreach ($tree as $taxon) {
            $query->orWhere(function ($q) use ($taxon) {
                $q->where('rank', $taxon['rank'])->where('name', 'like', trim($taxon['name']));
            });
        }

        return $query->get()->groupBy(function ($taxon) {
            return $taxon->isRoot() ? $taxon->id : $taxon->ancestors->filter->isRoot()->first()->id;
        })->sortByDesc(function ($group) {
            return $group->count();
        })->first() ?: EloquentCollection::make();
    }

    /**
     * Extract the rest of information that we'll use to describe the lowest taxon in the row.
     *
     * @param  array  $row
     * @return array
     */
    private function extractOtherTaxonData($item)
    {
        return array_merge(
            [
            'allochthonous' => $this->getBoolean($item, 'allochthonous'),
            'invasive' => $this->getBoolean($item, 'invasive'),
            'restricted' => $this->getBoolean($item, 'restricted'),
            'uses_atlas_codes' => $this->getBoolean($item, 'uses_atlas_codes'),
            'author' =>  Arr::get($item, 'author') ?: null, # $this->getAuthorOnly($item),
            'fe_old_id' => Arr::get($item, 'fe_old_id') ?: null,
            'fe_id' => Arr::get($item, 'fe_id') ?: null,
        ],
            Localization::transformTranslations($this->getLocaleData($item)),
        );
    }

    /**
     * Store the working tree of a row.
     * Some taxa might already exist, some are new and need to be created.
     *
     * @param  array  $tree
     * @return array
     */
    private function storeWorkingTree($tree)
    {
        $sum = [];
        $last = null;

        foreach ($tree as $current) {
            // Connect the taxon with it's parent to establish ancestry.
            $current->parent_id = $last ? $last->id : null;
            $doesntExist = ! $current->exists;

            if ($current->isDirty() || $doesntExist) {
                $current->save();
                //$this->info('Stored taxon: '.$current->name);
            }

            // If we wanted to attribute the taxa tree to a user,
            // this is the place we do it, adding an entry to
            // activity log.
            if ($doesntExist && $this->model()->user) {
                activity()->performedOn($current)
                    ->causedBy($this->model()->user)
                    ->log('created');
            }

            $sum[] = $current;
            $last = $current;
        }

        return $sum;
    }

    /**
     * Connect the lowest taxon in the row with some of it's relations.
     *
     * @param  \App\Taxon  $taxon
     * @param  array  $data
     * @return void
     */
    private function saveRelations($taxon, $data)
    {
        $this->createSynonyms($data, $taxon);

        $taxon->conservationLegislations()->sync($this->getConservationLegislations($data), []);
        $taxon->conservationDocuments()->sync($this->getConservationDocuments($data), []);
        $taxon->stages()->sync($this->getStages($data), []);

        $redListData = $this->getRedLists($data);
        if ($redListData != null) {
            $taxon->redLists()->sync(
                collect($redListData)->mapWithKeys(function ($item, $key) {
                    return [$item['id'] => ['category' => $item['category']]];
                })
            );
        }

        $taxon->countries()->sync($this->getCountries($data), []);
    }

    /**
     * Check if the value matches with "Yes" translation.
     *
     * @param string $value
     * @return bool
     */
    protected function isTranslatedYes($value)
    {
        if (! is_string($value)) {
            return false;
        }

        $yes = __('Yes', [], $this->model()->lang);

        return strtolower($yes) === strtolower($value);
    }

    private function createSynonyms(array $item, $taxon)
    {
        $synonym_names = Arr::get($item, 'synonyms');
        if (!$synonym_names) {
            return;
        }

        foreach (explode('; ', $synonym_names) as $name) {
            $synonym = Synonym::firstOrCreate([
                'name' => $name,
                'taxon_id' => $taxon->id,
            ]);
            $synonym->save();
        }
    }

    private function getLocaleData($item)
    {
        $locales = collect(LaravelLocalization::getSupportedLocales())->reverse();
        $localesData['native_name'] = array();
        foreach ($locales as $localeCode => $locale) {
            $localesData['native_name'][$localeCode] = Arr::get($item, 'native_name_'.Str::snake($localeCode));
        }
        return $localesData;
    }

    private function getAuthorOnly(array $item)
    {
        # TODO: This needs to be reworked!
        // trimming year after comma if exists
        $author = Arr::get($item, 'author');
        if (!$author) {
            return null;
        }
        $author = explode(', ', $author);
        return $author[0];
    }

    private function getBoolean(array $item, string $key)
    {
        $value = Arr::get($item, $key, false);

        return $this->isTranslatedYes($value) || filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     *  All separators must be semicolon with space afterwards ('; ')
     */

    private function getConservationLegislations(array $data)
    {
        $legislations = strtolower(Arr::get($data, 'conservation_legislations'));
        $legislation_ids = array();
        if (!$legislations) {
            return null;
        }
        foreach (explode('; ', $legislations) as $legislation) {
            $leg = $this->conservationLegislations->first(function ($leg) use ($legislation) {
                return strtolower($leg->getNameAttribute()) == $legislation;
            });
            $legislation_ids[] = $leg ? $leg->id : null;
        }
        return $legislation_ids;
    }

    private function getConservationDocuments(array $data)
    {
        $documents = strtolower(Arr::get($data, 'conservation_documents'));
        $document_ids = array();
        if (!$documents) {
            return null;
        }
        foreach (explode('; ', $documents) as $document) {
            $doc = $this->conservationDocuments->first(function ($doc) use ($document) {
                return strtolower($doc->getNameAttribute()) == $document;
            });
            $document_ids[] = $doc ? $doc->id : null;
        }
        return $document_ids;
    }

    private function getStages(array $data)
    {
        $stages = strtolower(Arr::get($data, 'stages'));
        $stage_ids = array();
        if (!$stages) {
            return null;
        }
        foreach (explode('; ', $stages) as $translation) {
            $stage = $this->stages->first(function ($stage) use ($translation) {
                return strtolower($stage->name_translation) == $translation;
            });
            $stage_ids[] = $stage ? $stage->id : null;
        }
        return $stage_ids;
    }

    private function getRedLists(array $data)
    {
        $red_lists = Arr::get($data, 'red_lists');
        $collection = collect();
        if (!$red_lists) {
            return null;
        }
        foreach (explode('; ', $red_lists) as $red_list) {
            # TODO: There must be an easier way...
            $x = explode(' [', $red_list);
            $region = strtolower($x[0]);
            $category = substr($x[1], 0, strlen($x[1]) - 1);

            $red_list = $this->redLists->first(function ($rl) use ($region) {
                return strtolower($rl->getNameAttribute()) == $region;
            });
            if ($red_list) {
                $collection->push(['id' => $red_list->id, 'category' => $category]);
            }
        }
        return $collection;
    }

    private function getCountries(array $data)
    {
        $countries = strtolower(Arr::get($data, 'countries'));
        $country_ids = array();
        if (!$countries) {
            return null;
        }
        foreach (explode('; ', $countries) as $country) {
            $country = $this->countries->first(function ($c) use ($country) {
                return strtolower($c->name) == $country;
            });
            $country_ids[] = $country ? $country->id : null;
        }
        return $country_ids;
    }
}
