<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrCalendarEventRequest;
use Modules\HR\App\Http\Requests\UpdateHrCalendarEventRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrCalendarEventRepository;
use Illuminate\Http\Request;

class HrCalendarEventController extends AppBaseController
{
    /** @var HrCalendarEventRepository $hrCalendarEventRepository*/
    private $hrCalendarEventRepository;

    public function __construct(HrCalendarEventRepository $hrCalendarEventRepo)
    {
        $this->hrCalendarEventRepository = $hrCalendarEventRepo;
    }

    /**
     * Display a listing of the HrCalendarEvents.
     */
    public function index(Request $request)
    {
        $data['calendar_events'] = $this->hrCalendarEventRepository->allQuery($request->except('pagination'))->paginate($request->pagination ?? 10);
        $data['statuses'] = $this->hrCalendarEventRepository->statuses();
        $data['types'] = $this->hrCalendarEventRepository->types();
        return view('hr::calendar_events.index', $data);
    }

    /**
     * Show the form for creating a new HrCalendarEvents.
     */
    public function create()
    {
        $data['statuses'] = $this->hrCalendarEventRepository->statuses();
        $data['types'] = $this->hrCalendarEventRepository->types();
        return view('hr::calendar_events.create', $data);
    }

    /**
     * Store a newly created HrCalendarEvents in storage.
     */
    public function store(CreateHrCalendarEventRequest $request)
    {
        $input = $request->all();

        $calendarEvent = $this->hrCalendarEventRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_calendar_events.singular')]));

        return redirect(route('hr.CalendarEvents.index'));
    }

    /**
     * Display the specified HrCalendarEvents.
     */
    public function show($id)
    {
        $calendarEvent = $this->hrCalendarEventRepository->find($id);

        if (empty($calendarEvent)) {
            flash()->error(__('hr::models/hr_calendar_events.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.CalendarEvents.index'));
        }

        return view('hr::calendar_events.show')->with('calendarEvent', $calendarEvent);
    }

    /**
     * Show the form for editing the specified HrCalendarEvents.
     */
    public function edit($id)
    {
        $data['calendarEvent'] = $this->hrCalendarEventRepository->find($id);

        if (empty($data['calendarEvent'])) {
            flash()->error(__('hr::models/hr_calendar_events.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.CalendarEvents.index'));
        }
        $data['statuses'] = $this->hrCalendarEventRepository->statuses();
        $data['types'] = $this->hrCalendarEventRepository->types();

        return view('hr::calendar_events.edit', $data);
    }

    /**
     * Update the specified HrCalendarEvents in storage.
     */
    public function update($id, UpdateHrCalendarEventRequest $request)
    {
        $calendarEvent = $this->hrCalendarEventRepository->find($id);

        if (empty($calendarEvent)) {
            flash()->error(__('hr::models/hr_calendar_events.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.CalendarEvents.index'));
        }

        $calendarEvent = $this->hrCalendarEventRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('hr::models/hr_calendar_events.singular')]));

        return redirect(route('hr.CalendarEvents.index'));
    }

    /**
     * Remove the specified HrCalendarEvents from storage.
     */
    public function destroy($id)
    {
        $calendarEvent = $this->hrCalendarEventRepository->find($id);

        if (empty($calendarEvent)) {
            flash()->error(__('hr::models/hr_calendar_events.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.CalendarEvents.index'));
        }

        $this->hrCalendarEventRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_calendar_events.singular')]));

        return redirect(route('hr.CalendarEvents.index'));
    }
}
