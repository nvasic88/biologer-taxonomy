<?php

namespace App\Repositories;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class StatsRepository
{
    /**
     * Retrieve data required for local community page.
     *
     * @return array
     */
    public function getLocalCommunityData()
    {
        return Cache::remember('localCommunityPageData', now()->addMinutes(5), function () {
            return $this->getLocalCommunityDataFromDb();
        });
    }

    /**
     * Retrieve data required for local community page from DB.
     *
     * @return array
     */
    private function getLocalCommunityDataFromDb()
    {
        $curators = User::curators()->with(['curatedTaxa' => function ($query) {
            $query->orderByAncestry();
        }])->sortByName()->get();

        $taxonomicGroupsCount = Collection::make(
            $curators->pluck('curatedTaxa')->flatten(1)
        )->unique()->count();

        return [
            'usersCount' => User::count(),
            'admins' => User::admins()->sortByName()->get(),
            'curators' => $curators,
            'taxonomicGroupsCount' => $taxonomicGroupsCount,
        ];
    }

    /**
     * We cache stats data so we don't hit database more than needed.
     *
     * @return array
     */
    public function getStatsData()
    {
        return Cache::remember('statsPageData', now()->addMinutes(15), function () {
            return $this->getStatsDataFromDb();
        });
    }

    /**
     * Get stats to show on the page.
     *
     * @return array
     */
    private function getStatsDataFromDb()
    {
        $topUsers = User::withCount('observationsOfTypeField as field_observations_count')
            ->has('observationsOfTypeField', '>', 0)
            ->orderBy('field_observations_count', 'desc')
            ->limit(10)
            ->get();

        $topCurators = User::curators()
            ->withCount('fieldObservationsIdentified')
            ->has('fieldObservationsIdentified', '>', 0)
            ->orderBy('field_observations_identified_count', 'desc')
            ->limit(10)
            ->get();

        return [
            'topUsers' => $topUsers,
            'topCurators' => $topCurators,
        ];
    }
}
