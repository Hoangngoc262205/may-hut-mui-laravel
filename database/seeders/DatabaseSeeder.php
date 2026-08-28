<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Demo accounts so the e-commerce flows can be tested immediately.
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Người dùng Demo',
                'password' => Hash::make('123456'),
                'phone' => '0900123456',
                'role' => 'customer',
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('123456'),
                'phone' => '0900000000',
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        $categories = [];
        foreach ([
            ['name' => 'Máy hút mùi kính cong', 'slug' => 'may-hut-mui-kinh-cong'],
            ['name' => 'Máy hút mùi âm tủ', 'slug' => 'may-hut-mui-am-tu'],
            ['name' => 'Máy hút mùi áp tường', 'slug' => 'may-hut-mui-ap-tuong'],
        ] as $item) {
            $categories[$item['slug']] = Category::updateOrCreate(
                ['slug' => $item['slug']],
                ['name' => $item['name'], 'description' => 'Thiết bị hút mùi cho căn bếp hiện đại.', 'status' => 'active']
            );
        }

        $brands = [];
        foreach ([
            ['name' => 'Kocher', 'slug' => 'kocher'],
            ['name' => 'Roller', 'slug' => 'roller'],
            ['name' => 'GROB', 'slug' => 'grob'],
        ] as $item) {
            $brands[$item['slug']] = Brand::updateOrCreate(
                ['slug' => $item['slug']],
                ['name' => $item['name'], 'description' => 'Thương hiệu demo cho bài tập thương mại điện tử.', 'status' => 'active']
            );
        }

        $sizes = [];
        foreach (['60 cm', '70 cm', '90 cm'] as $name) {
            $sizes[$name] = Size::firstOrCreate(['name' => $name], ['status' => 'active']);
        }

        $colors = [];
        foreach ([
            ['name' => 'Đen', 'hex_code' => '#111827'],
            ['name' => 'Inox', 'hex_code' => '#b8bec5'],
        ] as $item) {
            $colors[$item['name']] = Color::updateOrCreate(
                ['name' => $item['name']],
                ['hex_code' => $item['hex_code'], 'status' => 'active']
            );
        }

        $products = [
            [
                'slug' => 'kocher-kinh-cong-kc-90',
                'name' => 'Máy hút mùi Kocher KC-90 kính cong',
                'brand' => 'kocher',
                'category' => 'may-hut-mui-kinh-cong',
                'description' => 'Máy hút mùi kính cong thiết kế hiện đại, phù hợp bếp gia đình và không gian bếp tối giản.',
                'image' => 'images/products/kocher-glass.jpg',
                'hood_type' => 'Kính cong',
                'suction_capacity' => '750 m³/h',
                'noise_level' => '55 dB',
                'speed_levels' => '3 tốc độ',
                'voltage' => '220V / 50Hz',
                'material' => 'Kính cường lực + inox',
                'dimensions' => '700 / 900 mm',
                'warranty' => '24 tháng',
                'variants' => [
                    ['size' => '70 cm', 'color' => 'Đen', 'sku' => 'KCH-KC90-BLK-70', 'price' => 5290000, 'stock' => 18],
                    ['size' => '90 cm', 'color' => 'Inox', 'sku' => 'KCH-KC90-INOX-90', 'price' => 5990000, 'stock' => 12],
                ],
            ],
            [
                'slug' => 'roller-am-tu-rf-70',
                'name' => 'Máy hút mùi Roller RF-70 âm tủ',
                'brand' => 'roller',
                'category' => 'may-hut-mui-am-tu',
                'description' => 'Thiết kế âm tủ gọn gàng, phù hợp căn bếp nhỏ và phong cách nội thất hiện đại.',
                'image' => 'images/products/roller-built-in.jpg',
                'hood_type' => 'Âm tủ',
                'suction_capacity' => '650 m³/h',
                'noise_level' => '58 dB',
                'speed_levels' => '3 tốc độ',
                'voltage' => '220V / 50Hz',
                'material' => 'Inox',
                'dimensions' => '600 / 700 mm',
                'warranty' => '24 tháng',
                'variants' => [
                    ['size' => '60 cm', 'color' => 'Inox', 'sku' => 'RLR-RF70-INOX-60', 'price' => 4290000, 'stock' => 20],
                    ['size' => '70 cm', 'color' => 'Inox', 'sku' => 'RLR-RF70-INOX-70', 'price' => 4590000, 'stock' => 16],
                ],
            ],
            [
                'slug' => 'kocher-kinh-cong-kc-70-black',
                'name' => 'Máy hút mùi Kocher KC-70 Black',
                'brand' => 'kocher',
                'category' => 'may-hut-mui-kinh-cong',
                'description' => 'Phiên bản kính cong màu đen sang trọng, có đèn LED và lưới lọc nhiều lớp.',
                'image' => 'images/products/kocher-black-glass.jpg',
                'hood_type' => 'Kính cong',
                'suction_capacity' => '800 m³/h',
                'noise_level' => '54 dB',
                'speed_levels' => '3 tốc độ',
                'voltage' => '220V / 50Hz',
                'material' => 'Kính đen + inox',
                'dimensions' => '700 / 900 mm',
                'warranty' => '24 tháng',
                'variants' => [
                    ['size' => '70 cm', 'color' => 'Đen', 'sku' => 'KCH-KC70-BLK-70', 'price' => 4890000, 'stock' => 14],
                    ['size' => '90 cm', 'color' => 'Đen', 'sku' => 'KCH-KC70-BLK-90', 'price' => 5590000, 'stock' => 8],
                ],
            ],
            [
                'slug' => 'grob-am-tu-gb-70',
                'name' => 'Máy hút mùi GROB GB-70 âm tủ',
                'brand' => 'grob',
                'category' => 'may-hut-mui-am-tu',
                'description' => 'Máy hút mùi âm tủ kiểu dáng gọn, phù hợp bếp hiện đại và dễ vệ sinh.',
                'image' => 'images/products/grob-built-in.jpg',
                'hood_type' => 'Âm tủ',
                'suction_capacity' => '700 m³/h',
                'noise_level' => '56 dB',
                'speed_levels' => '3 tốc độ',
                'voltage' => '220V / 50Hz',
                'material' => 'Inox + kính',
                'dimensions' => '700 mm',
                'warranty' => '24 tháng',
                'variants' => [
                    ['size' => '70 cm', 'color' => 'Đen', 'sku' => 'GRB-GB70-BLK-70', 'price' => 5190000, 'stock' => 11],
                    ['size' => '70 cm', 'color' => 'Inox', 'sku' => 'GRB-GB70-INOX-70', 'price' => 4990000, 'stock' => 9],
                ],
            ],
        ];

        foreach ($products as $data) {
            $product = Product::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category_id' => $categories[$data['category']]->id,
                    'brand_id' => $brands[$data['brand']]->id,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'status' => 'active',
                ]
            );

            ProductDetail::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'suction_capacity' => $data['suction_capacity'],
                    'noise_level' => $data['noise_level'],
                    'speed_levels' => $data['speed_levels'],
                    'voltage' => $data['voltage'],
                    'material' => $data['material'],
                    'hood_type' => $data['hood_type'],
                    'dimensions' => $data['dimensions'],
                    'warranty' => $data['warranty'],
                ]
            );

            // Keep one clean primary image for each demo product.
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'image' => $data['image']],
                ['product_variant_id' => null, 'is_primary' => true, 'sort_order' => 0]
            );

            foreach ($data['variants'] as $variantData) {
                $variant = ProductVariant::updateOrCreate(
                    ['sku' => $variantData['sku']],
                    [
                        'product_id' => $product->id,
                        'size_id' => $sizes[$variantData['size']]->id,
                        'color_id' => $colors[$variantData['color']]->id,
                        'price' => $variantData['price'],
                        'stock' => $variantData['stock'],
                        'image' => $data['image'],
                        'status' => 'active',
                    ]
                );

                // Link the product image to the variant as well so the detail page can swap images.
                ProductImage::updateOrCreate(
                    ['product_id' => $product->id, 'product_variant_id' => $variant->id, 'image' => $data['image']],
                    ['is_primary' => false, 'sort_order' => 1]
                );
            }
        }
    }
}
