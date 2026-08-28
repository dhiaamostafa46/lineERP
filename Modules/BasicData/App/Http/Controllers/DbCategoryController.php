<?php

namespace Modules\BasicData\App\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Modules\BasicData\App\Exports\BasicDataExport;
use Modules\BasicData\App\Http\Requests\CreateDbCategoryRequest;
use Modules\BasicData\App\Http\Requests\UpdateDbCategoryRequest;
use Modules\BasicData\App\Repositories\DbCategoryRepository;
use Maatwebsite\Excel\Facades\Excel;

class DbCategoryController extends AppBaseController
{
    use \App\Traits\HasBulkActions;

    /** @var DbCategoryRepository $dbCategoryRepository*/
    private $dbCategoryRepository;

    public function __construct(DbCategoryRepository $dbCategoryRepo)
    {
        $this->dbCategoryRepository = $dbCategoryRepo;
    }

    /**
     * Display a listing of the Category.
     */
    public function index(Request $request)
    {
        $pagination = $request->get('pagination', 10);
        $data['categories'] = $this->dbCategoryRepository->allQuery($request->except('pagination'))->paginate($pagination);
        $data['statuses'] = $this->dbCategoryRepository->statuses();
        $data['types'] = $this->dbCategoryRepository->types();
        $data['parent_categories'] = $this->dbCategoryRepository->parentCategories();

        $categoryModel = \App\Models\BasicDataApp\Category::class;
        $data['totalCategoriesCount'] = $categoryModel::count();
        $data['activeCategoriesCount'] = $categoryModel::where('status', 1)->count();
        $data['mainCategoriesCount'] = $categoryModel::whereNull('parent_id')->count();
        $data['subCategoriesCount'] = $categoryModel::whereNotNull('parent_id')->count();

        return view('basicdata::categories.index', $data);
    }

    /**
     * Show the form for creating a new Category.
     */
    public function create()
    {

        $data['statuses'] = $this->dbCategoryRepository->statuses();
        $data['types'] = $this->dbCategoryRepository->types();
        $data['parent_categories'] = $this->dbCategoryRepository->parentCategories();
        return view('basicdata::categories.create', $data);
    }

    /**
     * Store a newly created Category in storage.
     */
    public function store(CreateDbCategoryRequest $request)
    {
        try {


            $input = $request->all();

            $category = $this->dbCategoryRepository->create($input);

            flash()->success(__('messages.saved', ['model' => __('basicdata::models/db_categories.singular')]));

            return redirect()->route('basicdata.categories.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_creating', ['model' => __('basicdata::models/db_categories.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display the specified Category.
     */
    public function show($id)
    {
        $category = $this->dbCategoryRepository->find($id);

        if (empty($category)) {
            flash()->error(__('basicdata::models/db_categories.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.categories.index'));
        }

        return view('basicdata::categories.show')->with('category', $category);
    }

    /**
     * Show the form for editing the specified Category.
     */
    public function edit($id)
    {
        $Category = $this->dbCategoryRepository->find($id);
        $data['statuses'] = $this->dbCategoryRepository->statuses();
        $data['types'] = $this->dbCategoryRepository->types();
        $data['parent_categories'] = $this->dbCategoryRepository->parentCategories($id);
        $data['Category'] = $Category;
        if (empty($Category)) {
            flash()->error(__('basicdata::models/db_categories.singular') . ' ' . __('messages.not_found'));
            return redirect(route('basicdata.categories.index'));
        }

        return view('basicdata::categories.edit' , $data);
    }

    /**
     * Update the specified Category in storage.
     *
     * @param int $id
     * @param UpdateDbCategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateDbCategoryRequest $request, $id)
    {
        try {
            $category = $this->dbCategoryRepository->find($id);

            if (empty($category)) {
                flash()->error(__('basicdata::models/db_categories.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.categories.index'));
            }

            $input = $request->all();
            $category = $this->dbCategoryRepository->update($input, $id);

            flash()->success(__('messages.updated', ['model' => __('basicdata::models/db_categories.singular')]));

            return redirect()->route('basicdata.categories.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_updating', ['model' => __('basicdata::models/db_categories.singular')]) . ': ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified Category from storage.
     */
    public function destroy($id)
    {
        try {
            $category = $this->dbCategoryRepository->find($id);

            if (empty($category)) {
                flash()->error(__('basicdata::models/db_categories.singular') . ' ' . __('messages.not_found'));
                return redirect(route('basicdata.categories.index'));
            }

            $this->dbCategoryRepository->delete($id);

            flash()->success(__('messages.deleted', ['model' => __('basicdata::models/db_categories.singular')]));

            return redirect()->route('basicdata.categories.index');
        } catch (\Exception $e) {
            flash()->error(__('messages.error_deleting', ['model' => __('basicdata::models/db_categories.singular')]) . ': ' . $e->getMessage());
            return redirect()->back();
        }
    }


        public function excel()
    {
        $headers = $this->dbCategoryRepository->header();
        $dataExcel = $this->dbCategoryRepository->dataExel(); // استخدم Unit بدلاً من dataExel

        return Excel::download(new BasicDataExport($dataExcel, $headers), 'categories.xlsx');
    }

    public function csv()
    {
        $headers = $this->dbCategoryRepository->header();
        $dataExcel = $this->dbCategoryRepository->dataExel();

        return Excel::download(new BasicDataExport($dataExcel, $headers), 'categories.csv');
    }

    public function pdf()
    {
         $headers = $this->dbCategoryRepository->header();
        $dataExcel = $this->dbCategoryRepository->dataExel();
          $name = $this->dbCategoryRepository->name();


            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8']);
            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont = true;
            $mpdf->autoArabic = true;

            $mpdf->baseScript = 1;
            $mpdf->autoVietnamese = true;

            $mpdf->shrink_tables_to_fit = 1;
            $mpdf->keep_table_proportions = true;

            $mpdf->SetDisplayMode('fullpage');

            $mpdf->list_indent_first_level = 0;
            $mpdf->SetDirectionality(  app()->getLocale() == 'ar' ? 'rtl' : 'ltr');
            $mpdf->WriteHTML(view('basicdata::exports.pdf', ['headers' => $headers ,'data'=>  $dataExcel ,'name'=> $name]));
            $mpdf->Output();


    }
}
