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

                                    $allhist = History::where('SN', $inv->SerialNumber)->get();
                                    foreach ($allhist as $hist) {
                                        $hist->LastStatus = $status;
                                        $hist->save();
                                    }
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

                                $allhist = History::where('SN', $inv->SerialNumber)->get();
                                foreach ($allhist as $hist) {
                                    $hist->LastStatus = $status;
                                    $hist->save();
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
                                    $type = $value->type;
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

    public function showDashboard() {
        $dataReport = [];
        $today = getdate();

        //monthly
        //SIM
        if (Session::has('sim_month')) {
            $total_shipout_this_year = Session::get('sim_month');
        } else {
            $total_shipout_this_year = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                    ->where('m_historymovement.Status', '2')
                    ->where('m_inventory.Missing', '0')
                    ->whereIn('m_inventory.Type', array(1, 4))
                    ->where('m_historymovement.Date', 'like', "%" . $today['year'] . '%')
                    ->count('m_inventory.SerialNumber');
            Session::put('sim_month', $total_shipout_this_year);
        }
        $last_history_month = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->where('m_historymovement.Status', '2')
                        ->where('m_inventory.Missing', '0')
                        ->whereIn('m_inventory.Type', array(1, 4))
                        ->where('m_historymovement.Date', 'like', "%" . $today['year'] . '%')->orderBy('m_historymovement.Date', 'desc')
                        ->select('m_historymovement.Date')->distinct()->first();
        $temp_count1 = 1;
        if ($last_history_month != null)
            $temp_count1 = (explode('-', $last_history_month->Date)[1]);
        $dataReport['avg_monthly_sim'] = (int) ($total_shipout_this_year / $temp_count1);
        //VOC
        if (Session::has('voc_month')) {
            $total_shipout_this_year = Session::get('voc_month');
        } else {
            $total_shipout_this_year = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                    ->where('m_historymovement.Status', '2')
                    ->where('m_inventory.Missing', '0')
                    ->whereIn('m_inventory.Type', array(2, 3))
                    ->where('m_historymovement.Date', 'like', "%" . $today['year'] . '%')
                    ->count('m_inventory.SerialNumber');
            Session::put('voc_month', $total_shipout_this_year);
        }
        $last_history_month = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->where('m_historymovement.Status', '2')
                        ->where('m_inventory.Missing', '0')
                        ->whereIn('m_inventory.Type', array(2, 3))
                        ->where('m_historymovement.Date', 'like', "%" . $today['year'] . '%')->orderBy('m_historymovement.Date', 'desc')
                        ->select('m_historymovement.Date')->distinct()->first();
        $temp_count1 = 1;
        if ($last_history_month != null)
            $temp_count1 = (explode('-', $last_history_month->Date)[1]);
        $dataReport['avg_monthly_voc'] = (int) ($total_shipout_this_year / $temp_count1);

        //weekly
        //SIM
        if (Session::has('sim_week')) {
            $total_shipout_this_month = Session::get('sim_week');
        } else {
            $total_shipout_this_month = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                    ->where('m_historymovement.Status', '2')
                    ->where('m_inventory.Missing', '0')
                    ->whereIn('m_inventory.Type', array(1, 4))
                    ->where('m_historymovement.Date', 'like', "%" . $today['year'] . '-' . $today['mon'] . '%')
                    ->count('m_inventory.SerialNumber');
            Session::put('sim_week', $total_shipout_this_month);
        }
        $last_history_week = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->where('m_historymovement.Status', '2')
                        ->where('m_inventory.Missing', '0')
                        ->whereIn('m_inventory.Type', array(1, 4))
                        ->where('m_historymovement.Date', 'like', "%" . $today['year'] . '-' . $today['mon'] . '%')->orderBy('m_historymovement.Date', 'desc')
                        ->select('m_historymovement.Date')->distinct()->first();
        $week_number = 1;
        if ($last_history_week != null)
            $week_number = (int) ((explode('-', $last_history_week->Date)[2]) / 7);
        $dataReport['avg_weekly_sim'] = (int) ($total_shipout_this_month / $week_number);
        //voc
        if (Session::has('voc_week')) {
            $total_shipout_this_month = Session::get('voc_week');
        } else {
            $total_shipout_this_month = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                    ->where('m_historymovement.Status', '2')
                    ->where('m_inventory.Missing', '0')
                    ->whereIn('m_inventory.Type', array(2, 3))
                    ->where('m_historymovement.Date', 'like', "%" . $today['year'] . '-' . $today['mon'] . '%')
                    ->count('m_inventory.SerialNumber');
            Session::put('voc_week', $total_shipout_this_month);
        }
        $last_history_week = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->where('m_historymovement.Status', '2')
                        ->where('m_inventory.Missing', '0')
                        ->whereIn('m_inventory.Type', array(2, 3))
                        ->where('m_historymovement.Date', 'like', "%" . $today['year'] . '-' . $today['mon'] . '%')->orderBy('m_historymovement.Date', 'desc')
                        ->select('m_historymovement.Date')->distinct()->first();
        $week_number = 1;
        if ($last_history_week != null)
            $week_number = (int) ((explode('-', $last_history_week->Date)[2]) / 7);
        $dataReport['avg_weekly_voc'] = (int) ($total_shipout_this_month / $week_number);
        return View::make('dashboard')->withPage('dashboard')->withData($dataReport);
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

                    $allhist = History::where('SN', $inv->SerialNumber)->get();
                    foreach ($allhist as $hist) {
                        $hist->LastStatus = 3;
                        $hist->save();
                    }
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
            $cs = Session::get('conses');

            if (Input::get('newagent') != '') {
                $subagent = Input::get('newagent');
            }
            $counter = 0;
            $allInvAvail = Inventory::whereIn('SerialNumber', Session::get('temp_inv_arr'))->where('Missing', 0)->get();
            foreach ($allInvAvail as $inv) {
                $status_ = 2;
                $history = History::where('ID', $inv['LastStatusID'])->first();
                if ($history->Status != 2) { //available
                    $hist = new History();
                    $hist->SN = $inv->SerialNumber;
                    $hist->SubAgent = $subagent;
                    $hist->Price = $price;
                    if ($price == '0' && $cs == 0) {
                        $hist->Warehouse = 'TELIN TAIWAN';
                        $inv->LastWarehouse = 'TELIN TAIWAN';
                    }
                    if ($cs == 1) {
                        $status_ = 4;
                    }
                    $hist->ShipoutNumber = $series;
                    $hist->Status = $status_;
                    $hist->Remark = Input::get('remark');
                    $hist->Date = Input::get('eventDate');
                    $hist->userRecord = Auth::user()->ID;
                    $hist->save();

                    //update last status
                    $inv->LastStatusID = $hist->ID;
                    $inv->save();

                    $allhist = History::where('SN', $inv->SerialNumber)->get();
                    foreach ($allhist as $hist) {
                        $hist->LastStatus = $status_;
                        $hist->save();
                    }
                    $counter++;
                }
            }
            Session::forget('temp_inv_start');
            Session::forget('temp_inv_end');
            Session::forget('temp_inv_price');
            Session::forget('temp_inv_arr');
            Session::forget('temp_inv_qty');
            Session::forget('conses');
            $allinvs = DB::table('m_inventory')
                            ->whereIn('SerialNumber', Session::get('temp_inv_arr'))->get();
            foreach ($allinvs as $upt_inv) {
                $need_update = Inventory::where('SerialNumber', $upt_inv->SerialNumber)->first();
                $need_update->TempPrice = 0;
                $need_update->save();
            }
            return View::make('shipout')->withResponse('Success')->withPage('inventory shipout')->withNumber($counter);
        }
        Session::forget('temp_inv_start');
        Session::forget('temp_inv_end');
        Session::forget('temp_inv_price');
        Session::forget('temp_inv_arr');
        Session::forget('temp_inv_qty');

        $allinvs = DB::table('m_inventory')
                        ->whereIn('SerialNumber', Session::get('temp_inv_arr'))->get();
        foreach ($allinvs as $upt_inv) {
            $need_update = Inventory::where('SerialNumber', $upt_inv->SerialNumber)->first();
            $need_update->TempPrice = 0;
            $need_update->save();
        }
        Session::forget('conses');
        return View::make('shipout')->withPage('inventory shipout');
    }

    public function showConsignment() {
        Session::forget('snCons');
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
                    $inv->save();

                    $allhist = History::where('SN', $inv->SerialNumber)->get();
                    foreach ($allhist as $hist) {
                        $hist->LastStatus = 2;
                        $hist->save();
                    }
                    $counter++;
                }
            }
            return View::make('consignment')->withResponse('Success')->withPage('shipout consignment')->withNumber($counter);
        }
        return View::make('consignment')->withPage('shipout consignment');
    }

    public function showReturnInventory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $SerialNumber = "";
            $counter = 0;
            $counterfail = 0;
            $notavail = '';
            $nodata = '';
            $successins = '';
