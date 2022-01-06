<?php

namespace App\Importing;

use App\Support\Localization;
use App\Synonym;
use App\Taxon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class TaxonImport extends BaseImport
{

    /**
     * Create new importer instance.
     *
     * @param  \App\Import  $import
     * @return void
     */
    public function __construct($import)
    {
        parent::__construct($import);
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
            [
                'label' => trans('labels.id'),
                'value' => 'id',
                'required' => false,
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
            //'stages' => trans('labels.taxa.stages'),
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
     * @param  array  $item
     * @return void
     */
    protected function storeSingleItem(array $item)
    {
        $taxon = Taxon::create(
            array_merge(
                $this->getTaxonData($item),
                Localization::transformTranslations($this->getLocaleData($item))
            )
        );
        $this->createSynonyms($item, $taxon);
    }

    /**
     * Get general observation data from the request.
     *
     * @param  array  $item
     * @return array
     */
    protected function getTaxonData(array $item)
    {
        $author = $this->getAuthorOnly($item);

        return [
            'name' => Arr::get($item, 'name'),
            'author' => $author,
            'rank' => 'species',
        ];
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
        if (!$synonym_names) return;

        foreach (explode('; ', $synonym_names) as $name){
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
        // trimming year after comma if exists
        $author = Arr::get($item, 'author');
        if (!$author) return null;
        $author = explode(', ', $author);
        return $author[0];
    }
}
