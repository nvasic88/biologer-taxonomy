<?php

namespace App\Importing;

use App\DEM\Reader as DEMReader;
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
     * @var \App\DEM\Reader
     */
    protected $demReader;

    /**
     * Create new importer instance.
     *
     * @param  \App\Import  $import
     * @param  \App\DEM\Reader  $demReader
     * @return void
     */
    public function __construct($import, DEMReader $demReader)
    {
        parent::__construct($import);

        $this->setDEMReader($demReader);
    }

    /**
     * Set DEM reader instance to get missing elevation.
     *
     * @param  \App\DEM\Reader  $demReader
     * @return self
     */
    public function setDEMReader(DEMReader $demReader)
    {
        $this->demReader = $demReader;

        return $this;
    }

    /**
     * Definition of all calumns with their labels.
     *
     * @param  \App\User|null  $user
     * @return \Illuminate\Support\Collection
     */
    public static function columns($user = null)
    {
        $locales = collect(LaravelLocalization::getSupportedLocales())->reverse();
        return collect([
            [
                'label' => trans('labels.id'),
                'value' => 'id',
                'required' => false,
            ],
            [
                'label' => trans('labels.taxa.name'),
                'value' => 'name',
                'required' => true,
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
        }))->concat([
            [
                'label' => trans('labels.taxa.author'),
                'value' => 'author',
                'required' => false,
            ],
        ])->pipe(function ($columns) use ($user) {
            if (! $user || optional($user)->hasAnyRole(['admin', 'curator'])) {
                return $columns;
            }
        });
    }

    public function generateErrorsRoute()
    {
        return route('api.taxon-imports.errors', $this->model());
    }

    /**
     * Make validator instance.
     *
     * @param  array  $data

     */
    protected function makeValidator(array $data)
    {
        $locales = collect(LaravelLocalization::getSupportedLocales())->reverse();
        return Validator::make($data, [
            'name' => ['required', 'string'],
            'synonyms' => ['nullable', 'string'],
            $locales->map(function ($locale) {
                $nativeName = trans('labels.taxa.native_name');
                $localeTranslation = trans('languages.' . $locale['name']);
                return [
                    "{$nativeName} - {$localeTranslation}" => ['nullable', 'string'],
                ];
            }),
            'author' => ['nullable', 'string'],
        ], [
            'synonyms' => trans('labels.taxa.synonyms'),
            'author' => trans('labels.taxa.author'),
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
