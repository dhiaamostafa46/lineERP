<?php

namespace Modules\HR\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\HR\App\Repositories\HrContractItemsRepository;

class HrContractItemController extends Controller
{
     /** @var HrContractItemsRepository $HrContractItemsRepositorysitory*/
     private $HrContractItemsRepositorysitory;

     public function __construct(HrContractItemsRepository $HrContractItemsRepository)
     {
         $this->HrContractItemsRepositorysitory = $HrContractItemsRepository;
     }

     /**
      * Display a listing of the HrContractType.
      */
     public function index(Request $request)
     {
         $data['contract_items'] = $this->HrContractItemsRepositorysitory->allQuery($request->except('pagination'))->paginate($request->pagination ?? 5);

         return view('hr::contract_items.index', $data);
     }

     /**
      * Show the form for creating a new HrContractType.
      */
     public function create()
     {


         return view('hr::contract_items.create', );
     }

     /**
      * Store a newly created HrContractType in storage.
      */
     public function store(Request $request)
     {
         $input = $request->all();

         $contract_item = $this->HrContractItemsRepositorysitory->create($input);

         flash()->success(__('messages.saved', ['model' => __('hr::models/hr_contract_items.singular')]));

         return redirect()->back();
     }

     /**
      * Display the specified HrContractType.
      */
     public function show($id)
     {
         $data['contract'] = $this->HrContractItemsRepositorysitory->listItems($id);

         if (empty($data['contract'])) {
             flash()->error(__('hr::models/hr_contract_items.singular') . ' ' . __('messages.not_found'));

             return redirect(route('hr.contract_items.index'));
         }

         return view('hr::contracts.contractItems', $data);
     }

     /**
      * Show the form for editing the specified HrContractType.
      */
     public function edit($id)
     {
         $data['contract_item'] = $this->HrContractItemsRepositorysitory->find($id);

         if (empty($data['contract_item'])) {
             flash()->error(__('hr::models/hr_contract_items.singular') . ' ' . __('messages.not_found'));

             return redirect(route('hr.contract_items.index'));
         }

         $data['statuses'] = $this->HrContractItemsRepositorysitory->statuses();
         return view('hr::contract_items.edit', $data);
     }

     /**
      * Update the specified HrContractType in storage.
      */
     public function update($id, Request $request)
     {
         $contract_item = $this->HrContractItemsRepositorysitory->find($id);

         if (empty($contract_item)) {
             flash()->error(__('hr::models/hr_contract_items.singular') . ' ' . __('messages.not_found'));

             return redirect(route('hr.contract_items.index'));
         }

         $contract_item = $this->HrContractItemsRepositorysitory->update($request->all(), $id);

         flash()->success(__('messages.updated', ['model' => __('hr::models/hr_contract_items.singular')]));

         return redirect()->back();
     }

     /**
      * Remove the specified HrContractType from storage.
      *
      * @throws \Exception
      */
     public function destroy($id)
     {
         $contract_item = $this->HrContractItemsRepositorysitory->find($id);

         if (empty($contract_item)) {
             flash()->error(__('hr::models/hr_contract_items.singular') . ' ' . __('messages.not_found'));

             return redirect(route('hr.contract_items.index'));
         }

         $this->HrContractItemsRepositorysitory->delete($id);

         flash()->success(__('messages.deleted', ['model' => __('hr::models/hr_contract_items.singular')]));

         return redirect()->back();
     }
}
