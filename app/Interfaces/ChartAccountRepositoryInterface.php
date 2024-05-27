<?php 

namespace App\Interfaces;

interface ChartAccountRepositoryInterface 
{
    public function showChartAccountParticular($userId, $chartCode);
    public function showChartAccountType($chartAccountTypes);
    public function createOrUpdateChartAccountsInBulk($chartAccountParts);
    public function getChartAccountAndTypes();
    public function getChartAccountTypesAndParts($userId);
    public function showChartAccount();
    public function createChartAccountParts($particulars);
    public function showChartAccountParticularById($userId, $id);
    public function index($userId, $request);
}
?>