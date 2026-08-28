<?php

namespace Database\Seeders;

use App\Models\Vehicles\Brand;
use App\Models\Vehicles\vehicleModel;
use Illuminate\Database\Seeder;

class VehicleBrandModelSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [

            [
                'en' => 'Toyota',
                'ar' => 'تويوتا',
                'models' => [
                    ['en' => 'Corolla', 'ar' => 'كورولا'],
                    ['en' => 'Yaris', 'ar' => 'يارس'],
                    ['en' => 'Raize', 'ar' => 'رايز'],
                    ['en' => 'Urban Cruiser', 'ar' => 'أوربان كروزر'],
                    ['en' => 'Hiace', 'ar' => 'هايس'],
                ],
            ],

            [
                'en' => 'Hyundai',
                'ar' => 'هيونداي',
                'models' => [
                    ['en' => 'Accent', 'ar' => 'أكسنت'],
                    ['en' => 'Elantra', 'ar' => 'إلنترا'],
                    ['en' => 'Creta', 'ar' => 'كريتا'],
                    ['en' => 'H-1', 'ar' => 'إتش 1'],
                    ['en' => 'Staria', 'ar' => 'ستاريا'],
                ],
            ],

            [
                'en' => 'Kia',
                'ar' => 'كيا',
                'models' => [
                    ['en' => 'Pegas', 'ar' => 'بيجاس'],
                    ['en' => 'Rio', 'ar' => 'ريو'],
                    ['en' => 'Sonet', 'ar' => 'سونيت'],
                    ['en' => 'Sportage', 'ar' => 'سبورتاج'],
                ],
            ],

            [
                'en' => 'Nissan',
                'ar' => 'نيسان',
                'models' => [
                    ['en' => 'Sunny', 'ar' => 'صني'],
                    ['en' => 'Kicks', 'ar' => 'كيكس'],
                    ['en' => 'Urvan', 'ar' => 'أورفان'],
                ],
            ],

            [
                'en' => 'Honda',
                'ar' => 'هوندا',
                'models' => [
                    ['en' => 'City', 'ar' => 'سيتي'],
                    ['en' => 'Civic', 'ar' => 'سيفيك'],
                ],
            ],

            [
                'en' => 'Ford',
                'ar' => 'فورد',
                'models' => [
                    ['en' => 'Transit', 'ar' => 'ترانزيت'],
                    ['en' => 'Transit Connect', 'ar' => 'ترانزيت كونيكت'],
                ],
            ],

            [
                'en' => 'Renault',
                'ar' => 'رينو',
                'models' => [
                    ['en' => 'Duster', 'ar' => 'داستر'],
                    ['en' => 'Kangoo', 'ar' => 'كانجو'],
                ],
            ],

            [
                'en' => 'Peugeot',
                'ar' => 'بيجو',
                'models' => [
                    ['en' => 'Partner', 'ar' => 'بارتنر'],
                ],
            ],

            [
                'en' => 'Citroen',
                'ar' => 'سيتروين',
                'models' => [
                    ['en' => 'Berlingo', 'ar' => 'برلينجو'],
                ],
            ],

        ];

        foreach ($brands as $brandData) {

            $brand = Brand::create([
                'status' => 2,
            ]);

            $brand->translations()->createMany([
                [
                    'locale' => 'en',
                    'name' => $brandData['en'],
                ],
                [
                    'locale' => 'ar',
                    'name' => $brandData['ar'],
                ],
            ]);

            foreach ($brandData['models'] as $modelData) {

                $model = VehicleModel::create([
                    'brand_id' => $brand->id,
                    'status' => 2,
                ]);

                $model->translations()->createMany([
                    [
                        'locale' => 'en',
                        'name' => $modelData['en'],
                    ],
                    [
                        'locale' => 'ar',
                        'name' => $modelData['ar'],
                    ],
                ]);
            }
        }
    }
}
