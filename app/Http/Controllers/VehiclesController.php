<?php

namespace App\Http\Controllers;

use App\Models\Vehicles\Brand;
use Illuminate\Http\Request;
use App\Models\Vehicles\Vehicle;
use App\Repositories\vc\VehiclesRepository;
use App\Http\Requests\CreateVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;

class VehiclesController extends AppBaseController
{
/** @var VehiclesRepository $vehiclesRepository*/
    private $vehiclesRepository;

    public function __construct(VehiclesRepository $employeeRepo)
    {
        $this->vehiclesRepository = $employeeRepo;
    }

    
    /**
     * Show the form for creating a new Employee.
     */
    public function create()
    {
        $data['branches'] = $this->vehiclesRepository->branches();
        $data['brands']  = Brand::activeOnly()->get();
        $data['statuses'] = Vehicle::statuses();


        return view('Vehicles.create', $data);
    }

    /**
     * Store a newly created Vehicle in storage.
     */
    public function store(CreateVehicleRequest $request)
    {
        $input = $request->all();

        $Vehicle = $this->vehiclesRepository->create($input);

        // $Vehicle->identity()->create($input);
        // $Vehicle->bank()->create($input);

        flash()->success(__('messages.saved', ['model' => __('models/vehicles.singular')]));

        return redirect(route('Vehicles.index'));
    }

    /**
     * Display the specified Vehicle.
     */
    public function show($id)
    {
        $Vehicle = $this->vehiclesRepository->find($id);

        if (empty($Vehicle)) {
            flash()->error(__('models/vehicles.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Vehicles.index'));
        }

        return view('Vehicles.show')->with('Vehicle', $Vehicle);
    }

    /**
     * Show the form for editing the specified Vehicle.
     */
    public function edit($id)
    {
        $Vehicle = $this->vehiclesRepository->find($id);

        $data['genders']         = Vehicle::genders();
        $data['maritalStatuses'] = Vehicle::maritalStatuses();
        $data['identityTypes']   = VehicleIdentity::types();
        $data['Vehicle']        = $Vehicle;

        if (empty($Vehicle)) {
            flash()->error(__('models/vehicles.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Vehicles.index'));
        }

        return view('Vehicles.edit', $data);
    }

    /**
     * Update the specified Vehicle in storage.
     */
    public function update($id, UpdateVehicleRequest $request)
    {
        $Vehicle = $this->vehiclesRepository->find($id);

        if (empty($Vehicle)) {
            flash()->error(__('models/vehicles.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Vehicles.index'));
        }

        $Vehicle = $this->vehiclesRepository->update($request->all(), $id);

        // dd($request->all());
        $Vehicle->identity()->update($request->only(['identity_type', 'identity_no', 'insurance_no', 'identity_expired_at', 'insurance_expired_at']));
        $Vehicle->bank()->update($request->only(['iban', 'bank_name']));

        flash()->success(__('messages.updated', ['model' => __('models/vehicles.singular')]));

        return redirect(route('Vehicles.index'));
    }


    

    /**
     * Remove the specified Vehicle from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $Vehicle = $this->vehiclesRepository->find($id);

        if (empty($Vehicle)) {
            flash()->error(__('models/vehicles.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Vehicles.index'));
        }

        $this->vehiclesRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/vehicles.singular')]));

        return redirect(route('Vehicles.index'));
    }
}