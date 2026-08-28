<?php

namespace Modules\HR\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\HR\App\Models\HrEmployee;
use Modules\HR\App\Repositories\HrArchiveRepository;
use Modules\HR\App\Helpers\RemoveEmployee;

class HrArchiveController extends Controller
{
    use RemoveEmployee;

    private $HrArchiveRepository;

    public function __construct(HrArchiveRepository $HrArchiveRepository)
    {
        $this->HrArchiveRepository = $HrArchiveRepository;
    }


    

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = HrEmployee::onlyTrashed();


        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('id', $request->employee_id);
        }

        if ($request->has('job_id') && $request->job_id) {
            $query->where('job_id', $request->job_id);
        }

        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

     
        $data['Hremployee'] = $query->paginate($request->get('pagination', 10)); // العدد الافتراضي 10 إذا لم يتم تحديد pagination
        $data['employees'] = $this->HrArchiveRepository->employees();
        $data['jobs'] = $this->HrArchiveRepository->jobs();
        $data['departments'] = $this->HrArchiveRepository->departments();

        return view('hr::Archive.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hr::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {

        $data['Employee']   = HrEmployee::where('id', $id)->onlyTrashed()->first();
        $data['Document']   = $data['Employee']->Document()->withTrashed()->get();
        $data['salary']     = $data['Employee']->salary()->withTrashed()->first();
        $data['Contract']   = $data['Employee']->Contract()->withTrashed()->get();
        $data['page']   = 'profile';


        return view('hr::Archive.show' ,$data);
    }


    public function penalties($id)
    {
        $data['Employee']   = HrEmployee::where('id', $id)->onlyTrashed()->first();
        $data['penalties']  = $data['Employee']->penalties()->withTrashed()->get();
        $data['page']       = 'penalties';
        return view('hr::Archive.show' ,$data);

    }

    public function advances($id)
    {
        $data['Employee']   = HrEmployee::where('id', $id)->onlyTrashed()->first();
        $data['advances'] = $data['Employee']->advances()->withTrashed()->get();
        $data['page']       = 'advances';
        return view('hr::Archive.show' ,$data);

    }


    public function rewards($id)
    {
        $data['Employee']   = HrEmployee::where('id', $id)->onlyTrashed()->first();
        $data['rewards']    = $data['Employee']->rewards()->withTrashed()->get();

        $data['page']       = 'rewards';
        return view('hr::Archive.show' ,$data);

    }


    public function custodies($id)
    {
        $data['Employee']     = HrEmployee::where('id', $id)->onlyTrashed()->first();
        $data['custodies']    = $data['Employee']->Custodies()->withTrashed()->get();
        $data['page']         = 'custodies';
        return view('hr::Archive.show' ,$data);

    }

    public function holidays($id)
    {
        $data['Employee']     = HrEmployee::where('id', $id)->onlyTrashed()->first();
        $data['holidays']    = $data['Employee']->holidays()->withTrashed()->get();
        $data['AbsentRequests'] = $data['Employee']->AbsentRequests()->withTrashed()->get();
        $data['page']         = 'holidays';
        return view('hr::Archive.show' ,$data);

    }








    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('hr::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore($id)
    {
        $result = $this->RestoreEmployee($id);

        if ($result['status']) {
            return redirect()->route('hr.Archive.index')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
