<?php

class InventoryController extends BaseController {

    public function getSequence($num) {
        return sprintf("%'.19d\n", $num);
    }

    public function showInsertInventory3() { #sim
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = Input::file('sample_file');
            if ($input != '') {
                if (Input::hasFile('sample_file')) {
                    $destination = base_path() . '/uploaded_file/';
                    $extention = Input::file('sample_file')->getClientOriginalExtension();
                    $filename = 'tempbase.' . $extention;
                    Input::file('sample_file')->move($destination, $filename);
                    $data = Excel::load(base_path() . '/uploaded_file/' . 'tempbase.' . $extention, function($reader) {
                                
                            })->formatDates(true)->get();
                    $SerialNumber = '';
                    $counterfail = 0;
                    $counter = 1;
                    $date = Input::get('eventDate');
                    if (!empty($data) && $data->count()) {
                        foreach ($data as $key => $value) {
                            foreach ($value as $key => $value) {
                                $subagent = '';
                                $outprice = 0;
                                $status = 2;
                                $tempSN = '/SO/';
                                $tempSA = '';
                                $SerialNumber = sprintf("%'019d", $counter);
                                if ($value->serial_number != null) {
                                    $SerialNumber = $value->serial_number;
                                }
                                if ($value->consignment != null) {
                                    $status = 4;
                                    $tempSN = '/CO/';
                                }
                                $type = 1;
                                $inv = Inventory::where('SerialNumber', $SerialNumber)->first();
                                if ($inv == null) {
                                    if (substr($value->msisdn, 0, 4) == '0908') {
                                        $type = 4;
                                    }
                                    $insertInventory = ['SerialNumber' => $SerialNumber, 'Price' => $value->ship_in_price, 'MSISDN' => $value->msisdn,
                                        'Type' => $type, 'LastWarehouse' => $value->warehouse, 'Remark' => $value->remark, 'userRecord' => Auth::user()->ID, 'Provider' => 'TAIWAN STAR'];
                                    $counter++;
                                    $shipinNumber = $value->shipin_date . '/SI/TST001';
                                    if (!empty($insertInventory)) {
                                        $insertnya = DB::table('m_inventory')->insertGetId($insertInventory);
                                    }

                                    $inv = Inventory::where('SerialNumber', $SerialNumber)->first();
                                    //insert history
                                    $insertHistory = ['SN' => $SerialNumber, 'Price' => $value->ship_in_price, 'Date' => $value->shipin_date, 'Remark' => $value->remark, 'ShipoutNumber' => $shipinNumber];
                                    if (!empty($insertHistory)) {
                                        $lasthistoryID = DB::table('m_historymovement')->insertGetId($insertHistory);
                                    }

                                    //insert shipout
                                    if ($value->shipout_date != null) {
                                        $statusnum = $value->shipout_date . $tempSN;
                                        $subagent = $value->shipout_to;
                                        $temp_sub = $value->sub_agent;
                                        $temp_sub2 = explode(' ', $temp_sub)[0];
                                        if (strtolower($subagent) != strtolower($temp_sub2)) {
                                            $subagent .= ' ' . $temp_sub;
                                        } else {
                                            $subagent .= ' ' . explode(' ', $temp_sub)[1];
                                        }

                                        if (strtolower(explode(' ', $subagent)[0]) == 'asprof') {
                                            $tempSA = 'ASF';
                                        } else if (strtolower(explode(' ', $subagent)[0]) == 'asprot') {
                                            $tempSA = 'AST';
                                        } else {
                                            $tempSA = substr(explode(' ', $subagent)[0], 0, 3);
                                        }
                                        if ($value->ship_out_price != null) {
                                            $outprice = $value->ship_out_price;
                                        }
                                        $statusnum .= $tempSA;
                                        $statusnum .= '001';
                                        $insertHistory = ['SN' => $SerialNumber, 'Warehouse' => $value->warehouse, 'Status' => $status, 'Price' => $outprice,
                                            'Date' => $value->shipout_date, 'Remark' => $value->remark, 'SubAgent' => $subagent, 'ShipoutNumber' => $statusnum];
                                        if (!empty($insertHistory)) {
                                            $lasthistoryID = DB::table('m_historymovement')->insertGetId($insertHistory);
                                        }
                                    }

                                    $inv->LastStatusID = $lasthistoryID;
                                    $inv->save();
                                }
                            }
                        }
                    }
                    return View::make('insertinventory')->withResponse('Success')->withPage('insert inventory')->withNumber($counter)->withNumberf($counterfail);
                }
            }
            return View::make('insertinventory')->withResponse('Failed')->withPage('insert inventory');
        }
        return View::make('insertinventory')->withPage('insert inventory');
    }

    public function showInsertInventory2() { #vocher
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = Input::file('sample_file');
            if ($input != '') {
                if (Input::hasFile('sample_file')) {
                    $destination = base_path() . '/uploaded_file/';
                    $extention = Input::file('sample_file')->getClientOriginalExtension();
                    $filename = 'tempbase.' . $extention;
                    Input::file('sample_file')->move($destination, $filename);
                    $data = Excel::load(base_path() . '/uploaded_file/' . 'tempbase.' . $extention, function($reader) {
                                
                            })->formatDates(true)->get();
                    $SerialNumber = '';
                    $counterfail = 0;
                    $counter = 1;
                    $date = Input::get('eventDate');
                    if (!empty($data) && $data->count()) {
                        foreach ($data as $key => $value) {
                            $subagent = '';
                            $outprice = 0;
                            $status = 2;
                            $tempSN = '/SO/';
                            $tempSA = '';
                            $SerialNumber = sprintf("%'019d", $counter);
                            if ($value->serial_number != null) {
                                $SerialNumber = $value->serial_number;
                            }
                            if ($value->consignment != null) {
                                $status = 4;
                                $tempSN = '/CO/';
                            }
                            $type = 2;
                            $inv = Inventory::where('SerialNumber', $SerialNumber)->first();
                            if ($inv == null) {
                                if (strtolower($value->type_voucher) == 'physical') {
                                    $type = 3;
                                }
                                $insertInventory = ['SerialNumber' => $SerialNumber, 'Price' => $value->ship_in_price,
                                    'Type' => $type, 'LastWarehouse' => $value->warehouse, 'Remark' => $value->remark, 'userRecord' => Auth::user()->ID, 'Provider' => 'TAIWAN STAR'];
                                $counter++;
                                $shipinNumber = $value->ship_in_date . '/SI/TST001';
                                if (!empty($insertInventory)) {
                                    $insertnya = DB::table('m_inventory')->insertGetId($insertInventory);
                                }

                                $inv = Inventory::where('SerialNumber', $SerialNumber)->first();
                                //insert history
                                $insertHistory = ['SN' => $SerialNumber, 'Price' => $value->ship_in_price, 'Date' => $value->ship_in_date, 'Remark' => $value->remark, 'ShipoutNumber' => $shipinNumber];
                                if (!empty($insertHistory)) {
                                    $lasthistoryID = DB::table('m_historymovement')->insertGetId($insertHistory);
                                }

                                //insert shipout
                                if ($value->ship_out_date != null) {
                                    $statusnum = $value->ship_out_date . $tempSN;
                                    $subagent = $value->ship_out_to;
                                    $temp_sub = $value->sub_agent;
                                    $temp_sub2 = explode(' ', $temp_sub)[0];
                                    if (strtolower($subagent) != strtolower($temp_sub2)) {
                                        $subagent .= ' ' . $temp_sub;
                                    } else {
                                        $subagent .= ' ' . explode(' ', $temp_sub)[1];
                                    }

                                    if (strtolower(explode(' ', $subagent)[0]) == 'asprof') {
                                        $tempSA = 'ASF';
                                    } else if (strtolower(explode(' ', $subagent)[0]) == 'asprot') {
                                        $tempSA = 'AST';
                                    } else {
                                        $tempSA = substr(explode(' ', $subagent)[0], 0, 3);
                                    }
                                    if ($value->ship_out_price != null) {
                                        $outprice = $value->ship_out_price;
                                    }
                                    $statusnum .= $tempSA;
                                    $statusnum .= '001';
                                    $insertHistory = ['SN' => $SerialNumber, 'Warehouse' => $value->warehouse, 'Status' => $status, 'Price' => $outprice,
                                        'Date' => $value->ship_out_date, 'Remark' => $value->remark, 'SubAgent' => $subagent, 'ShipoutNumber' => $statusnum];
                                    if (!empty($insertHistory)) {
                                        $lasthistoryID = DB::table('m_historymovement')->insertGetId($insertHistory);
                                    }
                                }

                                $inv->LastStatusID = $lasthistoryID;
                                $inv->save();
                            }
                        }
                    }
                    return View::make('insertinventory')->withResponse('Success')->withPage('insert inventory')->withNumber($counter)->withNumberf($counterfail);
                }
            }
            return View::make('insertinventory')->withResponse('Failed')->withPage('insert inventory');
        }
        return View::make('insertinventory')->withPage('insert inventory');
    }

    public function showInsertInventory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = Input::file('sample_file');
            if ($input != '') {
                if (Input::hasFile('sample_file')) {
                    $destination = base_path() . '/uploaded_file/';
                    $extention = Input::file('sample_file')->getClientOriginalExtension();
                    $filename = 'temp.' . $extention;
                    Input::file('sample_file')->move($destination, $filename);
                    $data = Excel::load(base_path() . '/uploaded_file/' . 'temp.' . $extention, function($reader) {
                                
                            })->get();
                    $SerialNumber = "";
                    $counter = 0;
                    $counterfail = 0;
//                    $date = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('Asia/Taipei'));
                    $date = Input::get('eventDate');
                    $wh = Input::get('warehouse');
                    $formSN = Input::get('formSN');
                    $remark = Input::get('remark');
                    if (!empty($data) && $data->count()) {
                        foreach ($data as $key => $value) {
                            if ($value->serial_number != null) {
                                $inv = Inventory::where('SerialNumber', $value->serial_number)->first();
                                if ($inv == null) {
                                    $type = 1;
                                    if ($value->msisdn == null) {
                                        $type = 2;
                                    }
                                    $insertInventory = ['SerialNumber' => $value->serial_number, 'MSISDN' => $value->msisdn, 'LastWarehouse' => $wh,
                                        'Type' => $type, 'Remark' => $remark, 'userRecord' => Auth::user()->ID];
                                    $counter++;
                                    if (!empty($insertInventory)) {
                                        $SerialNumber = DB::table('m_inventory')->insertGetId($insertInventory);
                                    }

                                    $inv = Inventory::where('SerialNumber', $value->serial_number)->first();

                                    //insert history
                                    $insertHistory = ['SN' => $value->serial_number, 'Warehouse' => $wh, 'Date' => $date, 'Remark' => $remark, 'ShipoutNumber' => $formSN];
                                    if (!empty($insertHistory)) {
                                        $lasthistoryID = DB::table('m_historymovement')->insertGetId($insertHistory);
                                    }

                                    $inv->LastStatusID = $lasthistoryID;
                                    $inv->save();
                                } else {
                                    $counterfail++;
                                }
                            }
                        }
                    }
                    return View::make('insertinventory')->withResponse('Success')->withPage('insert inventory')->withNumber($counter)->withNumberf($counterfail);
                }
            }
            return View::make('insertinventory')->withResponse('Failed')->withPage('insert inventory');
        }
        return View::make('insertinventory')->withPage('insert inventory');
    }
    
    public function showDashboard(){
        return View::make('dashboard')->withPage('dashboard');
    }

    public function showWarehouseInventory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstsn = Input::get('shipoutstart');
            $lastsn = Input::get('shipoutend');
            $moveto = Input::get('moveto');
            $newwh = Input::get('newwh');
            if ($newwh != '') {
                $moveto = $newwh;
            }
            $counter = 0;
            $allInvAvail = Inventory::whereBetween('SerialNumber', [$firstsn, $lastsn])->get();
            foreach ($allInvAvail as $inv) {
                $history = History::where('ID', $inv['LastStatusID'])->first();
                $checktru = true;
                if ($history->Status == 3 && $history->Warehouse == $moveto) {
                    $checktru = false;
                }
                if ($history->Status != 2 && $checktru) { //available
                    $hist = new History();
                    $hist->SN = $inv->SerialNumber;
                    $hist->Warehouse = $moveto;
                    $hist->Status = 3;
                    $hist->Remark = Input::get('remark');
                    $hist->userRecord = Auth::user()->ID;
                    $hist->save();

                    //update last status
                    $inv->LastStatusID = $hist->ID;
                    $inv->LastWarehouse = $moveto;
                    $inv->save();
                    $counter++;
                }
            }
            return View::make('warehouse')->withPage('inventory warehouse')->withNumber($counter);
        }
        return View::make('warehouse')->withPage('inventory warehouse');
    }

    public function getSubAgent() {
        $shipto = Input::get('ship');
        return History::where('SubAgent', 'like', '%' . $shipto . '%')->select('SubAgent')->distinct()->get();
    }

    public function showInventoryShipout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstsn = Input::get('shipoutstart');
            $lastsn = Input::get('shipoutend');
            $price = Input::get('price');
            $series = Input::get('formSN');
            $subagent = Input::get('subagent');

            if (Input::get('newagent') != '') {
                $subagent = Input::get('newagent');
            }
            $counter = 0;
            $allInvAvail = Inventory::whereBetween('SerialNumber', [$firstsn, $lastsn])->where('Missing', 0)->get();
            foreach ($allInvAvail as $inv) {
                $history = History::where('ID', $inv['LastStatusID'])->first();
                if ($history->Status != 2) { //available
                    $hist = new History();
                    $hist->SN = $inv->SerialNumber;
                    $hist->SubAgent = $subagent;
                    $hist->Price = $price;
                    if ($price == '0') {
                        $hist->Warehouse = 'TELIN TAIWAN';
                        $inv->LastWarehouse = 'TELIN TAIWAN';
                    }
                    $hist->ShipoutNumber = $series;
                    $hist->Status = 2;
                    $hist->Remark = Input::get('remark');
                    $hist->Date = Input::get('eventDate');
                    $hist->userRecord = Auth::user()->ID;
                    $hist->save();

                    //update last status
                    $inv->LastStatusID = $hist->ID;
                    $inv->Missing = 1;
                    $inv->save();
                    $counter++;
                }
            }
            return View::make('shipout')->withResponse('Success')->withPage('inventory shipout')->withNumber($counter);
        }
        return View::make('shipout')->withPage('inventory shipout');
    }

    public function showConsignment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstsn = Input::get('shipoutstart');
            $lastsn = Input::get('shipoutend');
            $price = Input::get('price');
            $series = Input::get('formSN');
            $subagent = Input::get('subagent');
            if (Input::get('newagent') != '') {
                $subagent = Input::get('newagent');
            }
            $counter = 0;
            $allInvAvail = Inventory::whereBetween('SerialNumber', [$firstsn, $lastsn])->where('Missing', 0)->get();
            foreach ($allInvAvail as $inv) {
                $history = History::where('ID', $inv['LastStatusID'])->first();
                if ($history->Status != 2) { //available
                    $hist = new History();
                    $hist->SN = $inv->SerialNumber;
                    $hist->SubAgent = $subagent;
                    $hist->Price = $price;
                    $hist->ShipoutNumber = $series;
                    $hist->Status = 2;
                    $hist->Remark = Input::get('remark');
                    $hist->Date = Input::get('eventDate');
                    $hist->userRecord = Auth::user()->ID;
                    $hist->save();

                    //update last status
                    $inv->LastStatusID = $hist->ID;
                    $inv->Missing = 1;
                    $inv->save();
                    $counter++;
                }
            }
            return View::make('shipout')->withResponse('Success')->withPage('inventory shipout')->withNumber($counter);
        }
        return View::make('consignment')->withPage('consignment');
    }

    public function showReturnInventory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $destination = base_path() . '/uploaded_file/';
            $extention = Input::file('sample_file')->getClientOriginalExtension();
            $filename = 'tempreturn.' . $extention;
            Input::file('sample_file')->move($destination, $filename);
            $data = Excel::load(base_path() . '/uploaded_file/' . 'tempreturn.' . $extention, function($reader) {
                        
                    })->get();
            $SerialNumber = "";
            $counter = 0;
            $counterfail = 0;
            $notavail = '';
            $nodata = '';
            $successins = '';
