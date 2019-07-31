<?php
/*
  Seed breadcrumbs names to categories
 */
use Illuminate\Database\Seeder;
use models\Categories;

class BreadcrumbsCategoriesSeeder extends Seeder
{
    public function run()
    {
        \DB::table('categories')->where('name', 'Сельскохозяйственные')->update(['breadcrumb' => 'Сельскохозяйственные шины']);
        \DB::table('categories')->where('name', 'Грузовые')->update(['breadcrumb' => 'Грузовые шины']);
        \DB::table('categories')->where('name', 'Легковые')->update(['breadcrumb' => 'Легковые шины']);
        \DB::table('categories')->where('name', 'Легкогрузовые')->update(['breadcrumb' => 'Легкогрузовые шины']);
        \DB::table('categories')->where('name', 'Индустриальные и КГШ')->update(['breadcrumb' => 'Индустриальные и КГШ']);
        \DB::table('categories')->where('name', 'Диски легковые')->update(['breadcrumb' => 'Диски легковые']);
        \DB::table('categories')->where('name', 'Диски грузовые')->update(['breadcrumb' => 'Диски грузовые']);
    }
}