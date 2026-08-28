<?php

namespace Modules\HR\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\HR\App\Http\Requests\HrPlaceRequest;
use Modules\HR\App\Repositories\HrPlaceRepository;
use Maatwebsite\Excel\Facades\Excel;
use Modules\HR\App\Exports\HrPlaceExport;
use Modules\HR\App\Imports\HrPlaceImport;


class HrPlaceController extends Controller
{
   /** @var HrPlaceRepository $HrPlaceRepository*/
   private $HrPlaceRepository;

   public function __construct(HrPlaceRepository $HrPlaceRepository)
   {
       $this->HrPlaceRepository = $HrPlaceRepository;
   }

   /**
    * Display a listing of the HrPlace.
    */
   public function index(Request $request)
   {
       $data['Places'] = $this->HrPlaceRepository->allQuery($request->except('pagination'))->paginate($request->pagination ?? 10);
    //    $data['employees'] = $this->HrPlaceRepository->employees();
       $data['statuses'] = $this->HrPlaceRepository->statuses();

       return view('hr::Place.index', $data);
   }

   /**
    * Show the form for creating a new HrPlace.
    */
   public function create()
   {
       $data['employees']      = $this->HrPlaceRepository->employees();
       $data['statuses']       = $this->HrPlaceRepository->statuses();
       $data['weekdays']       = $this->HrPlaceRepository->weekdays();
       $data['flages']         = $this->HrPlaceRepository->flages();
       $data['Department']     = $this->HrPlaceRepository->Department();
       $data['Branches']     = $this->HrPlaceRepository->Branches();

       return view('hr::Place.create', $data);
   }

   /**
    * Store a newly created HrPlace in storage.
    */
   public function store(HrPlaceRequest $request)
   {
       $input = $request->all();


       if (isset($input['daterangepicker'])) {

           $dates = explode(' - ', $input['daterangepicker']);


           if (count($dates) == 2) {
              $input['start_date'] = \Carbon\Carbon::parse(trim($dates[0]))->toDateTimeString();
               $input['end_date'] = \Carbon\Carbon::parse(trim($dates[1]))->toDateTimeString();
           }
       }
       unset($input['daterangepicker']);



       $hrPlace = $this->HrPlaceRepository->create($input);

       flash()->success(__('messages.saved', ['model' => __('hr::models/hr_places.singular')]));

       return redirect(route('hr.Place.index'));
   }

   /**
    * Display the specified HrPlace.
    */
   public function show($id)
   {
       $data['Place'] = $this->HrPlaceRepository->find($id);

       if (empty($data['Place'])) {
           flash()->error(__('hr::models/hr_places.singular') . ' ' . __('messages.not_found'));

           return redirect(route('hr.Place.index'));
       }

       return view('hr::Place.show', $data);
   }

   /**
    * Show the form for editing the specified HrPlace.
    */
   public function edit($id)
   {
       $data['Place']          = $this->HrPlaceRepository->find($id);
       $data['employees']      = $this->HrPlaceRepository->employees();
       $data['statuses']       = $this->HrPlaceRepository->statuses();
       $data['weekdays']       = $this->HrPlaceRepository->weekdays();
       $data['flages']         = $this->HrPlaceRepository->flages();
       $data['Department']     = $this->HrPlaceRepository->Department();
       $data['Branches']     = $this->HrPlaceRepository->Branches();

       if (empty($data['Place'])) {
           flash()->error(__('hr::models/hr_places.singular') . ' ' . __('messages.not_found'));

           return redirect(route('hr.Place.index'));
       }
       $data['employees'] = $this->HrPlaceRepository->employees();

       $data['statuses'] = $this->HrPlaceRepository->statuses();
       return view('hr::Place.edit', $data);
   }

   /**
    * Update the specified HrPlace in storage.
    */
   public function update($id, HrPlaceRequest $request)
   {
       $hrPlace = $this->HrPlaceRepository->find($id);

       if (empty($hrPlace)) {
           flash()->error(__('hr::models/hr_places.singular') . ' ' . __('messages.not_found'));

           return redirect(route('hr.Place.index'));
       }

       $input = $request->all();
       if (isset($input['daterangepicker'])) {
           $dates = explode(' - ', $input['daterangepicker']);
           if (count($dates) == 2) {
               $input['start_date'] = \Carbon\Carbon::parse(trim($dates[0]))->toDateTimeString();
               $endDateStr = trim($dates[1]);
               $endDate = \Carbon\Carbon::parse($endDateStr);
               if (!str_contains($endDateStr, ':')) {
                   $endDate->endOfDay();
               }
               $input['end_date'] = $endDate->toDateTimeString();
           }
       }
       unset($input['daterangepicker']);
       $hrPlace = $this->HrPlaceRepository->update($input, $id);

       flash()->success(__('messages.updated', ['model' => __('hr::models/hr_places.singular')]));

       return redirect(route('hr.Place.index'));
   }

   /**
    * Remove the specified HrPlace from storage.
    *
    * @throws \Exception
    */
   public function destroy($id)
   {
       $hrPlace = $this->HrPlaceRepository->find($id);

       if (empty($hrPlace)) {
           flash()->error(__('hr::models/hr_places.singular') . ' ' . __('messages.not_found'));

           return redirect(route('hr.Place.index'));
       }

        $this->HrPlaceRepository->delete($id);
       flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_places.singular')]));

       return redirect(route('hr.Place.index'));
   }
    public function export()
    {
        $places = $this->HrPlaceRepository->all();
        return Excel::download(new HrPlaceExport($places), 'HrPlaces.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new HrPlaceImport, $request->file('file'));

        flash()->success(__('messages.saved', ['model' => __('hr::models/hr_places.plural')]));

        return redirect(route('hr.Place.index'));
    }
}