//                    $date = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('Asia/Taipei'));
            $date = Input::get('eventDate');
            $remark = Input::get('remark');
            if (!empty($data) && $data->count()) {
                foreach ($data as $key => $value) {
                    if ($value->id != null) {
                        $inv = Inventory::where('SerialNumber', $value->id)->first();
                        if ($inv != null) {
                            $inv = Inventory::where('SerialNumber', $value->id)->first();
                            $lastmovement = $inv->LastStatusID;
                            //update history
                            $hist = History::where('ID', $lastmovement)->first();
                            if ($hist->Status == 2) {
                                $counter++;
                                if ($notavail == '') {
                                    $successins .= $value->id;
                                } else {
                                    $successins .= $value->id . ', ';
                                }

                                //can return, update
                                $insertHistory = ['SN' => $value->id, 'Date' => $date, 'Remark' => $remark, 'Status' => 1];
                                if (!empty($insertHistory)) {
                                    $lasthistoryID = DB::table('m_historymovement')->insertGetId($insertHistory);
                                }

                                $inv->LastStatusID = $lasthistoryID;
                                $inv->save();
                            } else {
                                if ($notavail == '') {
                                    $notavail .= $value->id;
                                } else {
                                    $notavail .= $value->id . ', ';
                                }
                                $counterfail++;
                            }
                        } else {
                            $counterfail++;
                            if ($nodata == '') {
                                $nodata .= $value->id;
                            } else {
                                $nodata .= $value->id . ', ';
                            }
                        }
                    }
                }
            }
            return View::make('returninventory')->withResponse('Success')->withPage('inventory return')
                            ->withNumber($counter)->withNumberf($counterfail)->withFail($nodata)->withSucc($successins)->withNoav($notavail);
        }
        return View::make('returninventory')->withPage('inventory return');
    }

    public function showChange() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (Input::get('jenis') == 'agent') {
                $oldname = Input::get('OldName');
                $newname = Input::get('NewName');
                $shipto = explode(' ', $oldname)[0];
                $olddata = History::where('SubAgent', $oldname)->get();
                $counter = 0;
                foreach ($olddata as $data) {
                    $data->SubAgent = $shipto . " " . $newname;
                    $data->save();
                    $counter++;
                }
                return View::make('change')->withPage('edit name')->withNumber($counter);
            }
            if (Input::get('jenis') == 'warehouse') {
                $oldname = Input::get('OldName2');
                $newname = Input::get('NewName2');
                $olddata = History::where('Warehouse', $oldname)->get();
                $oldinv = Inventory::where('LastWarehouse', $oldname)->get();
                $counter = 0;
                foreach ($olddata as $data) {
                    $data->Warehouse = $newname;
                    $data->save();
                }
                foreach ($oldinv as $data) {
                    $data->LastWarehouse = $newname;
                    $data->save();
                    $counter++;
                }
                return View::make('change')->withPage('edit name')->withNumberw($counter);
            }
        }
        return View::make('change')->withPage('edit name');
    }

    public function showInventory() {
        return View::make('inventory')->withPage('inventory');
    }

    //============================ajax===============================

    static function postMissing() {
        $sn = Input::get('sn');
        $inv = Inventory::find($sn);
        $inv->Missing = 1;
        $inv->save();
    }

    static function postNewAgent() {
        Session::put('NewAgent', Input::get('agent'));
    }

    static function postFormSeries() {
        Session::put('FormSeries', Input::get('fs'));
        Session::put('FormSeriesInv', Input::get('fs'));
    }

    static function exportExcel($filter) {
        $excel = new ExcelWriter("telkom_inventory.xls");
        if ($excel == false)
            echo $excel->error;

        $myArr = array("SERIAL NUMBER", "MSISDN", "TYPE", "LAST STATUS", "SHIPOUT TO", "SUBAGENT", "FORM SERIES", "LAST WAREHOUSE", "SHIPOUT DATE", "SHIPOUT PRICE", "SHIPIN DATE", "SHIPIN PRICE", "REMARK");
        $excel->writeLine($myArr);
        $filter = explode(',,,', $filter);
        $typesym = '>=';
        $type = '0';
        $statussym = '>=';
        $status = '0';
        $fs = '';
        if (Session::has('FormSeriesInv'))
            $fs = Session::get('FormSeriesInv');
        if ($filter[0] != 'all') {
            $typesym = '=';
            $type = $filter[0];
        }
        if (isset($filter[1])) {
            $statussym = '=';
            $status = $filter[1];
        }

        $invs = DB::table('m_inventory')
                ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                ->where('m_inventory.Type', $typesym, $type)
                ->where('m_historymovement.Status', $statussym, $status)
                ->where('m_historymovement.ShipoutNumber', 'like', '%' . $fs . '%')
                ->get();

        foreach ($invs as $inv) {
            $type = 'SIM 3G';
            if ($inv->Type == 2) {
                $type = 'eVoucher';
            } else if ($inv->Type == 3) {
                $type = 'phVoucher';
            } else if ($inv->Type == 4) {
                $type = 'SIM 4G';
            }

            $hist = History::where('ID', $inv->LastStatusID)->orderBy('ID', 'DESC')->first();
            $status = 'Available';
            $cons = 'no';
            $shipoutdt = '';
            $shipoutprice = '0';
            $histshipin = History::where('SN', $inv->SerialNumber)->where('Status', '0')->first();
            $shipindt = $histshipin->Date;
            if ($hist->Status == 1) {
                $status = 'Return';
            } else if ($hist->Status == 2) {
                $status = 'Shipout';
                $shipoutdt = $hist->Date;
                $shipoutprice = $hist->Price;
            } else if ($hist->Status == 3) {
                $status = 'Warehouse';
            } else if ($hist->Status == 4) {
                $status = 'Consignment';
            }

            $shipout = '';
            $subagent = '';
            $tempcount = 0;
            if ($hist->SubAgent != '') {
                $shipout = explode(' ', $hist->SubAgent);
                foreach ($shipout as $word) {
                    if ($tempcount > 0) {
                        $subagent .= $word . ' ';
                    }
                    $tempcount++;
                }
            }

            $myArr = array($inv->SerialNumber, $inv->MSISDN, $type, $status, $shipout[0], $subagent, $hist->ShipoutNumber, $inv->LastWarehouse, $shipoutdt, $shipoutprice, $shipindt, $inv->Price, $hist->Remark);
            $excel->writeLine($myArr);
        }
        $excel->close();
    }

    static function getPDFShipout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sn = Input::get('sn');
            $date = Input::get('date');
            $to = Input::get('to');
            $subagent = Input::get('subagent');
            $start = Input::get('start');
            $end = Input::get('end');
            $price = Input::get('price');

            Session::put('sn', $sn);
            Session::put('date', $date);
            Session::put('subagent', $subagent);
            Session::put('to', $to);
            Session::put('start', $start);
            Session::put('end', $end);
            Session::put('price', $price);

            return 'success';
        }
        $type = ['', '', '', ''];
        $count = ['', '', '', ''];
        $first = ['', '', '', ''];
        $last = ['', '', '', ''];
        $temp_count = 0;
        $subtotal = 0;
        $alltype = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->where('m_inventory.SerialNumber', '>=', Session::get('start'))->where('m_inventory.SerialNumber', '<=', Session::get('end'))
                        ->where('m_historymovement.Status', '!=', '2')
                        ->where('m_inventory.Missing', '0')
                        ->select('m_inventory.Type')
                        ->distinct()->get();
        if ($alltype != null) {
            foreach ($alltype as $types) {
                if ($types->Type == '1') {
                    $type[$temp_count] = 'SIM 3G';
                    $counters = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.SerialNumber', '>=', Session::get('start'))->where('m_inventory.SerialNumber', '<=', Session::get('end'))
                            ->where('m_historymovement.Status', '!=', '2')
                            ->where('m_inventory.Missing', '0')
                            ->where('m_inventory.Type', '1')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.SerialNumber', '>=', Session::get('start'))->where('m_inventory.SerialNumber', '<=', Session::get('end'))
                            ->where('m_historymovement.Status', '!=', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '1')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'asc')
                            ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.SerialNumber', '>=', Session::get('start'))->where('m_inventory.SerialNumber', '<=', Session::get('end'))
                            ->where('m_historymovement.Status', '!=', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '1')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'desc')
                            ->first();
                    $last[$temp_count] = $lastid->SerialNumber;
                    $temp_count++;
                } else if ($types->Type == '2') {
                    $type[$temp_count] = 'E-VOUCHER';
                    $counters = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.SerialNumber', '>=', Session::get('start'))->where('m_inventory.SerialNumber', '<=', Session::get('end'))
                            ->where('m_historymovement.Status', '!=', '2')
                            ->where('m_inventory.Missing', '0')
                            ->where('m_inventory.Type', '2')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.SerialNumber', '>=', Session::get('start'))->where('m_inventory.SerialNumber', '<=', Session::get('end'))
                            ->where('m_historymovement.Status', '!=', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '2')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'asc')
                            ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.SerialNumber', '>=', Session::get('start'))->where('m_inventory.SerialNumber', '<=', Session::get('end'))
                            ->where('m_historymovement.Status', '!=', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '2')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'desc')
                            ->first();
                    $last[$temp_count] = $lastid->SerialNumber;
                    $temp_count++;
                } else if ($types->Type == '3') {
                    $type[$temp_count] = 'PH-VOUCHER';
                    $counters = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.SerialNumber', '>=', Session::get('start'))->where('m_inventory.SerialNumber', '<=', Session::get('end'))
                            ->where('m_historymovement.Status', '!=', '2')
                            ->where('m_inventory.Missing', '0')
                            ->where('m_inventory.Type', '3')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.SerialNumber', '>=', Session::get('start'))->where('m_inventory.SerialNumber', '<=', Session::get('end'))
                            ->where('m_historymovement.Status', '!=', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '2')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'asc')
                            ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.SerialNumber', '>=', Session::get('start'))->where('m_inventory.SerialNumber', '<=', Session::get('end'))
                            ->where('m_historymovement.Status', '!=', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '3')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'desc')
                            ->first();
                    $last[$temp_count] = $lastid->SerialNumber;
                    $temp_count++;
                } else if ($types->Type == '4') {
                    $type[$temp_count] = 'SIM 4G';
                    $counters = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.SerialNumber', '>=', Session::get('start'))->where('m_inventory.SerialNumber', '<=', Session::get('end'))
                            ->where('m_historymovement.Status', '!=', '2')
                            ->where('m_inventory.Missing', '0')
                            ->where('m_inventory.Type', '4')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.SerialNumber', '>=', Session::get('start'))->where('m_inventory.SerialNumber', '<=', Session::get('end'))
                            ->where('m_historymovement.Status', '!=', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '4')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'asc')
                            ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.SerialNumber', '>=', Session::get('start'))->where('m_inventory.SerialNumber', '<=', Session::get('end'))
                            ->where('m_historymovement.Status', '!=', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '4')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'desc')
                            ->first();
                    $last[$temp_count] = $lastid->SerialNumber;
                    $temp_count++;
                }
            }
        }
        $html = '
            <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    <style>
                        @font-face {
                            font-family:traditional;
                            src:url("public/fonts/traditional.ttf");
                        }
                        body{
                            font-size: 12px;
                            font-family:traditional;
                            padding-top: -1cm;
                        }
                        p{
                            font-size: 90%;
                            line-height: 0.3;
                            font-family:traditional;
                        }
                    </style>
                </head>
                <body>
                    <div style="width:102%; height:93px; border-style: solid;border-width: 2px;">
                        <div style="width:91px;padding-top:1px; float:left; display: inline-block;"><img src="' . base_path() . '/uploaded_file/as.jpg" style="width: 100%;"></div>
                        <div style="width:500px; float:left; text-align:center; display: inline-block; padding-top:3px;">
                            <p>台灣紅白電訊股份有限公司</p>
                            <p>Telekomunikasi Indonesia International (Taiwan) Limited</p>
                            <p>114 台北市內湖區洲子街77號7樓之1</p>
                            <p>Tel: +886 (02) 87525071, Fax: +886 (02) 87523619</p>
                        </div>
                        <div style="width:91px;padding-top:1px; float:left; display: inline-block; "><img src="' . base_path() . '/uploaded_file/telin.jpg" style="width: 100%;"></div>
                    </div>
                    <div style="width:102%; height:30px; text-align:center;">
                        <p style="font-size:120%;">銷貨單</p>
                    </div>
                    <div style="width:101.6%; padding-left:3px;height:20px; border-left: 1px solid; border-top: 1px solid; border-right: 1px solid;">
                        訂單日期：' . Session::get('date') . '
                    </div>
                    <div style="width:101.6%; padding-left:3px;height:20px; border-left: 1px solid;  border-right: 1px solid;">
                        訂單編號：' . Session::get('sn') . '
                    </div>
                    <div style="width:101.6%; padding-left:3px;height:20px;border-left: 1px solid; border-bottom: 1px solid; border-right: 1px solid;">
                        客戶編號：' . Session::get('to') . '
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid; border-top: 1px solid; border-right: 1px solid;">
                        <div style="width:70px;padding-left:3px height:20px;float:left; display: inline-block;">客戶名稱 ：</div>
                        <div style="width:430px; height:20px;float:left; display: inline-block;">' . Session::get('subagent') . '</div>
                        <div style="width:200px; height:20px;float:left; display: inline-block;">統一編號: </div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid;">
                        <div style="width:70px;padding-left:3px height:20px;float:left; display: inline-block; ">送貨地址 ：</div>
                        <div style="width:430px; height:20px;float:left; display: inline-block;"></div>
                        <div style="width:200px; height:20px;float:left; display: inline-block;"></div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:80px;padding-left:3px height:20px;float:left; display: inline-block; ">發票號碼 ： </div>
                        <div style="width:420px; height:20px;float:left; display: inline-block;"></div>
                        <div style="width:200px; height:20px;float:left; display: inline-block;">倉 庫 別: </div>
                    </div>
                    <div style="width:102%; text-align:center;height:20px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:100px; height:20px;float:left; display: inline-block; border-right: 1px solid;">產品編號</div>
                        <div style="width:300px; height:20px;float:left; display: inline-block; border-right: 1px solid;">產品名稱</div>
                        <div style="width:70px; height:20px;float:left; display: inline-block; border-right: 1px solid;">數 量</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">訂價/單價</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">合計</div>
                    </div>';
        for ($i = 0; $i < count($type); $i++) {
            if ($type[$i] != '') {
                $subtotal += (Session::get('price')*$count[$i]);
                $html .= '<div style="width:102%; height:15px; border-left: 1px solid;  border-right: 1px solid;">
                        <div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:300px; height:15px;float:left; display: inline-block; border-right: 1px solid;">' . $type[$i] . '</div>
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;">' . $count[$i] . '</div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;">NT$ '.Session::get('price').'</div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;">NT$ '.(Session::get('price')*$count[$i]).'</div>
                    </div>
                    <div style="width:102%; height:15px; padding-top:-2px; border-left: 1px solid;  border-right: 1px solid; ';
            } else {
                $html .= '<div style="width:102%; height:15px; border-left: 1px solid;  border-right: 1px solid;">
                        <div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:300px; height:15px;float:left; display: inline-block; border-right: 1px solid;">' . $type[$i] . '</div>
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;">' . $count[$i] . '</div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;"></div>
                    </div>
                    <div style="width:102%; height:15px; padding-top:-2px; border-left: 1px solid;  border-right: 1px solid; ';
            }
            if ($i == count($type) - 1)
                $html .= 'border-bottom: 1px solid;';
            if ($type[$i] != '') {
                $html .= '">
                        <div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:300px; height:15px;float:left; display: inline-block; border-right: 1px solid;">' . $first[$i] . ' - ' . $last[$i] . '</div>
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;"></div>
                    </div>';
            } else {
                $html .= '">
                        <div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:300px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;"></div>
                    </div>';
            }
        }
        $html .= '<div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; ">
                        <div style="width:100px; text-align:center; height:20px;float:left; display: inline-block; border-right: 1px solid;">備</div>
                        <div style="width:377px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">總額</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ '.$subtotal.'</div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; ">
                        <div style="width:100px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:377px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">營業稅</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ '.$subtotal.'</div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:100px; text-align:center; height:20px;float:left; display: inline-block; border-right: 1px solid;">註</div>
                        <div style="width:377px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">總計</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ '.$subtotal.'</div>
                    </div>
                    <div style="width:102%;text-align:center; height:20px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:200px; height:20px;float:left; display: inline-block; border-right: 1px solid;">客戶簽章</div>
                        <div style="width:200px; height:20px;float:left; display: inline-block; border-right: 1px solid;">主管簽章</div>
                        <div style="width:70px; height:20px;float:left; display: inline-block; border-right: 1px solid;">財務處</div>
                        <div style="width:230px; height:20px;float:left; display: inline-block;">承辦人</div>
                    </div>
                    <div style="width:102%;text-align:center; height:60px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:200px; height:60px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:200px; height:60px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:70px; height:60px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:230px; height:60px;float:left; display: inline-block;">' . Auth::user()->UserEmail . '</div>
                    </div>
                    <div style="width:102%; height:10px;"></div>
                    <div style="width:102%;text-align:center; height:20px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;border-top: 1px solid;">
                        <div style="width:350px; height:20px;float:left; display: inline-block; border-right: 1px solid;">客戶簽章</div>
                        <div style="width:350px; height:20px;float:left; display: inline-block;">承辦人</div>
                    </div>
                    <div style="width:102%;text-align:center; height:392px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:350px; height:392px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:350px; height:392px;float:left; display: inline-block;"></div>
                    </div>
                </body>
            </html>';
        return PDF ::load($html, 'F4', 'portrait')->show(Session::get('sn'));
    }

    static function postAvail() {
        $sn = Input::get('sn');
        $inv = Inventory::find($sn);
        $inv->Missing = 0;
        $inv->save();
    }

    static function getSN($msi) {
        return Inventory::where('MSISDN', $msi)->first()->SerialNumber;
    }

    static function getShipout() {
        $lasthist = History::where('SN', 'like', '%' . Input::get('sn') . '%')->where('Status', '2')->orderBy('ID', 'desc')->first()->SubAgent;
        return $lasthist;
    }

    static function getFS() {
        $lasthist = DB::table('m_historymovement')->select('ShipoutNumber')->distinct()->get();
        Session::forget('FormSeriesInv');
        return $lasthist;
    }

    static function getForm() {
        $lastnum = History::where('ShipoutNumber', 'like', '%' . Input::get('sn') . '%')->orderBy('ID', 'desc')->first();
        if ($lastnum != null) {
            $lastnum = $lastnum->ShipoutNumber;
            $lastnum = substr($lastnum, -3, 3);
        } else {
            $lastnum = 0;
        }
        $lastnum ++;
        $lastnum = sprintf("%'03d", $lastnum);
        return $lastnum;
    }

    static function inventoryDataBackup($filter) {
        $table = 'm_inventory';
        $filter = explode(',,,', $filter);
        $type = '>0';
        $status = '>=0';
        $fs = '';
        if (Session::has('FormSeriesInv'))
            $fs = Session::get('FormSeriesInv');
        if ($filter[0] != 'all') {
            $type = '=' . $filter[0];
        }
        if (isset($filter[1])) {
            $status = '=' . $filter[1];
        }
        $primaryKey = 'm_inventory`.`SerialNumber';
        $columns = array(
            array('db' => 'SerialNumber', 'dt' => 0),
            array(
                'db' => 'Type',
                'dt' => 1,
                'formatter' => function( $d, $row ) {
                    if ($d == 1) {
                        return 'SIM 3G';
                    } else if ($d == 2) {
                        return 'eVoucher';
                    } else if ($d == 3) {
                        return 'phVoucher';
                    } else {
                        return 'SIM 4G';
                    }
                }
            ),
            array(
                'db' => 'Status',
                'dt' => 2,
                'formatter' => function( $d, $row ) {
                    if ($d == 0) {
                        return 'Ship In';
                    } else if ($d == 1) {
                        return 'Return';
                    } else if ($d == 2) {
                        return 'Ship Out';
                    } else if ($d == 3) {
                        return 'Warehouse';
                    } else {
                        return 'Consignment';
                    }
                }
            ),
            array('db' => 'SubAgent', 'dt' => 3),
            array('db' => 'ShipoutNumber', 'dt' => 4),
            array('db' => 'LastWarehouse', 'dt' => 5),
            array('db' => 'Date', 'dt' => 6),
            array('db' => 'MSISDN', 'dt' => 7)
        );

        $sql_details = getConnection();

        require('ssp.class.php');
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "m_inventory.Type " . $type;
        $extraCondition .= " && m_historymovement.Status " . $status;
        $extraCondition .= " && m_historymovement.ShipoutNumber LIKE '%" . $fs . "%'";
        $join = ' INNER JOIN m_historymovement on m_historymovement.ID = m_inventory.LastStatusID';

        echo json_encode(
                SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

    static function inventoryDataBackupOut($id) {
        $startid = explode(',,,', $id)[0];
        $endid = explode(',,,', $id)[1];
        $statusAvail = explode(',,,', $id)[2];
        $string_temp = '!= 2';
        $string_miss = '= 0';
        if ($statusAvail == 0) {
            $string_temp = '= 2';
        } else if ($statusAvail == 2) {
            $string_miss = '> 0';
        }
        $table = 'm_inventory';
        $primaryKey = 'm_inventory`.`SerialNumber';
        $columns = array(
            array('db' => 'SerialNumber', 'dt' => 0),
            array(
                'db' => 'Type',
                'dt' => 1,
                'formatter' => function( $d, $row ) {
                    if ($d == 1) {
                        return 'SIM 3G';
                    } else if ($d == 2) {
                        return 'eVoucher';
                    } else if ($d == 3) {
                        return 'phVoucher';
                    } else {
                        return 'SIM 4G';
                    }
                }
            ),
            array(
                'db' => 'Status',
                'dt' => 2,
                'formatter' => function( $d, $row ) {
                    if ($d == 0) {
                        return 'Ship In';
                    } else if ($d == 1) {
                        return 'Return';
                    } else if ($d == 2) {
                        return 'Ship Out';
                    } else if ($d == 3) {
                        return 'Warehouse';
                    } else {
                        return 'Consignment';
                    }
                }
            ),
            array('db' => 'LastWarehouse', 'dt' => 3),
            array('db' => 'Date', 'dt' => 4),
            array('db' => 'MSISDN', 'dt' => 5),
            array('db' => 'SerialNumber', 'dt' => 6, 'formatter' => function( $d, $row ) {
                    $data = Inventory::find($d);
                    if ($data->Missing == 0) {
                        $hist = History::find($data->LastStatusID);
                        $disa = '';
                        if ($hist->Status == 2) {
                            $disa = 'disabled';
                        }
                        $return = '<button type="button" data-internal="' . $data->SerialNumber . '"  onclick="deleteAttach(this)"
                                             class="btn btn-pure-xs btn-xs btn-delete" ' . $disa . '>
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </button>';
                    } else {
                        $return = '<button title="Set to available" type="button" data-internal="' . $data->SerialNumber . '"  onclick="availAttach(this)"
                                             class="btn btn-pure-xs btn-xs btn-delete">
                                        <span class="glyphicon glyphicon-thumbs-up"></span>
                                    </button>';
                    }
                    return $return;
                }, 'field' => 'm_inventory`.`SerialNumber')
        );

        $sql_details = getConnection();

        require('ssp.class.php');
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "m_inventory.`SerialNumber` >= '" . $startid . "' && " . "m_inventory.`SerialNumber` <= '" . $endid . "'";
        $extraCondition .= " && m_historymovement.Status " . $string_temp;
        $extraCondition .= " && m_inventory.Missing " . $string_miss;
        $join = ' INNER JOIN m_historymovement on m_historymovement.ID = m_inventory.LastStatusID';

        echo json_encode(
                SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

    static function inventoryDataBackupCons($id) {
        $msisdn = explode(',,,', $id)[0];
        $serial = explode(',,,', $id)[1];
        $series = '';
        if (Session::has('FormSeries'))
            $series = Session::get('FormSeries');
        $statusAvail = explode(',,,', $id)[2];
        $inv = '';
        $string_temp = '= 4';
        $string_miss = '= 0';
        if ($statusAvail == '0') {
            $string_temp = '!= 4';
        } else if ($statusAvail == '2') {
            $statusAvail = 1;
            $string_temp = '= 4';
            $string_miss = '= 1';
        }
        if ($series != '0') {
            $serial = '';
            $msisdn = '';
        } else {
            $series = '';
            if ($msisdn == '0') {
                $msisdn = '';
            } else {
                $inv = Inventory::where('MSISDN', 'like', '%' . $msisdn . '%')->first();
                $hist = History::where('SN', $inv->SerialNumber)->where('Status', 4)->orderBy('ID', 'desc')->first();
                $series = $hist->ShipoutNumber;
            }
            if ($serial == '0') {
                $serial = '';
            } else {
                $hist = History::where('SN', 'like', '%' . $serial . '%')->where('Status', 4)->orderBy('ID', 'desc')->first();
                $series = $hist->ShipoutNumber;
            }
        }
        if ($msisdn == 0) {
            $msisdn = '';
        }
        if ($serial == 0) {
            $serial = '';
        }
        $table = 'm_inventory';
        $primaryKey = 'm_inventory`.`SerialNumber';
        $columns = array(
            array('db' => 'SerialNumber', 'dt' => 0),
            array(
                'db' => 'Type',
                'dt' => 1,
                'formatter' => function( $d, $row ) {
                    if ($d == 1) {
                        return 'SIM 3G';
                    } else if ($d == 2) {
                        return 'eVoucher';
                    } else if ($d == 3) {
                        return 'phVoucher';
                    } else {
                        return 'SIM 4G';
                    }
                }
            ),
            array(
                'db' => 'Status',
                'dt' => 2,
                'formatter' => function( $d, $row ) {
                    if ($d == 0) {
                        return 'Ship In';
                    } else if ($d == 1) {
                        return 'Return';
                    } else if ($d == 2) {
                        return 'Ship Out';
                    } else if ($d == 3) {
                        return 'Warehouse';
                    } else {
                        return 'Consignment';
                    }
                }
            ),
            array('db' => 'LastWarehouse', 'dt' => 3),
            array('db' => 'Date', 'dt' => 4),
            array('db' => 'MSISDN', 'dt' => 5),
            array('db' => 'SerialNumber', 'dt' => 6, 'formatter' => function( $d, $row ) {
                    $data = Inventory::find($d);
                    if ($data->Missing == 0) {
                        $hist = History::find($data->LastStatusID);
                        $disa = '';
                        if ($hist->Status != 4) {
                            $disa = 'disabled';
                        }
                        $return = '<button type="button" data-internal="' . $data->SerialNumber . '"  onclick="deleteAttach(this)"
                                             class="btn btn-pure-xs btn-xs btn-delete" ' . $disa . '>
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </button>';
                    } else {
                        $return = '<button title="Set to available" type="button" data-internal="' . $data->SerialNumber . '"  onclick="availAttach(this)"
                                             class="btn btn-pure-xs btn-xs btn-delete">
                                        <span class="glyphicon glyphicon-thumbs-up"></span>
                                    </button>';
                    }
                    return $return;
                }, 'field' => 'm_inventory`.`SerialNumber')
        );

        $sql_details = getConnection();
        require('ssp.class.php');
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "m_historymovement.Status " . $string_temp;
        $extraCondition .= " && m_historymovement.ShipoutNumber LIKE '%" . $series . "%'";
        $extraCondition .= " && m_inventory.Missing " . $string_miss;
        $join = ' INNER JOIN m_historymovement on m_historymovement.ID = m_inventory.LastStatusID';

        echo json_encode(
                SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

    static function inventoryDataBackupWare($id) {
        $startid = explode(',,,', $id)[0];
        $endid = explode(',,,', $id)[1];
        $statusAvail = explode(',,,', $id)[2];
        $string_temp = '';
        if ($statusAvail == 0) {
            $string_temp = '= 2';
        } else {
            $string_temp = '!= 2';
        }
        $table = 'm_inventory';
        $primaryKey = 'm_inventory`.`SerialNumber';
        $columns = array(
            array('db' => 'SerialNumber', 'dt' => 0),
            array(
                'db' => 'Type',
                'dt' => 1,
                'formatter' => function( $d, $row ) {
                    if ($d == 1) {
                        return 'SIM 3G';
                    } else if ($d == 2) {
                        return 'eVoucher';
                    } else if ($d == 3) {
                        return 'phVoucher';
                    } else {
                        return 'SIM 4G';
                    }
                }
            ),
            array(
                'db' => 'Status',
                'dt' => 2,
                'formatter' => function( $d, $row ) {
                    if ($d == 0) {
                        return 'Ship In';
                    } else if ($d == 1) {
                        return 'Return';
                    } else if ($d == 2) {
                        return 'Ship Out';
                    } else if ($d == 3) {
                        return 'Warehouse';
                    } else {
                        return 'Consignment';
                    }
                }
            ),
            array('db' => 'Date', 'dt' => 3),
            array('db' => 'MSISDN', 'dt' => 4)
        );

        $sql_details = getConnection();

        require('ssp.class.php');
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "m_inventory.`SerialNumber` >= '" . $startid . "' && " . "m_inventory.`SerialNumber` <= '" . $endid . "'";
        $extraCondition .= " && m_historymovement.Status " . $string_temp;
        $join = ' INNER JOIN m_historymovement on m_historymovement.ID = m_inventory.LastStatusID';

        echo json_encode(
                SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

    static function inventoryDataBackupReturn($id) {
        $statusAvail = explode(',,,', $id)[1];
        $arrayids = explode(',', explode(',,,', $id)[0]);
        $array = implode("','", $arrayids);
        $string_temp = '= 2';
        if ($statusAvail == 0) {
            $string_temp = '!= 2';
        }
        $table = 'm_inventory';
        $primaryKey = 'm_inventory`.`SerialNumber';
        $columns = array(
            array('db' => 'SerialNumber', 'dt' => 0),
            array(
                'db' => 'Type',
                'dt' => 1,
                'formatter' => function( $d, $row ) {
                    if ($d == 1) {
                        return 'SIM 3G';
                    } else if ($d == 2) {
                        return 'eVoucher';
                    } else if ($d == 3) {
                        return 'phVoucher';
                    } else {
                        return 'SIM 4G';
                    }
                }
            ),
            array(
                'db' => 'Status',
                'dt' => 2,
                'formatter' => function( $d, $row ) {
                    if ($d == 0) {
                        return 'Ship In';
                    } else if ($d == 1) {
                        return 'Return';
                    } else if ($d == 2) {
                        return 'Ship Out';
                    } else if ($d == 3) {
                        return 'Warehouse';
                    } else {
                        return 'Consignment';
                    }
                }
            ),
            array('db' => 'Date', 'dt' => 3),
            array('db' => 'MSISDN', 'dt' => 4)
        );

        $sql_details = getConnection();

        require('ssp.class.php');
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "(m_inventory.`SerialNumber` IN ('" . $array . "')";
        $extraCondition .= " OR m_inventory.`MSISDN` IN ('" . $array . "'))";
        $extraCondition .= " AND m_historymovement.Status " . $string_temp;
        $join = ' INNER JOIN m_historymovement on m_historymovement.ID = m_inventory.LastStatusID';

        echo json_encode(
                SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

}
