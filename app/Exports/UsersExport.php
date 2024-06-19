<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Client;
use App\Models\Dealer;
use App\Models\SubDealer;
class UsersExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $dealers = Dealer::with('user')->get()->map(function($dealer) {
            return [
                'id' => $dealer->id,
                'uid' => $dealer->uid,
                 'empname' => $dealer->user->name,
                'name' => $dealer->dealerName,  // Ensure name is included
                'categoery_id' =>'0',
                   'emp_code' => $dealer->emp_code,
                'company_id' => $dealer->company_id,
               
            ];
        });
      $clients = Client::with('user')->get()->map(function($client) {
            return [
                'id' => $client->id,
                'uid' => $client->uid,
                 'empname' => $client->user->name,
                'name' => $client->clientName,  // Assuming name is in the related user
                'emp_code' => $client->emp_code,
                 'company_id' => $client->company_id,
              'categoery_id' => $client->categoery_id,
            ];
        });
       $sub_dealers = SubDealer::with('user')->get()->map(function($subDealer) {
            return [
                'id' => $subDealer->id,
                'uid' => $subDealer->uid,
                'empname' => $subDealer->user->name,
                'name' => $subDealer->name,
                'emp_code' => $subDealer->emp_code,
                'company_id' => $subDealer->company_id,
                'categoery_id' => '1',
            ];
        });
       
        return User::get();
    }
    public function headings(): array
    {
        return ["sl.nO", "firm name", "category","empcode","empName",'January','february','march','april','may','june','july','august','septemer','octomber','novmber','december'];
    }
}
