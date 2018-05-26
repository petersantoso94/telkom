<?php

class InventoryController extends BaseController {

    public function getSequence($num) {
        return sprintf("%'.19d\n", $num);
    }

    public function showInsertInventory2() { #sim
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                    $arr_sn = [];
                    $arr_msisdn = [];
                    $arr_sn = [];
                    $arr_hist_status = [];
                    $arr_shipinprice = [];
                    $arr_type = [];
                    $arr_lastwarehouse = [];
                    $arr_remark = [];
                    $arr_laststatusid = [];

                    //for history
                    $arr_sn_hist = [];
                    $arr_id_hist = [];
                    $arr_price_hist = [];
                    $arr_hist_date = [];
                    $arr_remark_hist = [];
                    $arr_subagent_hist = [];
                    $arr_shipoutnumber_hist = [];
                    $arr_status_hist = [];
                    $arr_laststatus_hist = [];
                    $arr_wh_hist = [];
                    $check_counter = History::select('ID')->orderBy('ID', 'DESC')->first();
                    if ($check_counter == null)
                        $id_counter = 1;
                    else
                        $id_counter = $check_counter->ID + 1;
                    foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                        if ($sheetIndex == 1)
                            foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                if ($rowNumber > 1) {
                                    if ($value[1] != null && $value[1] != '') {
                                        // do stuff with the row
                                        $type = 1;
                                        $wh = 'TELIN TAIWAN';
                                        $sn = (string) $value[1];
                                        $sn = strtoupper($sn);
                                        $remark_obj = $value[9];

                                        if (is_object($remark_obj)) {
                                            $remark_obj = $remark_obj->format('Y-m-d');
                                        }
                                        array_push($arr_sn, $sn);
                                        array_push($arr_msisdn, $value[2]);
                                        array_push($arr_shipinprice, $value[12]);
                                        if (strtolower($value[14]) == '4g') {
                                            $type = 4;
                                        }
                                        array_push($arr_type, $type);
                                        if ($value[4] != null && $value[4] != '') {
                                            $wh = $value[4];
                                        }
                                        array_push($arr_lastwarehouse, $wh);
                                        array_push($arr_remark, $remark_obj);

                                        //shipin
                                        $status = 0;
                                        array_push($arr_sn_hist, $sn);
                                        array_push($arr_id_hist, $id_counter);
                                        $date_shipin = $value[3];
                                        if (is_object($date_shipin)) {
                                            $date_shipin = $date_shipin->format('Y-m-d');
                                        } else {
                                            $date_shipin = strtotime($date_shipin);
                                            $date_shipin = date('Y-m-d', $date_shipin);
                                        }
                                        array_push($arr_hist_date, $date_shipin);
                                        array_push($arr_price_hist, $value[12]);
                                        array_push($arr_remark_hist, $remark_obj);
                                        $shipinNumber = $date_shipin . '/SI/TST001';
                                        array_push($arr_shipoutnumber_hist, $shipinNumber);
                                        array_push($arr_status_hist, $status);
                                        array_push($arr_subagent_hist, '-');
                                        array_push($arr_wh_hist, $wh);


                                        //there is shipout
                                        if ($value[7] != null && $value[7] != '') {
                                            $id_counter++;
                                            $status = 2;
                                            $tempSA = '';
                                            $tempSN = '/SO/';
                                            if ($value[11] != null) {
                                                $status = 4;
                                                $tempSN = '/CO/';
                                            }
                                            $subagent = $value[6];
                                            if ($subagent != null && $subagent != '') {
                                                $temp_sub = $value[8];
                                                if ($temp_sub != null && $temp_sub != '') {
                                                    $temp_sub2 = explode(' ', $temp_sub)[0];
                                                    if (strtolower($subagent) != strtolower($temp_sub2)) {
                                                        $subagent .= ' ' . $temp_sub;
                                                    } else {
                                                        if (isset(explode(' ', $temp_sub)[1]))
                                                            $subagent .= ' ' . explode(' ', $temp_sub)[1];
                                                    }
                                                }
                                                $tempSA = substr(explode(' ', $subagent)[0], 0, 3);
                                                if (strtolower(explode(' ', $subagent)[0]) == 'asprof') {
                                                    $tempSA = 'ASF';
                                                } else if (strtolower(explode(' ', $subagent)[0]) == 'asprot') {
                                                    $tempSA = 'AST';
                                                }
                                            }

                                            array_push($arr_sn_hist, $sn);
                                            array_push($arr_status_hist, $status);
                                            array_push($arr_laststatus_hist, $status);
                                            array_push($arr_id_hist, $id_counter);
                                            array_push($arr_price_hist, $value[13]);
                                            $date_shipout = $value[7];
                                            if (is_object($date_shipout)) {
                                                $date_shipout = $date_shipout->format('Y-m-d');
                                            } else {
                                                $date_shipout = strtotime($date_shipout);
                                                $date_shipout = date('Y-m-d', $date_shipout);
                                            }
                                            $statusnum = $date_shipout . $tempSN;
                                            $statusnum .= $tempSA;
                                            $statusnum .= '001';
                                            array_push($arr_shipoutnumber_hist, $statusnum);
                                            array_push($arr_hist_date, $date_shipout);
                                            array_push($arr_remark_hist, $remark_obj);
                                            array_push($arr_subagent_hist, $subagent);
                                            array_push($arr_wh_hist, $wh);
                                        }
                                        array_push($arr_hist_status, $status);
                                        array_push($arr_laststatus_hist, $status);
                                        array_push($arr_laststatusid, $id_counter);
                                        $id_counter++;
                                    }
                                }
                            }
                    }
                    $reader->close();
                    $for_raw = '';
                    for ($i = 0; $i < count($arr_sn); $i++) {
                        if ($i == 0)
                            $for_raw .= "('" . $arr_sn[$i] . "','" . $arr_shipinprice[$i] . "',0,0,'" . $arr_laststatusid[$i] . "','" . $arr_hist_status[$i] . "','" . $arr_lastwarehouse[$i] . "','" . $arr_type[$i] . "','" . $arr_msisdn[$i] . "','TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                        else
                            $for_raw .= ",('" . $arr_sn[$i] . "','" . $arr_shipinprice[$i] . "',0,0,'" . $arr_laststatusid[$i] . "','" . $arr_hist_status[$i] . "','" . $arr_lastwarehouse[$i] . "','" . $arr_type[$i] . "','" . $arr_msisdn[$i] . "','TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                    }
                    DB::insert("INSERT INTO m_inventory VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE SerialNumber=SerialNumber;");

                    $for_raw = '';
                    for ($i = 0; $i < count($arr_id_hist); $i++) {
                        if ($i == 0)
                            $for_raw .= "('" . $arr_id_hist[$i] . "','" . $arr_sn_hist[$i] . "','" . $arr_subagent_hist[$i] . "','" . $arr_wh_hist[$i] . "','" . $arr_price_hist[$i] . "','" . $arr_shipoutnumber_hist[$i] . "',NULL,'" . $arr_status_hist[$i] . "','" . $arr_laststatus_hist[$i] . "',0,'" . $arr_hist_date[$i] . "','" . $arr_remark_hist[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                        else
                            $for_raw .= ",('" . $arr_id_hist[$i] . "','" . $arr_sn_hist[$i] . "','" . $arr_subagent_hist[$i] . "','" . $arr_wh_hist[$i] . "','" . $arr_price_hist[$i] . "','" . $arr_shipoutnumber_hist[$i] . "',NULL,'" . $arr_status_hist[$i] . "','" . $arr_laststatus_hist[$i] . "',0,'" . $arr_hist_date[$i] . "','" . $arr_remark_hist[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                    }
                    DB::insert("INSERT INTO m_historymovement VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE ID=ID;");


                    return View::make('insertinventory')->withResponse('Success')->withPage('insert inventory')->withNumber(count($arr_sn));
                }
            }
            return View::make('insertinventory')->withResponse('Failed')->withPage('insert inventory');
        }
        return View::make('insertinventory')->withPage('insert inventory');
    }

    public function showInsertInventory4() { #vocher
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                    $arr_sn = [];
                    $arr_hist_status = [];
                    $arr_shipinprice = [];
                    $arr_type = [];
                    $arr_lastwarehouse = [];
                    $arr_remark = [];
                    $arr_laststatusid = [];

                    //for history
                    $arr_sn_hist = [];
                    $arr_id_hist = [];
                    $arr_price_hist = [];
                    $arr_hist_date = [];
                    $arr_remark_hist = [];
                    $arr_subagent_hist = [];
                    $arr_shipoutnumber_hist = [];
                    $arr_status_hist = [];
                    $arr_laststatus_hist = [];
                    $arr_wh_hist = [];
                    $check_counter = History::select('ID')->orderBy('ID', 'DESC')->first();
                    if ($check_counter == null)
                        $id_counter = 1;
                    else
                        $id_counter = $check_counter->ID + 1;
                    foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                        if ($sheetIndex == 1)
                            foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                if ($rowNumber > 1) {
                                    if ($value[0] != null && $value[0] != '') {
                                        // do stuff with the row
                                        $type = 2;
                                        $wh = 'TELIN TAIWAN';
                                        $sn = (string) $value[0];
                                        $sn = strtoupper($sn);

                                        $remark_obj = $value[9];

                                        if (is_object($remark_obj)) {
                                            $remark_obj = $remark_obj->format('Y-m-d');
                                        }
                                        array_push($arr_sn, $sn);
                                        array_push($arr_shipinprice, $value[13]);
                                        if (substr($value[0], 0, 6) == 'KR0350' || substr($value[0], 0, 6) == 'KR1850') {
                                            $type = 3;
                                        }
                                        array_push($arr_type, $type);
                                        if ($value[4] != null && $value[4] != '') {
                                            $wh = $value[4];
                                        }
                                        array_push($arr_lastwarehouse, $wh);
                                        array_push($arr_remark, $remark_obj);

                                        //shipin
                                        $status = 0;
                                        array_push($arr_sn_hist, $sn);
                                        array_push($arr_id_hist, $id_counter);
                                        $date_shipin = $value[3];
                                        if (is_object($date_shipin)) {
                                            $date_shipin = $date_shipin->format('Y-m-d');
                                        } else {
                                            $date_shipin = strtotime($date_shipin);
                                            $date_shipin = date('Y-m-d', $date_shipin);
                                        }
                                        array_push($arr_hist_date, $date_shipin);
                                        array_push($arr_price_hist, $value[13]);
                                        array_push($arr_remark_hist, $remark_obj);
                                        $shipinNumber = $date_shipin . '/SI/TST001';
                                        array_push($arr_shipoutnumber_hist, $shipinNumber);
                                        array_push($arr_status_hist, $status);
                                        array_push($arr_subagent_hist, '-');
                                        array_push($arr_wh_hist, $wh);


                                        //there is shipout
                                        if ($value[7] != null && $value[7] != '') {
                                            $id_counter++;
                                            $status = 2;
                                            $tempSA = '';
                                            $tempSN = '/SO/';
                                            if ($value[15] != null) {
                                                $status = 4;
                                                $tempSN = '/CO/';
                                            }
                                            $subagent = $value[6];
                                            if ($subagent != null && $subagent != '') {
                                                $temp_sub = $value[8];
                                                if ($temp_sub != null && $temp_sub != '') {
                                                    $temp_sub2 = explode(' ', $temp_sub)[0];
                                                    if (strtolower($subagent) != strtolower($temp_sub2)) {
                                                        $subagent .= ' ' . $temp_sub;
                                                    } else {
                                                        if (isset(explode(' ', $temp_sub)[1]))
                                                            $subagent .= ' ' . explode(' ', $temp_sub)[1];
                                                    }
                                                }
                                                $tempSA = substr(explode(' ', $subagent)[0], 0, 3);
                                                if (strtolower(explode(' ', $subagent)[0]) == 'asprof') {
                                                    $tempSA = 'ASF';
                                                } else if (strtolower(explode(' ', $subagent)[0]) == 'asprot') {
                                                    $tempSA = 'AST';
                                                }
                                            }

                                            array_push($arr_sn_hist, $sn);
                                            array_push($arr_status_hist, $status);
                                            array_push($arr_laststatus_hist, $status);
                                            array_push($arr_id_hist, $id_counter);
                                            array_push($arr_price_hist, $value[14]);
                                            $date_shipout = $value[7];
                                            if (is_object($date_shipout)) {
                                                $date_shipout = $date_shipout->format('Y-m-d');
                                            } else {
                                                $date_shipout = strtotime($date_shipout);
                                                $date_shipout = date('Y-m-d', $date_shipout);
                                            }
                                            $statusnum = $date_shipout . $tempSN;
                                            $statusnum .= $tempSA;
                                            $statusnum .= '001';
                                            array_push($arr_shipoutnumber_hist, $statusnum);
                                            array_push($arr_hist_date, $date_shipout);
                                            array_push($arr_remark_hist, $remark_obj);
                                            array_push($arr_subagent_hist, $subagent);
                                            array_push($arr_wh_hist, $wh);
                                        }
                                        array_push($arr_hist_status, $status);
                                        array_push($arr_laststatus_hist, $status);
                                        array_push($arr_laststatusid, $id_counter);
                                        $id_counter++;
                                    }
                                }
                            }
                    }
                    $reader->close();
                    $for_raw = '';
                    for ($i = 0; $i < count($arr_sn); $i++) {
                        if ($i == 0)
                            $for_raw .= "('" . $arr_sn[$i] . "','" . $arr_shipinprice[$i] . "',0,0,'" . $arr_laststatusid[$i] . "','" . $arr_hist_status[$i] . "','" . $arr_lastwarehouse[$i] . "','" . $arr_type[$i] . "',NULL,'TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                        else
                            $for_raw .= ",('" . $arr_sn[$i] . "','" . $arr_shipinprice[$i] . "',0,0,'" . $arr_laststatusid[$i] . "','" . $arr_hist_status[$i] . "','" . $arr_lastwarehouse[$i] . "','" . $arr_type[$i] . "',NULL,'TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                    }
                    DB::insert("INSERT INTO m_inventory VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE SerialNumber=SerialNumber;");

                    $for_raw = '';
                    for ($i = 0; $i < count($arr_id_hist); $i++) {
                        if ($i == 0)
                            $for_raw .= "('" . $arr_id_hist[$i] . "','" . $arr_sn_hist[$i] . "','" . $arr_subagent_hist[$i] . "','" . $arr_wh_hist[$i] . "','" . $arr_price_hist[$i] . "','" . $arr_shipoutnumber_hist[$i] . "',NULL,'" . $arr_status_hist[$i] . "','" . $arr_laststatus_hist[$i] . "',0,'" . $arr_hist_date[$i] . "','" . $arr_remark_hist[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                        else
                            $for_raw .= ",('" . $arr_id_hist[$i] . "','" . $arr_sn_hist[$i] . "','" . $arr_subagent_hist[$i] . "','" . $arr_wh_hist[$i] . "','" . $arr_price_hist[$i] . "','" . $arr_shipoutnumber_hist[$i] . "',NULL,'" . $arr_status_hist[$i] . "','" . $arr_laststatus_hist[$i] . "',0,'" . $arr_hist_date[$i] . "','" . $arr_remark_hist[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                    }
                    DB::insert("INSERT INTO m_historymovement VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE ID=ID;");


                    return View::make('insertinventory')->withResponse('Success')->withPage('insert inventory')->withNumber(count($arr_sn));
                }
            }
            return View::make('insertinventory')->withResponse('Failed')->withPage('insert inventory');
        }
        return View::make('insertinventory')->withPage('insert inventory');
    }

    public function showInsertInventory33() { // find missing msisdn
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = Input::file('sample_file');
            if ($input != '') {
                if (Input::hasFile('sample_file')) {
                    $destination = base_path() . '/uploaded_file/';
                    $extention = Input::file('sample_file')->getClientOriginalExtension();
                    $filename = 'temp.' . $extention;
                    Input::file('sample_file')->move($destination, $filename);
                    $filePath = base_path() . '/uploaded_file/' . 'temp.' . $extention;
                    $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
                    $reader->open($filePath);
                    $counter = 0;
                    $arr_msisdn = [];
                    $arr_buydate = [];
                    $arr_buy = [];
                    foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                        if ($sheetIndex == 1)
                            foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                if ($rowNumber > 2) {
                                    // do stuff with the row
                                    $msisdn = (string) $value[0];

                                    if ($msisdn != '' && $msisdn != null) {
                                        $msisdn = str_replace('\'', '', $msisdn);
                                        if (substr($msisdn, 0, 1) === '0') {
                                            $msisdn = substr($msisdn, 1);
                                        }
                                        array_push($arr_msisdn, $msisdn);
                                    }
                                }
                            }
                    }
                    $reader->close();
                    $check_msisdn = [];
                    $ids = $arr_msisdn;
                    $ids = implode("','", $ids);
                    $right_msisdn = DB::select("SELECT SerialNumber FROM `m_inventory`");
                    foreach ($right_msisdn as $msisdn) {
                        $check_msisdn[] = $msisdn->MSISDN;
                    }
                    $not_found = array_diff($arr_msisdn, $check_msisdn);
                    $not_found = implode(",", $not_found);
                    dd($not_found);
                }
            }
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
                    $filePath = base_path() . '/uploaded_file/' . 'temp.' . $extention;
                    $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
