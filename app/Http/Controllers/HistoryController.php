<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Http\Controllers;

use CachetHQ\Cachet\Models\ComponentGroup;
use CachetHQ\Cachet\Models\Incident;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

use AltThree\Badger\Facades\Badger;
use CachetHQ\Cachet\Http\Controllers\Api\AbstractApiController;
use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\Metric;
use CachetHQ\Cachet\Models\Schedule;
use CachetHQ\Cachet\Repositories\Metric\MetricRepository;
use CachetHQ\Cachet\Services\Dates\DateFactory;
use Exception;
use GrahamCampbell\Binput\Facades\Binput;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Jenssegers\Date\Date;
use McCool\LaravelAutoPresenter\Facades\AutoPresenter;


/**
 * This is the feed controller.
 *
 * @author James Brooks <james@alt-three.com>
 */
class HistoryController extends Controller
{
    /**
     * Displays the History page.
     *
     * @return \Illuminate\View\View
     */
    
    public function showHistory($pageNum = 0)
    {
        $today = Date::now();
        $startDate = Date::now();

        // Check if we have another starting date
        if (Binput::has('start_date')) {
            try {
                // If date provided is valid
                $oldDate = Date::createFromFormat('Y-m-d', Binput::get('start_date'));

                // If trying to get a future date fallback to today
                if ($today->gt($oldDate)) {
                    $startDate = $oldDate;
                }
            } catch (Exception $e) {
                // Fallback to today
            }
        }



        $appIncidentDays = (int) Config::get('setting.app_incident_days', 1);
        $incidentDays = array_pad([], $appIncidentDays, null);

        $allIncidents = Incident::where('visible', '>=', (int) !Auth::check())->whereBetween('occurred_at', [
            $startDate->copy()->subDays($appIncidentDays)->format('Y-m-d').' 00:00:00',
            $startDate->format('Y-m-d').' 23:59:59',
        ])->orderBy('occurred_at', 'desc')->get()->groupBy(function (Incident $incident) {
            return app(DateFactory::class)->make($incident->occurred_at)->toDateString();
        });

        // Add in days that have no incidents
        if (Config::get('setting.only_disrupted_days') === false) {
            foreach ($incidentDays as $i => $day) {
                $date = app(DateFactory::class)->make($startDate)->subDays($i);

                if (!isset($allIncidents[$date->toDateString()])) {
                    $allIncidents[$date->toDateString()] = [];
                }
            }
        }

        // Sort the array so it takes into account the added days
        $allIncidents = $allIncidents->sortBy(function ($value, $key) {
            return strtotime($key);
        }, SORT_REGULAR, true);

        return View::make('history')
            ->withDaysToShow($appIncidentDays)
            ->withAllIncidents($allIncidents)
            ->withCanPageForward((bool) $today->gt($startDate))
            ->withCanPageBackward(Incident::where('occurred_at', '<', $startDate->format('Y-m-d'))->count() > 0)
            ->withPreviousDate($startDate->copy()->subDays($appIncidentDays)->toDateString())
            ->withNextDate($startDate->copy()->addDays($appIncidentDays)->toDateString());
    
    }
}
