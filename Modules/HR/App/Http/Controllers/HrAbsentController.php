<?php

namespace Modules\HR\App\Http\Controllers;

use Modules\HR\App\Http\Requests\CreateHrAbsentRequest;
use Modules\HR\App\Http\Requests\UpdateHrHolidayRequest;
use App\Http\Controllers\AppBaseController;
use Modules\HR\App\Repositories\HrAbsentRequestRepository;
use Illuminate\Http\Request;

class HrAbsentController extends AppBaseController
{
    /** @var HrAbsentRequestRepository $hrAbsentRepository*/
    private $hrAbsentRepository;

    public function __construct(HrAbsentRequestRepository $hrHolidayRepo)
    {
        $this->hrAbsentRepository = $hrHolidayRepo;
    }

    /**
     * Display a listing of the HrHoliday.
     */
    public function index(Request $request)
    {
        $data['absentrequests'] = $this->hrAbsentRepository->paginate(10);
        $data['employees'] = $this->hrAbsentRepository->employees();
       // $data['types'] = $this->hrAbsentRepository->types();
        $data['statuses'] = $this->hrAbsentRepository->statuses();

       // dd($data['absenrequests']);
        return view('hr::absentrequests.index', $data);
    }

    /**
     * Show the form for creating a new HrHoliday.
     */
    public function create()
    {
        $data['employees'] = $this->hrAbsentRepository->employees();
       // $data['types'] = $this->hrAbsentRepository->types();
        $data['statuses'] = $this->hrAbsentRepository->statuses();

        return view('hr::holidays.create', $data);
    }

    /**
     * Store a newly created HrHoliday in storage.
     */
    public function store(CreateHrAbsentRequest $request)
    {
        //dd($request->all());
        $input = $request->all();
         
        $holiday = $this->hrAbsentRepository->create($input);
        // $this->hrAbsentRepository->checkTracking($holiday);

        flash()->success(__('messages.saved', ['model' => __('models/hr_holidays.singular')]));

         if (str_contains($_SERVER['HTTP_REFERER'], 'employeeDashboard')) 
        {
            
              $phone = '966544499336';
            $empname =auth()->user()->name;
            $curl = curl_init();

        $req = json_encode([
            'src' => 'Evix',
            'dests' => [$phone],
            'body' => 'تم رفع طلب إستئذان خروج اثناء الدوام الموظف :' . " $empname",
            'priority' => 0,
            'delay' => 0,
            'validity' => 0,
            'maxParts' => 0,
            'dlr' => 0,
            'prevDups' => 0,
            'msgClass' => 'transactional',
        ]);

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.oursms.com/msgs/sms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $req,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer xZX_3oI0jaf-2Q3gWF3N'],
        ]);

        $response = curl_exec($curl);
      //  dd($response);
        // Check for cURL errors
        if (curl_errno($curl)) {
            return response()->json(['error' => 'cURL error: ' . curl_error($curl)], 500);
        }

        curl_close($curl); // Close the cURL session

        $obj = json_decode($response);
            

            //return back();
        }

        //return redirect(route('hr.holidays.index'));
        return back();
    }
    
    public function storeabsent(CreateHrAbsentRequest $request)
    {
        dd($request->all());
        $input = $request->all();
        $holiday = $this->hrAbsentRepository->create($input);
   
        flash()->success(__('messages.saved', ['model' => __('models/hr_holidays.singular')]));

        if (str_contains($_SERVER['HTTP_REFERER'], 'my-requests')) {
        
            return back();
        }

        
        return back();
    }
    /**
     * Display the specified HrHoliday.
     */
    public function show($id)
    {
        $data['holiday'] = $this->hrAbsentRepository->find($id);

        if (empty($data['holiday'])) {
            flash()->error(__('models/hr_holidays.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.holidays.index'));
        }

        return view('hr::holidays.show', $data);
    }

    /**
     * Show the form for editing the specified HrHoliday.
     */
    public function edit($id)
    {
        $data['holiday'] = $this->hrAbsentRepository->find($id);
        if (empty($data['holiday'])) {
            flash()->error(__('models/hr_holidays.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.holidays.index'));
        }

        $data['employees'] = $this->hrAbsentRepository->employees();
     
        $data['statuses'] = $this->hrAbsentRepository->statuses();

        return view('hr::absentrequests.edit', $data);
    }

    /**
     * Update the specified HrHoliday in storage.
     */
    public function update($id, UpdateHrHolidayRequest $request)
    {
        $holiday = $this->hrAbsentRepository->find($id);

        if (empty($holiday)) {
            flash()->error(__('models/hr_holidays.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.holidays.index'));
        }

        $holiday = $this->hrAbsentRepository->update($request->all(), $id);
        //$this->hrAbsentRepository->checkTracking($holiday);

        flash()->success(__('messages.updated', ['model' => __('models/hr_holidays.singular')]));

        return redirect(route('hr.absentrequests.index'));
    }

    /**
     * Remove the specified HrHoliday from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $holiday = $this->hrHolidayRepository->find($id);

        if (empty($holiday)) {
            flash()->error(__('models/hr_holidays.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.holidays.index'));
        }

        $this->hrHolidayRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/hr_holidays.singular')]));

        return redirect(route('hr.holidays.index'));
    }

    public function updatestatus($id,$status)
    {
        $abrequest= $this->hrAbsentRepository->find($id);
        if (empty($abrequest)) {
            flash()->error(__('models/hr_absentrequest.singular') . ' ' . __('messages.not_found'));

            return redirect(route('hr.absentrequests.index'));
        }
        // $abrequest= $this->hrAbsentRepository->update($request->all(), $id);
        $abrequest->status=$status;
        $abrequest =$abrequest->save();
        return redirect(route('hr.absentrequests.index'));
    }
}