//$reader = ReaderFactory::create(Type::CSV); // for CSV files
//$reader = ReaderFactory::create(Type::ODS); // for ODS files

                    $reader->open($filePath);
                    $counter = 0;
                    $arr_sn = [];
                    $arr_msisdn = [];
                    $arr_sn = [];
                    $arr_shipinprice = [];
                    $arr_type = [];
                    $arr_lastwarehouse = [];
                    $arr_remark = [];
                    $arr_laststatusid = [];

                    //for history
                    $arr_sn_hist = [];
                    $arr_id_hist = [];
                    $arr_price_hist = [];
                    $arr_hist_date = [];
                    $arr_remark_hist = [];
                    $arr_subagent_hist = [];
                    $arr_shipoutnumber_hist = [];
                    $arr_status_hist = [];
                    $arr_laststatus_hist = [];
                    $arr_wh_hist = [];
                    $check_counter = History::select('ID')->orderBy('ID', 'DESC')->first();
                    if ($check_counter == null)
                        $id_counter = 1;
                    else
                        $id_counter = $check_counter->ID + 1;

                    foreach ($reader->getSheetIterator() as $sheet) {
                        foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                            if ($rowNumber > 1) {
                                if ($value[0] != null && $value[0] != '') {
                                    // do stuff with the row
                                    $type = $value[2];
                                    if ($type === '1' || $type === '4') {
                                        
                                    } else {
                                        
                                    }
                                    $wh = Input::get('warehouse', false);
                                    $sn = (string) $value[0];
                                    array_push($arr_sn, $sn);
                                    array_push($arr_type, $type);
                                    array_push($arr_msisdn, $value[1]);
                                    array_push($arr_lastwarehouse, $wh);
                                    array_push($arr_remark, Input::get('remark', false));

                                    //shipin
                                    $status = 0;
                                    array_push($arr_sn_hist, $sn);
                                    array_push($arr_id_hist, $id_counter);
                                    $date_shipin = Input::get('eventDate', false);
                                    array_push($arr_hist_date, $date_shipin);
                                    array_push($arr_remark_hist, Input::get('remark', false));
                                    $shipinNumber = Input::get('formSN', false);
                                    array_push($arr_shipoutnumber_hist, $shipinNumber);
                                    array_push($arr_status_hist, $status);
                                    array_push($arr_subagent_hist, '-');
                                    array_push($arr_wh_hist, $wh);

                                    array_push($arr_laststatus_hist, $status);
                                    array_push($arr_laststatusid, $id_counter);
                                    $id_counter++;
                                }
                            }
                        }
                    }
                    $reader->close();
                    $for_raw = '';
                    for ($i = 0; $i < count($arr_sn); $i++) {
                        if ($i == 0)
                            $for_raw .= "('" . $arr_sn[$i] . "',0,0,0,'" . $arr_laststatusid[$i] . "','" . $arr_laststatus_hist[$i] . "','" . $arr_lastwarehouse[$i] . "','" . $arr_type[$i] . "','" . $arr_msisdn[$i] . "','TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                        else
                            $for_raw .= ",('" . $arr_sn[$i] . "',0,0,0,'" . $arr_laststatusid[$i] . "','" . $arr_laststatus_hist[$i] . "','" . $arr_lastwarehouse[$i] . "','" . $arr_type[$i] . "','" . $arr_msisdn[$i] . "','TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                    }
                    DB::insert("INSERT INTO m_inventory VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE SerialNumber=SerialNumber;");

                    $for_raw = '';
                    for ($i = 0; $i < count($arr_id_hist); $i++) {
                        if ($i == 0)
                            $for_raw .= "('" . $arr_id_hist[$i] . "','" . $arr_sn_hist[$i] . "','" . $arr_subagent_hist[$i] . "','" . $arr_wh_hist[$i] . "',0,'" . $arr_shipoutnumber_hist[$i] . "',NULL,'" . $arr_status_hist[$i] . "','" . $arr_laststatus_hist[$i] . "',0,'" . $arr_hist_date[$i] . "','" . $arr_remark_hist[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                        else
                            $for_raw .= ",('" . $arr_id_hist[$i] . "','" . $arr_sn_hist[$i] . "','" . $arr_subagent_hist[$i] . "','" . $arr_wh_hist[$i] . "',0,'" . $arr_shipoutnumber_hist[$i] . "',NULL,'" . $arr_status_hist[$i] . "','" . $arr_laststatus_hist[$i] . "',0,'" . $arr_hist_date[$i] . "','" . $arr_remark_hist[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                    }
                    DB::insert("INSERT INTO m_historymovement VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE ID=ID;");


                    return View::make('insertinventory')->withResponse('Success')->withPage('insert inventory')->withNumber(count($arr_sn));
                }
            }
            return View::make('insertinventory')->withResponse('Failed')->withPage('insert inventory');
        }
        return View::make('insertinventory')->withPage('insert inventory');
    }

    public function showDashboard() {
        $years = DB::table('r_stats')->select('Year')->orderBy('Year', 'DESC')->distinct()->get();
        Session::put('UserFilterAct', 0);
        Session::put('UserFilterv300', 0);
        Session::put('UserFilterv100', 0);
        Session::put('UserFilterService', 0);
        return View::make('dashboard')->withPage('dashboard')->withYears($years);
    }

    public function showWarehouseInventory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstsn = Input::get('shipoutstart');
            $lastsn = Input::get('shipoutend');
            $moveto = Input::get('moveto');
            $newwh = '';
            if (Input::get('NewWarehouse') != '') {
                $newwh = Input::get('NewWarehouse');
            }
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
                    $hist->SubAgent = '';
                    $hist->Remark = Input::get('remark');
                    $hist->userRecord = Auth::user()->ID;
                    $hist->save();

                    //update last status
                    $inv->LastStatusID = $hist->ID;
                    $inv->LastStatusHist = 3;
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
            $fabiaoNumber = Input::get('fabiaoNumber');
            $cs = Session::get('conses');
            $invs = str_replace("'", '', Session::get('temp_inv_arr'));
            $arr_inv = explode(',', $invs);

            //for history
            $arr_sn_hist = [];
            $arr_id_hist = [];
            $arr_price_hist = [];
            $arr_hist_date = [];
            $arr_remark_hist = [];
            $arr_subagent_hist = [];
            $arr_shipoutnumber_hist = [];
            $arr_status_hist = [];
            $arr_laststatus_hist = [];
            $arr_wh_hist = [];

            $counter = 0;
            $check_counter = History::select('ID')->orderBy('ID', 'DESC')->first();
            if ($check_counter == null)
                $id_counter = 1;
            else
                $id_counter = $check_counter->ID + 1;

            $allInvAvail = Inventory::join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')->whereIn('m_inventory.SerialNumber', $arr_inv)
                    ->where('m_historymovement.Status', '!=', '2')->where('m_inventory.Missing', 0)
                    ->get();
            $status = 2;
            if ($cs == 1) {
                $status = 4;
            }
            foreach ($allInvAvail as $inv) {

                array_push($arr_sn_hist, $inv->SerialNumber);
                array_push($arr_status_hist, $status);
                array_push($arr_laststatus_hist, $status);
                array_push($arr_id_hist, $id_counter);
                array_push($arr_price_hist, $inv->TempPrice);
                $date_shipout = Input::get('eventDate');
                $statusnum = $series;
                array_push($arr_shipoutnumber_hist, $statusnum);
                array_push($arr_hist_date, $date_shipout);
                array_push($arr_remark_hist, Input::get('remark'));
                array_push($arr_subagent_hist, $subagent);
                array_push($arr_wh_hist, $inv->LastWarehouse);

//                //update last status
//                $inv->LastStatusID = $id_counter;
//                $inv->save();
//                
//                $allhist = History::where('SN', $inv->SerialNumber)->get();
//                foreach ($allhist as $hist) {
//                    $hist->LastStatus = $status;
//                    $hist->save();
//                }
                $id_counter++;
                $counter++;
            }

            $for_raw = '';
            for ($i = 0; $i < count($arr_id_hist); $i++) {
                if ($i == 0)
                    $for_raw .= "('" . $arr_id_hist[$i] . "','" . $arr_sn_hist[$i] . "','" . $arr_subagent_hist[$i] . "','" . $arr_wh_hist[$i] . "','" . $arr_price_hist[$i] . "','" . $arr_shipoutnumber_hist[$i] . "','{$fabiaoNumber}','" . $arr_status_hist[$i] . "','" . $arr_laststatus_hist[$i] . "',0,'" . $arr_hist_date[$i] . "','" . $arr_remark_hist[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                else
                    $for_raw .= ",('" . $arr_id_hist[$i] . "','" . $arr_sn_hist[$i] . "','" . $arr_subagent_hist[$i] . "','" . $arr_wh_hist[$i] . "','" . $arr_price_hist[$i] . "','" . $arr_shipoutnumber_hist[$i] . "','{$fabiaoNumber}','" . $arr_status_hist[$i] . "','" . $arr_laststatus_hist[$i] . "',0,'" . $arr_hist_date[$i] . "','" . $arr_remark_hist[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
            }
            DB::insert("INSERT INTO m_historymovement VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE ID=ID;");

//            $table = Inventory::getModel()->getTable();
//            $cases = [];
//            $ids = [];
//            $params = [];
//
//            for ($i = 0; $i < count($arr_sn_hist); $i++) {
//                $id = (int) $arr_sn_hist[$i];
//                $cases[] = "WHEN {$id} then ?";
//                $params[] = $arr_id_hist[$i];
//                $ids[] = $id;
//            }
//            $ids = implode(',', $ids);
//            $cases = implode(' ', $cases);
//            DB::update("UPDATE `{$table}` SET `LastStatusID` = CASE `SerialNumber` {$cases} END WHERE `SerialNumber` in ({$ids})", $params);
//            
//            for ($i = 0; $i < count($arr_sn_hist); $i++) {
//                DB::update("UPDATE `m_historymovement` SET `LastStatus` = '{$arr_laststatus_hist[$i]}'  WHERE `SN` = '{$arr_sn_hist[$i]}'");
//            }
//            
//            $cases = [];
//            $ids = [];
//            $params = [];
//
//            for ($i = 0; $i < count($arr_sn_hist); $i++) {
//                $id = (int) $arr_sn_hist[$i];
//                $cases[] = "WHEN {$id} then ?";
//                $params[] = $arr_laststatus_hist[$i];
//                $ids[] = $id;
//            }
//            $ids = implode(',', $ids);
//            $cases = implode(' ', $cases);
//            DB::update("UPDATE `m_historymovement` SET `LastStatus` = CASE `SN` {$cases} END WHERE `SN` in ({$ids})", $params);

            Session::forget('temp_inv_start');
            Session::forget('temp_inv_end');
            Session::forget('temp_inv_price');
            Session::forget('temp_inv_arr');
            Session::forget('temp_inv_qty');
            Session::forget('conses');
            return View::make('shipout')->withResponse('Success')->withPage('inventory shipout')->withNumber($counter);
        }
        Session::forget('temp_inv_start');
        Session::forget('temp_inv_end');
        Session::forget('temp_inv_price');
        Session::forget('temp_inv_arr');
        Session::forget('temp_inv_qty');
        Session::forget('conses');
        return View::make('shipout')->withPage('inventory shipout');
    }

    public function showConsignment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstsn = Input::get('shipoutstart');
            $lastsn = Input::get('shipoutend');
            $price = Input::get('price');
            $series = Input::get('formSN');
            $subagent = Input::get('subagent');
            $shipoutNumber = '';
            if (Input::get('newagent') != '') {
                $subagent = Input::get('newagent');
            }
            if (Session::has('snCons'))
                $shipoutNumber = Session::get('snCons');
            $counter = 0;
            //$allInvAvail = Inventory::whereBetween('SerialNumber', [$firstsn, $lastsn])->where('Missing', 0)->get();
            $allInvAvail = Inventory::join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                            ->where('m_inventory.Missing', 0)
                            ->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . $shipoutNumber . '%')->get();
            foreach ($allInvAvail as $inv) {
                $history = History::where('ID', $inv->LastStatusID)->first();
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
                    $inv->LastStatusHist = 2;
                    $inv->save();
                    $counter++;
                }
            }
            $allInvMiss = Inventory::join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                            ->where('m_inventory.Missing', 1)
                            ->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . $shipoutNumber . '%')->get();
            foreach ($allInvMiss as $inv) {
                $inv->Missing = 0;
                $inv->save();
            }
            Session::forget('snCons');
            return View::make('consignment')->withResponse('Success')->withPage('shipout consignment')->withNumber($counter);
        }
        Session::forget('snCons');
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
            $date = Input::get('eventDate', false);
            $fn = Input::get('formSN', false);
            $remark = NULL;
            if (Input::get('remark')) {
                $remark = Input::get('remark', false);
            }
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
                            if ($hist->Status == 2 || $hist->Status == 4) {
                                $counter++;
                                if ($successins == '') {
                                    $successins .= $value[0];
                                } else {
                                    $successins .= ', ' . $value[0];
                                }

                                //can return, update

                                $hist2 = new History();
                                $hist2->SN = $inv->SerialNumber;
                                $hist2->Price = 0;
                                $hist2->ShipoutNumber = $fn;
                                $hist2->Status = 1;
                                $hist2->SubAgent = $hist->SubAgent;
                                $hist2->Warehouse = $inv->LastWarehouse;
                                $hist2->LastStatus = 1;
                                $hist2->Remark = Input::get('remark', false);
                                $hist2->Date = Input::get('eventDate', false);
                                $hist2->userRecord = Auth::user()->ID;
                                $hist2->userUpdate = Auth::user()->ID;
                                $hist2->save();

                                $inv->LastStatusID = $hist2->ID;
                                $inv->LastStatusHist = 1;
                                $inv->save();
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
            $reader->close();
            return View::make('returninventory')->withResponse('Success')->withPage('inventory return')
                            ->withNumber($counter)->withNumberf($counterfail)->withFail($nodata)->withSucc($successins)->withNoav($notavail);
        }
        return View::make('returninventory')->withPage('inventory return');
    }

    public function showUncat() {
        return View::make('uncatagorized')->withPage('Uncatagorized Inventory');
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
                    $data->SubAgent = $newname;
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

    public function showResetReporting() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (Input::get('jenis') == 'reset_churn') {
                DB::delete('DELETE FROM `r_stats` WHERE Status LIKE "%churn%" OR Status LIKE "%chact%"');
                DB::update('UPDATE `m_inventory` SET `ChurnDate`=NULL WHERE 1');
                return View::make('resetreporting')->withPage('reset reporting')->withSuccess('ok');
            } else if (Input::get('jenis') == 'reset_prod') {
                DB::delete('DELETE FROM `r_stats` WHERE Status LIKE "%_sum%" OR Status LIKE "%services%"');
                DB::delete('DELETE FROM `m_productive` WHERE 1');
                return View::make('resetreporting')->withPage('reset reporting')->withSuccessp('ok');
            } else if (Input::get('jenis') == 'reset_ivr') {
                DB::delete('DELETE FROM `r_stats` WHERE Status > 10');
                DB::delete('DELETE FROM `m_ivr` WHERE 1');
                return View::make('resetreporting')->withPage('reset reporting')->withSuccessi('ok');
            } else if (Input::get('jenis') == 'reset_act') {
                DB::delete('DELETE FROM `r_stats` WHERE Status LIKE "%activation%"');
                DB::update('UPDATE `m_inventory` SET `ActivationDate`=NULL WHERE 1');
                return View::make('resetreporting')->withPage('reset reporting')->withSuccessa('ok');
            } else if (Input::get('jenis') == 'reset_top') {
                DB::delete('DELETE FROM `r_stats` WHERE Status LIKE "%topup%"');
                DB::update('UPDATE `m_inventory` SET `TopUpDate`=NULL, TopUpMSISDN=NULL WHERE 1');
                return View::make('resetreporting')->withPage('reset reporting')->withSuccesst('ok');
            }
        }
        return View::make('resetreporting')->withPage('reset reporting');
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
                        $arr_msisdn = [];
                        $arr_buydate = [];
                        $arr_buy = [];
                        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                            if ($sheetIndex == 1)
                                foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                    if ($rowNumber > 2) {
                                        // do stuff with the row
                                        $msisdn = (string) $value[3];

                                        if ($msisdn != '' && $msisdn != null) {
                                            $msisdn = str_replace('\'', '', $msisdn);
                                            if (substr($msisdn, 0, 1) === '0') {
                                                $msisdn = substr($msisdn, 1);
                                            }
                                            array_push($arr_msisdn, $msisdn);
                                            $date_return = $value[2];
                                            if (is_object($date_return)) {
                                                $date_return = $date_return->format('Y-m-d');
                                            } else {
                                                $date_return = strtotime($date_return);
                                                $date_return = date('Y-m-d', $date_return);
                                            }
                                            if (substr($date_return, 0, 4) === '1970') {
                                                $date_return = $value[2];
                                                $date_return = explode('/', $date_return);
                                                $date_return = $date_return[1] . '/' . $date_return[0] . '/' . $date_return[2];
                                                $date_return = strtotime($date_return);
                                                $date_return = date('Y-m-d', $date_return);
                                            }
                                            array_push($arr_buydate, $date_return);
                                            array_push($arr_buy, $value[5]);
                                        }
                                    }
                                }
                        }
                        $reader->close();
                        $check_msisdn = [];
                        $ids = $arr_msisdn;
                        $ids = implode("','", $ids);
                        $right_msisdn = DB::select("SELECT `MSISDN` FROM `m_inventory` WHERE `MSISDN` in ('{$ids}')");
                        foreach ($right_msisdn as $msisdn) {
                            $check_msisdn[] = $msisdn->MSISDN;
                        }
                        $not_found = array_diff($arr_msisdn, $check_msisdn);
                        $not_found = implode(",", $not_found);
                        $not_found = explode(",", $not_found);
                        if (count($not_found) > 0) {
                            $for_raw = '';
                            for ($i = 0; $i < count($not_found); $i++) {
                                if ($i == 0)
                                    $for_raw .= "(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from ivr file')";
                                else
                                    $for_raw .= ",(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from ivr file')";
                            }
                            DB::insert("INSERT INTO m_uncatagorized VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE MSISDN=MSISDN;");
                        }
                        $for_raw = '';
                        for ($i = 0; $i < count($arr_msisdn); $i++) {
                            $unik = $arr_msisdn[$i] . '-' . $arr_buydate[$i] . '-' . $arr_buy[$i];
                            if ($i == 0)
                                $for_raw .= "('" . $arr_msisdn[$i] . "','" . $arr_buydate[$i] . "','" . $unik . "','" . $arr_buy[$i] . "',CURDATE(),CURDATE(),'-','" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                            else
                                $for_raw .= ",('" . $arr_msisdn[$i] . "','" . $arr_buydate[$i] . "','" . $unik . "','" . $arr_buy[$i] . "',CURDATE(),CURDATE(),'-','" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                        }
                        DB::insert("INSERT INTO m_ivr VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE Unik=Unik;");
                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumber(count($arr_msisdn));
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
                        $filePath = base_path() . '/uploaded_file/' . 'temp.' . $extention;
                        $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::XLSX);
                        $reader->setShouldFormatDates(true);
                        $counter = 0;
                        $arr_msisdn = [];
                        $arr_date = [];
                        $reader->open($filePath);
                        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                            if ($sheetIndex == 1)
                                foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                    if ($rowNumber > 1) {
                                        // do stuff with the row
                                        $msisdn = (string) $value[0];
                                        if ($msisdn != '' && $msisdn != null) {
                                            $date_return = $value[1];
                                            if ($date_return != '' && $date_return != null) {
                                                $date_return = strtotime($date_return);
                                                $date_return = date('Y-m-d', $date_return);
                                                if (substr($date_return, 0, 4) === '1970') {
                                                    $date_return = $value[1];
                                                    $date_return = explode('/', $date_return);

                                                    try {
                                                        $date_return = $date_return[1] . '/' . $date_return[0] . '/' . $date_return[2];
                                                    } catch (Exception $e) {
                                                        dd($date_return);
                                                    }
                                                    $date_return = strtotime($date_return);
                                                    $date_return = date('Y-m-d', $date_return);
                                                }
                                                array_push($arr_date, $date_return);

                                                $msisdn = str_replace('\'', '', $msisdn);
                                                if (substr($msisdn, 0, 1) === '0') {
                                                    $msisdn = substr($msisdn, 1);
                                                }
                                                array_push($arr_msisdn, $msisdn);
                                            }
                                        }
                                    }
                                }
                        }

                        $reader->close();
                        $check_msisdn = [];
                        $ids = $arr_msisdn;
                        $ids = implode("','", $ids);
                        $right_msisdn = DB::select("SELECT `MSISDN` FROM `m_inventory` WHERE `MSISDN` in ('{$ids}')");
                        foreach ($right_msisdn as $msisdn) {
                            $check_msisdn[] = $msisdn->MSISDN;
                        }
                        $not_found = array_diff($arr_msisdn, $check_msisdn);
                        $not_found = implode(",", $not_found);
                        $not_found = explode(",", $not_found);
                        if (count($not_found) > 0) {
                            $for_raw = '';
                            for ($i = 0; $i < count($not_found); $i++) {
                                if ($i == 0)
                                    $for_raw .= "(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from apf file')";
                                else
                                    $for_raw .= ",(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from apf file')";
                            }
                            DB::insert("INSERT INTO m_uncatagorized VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE MSISDN=MSISDN;");
                        }
                        $table = Inventory::getModel()->getTable();
                        $cases = [];
                        $ids = [];
                        $params = [];
                        $counter = count($arr_msisdn);

                        for ($i = 0; $i < count($arr_msisdn); $i++) {
                            $id = (int) $arr_msisdn[$i];
                            $cases[] = "WHEN {$id} then ?";
                            $params[] = $arr_date[$i];
                            $ids[] = $id;
                        }
                        $ids = implode(',', $ids);
                        $cases = implode(' ', $cases);
                        DB::update("UPDATE `{$table}` SET `ApfDate` = CASE `MSISDN` {$cases} END WHERE `MSISDN` in ({$ids})", $params);
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
                        $filePath = base_path() . '/uploaded_file/' . 'temp.' . $extention;
                        if ($extention == 'csv') {
                            $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::CSV);
                            $reader->setShouldFormatDates(true);
                            $counter = 0;
                            $reader->open($filePath);
                            $arr_msisdn = [];
                            $arr_act_store = [];
                            foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                                if ($sheetIndex == 1)
                                    foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                        if ($rowNumber > 1) {
                                            // do stuff with the row
                                            $msisdn = (string) $value[14];
                                            if ($msisdn != '' && $msisdn != null) {
                                                $msisdn = str_replace(' ', '', $msisdn);
                                                $msisdn = str_replace('\'', '', $msisdn);
                                                if (substr($msisdn, 0, 1) === '0') {
                                                    $msisdn = substr($msisdn, 1);
                                                }
                                                array_push($arr_msisdn, $msisdn);
                                                array_push($arr_act_store, $value[6]);
                                            }
                                        }
                                    }
                            }
                            $reader->close();
                            $check_msisdn = [];
                            $ids = $arr_msisdn;
                            $ids = implode("','", $ids);
                            $right_msisdn = DB::select("SELECT `MSISDN` FROM `m_inventory` WHERE `MSISDN` in ('{$ids}')");
                            foreach ($right_msisdn as $msisdn) {
                                $check_msisdn[] = $msisdn->MSISDN;
                            }
                            $not_found = array_diff($arr_msisdn, $check_msisdn);
                            $not_found = implode(",", $not_found);
                            $not_found = explode(",", $not_found);
                            if (count($not_found) > 0) {
                                $for_raw = '';
                                for ($i = 0; $i < count($not_found); $i++) {
                                    if ($i == 0)
                                        $for_raw .= "(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from acquisition file')";
                                    else
                                        $for_raw .= ",(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from acquisition file')";
                                }
                                DB::insert("INSERT INTO m_uncatagorized VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE MSISDN=MSISDN;");
                            }
                            $table = Inventory::getModel()->getTable();

                            $counter = count($arr_msisdn);
                            $block = 40000;
                            for ($j = 1; $j <= ceil($counter / $block); $j++) {
                                $cases = [];
                                $ids = [];
                                $params = [];
                                for ($i = 0 + (($j - 1) * $block); $i < $j * $block; $i++) {
                                    if ($i < $counter) {
                                        $id = (int) $arr_msisdn[$i];
                                        $cases[] = "WHEN {$id} then ?";
                                        $params[] = $arr_act_store[$i];
                                        $ids[] = $id;
                                    } else {
                                        break;
                                    }
                                }
                                $ids = implode(',', $ids);
                                $cases = implode(' ', $cases);
                                DB::update("UPDATE `{$table}` SET `ActivationStore` = CASE `MSISDN` {$cases} END WHERE `MSISDN` in ({$ids})", $params);
                            }

                            return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting');
                        } else {
                            $inputFileName = './uploaded_file/temp.' . $extention;
                            /** Load $inputFileName to a Spreadsheet Object  * */
                            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
                            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                            $writer->save('./uploaded_file/' . 'temp.xlsx');

                            $filePath = base_path() . '/uploaded_file/' . 'temp.xlsx';
                            $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::XLSX);
                            $reader->setShouldFormatDates(true);
                            $counter = 0;
                            $reader->open($filePath);
                            $arr_msisdn = [];
                            $arr_return = [];
                            foreach ($reader->getSheetIterator() as $sheet) {
                                foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                    if ($rowNumber > 1) {
                                        // do stuff with the row
                                        $msisdn = (string) $value[3];
                                        if ($msisdn != '' && $msisdn != null) {
                                            $msisdn = str_replace('\'', '', $msisdn);
                                            if (substr($msisdn, 0, 1) === '0') {
                                                $msisdn = substr($msisdn, 1);
                                            }
                                            array_push($arr_msisdn, $msisdn);
                                            $date_return = $value[2];
                                            //$date_return = explode('/', $date_return);
                                            //$date_return = $date_return[1] . '/' . $date_return[0] . '/' . $date_return[2];
                                            $date_return = strtotime($date_return);
                                            $date_return = date('Y-m-d', $date_return);
                                            if (substr($date_return, 0, 4) === '1970') {
                                                $date_return = $value[2];
                                                $date_return = explode('/', $date_return);
                                                $date_return = $date_return[1] . '/' . $date_return[0] . '/' . $date_return[2];
                                                $date_return = strtotime($date_return);
                                                $date_return = date('Y-m-d', $date_return);
                                            }
                                            array_push($arr_return, $date_return);
                                        }
                                    }
                                }
                            }
                            $reader->close();
                            $check_msisdn = [];
                            $ids = $arr_msisdn;
                            $ids = implode("','", $ids);
                            $right_msisdn = DB::select("SELECT `MSISDN` FROM `m_inventory` WHERE `MSISDN` in ('{$ids}')");
                            foreach ($right_msisdn as $msisdn) {
                                $check_msisdn[] = $msisdn->MSISDN;
                            }
                            $not_found = array_diff($arr_msisdn, $check_msisdn);
                            $not_found = implode(",", $not_found);
                            $not_found = explode(",", $not_found);
                            if (count($not_found) > 0) {
                                $for_raw = '';
                                for ($i = 0; $i < count($not_found); $i++) {
                                    if ($i == 0)
                                        $for_raw .= "(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from aqcuisition file')";
                                    else
                                        $for_raw .= ",(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from acquisition file')";
                                }
                                DB::insert("INSERT INTO m_uncatagorized VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE MSISDN=MSISDN;");
                            }
                            $table = Inventory::getModel()->getTable();

                            $counter = count($arr_msisdn);
                            $block = 40000;
                            for ($j = 1; $j <= ceil($counter / $block); $j++) {
                                $cases = [];
                                $ids = [];
                                $params = [];
                                for ($i = 0 + (($j - 1) * $block); $i < $j * $block; $i++) {
                                    if ($i < $counter) {
                                        $id = (int) $arr_msisdn[$i];
                                        $cases[] = "WHEN {$id} then ?";
                                        $params[] = $arr_return[$i];
                                        $ids[] = $id;
                                    } else {
                                        break;
                                    }
                                }
                                $ids = implode(',', $ids);
                                $cases = implode(' ', $cases);
                                DB::update("UPDATE `{$table}` SET `ActivationDate` = CASE `MSISDN` {$cases} END WHERE `MSISDN` in ({$ids})", $params);
                            }
                            return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumberac($counter);
                        }
                    }
                }
                return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
            } else if (Input::get('jenis') == 'churn') {
                $input = Input::file('sample_file');
                if ($input != '') {
                    if (Input::hasFile('sample_file')) {
                        $destination = base_path() . '/uploaded_file/';
                        $extention = Input::file('sample_file')->getClientOriginalExtension();
                        $filename = 'temp.' . $extention;
                        Input::file('sample_file')->move($destination, $filename);
                        $filePath = base_path() . '/uploaded_file/' . 'temp.' . $extention;
                        $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::XLSX);
                        $reader->setShouldFormatDates(true);
                        $counter = 0;
                        $reader->open($filePath);
                        $arr_msisdn = [];
                        $arr_return = [];
                        $arr_act = [];
                        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                            if ($sheetIndex == 1)
                                foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                    if ($rowNumber > 2) {
                                        // do stuff with the row
                                        $msisdn = (string) $value[3];
                                        if ($msisdn != '' && $msisdn != null) {
                                            $msisdn = str_replace('\'', '', $msisdn);
                                            if (substr($msisdn, 0, 1) === '0') {
                                                $msisdn = substr($msisdn, 1);
                                            }
                                            array_push($arr_msisdn, $msisdn);
                                            $date_return = $value[2];
                                            //$date_return = explode('/', $date_return);
                                            //$date_return = $date_return[1] . '/' . $date_return[0] . '/' . $date_return[2];
                                            $date_return = strtotime($date_return);
                                            $date_return = date('Y-m-d', $date_return);
                                            if (substr($date_return, 0, 4) === '1970') {
                                                $date_return = $value[2];
                                                $date_return = explode('/', $date_return);
                                                $date_return = $date_return[1] . '/' . $date_return[0] . '/' . $date_return[2];
                                                $date_return = strtotime($date_return);
                                                $date_return = date('Y-m-d', $date_return);
                                            }
                                            array_push($arr_return, $date_return);

                                            $date_act = $value[5];
                                            //$date_return = explode('/', $date_return);
                                            //$date_return = $date_return[1] . '/' . $date_return[0] . '/' . $date_return[2];
                                            $date_act = strtotime($date_act);
                                            $date_act = date('Y-m-d', $date_act);
                                            if (substr($date_act, 0, 4) === '1970') {
                                                $date_act = $value[5];
                                                $date_act = explode('/', $date_act);
                                                $date_act = $date_act[1] . '/' . $date_act[0] . '/' . $date_act[2];
                                                $date_act = strtotime($date_act);
                                                $date_act = date('Y-m-d', $date_act);
                                            }
                                            array_push($arr_act, $date_act);
                                        }
                                    }
                                }
                        }
                        $reader->close();

                        $table = Inventory::getModel()->getTable();
                        $cases1 = [];
                        $cases2 = [];
                        $ids = [];
                        $params = [];
                        $counter = count($arr_msisdn);

                        for ($i = 0; $i < count($arr_msisdn); $i++) {
                            $id = $arr_msisdn[$i];
                            $cases2[] = "WHEN '{$id}' then '{$arr_return[$i]}'";
                            $cases1[] = "WHEN '{$id}' then '{$arr_act[$i]}'";
                            $ids[] = '\'' . $id . '\'';
                        }

                        $ids = implode(',', $ids);
                        $check_msisdn = [];
                        $right_msisdn = DB::select("SELECT `MSISDN` FROM `m_inventory` WHERE `MSISDN` in ({$ids})");
                        foreach ($right_msisdn as $msisdn) {
                            $check_msisdn[] = $msisdn->MSISDN;
                        }
                        $not_found = array_diff($arr_msisdn, $check_msisdn);
                        $not_found = implode(",", $not_found);
                        $not_found = explode(",", $not_found);
                        if (count($not_found) > 0) {
                            $for_raw = '';
                            for ($i = 0; $i < count($not_found); $i++) {
                                if ($i == 0)
                                    $for_raw .= "(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'unfound from churn file')";
                                else
                                    $for_raw .= ",(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'unfound from churn file')";
                            }
                            DB::insert("INSERT INTO m_uncatagorized VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE MSISDN=MSISDN;");
                        }
                        $cases1 = implode(' ', $cases1);
                        $cases2 = implode(' ', $cases2);
                        DB::update("UPDATE `{$table}` SET `ChurnDate` = CASE `MSISDN` {$cases2} END, `ActivationDate` = CASE `MSISDN` {$cases1} END WHERE `MSISDN` in ({$ids})");

                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumberch($counter);
                    }
                }
                return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
            } else if (Input::get('jenis') == 'topup') {
                $input = Input::file('sample_file');
                if ($input != '') {
                    if (Input::hasFile('sample_file')) {
                        $destination = base_path() . '/uploaded_file/';
                        $extention = Input::file('sample_file')->getClientOriginalExtension();
                        $filename = 'temp.' . $extention;
                        Input::file('sample_file')->move($destination, $filename);
                        $filePath = base_path() . '/uploaded_file/' . 'temp.' . $extention;
//                        $inputFileName = './uploaded_file/temp.' . $extention;
//                        /** Load $inputFileName to a Spreadsheet Object  * */
//                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
//                        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
//                        $writer->save('./uploaded_file/' . 'temp.xlsx');
//
//                        $filePath = base_path() . '/uploaded_file/' . 'temp.xlsx';
                        $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::XLSX);
                        $reader->setShouldFormatDates(true);
                        $counter = 0;
                        $reader->open($filePath);
                        $arr_msisdn = [];
                        $arr_voc = [];
                        $arr_return = [];
                        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                            if ($sheetIndex == 2)
                                foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                    if ($rowNumber > 2) {
                                        // do stuff with the row
                                        $msisdn = (string) $value[4];
                                        $voc = (string) $value[12];
                                        if ($msisdn != '' && $msisdn != null) {
                                            $msisdn = str_replace('\'', '', $msisdn);
                                            if (substr($msisdn, 0, 1) === '0') {
                                                $msisdn = substr($msisdn, 1);
                                            }
                                            array_push($arr_voc, $voc);
                                            array_push($arr_msisdn, $msisdn);
                                            $date_return = $value[2];
                                            $date_return = strtotime($date_return);
                                            $date_return = date('Y-m-d', $date_return);
                                            if (substr($date_return, 0, 4) === '1970') {
                                                $date_return = $value[2];
                                                $date_return = explode('/', $date_return);
                                                $date_return = $date_return[1] . '/' . $date_return[0] . '/' . $date_return[2];
                                                $date_return = strtotime($date_return);
                                                $date_return = date('Y-m-d', $date_return);
                                            }
                                            array_push($arr_return, $date_return);
                                        }
                                    }
                                }
                        }
                        $reader->close();
                        $check_msisdn = [];
                        $ids = $arr_voc;
                        $ids = implode("','", $ids);
                        $right_msisdn = DB::select("SELECT `SerialNumber` FROM `m_inventory` WHERE `SerialNumber` in ('{$ids}')");
                        foreach ($right_msisdn as $msisdn) {
                            $check_msisdn[] = $msisdn->SerialNumber;
                        }
                        $not_found = array_diff($arr_voc, $check_msisdn);
                        $not_found = implode(",", $not_found);
                        $not_found = explode(",", $not_found);
                        if (count($not_found) > 0) {
                            $for_raw = '';
                            for ($i = 0; $i < count($not_found); $i++) {
                                if ($i == 0)
                                    $for_raw .= "('{$not_found[$i]}',NULL,CURDATE(),CURDATE(),'unfound from recharge file')";
                                else
                                    $for_raw .= ",('{$not_found[$i]}',NULL,CURDATE(),CURDATE(),'unfound from recharge file')";
                            }
                            DB::insert("INSERT INTO m_uncatagorized VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE SerialNumber=SerialNumber;");
                        }
                        $table = Inventory::getModel()->getTable();
                        $counter = count($arr_msisdn);
                        $block = 40000;
                        for ($j = 1; $j <= ceil($counter / $block); $j++) {
                            $cases1 = [];
                            $cases2 = [];
                            $ids = [];
                            $params = [];
                            for ($i = 0 + (($j - 1) * $block); $i < $j * $block; $i++) {
                                if ($i < $counter) {
                                    $id = $arr_voc[$i];
                                    $cases2[] = "WHEN '{$id}' then '{$arr_return[$i]}'";
                                    $cases1[] = "WHEN '{$id}' then '{$arr_msisdn[$i]}'";
                                    $ids[] = '\'' . $id . '\'';
                                } else {
                                    break;
                                }
                            }
                            $ids = implode(',', $ids);
                            $cases1 = implode(' ', $cases1);
                            $cases2 = implode(' ', $cases2);
                            DB::update("UPDATE `{$table}` SET `TopUpMSISDN` = CASE `SerialNumber` {$cases1} END, `TopUpDate` = CASE `SerialNumber` {$cases2} END WHERE `SerialNumber` in ({$ids})");
                        }
                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumbertop($counter);
                    }
                }
                return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
            }
