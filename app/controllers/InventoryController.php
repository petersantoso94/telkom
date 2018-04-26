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
                            $for_raw .= "('" . $arr_sn[$i] . "','" . $arr_shipinprice[$i] . "',0,0,'" . $arr_laststatusid[$i] . "','" . $arr_lastwarehouse[$i] . "','" . $arr_type[$i] . "','" . $arr_msisdn[$i] . "','TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                        else
                            $for_raw .= ",('" . $arr_sn[$i] . "','" . $arr_shipinprice[$i] . "',0,0,'" . $arr_laststatusid[$i] . "','" . $arr_lastwarehouse[$i] . "','" . $arr_type[$i] . "','" . $arr_msisdn[$i] . "','TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
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
                            $for_raw .= "('" . $arr_sn[$i] . "','" . $arr_shipinprice[$i] . "',0,0,'" . $arr_laststatusid[$i] . "','" . $arr_lastwarehouse[$i] . "','" . $arr_type[$i] . "',NULL,'TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                        else
                            $for_raw .= ",('" . $arr_sn[$i] . "','" . $arr_shipinprice[$i] . "',0,0,'" . $arr_laststatusid[$i] . "','" . $arr_lastwarehouse[$i] . "','" . $arr_type[$i] . "',NULL,'TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
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
                                    $wh = Input::get('warehouse', false);
                                    $sn = (string) $value[0];
                                    array_push($arr_sn, $sn);
                                    array_push($arr_msisdn, $value[1]);
                                    array_push($arr_type, $type);
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
                            $for_raw .= "('" . $arr_sn[$i] . "',0,0,0,'" . $arr_laststatusid[$i] . "','" . $arr_lastwarehouse[$i] . "','" . $arr_type[$i] . "','" . $arr_msisdn[$i] . "','TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                        else
                            $for_raw .= ",('" . $arr_sn[$i] . "',0,0,0,'" . $arr_laststatusid[$i] . "','" . $arr_lastwarehouse[$i] . "','" . $arr_type[$i] . "','" . $arr_msisdn[$i] . "','TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
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

        return View::make('dashboard')->withPage('dashboard');
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
            $invs = str_replace("'", '', Session::get('temp_inv_arr'));
            $arr_inv = explode(',', $invs);

            $counter = 0;
            $allInvAvail = Inventory::whereIn('SerialNumber', $arr_inv)->where('Missing', 0)->get();
            foreach ($allInvAvail as $inv) {
                $status_ = 2;
                $history = History::where('ID', $inv->LastStatusID)->first();
                if ($history->Status != 2) { //available
                    $hist = new History();
                    $hist->SN = $inv->SerialNumber;
                    $hist->SubAgent = $subagent;
                    $hist->Price = $inv->TempPrice;
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
                    $hist->FabiaoNumber = Input::get('fabiaoNumber');
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
                    $inv->save();

                    $allhist = History::where('SN', $inv->SerialNumber)->get();
                    foreach ($allhist as $hist) {
                        $hist->LastStatus = 2;
                        $hist->save();
                    }
                    $counter++;
                }
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
                            if ($hist->Status == 2) {
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
                        foreach ($reader->getSheetIterator() as $sheet) {
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
                            foreach ($reader->getSheetIterator() as $sheet) {
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
                            $ids = $arr_msisdn;
                            $ids = implode("','", $ids);
                            $check_msisdn = [];
                            $right_msisdn = DB::select("SELECT `MSISDN` FROM `m_inventory` WHERE `MSISDN` in ('{$ids}')");
                            foreach ($right_msisdn as $msisdn) {
                                $check_msisdn[] = $msisdn->MSISDN;
                            }
                            $not_found = array_diff($arr_msisdn, $check_msisdn);
                            $not_found_str = '';
                            $counter = 0;
                            foreach ($not_found as $str) {
                                if ($counter == 0)
                                    $not_found_str .= $str;
                                else {
                                    if ($counter % 7 == 0)
                                        $not_found_str .= ',' . $str . '<br>';
                                    else
                                        $not_found_str .= ',' . $str;
                                }
                                $counter++;
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

                            return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNotfound($not_found_str);
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
                            $ids = $arr_msisdn;
                            $ids = implode("','", $ids);
                            $check_msisdn = [];
                            $right_msisdn = DB::select("SELECT `MSISDN` FROM `m_inventory` WHERE `MSISDN` in ('{$ids}')");
                            foreach ($right_msisdn as $msisdn) {
                                $check_msisdn[] = $msisdn->MSISDN;
                            }
                            $not_found = array_diff($arr_msisdn, $check_msisdn);
                            $not_found_str = '';
                            $counter = 0;
                            foreach ($not_found as $str) {
                                if ($counter == 0)
                                    $not_found_str .= $str;
                                else {
                                    if ($counter % 7 == 0)
                                        $not_found_str .= ',' . $str . '<br>';
                                    else
                                        $not_found_str .= ',' . $str;
                                }
                                $counter++;
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
                            return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumberac($counter)->withNotfound($not_found_str);
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
                        foreach ($reader->getSheetIterator() as $sheet) {
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
                        /* $check_msisdn = [];
                          $ids = $arr_voc;
                          $ids = implode("','", $ids);
                          $right_msisdn = DB::select("SELECT `SerialNumber` FROM `m_inventory` WHERE `SerialNumber` in ('{$ids}')");
                          foreach ($right_msisdn as $msisdn) {
                          $check_msisdn[] = $msisdn->SerialNumber;
                          }
                          $not_found = array_diff($arr_voc, $check_msisdn);
                          $not_found = implode("','", $not_found);
                          dd($not_found); */
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
            } else if (Input::get('jenis') == 'productive') {
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
                        foreach ($reader->getSheetIterator() as $sheet) {
                            $date_temp = $real_filename;
                            $date_temp = explode("_", $date_temp)[2];
                            $month_temp = substr($date_temp, 4, 2);
                            $year_temp = substr($date_temp, 0, 4);
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
                        $for_raw = '';
                        for ($i = 0; $i < count($arr_msisdn); $i++) {
                            $unik = $arr_msisdn[$i] . '-' . $arr_month[$i] . '-' . $arr_year[$i];
                            if ($i == 0)
                                $for_raw .= "('" . $arr_msisdn[$i] . "','" . $arr_mo[$i] . "','" . $arr_mt[$i] . "','" . $arr_internet[$i] . "','" . $arr_sms[$i] . "',NULL,'" . $arr_month[$i] . "','" . $arr_year[$i] . "','" . $unik . "')";
                            else
                                $for_raw .= ",('" . $arr_msisdn[$i] . "','" . $arr_mo[$i] . "','" . $arr_mt[$i] . "','" . $arr_internet[$i] . "','" . $arr_sms[$i] . "',NULL,'" . $arr_month[$i] . "','" . $arr_year[$i] . "','" . $unik . "')";
                        }
                        DB::insert("INSERT INTO m_productive VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE Unik=Unik;");
                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumberpr(count($arr_msisdn));
                    }
                }
                return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
            }else if (Input::get('jenis') == 'act_sip') {
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
                        foreach ($reader->getSheetIterator() as $sheet) {
                            foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                if ($rowNumber > 1) {
                                    // do stuff with the row
                                    $msisdn = (string) $value[14];
                                    if ($msisdn != '' && $msisdn != null) {
                                        $msisdn = str_replace('\'', '', $msisdn);
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
        return $data;
    }

    static function getCHURN() {
        $year = Input::get('year');
//        $year = '2016';
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
        return $data;
    }

    static function getProductive() {
        $year = Input::get('year');
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
        return $data;
    }

    static function getSumService() {
        $year = Input::get('year');
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
        return $data;
    }

    static function getPayload() {
        $year = Input::get('year');
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
        return $data;
    }

    static function getPayloadPerUser() {
        $year = Input::get('year');
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
        return $data;
    }

    static function getInternetVsNon() {
        $year = Input::get('year');
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
        return $data;
    }

    static function getVouchersTopUp() {
        $year = Input::get('year');
//        $year = '2017';
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
        $data = [];
        //1 -> evoucher; 2 -> phvoucher
        $all_ivr = [];
        if ($type == '') {
            // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
            $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%topup%\'')->get();
            if ($all_ivr != null) {
                foreach ($all_ivr as $ivr) {
                    $stats = '';
                    $temp_stat = $ivr->Status;
                    if (substr($temp_stat, 0, 1) == '1') {
                        $stats = 'PH-VOUCHER 100';
                    } else if (substr($temp_stat, 0, 1) == '2') {
                        $stats = 'PH-VOUCHER 300';
                    } else if (substr($temp_stat, 0, 1) == '3') {
                        $stats = 'E-VOUCHER 50';
                    } else if (substr($temp_stat, 0, 1) == '4') {
                        $stats = 'E-VOUCHER 100';
                    } else if (substr($temp_stat, 0, 1) == '5') {
                        $stats = 'E-VOUCHER 300';
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
            return $data;
        } else if ($type == '1') {
            // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
            $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%topup%\'')->get();
            if ($all_ivr != null) {
                foreach ($all_ivr as $ivr) {
                    $stats = '';
                    $temp_stat = $ivr->Status;
                    if (substr($temp_stat, 0, 1) == '3') {
                        $stats = 'E-VOUCHER 50';
                    } else if (substr($temp_stat, 0, 1) == '4') {
                        $stats = 'E-VOUCHER 100';
                    } else if (substr($temp_stat, 0, 1) == '5') {
                        $stats = 'E-VOUCHER 300';
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
            return $data;
        }
        return $data;
    }

    static function getMSISDNTopUp() {
        $year = Input::get('year');
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
        return $data;
    }

    static function getChurnDetail() {
        $year = Input::get('year');
//        $year = '2018';
        $type = '';
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
        return $data;
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

    static function exportExcel($filter) {
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
            $invs = DB::table('m_inventory')
                            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                            ->where('m_inventory.Type', $typesym, $type)
                            ->where('m_historymovement.Status', $statussym, $status)->get();
            if ($wh != '') {
                $invs = DB::table('m_inventory')
                                ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                                ->where('m_inventory.Type', $typesym, $type)->where('m_inventory.LastWarehouse', 'LIKE', '%' . $wh . '%')
                                ->where('m_historymovement.Status', $statussym, $status)->get();
                if ($st != '') {
                    $invs = DB::table('m_inventory')
                                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                                    ->where('m_inventory.Type', $typesym, $type)->where('m_inventory.LastWarehouse', 'LIKE', '%' . $wh . '%')
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $st . '%')
                                    ->where('m_historymovement.Status', $statussym, $status)->get();
                }
            } else {
                if ($st != '') {
                    $invs = DB::table('m_inventory')
                                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                                    ->where('m_inventory.Type', $typesym, $type)
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $st . '%')
                                    ->where('m_historymovement.Status', $statussym, $status)->get();
                }
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
                if ($st != '') {
                    $invs = DB::table('m_inventory')
                                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                                    ->where('m_inventory.Type', $typesym, $type)
                                    ->where('m_historymovement.Status', $statussym, $status)->where('m_inventory.LastWarehouse', 'LIKE', '%' . $wh . '%')
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $st . '%')
                                    ->where('m_historymovement.ShipoutNumber', 'like', '%' . $fs . '%')->get();
                }
            } else {
                if ($st != '') {
                    $invs = DB::table('m_inventory')
                                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                                    ->where('m_inventory.Type', $typesym, $type)
                                    ->where('m_historymovement.Status', $statussym, $status)
                                    ->where('m_historymovement.SubAgent', 'LIKE', '%' . $st . '%')
                                    ->where('m_historymovement.ShipoutNumber', 'like', '%' . $fs . '%')->get();
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
//        $date = "2018-01-21";
        $year = explode("-", $date)[0];
        $month = explode("-", $date)[1];
        $day = explode("-", $date)[2];

        if (substr($month, 0, 1) === "0") {
            $month = substr($month, 1, 1);
        }
        $last_year = $year;
        $last_month = $month - 1;
        if ($month === "01" || $month === "1") {
            $last_year = $year - 1;
            $last_month = 12;
        }
        $filenames = $month . "-" . $year . ' vs ' . $last_month . "-" . $last_year;
        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/Weekly_Performance_" . $filenames . ".xlsx";
        $writer->openToFile($filePath);
        $myArr = array($month . "-" . $year . ' vs ' . $last_month . "-" . $last_year);
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("Peformance Report Per Week (Best on Current Date Transaction Month Over Month)");
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("ITEMS", "UNIT", "CM", "BM", 'GROWTH');
        $writer->addRow($myArr); // add a row at a time

        $data = array();
        $all_ivr = Stats::where('Year', $year)->where('Month', $month)->whereRaw('Status LIKE \'%Churn%\'')->get();
        $data['churn'][0] = $all_ivr[0]->Counter;
        $all_ivr = Stats::where('Year', $last_year)->where('Month', $last_month)->whereRaw('Status LIKE \'%Churn%\'')->get();
        $data['churn'][1] = $all_ivr[0]->Counter;
        $all_ivr = Stats::where('Year', $year)->where('Month', $month)->whereRaw('Status LIKE \'%Activation%\'')->get();
        $data['act'][0] = $all_ivr[0]->Counter;
        $all_ivr = Stats::where('Year', $last_year)->where('Month', $last_month)->whereRaw('Status LIKE \'%Activation%\'')->get();
        $data['act'][1] = $all_ivr[0]->Counter;

        //total process
        $data['churn'][2] = round((($data['churn'][0] - $data['churn'][1]) / $data['churn'][0]) * 100, 2);
        $data['act'][2] = round((($data['act'][0] - $data['act'][1]) / $data['act'][0]) * 100, 2);

        $data["net"][0] = $data['act'][0] - $data['churn'][0];
        $data["net"][1] = $data['act'][1] - $data['churn'][1];
        $data['net'][2] = round((($data['net'][0] - $data['net'][1]) / $data['net'][0]) * 100, 2);

        $myArr = array("NET ADDITIONAL", "SUBS", $data["net"][0], $data["net"][1], $data["net"][2] . '%');
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("ACQUITITION", "SUBS", $data["act"][0], $data["act"][1], $data["act"][2] . '%');
        $writer->addRow($myArr);
        $myArr = array("CHURN", "SUBS", $data["churn"][0], $data["churn"][1], $data["churn"][2] . '%');
        $writer->addRow($myArr);
        $writer->addRow(['']);

        $data = array();
        $all_ivr = Stats::where('Year', $year)->where('Month', $month)->whereRaw('Status LIKE \'%topup%\'')->get();
        // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = '';
                $temp_stat = $ivr->Status;
                if (substr($temp_stat, 0, 1) == '2') {
                    $data['PH300'][0] = $ivr->Counter;
                } else if (substr($temp_stat, 0, 1) == '5') {
                    $data['E300'][0] = $ivr->Counter;
                }
            }
        }
        $all_ivr = Stats::where('Year', $last_year)->where('Month', $last_month)->whereRaw('Status LIKE \'%topup%\'')->get();
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = '';
                $temp_stat = $ivr->Status;
                if (substr($temp_stat, 0, 1) == '2') {
                    $data['PH300'][1] = $ivr->Counter;
                } else if (substr($temp_stat, 0, 1) == '5') {
                    $data['E300'][1] = $ivr->Counter;
                }
            }
        }
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
        $all_ivr = Stats::where('Year', $year)->where('Month', $month)->whereRaw('Status > 10')->get();
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                if ($ivr->Status == '180') {
                    $data['1GB'][0] = $ivr->Counter;
                } else if ($ivr->Status == '300') {
                    $data['2GB'][0] = $ivr->Counter;
                } else {
                    $data['30DAY'][0] = $ivr->Counter;
                }
            }
        }
        $all_ivr = Stats::where('Year', $last_year)->where('Month', $last_month)->whereRaw('Status > 10')->get();
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                if ($ivr->Status == '180') {
                    $data['1GB'][1] = $ivr->Counter;
                } else if ($ivr->Status == '300') {
                    $data['2GB'][1] = $ivr->Counter;
                } else {
                    $data['30DAY'][1] = $ivr->Counter;
                }
            }
        }
        //total process
        $data['1GB'][2] = round((($data['1GB'][0] - $data['1GB'][1]) / $data['1GB'][0]) * 100, 2);
        $data['2GB'][2] = round((($data['2GB'][0] - $data['2GB'][1]) / $data['2GB'][0]) * 100, 2);
        $data['30DAY'][2] = round((($data['30DAY'][0] - $data['30DAY'][1]) / $data['30DAY'][0]) * 100, 2);

        $data["INTERNET"][0] = $data['1GB'][0] + $data['2GB'][0] + $data['30DAY'][0];
        $data["INTERNET"][1] = $data['1GB'][1] + $data['2GB'][1] + $data['30DAY'][1];
        $data['INTERNET'][2] = round((($data['INTERNET'][0] - $data['INTERNET'][1]) / $data['INTERNET'][0]) * 100, 2);

        $myArr = array("INTERNET", "SUBS", $data["INTERNET"][0], $data["INTERNET"][1], $data["INTERNET"][2] . '%');
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("1GB", "SUBS", $data["1GB"][0], $data["1GB"][1], $data["1GB"][2] . '%');
        $writer->addRow($myArr);
        $myArr = array("2GB", "SUBS", $data["2GB"][0], $data["2GB"][1], $data["2GB"][2] . '%');
        $writer->addRow($myArr);
        $myArr = array("30 DAYS", "SUBS", $data["30DAY"][0], $data["30DAY"][1], $data["30DAY"][2] . '%');
        $writer->addRow($myArr);
        $writer->addRow(['']);

        $tempmonth = $month;
        if (strlen($month) === 1) {
            $tempmonth = "0" . $month;
        }
        $all_ivr = Stats::where('Year', $year)->where('Month', $tempmonth)->whereRaw('Status LIKE \'%_sum%\'')->get();
        $data = array();
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
        $all_ivr = Stats::where('Year', $last_year)->where('Month', $tempmonth)->whereRaw('Status LIKE \'%_sum%\'')->get();
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

        $myArr = array("MVNO CALL", "MINS", $data["MVNO_CALL"][0], $data["MVNO_CALL"][1], $data["MVNO_CALL"][2] . '%');
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("MO CALL", "MINS", $data["MO"][0], $data["MO"][1], $data["MO"][2] . '%');
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("MT CALL", "MINS", $data["MT"][0], $data["MT"][1], $data["MT"][2] . '%');
        $writer->addRow($myArr); // add a row at a time
        $writer->addRow(['']);
        $myArr = array("SMS", "TEXT", $data["SMS"][0], $data["SMS"][1], $data["SMS"][2] . '%');
        $writer->addRow($myArr); // add a row at a time
        $writer->addRow(['']);
        $myArr = array("INTERNET", "GB", $data["IT"][0], $data["IT"][1], $data["IT"][2] . '%');
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
                $myArr = array($data['shipoutto'], $data['subagent'], $data['date'], $data['COLUMBIA'][1], $data['COLUMBIA'][4], $data['COLUMBIA'][3], $data['COLUMBIA'][2], $data['TELIN TAIWAN'][1], $data['TELIN TAIWAN'][4], $data['TELIN TAIWAN'][3], $data['TELIN TAIWAN'][2]);
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
                $myArr = array($data['shipoutto'], $data['subagent'], $data['date'], $data['COLUMBIA'][1], $data['COLUMBIA'][4], $data['COLUMBIA'][3], $data['COLUMBIA'][2], $data['TELIN TAIWAN'][1], $data['TELIN TAIWAN'][4], $data['TELIN TAIWAN'][3], $data['TELIN TAIWAN'][2]);
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
                $myArr = array($data['shipoutto'], $data['subagent'], $data['date'], $data['COLUMBIA'][1], $data['COLUMBIA'][4], $data['COLUMBIA'][3], $data['COLUMBIA'][2], $data['TELIN TAIWAN'][1], $data['TELIN TAIWAN'][4], $data['TELIN TAIWAN'][3], $data['TELIN TAIWAN'][2]);
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
                $myArr = array($data['shipoutto'], $data['subagent'], $data['date'], $data['COLUMBIA'][1], $data['COLUMBIA'][4], $data['COLUMBIA'][3], $data['COLUMBIA'][2], $data['TELIN TAIWAN'][1], $data['TELIN TAIWAN'][4], $data['TELIN TAIWAN'][3], $data['TELIN TAIWAN'][2]);
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

        $myArr = array("", "", "TOTAL: ", $total['COLUMBIA'][1], $total['COLUMBIA'][4], $total['COLUMBIA'][3], $total['COLUMBIA'][2], $total['TELIN TAIWAN'][1], $total['TELIN TAIWAN'][4], $total['TELIN TAIWAN'][3], $total['TELIN TAIWAN'][2]);
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
            $myArr = array($month[$i], $idx1, $idx2, $idx3, $idx4, $idx5, $idx6);
            $writer->addRow($myArr); // add a row at a time
        }
        $myArr = array("TOTAL", $total[0], $total[1], $total[2], $total[3], $total[4], $total[5]);
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

    static function exportExcelUserDashboard() {
        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/user_report_allyears.xlsx";
        $writer->openToFile($filePath);
//
        $myArr = array("All User  Reporting");
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("Name", "Activation Date", "Churn Date", "Total Top Up (NTD)", "Last Top Up Date", "Service Usage", "Last Service Usage Date");
        $writer->addRow($myArr); // add a row at a time
        
        $simtopup = DB::table('m_inventory as inv1')
                        ->whereRaw('inv1.ActivationName IS NOT NULL')
                        ->select(DB::raw("inv1.`ActivationDate`,inv1.`ActivationName`,inv1.`ChurnDate`"
                                        . ",(SELECT COUNT(inv2.`SerialNumber`) FROM `m_inventory` as inv2 WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN`) as 'TotalVoucherPurchased'"
                                        . ",(SELECT inv2.`TopUpDate` FROM `m_inventory` as inv2  WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN` ORDER BY inv2.`TopUpDate` DESC LIMIT 1) as 'LastDatePurchasedVoucher' "
                                        . ",(SELECT prod.`Service` FROM `m_productive` as prod  WHERE prod.`MSISDN` = inv1.`MSISDN`) as 'ServiceUsed' "
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
            $myArr = array($data->ActivationName, $data->ActivationDate, $data->ChurnDate, $data->TotalVoucherPurchased, $data->LastDatePurchasedVoucher, $stats, $data->LastDateUsedService);
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
                    $myArr = array($channel->channel, $idx1[0], $idx1[1], $idx1[2], $idx1[3], $idx1[4], $idx1[5], $idx1[6], $idx1[7], $idx1[8], $idx1[9], $idx1[10], $idx1[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $myArr = array("TOTAL", $totalsim[0], $totalsim[1], $totalsim[2], $totalsim[3], $totalsim[4], $totalsim[5], $totalsim[6], $totalsim[7], $totalsim[8], $totalsim[9], $totalsim[10], $totalsim[11]);
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
                    $myArr = array($channel->channel, $idx1[0], $idx1[1], $idx1[2], $idx1[3], $idx1[4], $idx1[5], $idx1[6], $idx1[7], $idx1[8], $idx1[9], $idx1[10], $idx1[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $myArr = array("TOTAL", $totalsim[0], $totalsim[1], $totalsim[2], $totalsim[3], $totalsim[4], $totalsim[5], $totalsim[6], $totalsim[7], $totalsim[8], $totalsim[9], $totalsim[10], $totalsim[11]);
            $writer->addRow($myArr); // add a row at a time
            $writer->addRow(['']);
            $myArr = array("EVOC 300 SHIPOUT " . $year);
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
                    $myArr = array($channel->channel, $idx2[0], $idx2[1], $idx2[2], $idx2[3], $idx2[4], $idx2[5], $idx2[6], $idx2[7], $idx2[8], $idx2[9], $idx2[10], $idx2[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $myArr = array("TOTAL", $totalvoc[0], $totalvoc[1], $totalvoc[2], $totalvoc[3], $totalvoc[4], $totalvoc[5], $totalvoc[6], $totalvoc[7], $totalvoc[8], $totalvoc[9], $totalvoc[10], $totalvoc[11]);
            $writer->addRow($myArr); // add a row at a time
            $writer->addRow(['']);
            $totalvoc = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $myArr = array("EVOC 100 SHIPOUT " . $year);
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
                    $myArr = array($channel->channel, $idx2[0], $idx2[1], $idx2[2], $idx2[3], $idx2[4], $idx2[5], $idx2[6], $idx2[7], $idx2[8], $idx2[9], $idx2[10], $idx2[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $myArr = array("TOTAL", $totalvoc[0], $totalvoc[1], $totalvoc[2], $totalvoc[3], $totalvoc[4], $totalvoc[5], $totalvoc[6], $totalvoc[7], $totalvoc[8], $totalvoc[9], $totalvoc[10], $totalvoc[11]);
            $writer->addRow($myArr); // add a row at a time
            $writer->addRow(['']);
            $totalvoc = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $myArr = array("EVOC 50 SHIPOUT " . $year);
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
                    $myArr = array($channel->channel, $idx2[0], $idx2[1], $idx2[2], $idx2[3], $idx2[4], $idx2[5], $idx2[6], $idx2[7], $idx2[8], $idx2[9], $idx2[10], $idx2[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $myArr = array("TOTAL", $totalvoc[0], $totalvoc[1], $totalvoc[2], $totalvoc[3], $totalvoc[4], $totalvoc[5], $totalvoc[6], $totalvoc[7], $totalvoc[8], $totalvoc[9], $totalvoc[10], $totalvoc[11]);
            $writer->addRow($myArr); // add a row at a time
            $writer->addRow(['']);
            $totalvoc = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $myArr = array("PHVOC 100 SHIPOUT " . $year);
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
                    $myArr = array($channel->channel, $idx2[0], $idx2[1], $idx2[2], $idx2[3], $idx2[4], $idx2[5], $idx2[6], $idx2[7], $idx2[8], $idx2[9], $idx2[10], $idx2[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $myArr = array("TOTAL", $totalvoc[0], $totalvoc[1], $totalvoc[2], $totalvoc[3], $totalvoc[4], $totalvoc[5], $totalvoc[6], $totalvoc[7], $totalvoc[8], $totalvoc[9], $totalvoc[10], $totalvoc[11]);
            $writer->addRow($myArr); // add a row at a time
            $writer->addRow(['']);
            $totalvoc = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            $myArr = array("PHVOC 300 SHIPOUT " . $year);
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
                    $myArr = array($channel->channel, $idx2[0], $idx2[1], $idx2[2], $idx2[3], $idx2[4], $idx2[5], $idx2[6], $idx2[7], $idx2[8], $idx2[9], $idx2[10], $idx2[11]);
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $myArr = array("TOTAL", $totalvoc[0], $totalvoc[1], $totalvoc[2], $totalvoc[3], $totalvoc[4], $totalvoc[5], $totalvoc[6], $totalvoc[7], $totalvoc[8], $totalvoc[9], $totalvoc[10], $totalvoc[11]);
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
                    $header = 'EVOC 300';
                else if (strtoupper($key) == 'KR0150')
                    $header = 'EVOC 100';
                else if (strtoupper($key) == 'KR0450')
                    $header = 'EVOC 50';
                else if (strtoupper($key) == 'KR0350')
                    $header = 'PHVOC 100';
                else if (strtoupper($key) == 'KR1850')
                    $header = 'PHVOC 300';
                if (!isset($data[$header])) {
                    $data[$header] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                }
                $data[$header][$datas->month - 1] = $datas->counter;
            }
            foreach ($data as $key => $val) {
                for ($i = 0; $i < 12; $i++) {
                    $totalvoc[$i] += $val[$i];
                }
                $myArr = array($key, $val[0], $val[1], $val[2], $val[3], $val[4], $val[5], $val[6], $val[7], $val[8], $val[9], $val[10], $val[11], array_sum($val));
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
                    $header = 'EVOC 300';
                else if (strtoupper($key) == 'KR0150')
                    $header = 'EVOC 100';
                else if (strtoupper($key) == 'KR0450')
                    $header = 'EVOC 50';
                else if (strtoupper($key) == 'KR0350')
                    $header = 'PHVOC 100';
                else if (strtoupper($key) == 'KR1850')
                    $header = 'PHVOC 300';
                if (!isset($data[$header])) {
                    $data[$header] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                }
                $data[$header][$datas->month - 1] = $datas->counter;
            }
            foreach ($data as $key => $val) {
                for ($i = 0; $i < 12; $i++) {
                    $totalvoc[$i] += $val[$i];
                }
                $myArr = array($key, $val[0], $val[1], $val[2], $val[3], $val[4], $val[5], $val[6], $val[7], $val[8], $val[9], $val[10], $val[11], array_sum($val));
                $writer->addRow($myArr); // add a row at a time
            }
            $myArr = array("TOTAL", $totalvoc[0], $totalvoc[1], $totalvoc[2], $totalvoc[3], $totalvoc[4], $totalvoc[5], $totalvoc[6], $totalvoc[7], $totalvoc[8], $totalvoc[9], $totalvoc[10], $totalvoc[11]);
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
                    $header = 'EVOC 300';
                else if (strtoupper($key) == 'KR0150')
                    $header = 'EVOC 100';
                else if (strtoupper($key) == 'KR0450')
                    $header = 'EVOC 50';
                else if (strtoupper($key) == 'KR0350')
                    $header = 'PHVOC 100';
                else if (strtoupper($key) == 'KR1850')
                    $header = 'PHVOC 300';
                if (!isset($data[$header])) {
                    $data[$header] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                }
                $data[$header][$datas->month - 1] = $datas->counter;
            }
            foreach ($data as $key => $val) {
                for ($i = 0; $i < 12; $i++) {
                    $totalvoc[$i] += $val[$i];
                }
                $myArr = array($key, $val[0], $val[1], $val[2], $val[3], $val[4], $val[5], $val[6], $val[7], $val[8], $val[9], $val[10], $val[11], array_sum($val));
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
                    $header = 'EVOC 300';
                else if (strtoupper($key) == 'KR0150')
                    $header = 'EVOC 100';
                else if (strtoupper($key) == 'KR0450')
                    $header = 'EVOC 50';
                else if (strtoupper($key) == 'KR0350')
                    $header = 'PHVOC 100';
                else if (strtoupper($key) == 'KR1850')
                    $header = 'PHVOC 300';
                if (!isset($data[$header])) {
                    $data[$header] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                }
                $data[$header][$datas->month - 1] = $datas->counter;
            }
            foreach ($data as $key => $val) {
                for ($i = 0; $i < 12; $i++) {
                    $totalvoc[$i] += $val[$i];
                }
                $myArr = array($key, $val[0], $val[1], $val[2], $val[3], $val[4], $val[5], $val[6], $val[7], $val[8], $val[9], $val[10], $val[11], array_sum($val));
                $writer->addRow($myArr); // add a row at a time
            }
            $myArr = array("TOTAL", $totalvoc[0], $totalvoc[1], $totalvoc[2], $totalvoc[3], $totalvoc[4], $totalvoc[5], $totalvoc[6], $totalvoc[7], $totalvoc[8], $totalvoc[9], $totalvoc[10], $totalvoc[11]);
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

            Session::put('sn', $sn);
            Session::put('date', $date);
            Session::put('subagent', $subagent);
            Session::put('to', $to);
            Session::put('fabiao', $fabiao);
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
            $wh = 'TELIN TAIWAN';
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
                        <div style="width:200px; height:20px;float:left; display: inline-block;">統一編號: 54013468</div>
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
                        <div style="width:200px; height:20px;float:left; display: inline-block;">統一編號: 54013468</div>
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
        $lasthist = History::where('SN', 'like', '%' . Input::get('sn') . '%')->where('Status', '2')->orderBy('ID', 'desc')->first()->SubAgent;
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
                array('db' => 'FabiaoNumber', 'dt' => 5),
                array('db' => 'LastWarehouse', 'dt' => 6),
                array('db' => 'Date', 'dt' => 7),
                array('db' => 'MSISDN', 'dt' => 8)
            );

            $sql_details = getConnection();

            require('ssp.class.php');
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
            $extraCondition = "m_inventory.Type " . $type;
            $extraCondition .= " && m_historymovement.LastStatus " . $status;
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
                array('db' => 'MSISDN', 'dt' => 8)
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
