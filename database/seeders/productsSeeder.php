<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BasicDataApp\Product;
use App\Models\BasicDataApp\Category;
use App\Models\BasicDataApp\Unit;
use App\Models\BasicDataApp\ProductUnit;
use App\Models\BasicDataApp\ProductSize;

class productsSeeder extends Seeder
{
    public function run()
    {
        // ===== الفئات =====
        $categoriesData = [
            ['ar' => 'مشروبات',         'en' => 'Beverages'],
            ['ar' => 'ألبان وأجبان',    'en' => 'Dairy & Cheese'],
            ['ar' => 'حبوب وأرز',       'en' => 'Grains & Rice'],
            ['ar' => 'زيوت وتوابل',     'en' => 'Oils & Spices'],
            ['ar' => 'معلبات ومجففات',  'en' => 'Canned & Dried'],
        ];

        if (Category::count() == 0) {
            foreach ($categoriesData as $cat) {
                $category = Category::create([
                    'org_id' => 1,
                    'user_id' => 1,
                    'status' => Category::STATUS_ACTIVE,
                    'type'   => Category::TYPE_Visible,
                ]);
                $category->translateOrNew('ar')->name = $cat['ar'];
                $category->translateOrNew('en')->name = $cat['en'];
                $category->save();
            }
        }

        $categories = Category::all()->keyBy(function ($c) {
            return $c->translate('en')->name ?? $c->id;
        });

        // ===== الوحدات =====
        $unitsData = [
            ['ar' => 'قطعة',   'en' => 'Piece',     'is_base' => true,  'factor' => 1],
            ['ar' => 'كرتون',  'en' => 'Carton',    'is_base' => false, 'factor' => 12],
            ['ar' => 'كيلوغرام','en' => 'Kilogram', 'is_base' => true,  'factor' => 1],
        ];

        if (Unit::count() == 0) {
            foreach ($unitsData as $u) {
                $unit = Unit::create([
                    'org_id'            => 1,
                    'user_id'           => 1,
                    'conversion_factor' => $u['factor'],
                    'status'            => Unit::STATUS_ACTIVE,
                    'is_base'           => $u['is_base'],
                    'is_virtual'        => Unit::VIRTUAL_FALSE,
                ]);
                $unit->translateOrNew('ar')->name = $u['ar'];
                $unit->translateOrNew('en')->name = $u['en'];
                $unit->save();
            }
        }

        $pieceUnit   = Unit::whereHas('translations', fn($q) => $q->where('name', 'Piece'))->first();
        $cartonUnit  = Unit::whereHas('translations', fn($q) => $q->where('name', 'Carton'))->first();
        $kgUnit      = Unit::whereHas('translations', fn($q) => $q->where('name', 'Kilogram'))->first();

        // ===== المنتجات =====
        // [ar_name, en_name, ar_details, en_details, category_key, cost, price, vat, type, have_sizes, unit, second_unit, sizes]
        $products = [
            // ---- مشروبات ----
            [
                'ar' => 'مياه معدنية نقية', 'en' => 'Pure Mineral Water',
                'ar_d' => 'مياه معدنية طبيعية خالية من الشوائب، حجم 500 مل',
                'en_d' => 'Natural mineral water, free of impurities, 500ml',
                'cat' => 'Beverages', 'cost' => 1.50, 'price' => 2.50, 'vat' => 15,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],
            [
                'ar' => 'عصير برتقال طبيعي', 'en' => 'Natural Orange Juice',
                'ar_d' => 'عصير برتقال طازج 100% بدون إضافات، حجم 1 لتر',
                'en_d' => '100% fresh orange juice with no additives, 1L',
                'cat' => 'Beverages', 'cost' => 5.00, 'price' => 8.50, 'vat' => 15,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],
            [
                'ar' => 'شاي أحمر فاخر', 'en' => 'Premium Black Tea',
                'ar_d' => 'شاي أحمر سيلاني فاخر، علبة 100 كيس',
                'en_d' => 'Premium Ceylon black tea, box of 100 bags',
                'cat' => 'Beverages', 'cost' => 12.00, 'price' => 18.00, 'vat' => 15,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],
            [
                'ar' => 'قهوة عربية مطحونة', 'en' => 'Ground Arabic Coffee',
                'ar_d' => 'قهوة عربية أصيلة بالهيل، وزن 500 غرام',
                'en_d' => 'Authentic Arabic coffee with cardamom, 500g',
                'cat' => 'Beverages', 'cost' => 25.00, 'price' => 38.00, 'vat' => 15,
                'type' => Product::TYPE_SALE, 'sizes' => true,
                'unit' => $kgUnit, 'unit2' => null,
                'size_list' => [
                    ['ar' => '250 غرام', 'en' => '250g',  'cost' => 14.00, 'price' => 21.00],
                    ['ar' => '500 غرام', 'en' => '500g',  'cost' => 25.00, 'price' => 38.00],
                    ['ar' => '1 كيلو',  'en' => '1kg',   'cost' => 47.00, 'price' => 72.00],
                ],
            ],

            // ---- ألبان وأجبان ----
            [
                'ar' => 'حليب كامل الدسم', 'en' => 'Full Fat Milk',
                'ar_d' => 'حليب بقري طازج كامل الدسم، حجم 1 لتر',
                'en_d' => 'Fresh full fat cow milk, 1L',
                'cat' => 'Dairy & Cheese', 'cost' => 4.50, 'price' => 7.00, 'vat' => 0,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],
            [
                'ar' => 'جبنة شيدر شرائح', 'en' => 'Sliced Cheddar Cheese',
                'ar_d' => 'جبنة شيدر أصلية مقطعة شرائح، وزن 400 غرام',
                'en_d' => 'Original sliced cheddar cheese, 400g',
                'cat' => 'Dairy & Cheese', 'cost' => 18.00, 'price' => 27.00, 'vat' => 15,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],
            [
                'ar' => 'زبادي يوناني', 'en' => 'Greek Yogurt',
                'ar_d' => 'زبادي يوناني كامل الدسم غني بالبروتين، 200 غرام',
                'en_d' => 'Full fat Greek yogurt rich in protein, 200g',
                'cat' => 'Dairy & Cheese', 'cost' => 6.00, 'price' => 9.50, 'vat' => 0,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],
            [
                'ar' => 'زبدة طبيعية', 'en' => 'Natural Butter',
                'ar_d' => 'زبدة طبيعية غير مملحة، وزن 200 غرام',
                'en_d' => 'Unsalted natural butter, 200g',
                'cat' => 'Dairy & Cheese', 'cost' => 10.00, 'price' => 15.00, 'vat' => 15,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],

            // ---- حبوب وأرز ----
            [
                'ar' => 'أرز بسمتي', 'en' => 'Basmati Rice',
                'ar_d' => 'أرز بسمتي هندي طويل الحبة فاخر، كيس 5 كيلو',
                'en_d' => 'Premium long grain Indian basmati rice, 5kg bag',
                'cat' => 'Grains & Rice', 'cost' => 28.00, 'price' => 42.00, 'vat' => 0,
                'type' => Product::TYPE_SALE, 'sizes' => true,
                'unit' => $kgUnit, 'unit2' => null,
                'size_list' => [
                    ['ar' => '2 كيلو', 'en' => '2kg',  'cost' => 12.00, 'price' => 18.00],
                    ['ar' => '5 كيلو', 'en' => '5kg',  'cost' => 28.00, 'price' => 42.00],
                    ['ar' => '10 كيلو','en' => '10kg', 'cost' => 54.00, 'price' => 80.00],
                ],
            ],
            [
                'ar' => 'دقيق قمح أبيض', 'en' => 'White Wheat Flour',
                'ar_d' => 'دقيق قمح أبيض ناعم مناسب للمخبوزات، كيس 2 كيلو',
                'en_d' => 'Fine white wheat flour suitable for baking, 2kg bag',
                'cat' => 'Grains & Rice', 'cost' => 7.00, 'price' => 11.00, 'vat' => 0,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $kgUnit, 'unit2' => null,
            ],
            [
                'ar' => 'شوفان سريع التحضير', 'en' => 'Quick Oats',
                'ar_d' => 'شوفان مدروس سريع التحضير غني بالألياف، 500 غرام',
                'en_d' => 'Rolled quick oats rich in fiber, 500g',
                'cat' => 'Grains & Rice', 'cost' => 9.00, 'price' => 14.00, 'vat' => 0,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],
            [
                'ar' => 'عدس أحمر مقشر', 'en' => 'Red Lentils',
                'ar_d' => 'عدس أحمر مقشر منظف جاهز للطهي، كيس 1 كيلو',
                'en_d' => 'Peeled and cleaned red lentils ready to cook, 1kg',
                'cat' => 'Grains & Rice', 'cost' => 8.00, 'price' => 13.00, 'vat' => 0,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $kgUnit, 'unit2' => null,
            ],

            // ---- زيوت وتوابل ----
            [
                'ar' => 'زيت زيتون بكر ممتاز', 'en' => 'Extra Virgin Olive Oil',
                'ar_d' => 'زيت زيتون بكر ممتاز عصر بارد، حجم 750 مل',
                'en_d' => 'Cold pressed extra virgin olive oil, 750ml',
                'cat' => 'Oils & Spices', 'cost' => 35.00, 'price' => 55.00, 'vat' => 15,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],
            [
                'ar' => 'زيت نخيل نقي', 'en' => 'Pure Palm Oil',
                'ar_d' => 'زيت نخيل صافي للقلي والطهي، حجم 1.5 لتر',
                'en_d' => 'Pure palm oil for frying and cooking, 1.5L',
                'cat' => 'Oils & Spices', 'cost' => 14.00, 'price' => 22.00, 'vat' => 15,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],
            [
                'ar' => 'بهارات مشكلة', 'en' => 'Mixed Spices',
                'ar_d' => 'خلطة بهارات عربية مشكلة لتتبيل اللحوم، 100 غرام',
                'en_d' => 'Mixed Arabic spices blend for meat seasoning, 100g',
                'cat' => 'Oils & Spices', 'cost' => 8.00, 'price' => 14.00, 'vat' => 15,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],
            [
                'ar' => 'ملح طعام بحري', 'en' => 'Sea Salt',
                'ar_d' => 'ملح بحري طبيعي خشن أو ناعم، وزن 500 غرام',
                'en_d' => 'Natural sea salt coarse or fine, 500g',
                'cat' => 'Oils & Spices', 'cost' => 3.00, 'price' => 5.50, 'vat' => 0,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],

            // ---- معلبات ومجففات ----
            [
                'ar' => 'تونة في زيت الزيتون', 'en' => 'Tuna in Olive Oil',
                'ar_d' => 'تونة مدمسة محفوظة في زيت زيتون، علبة 185 غرام',
                'en_d' => 'Canned tuna preserved in olive oil, 185g tin',
                'cat' => 'Canned & Dried', 'cost' => 9.00, 'price' => 14.50, 'vat' => 15,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],
            [
                'ar' => 'فاصوليا بيضاء معلبة', 'en' => 'Canned White Beans',
                'ar_d' => 'فاصوليا بيضاء مطبوخة محفوظة بصلصة الطماطم، 400 غرام',
                'en_d' => 'Cooked white beans in tomato sauce, 400g',
                'cat' => 'Canned & Dried', 'cost' => 5.00, 'price' => 8.00, 'vat' => 15,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],
            [
                'ar' => 'معجون طماطم مركز', 'en' => 'Tomato Paste',
                'ar_d' => 'معجون طماطم مركز 28-30% بدون مواد حافظة، 200 غرام',
                'en_d' => 'Concentrated tomato paste 28-30% no preservatives, 200g',
                'cat' => 'Canned & Dried', 'cost' => 4.00, 'price' => 6.50, 'vat' => 15,
                'type' => Product::TYPE_SALE, 'sizes' => false,
                'unit' => $pieceUnit, 'unit2' => $cartonUnit,
            ],
            [
                'ar' => 'تمر مجدول فاخر', 'en' => 'Premium Medjool Dates',
                'ar_d' => 'تمر مجدول طبيعي فاخر كبير الحجم من المدينة المنورة',
                'en_d' => 'Premium large natural Medjool dates from Madinah',
                'cat' => 'Canned & Dried', 'cost' => 40.00, 'price' => 65.00, 'vat' => 0,
                'type' => Product::TYPE_SALE, 'sizes' => true,
                'unit' => $kgUnit, 'unit2' => null,
                'size_list' => [
                    ['ar' => '500 غرام', 'en' => '500g', 'cost' => 22.00, 'price' => 35.00],
                    ['ar' => '1 كيلو',   'en' => '1kg',  'cost' => 40.00, 'price' => 65.00],
                    ['ar' => '3 كيلو',   'en' => '3kg',  'cost' => 115.00,'price' => 180.00],
                ],
            ],
        ];

        // ===== إدخال المنتجات =====
        foreach ($products as $p) {
            $catModel = Category::whereHas('translations', fn($q) => $q->where('name', $p['cat']))->first();

            $product = Product::create([
                'org_id'       => 1,
                'user_id'      => 1,
                'category_id'  => $catModel?->id ?? Category::first()->id,
                'barcode'      => Product::generateUniqueBarcode(),
                'min_quantity' => 5,
                'type'         => $p['type'],
                'cost_price'   => $p['cost'],
                'prod_price'   => $p['price'],
                'vat'          => $p['vat'],
                'status'       => Product::STATUS_ACTIVE,
                'have_sizes'   => $p['sizes'],
            ]);

            $product->translateOrNew('ar')->name    = $p['ar'];
            $product->translateOrNew('ar')->details = $p['ar_d'];
            $product->translateOrNew('en')->name    = $p['en'];
            $product->translateOrNew('en')->details = $p['en_d'];
            $product->save();

            // الوحدة الأساسية
            if ($p['unit']) {
                ProductUnit::create([
                    'product_id'        => $product->id,
                    'unit_id'           => $p['unit']->id,
                    'conversion_factor' => 1,
                    'is_base'           => true,
                ]);
            }

            // الوحدة الثانوية
            if (!empty($p['unit2'])) {
                ProductUnit::create([
                    'product_id'        => $product->id,
                    'unit_id'           => $p['unit2']->id,
                    'conversion_factor' => 12,
                    'is_base'           => false,
                ]);
            }

            // الأحجام
            if ($p['sizes'] && !empty($p['size_list'])) {
                foreach ($p['size_list'] as $s) {
                    $size = ProductSize::create([
                        'product_id' => $product->id,
                        'sale_price' => $s['price'],
                        'cost_price' => $s['cost'],
                        'barcode'    => ProductSize::generateUniqueBarcode(),
                        'status'     => ProductSize::STATUS_ACTIVE,
                    ]);
                    $size->translateOrNew('ar')->name = $s['ar'];
                    $size->translateOrNew('en')->name = $s['en'];
                    $size->save();
                }
            }
        }
    }
}
