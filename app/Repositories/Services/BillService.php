<?php

namespace App\Repositories\Services;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\BillInterface;
use App\Repositories\Interfaces\BillDetailInterface;
use App\Http\Resources\BillResource;

class BillService {
    
    protected $bill;

    protected $billDetail;

    public function __construct(BillInterface $bill, BillDetailInterface $billDetail){

        $this->bill = $bill;

        $this->billDetail = $billDetail;
    }

    public function create(Request $request){

        $attribute = $request->all();
        
        $bill = $this->bill->create($attribute);

        $billDetailAttributes = $request->bill_detail;

        for ($i = 0; $i < count($billDetailAttributes); $i++) {

            $billDetailAttributes[$i]['bill_id'] = $bill['id'];

            $this->billDetail->create($billDetailAttributes[$i]);
        }
        
        return new BillResource($bill);
    }

    public function getAll(){

        $allBills = $this->bill->getSortBill();

        for($i = 0; $i < $allBills->count(); $i++){

            $bills[$i] = new BillResource($allBills[$i]);
        }

        return $bills;
    }
    
    public function delete($id){

        $bill = $this->read($id);
        if($bill == null)
            {
                return response()->json(["error"=>"Couldn't find record"]);
            }

        $this->bill->delete($id);

        $bill = $this->read($id);
        if($bill==null)
        {
            return response()->json(["message"=>"Record deleted successfuly"]);
        }
    }
}
