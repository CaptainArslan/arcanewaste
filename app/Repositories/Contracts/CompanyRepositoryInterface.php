<?php

namespace App\repositories\Contracts;

interface CompanyRepositoryInterface
{
    public function getAllCompanies();

    public function getCompanyById($id);

    public function createCompany(array $data);

    public function updateCompany(array $data, $id);

    public function deleteCompany($id);

    public function searchCompanies($query);
}