//             else if (Input::get('jenis') == 'productive') {
//                $input = Input::file('sample_file');
//                if ($input != '') {
//                    if (Input::hasFile('sample_file')) {
//                        $destination = base_path() . '/uploaded_file/';
//                        $extention = Input::file('sample_file')->getClientOriginalExtension();
//                        $real_filename = $_FILES['sample_file']['name'];
//                        $filename = 'temp.' . $extention;
//                        Input::file('sample_file')->move($destination, $filename);
//                        $filePath = base_path() . '/uploaded_file/' . 'temp.' . $extention;
//                        $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
////$reader = ReaderFactory::create(Type::CSV); // for CSV files
////$reader = ReaderFactory::create(Type::ODS); // for ODS files
//
//                        $reader->open($filePath);
//                        $counter = 0;
//                        $month_temp = 0;
//                        $year_temp = 0;
//                        $arr_msisdn = [];
//                        $arr_month = [];
//                        $arr_year = [];
//                        $arr_mo = [];
//                        $arr_mt = [];
//                        $arr_internet = [];
//                        $arr_sms = [];
//                        $arr_services = [];
//                        foreach ($reader->getSheetIterator() as $sheet) {
//                            // grab sheet name from existing file
//                            $sheet_name = $sheet->getName();
//                            $month_temp = substr($sheet_name, 4, 2);
//                            $year_temp = substr($sheet_name, 0, 4);
//                            foreach ($sheet->getRowIterator() as $rowNumber => $value) {
//                                if ($rowNumber > 1) {
//                                    // do stuff with the row
//                                    $msisdn = (string) $value[0];
//
//                                    if ($msisdn != '' && $msisdn != null) {
//                                        $msisdn = str_replace('\'', '', $msisdn);
//                                        if (substr($msisdn, 0, 1) === '0') {
//                                            $msisdn = substr($msisdn, 1);
//                                        }
//                                        array_push($arr_msisdn, $msisdn);
//                                        array_push($arr_month, $month_temp);
//                                        array_push($arr_year, $year_temp);
//                                        array_push($arr_mo, $value[4]);
//                                        array_push($arr_mt, $value[5]);
//                                        array_push($arr_internet, $value[6]);
//                                        array_push($arr_sms, $value[7]);
////                                            array_push($arr_services, $value[11]);
//                                    }
//                                }
//                            }
//                        }
//                        $reader->close();
//                        $for_raw = '';
//                        for ($i = 0; $i < count($arr_msisdn); $i++) {
//                            $unik = $arr_msisdn[$i] . '-' . $arr_month[$i] . '-' . $arr_year[$i];
//                            if ($i == 0)
//                                $for_raw .= "('" . $arr_msisdn[$i] . "','" . $arr_mo[$i] . "','" . $arr_mt[$i] . "','" . $arr_internet[$i] . "','" . $arr_sms[$i] . "',NULL,0,1,'" . $arr_month[$i] . "','" . $arr_year[$i] . "','" . $unik . "')";
//                            else
//                                $for_raw .= ",('" . $arr_msisdn[$i] . "','" . $arr_mo[$i] . "','" . $arr_mt[$i] . "','" . $arr_internet[$i] . "','" . $arr_sms[$i] . "',NULL,0,1,'" . $arr_month[$i] . "','" . $arr_year[$i] . "','" . $unik . "')";
//                        }
//                        DB::insert("INSERT INTO m_productive VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE Unik=Unik;");
//                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumberpr(count($arr_msisdn));
//                    }
//                }
//                return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
//            }
            else if (Input::get('jenis') == 'productive') {
                $input = Input::file('sample_file');
                if ($input != '') {
                    if (Input::hasFile('sample_file')) {
                        $destination = base_path() . '/uploaded_file/';
                        $extention = Input::file('sample_file')->getClientOriginalExtension();
                        $real_filename = $_FILES['sample_file']['name'];
                        $filename = 'temp.' . $extention;
                        Input::file('sample_file')->move($destination, $filename);
                        $filePath = base_path() . '/uploaded_file/' . 'temp.' . $extention;
                        $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::CSV); // for XLSX files
//$reader = ReaderFactory::create(Type::CSV); // for CSV files
//$reader = ReaderFactory::create(Type::ODS); // for ODS files

                        $reader->open($filePath);
                        $counter = 0;
                        $month_temp = 0;
                        $year_temp = 0;
                        $arr_msisdn = [];
                        $arr_month = [];
                        $arr_year = [];
                        $arr_mo = [];
                        $arr_mt = [];
                        $arr_internet = [];
                        $arr_sms = [];
                        $arr_services = [];
                        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                            $date_temp = $real_filename;
                            $date_temp = explode(" ", $date_temp)[0];
                            $month_temp = substr($date_temp, 4, 2);
                            $year_temp = substr($date_temp, 0, 4);
                            if ($sheetIndex == 1)
                                if (substr($date_temp, 0, 1) === '2') {
                                    foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                        if ($rowNumber > 1) {
                                            // do stuff with the row
                                            $msisdn = (string) $value[0];

                                            if ($msisdn != '' && $msisdn != null) {
                                                $msisdn = str_replace('\'', '', $msisdn);
                                                if (substr($msisdn, 0, 1) === '0') {
                                                    $msisdn = substr($msisdn, 1);
                                                }
                                                array_push($arr_msisdn, $msisdn);
                                                array_push($arr_month, $month_temp);
                                                array_push($arr_year, $year_temp);
                                                array_push($arr_mo, $value[3]);
                                                array_push($arr_mt, $value[6]);
                                                array_push($arr_internet, 0);
                                                array_push($arr_sms, $value[8]);
//                                            array_push($arr_services, $value[11]);
                                            }
                                        }
                                    }
                                }
                        }
                        $reader->close();
                        $check_msisdn = [];
                        $ids = $arr_msisdn;
                        $ids = implode("','", $ids);
                        $right_msisdn = DB::select("SELECT `MSISDN` FROM `m_inventory` WHERE `MSISDN` in ('{$ids}')");
                        foreach ($right_msisdn as $msisdn) {
                            $check_msisdn[] = $msisdn->MSISDN;
                        }
                        $not_found = array_diff($arr_msisdn, $check_msisdn);
                        $not_found = implode(",", $not_found);
                        $not_found = explode(",", $not_found);
                        if (count($not_found) > 0) {
                            $for_raw = '';
                            for ($i = 0; $i < count($not_found); $i++) {
                                if ($i == 0)
                                    $for_raw .= "(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from productive-hk file')";
                                else
                                    $for_raw .= ",(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from productive-hk file')";
                            }
                            DB::insert("INSERT INTO m_uncatagorized VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE MSISDN=MSISDN;");
                        }
                        $for_raw = '';
                        for ($i = 0; $i < count($arr_msisdn); $i++) {
                            $unik = $arr_msisdn[$i] . '-' . $arr_month[$i] . '-' . $arr_year[$i];
                            if ($i == 0)
                                $for_raw .= "('" . $arr_msisdn[$i] . "','" . $arr_mo[$i] . "','" . $arr_mt[$i] . "','" . $arr_internet[$i] . "','" . $arr_sms[$i] . "',NULL,1,0,'" . $arr_month[$i] . "','" . $arr_year[$i] . "','" . $unik . "')";
                            else
                                $for_raw .= ",('" . $arr_msisdn[$i] . "','" . $arr_mo[$i] . "','" . $arr_mt[$i] . "','" . $arr_internet[$i] . "','" . $arr_sms[$i] . "',NULL,1,0,'" . $arr_month[$i] . "','" . $arr_year[$i] . "','" . $unik . "')";
                        }
                        DB::insert("INSERT INTO m_productive VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE Unik=Unik;");
                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumberpr(count($arr_msisdn));
                    }
                }
                return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
            }
            else if (Input::get('jenis') == 'productive-tst') {
                $input = Input::file('sample_file');
                if ($input != '') {
                    if (Input::hasFile('sample_file')) {
                        $destination = base_path() . '/uploaded_file/';
                        $extention = Input::file('sample_file')->getClientOriginalExtension();
                        $real_filename = $_FILES['sample_file']['name'];
                        $filename = 'temp.' . $extention;
                        Input::file('sample_file')->move($destination, $filename);
                        $inputFileName = './uploaded_file/temp.' . $extention;
                        /** Load $inputFileName to a Spreadsheet Object  * */
                        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
                        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                        $writer->save('./uploaded_file/' . 'temp.xlsx');

                        $filePath = base_path() . '/uploaded_file/' . 'temp.xlsx';
                        $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
//$reader = ReaderFactory::create(Type::CSV); // for CSV files
//$reader = ReaderFactory::create(Type::ODS); // for ODS files

                        $reader->open($filePath);
                        $counter = 0;
                        $month_temp = 0;
                        $year_temp = 0;
                        $arr_msisdn = [];
                        $arr_month = [];
                        $arr_year = [];
                        $arr_mo = [];
                        $arr_mt = [];
                        $arr_internet = [];
                        $arr_sms = [];
                        $arr_services = [];
                        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                            $date_temp = $real_filename;
                            $date_temp = explode("_", $date_temp)[2];
                            $month_temp = substr($date_temp, 4, 2);
                            $month_temp = (int) $month_temp - 1;
                            if (strlen($month_temp) === 1) {
                                $month_temp = "0" . $month_temp;
                            }
                            $year_temp = substr($date_temp, 0, 4);
                            if ($sheetIndex == 1)
                                if (substr($date_temp, 0, 1) === '2') {
                                    foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                        if ($rowNumber > 1) {
                                            // do stuff with the row
                                            $msisdn = (string) $value[0];

                                            if ($msisdn != '' && $msisdn != null) {
                                                $msisdn = str_replace('\'', '', $msisdn);
                                                if (substr($msisdn, 0, 1) === '0') {
                                                    $msisdn = substr($msisdn, 1);
                                                }
                                                array_push($arr_msisdn, $msisdn);
                                                array_push($arr_month, $month_temp);
                                                array_push($arr_year, $year_temp);
                                                array_push($arr_mo, $value[4]);
                                                array_push($arr_mt, $value[5]);
                                                array_push($arr_internet, $value[6]);
                                                array_push($arr_sms, $value[7]);
//                                            array_push($arr_services, $value[11]);
                                            }
                                        }
                                    }
                                }
                        }
                        $reader->close();
                        $check_msisdn = [];
                        $ids = $arr_msisdn;
                        $ids = implode("','", $ids);
                        $right_msisdn = DB::select("SELECT `MSISDN` FROM `m_inventory` WHERE `MSISDN` in ('{$ids}')");
                        foreach ($right_msisdn as $msisdn) {
                            $check_msisdn[] = $msisdn->MSISDN;
                        }
                        $not_found = array_diff($arr_msisdn, $check_msisdn);
                        $not_found = implode(",", $not_found);
                        $not_found = explode(",", $not_found);
                        if (count($not_found) > 0) {
                            $for_raw = '';
                            for ($i = 0; $i < count($not_found); $i++) {
                                if ($i == 0)
                                    $for_raw .= "(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from productive-tst file')";
                                else
                                    $for_raw .= ",(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from productive-tst file')";
                            }
                            DB::insert("INSERT INTO m_uncatagorized VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE MSISDN=MSISDN;");
                        }
                        $for_raw = '';
                        for ($i = 0; $i < count($arr_msisdn); $i++) {
                            $unik = $arr_msisdn[$i] . '-' . $arr_month[$i] . '-' . $arr_year[$i];
                            if ($i == 0)
                                $for_raw .= "('" . $arr_msisdn[$i] . "','" . $arr_mo[$i] . "','" . $arr_mt[$i] . "','" . $arr_internet[$i] . "','" . $arr_sms[$i] . "',NULL,0,1,'" . $arr_month[$i] . "','" . $arr_year[$i] . "','" . $unik . "')";
                            else
                                $for_raw .= ",('" . $arr_msisdn[$i] . "','" . $arr_mo[$i] . "','" . $arr_mt[$i] . "','" . $arr_internet[$i] . "','" . $arr_sms[$i] . "',NULL,0,1,'" . $arr_month[$i] . "','" . $arr_year[$i] . "','" . $unik . "')";
                        }
                        DB::insert("INSERT INTO m_productive VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE Month=VALUES(Month), Year=VALUES(Year), Unik=VALUES(Unik), MO=VALUES(MO), MT=VALUES(MT), Internet=VALUES(Internet), Sms=VALUES(Sms), DataFromTST=1;");
                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumberprtst(count($arr_msisdn));
                    }
                }
                return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
            }
            else if (Input::get('jenis') == 'act_sip') {
                $input = Input::file('sample_file');
                if ($input != '') {
                    if (Input::hasFile('sample_file')) {
                        $destination = base_path() . '/uploaded_file/';
                        $extention = Input::file('sample_file')->getClientOriginalExtension();
                        $filename = 'temp.' . $extention;
                        Input::file('sample_file')->move($destination, $filename);
                        $filePath = base_path() . '/uploaded_file/' . 'temp.csv';
                        $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::CSV);
                        $reader->setShouldFormatDates(true);
                        $counter = 0;
                        $reader->open($filePath);
                        $arr_msisdn = [];
                        $arr_return = [];
                        $arr_names = [];
                        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                            if ($sheetIndex == 1)
                                foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                    if ($rowNumber > 1) {
                                        // do stuff with the row
                                        $msisdn = (string) $value[14];
                                        if ($msisdn != '' && $msisdn != null) {
                                            $msisdn = str_replace('\'', '', $msisdn);
                                            $msisdn = str_replace(' ', '', $msisdn);
                                            if (substr($msisdn, 0, 1) === '0') {
                                                $msisdn = substr($msisdn, 1);
                                            }
                                            array_push($arr_msisdn, $msisdn);
                                            $date_return = $value[6];
                                            $date_return = str_replace('\'', '', $date_return);
                                            array_push($arr_return, $date_return);
                                            $name = str_replace('\'', '', $value[1]);
                                            array_push($arr_names, $name);
                                        }
                                    }
                                }
                        }
                        $reader->close();
                        $check_msisdn = [];
                        $ids = $arr_msisdn;
                        $ids = implode("','", $ids);
                        $right_msisdn = DB::select("SELECT `MSISDN` FROM `m_inventory` WHERE `MSISDN` in ('{$ids}')");
                        foreach ($right_msisdn as $msisdn) {
                            $check_msisdn[] = $msisdn->MSISDN;
                        }
                        $not_found = array_diff($arr_msisdn, $check_msisdn);
                        $not_found = implode(",", $not_found);
                        $not_found = explode(",", $not_found);
                        if (count($not_found) > 0) {
                            $for_raw = '';
                            for ($i = 0; $i < count($not_found); $i++) {
                                if ($i == 0)
                                    $for_raw .= "(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from sip file')";
                                else
                                    $for_raw .= ",(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from sip file')";
                            }
                            DB::insert("INSERT INTO m_uncatagorized VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE MSISDN=MSISDN;");
                        }

                        $table = Inventory::getModel()->getTable();
                        $counter = count($arr_msisdn);
                        $block = 40000;
                        for ($j = 1; $j <= ceil($counter / $block); $j++) {
                            $cases1 = [];
                            $cases2 = [];
                            $ids = [];
                            $params = [];
                            for ($i = 0 + (($j - 1) * $block); $i < $j * $block; $i++) {
                                if ($i < $counter) {
                                    $id = $arr_msisdn[$i];
                                    $cases1[] = "WHEN '{$id}' then '{$arr_return[$i]}'";
                                    $cases2[] = "WHEN '{$id}' then '{$arr_names[$i]}'";
                                    $ids[] = '\'' . $id . '\'';
                                } else {
                                    break;
                                }
                            }
                            $ids = implode(',', $ids);
                            $cases1 = implode(' ', $cases1);
                            $cases2 = implode(' ', $cases2);
                            DB::update("UPDATE `{$table}` SET `ActivationStore` = CASE `MSISDN` {$cases1} END, `ActivationName` = CASE `MSISDN` {$cases2} END WHERE `MSISDN` in ({$ids})");

                            $check_msisdn = [];
                            $ids = $arr_msisdn;
                            $ids = implode("','", $ids);
                            $right_msisdn = DB::select("SELECT `MSISDN` FROM `m_inventory` WHERE ActivationDate IS NOT NULL");
                            foreach ($right_msisdn as $msisdn) {
                                $check_msisdn[] = $msisdn->MSISDN;
                            }
                            $not_found = array_diff($arr_msisdn, $check_msisdn);
                            $not_found = implode(",", $not_found);
                            $not_found = explode(",", $not_found);
                            if (count($not_found) > 0) {
                                $for_raw = '';
                                for ($i = 0; $i < count($not_found); $i++) {
                                    if ($i == 0)
                                        $for_raw .= "(NULL,'{$not_found[$i]}','activation date without activation name',CURDATE(),CURDATE())";
                                    else
                                        $for_raw .= ",(NULL,'{$not_found[$i]}','activation date without activation name',CURDATE(),CURDATE())";
                                }
                                DB::insert("INSERT INTO m_anomalies VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE MSISDN=MSISDN;");
                            }
                        }
                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumbersip($counter);
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
        Session::forget('ShipouttoInv');
        return View::make('inventory')->withPage('inventory');
    }

    //============================ajax===============================


    static function getIVR() {
//        $year = '2018';
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
        $data = [];
        $all_ivr = Stats::where('Year', $year)->whereRaw('Status >= 10')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
        foreach ($all_ivr as $ivr) {
            $stats = '30 days';
            if ($ivr->Status == '180') {
                $stats = '1 GB';
            } else if ($ivr->Status == '300') {
                $stats = '2 GB';
            } else if ($ivr->Status == '360') {
                $stats = '1 GB';
            } else if ($ivr->Status == '600') {
                $stats = '2 GB';
            }
            if (!isset($data[$stats]))
                $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            for ($i = 0; $i < 12; $i++) {
                if ($i == $ivr->Month - 1) {
                    if (!$data[$stats][$i])
                        $data[$stats][$i] = $ivr->Counter;
                    else
                        $data[$stats][$i] += $ivr->Counter;
                }
            }
        }

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                foreach (Stats::where('Year', $year->Year)->whereRaw('Status >= 10')->get() as $ivr) {
                    $stats = '30 days';
                    if ($ivr->Status == '180') {
                        $stats = '1 GB';
                    } else if ($ivr->Status == '300') {
                        $stats = '2 GB';
                    } else if ($ivr->Status == '360') {
                        $stats = '1 GB';
                    } else if ($ivr->Status == '600') {
                        $stats = '2 GB';
                    }
                    if (!isset($data[$stats]))
                        $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    for ($i = 0; $i < 12; $i++) {
                        if ($i == $ivr->Month - 1) {
                            if (!$data[$stats][$i])
                                $data[$stats][$i] = $ivr->Counter;
                            else
                                $data[$stats][$i] += $ivr->Counter;
                        }
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
        }
        return $data;
    }

    static function getCHURN() {
        $year = Input::get('year');
//        $year = '2016';
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');

        $data = [];
        $counter_z = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $counter_c = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $data['churn']['Churn'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $data['act']['Active MSISDN'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $sum_bef = 0;
        $sum_churn_bef = 0;
        $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Churn%\'')->get();
        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Activation%\'')->orderBy('Month', 'ASC')->get();
        $churn_year_before = Stats::where('Year', '<', $year)->whereRaw('Status LIKE \'%Churn%\'')->orderBy('Month', 'ASC')->get();
        $act_year_before = Stats::where('Year', '<', $year)->whereRaw('Status LIKE \'%Activation%\'')->orderBy('Month', 'ASC')->get();
        if ($act_year_before != null) {
            foreach ($act_year_before as $act) {
                $sum_bef += $act->Counter;
            }
        }
        if ($churn_year_before != null) {
            foreach ($churn_year_before as $act) {
                $sum_churn_bef += $act->Counter;
            }
        }
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $counter_c[($ivr->Month - 1)] = $ivr->Counter;
            }
        }
        if ($all_act != null) {
            foreach ($all_act as $ivr) {
                $counter_z[($ivr->Month - 1)] = $ivr->Counter;
            }
        }
        for ($i = 0; $i < 12; $i++) {
            $sum_bef += $counter_z[$i];
            $sum_churn_bef += $counter_c[$i];
            if ($counter_z[$i] > 0)
                $data['act']['Active MSISDN'][$i] = $sum_bef - $sum_churn_bef;
            if ($counter_c[$i] > 0)
                $data['churn']['Churn'][$i] = -($sum_churn_bef);
        }

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                $counter_z = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $counter_c = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $data['churn']['Churn'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $data['act']['Active MSISDN'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $sum_bef = 0;
                $sum_churn_bef = 0;
                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%Churn%\'')->get();
                $all_act = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%Activation%\'')->orderBy('Month', 'ASC')->get();
                $churn_year_before = Stats::where('Year', '<', $year->Year)->whereRaw('Status LIKE \'%Churn%\'')->orderBy('Month', 'ASC')->get();
                $act_year_before = Stats::where('Year', '<', $year->Year)->whereRaw('Status LIKE \'%Activation%\'')->orderBy('Month', 'ASC')->get();
                if ($act_year_before != null) {
                    foreach ($act_year_before as $act) {
                        $sum_bef += $act->Counter;
                    }
                }
                if ($churn_year_before != null) {
                    foreach ($churn_year_before as $act) {
                        $sum_churn_bef += $act->Counter;
                    }
                }
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $counter_c[($ivr->Month - 1)] = $ivr->Counter;
                    }
                }
                if ($all_act != null) {
                    foreach ($all_act as $ivr) {
                        $counter_z[($ivr->Month - 1)] = $ivr->Counter;
                    }
                }
                for ($i = 0; $i < 12; $i++) {
                    $sum_bef += $counter_z[$i];
                    $sum_churn_bef += $counter_c[$i];
                    if ($counter_z[$i] > 0)
                        $data['act']['Active MSISDN'][$i] = $sum_bef - $sum_churn_bef;
                    if ($counter_c[$i] > 0)
                        $data['churn']['Churn'][$i] = -($sum_churn_bef);
                }
                foreach ($data as $datas)
                    foreach ($datas as $key => $a) {
                        $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                        $writer->addRow($myArr); // add a row at a time
                    }
            }
            $writer->close();
        }
        return $data;
    }

    static function getCHURN2() {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data["Churn"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Churn%\'')->get();
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $data["Churn"][($ivr->Month - 1)] = $ivr->Counter;
            }
        }
        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                $data["Churn"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%Churn%\'')->get();
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $data["Churn"][($ivr->Month - 1)] = $ivr->Counter;
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
        }
        return $data;
    }

    static function getSubsriber() {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data["Subscriber"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Activation%\'')->get();
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $data["Subscriber"][($ivr->Month - 1)] = $ivr->Counter;
            }
        }

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                $data["Subscriber"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%Activation%\'')->get();
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $data["Subscriber"][($ivr->Month - 1)] = $ivr->Counter;
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
        }
        return $data;
    }

    static function getProductive() {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];
        $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%services%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = 'no service';
                $temp_stat = $ivr->Status;
                if (substr($temp_stat, 0, 1) == '1') {
                    $stats = 'Voice only';
                } else if (substr($temp_stat, 0, 1) == '2') {
                    $stats = 'Internet only';
                } else if (substr($temp_stat, 0, 1) == '3') {
                    $stats = 'Voice + Internet';
                } else if (substr($temp_stat, 0, 1) == '5') {
                    $stats = 'SMS only';
                } else if (substr($temp_stat, 0, 1) == '6') {
                    $stats = 'Voice + SMS';
                } else if (substr($temp_stat, 0, 1) == '7') {
                    $stats = 'Internet + SMS';
                } else if (substr($temp_stat, 0, 1) == '8') {
                    $stats = 'All';
                }
                if (!isset($data[$stats]))
                    $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data[$stats][$i] = $ivr->Counter;
                    }
                }
            }
        }

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%services%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = 'no service';
                        $temp_stat = $ivr->Status;
                        if (substr($temp_stat, 0, 1) == '1') {
                            $stats = 'Voice only';
                        } else if (substr($temp_stat, 0, 1) == '2') {
                            $stats = 'Internet only';
                        } else if (substr($temp_stat, 0, 1) == '3') {
                            $stats = 'Voice + Internet';
                        } else if (substr($temp_stat, 0, 1) == '5') {
                            $stats = 'SMS only';
                        } else if (substr($temp_stat, 0, 1) == '6') {
                            $stats = 'Voice + SMS';
                        } else if (substr($temp_stat, 0, 1) == '7') {
                            $stats = 'Internet + SMS';
                        } else if (substr($temp_stat, 0, 1) == '8') {
                            $stats = 'All';
                        }
                        if (!isset($data[$stats]))
                            $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data[$stats][$i] = $ivr->Counter;
                            }
                        }
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
        }
        return $data;
    }

    static function getSumService() {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];
        $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%_sum%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = '';
                $temp_stat = $ivr->Status;
                $temp_counter = $ivr->Counter;
                if (explode('_', $temp_stat)[0] == 'mt') {
                    $stats = 'MT (/1000 mins)';
                    $temp_counter = round(ceil($temp_counter / 60) / 1000, 1);
                } else if (explode('_', $temp_stat)[0] == 'mo') {
                    $stats = 'MO (/1000 mins)';
                    $temp_counter = round(ceil($temp_counter / 60) / 1000, 1);
                } else if (explode('_', $temp_stat)[0] == 'internet') {
                    $stats = 'Internet (TB)';
                    $temp_counter = round($temp_counter / 1000, 1);
                } else if (explode('_', $temp_stat)[0] == 'sms') {
                    $stats = 'SMS (/1000 sms)';
                    $temp_counter = round($temp_counter / 1000, 1);
                }
                if (!isset($data[$stats]))
                    $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data[$stats][$i] = $temp_counter;
                    }
                }
            }
        }
        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%_sum%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = '';
                        $temp_stat = $ivr->Status;
                        $temp_counter = $ivr->Counter;
                        if (explode('_', $temp_stat)[0] == 'mt') {
                            $stats = 'MT (/1000 mins)';
                            $temp_counter = round(ceil($temp_counter / 60) / 1000, 1);
                        } else if (explode('_', $temp_stat)[0] == 'mo') {
                            $stats = 'MO (/1000 mins)';
                            $temp_counter = round(ceil($temp_counter / 60) / 1000, 1);
                        } else if (explode('_', $temp_stat)[0] == 'internet') {
                            $stats = 'Internet (TB)';
                            $temp_counter = round($temp_counter / 1000, 1);
                        } else if (explode('_', $temp_stat)[0] == 'sms') {
                            $stats = 'SMS (/1000 sms)';
                            $temp_counter = round($temp_counter / 1000, 1);
                        }
                        if (!isset($data[$stats]))
                            $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data[$stats][$i] = $temp_counter;
                            }
                        }
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
        }
        return $data;
    }

    static function getPayload() {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];
        $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%internet_sum%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = 'Internet (TB)';
                $temp_counter = $ivr->Counter / 1000;
                if (!isset($data[$stats]))
                    $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data[$stats][$i] = round($temp_counter, 2);
                    }
                }
            }
        }
        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%internet_sum%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = 'Internet (TB)';
                        $temp_counter = $ivr->Counter / 1000;
                        if (!isset($data[$stats]))
                            $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data[$stats][$i] = round($temp_counter, 2);
                            }
                        }
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
        }
        return $data;
    }

    static function getPayloadPerUser() {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];
        $sum_internet = [];
        $count_internet = [];
        $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%internet_sum%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = 'Internet (TB)';
                $temp_counter = $ivr->Counter;
                if (!isset($sum_internet[$stats]))
                    $sum_internet[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $sum_internet[$stats][$i] = round($temp_counter, 2);
                    }
                }
            }
        }

        $internet_user = Stats::where('Year', $year)->whereRaw('Status LIKE \'%services%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
        $count_internet['Internet'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        if ($internet_user != null) {
            foreach ($internet_user as $ivr) {
                $stats = 'non';
                $temp_stat = $ivr->Status;
                if (substr($temp_stat, 0, 1) == '2') {
                    $stats = 'Internet';
                } else if (substr($temp_stat, 0, 1) == '3') {
                    $stats = 'Internet';
                } else if (substr($temp_stat, 0, 1) == '7') {
                    $stats = 'Internet';
                } else if (substr($temp_stat, 0, 1) == '8') {
                    $stats = 'Internet';
                }
                if ($stats != 'non') {
                    if (!isset($count_internet[$stats]))
                        $count_internet[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    for ($i = 0; $i < 12; $i++) {
                        if ($i == $ivr->Month - 1) {
                            $count_internet[$stats][$i] += $ivr->Counter;
                        }
                    }
                }
            }
        }
        $data['PayLoad Per User'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 0; $i < 12; $i++) {
            if ($count_internet['Internet'][$i] == 0) {
                $data['PayLoad Per User'][$i] = 0;
            } else {
                $data['PayLoad Per User'][$i] = round($sum_internet['Internet (TB)'][$i] / $count_internet['Internet'][$i], 2);
            }
        }

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                $sum_internet = [];
                $count_internet = [];
                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%internet_sum%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = 'Internet (TB)';
                        $temp_counter = $ivr->Counter;
                        if (!isset($sum_internet[$stats]))
                            $sum_internet[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $sum_internet[$stats][$i] = round($temp_counter, 2);
                            }
                        }
                    }
                }

                $internet_user = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%services%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
                $count_internet['Internet'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                if ($internet_user != null) {
                    foreach ($internet_user as $ivr) {
                        $stats = 'non';
                        $temp_stat = $ivr->Status;
                        if (substr($temp_stat, 0, 1) == '2') {
                            $stats = 'Internet';
                        } else if (substr($temp_stat, 0, 1) == '3') {
                            $stats = 'Internet';
                        } else if (substr($temp_stat, 0, 1) == '7') {
                            $stats = 'Internet';
                        } else if (substr($temp_stat, 0, 1) == '8') {
                            $stats = 'Internet';
                        }
                        if ($stats != 'non') {
                            if (!isset($count_internet[$stats]))
                                $count_internet[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                            for ($i = 0; $i < 12; $i++) {
                                if ($i == $ivr->Month - 1) {
                                    $count_internet[$stats][$i] += $ivr->Counter;
                                }
                            }
                        }
                    }
                }
                $data['PayLoad Per User'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($count_internet['Internet'][$i] == 0) {
                        $data['PayLoad Per User'][$i] = 0;
                    } else {
                        $data['PayLoad Per User'][$i] = round($sum_internet['Internet (TB)'][$i] / $count_internet['Internet'][$i], 2);
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
        }
        return $data;
    }

    static function getInternetVsNon() {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];
        $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%services%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = 'Non-Internet';
                $temp_stat = $ivr->Status;
                if (substr($temp_stat, 0, 1) == '1') {
                    $stats = 'Non-Internet';
                } else if (substr($temp_stat, 0, 1) == '2') {
                    $stats = 'Internet';
                } else if (substr($temp_stat, 0, 1) == '3') {
                    $stats = 'Internet';
                } else if (substr($temp_stat, 0, 1) == '5') {
                    $stats = 'Non-Internet';
                } else if (substr($temp_stat, 0, 1) == '6') {
                    $stats = 'Non-Internet';
                } else if (substr($temp_stat, 0, 1) == '7') {
                    $stats = 'Internet';
                } else if (substr($temp_stat, 0, 1) == '8') {
                    $stats = 'Internet';
                }
                if (!isset($data[$stats]))
                    $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data[$stats][$i] += $ivr->Counter;
                    }
                }
            }
        }

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%services%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
                $data = [];
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = 'Non-Internet';
                        $temp_stat = $ivr->Status;
                        if (substr($temp_stat, 0, 1) == '1') {
                            $stats = 'Non-Internet';
                        } else if (substr($temp_stat, 0, 1) == '2') {
                            $stats = 'Internet';
                        } else if (substr($temp_stat, 0, 1) == '3') {
                            $stats = 'Internet';
                        } else if (substr($temp_stat, 0, 1) == '5') {
                            $stats = 'Non-Internet';
                        } else if (substr($temp_stat, 0, 1) == '6') {
                            $stats = 'Non-Internet';
                        } else if (substr($temp_stat, 0, 1) == '7') {
                            $stats = 'Internet';
                        } else if (substr($temp_stat, 0, 1) == '8') {
                            $stats = 'Internet';
                        }
                        if (!isset($data[$stats]))
                            $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data[$stats][$i] += $ivr->Counter;
                            }
                        }
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
        }
        return $data;
    }

    static function getVouchers300TopUp() {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];
        //1 -> evoucher; 2 -> phvoucher
        $all_ivr = [];
        // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
        $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%topup%\'')->get();
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = '';
                $temp_stat = $ivr->Status;
                if (substr($temp_stat, 0, 1) == '2') {
                    $stats = 'pV300';
                } else if (substr($temp_stat, 0, 1) == '5') {
                    $stats = 'eV300';
                }
                if ($stats != '') {
                    if (!isset($data[$stats]))
                        $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    for ($i = 0; $i < 12; $i++) {
                        if ($i == $ivr->Month - 1) {
                            $data[$stats][$i] += $ivr->Counter;
                        }
                    }
                }
            }
        }

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%topup%\'')->get();
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = '';
                        $temp_stat = $ivr->Status;
                        if (substr($temp_stat, 0, 1) == '2') {
                            $stats = 'pV300';
                        } else if (substr($temp_stat, 0, 1) == '5') {
                            $stats = 'eV300';
                        }
                        if ($stats != '') {
                            if (!isset($data[$stats]))
                                $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                            for ($i = 0; $i < 12; $i++) {
                                if ($i == $ivr->Month - 1) {
                                    $data[$stats][$i] += $ivr->Counter;
                                }
                            }
                        }
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
        }
        return $data;
    }

    static function getSubsriberTopUp() {
        $year = Input::get('year');
//        $year = '2017';
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');

        $data = [];
        //1 -> evoucher; 2 -> phvoucher
        $simtopup300 = [];
        $simtopup100 = [];
        $simtopup50 = [];
        // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
        $simtopup300 = DB::table('m_inventory')
                        ->whereRaw("TopUpMSISDN IS NOT NULL AND (`SerialNumber` LIKE '%KR0250%' OR `SerialNumber` LIKE '%KR1850%') AND YEAR(TopUpDate) = '{$year}'")
                        ->select(DB::raw("COUNT(DISTINCT `TopUpMSISDN`) as 'Counter',MONTH(TopUpDate) as 'month'"))->groupBy(DB::raw("MONTH(TopUpDate)"))->get();
        $simtopup100 = DB::table('m_inventory')
                        ->whereRaw("TopUpMSISDN IS NOT NULL AND (`SerialNumber` LIKE '%KR0150%' OR `SerialNumber` LIKE '%KR0350%') AND YEAR(TopUpDate) = '{$year}'")
                        ->select(DB::raw("COUNT(DISTINCT `TopUpMSISDN`) as 'Counter',MONTH(TopUpDate)  as 'month'"))->groupBy(DB::raw("MONTH(TopUpDate)"))->get();
        $simtopup50 = DB::table('m_inventory')
                        ->whereRaw("TopUpMSISDN IS NOT NULL AND `SerialNumber` LIKE '%KR0450%' AND YEAR(TopUpDate) = '{$year}'")
                        ->select(DB::raw("COUNT(DISTINCT `TopUpMSISDN`) as 'Counter',MONTH(TopUpDate)  as 'month'"))->groupBy(DB::raw("MONTH(TopUpDate)"))->get();

        if ($simtopup50 != null) {
            foreach ($simtopup50 as $sim) {
                if (!isset($data['Voc50']))
                    $data['Voc50'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $sim->month - 1) {
                        $data['Voc50'][$i] += $sim->Counter;
                    }
                }
            }
        }
        if ($simtopup100 != null) {
            foreach ($simtopup100 as $sim) {
                if (!isset($data['Voc100']))
                    $data['Voc100'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $sim->month - 1) {
                        $data['Voc100'][$i] += $sim->Counter;
                    }
                }
            }
        }
        if ($simtopup300 != null) {
            foreach ($simtopup300 as $sim) {
                if (!isset($data['Voc300']))
                    $data['Voc300'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $sim->month - 1) {
                        $data['Voc300'][$i] += $sim->Counter;
                    }
                }
            }
        }
        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                $data = [];
                //1 -> evoucher; 2 -> phvoucher
                $simtopup300 = [];
                $simtopup100 = [];
                $simtopup50 = [];
                // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
                $simtopup300 = DB::table('m_inventory')
                                ->whereRaw("TopUpMSISDN IS NOT NULL AND (`SerialNumber` LIKE '%KR0250%' OR `SerialNumber` LIKE '%KR1850%') AND YEAR(TopUpDate) = '{$year->Year}'")
                                ->select(DB::raw("COUNT(DISTINCT `TopUpMSISDN`) as 'Counter',MONTH(TopUpDate) as 'month'"))->groupBy(DB::raw("MONTH(TopUpDate)"))->get();
                $simtopup100 = DB::table('m_inventory')
                                ->whereRaw("TopUpMSISDN IS NOT NULL AND (`SerialNumber` LIKE '%KR0150%' OR `SerialNumber` LIKE '%KR0350%') AND YEAR(TopUpDate) = '{$year->Year}'")
                                ->select(DB::raw("COUNT(DISTINCT `TopUpMSISDN`) as 'Counter',MONTH(TopUpDate)  as 'month'"))->groupBy(DB::raw("MONTH(TopUpDate)"))->get();
                $simtopup50 = DB::table('m_inventory')
                                ->whereRaw("TopUpMSISDN IS NOT NULL AND `SerialNumber` LIKE '%KR0450%' AND YEAR(TopUpDate) = '{$year->Year}'")
                                ->select(DB::raw("COUNT(DISTINCT `TopUpMSISDN`) as 'Counter',MONTH(TopUpDate)  as 'month'"))->groupBy(DB::raw("MONTH(TopUpDate)"))->get();

                if ($simtopup50 != null) {
                    foreach ($simtopup50 as $sim) {
                        if (!isset($data['Voc50']))
                            $data['Voc50'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $sim->month - 1) {
                                $data['Voc50'][$i] += $sim->Counter;
                            }
                        }
                    }
                }
                if ($simtopup100 != null) {
                    foreach ($simtopup100 as $sim) {
                        if (!isset($data['Voc100']))
                            $data['Voc100'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $sim->month - 1) {
                                $data['Voc100'][$i] += $sim->Counter;
                            }
                        }
                    }
                }
                if ($simtopup300 != null) {
                    foreach ($simtopup300 as $sim) {
                        if (!isset($data['Voc300']))
                            $data['Voc300'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $sim->month - 1) {
                                $data['Voc300'][$i] += $sim->Counter;
                            }
                        }
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
        }
        return $data;
    }

    static function getVouchersTopUp() {
        $year = Input::get('year');
//        $year = '2016';
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');

        $data = [];
        //1 -> evoucher; 2 -> phvoucher
        $all_ivr = [];
        // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
        $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%topup%\'')->get();
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = '';
                $temp_stat = $ivr->Status;
                if (substr($temp_stat, 0, 1) == '1') {
                    $stats = 'pV100';
                } else if (substr($temp_stat, 0, 1) == '2') {
                    $stats = 'pV300';
                } else if (substr($temp_stat, 0, 1) == '3') {
                    $stats = 'eV50';
                } else if (substr($temp_stat, 0, 1) == '4') {
                    $stats = 'eV100';
                } else if (substr($temp_stat, 0, 1) == '5') {
                    $stats = 'eV300';
                }
                if ($stats != '') {
                    if (!isset($data[$stats]))
                        $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    for ($i = 0; $i < 12; $i++) {
                        if ($i == $ivr->Month - 1) {
                            $data[$stats][$i] += $ivr->Counter;
                        }
                    }
                }
            }
        }
        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%topup%\'')->get();
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = '';
                        $temp_stat = $ivr->Status;
                        if (substr($temp_stat, 0, 1) == '1') {
                            $stats = 'pV100';
                        } else if (substr($temp_stat, 0, 1) == '2') {
                            $stats = 'pV300';
                        } else if (substr($temp_stat, 0, 1) == '3') {
                            $stats = 'eV50';
                        } else if (substr($temp_stat, 0, 1) == '4') {
                            $stats = 'eV100';
                        } else if (substr($temp_stat, 0, 1) == '5') {
                            $stats = 'eV300';
                        }
                        if ($stats != '') {
                            if (!isset($data[$stats]))
                                $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                            for ($i = 0; $i < 12; $i++) {
                                if ($i == $ivr->Month - 1) {
                                    $data[$stats][$i] += $ivr->Counter;
                                }
                            }
                        }
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
        }



        return $data;
    }

    static function geteVouchersTopUp() {
        $year = Input::get('year');
//        $year = '2016';
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');

        $data = [];
        //1 -> evoucher; 2 -> phvoucher
        $all_ivr = [];
        // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
        $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%topup%\'')->get();
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = '';
                $temp_stat = $ivr->Status;
                if (substr($temp_stat, 0, 1) == '3') {
                    $stats = 'eV50';
                } else if (substr($temp_stat, 0, 1) == '4') {
                    $stats = 'eV100';
                } else if (substr($temp_stat, 0, 1) == '5') {
                    $stats = 'eV300';
                }
                if ($stats != '') {
                    if (!isset($data[$stats]))
                        $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    for ($i = 0; $i < 12; $i++) {
                        if ($i == $ivr->Month - 1) {
                            $data[$stats][$i] += $ivr->Counter;
                        }
                    }
                }
            }
        }

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%topup%\'')->get();
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = '';
                        $temp_stat = $ivr->Status;
                        if (substr($temp_stat, 0, 1) == '3') {
                            $stats = 'eV50';
                        } else if (substr($temp_stat, 0, 1) == '4') {
                            $stats = 'eV100';
                        } else if (substr($temp_stat, 0, 1) == '5') {
                            $stats = 'eV300';
                        }
                        if ($stats != '') {
                            if (!isset($data[$stats]))
                                $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                            for ($i = 0; $i < 12; $i++) {
                                if ($i == $ivr->Month - 1) {
                                    $data[$stats][$i] += $ivr->Counter;
                                }
                            }
                        }
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
        }
        return $data;
    }

    static function getMSISDNTopUp() {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];
        //1 -> evoucher; 2 -> phvoucher
        $counts = Inventory::select(DB::raw('count(DISTINCT `TopUpMSISDN`) as "Counter",MONTH(`TopUpDate`) as "Month"'))->
                        whereRaw('`TopUpDate` LIKE "%' . $year . '%" GROUP BY CONCAT(MONTH(`TopUpDate`),YEAR(`TopUpDate`))')->get();
        if ($counts != null) {
            foreach ($counts as $count) {
                if (!isset($data['Top Up MSISDN']))
                    $data['Top Up MSISDN'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $count->Month - 1) {
                        $data['Top Up MSISDN'][$i] += $count->Counter;
                    }
                }
            }
        }

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                $counts = Inventory::select(DB::raw('count(DISTINCT `TopUpMSISDN`) as "Counter",MONTH(`TopUpDate`) as "Month"'))->
                                whereRaw('`TopUpDate` LIKE "%' . $year->Year . '%" GROUP BY CONCAT(MONTH(`TopUpDate`),YEAR(`TopUpDate`))')->get();
                if ($counts != null) {
                    foreach ($counts as $count) {
                        if (!isset($data['Top Up MSISDN']))
                            $data['Top Up MSISDN'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $count->Month - 1) {
                                $data['Top Up MSISDN'][$i] += $count->Counter;
                            }
                        }
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
        }
        return $data;
    }

    static function getChurnDetail() {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2018';
//        $type = '';
        $data = [];
        $data['Churn'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $data['Active MSISDN'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        //1 -> evoucher; 2 -> phvoucher
        $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Chact%\'')->get();
        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Activation%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data['Churn'][$i] = -($ivr->Counter);
                    }
                }
            }
        }
        if ($all_act != null) {
            foreach ($all_act as $ivr) {
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data['Active MSISDN'][$i] = $ivr->Counter + $data['Churn'][$i];
                    }
                }
            }
        }
        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                $data['Churn'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $data['Active MSISDN'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                //1 -> evoucher; 2 -> phvoucher
                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%Chact%\'')->get();
                $all_act = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%Activation%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data['Churn'][$i] = -($ivr->Counter);
                            }
                        }
                    }
                }
                if ($all_act != null) {
                    foreach ($all_act as $ivr) {
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data['Active MSISDN'][$i] = $ivr->Counter + $data['Churn'][$i];
                            }
                        }
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9], $a[10], $a[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
        }
        return $data;
    }

    static function postShipin() {
        $sn = Input::get('sn');
        $msisdn = Input::get('msisdn');
        $check_counter = History::select('ID')->orderBy('ID', 'DESC')->first();
        if ($check_counter == null)
            $id_counter = 1;
        else
            $id_counter = $check_counter->ID + 1;

        $type = '3';
        if ($msisdn != NULL)
            $type = '4';

        $for_raw = "('{$sn}',0,0,0,'{$id_counter}','TELIN TAIWAN','{$type}','{$msisdn}','TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'shipin from uncatagorized',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
        DB::insert("INSERT INTO m_inventory VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE SerialNumber=SerialNumber;");

        $for_raw = "('{$id_counter}','{$sn}','-','TELIN TAIWAN',0,CONCAT(CURDATE(),'/SI/TST001'),NULL,'0','{$id_counter}',0,CURDATE(),'shipin from uncatagorized',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
        DB::insert("INSERT INTO m_historymovement VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE ID=ID;");
    }

    static function postRemark() {
        $sn = Input::get('sn');
        $remark = Input::get('new_remark');
//        $sn = 'FM155012310699003306';
//        $remark = 'unfound from churn file2';
        DB::Update("UPDATE `m_uncatagorized` SET `Remark` = '{$remark}' WHERE `SerialNumber` LIKE '{$sn}'");
    }

    static function postMissing() {
        $sn = Input::get('sn');
        $inv = Inventory::find($sn);
        $inv->Missing = 1;
        $inv->save();

        $idx = 0;
        $found = false;
        $all_start = explode(',,,', Session::get('temp_inv_start'));
        $all_end = explode(',,,', Session::get('temp_inv_end'));
        for ($i = 0; $i < count($all_start); $i++) {
            if ($sn >= $all_start[$i] && $sn <= $all_end[$i]) {
                $idx = $i;
                $found = true;
            }
        }
        if ($found) {
            $last_inv = explode(',,,', Session::get('temp_inv_qty'));
            $last_inv[$idx] -= 1;
            $temp_string_a = '';
            foreach ($last_inv as $invs) {
                if ($temp_string_a == '') {
                    $temp_string_a = $invs;
                } else {
                    $temp_string_a .= ',,,' . $invs;
                }
            }
            Session::put('temp_inv_qty', $temp_string_a);
        }
    }

    static function changeFB() {
        $hists = History::where('ShipoutNumber', Session::get('FormSeriesInv'))->get();
        $fabiao = Input::get('fab');
        $counter = 0;
        foreach ($hists as $hist) {
            $hist->FabiaoNumber = $fabiao;
            $hist->save();
            $counter++;
        }
        return $counter;
    }

    static function postConsStat() {
        Session::put('conses', Input::get('cs'));
    }

    static function postNewAgent() {
        Session::put('NewAgent', Input::get('agent'));
    }

    static function postNewWh() {
        Session::put('NewWarehouse', Input::get('wh'));
    }

    static function postFormSeries() {
        Session::put('FormSeries', Input::get('fs'));
        Session::put('FormSeriesInv', Input::get('fs'));
    }

    static function postWarehouse() {
        Session::put('WarehouseInv', Input::get('wh'));
    }

    static function postST() {
        Session::put('ShipouttoInv', Input::get('st'));
    }

    static function delST() {
        Session::forget('ShipouttoInv');
    }

    static function postSemuaSN() {
        $sn = Input::get('sn');
        $sn = explode(",", $sn);
        Session::put('SemuaSN', $sn);
    }

    static function exportExcel($filter) {
        ini_set('memory_limit', '3000M');
        $invs = '';
        $filter = explode(',,,', $filter);
        $typesym = '>=';
        $type = '0';
        $filenames = 'all';
        $statussym = '>=';
        $status = '0';
        $fs = '';
        $wh = '';
        $st = '';
        if ($filter[0] != 'all') {
            $typesym = '=';
            $type = $filter[0];
            if ($filter[0] == '1') {
                $filenames = 'sim3g';
            } else if ($filter[0] == '2') {
                $filenames = 'evoc';
            } else if ($filter[0] == '3') {
                $filenames = 'phvoc';
            } else if ($filter[0] == '4') {
                $filenames = 'sim4g';
            }
        }
        if (Session::has('WarehouseInv')) {
            $wh = Session::get('WarehouseInv');
            $filenames .= '_' . $wh;
        }
        if (Session::has('ShipouttoInv')) {
            $st = Session::get('ShipouttoInv');
            $filenames .= '_' . $st;
        }
        if (Session::has('FormSeriesInv')) {
            $fs = Session::get('FormSeriesInv');
            $filenames .= '_' . str_replace('/', '_', $fs);
        }
        if (isset($filter[1])) {
            $statussym = '=';
            $status = $filter[1];
            if ($filter[1] == '0') {
                $filenames .= '_shipin';
            } else if ($filter[1] == '1') {
                $filenames .= '_return';
            } else if ($filter[1] == '2') {
                $filenames .= '_shipout';
            } else if ($filter[1] == '3') {
                $filenames .= '_warehouse';
            } else if ($filter[1] == '4') {
                $filenames .= '_consignment';
            }
        }

        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/inventory_" . $filenames . ".xlsx";
        $writer->openToFile($filePath);
        $myArr = array("SERIAL NUMBER", "MSISDN", "TYPE", "LAST STATUS", "SHIPOUT TO", "SUBAGENT", "FORM SERIES", "LAST WAREHOUSE", "SHIPOUT DATE", "SHIPOUT PRICE", "SHIPIN DATE", "SHIPIN PRICE", "REMARK");
        $writer->addRow($myArr); // add a row at a time

        if ($fs == '') {
            $invs = DB::table('m_inventory as inv1')
                            ->join('m_historymovement', 'inv1.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('inv1.Type', $typesym, $type)
                            ->where('m_historymovement.Status', $statussym, $status)->select(DB::raw('inv1.SerialNumber, inv1.MSISDN, inv1.Type, m_historymovement.Status,'
                                . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                                . '(SELECT SubAgent FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "SubAgent", '
                                . '(SELECT `ShipoutNumber` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutNumber", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutDate", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutPrice", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinPrice", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinDate"'))->get();
            
            if ($wh != '') {
                $invs = DB::table('m_inventory as inv1')
                                ->join('m_historymovement', 'inv1.LastStatusID', '=', 'm_historymovement.ID')
                                ->where('inv1.Type', $typesym, $type)->where('inv1.LastWarehouse', 'LIKE', '%' . $wh . '%')
                                ->where('m_historymovement.Status', $statussym, $status)->select(DB::raw('inv1.SerialNumber, inv1.MSISDN, inv1.Type, m_historymovement.Status,'
                                . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                                . '(SELECT SubAgent FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "SubAgent", '
                                . '(SELECT `ShipoutNumber` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutNumber", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutDate", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutPrice", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinPrice", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinDate"'))->get();
                if ($st != '') {
                    $invs = DB::table('m_inventory as inv1')
                                    ->join('m_historymovement', 'inv1.LastStatusID', '=', 'm_historymovement.ID')
                                    ->where('inv1.Type', $typesym, $type)->where('inv1.LastWarehouse', 'LIKE', '%' . $wh . '%')
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $st . '%')
                                    ->where('m_historymovement.Status', $statussym, $status)->select(DB::raw('inv1.SerialNumber, inv1.MSISDN, inv1.Type, m_historymovement.Status,'
                                . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                                . '(SELECT SubAgent FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "SubAgent", '
                                . '(SELECT `ShipoutNumber` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutNumber", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutDate", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutPrice", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinPrice", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinDate"'))->get();
                }
            } else {
                if ($st != '') {
                    $invs = DB::table('m_inventory')
                                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                                    ->where('m_inventory.Type', $typesym, $type)
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $st . '%')
                                    ->where('m_historymovement.Status', $statussym, $status)->select(DB::raw('inv1.SerialNumber, inv1.MSISDN, inv1.Type, m_historymovement.Status,'
                                . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                                . '(SELECT SubAgent FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "SubAgent", '
                                . '(SELECT `ShipoutNumber` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutNumber", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutDate", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutPrice", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinPrice", '
                                . '(SELECT Date FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinDate"'))->get();
                }
            }
        } else if ($fs != '') {
            $invs = DB::table('m_inventory as inv1')
                            ->join('m_historymovement', 'inv1.SerialNumber', '=', 'm_historymovement.SN')
                            ->where('inv1.Type', $typesym, $type)
                            ->where('inv1.LastStatusHist', $statussym, $status)
                            ->where('m_historymovement.ShipoutNumber', 'like', '%' . $fs . '%')->select(DB::raw('inv1.SerialNumber, inv1.MSISDN, inv1.Type, m_historymovement.Status,'
                                . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                                . '(SELECT SubAgent FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "SubAgent", '
                                . '(SELECT `ShipoutNumber` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutNumber", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutDate", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutPrice", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinPrice", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinDate"'))->get();
            if ($wh != '') {
                $invs = DB::table('m_inventory as inv1')
                                ->join('m_historymovement', 'inv1.SerialNumber', '=', 'm_historymovement.SN')
                                ->where('inv1.Type', $typesym, $type)
                                ->where('m_historymovement.Status', $statussym, $status)->where('inv1.LastWarehouse', 'LIKE', '%' . $wh . '%')
                                ->where('m_historymovement.ShipoutNumber', 'like', '%' . $fs . '%')->select(DB::raw('inv1.SerialNumber, inv1.MSISDN, inv1.Type, m_historymovement.Status,'
                                . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                                . '(SELECT SubAgent FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "SubAgent", '
                                . '(SELECT `ShipoutNumber` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutNumber", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutDate", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutPrice", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinPrice", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinDate"'))->get();
                if ($st != '') {
                    $invs = DB::table('m_inventory as inv1')
                                    ->join('m_historymovement', 'inv1.SerialNumber', '=', 'm_historymovement.SN')
                                    ->where('inv1.Type', $typesym, $type)
                                    ->where('m_historymovement.Status', $statussym, $status)->where('inv1.LastWarehouse', 'LIKE', '%' . $wh . '%')
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $st . '%')
                                    ->where('m_historymovement.ShipoutNumber', 'like', '%' . $fs . '%')->select(DB::raw('inv1.SerialNumber, inv1.MSISDN, inv1.Type, m_historymovement.Status,'
                                . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                                . '(SELECT SubAgent FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "SubAgent", '
                                . '(SELECT `ShipoutNumber` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutNumber", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutDate", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutPrice", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinPrice", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinDate"'))->get();
                }
            } else {
                if ($st != '') {
                    $invs = DB::table('m_inventory as inv1')
                                    ->join('m_historymovement', 'inv1.SerialNumber', '=', 'm_historymovement.SN')
                                    ->where('inv1.Type', $typesym, $type)
                                    ->where('m_historymovement.Status', $statussym, $status)
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $st . '%')
                                    ->where('m_historymovement.ShipoutNumber', 'like', '%' . $fs . '%')->select(DB::raw('inv1.SerialNumber, inv1.MSISDN, inv1.Type, m_historymovement.Status,'
                                . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                                . '(SELECT SubAgent FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "SubAgent", '
                                . '(SELECT `ShipoutNumber` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutNumber", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutDate", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "2" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipoutPrice", '
                                . '(SELECT Price FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinPrice", '
                                . '(SELECT `Date` FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinDate"'))->get();
                }
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
            $status = 'Available';
            $cons = 'no';
            $shipoutdt = '';
            $shipoutprice = '0';
            $shipindt = $inv->ShipinDate;
            if ($inv->Status == 1) {
                $status = 'Return';
            } else if ($inv->Status == 2) {
                $status = 'Shipout';
                $shipoutdt = $inv->ShipoutDate;
                $shipoutprice = $inv->ShipoutPrice;
            } else if ($inv->Status == 3) {
                $status = 'Warehouse';
            } else if ($inv->Status == 4) {
                $status = 'Consignment';
            }

            $shipout = '';
            $agent = '';
            $subagent = '';
            $tempcount = 0;
            if ($inv->SubAgent != '') {
                $shipout = explode(' ', $inv->SubAgent);
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
            $myArr = array($inv->SerialNumber, $inv->MSISDN, $type, $status, $agent, $inv->SubAgent, $inv->ShipoutNumber, $inv->LastWarehouse, $shipoutdt, $shipoutprice, $shipindt, $inv->ShipinPrice, $inv->Remark);
            $writer->addRow($myArr);
        }
        $writer->close();
        return "/inventory_" . $filenames . ".xlsx";
    }

    static function postDashboard() {
        $year = Input::get("year");
        //$year = "2017";
        $wh = Input::get("wh");
        $quartal = Input::get("qt");
        //$quartal = 1;
        $start_month = 1;
        $end_month = 3;
        $arr_subagent = [];
        $arr_1shipout = [];
        $arr_1active = [];
        $arr_1apf = [];
        $arr_2shipout = [];
        $arr_2active = [];
        $arr_2apf = [];
        $arr_3shipout = [];
        $arr_3active = [];
        $arr_3apf = [];
        DB::table('r_shipout_subagent')->delete();
        if ($quartal == '2') {
            $start_month = 4;
            $end_month = 6;
        } else if ($quartal == '3') {
            $start_month = 7;
            $end_month = 9;
        } else if ($quartal == '4') {
            $start_month = 10;
            $end_month = 12;
        }

        $allchan = DB::table('m_historymovement')
                        ->select(DB::raw(" DISTINCT `SubAgent`"))->where('Status', 2)->get();

        foreach ($allchan as $subagent) {
            array_push($arr_subagent, $subagent->SubAgent);

            $simshipout = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                            ->whereRaw('m_inventory.Type IN ("4","1")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                            ->whereRaw('MONTH(m_historymovement.Date) >= ' . $start_month)->whereRaw('MONTH(m_historymovement.Date) <= ' . $end_month)
                            ->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
                            ->where('m_historymovement.SubAgent', 'LIKE', '%' . $subagent->SubAgent . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                            ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

            $simactive = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                            ->whereRaw('m_inventory.Type IN ("4","1")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)->whereRaw('m_inventory.ActivationDate IS NOT NULL')
                            ->whereRaw('MONTH(m_historymovement.Date) >= ' . $start_month)->whereRaw('MONTH(m_historymovement.Date) <= ' . $end_month)
                            ->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
                            ->where('m_historymovement.SubAgent', 'LIKE', '%' . $subagent->SubAgent . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                            ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

            $simapfret = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                            ->whereRaw('m_inventory.Type IN ("4","1")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)->whereRaw('m_inventory.ApfDate IS NOT NULL')
                            ->whereRaw('MONTH(m_historymovement.Date) >= ' . $start_month)->whereRaw('MONTH(m_historymovement.Date) <= ' . $end_month)
                            ->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
                            ->where('m_historymovement.SubAgent', 'LIKE', '%' . $subagent->SubAgent . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                            ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

            if (isset($simshipout)) {
                $counter1 = 0;
                $counter2 = 0;
                $counter3 = 0;
                foreach ($simshipout as $eachship) {
                    if ($eachship->month == 1) {
                        array_push($arr_1shipout, $eachship->counter);
                        $counter1++;
                    } else if ($eachship->month == 2) {
                        array_push($arr_2shipout, $eachship->counter);
                        $counter2++;
                    } else if ($eachship->month == 3) {
                        array_push($arr_3shipout, $eachship->counter);
                        $counter3++;
                    }
                }
                if ($counter1 == 0)
                    array_push($arr_1shipout, 0);
                if ($counter2 == 0)
                    array_push($arr_2shipout, 0);
                if ($counter3 == 0)
                    array_push($arr_3shipout, 0);
            } else {
                array_push($arr_1shipout, 0);
                array_push($arr_2shipout, 0);
                array_push($arr_3shipout, 0);
            }

            if (isset($simapfret)) {
                $counter1 = 0;
                $counter2 = 0;
                $counter3 = 0;
                foreach ($simapfret as $eachship) {
                    if ($eachship->month == 1) {
                        array_push($arr_1apf, $eachship->counter);
                        $counter1++;
                    } else if ($eachship->month == 2) {
                        array_push($arr_2apf, $eachship->counter);
                        $counter2++;
                    } else if ($eachship->month == 3) {
                        array_push($arr_3apf, $eachship->counter);
                        $counter3++;
                    }
                }
                if ($counter1 == 0)
                    array_push($arr_1apf, 0);
                if ($counter2 == 0)
                    array_push($arr_2apf, 0);
                if ($counter3 == 0)
                    array_push($arr_3apf, 0);
            } else {
                array_push($arr_1apf, 0);
                array_push($arr_2apf, 0);
                array_push($arr_3apf, 0);
            }
            if (isset($simactive)) {
                $counter1 = 0;
                $counter2 = 0;
                $counter3 = 0;
                foreach ($simactive as $eachship) {
                    if ($eachship->month == 1) {
                        array_push($arr_1active, $eachship->counter);
                        $counter1++;
                    } else if ($eachship->month == 2) {
                        array_push($arr_2active, $eachship->counter);
                        $counter2++;
                    } else if ($eachship->month == 3) {
                        array_push($arr_3active, $eachship->counter);
                        $counter3++;
                    }
                }
                if ($counter1 == 0)
                    array_push($arr_1active, 0);
                if ($counter2 == 0)
                    array_push($arr_2active, 0);
                if ($counter3 == 0)
                    array_push($arr_3active, 0);
            } else {
                array_push($arr_1active, 0);
                array_push($arr_2active, 0);
                array_push($arr_3active, 0);
            }
        }
//        dd('subagent:'.count($arr_subagent).'so1:'.count($arr_1shipout).'so2:'.count($arr_2shipout).'so3:'.count($arr_3shipout).'act1:'.count($arr_1active).'act2:'.count($arr_2active).'act3:'.count($arr_3active).'apf1:'.count($arr_1apf).'apf2:'.count($arr_2apf).'apf3:'.count($arr_3apf));

        $for_raw = '';
        for ($i = 0; $i < count($arr_subagent); $i++) {
            if ($i == 0)
                $for_raw .= "('" . $arr_subagent[$i] . "','" . $arr_1shipout[$i] . "','" . $arr_2shipout[$i] . "','" . $arr_3shipout[$i] . "','" . $arr_1active[$i] . "','" . $arr_2active[$i] . "','" . $arr_3active[$i] . "','" . $arr_1apf[$i] . "','" . $arr_2apf[$i] . "','" . $arr_3apf[$i] . "')";
            else
                $for_raw .= ",('" . $arr_subagent[$i] . "','" . $arr_1shipout[$i] . "','" . $arr_2shipout[$i] . "','" . $arr_3shipout[$i] . "','" . $arr_1active[$i] . "','" . $arr_2active[$i] . "','" . $arr_3active[$i] . "','" . $arr_1apf[$i] . "','" . $arr_2apf[$i] . "','" . $arr_3apf[$i] . "')";
        }
        DB::insert("INSERT INTO r_shipout_subagent VALUES " . $for_raw);
        return 'true';
    }

    static function exportExcelWeeklyDashboard() {
        $date = Input::get("argyear");
//        $date = "2018-04-15";
        $year = explode("-", $date)[0];
        $month = explode("-", $date)[1];
        $day = explode("-", $date)[2];
        $day = $day - 1;

        if (substr($month, 0, 1) === "0") {
            $month = substr($month, 1, 1);
        }
        $last_year = $year;
        $last_month = $month - 1;
        $last_day = $day - 1;
        if ($day === '1')
            $last_day = $day;
        if ($month === "01" || $month === "1") {
            $last_year = $year - 1;
            $last_month = 12;
        }
        $filenames = $month . "-" . $year . ' vs ' . $last_month . "-" . $last_year;
        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/Weekly_Performance_" . $filenames . ".xlsx";
        $writer->openToFile($filePath);
        $myArr = array($day . "-" . $month . "-" . $year . ' vs ' . $day . "-" . $last_month . "-" . $year);
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("Peformance Report Per Week (Best on Current Date Transaction Month Over Month)");
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("ITEMS", "UNIT", "CM", "BM", 'GROWTH');
        $writer->addRow($myArr); // add a row at a time

        $data = array();
        $all_ivr = Inventory::whereRaw("ChurnDate IS NOT NULL AND YEAR(ChurnDate) LIKE '{$year}' AND MONTH(ChurnDate) LIKE '{$month}' AND DAY(ChurnDate) >= '1' AND DAY(ChurnDate) <= '{$day}'")->select(DB::raw("COUNT(MSISDN) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['churn'][0] = $all_ivr[0]->Counter;
        else {
            $data['churn'][0] = 1;
        }
        $all_ivr = Inventory::whereRaw("ChurnDate IS NOT NULL AND YEAR(ChurnDate) LIKE '{$year}' AND MONTH(ChurnDate) LIKE '{$last_month}' AND DAY(ChurnDate) >= '1' AND DAY(ChurnDate) <= '{$day}'")->select(DB::raw("COUNT(MSISDN) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['churn'][1] = $all_ivr[0]->Counter;
        else {
            $data['churn'][1] = 1;
        }
        $all_ivr = Inventory::whereRaw("ActivationDate IS NOT NULL AND YEAR(ActivationDate) LIKE '{$year}' AND MONTH(ActivationDate) LIKE '{$month}' AND DAY(ActivationDate) >= '1' AND DAY(ActivationDate) <= '{$day}'")->select(DB::raw("COUNT(MSISDN) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['act'][0] = $all_ivr[0]->Counter;
        else {
            $data['act'][0] = 1;
        }
        $all_ivr = Inventory::whereRaw("ActivationDate IS NOT NULL AND YEAR(ActivationDate) LIKE '{$year}' AND MONTH(ActivationDate) LIKE '{$last_month}' AND DAY(ActivationDate) >= '1' AND DAY(ActivationDate) <= '{$day}'")->select(DB::raw("COUNT(MSISDN) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['act'][1] = $all_ivr[0]->Counter;
        else {
            $data['act'][1] = 1;
        }
        //total process
        $data['churn'][2] = round((($data['churn'][0] - $data['churn'][1]) / $data['churn'][0]) * 100, 2);
        $data['act'][2] = round((($data['act'][0] - $data['act'][1]) / $data['act'][0]) * 100, 2);

        $data["net"][0] = $data['act'][0] - $data['churn'][0];
        $data["net"][1] = $data['act'][1] - $data['churn'][1];
        if ($data['net'][0] === 0)
            $data['net'][0] = 1;
        $data['net'][2] = round((($data['net'][0] - $data['net'][1]) / $data['net'][0]) * 100, 2);

        $myArr = array("NET ADDITIONAL", "SUBS", $data["net"][0], $data["net"][1], $data["net"][2] . '%');
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("ACQUITITION", "SUBS", $data["act"][0], $data["act"][1], $data["act"][2] . '%');
        $writer->addRow($myArr);
        $myArr = array("CHURN", "SUBS", $data["churn"][0], $data["churn"][1], $data["churn"][2] . '%');
        $writer->addRow($myArr);
        $writer->addRow(['']);

        $data = array();
        $all_ivr = Inventory::whereRaw("TopUpDate IS NOT NULL AND YEAR(TopUpDate) LIKE '{$year}' AND MONTH(TopUpDate) LIKE '{$month}' AND DAY(TopUpDate) >= '1' AND DAY(TopUpDate) <= '{$day}' AND (`SerialNumber` LIKE '%KR0250%')")->select(DB::raw("COUNT(SerialNumber) as Counter"))->get();
        // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
        $data['PH300'][0] = 1;
        $data['E300'][0] = 1;
        $data['PH300'][1] = 1;
        $data['E300'][1] = 1;
        if (count($all_ivr) > 0)
            $data['E300'][0] = $all_ivr[0]->Counter;

        $all_ivr = Inventory::whereRaw("TopUpDate IS NOT NULL AND YEAR(TopUpDate) LIKE '{$year}' AND MONTH(TopUpDate) LIKE '{$month}' AND DAY(TopUpDate) >= '1' AND DAY(TopUpDate) <= '{$day}' AND (`SerialNumber` LIKE '%KR1850%')")->select(DB::raw("COUNT(SerialNumber) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['PH300'][0] = $all_ivr[0]->Counter;

        $all_ivr = Inventory::whereRaw("TopUpDate IS NOT NULL AND YEAR(TopUpDate) LIKE '{$year}' AND MONTH(TopUpDate) LIKE '{$last_month}' AND DAY(TopUpDate) >= '1' AND DAY(TopUpDate) <= '{$day}' AND (`SerialNumber` LIKE '%KR1850%')")->select(DB::raw("COUNT(SerialNumber) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['PH300'][1] = $all_ivr[0]->Counter;

        $all_ivr = Inventory::whereRaw("TopUpDate IS NOT NULL AND YEAR(TopUpDate) LIKE '{$year}' AND MONTH(TopUpDate) LIKE '{$last_month}' AND DAY(TopUpDate) >= '1' AND DAY(TopUpDate) <= '{$day}' AND (`SerialNumber` LIKE '%KR0250%')")->select(DB::raw("COUNT(SerialNumber) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['E300'][1] = $all_ivr[0]->Counter;
        //total process
        $data['PH300'][2] = round((($data['PH300'][0] - $data['PH300'][1]) / $data['PH300'][0]) * 100, 2);
        $data['E300'][2] = round((($data['E300'][0] - $data['E300'][1]) / $data['E300'][0]) * 100, 2);

        $data["300NT"][0] = $data['PH300'][0] + $data['E300'][0];
        $data["300NT"][1] = $data['PH300'][1] + $data['E300'][1];
        $data['300NT'][2] = round((($data['300NT'][0] - $data['300NT'][1]) / $data['300NT'][0]) * 100, 2);

        $myArr = array("VOUCHERS 300NT", "CARDS", $data["300NT"][0], $data["300NT"][1], $data["300NT"][2] . '%');
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("VOUCHERS PHYSICAL 300NT", "CARDS", $data["PH300"][0], $data["PH300"][1], $data["PH300"][2] . '%');
        $writer->addRow($myArr);
        $myArr = array("VOUCHERS ELECTRIC 300NT", "CARDS", $data["E300"][0], $data["E300"][1], $data["E300"][2] . '%');
        $writer->addRow($myArr);
        $writer->addRow(['']);

        $data = array();
        $data['1GB'][0] = 1;
        $data['2GB'][0] = 1;
        $data['30DAY'][0] = 1;
        $data['1GB'][1] = 1;
        $data['2GB'][1] = 1;
        $data['30DAY'][1] = 1;

        $all_ivr = DB::table("m_ivr")->whereRaw("Date IS NOT NULL AND YEAR(Date) LIKE '{$year}' AND MONTH(Date) LIKE "
                                . "'{$month}' AND DAY(Date) >= '1' AND DAY(Date) <= '{$day}' AND PurchaseAmount LIKE '180'")
                        ->select(DB::raw("COUNT(MSISDN_) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['1GB'][0] = $all_ivr[0]->Counter;

        $all_ivr = DB::table("m_ivr")->whereRaw("Date IS NOT NULL AND YEAR(Date) LIKE '{$year}' AND MONTH(Date) LIKE "
                                . "'{$month}' AND DAY(Date) >= '1' AND DAY(Date) <= '{$day}' AND PurchaseAmount LIKE '300'")
                        ->select(DB::raw("COUNT(MSISDN_) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['2GB'][0] = $all_ivr[0]->Counter;

        $all_ivr = DB::table("m_ivr")->whereRaw("Date IS NOT NULL AND YEAR(Date) LIKE '{$year}' AND MONTH(Date) LIKE "
                                . "'{$month}' AND DAY(Date) >= '1' AND DAY(Date) <= '{$day}' AND PurchaseAmount > 300")
                        ->select(DB::raw("COUNT(MSISDN_) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['30DAY'][0] = $all_ivr[0]->Counter;

        $all_ivr = DB::table("m_ivr")->whereRaw("Date IS NOT NULL AND YEAR(Date) LIKE '{$year}' AND MONTH(Date) LIKE "
                                . "'{$last_month}' AND DAY(Date) >= '1' AND DAY(Date) <= '{$day}' AND PurchaseAmount LIKE '180'")
                        ->select(DB::raw("COUNT(MSISDN_) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['1GB'][1] = $all_ivr[0]->Counter;

        $all_ivr = DB::table("m_ivr")->whereRaw("Date IS NOT NULL AND YEAR(Date) LIKE '{$year}' AND MONTH(Date) LIKE "
                                . "'{$last_month}' AND DAY(Date) >= '1' AND DAY(Date) <= '{$day}' AND PurchaseAmount LIKE '300'")
                        ->select(DB::raw("COUNT(MSISDN_) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['2GB'][1] = $all_ivr[0]->Counter;

        $all_ivr = DB::table("m_ivr")->whereRaw("Date IS NOT NULL AND YEAR(Date) LIKE '{$year}' AND MONTH(Date) LIKE "
                                . "'{$last_month}' AND DAY(Date) >= '1' AND DAY(Date) <= '{$day}' AND PurchaseAmount > 300")
                        ->select(DB::raw("COUNT(MSISDN_) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['30DAY'][1] = $all_ivr[0]->Counter;

        //total process
        $data['1GB'][2] = round((($data['1GB'][0] - $data['1GB'][1]) / $data['1GB'][0]) * 100, 2);
        $data['2GB'][2] = round((($data['2GB'][0] - $data['2GB'][1]) / $data['2GB'][0]) * 100, 2);
        $data['30DAY'][2] = round((($data['30DAY'][0] - $data['30DAY'][1]) / $data['30DAY'][0]) * 100, 2);

        $data["INTERNET"][0] = $data['1GB'][0] + $data['2GB'][0] + $data['30DAY'][0];
        $data["INTERNET"][1] = $data['1GB'][1] + $data['2GB'][1] + $data['30DAY'][1];
        $data['INTERNET'][2] = round((($data['INTERNET'][0] - $data['INTERNET'][1]) / $data['INTERNET'][0]) * 100, 2);

        $myArr = array("INTERNET", "SUBS", number_format($data["INTERNET"][0]), number_format($data["INTERNET"][1]), $data["INTERNET"][2] . '%');
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("1GB", "SUBS", number_format($data["1GB"][0]), number_format($data["1GB"][1]), $data["1GB"][2] . '%');
        $writer->addRow($myArr);
        $myArr = array("2GB", "SUBS", number_format($data["2GB"][0]), number_format($data["2GB"][1]), $data["2GB"][2] . '%');
        $writer->addRow($myArr);
        $myArr = array("30 DAYS", "SUBS", number_format($data["30DAY"][0]), number_format($data["30DAY"][1]), $data["30DAY"][2] . '%');
        $writer->addRow($myArr);
        $writer->addRow(['']);

        $tempmonth = $month;
        if (strlen($month) === 1) {
            $tempmonth = "0" . $month;
        }
        $all_ivr = Stats::where('Year', $year)->where('Month', $tempmonth)->whereRaw('Status LIKE \'%_sum%\'')->get();
        $data = array();
        $data['MT'][0] = 1;
        $data['MO'][0] = 1;
        $data['IT'][0] = 1;
        $data['SMS'][0] = 1;
        $data['MT'][1] = 1;
        $data['MO'][1] = 1;
        $data['IT'][1] = 1;
        $data['SMS'][1] = 1;
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $temp_stat = $ivr->Status;
                $temp_counter = $ivr->Counter;
                if (explode('_', $temp_stat)[0] == 'mt') {
                    $temp_counter = round(ceil($temp_counter / 60), 1);
                    $data['MT'][0] = $temp_counter;
                } else if (explode('_', $temp_stat)[0] == 'mo') {
                    $temp_counter = round(ceil($temp_counter / 60), 1);
                    $data['MO'][0] = $temp_counter;
                } else if (explode('_', $temp_stat)[0] == 'internet') {
                    $temp_counter = round($temp_counter, 1);
                    $data['IT'][0] = $temp_counter;
                } else if (explode('_', $temp_stat)[0] == 'sms') {
                    $temp_counter = round($temp_counter, 1);
                    $data['SMS'][0] = $temp_counter;
                }
            }
        }
        $tempmonth = $last_month;
        if (strlen($last_month) === 1) {
            $tempmonth = "0" . $last_month;
        }
        $all_ivr = Stats::where('Year', $year)->where('Month', $tempmonth)->whereRaw('Status LIKE \'%_sum%\'')->get();
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $temp_stat = $ivr->Status;
                $temp_counter = $ivr->Counter;
                if (explode('_', $temp_stat)[0] == 'mt') {
                    $temp_counter = round(ceil($temp_counter / 60), 1);
                    $data['MT'][1] = $temp_counter;
                } else if (explode('_', $temp_stat)[0] == 'mo') {
                    $temp_counter = round(ceil($temp_counter / 60), 1);
                    $data['MO'][1] = $temp_counter;
                } else if (explode('_', $temp_stat)[0] == 'internet') {
                    $temp_counter = round($temp_counter, 1);
                    $data['IT'][1] = $temp_counter;
                } else if (explode('_', $temp_stat)[0] == 'sms') {
                    $temp_counter = round($temp_counter, 1);
                    $data['SMS'][1] = $temp_counter;
                }
            }
        }
        //total process
        $data['MT'][2] = round((($data['MT'][0] - $data['MT'][1]) / $data['MT'][0]) * 100, 2);
        $data['MO'][2] = round((($data['MO'][0] - $data['MO'][1]) / $data['MO'][0]) * 100, 2);
        $data['IT'][2] = round((($data['IT'][0] - $data['IT'][1]) / $data['IT'][0]) * 100, 2);
        $data['SMS'][2] = round((($data['SMS'][0] - $data['SMS'][1]) / $data['SMS'][0]) * 100, 2);

        $data["MVNO_CALL"][0] = $data['MT'][0] + $data['MO'][0];
        $data["MVNO_CALL"][1] = $data['MT'][1] + $data['MO'][1];
        $data['MVNO_CALL'][2] = round((($data['MVNO_CALL'][0] - $data['MVNO_CALL'][1]) / $data['MVNO_CALL'][0]) * 100, 2);

        $myArr = array("MVNO CALL", "MINS", number_format($data["MVNO_CALL"][0]), number_format($data["MVNO_CALL"][1]), $data["MVNO_CALL"][2] . '%');
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("MO CALL", "MINS", number_format($data["MO"][0]), number_format($data["MO"][1]), $data["MO"][2] . '%');
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("MT CALL", "MINS", number_format($data["MT"][0]), number_format($data["MT"][1]), $data["MT"][2] . '%');
        $writer->addRow($myArr); // add a row at a time
        $writer->addRow(['']);
        $myArr = array("SMS", "TEXT", number_format($data["SMS"][0]), number_format($data["SMS"][1]), $data["SMS"][2] . '%');
        $writer->addRow($myArr); // add a row at a time
        $writer->addRow(['']);
        $myArr = array("INTERNET", "GB", number_format($data["IT"][0]), number_format($data["IT"][1]), $data["IT"][2] . '%');
        $writer->addRow($myArr); // add a row at a time

        $writer->close();
        return "/Weekly_Performance_" . $filenames . ".xlsx";
    }

    static function exportExcelSIM1Dashboard() {
        $from_year = Input::get("from_year");
//        $from_year = "2017-01-01";
        $to_year = Input::get("to_year");
//        $to_year = "2017-01-31";
        $filenames = $from_year . '_to_' . $to_year;
        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/SIMreport_" . $filenames . ".xlsx";
        $writer->openToFile($filePath);
        $myArr = array("Date: ", $from_year, ' to ', $to_year);
        $writer->addRow($myArr); // add a row at a time
        $writer->addRow(['']);
        $myArr = array("", "", "", "COLUMBIA", "COLUMBIA", "COLUMBIA", "COLUMBIA", "TELIN TAIWAN", "TELIN TAIWAN", "TELIN TAIWAN", "TELIN TAIWAN");
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("SHIPOUT TO", "SUBAGENT", "DATE", "SIM 3G", "SIM 4G", "PH-VOUCHER", "E-VOUCHER", "SIM 3G", "SIM 4G", "PH-VOUCHER", "E-VOUCHER");
        $writer->addRow($myArr); // add a row at a time
        $total = array();
        //price, status
        $shipout = array();
        $free = array();
        $cons = array();
        $return = array();

        $all_data = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                        ->whereRaw('m_historymovement.Date >= "' . $from_year . '"')->whereRaw('m_historymovement.Date <= "' . $to_year . '"')
                        ->where('Deleted', '0')->groupBy(DB::raw("m_historymovement.Date,m_historymovement.SubAgent,m_historymovement.Price, m_historymovement.Status, m_inventory.Type,m_inventory.LastWarehouse"))
                        ->select(DB::raw("count(m_inventory.SerialNumber) as counter,m_historymovement.SubAgent,m_historymovement.Date,m_historymovement.Price,m_historymovement.Status,m_inventory.Type,m_inventory.LastWarehouse"))->get();

        foreach ($all_data as $data) {
            if ($data->Status == 2) {
                if ($data->Price != '0') {
                    $agent = explode(' ', $data->SubAgent);
                    $shipout[$data->SubAgent][$data->Date]["shipoutto"] = $agent[0];
                    $shipout[$data->SubAgent][$data->Date]["subagent"] = '';
                    for ($i = 1; $i < count($agent); $i++) {
                        $shipout[$data->SubAgent][$data->Date]["subagent"] .= $agent[$i];
                    }
                    $shipout[$data->SubAgent][$data->Date]["date"] = $data->Date;
                    $shipout[$data->SubAgent][$data->Date][$data->LastWarehouse][$data->Type] = $data->counter;
                    if (!isset($total[$data->LastWarehouse][$data->Type]))
                        $total[$data->LastWarehouse][$data->Type] = 0;
                    $total[$data->LastWarehouse][$data->Type] += $data->counter;
                } else if ($data->Price === '0') {
                    $agent = explode(' ', $data->SubAgent);
                    $free[$data->SubAgent][$data->Date]["shipoutto"] = $agent[0];
                    $free[$data->SubAgent][$data->Date]["subagent"] = '';
                    for ($i = 1; $i < count($agent); $i++) {
                        $free[$data->SubAgent][$data->Date]["subagent"] .= $agent[$i];
                    }
                    $free[$data->SubAgent][$data->Date]["date"] = $data->Date;
                    $free[$data->SubAgent][$data->Date][$data->LastWarehouse][$data->Type] = $data->counter;
                    if (!isset($total[$data->LastWarehouse][$data->Type]))
                        $total[$data->LastWarehouse][$data->Type] = 0;
                    $total[$data->LastWarehouse][$data->Type] += $data->counter;
                }
            } else if ($data->Status == 1) {
                $agent = explode(' ', $data->SubAgent);
                $cons[$data->SubAgent][$data->Date]["shipoutto"] = $agent[0];
                $cons[$data->SubAgent][$data->Date]["subagent"] = '';
                for ($i = 1; $i < count($agent); $i++) {
                    $cons[$data->SubAgent][$data->Date]["subagent"] .= $agent[$i];
                }
                $cons[$data->SubAgent][$data->Date]["date"] = $data->Date;
                $cons[$data->SubAgent][$data->Date][$data->LastWarehouse][$data->Type] = $data->counter;
                if (!isset($total[$data->LastWarehouse][$data->Type]))
                    $total[$data->LastWarehouse][$data->Type] = 0;
                $total[$data->LastWarehouse][$data->Type] += $data->counter;
            } else if ($data->Status == 4) {
                $agent = explode(' ', $data->SubAgent);
                $return[$data->SubAgent][$data->Date]["shipoutto"] = $agent[0];
                $return[$data->SubAgent][$data->Date]["subagent"] = '';
                for ($i = 1; $i < count($agent); $i++) {
                    $return[$data->SubAgent][$data->Date]["subagent"] .= $agent[$i];
                }
                $return[$data->SubAgent][$data->Date]["date"] = $data->Date;
                $return[$data->SubAgent][$data->Date][$data->LastWarehouse][$data->Type] = $data->counter;
                if (!isset($total[$data->LastWarehouse][$data->Type]))
                    $total[$data->LastWarehouse][$data->Type] = 0;
                $total[$data->LastWarehouse][$data->Type] += $data->counter;
            }
        }
        $myArr = array("SHIPOUT SELL OUT");
        $writer->addRow($myArr); // add a row at a time
        foreach ($shipout as $datas) {
            foreach ($datas as $data) {
                for ($i = 1; $i <= 4; $i++) {
                    if (!isset($data['COLUMBIA'][$i])) {
                        $data['COLUMBIA'][$i] = 0;
                    }
                    if (!isset($data['TELIN TAIWAN'][$i])) {
                        $data['TELIN TAIWAN'][$i] = 0;
                    }
                }
                $myArr = array($data['shipoutto'], $data['subagent'], $data['date'], number_format($data['COLUMBIA'][1]), number_format($data['COLUMBIA'][4])
                    , number_format($data['COLUMBIA'][3]), number_format($data['COLUMBIA'][2]), number_format($data['TELIN TAIWAN'][1])
                    , number_format($data['TELIN TAIWAN'][4]), number_format($data['TELIN TAIWAN'][3]), number_format($data['TELIN TAIWAN'][2]));
                $writer->addRow($myArr); // add a row at a time
            }
        }
        $writer->addRow(['']);

        $myArr = array("SHIPOUT FREE");
        $writer->addRow($myArr); // add a row at a time
        foreach ($free as $datas) {
            foreach ($datas as $data) {
                for ($i = 1; $i <= 4; $i++) {
                    if (!isset($data['COLUMBIA'][$i])) {
                        $data['COLUMBIA'][$i] = 0;
                    }
                    if (!isset($data['TELIN TAIWAN'][$i])) {
                        $data['TELIN TAIWAN'][$i] = 0;
                    }
                }
                $myArr = array($data['shipoutto'], $data['subagent'], $data['date'], number_format($data['COLUMBIA'][1]), number_format($data['COLUMBIA'][4])
                    , number_format($data['COLUMBIA'][3]), number_format($data['COLUMBIA'][2]), number_format($data['TELIN TAIWAN'][1])
                    , number_format($data['TELIN TAIWAN'][4]), number_format($data['TELIN TAIWAN'][3]), number_format($data['TELIN TAIWAN'][2]));
//                $myArr = array($data['shipoutto'], $data['subagent'], $data['date'], $data['COLUMBIA'][1]
//                        , $data['COLUMBIA'][4], $data['COLUMBIA'][3], $data['COLUMBIA'][2], $data['TELIN TAIWAN'][1]
//                        , $data['TELIN TAIWAN'][4], $data['TELIN TAIWAN'][3], $data['TELIN TAIWAN'][2]);
                $writer->addRow($myArr); // add a row at a time
            }
        }
        $writer->addRow(['']);

        $myArr = array("SHIPOUT RETURN");
        $writer->addRow($myArr); // add a row at a time
        foreach ($return as $datas) {
            foreach ($datas as $data) {
                for ($i = 1; $i <= 4; $i++) {
                    if (!isset($data['COLUMBIA'][$i])) {
                        $data['COLUMBIA'][$i] = 0;
                    }
                    if (!isset($data['TELIN TAIWAN'][$i])) {
                        $data['TELIN TAIWAN'][$i] = 0;
                    }
                }
                $myArr = array($data['shipoutto'], $data['subagent'], $data['date'], number_format($data['COLUMBIA'][1]), number_format($data['COLUMBIA'][4])
                    , number_format($data['COLUMBIA'][3]), number_format($data['COLUMBIA'][2]), number_format($data['TELIN TAIWAN'][1])
                    , number_format($data['TELIN TAIWAN'][4]), number_format($data['TELIN TAIWAN'][3]), number_format($data['TELIN TAIWAN'][2]));
//                $myArr = array($data['shipoutto'], $data['subagent'], $data['date'], $data['COLUMBIA'][1], $data['COLUMBIA'][4], $data['COLUMBIA'][3], $data['COLUMBIA'][2], $data['TELIN TAIWAN'][1], $data['TELIN TAIWAN'][4], $data['TELIN TAIWAN'][3], $data['TELIN TAIWAN'][2]);
                $writer->addRow($myArr); // add a row at a time
            }
        }
        $writer->addRow(['']);

        $myArr = array("SHIPOUT CONSIGNMENT");
        $writer->addRow($myArr); // add a row at a time
        foreach ($cons as $datas) {
            foreach ($datas as $data) {
                for ($i = 1; $i <= 4; $i++) {
                    if (!isset($data['COLUMBIA'][$i])) {
                        $data['COLUMBIA'][$i] = 0;
                    }
                    if (!isset($data['TELIN TAIWAN'][$i])) {
                        $data['TELIN TAIWAN'][$i] = 0;
                    }
                }
                $myArr = array($data['shipoutto'], $data['subagent'], $data['date'], number_format($data['COLUMBIA'][1]), number_format($data['COLUMBIA'][4])
                    , number_format($data['COLUMBIA'][3]), number_format($data['COLUMBIA'][2]), number_format($data['TELIN TAIWAN'][1])
                    , number_format($data['TELIN TAIWAN'][4]), number_format($data['TELIN TAIWAN'][3]), number_format($data['TELIN TAIWAN'][2]));
//                $myArr = array($data['shipoutto'], $data['subagent'], $data['date'], $data['COLUMBIA'][1], $data['COLUMBIA'][4], $data['COLUMBIA'][3], $data['COLUMBIA'][2], $data['TELIN TAIWAN'][1], $data['TELIN TAIWAN'][4], $data['TELIN TAIWAN'][3], $data['TELIN TAIWAN'][2]);
                $writer->addRow($myArr); // add a row at a time
            }
        }
        $writer->addRow(['']);

        for ($i = 1; $i <= 4; $i++) {
            if (!isset($total['COLUMBIA'][$i])) {
                $total['COLUMBIA'][$i] = 0;
            }
            if (!isset($total['TELIN TAIWAN'][$i])) {
                $total['TELIN TAIWAN'][$i] = 0;
            }
        }

        $myArr = array("", "", "TOTAL: ", number_format($total['COLUMBIA'][1]), number_format($total['COLUMBIA'][4]), number_format($total['COLUMBIA'][3])
            , number_format($total['COLUMBIA'][2]), number_format($total['TELIN TAIWAN'][1]), number_format($total['TELIN TAIWAN'][4])
            , number_format($total['TELIN TAIWAN'][3]), number_format($total['TELIN TAIWAN'][2]));
        $writer->addRow($myArr); // add a row at a time

        $writer->close();
        return "/SIMreport_" . $filenames . ".xlsx";
    }

    static function exportExcelDashboard() {
        $year = Input::get("argyear");
//        $year = "2017";
        $wh = Input::get("argwh");
        $subagent = Input::get("argsubagent");
//        $subagent = "ASPROF ESTHER";
        $month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        $total = [0, 0, 0, 0, 0, 0];
        $filenames = $year . '_' . $wh . '_' . $subagent;

        $simshipout = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                        ->whereRaw('m_inventory.Type IN ("4","1")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                        ->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
                        ->where('m_historymovement.SubAgent', 'LIKE', '%' . $subagent . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                        ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

        $simactive = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                        ->whereRaw('m_inventory.Type IN ("4","1")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)->whereRaw('m_inventory.ActivationDate IS NOT NULL')
                        ->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
                        ->where('m_historymovement.SubAgent', 'LIKE', '%' . $subagent . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                        ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

        $simnotactive = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                        ->whereRaw('m_inventory.Type IN ("4","1")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)->whereRaw('m_inventory.ActivationDate IS NULL')
                        ->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
                        ->where('m_historymovement.SubAgent', 'LIKE', '%' . $subagent . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                        ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

        $simapfret = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                        ->whereRaw('m_inventory.Type IN ("4","1")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)->whereRaw('m_inventory.ApfDate IS NOT NULL')
                        ->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
                        ->where('m_historymovement.SubAgent', 'LIKE', '%' . $subagent . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                        ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

        $simapfretnotact = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                        ->whereRaw('m_inventory.Type IN ("4","1")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)->whereRaw('m_inventory.ApfDate IS NOT NULL')->whereRaw('m_inventory.ActivationDate IS NULL')
                        ->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
                        ->where('m_historymovement.SubAgent', 'LIKE', '%' . $subagent . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                        ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

        $simapfnotret = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                        ->whereRaw('m_inventory.Type IN ("4","1")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)->whereRaw('m_inventory.ApfDate IS NULL')
                        ->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
                        ->where('m_historymovement.SubAgent', 'LIKE', '%' . $subagent . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                        ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();


        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/subagent_SIMreport_" . $filenames . ".xlsx";
        $writer->openToFile($filePath);
        $myArr = array("Sub Agent:", $subagent);
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("Product:", "SIM CARD");
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("MONTH SHIPOUT", "SIM SHIPOUT", "SIM ACTIVE", "SIM NOT ACTIVE", "APF RETURN", "APF NOT RETURN", "APF RETURN NOT ACTIVE");
        $writer->addRow($myArr); // add a row at a time
        for ($i = 0; $i < 12; $i++) {
            $idx1 = 0;
            $idx2 = 0;
            $idx3 = 0;
            $idx4 = 0;
            $idx5 = 0;
            $idx6 = 0;
            for ($j = 0; $j < 12; $j++) {
                if (isset($simshipout))
                    if (isset($simshipout[$j]))
                        if ($simshipout[$j]->month - 1 == $i) {
                            $idx1 = $simshipout[$j]->counter;
                        }
                if (isset($simactive))
                    if (isset($simactive[$j]))
                        if ($simactive[$j]->month - 1 == $i) {
                            $idx2 = $simactive[$j]->counter;
                        }
                if (isset($simnotactive))
                    if (isset($simnotactive[$j]))
                        if ($simnotactive[$j]->month - 1 == $i) {
                            $idx3 = $simnotactive[$j]->counter;
                        }
                if (isset($simapfret))
                    if (isset($simapfret[$j]))
                        if ($simapfret[$j]->month - 1 == $i) {
                            $idx4 = $simapfret[$j]->counter;
                        }
                if (isset($simapfnotret))
                    if (isset($simapfnotret[$j]))
                        if ($simapfnotret[$j]->month - 1 == $i) {
                            $idx5 = $simapfnotret[$j]->counter;
                        }
                if (isset($simapfretnotact))
                    if (isset($simapfretnotact[$j]))
                        if ($simapfretnotact[$j]->month - 1 == $i) {
                            $idx6 = $simapfretnotact[$j]->counter;
                        }
            }



            $total[0] += $idx1;
            $total[1] += $idx2;
            $total[2] += $idx3;
            $total[3] += $idx4;
            $total[4] += $idx5;
            $total[5] += $idx6;
            $myArr = array($month[$i], number_format($idx1), number_format($idx2), number_format($idx3), number_format($idx4), number_format($idx5), number_format($idx6));
            $writer->addRow($myArr); // add a row at a time
        }
        $myArr = array("TOTAL", number_format($total[0]), number_format($total[1]), number_format($total[2]), number_format($total[3]), number_format($total[4])
            , number_format($total[5]));
        $writer->addRow($myArr); // add a row at a time
        $writer->close();
        return "/subagent_SIMreport_" . $filenames . ".xlsx";
    }

    static function postShipoutDashboard() {
        $year = Input::get("year");
//        $type = Input::get("type");
        $channel = Input::get("channel");
//        $year = '2016';
//        $type = 'SIM Card';
//        $channel = 'DIRECT';
//        $arr_type = "'2','3'";
//        $is_SIM = false;
        $data = [];
//        if ($type === 'SIM Card') {
//            $arr_type = "'1','4'";
//            $is_SIM = true;
//        }
//        if ($is_SIM) {
//            $simshipout = DB::table('m_inventory')
//                            ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
//                            ->whereRaw('m_inventory.Type IN (' . $arr_type . ')')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
//                            ->where('m_historymovement.SubAgent', 'LIKE', '%' . $channel . '%')->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
//                            ->groupBy(DB::raw('MONTH(m_historymovement.Date), m_inventory.Type'))
//                            ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month, m_inventory.Type as type'))->get();
//            foreach($simshipout as $datas){
//                if(!isset($data[$datas->type])){
//                    $data[$datas->type] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//                }
//                $data[$datas->type][$datas->month-1] = $datas->counter;
//            }
//            return $data;
//        } else {
//            $simshipout = DB::table('m_inventory')
//                            ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
//                            ->whereRaw('m_inventory.Type IN (' . $arr_type . ')')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
//                            ->where('m_historymovement.SubAgent', 'LIKE', '%' . $channel . '%')->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
//                            ->groupBy(DB::raw('MONTH(m_historymovement.Date), SUBSTRING(m_inventory.SerialNumber, 1, 6)'))
//                            ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month , SUBSTRING(m_inventory.SerialNumber, 1, 6) as type'))->get();
//            foreach($simshipout as $datas){
//                if(!isset($data[$datas->type])){
//                    $data[$datas->type] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//                }
//                $data[$datas->type][$datas->month-1] = $datas->counter;
//            }
//            return $data;
//        }
        $simshipout = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                        ->whereRaw('m_inventory.Type IN (1,4)')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                        ->where('m_historymovement.SubAgent', 'LIKE', '%' . $channel . '%')->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
                        ->groupBy(DB::raw('MONTH(m_historymovement.Date), m_inventory.Type'))
                        ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month, m_inventory.Type as type'))->get();
        foreach ($simshipout as $datas) {
            if (!isset($data[$datas->type])) {
                $data[$datas->type] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            }
            $data[$datas->type][$datas->month - 1] = $datas->counter;
        }
        $simshipout = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                        ->whereRaw('m_inventory.Type IN (2,3)')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                        ->where('m_historymovement.SubAgent', 'LIKE', '%' . $channel . '%')->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
                        ->groupBy(DB::raw('MONTH(m_historymovement.Date), SUBSTRING(m_inventory.SerialNumber, 1, 6)'))
                        ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month , SUBSTRING(m_inventory.SerialNumber, 1, 6) as type'))->get();
        foreach ($simshipout as $datas) {
            if (!isset($data[$datas->type])) {
                $data[$datas->type] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            }
            $data[$datas->type][$datas->month - 1] = $datas->counter;
        }
        return $data;
    }

    static function postShipinDashboard() {
        $year = Input::get("year");
//        $type = Input::get("type");
        $channel = Input::get("channel");
        $data = [];
        $simshipout = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                        ->whereRaw('m_inventory.Type IN (1,4)')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                        ->where('m_historymovement.Status', '0')->where('m_historymovement.Deleted', '0')
                        ->groupBy(DB::raw('MONTH(m_historymovement.Date), m_inventory.Type'))
                        ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month, m_inventory.Type as type'))->get();
        foreach ($simshipout as $datas) {
            if (!isset($data[$datas->type])) {
                $data[$datas->type] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            }
            $data[$datas->type][$datas->month - 1] = $datas->counter;
        }
        $simshipout = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                        ->whereRaw('m_inventory.Type IN (2,3)')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                        ->where('m_historymovement.Status', '0')->where('m_historymovement.Deleted', '0')
                        ->groupBy(DB::raw('MONTH(m_historymovement.Date), SUBSTRING(m_inventory.SerialNumber, 1, 6)'))
                        ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month , SUBSTRING(m_inventory.SerialNumber, 1, 6) as type'))->get();
        foreach ($simshipout as $datas) {
            if (!isset($data[$datas->type])) {
                $data[$datas->type] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            }
            $data[$datas->type][$datas->month - 1] = $datas->counter;
        }
        return $data;
    }

    static function postUsageDashboard() {
        $year = Input::get("year");
//        $year = '2017';
//        $type = Input::get("type");
//        $channel = Input::get("channel");
        $data = [];
        $simshipout = DB::table('m_inventory')
                        ->whereRaw('Type IN (1,4)')->whereRaw('YEAR(ActivationDate) = ' . $year)
                        ->groupBy(DB::raw('MONTH(ActivationDate), Type'))
                        ->select(DB::raw('count(SerialNumber) as counter, MONTH(ActivationDate) as month , Type'))->get();
        foreach ($simshipout as $datas) {
            if (!isset($data[$datas->Type])) {
                $data[$datas->Type] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            }
            $data[$datas->Type][$datas->month - 1] = $datas->counter;
        }
        $simshipout = DB::table('m_inventory')
                        ->whereRaw('Type IN (2,3)')->whereRaw('YEAR(TopUpDate) = ' . $year)->whereRaw('TopUpMSISDN IS NOT NULL')
                        ->groupBy(DB::raw('MONTH(TopUpDate), SUBSTRING(SerialNumber, 1, 6)'))
                        ->select(DB::raw('count(SerialNumber) as counter, MONTH(TopUpDate) as month , SUBSTRING(SerialNumber, 1, 6) as type'))->get();
        foreach ($simshipout as $datas) {
            if (!isset($data[$datas->type])) {
                $data[$datas->type] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            }
            $data[$datas->type][$datas->month - 1] = $datas->counter;
        }
        return $data;
    }

    static function postUserFilterActive() {
        $state = Input::get("argstate");
        Session::put('UserFilterAct', $state);
    }

    static function postUserFilterv300() {
        $state = Input::get("argstate");
        Session::put('UserFilterv300', $state);
    }

    static function postUserFilterv100() {
        $state = Input::get("argstate");
        Session::put('UserFilterv100', $state);
    }

    static function postUserFilterService() {
        $state = Input::get("argstate");
        Session::put('UserFilterService', $state);
    }

    static function postUserResetFilter() {
        Session::put('UserFilterAct', 0);
        Session::put('UserFilterv300', 0);
        Session::put('UserFilterv100', 0);
        Session::put('UserFilterService', 0);
    }

    static function exportExcelSubAgentDashboard() {
        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/subagent_report.xlsx";
        $writer->openToFile($filePath);
        $year = Input::GET('argyear');
//        $year = '2018';
//
        $myArr = array("All Subagent Reporting");
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("SubAgent", "January Activation", "January Productive", "January Topup", "February Activation", "February Productive", "February Topup"
            , "March Activation", "March Productive", "March Topup", "April Activation", "April Productive", "April Topup", "May Activation", "May Productive"
            , "May Topup", "June Activation", "June Productive", "June Topup", "July Activation", "July Productive", "July Topup", "August Activation", "August Productive", "August Topup"
            , "September Activation", "September Productive", "September Topup", "October Activation", "October Productive", "October Topup", "November Activation", "November Productive", "November Topup"
            , "December Activation", "December Productive", "December Topup");
        $writer->addRow($myArr); // add a row at a time


        $activation = DB::table('m_inventory as inv1')
                        ->join('m_historymovement as hist1', 'inv1.LastStatusID', '=', 'hist1.ID')
                        ->whereRaw("hist1.SubAgent != '-' AND hist1.Status = 2 AND inv1.Type IN ('1','4') AND inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year}'")
                        ->groupBy(DB::raw('hist1.SubAgent, MONTH(inv1.ActivationDate), YEAR(inv1.ActivationDate)'))
                        ->select(DB::raw("hist1.SubAgent, COUNT(inv1.SerialNumber) as 'count', MONTH(inv1.ActivationDate) as 'month', YEAR(inv1.ActivationDate) as 'year'"
                        ))->get();
        $topup = DB::table('m_inventory as inv1')
                        ->join('m_historymovement as hist1', 'inv1.LastStatusID', '=', 'hist1.ID')
                        ->whereRaw("hist1.SubAgent != '-' AND hist1.Status = 2 AND inv1.Type IN ('2','3') AND inv1.TopUpDate IS NOT NULL AND YEAR(inv1.TopUpDate) = '{$year}'")
                        ->groupBy(DB::raw('hist1.SubAgent, MONTH(inv1.TopUpDate), YEAR(inv1.TopUpDate)'))
                        ->select(DB::raw("hist1.SubAgent, COUNT(inv1.SerialNumber) as 'count', MONTH(inv1.TopUpDate) as 'month', YEAR(inv1.TopUpDate) as 'year'"
                        ))->get();
        $prod = DB::table('m_inventory as inv1')
                        ->join('m_historymovement as hist1', 'inv1.LastStatusID', '=', 'hist1.ID')
                        ->join('m_productive as prod1', 'inv1.MSISDN', '=', 'prod1.MSISDN')
                        ->whereRaw("hist1.SubAgent != '-' AND hist1.Status = 2 AND inv1.Type IN ('1','4') AND prod1.Year = '{$year}'")
                        ->groupBy(DB::raw('hist1.SubAgent, prod1.Month, prod1.Year'))
                        ->select(DB::raw("hist1.SubAgent, COUNT(prod1.MSISDN) as 'count', prod1.Month as 'month', prod1.Year as 'year'"
                        ))->get();
        $write_array = [];
        foreach ($activation as $data) {
            if (!isset($write_array[$data->SubAgent]['Activation']))
                $write_array[$data->SubAgent]['Activation'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            if (!isset($write_array[$data->SubAgent]['Topup']))
                $write_array[$data->SubAgent]['Topup'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            if (!isset($write_array[$data->SubAgent]['Productive']))
                $write_array[$data->SubAgent]['Productive'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $month = $data->month;
            if ($month[0] === '0')
                $month = substr($month, 1);
            $write_array[$data->SubAgent]['Activation'][$month - 1] = $data->count;
        }
        foreach ($topup as $data) {
            if (!isset($write_array[$data->SubAgent]['Activation']))
                $write_array[$data->SubAgent]['Activation'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            if (!isset($write_array[$data->SubAgent]['Topup']))
                $write_array[$data->SubAgent]['Topup'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            if (!isset($write_array[$data->SubAgent]['Productive']))
                $write_array[$data->SubAgent]['Productive'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $month = $data->month;
            if ($month[0] === '0')
                $month = substr($month, 1);
            $write_array[$data->SubAgent]['Topup'][$month - 1] = $data->count;
        }
        foreach ($prod as $data) {
            if (!isset($write_array[$data->SubAgent]['Activation']))
                $write_array[$data->SubAgent]['Activation'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            if (!isset($write_array[$data->SubAgent]['Topup']))
                $write_array[$data->SubAgent]['Topup'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            if (!isset($write_array[$data->SubAgent]['Productive']))
                $write_array[$data->SubAgent]['Productive'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $month = $data->month;
            if ($month[0] === '0')
                $month = substr($month, 1);
            $write_array[$data->SubAgent]['Productive'][$month - 1] = $data->count;
        }
        foreach ($write_array as $key => $data) {
            $myArr = array($key, $data["Activation"][0], $data["Productive"][0], $data["Topup"][0], $data["Activation"][1], $data["Productive"][1], $data["Topup"][1], $data["Activation"][2], $data["Productive"][2], $data["Topup"][2], $data["Activation"][3], $data["Productive"][3], $data["Topup"][3]
                , $data["Activation"][4], $data["Productive"][4], $data["Topup"][4], $data["Activation"][5], $data["Productive"][5], $data["Topup"][5], $data["Activation"][6], $data["Productive"][6], $data["Topup"][6]
                , $data["Activation"][7], $data["Productive"][7], $data["Topup"][7], $data["Activation"][8], $data["Productive"][8], $data["Topup"][8], $data["Activation"][9], $data["Productive"][9], $data["Topup"][9]
                , $data["Activation"][10], $data["Productive"][10], $data["Topup"][10], $data["Activation"][11], $data["Productive"][11], $data["Topup"][11]);
            $writer->addRow($myArr); // add a row at a time
        }
        $writer->close();
        return '/subagent_report.xlsx';
    }

    static function exportExcelUserDashboard() {
        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/user_report_allyears.xlsx";
        $writer->openToFile($filePath);
//
        $myArr = array("All User  Reporting");
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("MSISDN", "Name", "Activation Date", "Activation Store", "Churn Date", "Voc 300 TopUp", "Voc 100 TopUp", "Voc 50 TopUp", "Last Top Up Date", "Service Usage", "Last Service Usage Date");
        $writer->addRow($myArr); // add a row at a time

        $raw_where = '';

        if (Session::has('UserFilterAct')) {
            if (Session::get('UserFilterAct') === '2') {
                $raw_where .= " AND inv1.`ChurnDate` IS NULL";
            } else if (Session::get('UserFilterAct') === '3') {
                $raw_where .= " AND inv1.`ChurnDate` IS NOT NULL";
            }
        }
        if (Session::has('UserFilterv300')) {
            if (Session::get('UserFilterv300') === '2') {
                $raw_where .= " AND (SELECT COUNT(inv2.`SerialNumber`) FROM `m_inventory` as inv2 WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN` AND (inv2.`SerialNumber` LIKE '%KR0250%' OR inv2.`SerialNumber` LIKE '%KR1850%')) > 0";
            } else if (Session::get('UserFilterv300') === '3') {
                $raw_where .= " AND (SELECT COUNT(inv2.`SerialNumber`) FROM `m_inventory` as inv2 WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN` AND (inv2.`SerialNumber` LIKE '%KR0250%' OR inv2.`SerialNumber` LIKE '%KR1850%')) = '0'";
            }
        }
        if (Session::has('UserFilterv100')) {
            if (Session::get('UserFilterv100') === '2') {
                $raw_where .= " AND ((SELECT COUNT(inv2.`SerialNumber`) FROM `m_inventory` as inv2 WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN` AND (inv2.`SerialNumber` LIKE '%KR0150%' OR inv2.`SerialNumber` LIKE '%KR0350%')) > 0 OR (SELECT COUNT(inv2.`SerialNumber`) FROM `m_inventory` as inv2 WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN` AND inv2.`SerialNumber` LIKE '%KR0450%') > 0)";
            } else if (Session::get('UserFilterv100') === '3') {
                $raw_where .= " AND ((SELECT COUNT(inv2.`SerialNumber`) FROM `m_inventory` as inv2 WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN` AND (inv2.`SerialNumber` LIKE '%KR0150%' OR inv2.`SerialNumber` LIKE '%KR0350%')) = '0' OR (SELECT COUNT(inv2.`SerialNumber`) FROM `m_inventory` as inv2 WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN` AND inv2.`SerialNumber` LIKE '%KR0450%') = '0')";
            }
        }
        if (Session::has('UserFilterService')) {
            if (Session::get('UserFilterService') === '2') {
                $raw_where .= " AND (SELECT prod.`Service` FROM `m_productive` as prod  WHERE prod.`MSISDN` = inv1.`MSISDN` ORDER BY CONCAT(prod.`Month`,prod.`Year`) DESC LIMIT 1) != '0'";
            } else if (Session::get('UserFilterService') === '3') {
                $raw_where .= " AND ((SELECT prod.`Service` FROM `m_productive` as prod  WHERE prod.`MSISDN` = inv1.`MSISDN` ORDER BY CONCAT(prod.`Month`,prod.`Year`) DESC LIMIT 1) IS NULL OR (SELECT prod.`Service` FROM `m_productive` as prod  WHERE prod.`MSISDN` = inv1.`MSISDN` ORDER BY CONCAT(prod.`Month`,prod.`Year`) DESC LIMIT 1) = '0')";
            }
        }

        $simtopup = DB::table('m_inventory as inv1')
                        ->whereRaw('inv1.ActivationName IS NOT NULL' . $raw_where)
                        ->select(DB::raw("inv1.`ActivationDate`,inv1.`ActivationName`,inv1.`MSISDN`,inv1.`ChurnDate`,inv1.`ActivationStore`"
                                        . ",(SELECT COUNT(inv2.`SerialNumber`) FROM `m_inventory` as inv2 WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN` AND (inv2.`SerialNumber` LIKE '%KR0250%' OR inv2.`SerialNumber` LIKE '%KR1850%')) as 'Voc300'"
                                        . ",(SELECT COUNT(inv2.`SerialNumber`) FROM `m_inventory` as inv2 WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN` AND (inv2.`SerialNumber` LIKE '%KR0150%' OR inv2.`SerialNumber` LIKE '%KR0350%')) as 'Voc100'"
                                        . ",(SELECT COUNT(inv2.`SerialNumber`) FROM `m_inventory` as inv2 WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN` AND inv2.`SerialNumber` LIKE '%KR0450%') as 'Voc50'"
                                        . ",(SELECT inv2.`TopUpDate` FROM `m_inventory` as inv2  WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN`  ORDER BY inv2.`TopUpDate` DESC LIMIT 1) as 'LastDatePurchasedVoucher' "
                                        . ",(SELECT prod.`Service` FROM `m_productive` as prod  WHERE prod.`MSISDN` = inv1.`MSISDN` ORDER BY CONCAT(prod.`Month`,prod.`Year`) DESC LIMIT 1) as 'ServiceUsed' "
                                        . ",(SELECT CONCAT(prod.`Month`,prod.`Year`) FROM `m_productive` as prod  WHERE prod.`MSISDN` = inv1.`MSISDN` ORDER BY CONCAT(prod.`Month`,prod.`Year`) DESC LIMIT 1) as 'LastDateUsedService'"
                        ))->get();
        foreach ($simtopup as $data) {
            $stats = "no service";
            if ($data->ServiceUsed == '1') {
                $stats = 'Voice only';
            } else if ($data->ServiceUsed == '2') {
                $stats = 'Internet only';
            } else if ($data->ServiceUsed == '3') {
                $stats = 'Voice + Internet';
            } else if ($data->ServiceUsed == '5') {
                $stats = 'SMS only';
            } else if ($data->ServiceUsed == '6') {
                $stats = 'Voice + SMS';
            } else if ($data->ServiceUsed == '7') {
                $stats = 'Internet + SMS';
            } else if ($data->ServiceUsed == '8') {
                $stats = 'All';
            }
            $myArr = array($data->MSISDN, $data->ActivationName, $data->ActivationDate, $data->ActivationStore, $data->ChurnDate, number_format($data->Voc300), number_format($data->Voc100), number_format($data->Voc50), $data->LastDatePurchasedVoucher, $stats, $data->LastDateUsedService);
            $writer->addRow($myArr); // add a row at a time
        }
        $writer->close();
        return '/user_report_allyears.xlsx';
    }

    static function exportExcelShipoutDashboard() {
//        $year = Input::get("argyear");
//        $year = "2017";
        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/shippout_report_allyears.xlsx";
        $writer->openToFile($filePath);

        $myArr = array("All Channel Reporting");
        $writer->addRow($myArr); // add a row at a time

        foreach (DB::table('m_historymovement')->select(DB::raw('YEAR(Date) as year'))->where('Status', 2)->orderBy('year', 'DESC')->distinct()->get() as $year) {
            $year = $year->year;
            $month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            $totalsim = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $totalvoc = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

            $allchan = DB::table('m_historymovement')
                            ->select(DB::raw(" DISTINCT SUBSTRING_INDEX(`SubAgent`, ' ', 1) as 'channel'"))->where('Status', 2)->get();
            $myArr = array("SIM 3G SHIPOUT " . $year);
            $writer->addRow($myArr); // add a row at a time
            $myArr = array("CHANNEL", "JANUARY " . $year, "FEBRUARY " . $year, "MARCH " . $year, "APRIL " . $year, "MAY " . $year, "JUNE " . $year, "JULY " . $year, "AUGUST " . $year, "SEPTEMBER " . $year, "OCTOBER " . $year, "NOVEMBER " . $year, "DECEMBER " . $year);
            $writer->addRow($myArr); // add a row at a time
            foreach ($allchan as $channel) {
                $idx1 = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                if ($channel->channel != '-' || $channel->channel != ' ') {
                    $simshipout = DB::table('m_inventory')
                                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                                    ->whereRaw('m_inventory.Type IN ("1")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $channel->channel . '%')->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
                                    ->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                                    ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

                    for ($i = 0; $i < 12; $i++) {
                        for ($j = 0; $j < 12; $j++) {
                            if (isset($simshipout))
                                if (isset($simshipout[$j]))
                                    if ($simshipout[$j]->month - 1 == $i) {
                                        $idx1[$i] = $simshipout[$j]->counter;
                                        $totalsim[$i] += $simshipout[$j]->counter;
                                    }
                        }
                    }
                    $myArr = array($channel->channel, number_format($idx1[0]), number_format($idx1[1]), number_format($idx1[2]), number_format($idx1[3])
                        , number_format($idx1[4]), number_format($idx1[5]), number_format($idx1[6]), number_format($idx1[7]), number_format($idx1[8])
                        , number_format($idx1[9]), number_format($idx1[10]), number_format($idx1[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $myArr = array("TOTAL", number_format($totalsim[0]), number_format($totalsim[1]), number_format($totalsim[2]), number_format($totalsim[3])
                , number_format($totalsim[4]), number_format($totalsim[5]), number_format($totalsim[6]), number_format($totalsim[7]), number_format($totalsim[8])
                , number_format($totalsim[9]), number_format($totalsim[10]), number_format($totalsim[11]));
//            $myArr = array("TOTAL", $totalsim[0], $totalsim[1], $totalsim[2], $totalsim[3], $totalsim[4], $totalsim[5], $totalsim[6], $totalsim[7], $totalsim[8], $totalsim[9], $totalsim[10], $totalsim[11]);
            $writer->addRow($myArr); // add a row at a time
            $writer->addRow(['']);
            $totalsim = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $myArr = array("SIM 4G SHIPOUT " . $year);
            $writer->addRow($myArr); // add a row at a time
            $myArr = array("CHANNEL", "JANUARY " . $year, "FEBRUARY " . $year, "MARCH " . $year, "APRIL " . $year, "MAY " . $year, "JUNE " . $year, "JULY " . $year, "AUGUST " . $year, "SEPTEMBER " . $year, "OCTOBER " . $year, "NOVEMBER " . $year, "DECEMBER " . $year);
            $writer->addRow($myArr); // add a row at a time
            foreach ($allchan as $channel) {
                $idx1 = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                if ($channel->channel != '-' || $channel->channel != ' ') {
                    $simshipout = DB::table('m_inventory')
                                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                                    ->whereRaw('m_inventory.Type IN ("4")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $channel->channel . '%')->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')
                                    ->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                                    ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

                    for ($i = 0; $i < 12; $i++) {
                        for ($j = 0; $j < 12; $j++) {
                            if (isset($simshipout))
                                if (isset($simshipout[$j]))
                                    if ($simshipout[$j]->month - 1 == $i) {
                                        $idx1[$i] = $simshipout[$j]->counter;
                                        $totalsim[$i] += $simshipout[$j]->counter;
                                    }
                        }
                    }
                    $myArr = array($channel->channel, number_format($idx1[0]), number_format($idx1[1]), number_format($idx1[2]), number_format($idx1[3])
                        , number_format($idx1[4]), number_format($idx1[5]), number_format($idx1[6]), number_format($idx1[7]), number_format($idx1[8])
                        , number_format($idx1[9]), number_format($idx1[10]), number_format($idx1[11]));
//                    $myArr = array($channel->channel, $idx1[0], $idx1[1], $idx1[2], $idx1[3], $idx1[4], $idx1[5], $idx1[6], $idx1[7], $idx1[8], $idx1[9], $idx1[10], $idx1[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $myArr = array("TOTAL", number_format($totalsim[0]), number_format($totalsim[1]), number_format($totalsim[2]), number_format($totalsim[3])
                , number_format($totalsim[4]), number_format($totalsim[5]), number_format($totalsim[6]), number_format($totalsim[7]), number_format($totalsim[8])
                , number_format($totalsim[9]), number_format($totalsim[10]), number_format($totalsim[11]));
            $writer->addRow($myArr); // add a row at a time
            $writer->addRow(['']);
            $myArr = array("eVC 300 SHIPOUT " . $year);
            $writer->addRow($myArr); // add a row at a time
            $myArr = array("CHANNEL", "JANUARY " . $year, "FEBRUARY " . $year, "MARCH " . $year, "APRIL " . $year, "MAY " . $year, "JUNE " . $year, "JULY " . $year, "AUGUST " . $year, "SEPTEMBER " . $year, "OCTOBER " . $year, "NOVEMBER " . $year, "DECEMBER " . $year);
            $writer->addRow($myArr); // add a row at a time


            foreach ($allchan as $channel) {
                $idx2 = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                if ($channel != '-' || $channel != ' ') {
                    $vocshipout = DB::table('m_inventory')
                                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                                    ->whereRaw('m_inventory.Type IN ("2","3")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                                    ->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')->where('m_inventory.SerialNumber', 'LIKE', "%KR0250%")
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $channel->channel . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                                    ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

                    for ($i = 0; $i < 12; $i++) {
                        for ($j = 0; $j < 12; $j++) {
                            if (isset($vocshipout))
                                if (isset($vocshipout[$j]))
                                    if ($vocshipout[$j]->month - 1 == $i) {
                                        $idx2[$i] = $vocshipout[$j]->counter;
                                        $totalvoc[$i] += $vocshipout[$j]->counter;
                                    }
                        }
                    }
                    $myArr = array($channel->channel, number_format($idx2[0]), number_format($idx2[1]), number_format($idx2[2]), number_format($idx2[3])
                        , number_format($idx2[4]), number_format($idx2[5]), number_format($idx2[6]), number_format($idx2[7]), number_format($idx2[8])
                        , number_format($idx2[9]), number_format($idx2[10]), number_format($idx2[11]));
//                    $myArr = array($channel->channel, $idx2[0], $idx2[1], $idx2[2], $idx2[3], $idx2[4], $idx2[5], $idx2[6], $idx2[7], $idx2[8], $idx2[9], $idx2[10], $idx2[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $myArr = array("TOTAL", number_format($totalvoc[0]), number_format($totalvoc[1]), number_format($totalvoc[2])
                , number_format($totalvoc[3]), number_format($totalvoc[4]), number_format($totalvoc[5]), number_format($totalvoc[6])
                , number_format($totalvoc[7]), number_format($totalvoc[8]), number_format($totalvoc[9]), number_format($totalvoc[10]), number_format($totalvoc[11]));
            //$myArr = array("TOTAL", $totalvoc[0], $totalvoc[1], $totalvoc[2], $totalvoc[3], $totalvoc[4], $totalvoc[5], $totalvoc[6], $totalvoc[7], $totalvoc[8], $totalvoc[9], $totalvoc[10], $totalvoc[11]);
            $writer->addRow($myArr); // add a row at a time
            $writer->addRow(['']);
            $totalvoc = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $myArr = array("eVC 100 SHIPOUT " . $year);
            $writer->addRow($myArr); // add a row at a time
            $myArr = array("CHANNEL", "JANUARY " . $year, "FEBRUARY " . $year, "MARCH " . $year, "APRIL " . $year, "MAY " . $year, "JUNE " . $year, "JULY " . $year, "AUGUST " . $year, "SEPTEMBER " . $year, "OCTOBER " . $year, "NOVEMBER " . $year, "DECEMBER " . $year);
            $writer->addRow($myArr); // add a row at a time


            foreach ($allchan as $channel) {
                $idx2 = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                if ($channel != '-' || $channel != ' ') {
                    $vocshipout = DB::table('m_inventory')
                                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                                    ->whereRaw('m_inventory.Type IN ("2","3")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                                    ->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')->where('m_inventory.SerialNumber', "LIKE", "%KR0150%")
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $channel->channel . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                                    ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

                    for ($i = 0; $i < 12; $i++) {
                        for ($j = 0; $j < 12; $j++) {
                            if (isset($vocshipout))
                                if (isset($vocshipout[$j]))
                                    if ($vocshipout[$j]->month - 1 == $i) {
                                        $idx2[$i] = $vocshipout[$j]->counter;
                                        $totalvoc[$i] += $vocshipout[$j]->counter;
                                    }
                        }
                    }
                    $myArr = array($channel->channel, number_format($idx2[0]), number_format($idx2[1]), number_format($idx2[2]), number_format($idx2[3])
                        , number_format($idx2[4]), number_format($idx2[5]), number_format($idx2[6]), number_format($idx2[7]), number_format($idx2[8])
                        , number_format($idx2[9]), number_format($idx2[10]), number_format($idx2[11]));
//                    $myArr = array($channel->channel, $idx2[0], $idx2[1], $idx2[2], $idx2[3], $idx2[4], $idx2[5], $idx2[6], $idx2[7], $idx2[8], $idx2[9], $idx2[10], $idx2[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $myArr = array("TOTAL", number_format($totalvoc[0]), number_format($totalvoc[1]), number_format($totalvoc[2])
                , number_format($totalvoc[3]), number_format($totalvoc[4]), number_format($totalvoc[5]), number_format($totalvoc[6])
                , number_format($totalvoc[7]), number_format($totalvoc[8]), number_format($totalvoc[9]), number_format($totalvoc[10]), number_format($totalvoc[11]));
            //$myArr = array("TOTAL", $totalvoc[0], $totalvoc[1], $totalvoc[2], $totalvoc[3], $totalvoc[4], $totalvoc[5], $totalvoc[6], $totalvoc[7], $totalvoc[8], $totalvoc[9], $totalvoc[10], $totalvoc[11]);
            $writer->addRow($myArr); // add a row at a time
            $writer->addRow(['']);
            $totalvoc = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $myArr = array("eVC 50 SHIPOUT " . $year);
            $writer->addRow($myArr); // add a row at a time
            $myArr = array("CHANNEL", "JANUARY " . $year, "FEBRUARY " . $year, "MARCH " . $year, "APRIL " . $year, "MAY " . $year, "JUNE " . $year, "JULY " . $year, "AUGUST " . $year, "SEPTEMBER " . $year, "OCTOBER " . $year, "NOVEMBER " . $year, "DECEMBER " . $year);
            $writer->addRow($myArr); // add a row at a time


            foreach ($allchan as $channel) {
                $idx2 = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                if ($channel != '-' || $channel != ' ') {
                    $vocshipout = DB::table('m_inventory')
                                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                                    ->whereRaw('m_inventory.Type IN ("2","3")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                                    ->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')->where('m_inventory.SerialNumber', "LIKE", "%KR0450%")
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $channel->channel . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                                    ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

                    for ($i = 0; $i < 12; $i++) {
                        for ($j = 0; $j < 12; $j++) {
                            if (isset($vocshipout))
                                if (isset($vocshipout[$j]))
                                    if ($vocshipout[$j]->month - 1 == $i) {
                                        $idx2[$i] = $vocshipout[$j]->counter;
                                        $totalvoc[$i] += $vocshipout[$j]->counter;
                                    }
                        }
                    }
                    $myArr = array($channel->channel, number_format($idx2[0]), number_format($idx2[1]), number_format($idx2[2]), number_format($idx2[3])
                        , number_format($idx2[4]), number_format($idx2[5]), number_format($idx2[6]), number_format($idx2[7]), number_format($idx2[8])
                        , number_format($idx2[9]), number_format($idx2[10]), number_format($idx2[11]));
//                    $myArr = array($channel->channel, $idx2[0], $idx2[1], $idx2[2], $idx2[3], $idx2[4], $idx2[5], $idx2[6], $idx2[7], $idx2[8], $idx2[9], $idx2[10], $idx2[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $myArr = array("TOTAL", number_format($totalvoc[0]), number_format($totalvoc[1]), number_format($totalvoc[2])
                , number_format($totalvoc[3]), number_format($totalvoc[4]), number_format($totalvoc[5]), number_format($totalvoc[6])
                , number_format($totalvoc[7]), number_format($totalvoc[8]), number_format($totalvoc[9]), number_format($totalvoc[10]), number_format($totalvoc[11]));
            //$myArr = array("TOTAL", $totalvoc[0], $totalvoc[1], $totalvoc[2], $totalvoc[3], $totalvoc[4], $totalvoc[5], $totalvoc[6], $totalvoc[7], $totalvoc[8], $totalvoc[9], $totalvoc[10], $totalvoc[11]);
            $writer->addRow($myArr); // add a row at a time
            $writer->addRow(['']);
            $totalvoc = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $myArr = array("phVC 100 SHIPOUT " . $year);
            $writer->addRow($myArr); // add a row at a time
            $myArr = array("CHANNEL", "JANUARY " . $year, "FEBRUARY " . $year, "MARCH " . $year, "APRIL " . $year, "MAY " . $year, "JUNE " . $year, "JULY " . $year, "AUGUST " . $year, "SEPTEMBER " . $year, "OCTOBER " . $year, "NOVEMBER " . $year, "DECEMBER " . $year);
            $writer->addRow($myArr); // add a row at a time


            foreach ($allchan as $channel) {
                $idx2 = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                if ($channel != '-' || $channel != ' ') {
                    $vocshipout = DB::table('m_inventory')
                                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                                    ->whereRaw('m_inventory.Type IN ("2","3")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                                    ->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')->where('m_inventory.SerialNumber', "LIKE", "%KR0350%")
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $channel->channel . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                                    ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

                    for ($i = 0; $i < 12; $i++) {
                        for ($j = 0; $j < 12; $j++) {
                            if (isset($vocshipout))
                                if (isset($vocshipout[$j]))
                                    if ($vocshipout[$j]->month - 1 == $i) {
                                        $idx2[$i] = $vocshipout[$j]->counter;
                                        $totalvoc[$i] += $vocshipout[$j]->counter;
                                    }
                        }
                    }
                    $myArr = array($channel->channel, number_format($idx2[0]), number_format($idx2[1]), number_format($idx2[2]), number_format($idx2[3])
                        , number_format($idx2[4]), number_format($idx2[5]), number_format($idx2[6]), number_format($idx2[7]), number_format($idx2[8])
                        , number_format($idx2[9]), number_format($idx2[10]), number_format($idx2[11]));
//                    $myArr = array($channel->channel, $idx2[0], $idx2[1], $idx2[2], $idx2[3], $idx2[4], $idx2[5], $idx2[6], $idx2[7], $idx2[8], $idx2[9], $idx2[10], $idx2[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $myArr = array("TOTAL", $totalvoc[0], $totalvoc[1], $totalvoc[2], $totalvoc[3], $totalvoc[4], $totalvoc[5], $totalvoc[6], $totalvoc[7], $totalvoc[8], $totalvoc[9], $totalvoc[10], $totalvoc[11]);
            $writer->addRow($myArr); // add a row at a time
            $writer->addRow(['']);
            $totalvoc = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $myArr = array("phVC 300 SHIPOUT " . $year);
            $writer->addRow($myArr); // add a row at a time
            $myArr = array("CHANNEL", "JANUARY " . $year, "FEBRUARY " . $year, "MARCH " . $year, "APRIL " . $year, "MAY " . $year, "JUNE " . $year, "JULY " . $year, "AUGUST " . $year, "SEPTEMBER " . $year, "OCTOBER " . $year, "NOVEMBER " . $year, "DECEMBER " . $year);
            $writer->addRow($myArr); // add a row at a time


            foreach ($allchan as $channel) {
                $idx2 = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                if ($channel != '-' || $channel != ' ') {
                    $vocshipout = DB::table('m_inventory')
                                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                                    ->whereRaw('m_inventory.Type IN ("2","3")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                                    ->where('m_historymovement.Status', '2')->where('m_historymovement.Deleted', '0')->where('m_inventory.SerialNumber', "LIKE", "%KR1850%")
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $channel->channel . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
                                    ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month'))->get();

                    for ($i = 0; $i < 12; $i++) {
                        for ($j = 0; $j < 12; $j++) {
                            if (isset($vocshipout))
                                if (isset($vocshipout[$j]))
                                    if ($vocshipout[$j]->month - 1 == $i) {
                                        $idx2[$i] = $vocshipout[$j]->counter;
                                        $totalvoc[$i] += $vocshipout[$j]->counter;
                                    }
                        }
                    }
                    $myArr = array($channel->channel, number_format($idx2[0]), number_format($idx2[1]), number_format($idx2[2]), number_format($idx2[3])
                        , number_format($idx2[4]), number_format($idx2[5]), number_format($idx2[6]), number_format($idx2[7]), number_format($idx2[8])
                        , number_format($idx2[9]), number_format($idx2[10]), number_format($idx2[11]));
//                    $myArr = array($channel->channel, $idx2[0], $idx2[1], $idx2[2], $idx2[3], $idx2[4], $idx2[5], $idx2[6], $idx2[7], $idx2[8], $idx2[9], $idx2[10], $idx2[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $myArr = array("TOTAL", number_format($totalvoc[0]), number_format($totalvoc[1]), number_format($totalvoc[2])
                , number_format($totalvoc[3]), number_format($totalvoc[4]), number_format($totalvoc[5]), number_format($totalvoc[6])
                , number_format($totalvoc[7]), number_format($totalvoc[8]), number_format($totalvoc[9]), number_format($totalvoc[10]), number_format($totalvoc[11]));
            //$myArr = array("TOTAL", $totalvoc[0], $totalvoc[1], $totalvoc[2], $totalvoc[3], $totalvoc[4], $totalvoc[5], $totalvoc[6], $totalvoc[7], $totalvoc[8], $totalvoc[9], $totalvoc[10], $totalvoc[11]);
            $writer->addRow($myArr); // add a row at a time
            $writer->addRow(['']);
        }
        $writer->close();
        return "/shippout_report_allyears.xlsx";
    }

    static function exportExcelShipinDashboard() {
//        $year = Input::get("argyear");
//        $year = "2017";
        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/shipin_report_allyears.xlsx";
        $writer->openToFile($filePath);

        $myArr = array("Shipin Reporting");
        $writer->addRow($myArr); // add a row at a time

        foreach (DB::table('m_historymovement')->select(DB::raw('YEAR(Date) as year'))->where('Status', 2)->orderBy('year', 'DESC')->distinct()->get() as $year) {
            $year = $year->year;
            $myArr = array("SHIPIN " . $year);
            $writer->addRow($myArr); // add a row at a time
            $myArr = array("TYPE", "JANUARY " . $year, "FEBRUARY " . $year, "MARCH " . $year, "APRIL " . $year, "MAY " . $year, "JUNE " . $year, "JULY " . $year, "AUGUST " . $year, "SEPTEMBER " . $year, "OCTOBER " . $year, "NOVEMBER " . $year, "DECEMBER " . $year, "TOTAL");
            $writer->addRow($myArr); // add a row at a time
            $simshipout = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                            ->whereRaw('m_inventory.Type IN (1,4)')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                            ->where('m_historymovement.Status', '0')->where('m_historymovement.Deleted', '0')
                            ->groupBy(DB::raw('MONTH(m_historymovement.Date), m_inventory.Type'))
                            ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month, m_inventory.Type as type'))->get();
            $data = [];
            $totalvoc = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach ($simshipout as $datas) {
                $key = $datas->type;
                $header = "";
                if ($key == '1')
                    $header = 'SIM 3G';
                else if ($key == '4')
                    $header = 'SIM 4G';
                else if (strtoupper($key) == 'KR0250')
                    $header = 'eVC 300';
                else if (strtoupper($key) == 'KR0150')
                    $header = 'eVC 100';
                else if (strtoupper($key) == 'KR0450')
                    $header = 'eVC 50';
                else if (strtoupper($key) == 'KR0350')
                    $header = 'phVC 100';
                else if (strtoupper($key) == 'KR1850')
                    $header = 'phVC 300';
                if (!isset($data[$header])) {
                    $data[$header] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                }
                $data[$header][$datas->month - 1] = $datas->counter;
            }
            foreach ($data as $key => $val) {
                for ($i = 0; $i < 12; $i++) {
                    $totalvoc[$i] += $val[$i];
                }
//                $myArr = array($key, $val[0], $val[1], $val[2], $val[3], $val[4], $val[5], $val[6], $val[7], $val[8], $val[9], $val[10], $val[11], array_sum($val));
                $myArr = array($key, number_format($val[0]), number_format($val[1]), number_format($val[2]), number_format($val[3]), number_format($val[4]),
                    number_format($val[5]), number_format($val[6]), number_format($val[7]), number_format($val[8]), number_format($val[9]), number_format($val[10]),
                    number_format($val[11]), number_format(array_sum($val)));
                $writer->addRow($myArr); // add a row at a time
            }

            $simshipout = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                            ->whereRaw('m_inventory.Type IN (2,3)')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                            ->where('m_historymovement.Status', '0')->where('m_historymovement.Deleted', '0')
                            ->groupBy(DB::raw('MONTH(m_historymovement.Date), SUBSTRING(m_inventory.SerialNumber, 1, 6)'))
                            ->select(DB::raw('count(m_inventory.SerialNumber) as counter, MONTH(m_historymovement.Date) as month , SUBSTRING(m_inventory.SerialNumber, 1, 6) as type'))->get();
            $data = [];
            foreach ($simshipout as $datas) {
                $key = $datas->type;
                $header = "";
                if ($key == '1')
                    $header = 'SIM 3G';
                else if ($key == '4')
                    $header = 'SIM 4G';
                else if (strtoupper($key) == 'KR0250')
                    $header = 'eVC 300';
                else if (strtoupper($key) == 'KR0150')
                    $header = 'eVC 100';
                else if (strtoupper($key) == 'KR0450')
                    $header = 'eVC 50';
                else if (strtoupper($key) == 'KR0350')
                    $header = 'phVC 100';
                else if (strtoupper($key) == 'KR1850')
                    $header = 'phVC 300';
                if (!isset($data[$header])) {
                    $data[$header] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                }
                $data[$header][$datas->month - 1] = $datas->counter;
            }
            foreach ($data as $key => $val) {
                for ($i = 0; $i < 12; $i++) {
                    $totalvoc[$i] += $val[$i];
                }
//                $myArr = array($key, $val[0], $val[1], $val[2], $val[3], $val[4], $val[5], $val[6], $val[7], $val[8], $val[9], $val[10], $val[11], array_sum($val));
                $myArr = array($key, number_format($val[0]), number_format($val[1]), number_format($val[2]), number_format($val[3]), number_format($val[4]),
                    number_format($val[5]), number_format($val[6]), number_format($val[7]), number_format($val[8]), number_format($val[9]), number_format($val[10]),
                    number_format($val[11]), number_format(array_sum($val)));
                $writer->addRow($myArr); // add a row at a time
            }
            $myArr = array("TOTAL", number_format($totalvoc[0]), number_format($totalvoc[1]), number_format($totalvoc[2])
                , number_format($totalvoc[3]), number_format($totalvoc[4]), number_format($totalvoc[5]), number_format($totalvoc[6])
                , number_format($totalvoc[7]), number_format($totalvoc[8]), number_format($totalvoc[9]), number_format($totalvoc[10]), number_format($totalvoc[11]));
//            $myArr = array("TOTAL", $totalvoc[0], $totalvoc[1], $totalvoc[2], $totalvoc[3], $totalvoc[4], $totalvoc[5], $totalvoc[6], $totalvoc[7], $totalvoc[8], $totalvoc[9], $totalvoc[10], $totalvoc[11]);
            $writer->addRow($myArr); // add a row at a time
            $writer->addRow(['']);
        }
        $writer->close();
        return "/shipin_report_allyears.xlsx";
    }

    static function exportExcelUsageDashboard() {
//        $year = Input::get("argyear");
//        $year = "2017";
        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/usage_report_allyears.xlsx";
        $writer->openToFile($filePath);

        $myArr = array("Usage Reporting");
        $writer->addRow($myArr); // add a row at a time

        foreach (DB::table('m_historymovement')->select(DB::raw('YEAR(Date) as year'))->where('Status', 2)->orderBy('year', 'DESC')->distinct()->get() as $year) {
            $year = $year->year;
            $myArr = array("USAGE " . $year);
            $writer->addRow($myArr); // add a row at a time
            $myArr = array("TYPE", "JANUARY " . $year, "FEBRUARY " . $year, "MARCH " . $year, "APRIL " . $year, "MAY " . $year, "JUNE " . $year, "JULY " . $year, "AUGUST " . $year, "SEPTEMBER " . $year, "OCTOBER " . $year, "NOVEMBER " . $year, "DECEMBER " . $year, "TOTAL");
            $writer->addRow($myArr); // add a row at a time
            $totalvoc = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $simshipout = DB::table('m_inventory')
                            ->whereRaw('Type IN (1,4)')->whereRaw('YEAR(ActivationDate) = ' . $year)
                            ->groupBy(DB::raw('MONTH(ActivationDate), Type'))
                            ->select(DB::raw('count(SerialNumber) as counter, MONTH(ActivationDate) as month , Type'))->get();
            $data = [];
            foreach ($simshipout as $datas) {
                $key = $datas->Type;
                $header = "";
                if ($key == '1')
                    $header = 'SIM 3G';
                else if ($key == '4')
                    $header = 'SIM 4G';
                else if (strtoupper($key) == 'KR0250')
                    $header = 'eVC 300';
                else if (strtoupper($key) == 'KR0150')
                    $header = 'eVC 100';
                else if (strtoupper($key) == 'KR0450')
                    $header = 'eVC 50';
                else if (strtoupper($key) == 'KR0350')
                    $header = 'phVC 100';
                else if (strtoupper($key) == 'KR1850')
                    $header = 'phVC 300';
                if (!isset($data[$header])) {
                    $data[$header] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                }
                $data[$header][$datas->month - 1] = $datas->counter;
            }
            foreach ($data as $key => $val) {
                for ($i = 0; $i < 12; $i++) {
                    $totalvoc[$i] += $val[$i];
                }
                $myArr = array($key, number_format($val[0]), number_format($val[1]), number_format($val[2]), number_format($val[3]), number_format($val[4]),
                    number_format($val[5]), number_format($val[6]), number_format($val[7]), number_format($val[8]), number_format($val[9]), number_format($val[10]),
                    number_format($val[11]), number_format(array_sum($val)));
                $writer->addRow($myArr); // add a row at a time
            }
            $simshipout = DB::table('m_inventory')
                            ->whereRaw('Type IN (2,3)')->whereRaw('YEAR(TopUpDate) = ' . $year)
                            ->groupBy(DB::raw('MONTH(TopUpDate), SUBSTRING(SerialNumber, 1, 6)'))
                            ->select(DB::raw('count(SerialNumber) as counter, MONTH(TopUpDate) as month , SUBSTRING(SerialNumber, 1, 6) as type'))->get();
            $data = [];
            foreach ($simshipout as $datas) {
                $key = $datas->type;
                $header = "";
                if ($key == '1')
                    $header = 'SIM 3G';
                else if ($key == '4')
                    $header = 'SIM 4G';
                else if (strtoupper($key) == 'KR0250')
                    $header = 'eVC 300';
                else if (strtoupper($key) == 'KR0150')
                    $header = 'eVC 100';
                else if (strtoupper($key) == 'KR0450')
                    $header = 'eVC 50';
                else if (strtoupper($key) == 'KR0350')
                    $header = 'phVC 100';
                else if (strtoupper($key) == 'KR1850')
                    $header = 'phVC 300';
                if (!isset($data[$header])) {
                    $data[$header] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                }
                $data[$header][$datas->month - 1] = $datas->counter;
            }
            foreach ($data as $key => $val) {
                for ($i = 0; $i < 12; $i++) {
                    $totalvoc[$i] += $val[$i];
                }
                $myArr = array($key, number_format($val[0]), number_format($val[1]), number_format($val[2]), number_format($val[3]), number_format($val[4]), number_format($val[5]), number_format($val[6]), number_format($val[7]), number_format($val[8]), number_format($val[9]), number_format($val[10]), number_format($val[11]), number_format(array_sum($val)));
                ;
                $writer->addRow($myArr); // add a row at a time
            }
            $myArr = array("TOTAL", number_format($totalvoc[0]), number_format($totalvoc[1]), number_format($totalvoc[2])
                , number_format($totalvoc[3]), number_format($totalvoc[4]), number_format($totalvoc[5]), number_format($totalvoc[6])
                , number_format($totalvoc[7]), number_format($totalvoc[8]), number_format($totalvoc[9]), number_format($totalvoc[10]), number_format($totalvoc[11]));
            $writer->addRow($myArr); // add a row at a time
            $writer->addRow(['']);
        }
        $writer->close();
        return "/usage_report_allyears.xlsx";
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
        $st = '';
        if (Session::has('WarehouseInv'))
            $wh = Session::get('WarehouseInv');
        if (Session::has('ShipouttoInv'))
            $st = Session::get('ShipouttoInv');
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
            if ($st != '') {
                $invs->where('m_historymovement.SubAgent', 'LIKE', '%' . $st . '%');
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
            if ($st != '') {
                $invs->where('m_historymovement.SubAgent', 'LIKE', '%' . $st . '%');
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
            $fabiao = Input::get('fabiao');
            $remark = Input::get('remark');

            Session::put('sn', $sn);
            Session::put('date', $date);
            Session::put('subagent', $subagent);
            Session::put('to', $to);
            Session::put('fabiao', $fabiao);
            Session::put('conses', Input::get('cs'));
            Session::put('remark', $remark);
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
        $font_color = '';
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
                            ->distinct()->first();
            if ($wh) {
                $wh = $wh->LastWarehouse;
            }
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
            $temp_string = '銷貨單';
        }
        if (Session::get('conses') == 1) {
            $temp_string = '借貨單';
            $font_color = 'color:red;';
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
                        <p style="font-size:120%;' . $font_color . '">' . $temp_string . '</p>
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
                        <div style="width:70px;padding-left:3px height:20px;float:left; display: inline-block; ">發票號碼 :</div>
                        <div style="width:430px; height:20px;float:left; display: inline-block;">' . Session::get('fabiao') . '</div>
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
                        $html .= '<div style="width:102%; height:15px; padding-top:-3px; border-left: 1px solid;  border-right: 1px solid;">';
                        $html .= '<div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>';
                        $html .= '<div style="width:302px; height:15px;float:left; display: inline-block; border-right: 1px solid; padding-left: 4px;font-size:9px">';
                        if ($temp_cot == count($starts) - 1)
                            if ($temp1 === $ends[$temp_cot])
                                $html .= $temp1;
                            else
                                $html .= $temp1 . ' - ' . $ends[$temp_cot];
                        else {
                            if ($temp1 === $ends[$temp_cot])
                                $html .= $temp1;
                            else
                                $html .= $temp1 . ' - ' . $ends[$temp_cot] . ', ';
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
                    $html .= '<div style="width:102%; height:15px; padding-top:-2px; border-left: 1px solid;  border-right: 1px solid; ">';
                    $html .= '<div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:300px; height:15px;float:left; display: inline-block; border-right: 1px solid; font-size:9px">';
                    if ($starts === $ends)
                        $html .= $starts;
                    else
                        $html .= $starts . ' - ' . $ends;
                    $html .= '</div>
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;"></div>';
                    $html .= '</div>';
                }
            }
        }
        for ($i = 0; $i < (7 - count($all_start)); $i++) {
            $html .= '<div style="width:102%; height:15px; border-left: 1px solid;  border-right: 1px solid;">
                        <div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:300px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;"></div>
                    </div>';
        }
        $html .= '<div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; border-top:1px solid;">
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
                        <div style="width:200px; height:60px;float:left; display: inline-block; border-right: 1px solid;">' . Session::get('remark') . '</div>
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
                            ->distinct()->first();
            if ($wh) {
                $wh = $wh->LastWarehouse;
            }
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
                        <div style="width:200px; height:20px;float:left; display: inline-block;">統一編號: </div>
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
                $subtotal += round(((Session::get('price') / 1.05) * $count[$i]), 0);
                $html .= '<div style="width:102%; height:15px; border-left: 1px solid;  border-right: 1px solid;">
                        <div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:300px; height:15px;float:left; display: inline-block; border-right: 1px solid;">' . $type[$i] . '</div>
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;">' . $count[$i] . '</div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;">NT$ ' . round((Session::get('price') / 1.05), 4) . '</div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;">NT$ ' . round(((Session::get('price') / 1.05) * $count[$i]), 0) . '</div>
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
        for ($i = 0; $i < (7 - count($type)); $i++) {
            $html .= '<div style="width:102%; height:15px; border-left: 1px solid;  border-right: 1px solid;">
                        <div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:300px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;"></div>
                    </div>';
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
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ ' . round($subtotal * 0.05, 0) . '</div>
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

    static function getPDFInv() {
        $type = ['', '', '', ''];
        $count = ['', '', '', ''];
        $first = ['', '', '', ''];
        $last = ['', '', '', ''];
        $temp_count = 0;
        $subtotal = 0;
        $date_item = '';
        $shipout_item = '';
        $alltype = '';
        $wh = '';
        $fabiao = '';
        $title = '銷貨單';
        $color = '';
        $inv_item = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                        ->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('FormSeriesInv') . '%')->first();
        if ($inv_item) {
            $date_item = $inv_item->Date;
            $shipout_item = $inv_item->SubAgent;
            $wh = $inv_item->Warehouse;
            $fabiao = $inv_item->FabiaoNumber;

            switch ($inv_item->Status) {
                case 4:
                    $color = 'color:red;';
                    $title = '借貨單';
                    break;
                case 1:
                    $color = 'color:red;';
                    $title = '還貨單';
                    break;
            }
        }
        if (Session::has('FormSeriesInv')) {
            $alltype = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                            ->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('FormSeriesInv') . '%')
                            ->select(DB::raw(' m_historymovement.Price, m_inventory.Type, COUNT(m_inventory.SerialNumber) AS "Qty"'))
                            ->groupBy('m_historymovement.Price', 'm_inventory.Type')->get();
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
                        <p style="font-size:120%;' . $color . '">' . $title . '</p>
                    </div>
                    <div style="width:101.6%; padding-left:3px;height:20px; border-left: 1px solid; border-top: 1px solid; border-right: 1px solid;">
                        訂單日期：' . $date_item . '
                    </div>
                    <div style="width:101.6%; padding-left:3px;height:20px; border-left: 1px solid;  border-right: 1px solid;">
                        訂單編號：' . Session::get('FormSeriesInv') . '
                    </div>
                    <div style="width:101.6%; padding-left:3px;height:20px;border-left: 1px solid; border-bottom: 1px solid; border-right: 1px solid;">
                        客戶編號：' . explode(' ', $shipout_item)[0] . '
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid; border-top: 1px solid; border-right: 1px solid;">
                        <div style="width:70px;padding-left:3px height:20px;float:left; display: inline-block;">客戶名稱 ：</div>
                        <div style="width:430px; height:20px;float:left; display: inline-block;">' . $shipout_item . '</div>
                        <div style="width:200px; height:20px;float:left; display: inline-block;">統一編號: </div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid;">
                        <div style="width:70px;padding-left:3px height:20px;float:left; display: inline-block; ">送貨地址 ：</div>
                        <div style="width:430px; height:20px;float:left; display: inline-block;"></div>
                        <div style="width:200px; height:20px;float:left; display: inline-block;"></div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:70px;padding-left:3px height:20px;float:left; display: inline-block; ">發票號碼 :</div>
                        <div style="width:430px; height:20px;float:left; display: inline-block;">' . $fabiao . '</div>
                        <div style="width:200px; height:20px;float:left; display: inline-block;">倉 庫 別:' . $wh . ' (紅白電訊)</div>
                    </div>
                    <div style="width:102%; text-align:center;height:20px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:100px; height:20px;float:left; display: inline-block; border-right: 1px solid;">產品編號</div>
                        <div style="width:300px; height:20px;float:left; display: inline-block; border-right: 1px solid;">產品名稱</div>
                        <div style="width:70px; height:20px;float:left; display: inline-block; border-right: 1px solid;">數 量</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">訂價/單價</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">合計</div>
                    </div>';
        foreach ($alltype as $per_inv) {
            if ($per_inv != '') {
                $subtotal += round((($per_inv->Price / 1.05) * $per_inv->Qty), 0);
                $tipe = '';
                switch ($per_inv->Type) {
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
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;">' . $per_inv->Qty . '</div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;">NT$ ' . round(($per_inv->Price / 1.05), 4) . '</div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;">NT$ ' . round((($per_inv->Price / 1.05) * $per_inv->Qty), 0) . '</div>
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

            if ($per_inv != '') {
                $starts = DB::table('m_inventory')
                                ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                                ->where('m_historymovement.Price', $per_inv->Price)->where('m_inventory.Type', $per_inv->Type)
                                ->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('FormSeriesInv') . '%')
                                ->orderBy('m_inventory.SerialNumber', 'ASC')->first();
                $ends = DB::table('m_inventory')
                                ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                                ->where('m_historymovement.Price', $per_inv->Price)->where('m_inventory.Type', $per_inv->Type)
                                ->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('FormSeriesInv') . '%')
                                ->orderBy('m_inventory.SerialNumber', 'DESC')->first();

                $html .= '<div style="width:102%; height:15px; padding-top:-3px; border-left: 1px solid;  border-right: 1px solid;">';
                $html .= '<div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>';
                $html .= '<div style="width:302px; height:15px;float:left; display: inline-block; border-right: 1px solid; padding-left: 4px;font-size:9px">';
                if ($starts->SerialNumber === $ends->SerialNumber)
                    $html .= $starts->SerialNumber;
                else
                    $html .= $starts->SerialNumber . ' - ' . $ends->SerialNumber;
                $html .= '</div>';
                $html .= '
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;"></div>';
                $html .= '</div>';
            }
        }
        $html .= '<div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; border-top: 1px solid; ">
                        <div style="width:100px; text-align:center; height:20px;float:left; display: inline-block; border-right: 1px solid;">備</div>
                        <div style="width:377px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">總額</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ ' . $subtotal . '</div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; ">
                        <div style="width:100px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:377px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">營業稅</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ ' . round($subtotal * 0.05, 0) . '</div>
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

    static function getPDFReturn() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sn = Input::get('sn');
            $date = Input::get('date');
            $arr = Input::get('array_SN');

            Session::put('sn_ret', $sn);
            Session::put('date_ret', $date);
            Session::put('arr_ret', $arr);

            return $arr;
        }
        $type = ['', '', '', ''];
        $count = ['', '', '', ''];
        $first = ['', '', '', ''];
        $last = ['', '', '', ''];
        $temp_count = 0;
        $subtotal = 0;
        if (Session::has('arr_ret')) {
            $arr_sn = Session::get('arr_ret');
            $arr_sn = explode(',', $arr_sn);
//            $arr_sn = str_replace(',', "','", $arr_sn);
            $alltype = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '2')
                            ->where('m_inventory.Missing', '0')->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                            ->select('m_inventory.Type')
                            ->distinct()->get();
            $wh = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '2')
                            ->where('m_inventory.Missing', '0')->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                            ->select('m_inventory.LastWarehouse')
                            ->distinct()->first();
            if ($wh) {
                $wh = $wh->LastWarehouse;
            }
        }
        if ($alltype != null) {
            foreach ($alltype as $types) {
                if ($types->Type == '1') {
                    $type[$temp_count] = 'SIM 3G';
                    $counters = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '2')
                            ->where('m_inventory.Missing', '0')
                            ->where('m_inventory.Type', '1')->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '1')
                            ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'asc')
                            ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '1')
                            ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'desc')
                            ->first();
                    $last[$temp_count] = $lastid->SerialNumber;
                    $temp_count++;
                } else if ($types->Type == '2') {
                    $type[$temp_count] = 'E-VOUCHER';
                    $counters = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '2')
                            ->where('m_inventory.Missing', '0')
                            ->where('m_inventory.Type', '2')
                            ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '2')
                            ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'asc')
                            ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '2')
                            ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'desc')
                            ->first();
                    $last[$temp_count] = $lastid->SerialNumber;
                    $temp_count++;
                } else if ($types->Type == '3') {
                    $type[$temp_count] = 'PH-VOUCHER';
                    $counters = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '2')
                            ->where('m_inventory.Missing', '0')
                            ->where('m_inventory.Type', '3')
                            ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '3')
                            ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'asc')
                            ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '3')
                            ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'desc')
                            ->first();
                    $last[$temp_count] = $lastid->SerialNumber;
                    $temp_count++;
                } else if ($types->Type == '4') {
                    $type[$temp_count] = 'SIM 4G';
                    $counters = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '2')
                            ->where('m_inventory.Missing', '0')
                            ->where('m_inventory.Type', '4')
                            ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '4')
                            ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                            ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                            ->orderBy('m_inventory.SerialNumber', 'asc')
                            ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_historymovement.Status', '2')
                            ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '4')
                            ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
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
                        <p style="font-size:120%; color:red;">還貨單</p>
                    </div>
                    <div style="width:101.6%; padding-left:3px;height:20px; border-left: 1px solid; border-top: 1px solid; border-right: 1px solid;">
                        訂單日期：' . Session::get('date_ret') . '
                    </div>
                    <div style="width:101.6%; padding-left:3px;height:20px; border-left: 1px solid;  border-right: 1px solid;">
                        訂單編號：' . Session::get('sn_ret') . '
                    </div>
                    <div style="width:101.6%; padding-left:3px;height:20px;border-left: 1px solid; border-bottom: 1px solid; border-right: 1px solid;">
                        客戶編號：
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid; border-top: 1px solid; border-right: 1px solid;">
                        <div style="width:70px;padding-left:3px height:20px;float:left; display: inline-block;">客戶名稱 ：</div>
                        <div style="width:430px; height:20px;float:left; display: inline-block;"></div>
                        <div style="width:200px; height:20px;float:left; display: inline-block;">統一編號: </div>
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
                $html .= '<div style="width:102%; height:15px; border-left: 1px solid;  border-right: 1px solid;">
                        <div style="width:100px; height:15px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:300px; height:15px;float:left; display: inline-block; border-right: 1px solid;">' . $type[$i] . '</div>
                        <div style="width:70px; height:15px;float:left; display: inline-block; border-right: 1px solid;">' . $count[$i] . '</div>
                        <div style="width:115px; height:15px;float:left; display: inline-block; border-right: 1px solid;">NT$ -</div>
                        <div style="width:115px; height:15px;float:left; display: inline-block;">NT$ -</div>
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
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ -</div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; ">
                        <div style="width:100px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:377px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">營業稅</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ -</div>
                    </div>
                    <div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:100px; text-align:center; height:20px;float:left; display: inline-block; border-right: 1px solid;">註</div>
                        <div style="width:377px; height:20px;float:left; display: inline-block; border-right: 1px solid;"></div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">總計</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">NT$ -</div>
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

        $idx = 0;
        $found = false;
        $all_start = explode(',,,', Session::get('temp_inv_start'));
        $all_end = explode(',,,', Session::get('temp_inv_end'));
        for ($i = 0; $i < count($all_start); $i++) {
            if ($sn >= $all_start[$i] && $sn <= $all_end[$i]) {
                $idx = $i;
                $found = true;
            }
        }
        if ($found) {
            $last_inv = explode(',,,', Session::get('temp_inv_qty'));
            $last_inv[$idx] += 1;
            $temp_string_a = '';
            foreach ($last_inv as $invs) {
                if ($temp_string_a == '') {
                    $temp_string_a = $invs;
                } else {
                    $temp_string_a .= ',,,' . $invs;
                }
            }
            Session::put('temp_inv_qty', $temp_string_a);
        }
    }

    static function getSN($msi) {
        return Inventory::where('MSISDN', $msi)->first()->SerialNumber;
    }

    static function getShipout() {
        $lasthist = History::where('SN', 'like', '%' . Input::get('sn') . '%')->where('Status', '2')->orWhere('Status', '4')->orderBy('ID', 'desc')->first()->SubAgent;
        return $lasthist;
    }

    static function getFS() {
        $lasthist['FS'] = DB::table('m_historymovement')->select('ShipoutNumber')->distinct()->get();
        $lasthist['WH'] = DB::table('m_historymovement')->select('Warehouse')->distinct()->get();
        Session::forget('FormSeriesInv');
        Session::forget('WarehouseInv');
        Session::forget('ShipouttoInv');
        return $lasthist;
    }

    static function postFS() {
        $sns = Input::get('sns');
        $lasthist['FS'] = DB::table('m_historymovement')->where('SN', 'LIKE', '%' . $sns . '%')->select('ShipoutNumber')->distinct()->get();
        $lasthist['WH'] = DB::table('m_historymovement')->select('Warehouse')->distinct()->get();
        Session::forget('FormSeriesInv');
        Session::forget('WarehouseInv');
        Session::forget('ShipouttoInv');
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
        $st = '';
        if (Session::has('FormSeriesInv'))
            $fs = Session::get('FormSeriesInv');
        if (Session::has('WarehouseInv'))
            $wh = Session::get('WarehouseInv');
        if (Session::has('ShipouttoInv'))
            $st = Session::get('ShipouttoInv');
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
                    'db' => 'LastStatusHist',
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
                array('db' => 'FabiaoNumber', 'dt' => 5),
                array('db' => 'LastWarehouse', 'dt' => 6),
                array('db' => 'Date', 'dt' => 7),
                array('db' => 'MSISDN', 'dt' => 8),
                array('db' => 'Remark', 'dt' => 9),
                array('db' => 'SerialNumber', 'dt' => 10, 'formatter' => function( $d, $row ) {
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
            $extraCondition = "m_inventory.Type " . $type;
            $extraCondition .= " && m_inventory.LastStatusHist " . $status;
            if ($wh != '')
                $extraCondition .= " && m_inventory.LastWarehouse LIKE '%" . $wh . "%'";
            if ($st != '')
                $extraCondition .= " && m_historymovement.SubAgent LIKE '%" . $st . "%'";

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
                array('db' => 'FabiaoNumber', 'dt' => 5),
                array('db' => 'LastWarehouse', 'dt' => 6),
                array('db' => 'Date', 'dt' => 7),
                array('db' => 'MSISDN', 'dt' => 8),
                array('db' => 'Remark', 'dt' => 9),
                array('db' => 'SerialNumber', 'dt' => 10, 'formatter' => function( $d, $row ) {
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
            $extraCondition = "m_inventory.Type " . $type;
            $extraCondition .= " && m_historymovement.Status " . $status;
            if ($wh != '')
                $extraCondition .= " && m_inventory.LastWarehouse LIKE '%" . $wh . "%'";
            if ($st != '')
                $extraCondition .= " && m_historymovement.SubAgent LIKE '%" . $st . "%'";
            $join = ' INNER JOIN m_historymovement on m_historymovement.ID = m_inventory.LastStatusID';
            echo json_encode(
                    SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
        }
    }

    static function inventoryDataBackupUncat() {
        $table = 'm_uncatagorized';
        $primaryKey = 'm_uncatagorized`.`SerialNumber';
        $columns = array(
            array('db' => 'SerialNumber', 'dt' => 0),
            array(
                'db' => 'MSISDN',
                'dt' => 1
            ),
            array(
                'db' => 'Remark',
                'dt' => 2
            )
        );

        $sql_details = getConnection();

        require('ssp.class.php');
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "";
        $join = '';
        echo json_encode(
                SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

    static function inventoryDataBackupAnomalies() {
        $table = 'm_anomalies';
        $primaryKey = 'm_anomalies`.`SerialNumber';
        $columns = array(
            array('db' => 'SerialNumber', 'dt' => 0),
            array(
                'db' => 'MSISDN',
                'dt' => 1
            ),
            array(
                'db' => 'Remark',
                'dt' => 2
            )
        );

        $sql_details = getConnection();

        require('ssp.class.php');
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "";
        $join = '';
        echo json_encode(
                SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

    static function delInv() {
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
                            ->where('m_historymovement.Status', '!=', '2')->where('m_inventory.Missing', '0')->count();
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

    static function inventoryDataBackupDashboard() {
        $table = 'r_shipout_subagent';
        $primaryKey = 'r_shipout_subagent`.`SubAgent';
        $columns = array(
            array('db' => 'SubAgent', 'dt' => 0),
            array('db' => '1Shipout', 'dt' => 1),
            array('db' => '1Active', 'dt' => 2),
            array('db' => '1ApfReturn', 'dt' => 3),
            array('db' => '2Shipout', 'dt' => 4),
            array('db' => '2Active', 'dt' => 5),
            array('db' => '2ApfReturn', 'dt' => 6),
            array('db' => '3Shipout', 'dt' => 7),
            array('db' => '3Active', 'dt' => 8),
            array('db' => '3ApfReturn', 'dt' => 9)
        );

        $sql_details = getConnection();

        require('ssp.class.php');
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "";
        $join = '';

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
        $statusAvail = $id;
        $arrayids = Session::get('SemuaSN');
        $array = implode("','", $arrayids);
        $string_temp = "2','4";
        if ($statusAvail == 0) {
            $string_temp = "0','1','3";
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
        $extraCondition .= " AND m_historymovement.Status IN ('" . $string_temp . "')";
        $join = ' INNER JOIN m_historymovement on m_historymovement.ID = m_inventory.LastStatusID';

        echo json_encode(
                SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

}
