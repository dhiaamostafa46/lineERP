<?php

namespace App\Http\Controllers;

use App\Http\Requests\branchesRequest;
use App\Repositories\BranchRepository;
use Illuminate\Http\Request;

class BranchesController extends Controller
{
    private $BranchRepository;

    public function __construct(BranchRepository $BranchRepository)
    {
        $this->BranchRepository = $BranchRepository;
    }

 
    /**
     * Get branches based on global viewBranches permission.
     */
    public function viewBranches(Request $request)
    {
        // Default to filtering by the user's current branch
        $query = Branch::query();
        if (auth()->check() && !auth()->user()->can('global.viewBranches')) {
            $query->where('id', auth()->user()->branch_id);
        }
        
        // You might want to filter active branches or format them for a dropdown
        return response()->json($query->get());
    }

    /**
     * Display a listing of the HrAssetType.
     */
    public function index(Request $request)
    {
        $data['Branches'] = $this->BranchRepository->paginate(10);
        $data['statuses'] = $this->BranchRepository->statuses();

        return view('Branches.index', $data);
    }

    /**
     * Show the form for creating a new HrAssetType.
     */
    public function create()
    {
        $data['statuses'] = $this->BranchRepository->statuses();

        return view('Branches.create', $data);
    }

    /**
     * Store a newly created HrAssetType in storage.
     */
    public function store(branchesRequest $request)
    {
        $input = $request->all();

        $Branches = $this->BranchRepository->create($input);

        flash()->success(__('messages.saved', ['model' => __('models/Branches.singular')]));

        return redirect(route('Branches.index'));
    }

    /**
     * Display the specified HrAssetType.
     */
    public function show($id)
    {
        $Branch = $this->BranchRepository->find($id);

        if (empty($Branch)) {
            flash()->error(__('models/Branches.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Branches.index'));
        }

        return view('Branches.show')->with('Branch', $Branch);
    }

    /**
     * Show the form for editing the specified HrAssetType.
     */
    public function edit($id)
    {
        $data['Branch'] = $this->BranchRepository->find($id);

        if (empty($data['Branch'])) {
            flash()->error(__('models/Branches.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Branches.index'));
        }
        $data['statuses'] = $this->BranchRepository->statuses();

        return view('Branches.edit', $data);
    }

    /**
     * Update the specified HrAssetType in storage.
     */
    public function update($id, branchesRequest $request)
    {
        $Branches = $this->BranchRepository->find($id);

        if (empty($Branches)) {
            flash()->error(__('models/Branches.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Branches.index'));
        }

        $Branches = $this->BranchRepository->update($request->all(), $id);

        flash()->success(__('messages.updated', ['model' => __('models/Branches.singular')]));

        return redirect(route('Branches.index'));
    }

    /**
     * Remove the specified HrAssetType from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $Branches = $this->BranchRepository->find($id);

        if (empty($Branches)) {
            flash()->error(__('models/Branches.singular') . ' ' . __('messages.not_found'));

            return redirect(route('Branches.index'));
        }

        $this->BranchRepository->delete($id);

        flash()->success(__('messages.deleted', ['model' => __('models/Branches.singular')]));

        return redirect(route('Branches.index'));
    }

    /**
     * Switch active branch for authenticated user.
     */
    public function switchBranch($branchId)
    {
        $branch = \App\Models\Branch::findOrFail($branchId);

        $user = auth()->user();
        if ($user) {
            $user->branch_id = $branch->id;
            $user->save();
            session(['current_branch_id' => $branch->id]);
        }

        flash(__('messages.updated', ['model' => __('models/Branches.singular')]))->success();
        return redirect()->back();
    }
}
