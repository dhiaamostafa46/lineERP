<?php

namespace App\Repositories;

use App\Models\Theme;
use App\Repositories\BaseRepository;

class ThemeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'panel_body_background',
        'panel_aside_background',
        'panel_aside_color',
        'panel_btn_background',
        'panel_header_color',
        'panel_content_color',
        'panel_btn_color',
        'mobile_body_background',
        'mobile_aside_background',
        'mobile_aside_color',
        'mobile_btn_background',
        'mobile_header_color',
        'mobile_content_color',
        'mobile_btn_color'
    ];


    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model(): string
    {
        return Theme::class;
    }
}
