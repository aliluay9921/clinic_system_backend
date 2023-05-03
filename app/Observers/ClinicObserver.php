<?php

namespace App\Observers;

use App\Models\Clinic;

class ClinicObserver
{
    /**
     * Handle the Clinic "created" event.
     *
     * @param  \App\Models\Clinic  $clinic
     * @return void
     */
    public function created(Clinic $clinic)
    {
        //
    }

    /**
     * Handle the Clinic "updated" event.
     *
     * @param  \App\Models\Clinic  $clinic
     * @return void
     */
    public function updated(Clinic $clinic)
    {
        //
    }

    /**
     * Handle the Clinic "deleted" event.
     *
     * @param  \App\Models\Clinic  $clinic
     * @return void
     */
    public function deleted(Clinic $clinic)
    {
        $clinic->users()->delete();
    }

    /**
     * Handle the Clinic "restored" event.
     *
     * @param  \App\Models\Clinic  $clinic
     * @return void
     */
    public function restored(Clinic $clinic)
    {
        //
    }

    /**
     * Handle the Clinic "force deleted" event.
     *
     * @param  \App\Models\Clinic  $clinic
     * @return void
     */
    public function forceDeleted(Clinic $clinic)
    {
        //
    }
}
