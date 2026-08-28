<?php


namespace Modules\HR\App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HrTemplateController extends Controller
{
    public function index()
    {
        return view('hr::templates.index');
      
    }
}