//                    $date = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('Asia/Taipei'));
            $date = Input::get('eventDate');
            $fn = Input::get('formSN');
            $remark = Input::get('remark');
            $destination = base_path() . '/uploaded_file/';
            $extention = Input::file('sample_file')->getClientOriginalExtension();
            $filename = 'tempreturn.' . $extention;
            Input::file('sample_file')->move($destination, $filename);
            $filePath = base_path() . '/uploaded_file/' . 'tempreturn.' . $extention;
            $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
//$reader = ReaderFactory::create(Type::CSV); // for CSV files
//$reader = ReaderFactory::create(Type::ODS); // for ODS files

            $reader->open($filePath);
            $counter = 0;
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                    if ($rowNumber > 1) {
                        $inv = Inventory::where('SerialNumber', 'LIKE', '%' . $value[0] . '%')->orWhere('MSISDN', 'LIKE', '%' . $value[0] . '%')->first();
                        if ($inv != null) {
                            $lastmovement = $inv->LastStatusID;
                            //update history
                            $hist = History::where('ID', $lastmovement)->first();
                            if ($hist->Status == 2) {
                                $counter++;
                                if ($successins == '') {
                                    $successins .= $value[0];
                                } else {
                                    $successins .= ', ' . $value[0];
                                }

                                //can return, update
                                $insertHistory = ['SN' => $inv->SerialNumber, 'Date' => $date, 'Remark' => $remark, 'Status' => 1, 'ShipoutNumber' => $fn];
                                if (!empty($insertHistory)) {
                                    $lasthistoryID = DB::table('m_historymovement')->insertGetId($insertHistory);
                                }

                                $inv->LastStatusID = $lasthistoryID;
                                $inv->save();

                                $allhist = History::where('SN', $inv->SerialNumber)->get();
                                foreach ($allhist as $hist) {
                                    $hist->LastStatus = 1;
                                    $hist->save();
                                }
                            } else {
                                if ($notavail == '') {
                                    $notavail .= $value[0];
                                } else {
                                    $notavail .= ', ' . $value[0];
                                }
                                $counterfail++;
                            }
                        } else {
                            $counterfail++;
                            if ($nodata == '') {
                                $nodata .= $value[0];
                            } else {
                                $nodata .= ', ' . $value[0];
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

    public function showInsertReporting() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (Input::get('jenis') == 'ivr') {
                $input = Input::file('sample_file');
                if ($input != '') {
                    if (Input::hasFile('sample_file')) {
                        $destination = base_path() . '/uploaded_file/';
                        $extention = Input::file('sample_file')->getClientOriginalExtension();
                        $filename = 'temp.' . $extention;
                        Input::file('sample_file')->move($destination, $filename);
                        $filePath = base_path() . '/uploaded_file/' . 'temp.' . $extention;
                        $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
//$reader = ReaderFactory::create(Type::CSV); // for CSV files
//$reader = ReaderFactory::create(Type::ODS); // for ODS files

                        $reader->open($filePath);
                        $counter = 0;
                        foreach ($reader->getSheetIterator() as $sheet) {
                            foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                if ($rowNumber > 1) {
                                    // do stuff with the row
                                    $msisdn = $value[2];
                                    if ($msisdn != '' && $msisdn != null) {
                                        $inv = Inventory::where('MSISDN', $msisdn)->first();
                                        if ($inv != null) {
                                            $ivr = new Ivr();
                                            $ivr->MSISDN_ = $msisdn;
                                            $ivr->Date = $value[1];
                                            $ivr->PurchaseAmount = $value[4];
                                            $ivr->save();
                                            $counter++;
                                        }
                                    }
                                }
                            }
                        }

                        $reader->close();
                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumber($counter);
                    }
                }
                return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
            } else if (Input::get('jenis') == 'apf') {
                $input = Input::file('sample_file');
                if ($input != '') {
                    if (Input::hasFile('sample_file')) {
                        $destination = base_path() . '/uploaded_file/';
                        $extention = Input::file('sample_file')->getClientOriginalExtension();
                        $filename = 'temp.' . $extention;
                        Input::file('sample_file')->move($destination, $filename);
                        $data = Excel::load(base_path() . '/uploaded_file/' . 'temp.' . $extention, function($reader) {
                                    
                                })->get();
                        $counter = 0;
                        if (!empty($data) && $data->count()) {
                            foreach ($data as $key => $value) {
                                $msisdn = $value->msisdn;
                                if ($msisdn != '' && $msisdn != null) {
                                    $inv = Inventory::where('MSISDN', $msisdn)->first();
                                    if ($inv != null) {
                                        if ($inv->ApfDate == null || $inv->ApfDate == '') {
                                            $inv->ApfDate = $value->apf_returned_date;
                                            $inv->save();
                                        }
                                    }
                                }
                                $counter++;
                            }
                        }
                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumberapf($counter);
                    }
                }
                return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
            } else if (Input::get('jenis') == 'act') {
                $input = Input::file('sample_file');
                if ($input != '') {
                    if (Input::hasFile('sample_file')) {
                        $destination = base_path() . '/uploaded_file/';
                        $extention = Input::file('sample_file')->getClientOriginalExtension();
                        $filename = 'temp.' . $extention;
                        Input::file('sample_file')->move($destination, $filename);
                        $data = Excel::load(base_path() . '/uploaded_file/' . 'temp.' . $extention, function($reader) {
                                    
                                })->get();
                        $counter = 0;
                        if (!empty($data) && $data->count()) {
                            foreach ($data as $key => $value) {
                                $msisdn = $value->new_msisdn;
                                if ($msisdn != '' && $msisdn != null) {
                                    $inv = Inventory::where('MSISDN', $msisdn)->first();
                                    if ($inv != null) {
                                        $inv->ActivationDate = $value->activation_date;
                                        $inv->save();
                                    }
                                }
                                $counter++;
                            }
                        }
                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumberac($counter);
                    }
                }
                return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
            }
        }
        return View::make('insertreporting')->withPage('insert reporting');
    }

    public function showInventory() {
        Session::forget('FormSeriesInv');
        Session::forget('WarehouseInv');
        return View::make('inventory')->withPage('inventory');
    }

    //============================ajax===============================

    static function postMissing() {
        $sn = Input::get('sn');
        $inv = Inventory::find($sn);
        $inv->Missing = 1;
        $inv->save();
    }

    static function postConsStat() {
        Session::put('conses', Input::get('cs'));
    }

    static function postNewAgent() {
        Session::put('NewAgent', Input::get('agent'));
    }

    static function postFormSeries() {
        Session::put('FormSeries', Input::get('fs'));
        Session::put('FormSeriesInv', Input::get('fs'));
    }

    static function postWarehouse() {
        Session::put('WarehouseInv', Input::get('wh'));
    }

    static function exportExcel($filter) {
        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "test.xlsx";
        $writer->openToFile($filePath);
        $myArr = array("SERIAL NUMBER", "MSISDN", "TYPE", "LAST STATUS", "SHIPOUT TO", "SUBAGENT", "FORM SERIES", "LAST WAREHOUSE", "SHIPOUT DATE", "SHIPOUT PRICE", "SHIPIN DATE", "SHIPIN PRICE", "REMARK");
        $writer->addRow($myArr); // add a row at a time

        $invs = '';
        $filter = explode(',,,', $filter);
        $typesym = '>=';
        $type = '0';
        $statussym = '>=';
        $status = '0';
        $fs = '';
        $wh = '';
        if (Session::has('WarehouseInv'))
            $wh = Session::get('WarehouseInv');
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
        if ($fs == '') {
            $invs = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.Type', $typesym, $type)
                            ->where('m_historymovement.Status', $statussym, $status)->get();
            if ($wh != '') {
                $invs = DB::table('m_inventory')
                                ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                                ->where('m_inventory.Type', $typesym, $type)->where('m_inventory.LastWarehouse', 'LIKE', '%' . $wh . '%')
                                ->where('m_historymovement.Status', $statussym, $status)->get();
            }
        } else if ($fs != '') {
            $invs = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                            ->where('m_inventory.Type', $typesym, $type)
                            ->where('m_historymovement.LastStatus', $statussym, $status)
                            ->where('m_historymovement.ShipoutNumber', 'like', '%' . $fs . '%')->get();
            if ($wh != '') {
                $invs = DB::table('m_inventory')
                                ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                                ->where('m_inventory.Type', $typesym, $type)
                                ->where('m_historymovement.Status', $statussym, $status)->where('m_inventory.LastWarehouse', 'LIKE', '%' . $wh . '%')
                                ->where('m_historymovement.ShipoutNumber', 'like', '%' . $fs . '%')->get();
            }
        }
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
            $agent = '';
            $subagent = '';
            $tempcount = 0;
            if ($hist->SubAgent != '') {
                $shipout = explode(' ', $hist->SubAgent);
//                foreach ($shipout as $word) {
//                    if ($tempcount > 0) {
//                        $subagent .= $word . ' ';
//                    }
//                    $tempcount++;
//                }
            }
            if ($shipout != '') {
                $agent = $shipout[0];
            }
            $myArr = array($inv->SerialNumber, $inv->MSISDN, $type, $status, $agent, $hist->SubAgent, $hist->ShipoutNumber, $inv->LastWarehouse, $shipoutdt, $shipoutprice, $shipindt, $inv->Price, $hist->Remark);
            $writer->addRow($myArr);
        }
        $writer->close();
    }

    static function exportExcel2($filter) {
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
        $wh = '';
        if (Session::has('WarehouseInv'))
            $wh = Session::get('WarehouseInv');
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
        if ($fs == '') {
            $invs = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                    ->where('m_inventory.Type', $typesym, $type)
                    ->where('m_historymovement.Status', $statussym, $status);
            if ($wh != '') {
                $invs->where('m_inventory.LastWarehouse', 'LIKE', '%' . $wh . '%');
            }
            $invs = $invs->get();
        } else if ($fs != '') {
            $invs = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                    ->where('m_inventory.Type', $typesym, $type)
                    ->where('m_historymovement.Status', $statussym, $status)
                    ->where('m_historymovement.ShipoutNumber', 'like', '%' . $fs . '%');
            if ($wh != '') {
                $invs->where('m_inventory.LastWarehouse', 'LIKE', '%' . $wh . '%');
            }
            $invs = $invs->get();
        }
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

            Session::put('sn', $sn);
            Session::put('date', $date);
            Session::put('subagent', $subagent);
            Session::put('to', $to);
            Session::put('conses', Input::get('cs'));
            if ($start != '' && $end != '') {
                Session::put('start', $start);
                Session::put('end', $end);
            }

            return 'success';
        }

        $all_start = [];
        $all_end = [];
        $all_qty = [];
        $all_price = [];
        $all_type = [];
        $temp_count = 0;
        $subtotal = 0;
        $temp_string = '';
        if (Session::has('temp_inv_arr')) {
            $wh = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->whereRaw('m_inventory.SerialNumber IN (' . Session::get('temp_inv_arr') . ')')
                            ->where('m_historymovement.Status', '!=', '2')
                            ->where('m_inventory.Missing', '0')
                            ->select('m_inventory.LastWarehouse')
                            ->distinct()->first()->LastWarehouse;
        }
        if (Session::has('temp_inv_start')) {
            $all_start = explode(',,,', Session::get('temp_inv_start'));
            $all_end = explode(',,,', Session::get('temp_inv_end'));
            $all_qty = explode(',,,', Session::get('temp_inv_qty'));
            $all_price = explode(',,,', Session::get('temp_inv_price'));
            foreach ($all_start as $starting) {
                if (strpos($starting, '+') !== false) {
                    $starting = explode('+', $starting)[0];
                }
                $type_a = Inventory::where('SerialNumber', $starting)->first()->Type;
                array_push($all_type, $type_a);
            }
        }

        if (Session::get('conses') == 0) {
            $wh = 'TELIN TAIWAN';
            $temp_string = '銷貨單';
        }
        if (Session::get('conses') == 1)
            $temp_string = '借貨單';
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
                        <p style="font-size:120%;">' . $temp_string . '</p>
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
                        <div style="width:200px; height:20px;float:left; display: inline-block;">統一編號: 54013468</div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid;">
                        <div style="width:70px;padding-left:3px height:20px;float:left; display: inline-block; ">送貨地址 ：</div>
                        <div style="width:430px; height:20px;float:left; display: inline-block;"></div>
                        <div style="width:200px; height:20px;float:left; display: inline-block;"></div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:70px;padding-left:3px height:20px;float:left; display: inline-block; ">發票號碼 :</div>
                        <div style="width:430px; height:20px;float:left; display: inline-block;">QS 48949608</div>
                        <div style="width:200px; height:20px;float:left; display: inline-block;">倉 庫 別:' . $wh . ' (紅白電訊)</div>
                    </div>
                    <div style="width:102%; text-align:center;height:20px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:100px; height:20px;float:left; display: inline-block; border-right: 1px solid;">產品編號</div>
                        <div style="width:300px; height:20px;float:left; display: inline-block; border-right: 1px solid;">產品名稱</div>
                        <div style="width:70px; height:20px;float:left; display: inline-block; border-right: 1px solid;">數 量</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">訂價/單價</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">合計</div>
                    </div>';
        for ($i = 0; $i < count($all_start); $i++) {
            if ($all_start[$i] != '') {
                $subtotal += round((($all_price[$i] / 1.05) * $all_qty[$i]), 0);
                $tipe = '';
                switch ($all_type[$i]) {
                    case 3:
                        $tipe = 'ph-Voucher';
                        break;
                    case 1:
                        $tipe = 'SIM 3G';
                        break;
                    case 2:
                        $tipe = 'e-Voucher';
                        break;
                    case 4:
                        $tipe = 'SIM 4G';
                        break;
                }
                $html .= '<div style="width:102%; height:15px; border-left: 1px solid;  border-right: 1px solid;">
                        <div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:300px; height:15px;float:left; display: inline-block; border-right: 1px solid;">' . $tipe . '</div>
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;">' . $all_qty[$i] . '</div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;">NT$ ' . round(($all_price[$i] / 1.05), 4) . '</div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;">NT$ ' . round((($all_price[$i] / 1.05) * $all_qty[$i]), 0) . '</div>
                    </div>
                    ';
            } else {
                $html .= '<div style="width:102%; height:15px; border-left: 1px solid;  border-right: 1px solid;">
                        <div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:300px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;"></div>
                    </div>';
            }

            if ($all_start[$i] != '') {
                $starts = $all_start[$i];
                $ends = $all_end[$i];
                $temp_cot = 0;
                if (strpos($all_start[$i], '+') !== false) {
                    $starts = explode('+', $all_start[$i]);
                }
                if (strpos($all_end[$i], '+') !== false) {
                    $ends = explode('+', $all_end[$i]);
                }
                if (count($starts) > 1) {
                    foreach ($starts as $temp1) {
                        $html .= '<div style="width:102%; height:15px; padding-top:-2px; border-left: 1px solid;  border-right: 1px solid; ';
                        if ($i == count($all_start) - 1)
                            $html .= 'border-bottom: 1px solid;';
                        $html .= '"><div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>';
                        $html .= '<div style="width:302px; height:15px;float:left; display: inline-block; border-right: 1px solid; padding-left: 4px;">';
                        if ($temp_cot == count($starts) - 1)
                            $html .= $temp1 . ' - ' . $ends[$temp_cot];
                        else {
                            $html .=  $temp1 . ' - ' . $ends[$temp_cot].', ';
                        }
                        $temp_cot++;
                        $html .= '</div>';
                        $html .= '
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;"></div>';
                        $html .= '</div>';
                    }
                } else {
                    $html .= '<div style="width:102%; height:15px; padding-top:-2px; border-left: 1px solid;  border-right: 1px solid; ';
                    if ($i == count($all_start) - 1)
                        $html .= 'border-bottom: 1px solid;';
                    $html .= '">
                        <div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:300px; height:15px;float:left; display: inline-block; border-right: 1px solid;">';
                    $html .= $starts . ' - ' . $ends;
                    $html .= '</div>
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;"></div>';
                    $html .= '</div>';
                }
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
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ ' . $subtotal . '</div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; ">
                        <div style="width:100px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:377px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">營業稅</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ ' . round(($subtotal * 0.05), 0) . '</div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:100px; text-align:center; height:20px;float:left; display: inline-block; border-right: 1px solid;">註</div>
                        <div style="width:377px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">總計</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ ' . round(($subtotal + ($subtotal * 0.05)), 0) . '</div>
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

    static function getPDFCons() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sn = Input::get('sn');
            $date = Input::get('date');
            $to = Input::get('to');
            $subagent = Input::get('subagent');
            $price = Input::get('price');

            Session::put('sn', $sn);
            Session::put('date', $date);
            Session::put('subagent', $subagent);
            Session::put('to', $to);
            Session::put('price', $price);

            return 'success';
        }
        $type = ['', '', '', ''];
        $count = ['', '', '', ''];
        $first = ['', '', '', ''];
        $last = ['', '', '', ''];
        $temp_count = 0;
        $subtotal = 0;
        if (Session::has('snCons')) {
            $alltype = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                            ->where('m_inventory.Missing', '0')
                            ->select('m_inventory.Type')
                            ->distinct()->get();
            $wh = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                            ->where('m_inventory.Missing', '0')
                            ->select('m_inventory.LastWarehouse')
                            ->distinct()->first()->LastWarehouse;
        }
        if ($alltype != null) {
            foreach ($alltype as $types) {
                if ($types->Type == '1') {
                    $type[$temp_count] = 'SIM 3G';
                    $counters = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                            ->where('m_inventory.Missing', '0')
                            ->where('m_inventory.Type', '1')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '1')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'asc')
                            ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
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
                            ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                            ->where('m_inventory.Missing', '0')
                            ->where('m_inventory.Type', '2')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '2')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'asc')
                            ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
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
                            ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                            ->where('m_inventory.Missing', '0')
                            ->where('m_inventory.Type', '3')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '2')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'asc')
                            ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
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
                            ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                            ->where('m_inventory.Missing', '0')
                            ->where('m_inventory.Type', '4')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '4')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'asc')
                            ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '4')
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'desc')
                            ->first();
                    $last[$temp_count] = $lastid->SerialNumber;
                    $temp_count++;
                }
            }
        }
        if (Session::get('price') == 0) {
            $wh = 'TELIN TAIWAN';
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
                        <div style="width:200px; height:20px;float:left; display: inline-block;">統一編號: 54013468</div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid;">
                        <div style="width:70px;padding-left:3px height:20px;float:left; display: inline-block; ">送貨地址 ：</div>
                        <div style="width:430px; height:20px;float:left; display: inline-block;"></div>
                        <div style="width:200px; height:20px;float:left; display: inline-block;"></div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:70px;padding-left:3px height:20px;float:left; display: inline-block; ">發票號碼 :</div>
                        <div style="width:430px; height:20px;float:left; display: inline-block;">QS 48949608</div>
                        <div style="width:200px; height:20px;float:left; display: inline-block;">倉 庫 別:' . $wh . ' (紅白電訊)</div>
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
                $subtotal += round(((Session::get('temp_inv_price') / 1.05) * $count[$i]), 4);
                $html .= '<div style="width:102%; height:15px; border-left: 1px solid;  border-right: 1px solid;">
                        <div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:300px; height:15px;float:left; display: inline-block; border-right: 1px solid;">' . $type[$i] . '</div>
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;">' . $count[$i] . '</div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;">NT$ ' . round((Session::get('temp_inv_price') / 1.05), 4) . '</div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;">NT$ ' . round(((Session::get('temp_inv_price') / 1.05) * $count[$i]), 4) . '</div>
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
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ ' . $subtotal . '</div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; ">
                        <div style="width:100px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:377px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">營業稅</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ ' . $subtotal / 0.05 . '</div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:100px; text-align:center; height:20px;float:left; display: inline-block; border-right: 1px solid;">註</div>
                        <div style="width:377px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">總計</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ ' . ($subtotal + ($subtotal / 0.05)) . '</div>
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
        $lasthist['FS'] = DB::table('m_historymovement')->select('ShipoutNumber')->distinct()->get();
        $lasthist['WH'] = DB::table('m_historymovement')->select('Warehouse')->distinct()->get();
        Session::forget('FormSeriesInv');
        Session::forget('WarehouseInv');
        return $lasthist;
    }

    static function postFS() {
        $sns = Input::get('sns');
        $lasthist['FS'] = DB::table('m_historymovement')->where('SN', 'LIKE', '%' . $sns . '%')->select('ShipoutNumber')->distinct()->get();
        $lasthist['WH'] = DB::table('m_historymovement')->select('Warehouse')->distinct()->get();
        Session::forget('FormSeriesInv');
        Session::forget('WarehouseInv');
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
//        $allinv = Inventory::all();
//        foreach ($allinv as $inv) {
//            $lastHist = History::where('ID', $inv->LastStatusID)->first();
//            $lastHist = $lastHist->Status;
//            $allhist = History::where('SN', $inv->SerialNumber)->get();
//            foreach ($allhist as $hist) {
//                $hist->LastStatus = $lastHist;
//                $hist->save();
//            }
//        }
//        dd('abc');
        $table = 'm_inventory';
        $filter = explode(',,,', $filter);
        $type = '> 0';
        $status = '>= 0';
        $fs = '';
        $wh = '';
        if (Session::has('FormSeriesInv'))
            $fs = Session::get('FormSeriesInv');
        if (Session::has('WarehouseInv'))
            $wh = Session::get('WarehouseInv');
        if ($filter[0] != 'all') {
            $type = '= ' . $filter[0];
        }
        if (isset($filter[1])) {
            $status = '= ' . $filter[1];
        }
        $primaryKey = 'm_inventory`.`SerialNumber';
        if ($fs != '') {
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
                    'db' => 'LastStatus',
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
                        } else if ($d == 4) {
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
            $extraCondition .= " && m_historymovement.LastStatus " . $status;
            if ($wh != '')
                $extraCondition .= " && m_inventory.LastWarehouse LIKE '%" . $wh . "%'";

            $extraCondition .= " && m_historymovement.ShipoutNumber LIKE '%" . $fs . "%'";
            $join = ' INNER JOIN m_historymovement on m_historymovement.SN = m_inventory.SerialNumber';
            echo json_encode(
                    SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
        }else {
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
                        } else if ($d == 4) {
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
            if ($wh != '')
                $extraCondition .= " && m_inventory.LastWarehouse LIKE '%" . $wh . "%'";
            $join = ' INNER JOIN m_historymovement on m_historymovement.ID = m_inventory.LastStatusID';
            echo json_encode(
                    SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
        }
    }

    static function delInv() {
        $allinvs = DB::table('m_inventory')
                        ->whereIn('SerialNumber', Session::get('temp_inv_arr'))->get();
        foreach ($allinvs as $upt_inv) {
            $need_update = Inventory::where('SerialNumber', $upt_inv->SerialNumber)->first();
            $need_update->TempPrice = 0;
            $need_update->save();
        }
        Session::forget('temp_inv_start');
        Session::forget('temp_inv_end');
        Session::forget('temp_inv_price');
        Session::forget('temp_inv_arr');
        Session::forget('temp_inv_qty');
    }

    static function addInv() {
        try {
            //BIKIN TYPE -> KALO PRICE AMA TYPE SAMA, QTY DIJUMLAH
            $start = Input::get('start');
            $end = Input::get('end');
            $price = Input::get('price');
            $arrInv = '';
            $qty = 0;
            $idx = 0;
            $redundant = false;
            $check_double = false;

            $allInv = Inventory::where('SerialNumber', '>=', $start)->where('SerialNumber', '<=', $end)->select('SerialNumber')->get();
            foreach ($allInv as $value) {
                if ($arrInv == '')
                    $arrInv = "'" . $value->SerialNumber . "'";
                else
                    $arrInv .= ',' . "'" . $value->SerialNumber . "'";
            }
            $qty = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.SerialNumber', '>=', $start)->where('m_inventory.SerialNumber', '<=', $end)
                            ->where('m_historymovement.Status', '!=', '2')->count();
            if ($qty == 0) {
                $redundant = true;
            }

            $cur_type = Inventory::where('SerialNumber', $start)->first()->Type;
            if (!$redundant) {
                $update_invs = DB::table('m_inventory')
                                ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                                ->where('m_inventory.SerialNumber', '>=', $start)->where('m_inventory.SerialNumber', '<=', $end)
                                ->where('m_historymovement.Status', '!=', '2')->get();
                foreach ($update_invs as $upt_inv) {
                    $need_update = Inventory::where('SerialNumber', $upt_inv->SerialNumber)->first();
                    $need_update->TempPrice = $price;
                    $need_update->save();
                }
                if (Session::has('temp_inv_start')) {
                    $last_inv = explode(',,,', Session::get('temp_inv_start'));
                    $counter = 0;
                    $temp_string_a = '';
                    foreach ($last_inv as $invs) {
                        if (strpos($invs, '+') !== false) {
                            $invs = explode('+', $invs)[0];
                        }
                        $inventories = Inventory::where('SerialNumber', $invs)->first()->Type;
                        if ($cur_type == $inventories) {
                            $last_price = explode(',,,', Session::get('temp_inv_price'));
                            if ($last_price[$counter] == $price) {
                                $last_inv[$counter] .= '+' . $start;
                                $idx = $counter;
                                $check_double = true;
                            }
                        }
                        if ($temp_string_a == '') {
                            $temp_string_a = $last_inv[$counter];
                        } else {
                            $temp_string_a .= ',,,' . $last_inv[$counter];
                        }
                        $counter++;
                    }
                    if (!$check_double) {
                        Session::put('temp_inv_start', Session::get('temp_inv_start') . ',,,' . $start);
                    } else {
                        Session::put('temp_inv_start', $temp_string_a);
                    }
                } else {
                    Session::put('temp_inv_start', $start);
                }
                if (Session::has('temp_inv_end')) {
                    if ($check_double) {
                        $last_inv = explode(',,,', Session::get('temp_inv_end'));
                        $last_inv[$idx] .= '+' . $end;
                        $temp_string_a = '';
                        foreach ($last_inv as $invs) {
                            if ($temp_string_a == '') {
                                $temp_string_a = $invs;
                            } else {
                                $temp_string_a .= ',,,' . $invs;
                            }
                        }
                        Session::put('temp_inv_end', $temp_string_a);
                    } else
                        Session::put('temp_inv_end', Session::get('temp_inv_end') . ',,,' . $end);
                } else {
                    Session::put('temp_inv_end', $end);
                }
                if (Session::has('temp_inv_price')) {
                    if (!$check_double)
                        Session::put('temp_inv_price', Session::get('temp_inv_price') . ',,,' . $price);
                } else {
                    Session::put('temp_inv_price', $price);
                }

                if (Session::has('temp_inv_qty')) {
                    if ($check_double) {
                        $last_inv = explode(',,,', Session::get('temp_inv_qty'));
                        $last_inv[$idx] += $qty;
                        $temp_string_a = '';
                        foreach ($last_inv as $invs) {
                            if ($temp_string_a == '') {
                                $temp_string_a = $invs;
                            } else {
                                $temp_string_a .= ',,,' . $invs;
                            }
                        }
                        Session::put('temp_inv_qty', $temp_string_a);
                    } else
                        Session::put('temp_inv_qty', Session::get('temp_inv_qty') . ',,,' . $qty);
                } else {
                    Session::put('temp_inv_qty', $qty);
                }
            }
            if (Session::has('temp_inv_arr')) {
                Session::put('temp_inv_arr', Session::get('temp_inv_arr') . ',' . $arrInv);
            } else {
                Session::put('temp_inv_arr', $arrInv);
            }
            return Session::get('temp_inv_start') . 'Qty: ' . Session::get('temp_inv_qty');
        } catch (Exception $e) {
            return ('Caught exception: ' . $e->getMessage() . "\n" . " The exception was created on line: " . $e->getLine());
        }
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
            array('db' => 'TempPrice', 'dt' => 4),
            array('db' => 'Date', 'dt' => 5),
            array('db' => 'MSISDN', 'dt' => 6),
            array('db' => 'SerialNumber', 'dt' => 7, 'formatter' => function( $d, $row ) {
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
        $extraCondition = "m_inventory.`SerialNumber` IN (" . Session::get('temp_inv_arr') . ")";
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
            $string_temp = '= 2';
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
                if ($hist != null)
                    $series = $hist->ShipoutNumber;
            }
            if ($serial == '0') {
                $serial = '';
            } else {
                $hist = History::where('SN', 'like', '%' . $serial . '%')->where('Status', 4)->orderBy('ID', 'desc')->first();
                if ($hist != null)
                    $series = $hist->ShipoutNumber;
            }
        }
        if ($msisdn == 0) {
            $msisdn = '';
        }
        if ($serial == 0) {
            $serial = '';
        }
        Session::put('snCons', $series);
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
