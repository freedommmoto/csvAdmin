<?php

namespace App\Http\Controllers;

use App\Products;
use Illuminate\Http\Request;
use App\Events\MessagePosted;
use Auth;
use App\classes\CsvBasic;

class CsvController extends Controller
{

    private $columnsProduct = ['id', 'Name', 'Description', 'Price', 'Stock'];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // used middleware in Controller so no need to insert in route
        $this->middleware('auth');
    }

    /**
     * handel messages save to base and push to Vuejs
     *
     * @return status
     */
    public function csvUpload()
    {
        $csvData = request()->get('parse_csv');
        if (isset($csvData[0])) {
            if (count(array_intersect_key(array_flip($this->columnsProduct), $csvData[0])) !== count($this->columnsProduct)) {
                // All required keys not exist!
                return json_decode(['error' => 'keys not exist'], true);
            }
        } else {
            return json_decode(['error' => 'wrong data fotmat'], true);
        }

        if ($this->saveCsv($csvData)) {
            $user = Auth::user();
            $message = $user->messages()->create(['message' => 'upload done']);
            broadcast(new MessagePosted($message, $user))->toOthers();

            return $csvData;
        }
    }

    /**t
     * handel  insert / update Products
     *
     * @return boolean
     */
    private function saveCsv($csvData)
    {
        $numSave = 0;
        foreach ($csvData as $key => $csv) {
            $oldProduct = Products::find($csv['id']);
            if ($oldProduct === null) {
                //insert case
                $products = new Products;
            } else {
                //update case
                $products = $oldProduct;
            }
            $products->Name = $csv['Name'];
            $products->Description = $csv['Description'];
            $products->Price = $csv['Price'];
            $products->Stock = $csv['Stock'];
            if ($products->save()) {
                $numSave++;
            }
        }
        if (count($csvData) === $numSave) {
            return true;
        }
        return false;
    }

    /**
     * export data to csv file used basic csv class
     *
     * @return csv file
     */
    public function csvDownload()
    {
        $csvData = Products::all($this->columnsProduct)->toArray();
        $csv = new CsvBasic();
        return $csv->exportCSV($csvData, 'Products');
    }


}