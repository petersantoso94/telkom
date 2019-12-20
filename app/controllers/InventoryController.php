<?php

require dirname(__DIR__) . '/ssp.class.php';

class InventoryController extends BaseController
{

    public function getSequence($num)
    {
        return sprintf("%'.19d\n", $num);
    }

    public function showInsertInventory122()
    { #sim
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = Input::file('sample_file');
            if ($input != '') {
                if (Input::hasFile('sample_file')) {
                    $destination = base_path() . '/uploaded_file/';
                    $extention = Input::file('sample_file')->getClientOriginalExtension();
                    $filename = 'temp.' . $extention;
                    Input::file('sample_file')->move($destination, $filename);
                    $filePath = base_path() . '/uploaded_file/' . 'temp.' . $extention;
                    // $filePath = base_path() . '\uploaded_file\\' . 'temp.' . $extention;
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
                    // $check_counter = History::select('ID')->orderBy('ID', 'DESC')->first();
                    $check_counter = DB::table('m_historymovement')->select(DB::raw('ID'))->orderBy('ID', 'DESC')->first();
                    if (!$check_counter)
                        $id_counter = 1;
                    else
                        $id_counter = $check_counter->ID + 1;
                    foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                        if ($sheetIndex == 1)
                            foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                 if ($rowNumber > 1) {
                                    if ($value[0] != null && $value[0] != '') {
                                        // do stuff with the row
                                        $type = 1;
                                        $wh = 'TELIN TAIWAN';
                                        $sn = (string)$value[0];
                                        $sn = strtoupper($sn);
                                        $remark_obj = $value[11];

                                        if (is_object($remark_obj)) {
                                            $remark_obj = $remark_obj->format('Y-m-d');
                                        }else{
                                            $remark_obj = str_replace('\'', " ", $remark_obj);
                                        }
                                        array_push($arr_sn, $sn);
                                        array_push($arr_msisdn, $value[2]);
                                        array_push($arr_shipinprice, $value[14]);
                                        if (strtolower((string)$value[16]) == '4g') {
                                            $type = 4;
                                        }
                                        array_push($arr_type, $type);
                                        if ($value[6] != null && $value[6] != '') {
                                            $wh = $value[6];
                                        }
                                        array_push($arr_lastwarehouse, $wh);
                                        array_push($arr_remark, $remark_obj);

                                        //shipin
                                        $status = 0;
                                        array_push($arr_sn_hist, $sn);
                                        array_push($arr_id_hist, $id_counter);
                                        $date_shipin = $value[5];
                                        if (is_object($date_shipin)) {
                                            $date_shipin = $date_shipin->format('Y-m-d');
                                        } else {
                                            $date_shipin = strtotime($date_shipin);
                                            $date_shipin = date('Y-m-d', $date_shipin);
                                        }
                                        array_push($arr_hist_date, $date_shipin);
                                        array_push($arr_price_hist, $value[14]);
                                        array_push($arr_remark_hist, $remark_obj);
                                        $shipinNumber = $date_shipin . '/SI/TST001';
                                        array_push($arr_shipoutnumber_hist, $shipinNumber);
                                        array_push($arr_status_hist, $status);
                                        array_push($arr_subagent_hist, '-');
                                        array_push($arr_wh_hist, $wh);

                                        //there is warehouse
                                        if ($value[7] != null && $value[7] != '') {
                                            $id_counter++;
                                            $status = 3;
                                            array_push($arr_sn_hist, $sn);
                                            array_push($arr_id_hist, $id_counter);
                                            $date_shipin = $value[7];
                                            if (is_object($date_shipin)) {
                                                $date_shipin = $date_shipin->format('Y-m-d');
                                            } else {
                                                $date_shipin = strtotime($date_shipin);
                                                $date_shipin = date('Y-m-d', $date_shipin);
                                            }
                                            array_push($arr_hist_date, $date_shipin);
                                            array_push($arr_price_hist, '0');
                                            array_push($arr_remark_hist, $remark_obj);
                                            array_push($arr_laststatus_hist, $status);
                                            $shipinNumber = $date_shipin . '/WH/001';
                                            array_push($arr_shipoutnumber_hist, $shipinNumber);
                                            array_push($arr_status_hist, $status);
                                            array_push($arr_subagent_hist, '-');
                                            array_push($arr_wh_hist, $wh);
                                        }


                                        //there is shipout
                                        if ($value[9] != null && $value[9] != '') {
                                            $id_counter++;
                                            $status = 2;
                                            $tempSA = '';
                                            $tempSN = '/SO/';
                                            if (strtolower($value[13]) === 'consignment') {
                                                $status = 4;
                                                $tempSN = '/CO/';
                                            }
                                            $subagent = $value[8];
                                            if ($subagent != null && $subagent != '') {
                                                $temp_sub = $value[10];
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
                                            array_push($arr_price_hist, $value[15]);
                                            $date_shipout = $value[9];
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
                            $for_raw .= "('" . $arr_sn[$i] . "','" . $arr_shipinprice[$i] . "',0,0,'" . $arr_laststatusid[$i] . "','" . $arr_hist_status[$i] . "','" . $arr_lastwarehouse[$i] . "',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_type[$i] . "','" . $arr_msisdn[$i] . "',NULL,NULL,'TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                        else
                            $for_raw .= ",('" . $arr_sn[$i] . "','" . $arr_shipinprice[$i] . "',0,0,'" . $arr_laststatusid[$i] . "','" . $arr_hist_status[$i] . "','" . $arr_lastwarehouse[$i] . "',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_type[$i] . "','" . $arr_msisdn[$i] . "',NULL,NULL,'TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
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

    public function showInsertInventory4()
    { #vocher
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
                                        $sn = (string)$value[0];
                                        $sn = strtoupper($sn);

                                        $remark_obj = $value[9];

                                        if (is_object($remark_obj)) {
                                            $remark_obj = $remark_obj->format('Y-m-d');
                                        }else{
                                            $remark_obj = str_replace('\'', " ", $remark_obj);
                                        }
                                        array_push($arr_sn, $sn);
                                        array_push($arr_shipinprice, $value[11]);
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
                                        array_push($arr_price_hist, $value[11]);
                                        array_push($arr_remark_hist, $remark_obj);
                                        $shipinNumber = $date_shipin . '/SI/TST001';
                                        array_push($arr_shipoutnumber_hist, $shipinNumber);
                                        array_push($arr_status_hist, $status);
                                        array_push($arr_subagent_hist, '-');
                                        array_push($arr_wh_hist, $wh);

                                        //there is warehouse
                                        if ($value[5] != null && $value[5] != '') {
                                            $id_counter++;
                                            $status = 3;
                                            array_push($arr_sn_hist, $sn);
                                            array_push($arr_id_hist, $id_counter);
                                            $date_shipin = $value[5];
                                            if (is_object($date_shipin)) {
                                                $date_shipin = $date_shipin->format('Y-m-d');
                                            } else {
                                                $date_shipin = strtotime($date_shipin);
                                                $date_shipin = date('Y-m-d', $date_shipin);
                                            }
                                            array_push($arr_hist_date, $date_shipin);
                                            array_push($arr_price_hist, '0');
                                            array_push($arr_remark_hist, $remark_obj);
                                            array_push($arr_laststatus_hist, $status);
                                            $shipinNumber = $date_shipin . '/WH/001';
                                            array_push($arr_shipoutnumber_hist, $shipinNumber);
                                            array_push($arr_status_hist, $status);
                                            array_push($arr_subagent_hist, '-');
                                            array_push($arr_wh_hist, $wh);
                                        }

                                        //there is shipout
                                        if ($value[7] != null && $value[7] != '') {
                                            $id_counter++;
                                            $status = 2;
                                            $tempSA = '';
                                            $tempSN = '/SO/';
                                            if (strtolower($value[13]) === 'consignment') {
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
                                            array_push($arr_price_hist, $value[12]);
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
                            $for_raw .= "('" . $arr_sn[$i] . "','" . $arr_shipinprice[$i] . "',0,0,'" . $arr_laststatusid[$i] . "','" . $arr_hist_status[$i] . "','" . $arr_lastwarehouse[$i] . "',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_type[$i] . "',NULL,NULL,NULL,'TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                        else
                            $for_raw .= ",('" . $arr_sn[$i] . "','" . $arr_shipinprice[$i] . "',0,0,'" . $arr_laststatusid[$i] . "','" . $arr_hist_status[$i] . "','" . $arr_lastwarehouse[$i] . "',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_type[$i] . "',NULL,NULL,NULL'TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
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

    public function showInsertInventory33()
    { // find missing msisdn
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
                                    $msisdn = (string)$value[3];

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
                    $right_msisdn = DB::select("SELECT MSISDN FROM `m_inventory` WHERE MSISDN IN ('{$ids}')");
//                    dd($right_msisdn);
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


    public function showInsertInventory44()
    { // change inventory data
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
                    $arr_id = [];
                    $arr_subagent = [];
                    foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                        foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                            if ($rowNumber > 1) {
                                // do stuff with the row
                                $act_msisdn = (string)$value[0];
                                $status = ['2','4'];
                                if($value[16] == 'LAIN') {
                                    $invs = DB::table('m_inventory as inv1')
                                        ->join('m_historymovement', 'inv1.LastStatusID', '=', 'm_historymovement.ID')
                                        ->where('inv1.MSISDN', $act_msisdn)->select(DB::raw('inv1.SerialNumber, inv1.MSISDN, inv1.Type,inv1.ActivationDate,inv1.TopUpDate, m_historymovement.Status,'
                                            . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                                            . '(SELECT ID FROM m_historymovement WHERE (Status = "2" OR Status = "4") AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ID"'))->get();
                                    array_push($arr_id, $invs[0]->ID);
                                    array_push($arr_subagent, $value[14]);
                                }
                            }
                        }
                    }
                    $reader->close();
                    dd($arr_id);
                    $table = Inventory::getModel()->getTable();
                    $cases1 = [];
                    $cases2 = [];
                    $ids = [];
                    $params = [];
//                    $counter = count($arr_msisdn);

                    for ($i = 0; $i < count($arr_msisdn); $i++) {
                        $id = $arr_id[$i];
                        $cases2[] = "WHEN '{$id}' then '{$arr_subagent[$i]}'";
//                        $cases1[] = "WHEN '{$id}' then '{$arr_act[$i]}'";
                        $ids[] = '\'' . $id . '\'';
                    }

                    $ids = implode(',', $ids);
                    $cases1 = implode(' ', $cases1);
                    $cases2 = implode(' ', $cases2);
                    DB::update("UPDATE `m_historymovement` SET `SubAgent` = CASE `ID` {$cases2} END WHERE `ID` in ({$ids})");
                }
            }
        }
        return View::make('insertinventory')->withPage('insert inventory');
    }
    
    public function showInsertInventory333()
    { // change inventory data
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
                    $arr_msisdn =  [];
                    $arr_batch =  [];
                    $arr_sn =  [];
                    $arr_type =  [];
                    foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                        foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                            if ($rowNumber > 1) {
                                // do stuff with the row
                                $act_serial_number = (string)$value[0];
                                $type = (string)$value[1];
                                // $msisdn = (string)$value[2];
                                // $batch = $value[3];
                                $arr_sn[] = $act_serial_number;
                                $arr_type[] = $type;
                                // $arr_batch[] = $batch;
                                // $arr_msisdn[] = $msisdn;

                                // DB::update("UPDATE `m_inventory` SET `MSISDN_TSEL`= '". $msisdn ."', `BATCH` = '".$batch."' WHERE `SerialNumber` LIKE '%".$act_serial_number."%'");
                                //DB::update("UPDATE `m_historymovement` SET `SubAgent`= '". $subagent ."' WHERE `SN` LIKE '%".$act_serial_number."%' AND (`Status` = '2' OR `Status` = '4')");

                                // DB::update("UPDATE `m_inventory` SET `Type`= '". $type ."' WHERE `SerialNumber` LIKE '%".$act_serial_number."%'");
                            }
                        }
                    }
                    $reader->close();
                    // dd("Success");
                    $cases1 = [];
                    $cases2 = [];
                    $ids = [];
                    $params = [];
                    for ($i = 0; $i < count($arr_sn); $i++) {
                        $id = $arr_sn[$i];
                        // $cases2[] = "WHEN '{$id}' then '{$arr_batch[$i]}'";
                        // $cases1[] = "WHEN '{$id}' then '{$arr_msisdn[$i]}'";
                        $cases1[] = "WHEN '{$id}' then '{$arr_type[$i]}'";
                        $ids[] = '\'' . $id . '\'';
                    }
                    $ids = implode(',', $ids);
                    $cases1 = implode(' ', $cases1);
                    // $cases2 = implode(' ', $cases2);
                    // DB::update("UPDATE `m_inventory` SET `MSISDN_TSEL` = CASE `SerialNumber` {$cases1} END, `BATCH` = CASE `SerialNumber` {$cases2} END WHERE `SerialNumber` in ({$ids}) AND `ChurnDate` IS NULL");
                    DB::update("UPDATE `m_inventory` SET `Type` = CASE `SerialNumber` {$cases1} END WHERE `SerialNumber` in ({$ids})");
                }
            }
        }
        return View::make('insertinventory')->withPage('insert inventory');
    }

    public function showInsertInventory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = Input::file('sample_file');
            if ($input != '') {
                if (Input::hasFile('sample_file')) {
                    $destination = base_path() . '/uploaded_file/';
                    $extention = Input::file('sample_file')->getClientOriginalExtension();
                    $filename = 'temp.' . $extention;
                    Input::file('sample_file')->move($destination, $filename);
                    $inputFileName = base_path() . '/uploaded_file/' . 'temp.' . $extention;
                    $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
                    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                    $writer->save(base_path().'/uploaded_file/' . 'temp.xlsx');

                    $filePath = base_path() . '/uploaded_file/' . 'temp.xlsx';
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
                    $arr_batch = [];
                    $arr_tsel =[];
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
                                    $sn = (string)$value[0];
                                    array_push($arr_sn, $sn);
                                    array_push($arr_type, $type);
                                    array_push($arr_msisdn, $value[1]);
                                    array_push($arr_batch, $value[3]);
                                    array_push($arr_tsel, $value[4]);
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
                            $for_raw .= "('" . $arr_sn[$i] . "',0,0,0,'" . $arr_laststatusid[$i] . "','" . $arr_laststatus_hist[$i] . "','" . $arr_lastwarehouse[$i] . "',NULL,NULL,NULL,NULL,NULL,0,'" . $arr_hist_date[$i] . "','" . $arr_type[$i] . "','" . $arr_msisdn[$i] . "','".$arr_tsel[$i]."','".$arr_batch[$i]."','TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                        else
                            $for_raw .= ",('" . $arr_sn[$i] . "',0,0,0,'" . $arr_laststatusid[$i] . "','" . $arr_laststatus_hist[$i] . "','" . $arr_lastwarehouse[$i] . "',NULL,NULL,NULL,NULL,NULL,0,'" . $arr_hist_date[$i] . "','" . $arr_type[$i] . "','" . $arr_msisdn[$i] . "','".$arr_tsel[$i]."','".$arr_batch[$i]."','TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'" . $arr_remark[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                    }
                    DB::insert("INSERT INTO m_inventory VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE SerialNumber=SerialNumber;");

                    $for_raw = '';
                    for ($i = 0; $i < count($arr_id_hist); $i++) {
                        if ($i == 0)
                            $for_raw .= "('" . $arr_id_hist[$i] . "','" . $arr_sn_hist[$i] . "',NULL,'" . $arr_wh_hist[$i] . "',0,'" . $arr_shipoutnumber_hist[$i] . "',NULL,'" . $arr_status_hist[$i] . "','" . $arr_laststatus_hist[$i] . "',0,'" . $arr_hist_date[$i] . "','" . $arr_remark_hist[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                        else
                            $for_raw .= ",('" . $arr_id_hist[$i] . "','" . $arr_sn_hist[$i] . "',NULL,'" . $arr_wh_hist[$i] . "',0,'" . $arr_shipoutnumber_hist[$i] . "',NULL,'" . $arr_status_hist[$i] . "','" . $arr_laststatus_hist[$i] . "',0,'" . $arr_hist_date[$i] . "','" . $arr_remark_hist[$i] . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                    }
                    DB::insert("INSERT INTO m_historymovement VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE ID=ID;");


                    return View::make('insertinventory')->withResponse('Success')->withPage('insert inventory')->withNumber(count($arr_sn));
                }
            }
            return View::make('insertinventory')->withResponse('Failed')->withPage('insert inventory');
        }
        return View::make('insertinventory')->withPage('insert inventory');
    }

    public function showDashboard()
    {
        // SET `ActivationDate`= NULL,`ActivationName`= NULL,`ActivationStore`= NULL,`Channel`= NULL,`ChurnDate`=NULL, `ApfDate`=NULL,`Apf-Activation`=NULL
        //DB::update("UPDATE `m_inventory` SET `ActivationDate`= NULL WHERE ActivationDate LIKE '%2017-12%' AND MSISDN in ('973001441', '973001465', '973001543', '973001545', '973001694', '973001884', '973001949', '973002347', '973002349', '973002634', '973002714', '973002947', '973003842', '973004015', '973004077', '973004084', '973004338', '973004345', '973004364', '973004381', '973004502', '973004525', '973004629', '973004650', '973004670', '973004758', '973004806', '973004820', '973004887', '973004964', '973005045', '973005147', '973005421', '973005942', '973005984', '973006154', '973006405', '973006432', '973007045', '973007435', '973007954', '973008410', '973008421', '973008468', '973008490', '973009084', '973009334', '973009435', '973010249', '973010341', '973010342', '973010422', '973010441', '973010541', '973010640', '973010824', '973011243', '973011384', '973011534', '973011541', '973011614', '973011745', '973011924', '973011984', '973012459', '973012462', '973012468', '973012470', '973012494', '973012564', '973012744', '973012745', '973012784', '973012824', '973013084', '973013184', '973013264', '973013524', '973013548', '973014036', '973014063', '973014066', '973014079', '973014092', '973014105', '973014136', '973014172', '973014188', '973014211', '973014317', '973014330', '973014463', '973014484', '973014525', '973014577', '973014588', '973014607', '973014625', '973014629', '973014665', '973014683', '973014710', '973014716', '973014748', '973014781', '973014791', '973014796', '973014824', '973015340', '973016043', '973016049', '973016214', '973016294', '973016408', '973016420', '973018402', '973018451', '973018540', '973018564', '973018945', '973019024', '973019046', '973019141', '973019249', '973019642', '973019649', '973019674', '973019740', '973019745', '973020433', '973020460', '973020462', '973020476', '973021402', '973021459', '973021472', '973021482', '973021487', '973021499', '973021542', '973021546', '973021642', '973021646', '973021748', '973022014', '973022141', '973022418', '973022433', '973022436', '973024769', '973024779', '973024819', '973024854', '973024880', '973024881', '973024882', '973024891', '973024911', '973024920', '973024930', '973024939', '973024944', '973024970', '973024971', '973024993', '973025041', '973025046', '973025461', '973025490', '973025545', '973025574', '973025642', '973025742', '973025747', '973025804', '973025945', '973025964', '973026214', '973026345', '973026450', '973026854', '973026942', '973027046', '973027284', '973027402', '973027545', '973027949', '973028049', '973028149', '973028240', '973028249', '973028343', '973028348', '973028459', '973028471', '973028475', '973028493', '973028496', '973028547', '973028744', '973028940', '973028948', '973029048', '973029114', '973029448', '973030042', '973030064', '973030241', '973030405', '973030414', '973030417', '973030435', '973030437', '973030439', '973030456', '973030462', '973032422', '973032428', '973032498', '973032545', '973032643', '973032840', '973032846', '973033462', '973034417', '973034419', '973043503', '973043505', '973043506', '973043509', '973043512', '973043526', '973043538', '973043582', '973043625', '973043640', '973043674', '973043675', '973043683', '973043696', '973043707', '973043726', '973043728', '973043740', '973043744', '973043747', '973043782', '973043784', '973043786', '973043795', '973043805', '973043812', '973043820', '973043840', '973043846', '973043861', '973043872', '973043876', '973043878', '973043891', '973043913', '973043921', '973043949', '973046149', '973046172', '973046273', '973046290', '973046361', '973046412', '973046414', '973046522', '973046536', '973046577', '973046582', '973046623', '973046628', '973046647', '973046672', '973046676', '973046696', '973046707', '973046714', '973046721', '973046730', '973046761', '973046838', '973047852', '973047853', '973047989', '973049015', '973049131', '973049142', '973049145', '973049149', '973049150', '973049161', '973049172', '973049184', '973049187', '973049204', '973049219', '973049238', '973049249', '973049252', '973049254', '973049281', '973049282', '973049438', '973049457', '973049477', '973049487', '973049516', '973049548', '973049553', '973049817', '973049824', '973049851', '973049853', '973049873', '973049949', '973049986', '973050244', '973050406', '973050428', '973050435', '973050442', '973050445', '973050480', '973050614', '973050824', '973050947', '973051049', '973051400', '973051407', '973051410', '973051417', '973051428', '973051459', '973051463', '973051464', '973051487', '973051543', '973051764', '973052247', '973052284', '973052403', '973052404', '973052409', '973052433', '973052448', '973052450', '973052453', '973052741', '973052745', '973052846', '973053148', '973053340', '973053342', '973053422', '973053431', '973053470', '973053643', '973053748', '973055143', '973055463', '973055749', '973055946', '973056341', '973056413', '973056432', '973056451', '973056481', '973056498', '973056640', '973056843', '973057094', '973057294', '973057417', '973057427', '973057834', '973057849', '973058049', '973058243', '973058254', '973058340', '973058346', '973058349', '973058427', '973058456', '973058457', '973058462', '973058493', '973058534', '973058714', '973058854', '973059243', '973059246', '973059428', '973059704', '973060334', '973060384', '973060424', '973060549', '973060734', '973060740', '973061143', '973061154', '973061243', '973061304', '973061334', '973061445', '973061452', '973061487', '973061493', '973061540', '973061542', '973061547', '973061554', '973061604', '973061641', '973061704', '973061747', '973061945', '973062048', '973062142', '973062145', '973062149', '973062420', '973062422', '973062430', '973062441', '973062458', '973062461', '973062495', '973062574', '973062648', '973062724', '973062842', '973062940', '973062947', '973063034', '973063134', '973063140', '973063243', '973063423', '973063435', '973063436', '973063462', '973063467', '973063472', '973063474', '973063480', '973063492', '973063495', '973063496', '973063643', '973063743', '973063848', '973063946', '973064005', '973064010', '973064037', '973064051', '973064067', '973064105', '973064135', '973064139', '973064195', '973064197', '973064207', '973064211', '973064232', '973064250', '973064261', '973064269', '973064279', '973064289', '973064311', '973064344', '973064392', '973064402', '973064408', '973064430', '973064435', '973064469', '973064513', '973064551', '973064556', '973064564', '973064592', '973064615', '973064617', '973064621', '973064718', '973064726', '973064727', '973064745', '973064764', '973064801', '973064818', '973064819', '973064845', '973064849', '973064871', '973064925', '973064982', '973064989', '973064997', '973065164', '973065249', '973065410', '973065414', '973065480', '973065481', '973065485', '973065574', '973065614', '973065704', '973065846', '973065914', '973066164', '973066294', '973066394', '973066406', '973066481', '973066514', '973066541', '973066548', '973066794', '973067114', '973067340', '973067347', '973067453', '973067456', '973067534', '973067645', '973067694', '973067946', '973068042', '973068045', '973068147', '973068469', '973068474', '973068476', '973068487', '973068489', '973068490', '973068495', '973076046', '973076154', '973076426', '973076465', '973076471', '973076474', '973076794', '973076834', '973076846', '973076945', '973077041', '973077045', '973077124', '973077144', '973077184', '973077304', '973077341', '973077364', '973077414', '973077416', '973077434', '973077436', '973077438', '973077439', '973077456', '973077464', '973077489', '973077564', '973077642', '973077854', '973077894', '973077941', '973077947', '973078124', '973078164', '973078411', '973078424', '973078445', '973078447', '973078467', '973078475', '973078476', '973078494', '973078547', '973078614', '973078634', '973078648', '973078943', '973079046', '973079224', '973079314', '973079408', '973079412', '973079424', '973079431', '973079467', '973079474', '973079664', '973079741', '973079840', '973079845', '973079949', '973079964', '973080140', '973080194', '973080234', '973080245', '973080446', '973080464', '973080544', '973080548', '973080614', '973080742', '973080743', '973081046', '973081148', '973081454', '973081455', '973081461', '973081469', '973081479', '973081486', '973081495', '973081524', '973081534', '973081542', '973081545', '973081634', '973081664', '973081741', '973081745', '973081840', '973081904', '973082064', '973082142', '973082144', '973082249', '973082410', '973082426', '973082427', '973082437', '973082473', '973082475', '973082489', '973082493', '973082504', '973082554', '973082594', '973083043', '973083141', '973083154', '973083451', '973083453', '973083459', '973083461', '973083465', '973083469', '973083480', '973083497', '973083540', '973083547', '973083740', '973083742', '973083924', '973085124', '973085194', '973085204', '973085344', '973085421', '973085471', '973085493', '973085499', '973085543', '973085548', '973085549', '973085594', '973085640', '973085644', '973085649', '973085684', '973085794', '973085842', '973085904', '973086046', '973086047', '973086114', '973086141', '973086194', '973086340', '973086384', '973086394', '973086400', '973086402', '973086407', '973086410', '973086423', '973086427', '973086440', '973086441', '973086452', '973086456', '973086463', '973086469', '973086472', '973086479', '973086480', '973086534', '973086546', '973086554', '973086694', '973086734', '973086845', '973086884', '973087043', '973087046', '973087047', '973087342', '973087410', '973087412', '973087415', '973087416', '973087417', '973087424', '973087426', '973087433', '973087440', '973087442', '973087443', '973087446', '973087453', '973087459', '973087476', '973087491', '973087524', '973087540', '973087924', '973087949', '973087994', '973088046', '973088142', '973088241', '973088248', '973088249', '973088408', '973088428', '973088433', '973088452', '973088463', '973088474', '973088483', '973088486', '973088487', '973088493', '973088594', '973088624', '973088942', '973089047', '973089084', '973089104', '973089248', '973089274', '973089410', '973089421', '973089432', '973089434', '973089439', '973089440', '973089446', '973089479', '973089493', '973089495', '973089643', '973089734', '973089745', '973089746', '973089804', '973089834', '973089847', '973089854', '973089943', '973090014', '973090048', '973090140', '973090142', '973090143', '973090324', '973090406', '973090428', '973090439', '973090446', '973090449', '973090456', '973090461', '973090477', '973090481', '973090485', '973090498', '973090541', '973090546', '973090642', '973090764', '973090784', '973090841', '973091140', '973091340', '973091344', '973091401', '973091423', '973091429', '973091436', '973091445', '973091449', '973091451', '973091458', '973091465', '973091479', '973091490', '973091542', '973091543', '973091546', '973091564', '973091624', '973091743', '973091947', '973092140', '973092143', '973092144', '973092149', '973092154', '973092401', '973092413', '973092443', '973092445', '973092449', '973092459', '973092469', '973092498', '973092554', '973092643', '973092646', '973092654', '973092684', '973092694', '973092845', '973092943', '973092947', '973093034', '973093045', '973093142', '973093184', '973093240', '973093246', '973093345', '973093410', '973093418', '973093426', '973093435', '973093436', '973093463', '973093474', '973093493', '973093524', '973093545', '973093645', '973093749', '973093804', '973093840', '973094990', '973095140', '973095146', '973095247', '973095248', '973095274', '973095405', '973095412', '973095424', '973095447', '973095468', '973095481', '973095646', '973095814', '973095904', '973096004', '973096014', '973096054', '973096412', '973096435', '973096457', '973096489', '973096534', '973096741', '973096844', '973096945', '973096947', '973097014', '973097041', '973097064', '973097114', '973097124', '973097145', '973097241', '973097384', '973097402', '973097403', '973097420', '973097430', '973097438', '973097448', '973097460', '973097465', '973097471', '973097479', '973097541', '973097642', '973097694', '973097794', '973097804', '973097834', '973097842', '973097849', '973097934', '973098043', '973098045', '973098048', '973098145', '973098340', '973098346', '973098348', '973098401', '973098415', '973098445', '973098472', '973098492', '973098524', '973098544', '973098647', '973098654', '973098874', '973099024', '973099042', '973099084', '973099142', '973099247', '973099347', '973099439', '973099475', '973099546', '973099547', '973099649', '973099741', '973099743', '973099745', '973200304', '973200456', '973200840', '973201024', '973201348', '973201461', '973201471', '973202164', '973202264', '973202354', '973202409', '973202442', '973202456', '973202564', '973202646', '973202648', '973202684', '973203114', '973203247', '973203423', '973203446', '973203746', '973204025', '973204065', '973204070', '973204086', '973204163', '973204165', '973204182', '973204193', '973204243', '973204348', '973204353', '973204354', '973204437', '973204465', '973204526', '973204652', '973204698', '973204742', '973204908', '973204947', '973204958', '973204959', '973205649', '973205846', '973205954', '973206334', '973206429', '973206849', '973207342', '973207364', '973207436', '973207438', '973207446', '973207472', '973208549', '973208714', '973209384', '973209411', '973209457', '973210514', '973210645', '973210647', '973211647', '973211649', '973212456', '973212465', '973212466', '973212940', '973213403', '973213411', '973213457', '973213714', '973213774', '973214021', '973214102', '973214110', '973214182', '973214186', '973214195', '973214426', '973214452', '973214582', '973214616', '973214634', '973214775', '973214803', '973214844') AND SerialNumber NOT IN ('8988689003001731832', '8988689003001731835', '8988689003001731841', '8988689003001731842', '8988689003001731845', '8988689003001731848', '8988689003001731850', '8988689003001731855', '8988689003001731856', '8988689003001731862', '8988689003001731864', '8988689003001731865', '8988689003001731880', '8988689003001731887', '8988689003001731892', '8988689003001731894', '8988689003001731918', '8988689003001731919', '8988689003001731921', '8988689003001731923', '8988689003001731936', '8988689003001731939', '8988689003001731944', '8988689003001731946', '8988689003001731947', '8988689003001731949', '8988689003001731952', '8988689003001731954', '8988689003001731955', '8988689003001731956', '8988689003001731959', '8988689003001731963', '8988689003001731966', '8988689003001731972', '8988689003001731973', '8988689003001731974', '8988689003001731978', '8988689003001731979', '8988689003001731993', '8988689003001732000', '8988689003001732011', '8988689003001732015', '8988689003001732016', '8988689003001732017', '8988689003001732020', '8988689003001732025', '8988689003001732029', '8988689003001732032', '8988689003001732043', '8988689003001732044', '8988689003001732045', '8988689003001732048', '8988689003001732050', '8988689003001732056', '8988689003001732059', '8988689003001732061', '8988689003001732068', '8988689003001732070', '8988689003001732075', '8988689003001732076', '8988689003001732079', '8988689003001732080', '8988689003001732081', '8988689003001732082', '8988689003001732086', '8988689003001732087', '8988689003001732089', '8988689003001732090', '8988689003001732093', '8988689003001732094', '8988689003001732096', '8988689003001732097', '8988689003001732098', '8988689003001732099', '8988689003001732101', '8988689003001732102', '8988689003001732103', '8988689003001732111', '8988689003001732112', '8988689003001732120', '8988689003001732121', '8988689003001732122', '8988689003001732125', '8988689003001732128', '8988689003001732129', '8988689003001732130', '8988689003001732132', '8988689003001732133', '8988689003001732134', '8988689003001732140', '8988689003001732142', '8988689003001732148', '8988689003001732149', '8988689003001732152', '8988689003001732157', '8988689003001732158', '8988689003001732160', '8988689003001732162', '8988689003001732163', '8988689003001732168', '8988689003001732169', '8988689003001732170', '8988689003001732171', '8988689003001732176', '8988689003001732184', '8988689003001732185', '8988689003001732186', '8988689003001732189', '8988689003001732204', '8988689003001732212', '8988689003001732215', '8988689003001732219', '8988689003001732221', '8988689003001732226', '8988689003001732229', '8988689003001732292', '8988689003001732295', '8988689003001732301', '8988689003001732302', '8988689003001732306', '8988689003001732308', '8988689003001732309', '8988689003001732310', '8988689003001732318', '8988689003001732333', '8988689003001732335', '8988689003001732336', '8988689003001732338', '8988689003001732340', '8988689003001732356', '8988689003001732357', '8988689003001732358', '8988689003001732360', '8988689003001732373', '8988689003001732378', '8988689003001732379', '8988689003001732382', '8988689003001732384', '8988689003001732385', '8988689003001732386', '8988689003001732387', '8988689003001732388', '8988689003001732389', '8988689003001732392', '8988689003001732395', '8988689003001732402', '8988689003001732407', '8988689003001732408', '8988689003001732410', '8988689003001732500', '8988689003001732501', '8988689003001732503', '8988689003001732507', '8988689003001732512', '8988689003001732513', '8988689003001732514', '8988689003001732515', '8988689003001732517', '8988689003001732519', '8988689003001732520', '8988689003001732521', '8988689003001732522', '8988689003001732523', '8988689003001732524', '8988689003001732526', '8988689003001732528', '8988689003001732529', '8988689003001732533', '8988689003001732536', '8988689003001732538', '8988689003001732540', '8988689003001732542', '8988689003001732543', '8988689003001732544', '8988689003001732546', '8988689003001732548', '8988689003001732549', '8988689003001732553', '8988689003001732554', '8988689003001732555', '8988689003001732558', '8988689003001732559', '8988689003001732560', '8988689003001732562', '8988689003001732564', '8988689003001732570', '8988689003001732576', '8988689003001732579', '8988689003001732580', '8988689003001732581', '8988689003001732582', '8988689003001732585', '8988689003001732587', '8988689003001732601', '8988689003001732603', '8988689003001732604', '8988689003001732606', '8988689003001732607', '8988689003001732610', '8988689003001732616', '8988689003001732621', '8988689003001732622', '8988689003001732623', '8988689003001732624', '8988689003001732631', '8988689003001732642', '8988689003001732644', '8988689003001732646', '8988689003001732649', '8988689003001732650', '8988689003001732651', '8988689003001732653', '8988689003001732654', '8988689003001732655', '8988689003001732658', '8988689003001732659', '8988689003001732694', '8988689003001732695', '8988689003001732700', '8988689003001732701', '8988689003001732702', '8988689003001732703', '8988689003001732704', '8988689003001732720', '8988689003001732791', '8988689003001732792', '8988689003001733258', '8988689003001733259', '8988689003001733260', '8988689003001733261', '8988689003001733262', '8988689003001733267', '8988689003001733270', '8988689003001733275', '8988689003001733282', '8988689003001733283', '8988689003001733286', '8988689003001733287', '8988689003001733289', '8988689003001733291', '8988689003001733293', '8988689003001733297', '8988689003001733298', '8988689003001733302', '8988689003001733305', '8988689003001733307', '8988689003001733313', '8988689003001733314', '8988689003001733315', '8988689003001733316', '8988689003001733318', '8988689003001733319', '8988689003001733320', '8988689003001733322', '8988689003001733325', '8988689003001733326', '8988689003001733327', '8988689003001733329', '8988689003001733330', '8988689003001733331', '8988689003001733334', '8988689003001733335', '8988689003001733340', '8988689003001733361', '8988689003001733364', '8988689003001733370', '8988689003001733371', '8988689003001733375', '8988689003001733381', '8988689003001733382', '8988689003001733390', '8988689003001733391', '8988689003001733397', '8988689003001733398', '8988689003001733401', '8988689003001733402', '8988689003001733404', '8988689003001733405', '8988689003001733406', '8988689003001733408', '8988689003001733412', '8988689003001733414', '8988689003001733415', '8988689003001733416', '8988689003001733420', '8988689003001733423', '8988689003001733430', '8988689003001733431', '8988689003001733438', '8988689003001733440', '8988689003001733452', '8988689003001733453', '8988689003001733454', '8988689003001733456', '8988689003001733457', '8988689003001733458', '8988689003001733461', '8988689003001733462', '8988689003001733464', '8988689003001733465', '8988689003001733466', '8988689003001733467', '8988689003001733469', '8988689003001733471', '8988689003001733472', '8988689003001733474', '8988689003001733475', '8988689003001733480', '8988689003001733481', '8988689003001733483', '8988689003001733485', '8988689003001733490', '8988689003001733493', '8988689003001733494', '8988689003001733506', '8988689003001733507', '8988689003001733508', '8988689003001733509', '8988689003001733511', '8988689003001733517', '8988689003001733521', '8988689003001733527', '8988689003001733536', '8988689003001733541', '8988689003001733542', '8988689003001733543', '8988689003001733544', '8988689003001733550', '8988689003001733555', '8988689003001733556', '8988689003001733558', '8988689003001733559', '8988689003001733566', '8988689003001733567', '8988689003001733568', '8988689003001733569', '8988689003001733570', '8988689003001733576', '8988689003001733577', '8988689003001733578', '8988689003001733580', '8988689003001733582', '8988689003001733586', '8988689003001733596', '8988689003001733598', '8988689003001733603', '8988689003001733604', '8988689003001733605', '8988689003001733608', '8988689003001733609', '8988689003001733610', '8988689003001733611', '8988689003001733613', '8988689003001733614', '8988689003001733615', '8988689003001733621', '8988689003001733625', '8988689003001733626', '8988689003001733627', '8988689003001733628', '8988689003001733633', '8988689003001733636', '8988689003001733639', '8988689003001733642', '8988689003001733651', '8988689003001733661', '8988689003001733663', '8988689003001733668', '8988689003001733670', '8988689003001733672', '8988689003001733673', '8988689003001733675', '8988689003001733677', '8988689003001733679', '8988689003001733688', '8988689003001733690', '8988689003001733695', '8988689003001733698', '8988689003001733700', '8988689003001733708', '8988689003001733710', '8988689003001733717', '8988689003001733721', '8988689003001733722', '8988689003001733723', '8988689003001733724', '8988689003001733725', '8988689003001733732', '8988689003001733735', '8988689003001733736', '8988689003001733737', '8988689003001733738', '8988689003001733739', '8988689003001733741', '8988689003001733745', '8988689003001733752', '8988689003001733753', '8988689003001733758', '8988689003001733766', '8988689003001733777', '8988689003001733779', '8988689003001733781', '8988689003001733791', '8988689003001733798', '8988689003001733799', '8988689003001733802', '8988689003001733803', '8988689003001733804', '8988689003001733805', '8988689003001733806', '8988689003001733809', '8988689003001733810', '8988689003001733811', '8988689003001733813', '8988689003001733814', '8988689003001733815', '8988689003001733816', '8988689003001733817', '8988689003001733818', '8988689003001733819', '8988689003001733820', '8988689003001733822', '8988689003001733823', '8988689003001733824', '8988689003001733825', '8988689003001733826', '8988689003001733827', '8988689003001733830', '8988689003001733831', '8988689003001733833', '8988689003001733834', '8988689003001733836', '8988689003001733837', '8988689003001733838', '8988689003001733839', '8988689003001733840', '8988689003001733841', '8988689003001733843', '8988689003001733845', '8988689003001733846', '8988689003001733847', '8988689003001733848', '8988689003001733849', '8988689003001733851', '8988689003001733853', '8988689003001733855', '8988689003001733856', '8988689003001733858', '8988689003001733859', '8988689003001733860', '8988689003001733861', '8988689003001733862', '8988689003001733864', '8988689003001733865', '8988689003001733866', '8988689003001733868', '8988689003001733869', '8988689003001733870', '8988689003001733871', '8988689003001733872', '8988689003001733873', '8988689003001733874', '8988689003001733875', '8988689003001733877', '8988689003001733880', '8988689003001733882', '8988689003001733883', '8988689003001733890', '8988689003001733891', '8988689003001733892', '8988689003001733893', '8988689003001733895', '8988689003001733896', '8988689003001733897', '8988689003001733899', '8988689003001733900', '8988689003001733901', '8988689003001733902', '8988689003001733905', '8988689003001733910', '8988689003001733911', '8988689003001733912', '8988689003001733915', '8988689003001733916', '8988689003001733921', '8988689003001733926', '8988689003001733929', '8988689003001733930', '8988689003001733931', '8988689003001733932', '8988689003001733934', '8988689003001733935', '8988689003001733936', '8988689003001733938', '8988689003001733939', '8988689003001733940', '8988689003001733941', '8988689003001733943', '8988689003001733945', '8988689003001733946', '8988689003001733947', '8988689003001733948', '8988689003001733949', '8988689003001733950', '8988689003001733957', '8988689003001733960', '8988689003001733961', '8988689003001733962', '8988689003001733964', '8988689003001733965', '8988689003001733966', '8988689003001733967', '8988689003001733970', '8988689003001733971', '8988689003001733972', '8988689003001733974', '8988689003001733975', '8988689003001733978', '8988689003001733980', '8988689003001733981', '8988689003001733984', '8988689003001733986', '8988689003001733987', '8988689003001733988', '8988689003001733991', '8988689003001733992', '8988689003001733993', '8988689003001733994', '8988689003001733997', '8988689003001733998', '8988689003001734000', '8988689003001734001', '8988689003001734006', '8988689003001734007', '8988689003001734008', '8988689003001734009', '8988689003001734010', '8988689003001734012', '8988689003001734013', '8988689003001734014', '8988689003001734015', '8988689003001734017', '8988689003001734018', '8988689003001734019', '8988689003001734021', '8988689003001734022', '8988689003001734023', '8988689003001734024', '8988689003001734200', '8988689003001734204', '8988689003001734208', '8988689003001734211', '8988689003001734212', '8988689003001734213', '8988689003001734218', '8988689003001734219', '8988689003001734221', '8988689003001734222', '8988689003001734224', '8988689003001734225', '8988689003001734226', '8988689003001734227', '8988689003001734228', '8988689003001734229', '8988689003001734230', '8988689003001734231', '8988689003001734232', '8988689003001734233', '8988689003001734234', '8988689003001734235', '8988689003001734236', '8988689003001734237', '8988689003001734238', '8988689003001734240', '8988689003001734242', '8988689003001734243', '8988689003001734244', '8988689003001734247', '8988689003001734248', '8988689003001734249', '8988689003001734250', '8988689003001734252', '8988689003001734254', '8988689003001734255', '8988689003001734256', '8988689003001734258', '8988689003001734259', '8988689003001734260', '8988689003001734261', '8988689003001734262', '8988689003001734264', '8988689003001734266', '8988689003001734268', '8988689003001734269', '8988689003001734270', '8988689003001734271', '8988689003001734273', '8988689003001734274', '8988689003001734275', '8988689003001734276', '8988689003001734277', '8988689003001734279', '8988689003001734280', '8988689003001734284', '8988689003001734285', '8988689003001734286', '8988689003001734287', '8988689003001734288', '8988689003001734289', '8988689003001734290', '8988689003001734291', '8988689003001734292', '8988689003001734293', '8988689003001734295', '8988689003001734297', '8988689003001734300', '8988689003001734301', '8988689003001734304', '8988689003001734305', '8988689003001734306', '8988689003001734309', '8988689003001734310', '8988689003001734312', '8988689003001734315', '8988689003001734319', '8988689003001734320', '8988689003001734321', '8988689003001734322', '8988689003001734323', '8988689003001734324', '8988689003001734326', '8988689003001734327', '8988689003001734328', '8988689003001734329', '8988689003001734330', '8988689003001734332', '8988689003001734333', '8988689003001734334', '8988689003001734335', '8988689003001734336', '8988689003001734338', '8988689003001734340', '8988689003001734341', '8988689003001734342', '8988689003001734344', '8988689003001734345', '8988689003001734346', '8988689003001734347', '8988689003001734348', '8988689003001734350', '8988689003001734351', '8988689003001734352', '8988689003001734353', '8988689003001734355', '8988689003001734357', '8988689003001734358', '8988689003001734359', '8988689003001734360', '8988689003001734361', '8988689003001734364', '8988689003001734365', '8988689003001734366', '8988689003001734367', '8988689003001734368', '8988689003001734369', '8988689003001734370', '8988689003001734374', '8988689003001734375', '8988689003001734376', '8988689003001734377', '8988689003001734378', '8988689003001734380', '8988689003001734382', '8988689003001734384', '8988689003001734385', '8988689003001734387', '8988689003001734389', '8988689003001734391', '8988689003001734392', '8988689003001734394', '8988689003001734395', '8988689003001734396', '8988689003001734397', '8988689003001734398', '8988689003001734399', '8988689003001734400', '8988689003001734401', '8988689003001734402', '8988689003001734403', '8988689003001734404', '8988689003001734406', '8988689003001734408', '8988689003001734409', '8988689003001734410', '8988689003001734411', '8988689003001734412', '8988689003001734414', '8988689003001734416', '8988689003001734417', '8988689003001734418', '8988689003001734419', '8988689003001734420', '8988689003001734421', '8988689003001734422', '8988689003001734423', '8988689003001734424', '8988689003001734425', '8988689003001734426', '8988689003001734427', '8988689003001734428', '8988689003001734429', '8988689003001734430', '8988689003001734431', '8988689003001734432', '8988689003001734433', '8988689003001734434', '8988689003001734435', '8988689003001734436', '8988689003001734437', '8988689003001734438', '8988689003001734440', '8988689003001734441', '8988689003001734442', '8988689003001734443', '8988689003001734445', '8988689003001734447', '8988689003001734448', '8988689003001734449', '8988689003001734450', '8988689003001734451', '8988689003001734452', '8988689003001734453', '8988689003001734454', '8988689003001734456', '8988689003001734457', '8988689003001734458', '8988689003001734459', '8988689003001734460', '8988689003001734461', '8988689003001734462', '8988689003001734463', '8988689003001734464', '8988689003001734465', '8988689003001734468', '8988689003001734469', '8988689003001734470', '8988689003001734471', '8988689003001734472', '8988689003001734473', '8988689003001734474', '8988689003001734475', '8988689003001734478', '8988689003001734479', '8988689003001734480', '8988689003001734482', '8988689003001734483', '8988689003001734484', '8988689003001734486', '8988689003001734487', '8988689003001734488', '8988689003001734489', '8988689003001734490', '8988689003001734491', '8988689003001734495', '8988689003001734498', '8988689003001734499', '8988689003001734500', '8988689003001734501', '8988689003001734503', '8988689003001734504', '8988689003001734505', '8988689003001734506', '8988689003001734507', '8988689003001734508', '8988689003001734509', '8988689003001734511', '8988689003001734512', '8988689003001734513', '8988689003001734514', '8988689003001734516', '8988689003001734518', '8988689003001734519', '8988689003001734520', '8988689003001734521', '8988689003001734522', '8988689003001734523', '8988689003001734524', '8988689003001734525', '8988689003001734526', '8988689003001734527', '8988689003001734529', '8988689003001734530', '8988689003001734531', '8988689003001734532', '8988689003001734533', '8988689003001734534', '8988689003001734536', '8988689003001734537', '8988689003001734538', '8988689003001734539', '8988689003001734540', '8988689003001734541', '8988689003001734542', '8988689003001734543', '8988689003001734544', '8988689003001734545', '8988689003001734546', '8988689003001734547', '8988689003001734549', '8988689003001734550', '8988689003001734551', '8988689003001734554', '8988689003001734556', '8988689003001734557', '8988689003001734558', '8988689003001734559', '8988689003001734560', '8988689003001734562', '8988689003001734563', '8988689003001734564', '8988689003001734565', '8988689003001734566', '8988689003001734567', '8988689003001734568', '8988689003001734570', '8988689003001734571', '8988689003001734572', '8988689003001734573', '8988689003001734574', '8988689003001734575', '8988689003001734576', '8988689003001734577', '8988689003001734579', '8988689003001734580', '8988689003001734581', '8988689003001734582', '8988689003001734583', '8988689003001734584', '8988689003001734585', '8988689003001734588', '8988689003001734589', '8988689003001734590', '8988689003001734591', '8988689003001734592', '8988689003001734593', '8988689003001734594', '8988689003001734595', '8988689003001734596', '8988689003001734597', '8988689003001734598', '8988689003001734599', '8988689003001734602', '8988689003001734603', '8988689003001734604', '8988689003001734605', '8988689003001734606', '8988689003001734607', '8988689003001734609', '8988689003001734610', '8988689003001734611', '8988689003001734612', '8988689003001734613', '8988689003001734614', '8988689003001734616', '8988689003001734619', '8988689003001734620', '8988689003001734621', '8988689003001734622', '8988689003001734623', '8988689003001734624', '8988689003001734625', '8988689003001734626', '8988689003001734627', '8988689003001734628', '8988689003001734629', '8988689003001734723', '8988689003001734724', '8988689003001734725', '8988689003001734726', '8988689003001734727', '8988689003001734728', '8988689003001734729', '8988689003001734730', '8988689003001734731', '8988689003001734732', '8988689003001734733', '8988689003001734734', '8988689003001734735', '8988689003001734736', '8988689003001734737', '8988689003001734738', '8988689003001734739', '8988689003001734740', '8988689003001734741', '8988689003001734742', '8988689003001734743', '8988689003001734744', '8988689003001734745', '8988689003001734748', '8988689003001734749', '8988689003001734751', '8988689003001734752', '8988689003001734753', '8988689003001734754', '8988689003001734755', '8988689003001734756', '8988689003001734757', '8988689003001734758', '8988689003001734759', '8988689003001734761', '8988689003001734762', '8988689003001734763', '8988689003001734764', '8988689003001734765', '8988689003001734766', '8988689003001734767', '8988689003001734768', '8988689003001734769', '8988689003001734770', '8988689003001734771', '8988689003001734773', '8988689003001734774', '8988689003001734775', '8988689003001734776', '8988689003001734777', '8988689003001734778', '8988689003001734779', '8988689003001734780', '8988689003001734781', '8988689003001734782', '8988689003001734783', '8988689003001734784', '8988689003001734785', '8988689003001734787', '8988689003001734789', '8988689003001734790', '8988689003001734791', '8988689003001734793', '8988689003001734798', '8988689003001734799', '8988689003001734800', '8988689003001734801', '8988689003001734802', '8988689003001734805', '8988689003001734806', '8988689003001734808', '8988689003001734810', '8988689003001734811', '8988689003001734812', '8988689003001734813', '8988689003001734815', '8988689003001734816', '8988689003001734817', '8988689003001734819', '8988689003001734822', '8988689003001734823', '8988689003001734824', '8988689003001734825', '8988689003001734826', '8988689003001734827', '8988689003001734834', '8988689003001734839', '8988689003001734841', '8988689003001734842', '8988689003001734846', '8988689003001734853', '8988689003001734854', '8988689003001734866', '8988689003001734868', '8988689003001734871', '8988689003001734872', '8988689003001734873', '8988689003001734874', '8988689003001734877', '8988689003001734878', '8988689003001734879', '8988689003001734880', '8988689003001734884', '8988689003001734885', '8988689003001734889', '8988689003001734891', '8988689003001734896', '8988689003001734899', '8988689003001734901', '8988689003001734902', '8988689003001734903', '8988689003001734906', '8988689003001734907', '8988689003001734910', '8988689003001734911', '8988689003001734913', '8988689003001734915', '8988689003001734916', '8988689003001734917', '8988689003001734925', '8988689003001734928', '8988689003001734933', '8988689003001734936', '8988689003001734940', '8988689003001734943', '8988689003001734947', '8988689003001734950', '8988689003001734951', '8988689003001734952', '8988689003001734955', '8988689003001734957', '8988689003001734959', '8988689003001734963', '8988689003001734966', '8988689003001734970', '8988689003001734977', '8988689003001734978', '8988689003001734979', '8988689003001734980', '8988689003001734982', '8988689003001734985', '8988689003001830003', '8988689003001830004', '8988689003001830010', '8988689003001830011', '8988689003001830014', '8988689003001830031', '8988689003001830032', '8988689003001830034', '8988689003001830047', '8988689003001830048', '8988689003001830057', '8988689003001830058', '8988689003001830059', '8988689003001830063', '8988689003001830067', '8988689003001830069', '8988689003001830072', '8988689003001830080', '8988689003001830083', '8988689003001830085', '8988689003001830088', '8988689003001830089', '8988689003001830093', '8988689003001830094', '8988689003001830095', '8988689003001830105', '8988689003001830106', '8988689003001830111', '8988689003001830112', '8988689003001830113', '8988689003001830120', '8988689003001830122', '8988689003001830124')");
        $years = DB::table('r_stats')->select('Year')->orderBy('Year', 'DESC')->distinct()->get();
        $yearElements = "";
        foreach($years as $year){
            if($year->Year >0){
                $yearElements .= "<option value=".$year->Year.">".$year->Year."</option>";
            }
        }
        Session::put('UserFilterAct', 0);
        Session::put('UserFilterv300', 0);
        Session::put('UserFilterv100', 0);
        Session::put('UserFilterService', 0);
        return View::make('dashboard')->withPage('dashboard')->withYears($years)->withElements($yearElements);
    }

    public function showWarehouseInventory()
    {
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
                    $hist->Date = Input::get('eventDate');
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

    public function getSubAgent()
    {
        $shipto = Input::get('ship');
        return History::where('SubAgent', 'like', '%' . $shipto . '%')->select('SubAgent')->distinct()->get();
    }

    public function showInventoryShipout()
    {
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
                $remark_temp = addslashes($arr_remark_hist[$i]);
                if ($i == 0)
                    $for_raw .= "('" . $arr_id_hist[$i] . "','" . $arr_sn_hist[$i] . "','" . $arr_subagent_hist[$i] . "','" . $arr_wh_hist[$i] . "','" . $arr_price_hist[$i] . "','" . $arr_shipoutnumber_hist[$i] . "','{$fabiaoNumber}','" . $arr_status_hist[$i] . "','" . $arr_laststatus_hist[$i] . "',0,'" . $arr_hist_date[$i] . "','" . $remark_temp . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
                else
                    $for_raw .= ",('" . $arr_id_hist[$i] . "','" . $arr_sn_hist[$i] . "','" . $arr_subagent_hist[$i] . "','" . $arr_wh_hist[$i] . "','" . $arr_price_hist[$i] . "','" . $arr_shipoutnumber_hist[$i] . "','{$fabiaoNumber}','" . $arr_status_hist[$i] . "','" . $arr_laststatus_hist[$i] . "',0,'" . $arr_hist_date[$i] . "','" . $remark_temp . "',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
            }
            DB::insert("INSERT INTO m_historymovement VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE ID=ID;");

            $cases1 = [];
            $cases2 = [];
            $ids = [];
            $params = [];
            for ($i = 0; $i < count($arr_sn_hist); $i++) {
                $id = $arr_sn_hist[$i];
                $cases6[] = "WHEN '{$id}' then '{$arr_status_hist[$i]}'";
                $cases5[] = "WHEN '{$id}' then '{$arr_id_hist[$i]}'";
                $cases4[] = "WHEN '{$id}' then '{$arr_subagent_hist[$i]}'";
                $cases3[] = "WHEN '{$id}' then '{$arr_price_hist[$i]}'";
                $cases2[] = "WHEN '{$id}' then '{$arr_hist_date[$i]}'";
                $cases1[] = "WHEN '{$id}' then '{$arr_shipoutnumber_hist[$i]}'";
                $ids[] = '\'' . $id . '\'';
            }
            $ids = implode(',', $ids);
            $cases1 = implode(' ', $cases1);
            $cases2 = implode(' ', $cases2);
            $cases3 = implode(' ', $cases3);
            $cases4 = implode(' ', $cases4);
            $cases5 = implode(' ', $cases5);
            $cases6 = implode(' ', $cases6);
            DB::update("UPDATE `m_inventory` SET `LastStatusHist` = CASE `SerialNumber` {$cases6} END, `LastStatusID` = CASE `SerialNumber` {$cases5} END, `LastShipoutNumber` = CASE `SerialNumber` {$cases1} END, `LastShipoutDate` = CASE `SerialNumber` {$cases2} END, `LastShipoutPrice` = CASE `SerialNumber` {$cases3} END, `LastSubAgent` = CASE `SerialNumber` {$cases4} END WHERE `SerialNumber` in ({$ids})");
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

    public function showConsignment()
    {
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

    public function showReturnInventory()
    {
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
                $remark = Input::get('remark');
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
                        $msisdn = $value[0];
                        $msisdn = str_replace('\'', '', $msisdn);
                        $inv = Inventory::where('SerialNumber', 'LIKE', '%' . $msisdn . '%')->orWhere('MSISDN', 'LIKE', '%' . $msisdn . '%')->first();
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
                                $hist2->Remark = Input::get('remark');
                                $hist2->Date = Input::get('eventDate');
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

    public function showUncat()
    {
        return View::make('uncatagorized')->withPage('Uncatagorized Inventory');
    }

    public function showChange()
    {
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

    public function showResetReporting()
    {
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
            } else if (Input::get('jenis') == 'reset_sip') {
                DB::update('UPDATE `m_inventory` SET `ActivationName`= NULL,`ActivationStore`= NULL WHERE 1');
                return View::make('resetreporting')->withPage('reset reporting')->withSuccesssip('ok');
            } else if (Input::get('jenis') == 'reset_today_prod') {
                DB::delete('DELETE FROM `m_productive` WHERE DATE(dtRecord)= CURDATE()');
                return View::make('resetreporting')->withPage('reset reporting')->withSuccesstoday('ok');
            }
        }
        return View::make('resetreporting')->withPage('reset reporting');
    }

    public function showAddAdmin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = Input::get('username');
            $pass = Hash::make(Input::get('password'));
            $pos = Input::get('position');
            $iplock = Input::get('iplock');
            $ip = '';
            if ($iplock === '3') {
                $ip = '192.168.';
            } else if ($iplock === '2') {
                $ip = Input::get('ipadd');
            }
            $existed = User::where('UserEmail', $name)->first();
            if (count($existed) > 0) {
                return View::make('addadmin')->withPage('Add User')->withError('Username already exist');
            }
            $userRecord = Auth::user()->ID;
            DB::insert("INSERT INTO m_user (`UserEmail`, `UserPassword`, `dtRecord`, `dtModified`, `userRecord`, `Position`, `LockIP`) "
                . "VALUES ('{$name}','{$pass}',CURDATE(),CURDATE(),'{$userRecord}','{$pos}','{$ip}')");
            return View::make('addadmin')->withPage('Add User')->withSuccess('ok');
        }
        return View::make('addadmin')->withPage('Add User');
    }

    public function showInsertReporting()
    {
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

                        $reader->open($filePath);
                        $counter = 0;
                        $arr_msisdn = [];
                        $arr_buydate = [];
                        $arr_buy = [];
                        $arr_id = [];
                        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                            if ($sheetIndex == 1)
                                foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                    if ($rowNumber > 2) {
                                        // do stuff with the row
                                        $msisdn = (string)$value[3];

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
                                            array_push($arr_id, $value[7]);
                                        }
                                    }
                                }
                        }
                        $reader->close();
                        $check_msisdn = [];
                        $ids = $arr_msisdn;
                        $ids = implode("','", $ids);
                        // $right_msisdn = DB::select("SELECT `MSISDN` FROM `m_inventory` WHERE `MSISDN` in ('{$ids}')");
                        // foreach ($right_msisdn as $msisdn) {
                        //     $check_msisdn[] = $msisdn->MSISDN;
                        // }
                        // $not_found = array_diff($arr_msisdn, $check_msisdn);
                        // $not_found = implode(",", $not_found);
                        // $not_found = explode(",", $not_found);
                        // if (count($not_found) > 0) {
                        //     $for_raw = '';
                        //     for ($i = 0; $i < count($not_found); $i++) {
                        //         if ($i == 0)
                        //             $for_raw .= "(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from ivr file')";
                        //         else
                        //             $for_raw .= ",(NULL,'{$not_found[$i]}',CURDATE(),CURDATE(),'not found from ivr file')";
                        //     }
                        //     DB::insert("INSERT INTO m_uncatagorized VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE MSISDN=MSISDN;");
                        // }
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
                                        $msisdn = (string)$value[0];
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
                            $id = (int)$arr_msisdn[$i];
                            $cases[] = "WHEN {$id} then ?";
                            $params[] = $arr_date[$i];
                            $ids[] = $id;
                        }
                        $ids = implode(',', $ids);
                        $cases = implode(' ', $cases);
                        DB::update("UPDATE `{$table}` SET `ApfDate` = CASE `MSISDN` {$cases} END WHERE `MSISDN` in ({$ids}) AND `ApfDate` IS NULL", $params);
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
                                            $msisdn = (string)$value[14];
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
                                        $id = (int)$arr_msisdn[$i];
                                        $cases[] = "WHEN {$id} then ?";
                                        $params[] = $arr_act_store[$i];
                                        $ids[] = $id;
                                    } else {
                                        break;
                                    }
                                }
                                $ids = implode(',', $ids);
                                $cases = implode(' ', $cases);
                                DB::update("UPDATE `{$table}` SET `ActivationStore` = CASE `MSISDN` {$cases} END WHERE `MSISDN` in ({$ids}) AND `ActivationStore` IS NULL", $params);
                            }

                            return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting');
                        } else {
                            $inputFileName = base_path().'/uploaded_file/temp.' . $extention;
                            /** Load $inputFileName to a Spreadsheet Object  * */
                            // $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
                            // $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                            // $writer->save('./uploaded_file/' . 'temp.xlsx');

                            $filePath = base_path() . '/uploaded_file/' . 'temp.xlsx';
                            $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::XLSX);
                            $reader->setShouldFormatDates(true);
                            $counter = 0;
                            $reader->open($filePath);
                            $arr_msisdn = [];
                            $arr_return = [];
                            foreach ($reader->getSheetIterator() as $sheet) {
                                foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                    if ($rowNumber > 2) {
                                        // do stuff with the row
                                        $msisdn = (string)$value[3];
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
                                        $id = (int)$arr_msisdn[$i];
                                        $cases[] = "WHEN '{$id}' then '{$arr_return[$i]}'";
                                        $params[] = $arr_return[$i];
                                        $ids[] = $id;
                                    } else {
                                        break;
                                    }
                                }
                                $ids = implode(',', $ids);
                                $cases = implode(' ', $cases);
                                DB::update("UPDATE `{$table}` SET `ActivationDate` = CASE `MSISDN` {$cases} END WHERE `MSISDN` in ({$ids}) AND `ActivationDate` IS NULL");
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
                            foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                if ($rowNumber > 2) {
                                    // do stuff with the row
                                    $msisdn = (string)$value[3];
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
                        DB::update("UPDATE `{$table}` SET `ChurnDate` = CASE `MSISDN` {$cases2} END WHERE `MSISDN` in ({$ids}) AND `ChurnDate` IS NULL");

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
                    //    $inputFileName = './uploaded_file/temp.' . $extention;
                    //    /** Load $inputFileName to a Spreadsheet Object  * */
                    //    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
                    //    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                    //    $writer->save('./uploaded_file/' . 'temp.xlsx');

                    //    $filePath = base_path() . '/uploaded_file/' . 'temp.xlsx';
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
                                        $msisdn = (string)$value[4];
                                        $voc = (string)$value[12];
                                        if ($msisdn != '' && $msisdn != null) {
                                            
                                            array_push($arr_voc, $voc);
                                            array_push($arr_msisdn, $msisdn);
                                            $date_return = $value[2];
                                            array_push($arr_return, $date_return);
                                        }
                                    }
                                }
                        }
                        $reader->close();
                        // dd($arr_msisdn);
                        $check_msisdn = [];
                        $ids = $arr_voc;
                        $ids = implode("','", $ids);
                        $table = Inventory::getModel()->getTable();
                        $counter = count($arr_msisdn);
                        $block = 50000;
                        for ($j = 1; $j <= ceil($counter / $block); $j++) {
                            $cases1 = [];
                            $cases2 = [];
                            $ids = [];
                            $params = [];
                            for ($i = 0 + (($j - 1) * $block); $i < $j * $block; $i++) {
                                if ($i < $counter) {
                                    $id = $arr_voc[$i];
                                    $msisdn = str_replace('\'', '', $arr_msisdn[$i]);
                                            if (substr($msisdn, 0, 1) === '0') {
                                                $msisdn = substr($msisdn, 1);
                                            }

                                    $date_return = $arr_return[$i];
                                    $date_return = strtotime($date_return);
                                    $date_return = date('Y-m-d', $date_return);
                                    if (substr($date_return, 0, 4) === '1970') {
                                        $date_return = $arr_return[$i];
                                        $date_return = explode('/', $date_return);
                                        $date_return = $date_return[1] . '/' . $date_return[0] . '/' . $date_return[2];
                                        $date_return = strtotime($date_return);
                                        $date_return = date('Y-m-d', $date_return);
                                    }
                                    $cases2[] = "WHEN '{$id}' then '{$date_return}'";
                                    $cases1[] = "WHEN '{$id}' then '{$msisdn}'";
                                    $ids[] = '\'' . $id . '\'';
                                } else {
                                    break;
                                }
                            }
                            $ids = implode(',', $ids);
                            $cases1 = implode(' ', $cases1);
                            $cases2 = implode(' ', $cases2);
                            DB::update("UPDATE `{$table}` SET `TopUpMSISDN` = CASE `SerialNumber` {$cases1} END, `TopUpDate` = CASE `SerialNumber` {$cases2} END WHERE `SerialNumber` in ({$ids}) AND `ChurnDate` IS NULL");
                        }
                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumbertop($counter);
                    }
                }
                return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
            } /* else if (Input::get('jenis') == 'productive') {
              $input = Input::file('sample_file');
              if ($input != '') {
              if (Input::hasFile('sample_file')) {
              $destination = base_path() . '/uploaded_file/';
              $extention = Input::file('sample_file')->getClientOriginalExtension();
              $real_filename = $_FILES['sample_file']['name'];
              $filename = 'temp.' . $extention;
              Input::file('sample_file')->move($destination, $filename);
              $filePath = base_path() . '/uploaded_file/' . 'temp.' . $extention;
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
              foreach ($reader->getSheetIterator() as $sheet) {
              // grab sheet name from existing file
              $sheet_name = $sheet->getName();
              $month_temp = substr($sheet_name, 4, 2);
              $year_temp = substr($sheet_name, 0, 4);
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
              $reader->close();
              $for_raw = '';
              for ($i = 0; $i < count($arr_msisdn); $i++) {
              $unik = $arr_msisdn[$i] . '-' . $arr_month[$i] . '-' . $arr_year[$i];
              if ($i == 0)
              $for_raw .= "('" . $arr_msisdn[$i] . "','" . $arr_mo[$i] . "','" . $arr_mt[$i] . "','" . $arr_internet[$i] . "','" . $arr_sms[$i] . "',NULL,0,1,'" . $arr_month[$i] . "','" . $arr_year[$i] . "','" . $unik . "')";
              else
              $for_raw .= ",('" . $arr_msisdn[$i] . "','" . $arr_mo[$i] . "','" . $arr_mt[$i] . "','" . $arr_internet[$i] . "','" . $arr_sms[$i] . "',NULL,0,1,'" . $arr_month[$i] . "','" . $arr_year[$i] . "','" . $unik . "')";
              }
              DB::insert("INSERT INTO m_productive VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE Unik=Unik;");
              return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumberpr(count($arr_msisdn));
              }
              }
              return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
              } */ 
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
                        // $reader = ReaderFactory::create(Type::CSV); // for CSV files
                        // $reader = ReaderFactory::create(Type::ODS); // for ODS files

                        $reader->open($filePath);
                        $counter = 0;
                        $month_temp = 0;
                        $year_temp = 0;
                        $day_temp = 0;
                        $arr_msisdn = [];
                        $arr_day = [];
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
                            $day_temp = substr($date_temp, 6, 2);
                            if ($sheetIndex == 1)
                                if (substr($date_temp, 0, 1) === '2') {
                                    foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                        if ($rowNumber > 1) {
                                            // do stuff with the row
                                            $msisdn = (string)$value[0];

                                            if ($msisdn != '' && $msisdn != null) {
                                                $msisdn = str_replace('\'', '', $msisdn);
                                                if (substr($msisdn, 0, 1) === '0') {
                                                    $msisdn = substr($msisdn, 1);
                                                }
                                                array_push($arr_msisdn, $msisdn);
                                                array_push($arr_month, $month_temp);
                                                array_push($arr_year, $year_temp);
                                                array_push($arr_day, $day_temp);
                                                array_push($arr_mo, $value[3]);
                                                array_push($arr_mt, $value[6]);
                                                array_push($arr_internet, 0);
                                                array_push($arr_sms, $value[8]);
                                        //    array_push($arr_services, $value[11]);
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
                                $for_raw .= "('" . $arr_msisdn[$i] . "','" . $arr_mo[$i] . "','" . $arr_mt[$i] . "','" . $arr_internet[$i] . "','" . $arr_sms[$i] . "',NULL,1,0,'" . $arr_day[$i] . "','" . $arr_month[$i] . "','" . $arr_year[$i] . "','" . $unik . "',CURDATE(),CURDATE())";
                            else
                                $for_raw .= ",('" . $arr_msisdn[$i] . "','" . $arr_mo[$i] . "','" . $arr_mt[$i] . "','" . $arr_internet[$i] . "','" . $arr_sms[$i] . "',NULL,1,0,'" . $arr_day[$i] . "','" . $arr_month[$i] . "','" . $arr_year[$i] . "','" . $unik . "',CURDATE(),CURDATE())";
                        }
                        DB::insert("INSERT INTO m_productive VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE Month=VALUES(Month), Year=VALUES(Year), Unik=VALUES(Unik), MO=CASE WHEN MO < VALUES(MO) 
                                    THEN VALUES(MO) ELSE MO END, MT=CASE WHEN MT < VALUES(MT) 
                                    THEN VALUES(MT) ELSE MT END, Internet=CASE WHEN Internet < VALUES(Internet) 
                                    THEN VALUES(Internet) ELSE Internet END, Sms=CASE WHEN Sms < VALUES(Sms) 
                                    THEN VALUES(Sms) ELSE Sms END, DataFromHK=1, dtModified=CURDATE();");
                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumberpr(count($arr_msisdn));
                    }
                }
                return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
            } else if (Input::get('jenis') == 'productive-tst') {
                $input = Input::file('sample_file');
                if ($input != '') {
                    if (Input::hasFile('sample_file')) {
                        $destination = base_path() . '/uploaded_file/';
                        $extention = Input::file('sample_file')->getClientOriginalExtension();
                        $real_filename = $_FILES['sample_file']['name'];
                        $filename = 'temp.' . $extention;
                        Input::file('sample_file')->move($destination, $filename);
                        $inputFileName = base_path().'/uploaded_file/temp.' . $extention;
                        /** Load $inputFileName to a Spreadsheet Object  * */
                        $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
                        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                        $writer->save(base_path().'/uploaded_file/' . 'temp.xlsx');

                        $filePath = base_path() . '/uploaded_file/' . 'temp.xlsx';
                        $reader = Box\Spout\Reader\ReaderFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
                        //$reader = ReaderFactory::create(Type::CSV); // for CSV files
                        //$reader = ReaderFactory::create(Type::ODS); // for ODS files

                        $reader->open($filePath);
                        $counter = 0;
                        $month_temp = 0;
                        $year_temp = 0;
                        $service_temp = 0;
                        $arr_msisdn = [];
                        $arr_day = [];
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
                            $month_temp = (int)$month_temp;
                            if (strlen($month_temp) === 1) {
                                $month_temp = "0" . $month_temp;
                            }
                            $year_temp = substr($date_temp, 0, 4);
                            $day_temp = substr($date_temp, 6, 2);
                            if ($sheetIndex == 1)
                                if (substr($date_temp, 0, 1) === '2') {
                                    foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                        if ($rowNumber > 1) {
                                            // do stuff with the row
                                            $msisdn = (string)$value[0];

                                            if ($msisdn != '' && $msisdn != null) {
                                                $cek_mo = true;
                                                $cek_mt = true;
                                                $cek_int = true;
                                                $cek_sms = true;

                                                $msisdn = str_replace('\'', '', $msisdn);
                                                if (substr($msisdn, 0, 1) === '0') {
                                                    $msisdn = substr($msisdn, 1);
                                                }
                                                array_push($arr_msisdn, $msisdn);
                                                array_push($arr_day, $day_temp);
                                                array_push($arr_month, $month_temp);
                                                array_push($arr_year, $year_temp);
                                                array_push($arr_mo, $value[4]);
                                                array_push($arr_mt, $value[5]);
                                                array_push($arr_internet, $value[6]);
                                                array_push($arr_sms, $value[7]);

                                                if ($value[4] === '0') {
                                                    $cek_mo = FALSE;
                                                }
                                                if ($value[5] === '0') {
                                                    $cek_mt = FALSE;
                                                }
                                                if ($value[6] === '0') {
                                                    $cek_int = FALSE;
                                                }
                                                if ($value[7] === '0') {
                                                    $cek_sms = FALSE;
                                                }

                                                IF ($cek_sms == true && $cek_int == false && $cek_mo == false && $cek_mt == false)
                                                    $service_temp = 5;

                                                IF ($cek_sms == true && $cek_int == true && $cek_mo == false && $cek_mt == false)
                                                    $service_temp = 7;

                                                IF ($cek_sms == true && $cek_int == false && ($cek_mo == true || $cek_mt == true))
                                                    $service_temp = 6;

                                                IF ($cek_int == true && $cek_sms == false && $cek_mo == false && $cek_mt == false)
                                                    $service_temp = 2;

                                                IF ($cek_int == true && $cek_sms == false && ($cek_mo == true || $cek_mt == true))
                                                    $service_temp = 3;

                                                IF (($cek_mo == true || $cek_mt == true) && $cek_sms == false && $cek_int == false)
                                                    $service_temp = 1;

                                                IF (($cek_mo == true || $cek_mt == true) && $cek_sms == true && $cek_int == true)
                                                    $service_temp = 8;

                                                IF ($cek_mo == false && $cek_mt == false && $cek_sms == false && $cek_int == false)
                                                    $service_temp = 0;

                                                array_push($arr_services, $service_temp);
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
                                $for_raw .= "('" . $arr_msisdn[$i] . "','" . $arr_mo[$i] . "','" . $arr_mt[$i] . "','" . $arr_internet[$i] . "','" . $arr_sms[$i] . "','" . $arr_services[$i] . "',0,1,'" . $arr_day[$i] . "','" . $arr_month[$i] . "','" . $arr_year[$i] . "','" . $unik . "',CURDATE(),CURDATE())";
                            else
                                $for_raw .= ",('" . $arr_msisdn[$i] . "','" . $arr_mo[$i] . "','" . $arr_mt[$i] . "','" . $arr_internet[$i] . "','" . $arr_sms[$i] . "','" . $arr_services[$i] . "',0,1,'" . $arr_day[$i] . "','" . $arr_month[$i] . "','" . $arr_year[$i] . "','" . $unik . "',CURDATE(),CURDATE())";
                        }
                        DB::insert("INSERT INTO m_productive VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE Month=VALUES(Month),Service=VALUES(Service), Year=VALUES(Year), Unik=VALUES(Unik), MO=CASE WHEN MO < VALUES(MO) 
                                    THEN VALUES(MO) ELSE MO END, MT=CASE WHEN MT < VALUES(MT) 
                                    THEN VALUES(MT) ELSE MT END, Internet=CASE WHEN Internet < VALUES(Internet) 
                                    THEN VALUES(Internet) ELSE Internet END, Sms=CASE WHEN Sms < VALUES(Sms) 
                                    THEN VALUES(Sms) ELSE Sms END, DataFromTST=1, dtModified=CURDATE();");
                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumberprtst(count($arr_msisdn));
                    }
                }
                return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
            } else if (Input::get('jenis') == 'act_sip') {
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
                        $arr_actDate = [];
                        $arr_names = [];
                        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
                            if ($sheetIndex == 1)
                                foreach ($sheet->getRowIterator() as $rowNumber => $value) {
                                    if ($rowNumber > 1) {
                                        // do stuff with the row
                                        $msisdn = (string)$value[14];
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
                                            $actDate = $value[10];
                                            $actDate = strtotime($actDate);
                                            $actDate = date('Y-m-d', $actDate);
                                            if (substr($date_return, 0, 4) === '1970') {
                                                $actDate = $value[10];
                                                $actDate = explode('/', $actDate);
                                                $actDate = $actDate[1] . '/' . $actDate[0] . '/' . $actDate[2];
                                                $actDate = strtotime($actDate);
                                                $actDate = date('Y-m-d', $actDate);
                                            }
                                            array_push($arr_actDate, $actDate);
                                        }
                                    }
                                }
                        }
                        $reader->close();
                        $check_msisdn = [];
                        $ids = $arr_msisdn;
                        $ids_old = [];
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
                        // $block = 40000;
                        // for ($j = 1; $j <= ceil($counter / $block); $j++) {
                            
                        // }
                        $cases1 = [];
                        $cases2 = [];
                        $ids = [];
                        $cases1_old = [];
                        $cases2_old = [];
                        $ids_old = [];
                        $params = [];
                        $temp_msisdn = [];
                        // for ($i = 0 + (($j - 1) * $block); $i < $j * $block; $i++) {
                        for ($i = 0 ; $i < count($arr_msisdn); $i++) {
                            if ($i < $counter) {
                                if((int)substr($arr_actDate[$i],0,4) > 2018){
                                    $id = $arr_msisdn[$i];
                                    $cases1[] = "WHEN '{$id}' then '{$arr_return[$i]}'";
                                    $cases2[] = "WHEN '{$id}' then '{$arr_names[$i]}'";
                                    $ids[] = '\'' . $id . '\'';
                                }else{
                                    $id = $arr_msisdn[$i];
                                    $cases1_old[] = "WHEN '{$id}' then '{$arr_return[$i]}'";
                                    $cases2_old[] = "WHEN '{$id}' then '{$arr_names[$i]}'";
                                    $ids_old[] = '\'' . $id . '\'';
                                }
                            } else {
                                break;
                            }
                        }
                        $ids = implode(',', $ids);
                        $cases1 = implode(' ', $cases1);
                        $cases2 = implode(' ', $cases2);
                        $ids_old = implode(',', $ids_old);
                        $cases1_old = implode(' ', $cases1_old);
                        $cases2_old = implode(' ', $cases2_old);
                        if($cases1)
                        DB::update("UPDATE `{$table}` SET `ActivationStore` = CASE `MSISDN` {$cases1} END, `ActivationName` = CASE `MSISDN` {$cases2} END WHERE `MSISDN` in ({$ids}) AND ActivationDate IS NOT NULL");
                        if($ids)
                        $right_msisdn = DB::select("SELECT SerialNumber FROM m_inventory WHERE MSISDN IN ({$ids}) GROUP BY MSISDN HAVING COUNT(*) = 2");
                        foreach ($right_msisdn as $msisdn) {
                            $temp_msisdn[] = $msisdn->SerialNumber;
                        }
                        $not_found = implode('\', \'', $temp_msisdn);
                        if($not_found && $cases1_old)
                        DB::update("UPDATE `{$table}` SET `ActivationStore` = NULL, `ActivationName` = NULL WHERE `SerialNumber` in ('{$not_found}')");
                        if($cases1_old)
                        DB::update("UPDATE `{$table}` SET `ActivationStore` = CASE `MSISDN` {$cases1_old} END, `ActivationName` = CASE `MSISDN` {$cases2_old} END WHERE `MSISDN` in ({$ids_old}) AND `ActivationName` IS NULL AND ActivationDate IS NOT NULL");
                        return View::make('insertreporting')->withResponse('Success')->withPage('insert reporting')->withNumbersip($counter);
                    }
                }
                return View::make('insertreporting')->withResponse('Failed')->withPage('insert reporting');
            }
        }
        return View::make('insertreporting')->withPage('insert reporting');
    }

    public function showInventory()
    {
        Session::forget('FormSeriesInv');
        Session::forget('WarehouseInv');
        Session::forget('ShipouttoInv');
        return View::make('inventory')->withPage('inventory');
    }

    //============================ajax===============================


    static function getIVR()
    {
        $year = Input::get('year');
//        $year = '2017';
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
        $data = [];

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

                $all_ivr = DB::table('m_ivr as ivr1')
                    ->whereRaw("YEAR(`ivr1`.Date) = '{$year->Year}'")
                    ->groupBy(DB::raw('YEAR(`ivr1`.Date), MONTH(`ivr1`.Date), `ivr1`.PurchaseAmount'))
                    ->select(DB::raw("COUNT(`ivr1`.MSISDN_) as 'Counter',YEAR(`ivr1`.Date) as 'Year', MONTH(`ivr1`.Date) as 'Month', `ivr1`.PurchaseAmount as 'Status'"))->get();
                foreach ($all_ivr as $ivr) {
                    $stats = '30 days';
                    if ($ivr->Status == '180') {
                        $stats = '1 GB';
                    } else if ($ivr->Status == '88') {
                        $stats = '1 DAY 4G';
                    } else if ($ivr->Status == '300') {
                        $stats = '2 GB';
                    } else if ($ivr->Status == '360') {
                        $stats = '1 GB';
                    } else if ($ivr->Status == '540') {
                        $stats = '1 GB';
                    } else if ($ivr->Status == '600') {
                        $stats = '2 GB';
                    } else if ($ivr->Status == '900') {
                        $stats = '2 GB';
                    } else if ($ivr->Status == '200') {
                        $stats = '2 DAYS 4G + Movies 30';
                    } else if ($ivr->Status == '400') {
                        $stats = '2 DAYS 4G + Movies 30';
                    } else if ($ivr->Status == '699') {
                        $stats = 'Movies 4G';
                    } else if ($ivr->Status == '630') {
                        $stats = 'Movies 3G';
                    } else if ($ivr->Status == '1199') {
                        $stats = '90 days';
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
                    $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
            return $data;
        }
        $all_ivr = DB::table('m_ivr as ivr1')
            ->whereRaw("YEAR(`ivr1`.Date) = '{$year}'")
            ->groupBy(DB::raw('YEAR(`ivr1`.Date), MONTH(`ivr1`.Date), `ivr1`.PurchaseAmount'))
            ->select(DB::raw("COUNT(`ivr1`.MSISDN_) as 'Counter',YEAR(`ivr1`.Date) as 'Year', MONTH(`ivr1`.Date) as 'Month', `ivr1`.PurchaseAmount as 'Status'"))->get();
//        $all_ivr = Stats::where('Year', $year)->whereRaw('Status >= 10')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
        foreach ($all_ivr as $ivr) {
            $stats = '30 days';
            if ($ivr->Status == '180') {
                $stats = '1 GB';
            } else if ($ivr->Status == '88') {
                $stats = '1 DAY 4G';
            } else if ($ivr->Status == '300') {
                $stats = '2 GB';
            } else if ($ivr->Status == '360') {
                $stats = '1 GB';
            } else if ($ivr->Status == '540') {
                $stats = '1 GB';
            } else if ($ivr->Status == '900') {
                $stats = '2 GB';
            } else if ($ivr->Status == '600') {
                $stats = '2 GB';
            } else if ($ivr->Status == '200') {
                $stats = '2 DAYS 4G + Movies 30';
            } else if ($ivr->Status == '400') {
                $stats = '2 DAYS 4G + Movies 30';
            } else if ($ivr->Status == '699') {
                $stats = 'Movies 4G';
            } else if ($ivr->Status == '630' ) {
                $stats = 'Movies 3G';
            } else if ($ivr->Status == '1199') {
                $stats = '90 days';
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

    static function getCHURN()
    {
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
                $all_ivr = DB::table('m_inventory as inv1')->whereRaw("inv1.ChurnDate IS NOT NULL AND YEAR(inv1.ChurnDate) = '{$year->Year}'")->groupBy(DB::raw('YEAR(inv1.ChurnDate), MONTH(inv1.ChurnDate)'))
                ->select(DB::raw("COUNT(DISTINCT inv1.MSISDN) as 'Counter', YEAR(inv1.ChurnDate) as 'Year', MONTH(inv1.ChurnDate) as 'Month'"))->get();
                $all_act = DB::table('m_inventory as inv1')->whereRaw("inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year->Year}'")->groupBy(DB::raw('YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)'))
                ->select(DB::raw("COUNT(DISTINCT inv1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->get();
                $churn_year_before = DB::table('m_inventory as inv1')->whereRaw("inv1.ChurnDate IS NOT NULL AND YEAR(inv1.ChurnDate) < '{$year->Year}'")->groupBy(DB::raw('YEAR(inv1.ChurnDate), MONTH(inv1.ChurnDate)'))
                ->select(DB::raw("COUNT(DISTINCT inv1.MSISDN) as 'Counter', YEAR(inv1.ChurnDate) as 'Year', MONTH(inv1.ChurnDate) as 'Month'"))->orderBy('Month', 'ASC')->get();
                $act_year_before = DB::table('m_inventory as inv1')->whereRaw("inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) < '{$year->Year}'")->groupBy(DB::raw('YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)'))
                ->select(DB::raw("COUNT(DISTINCT inv1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->orderBy('Month', 'ASC')->get();
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
                        $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                        $writer->addRow($myArr); // add a row at a time
                    }
            }
            $writer->close();
            return $data;
        }
        $all_ivr = DB::table('m_inventory as inv1')->whereRaw("inv1.ChurnDate IS NOT NULL AND YEAR(inv1.ChurnDate) = '{$year}'")->groupBy(DB::raw('YEAR(inv1.ChurnDate), MONTH(inv1.ChurnDate)'))
        ->select(DB::raw("COUNT(DISTINCT inv1.MSISDN) as 'Counter', YEAR(inv1.ChurnDate) as 'Year', MONTH(inv1.ChurnDate) as 'Month'"))->get();
        $all_act = DB::table('m_inventory as inv1')->whereRaw("inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year}'")->groupBy(DB::raw('YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)'))
        ->select(DB::raw("COUNT(DISTINCT inv1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->get();
        $churn_year_before = DB::table('m_inventory as inv1')->whereRaw("inv1.ChurnDate IS NOT NULL AND YEAR(inv1.ChurnDate) < '{$year}'")->groupBy(DB::raw('YEAR(inv1.ChurnDate), MONTH(inv1.ChurnDate)'))
        ->select(DB::raw("COUNT(DISTINCT inv1.MSISDN) as 'Counter', YEAR(inv1.ChurnDate) as 'Year', MONTH(inv1.ChurnDate) as 'Month'"))->orderBy('Month', 'ASC')->get();
        $act_year_before = DB::table('m_inventory as inv1')->whereRaw("inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) < '{$year}'")->groupBy(DB::raw('YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)'))
        ->select(DB::raw("COUNT(DISTINCT inv1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->orderBy('Month', 'ASC')->get();
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

    static function getCHURN2()
    {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data["Productive Churn"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $data["Not Productive Churn"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

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
                $data["Productive Churn"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $data["Not Productive Churn"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%Churn%\'')->get();
                $all_ivr = DB::table('m_inventory as inv1')->whereRaw("inv1.ChurnDate IS NOT NULL AND YEAR(inv1.ChurnDate) = '{$year->Year}'")
                    ->groupBy(DB::raw('YEAR(inv1.ChurnDate), MONTH(inv1.ChurnDate)'))
                    ->select(DB::raw("COUNT(DISTINCT inv1.MSISDN) as 'Counter', YEAR(inv1.ChurnDate) as 'Year', MONTH(inv1.ChurnDate) as 'Month'"))->get();
                $churn = DB::table('m_inventory as inv1')->whereRaw("inv1.ChurnDate IS NOT NULL AND YEAR(inv1.ChurnDate) = '{$year->Year}'")
                    ->join('m_productive as prod1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
                    ->groupBy(DB::raw('YEAR(inv1.ChurnDate), MONTH(inv1.ChurnDate)'))
                    ->select(DB::raw("COUNT(DISTINCT prod1.MSISDN) as 'Counter', YEAR(inv1.ChurnDate) as 'Year', MONTH(inv1.ChurnDate) as 'Month'"))->get();
                if (count($churn) > 0) {
                    foreach ($churn as $ivr) {
                        $data["Productive Churn"][($ivr->Month - 1)] = $ivr->Counter;
                    }
                }
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $data["Not Productive Churn"][($ivr->Month - 1)] = $ivr->Counter - $data["Productive Churn"][($ivr->Month - 1)];
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
            return $data;
        }
        $all_ivr = DB::table('m_inventory as inv1')->whereRaw("inv1.ChurnDate IS NOT NULL AND YEAR(inv1.ChurnDate) = '{$year}'")
            ->groupBy(DB::raw('YEAR(inv1.ChurnDate), MONTH(inv1.ChurnDate)'))
            ->select(DB::raw("COUNT(DISTINCT inv1.MSISDN) as 'Counter', YEAR(inv1.ChurnDate) as 'Year', MONTH(inv1.ChurnDate) as 'Month'"))->get();
        $churn = DB::table('m_inventory as inv1')->whereRaw("inv1.ChurnDate IS NOT NULL AND YEAR(inv1.ChurnDate) = '{$year}'")
            ->join('m_productive as prod1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
            ->groupBy(DB::raw('YEAR(inv1.ChurnDate), MONTH(inv1.ChurnDate)'))
            ->select(DB::raw("COUNT(DISTINCT prod1.MSISDN) as 'Counter', YEAR(inv1.ChurnDate) as 'Year', MONTH(inv1.ChurnDate) as 'Month'"))->get();
        if (count($churn) > 0) {
            foreach ($churn as $ivr) {
                $data["Productive Churn"][($ivr->Month - 1)] = $ivr->Counter;
            }
        }
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $data["Not Productive Churn"][($ivr->Month - 1)] = $ivr->Counter - $data["Productive Churn"][($ivr->Month - 1)];
            }
        }
        return $data;
    }

    static function getChannelShipoutSim()
    {
        $year = Input::get('year');
        $type = '';
//        $type = '2';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data2 = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Channel", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                #3g
                $act_prod = DB::table('m_inventory')->whereRaw("LastShipoutDate IS NOT NULL AND LastSubAgent IS NOT NULL AND YEAR(LastShipoutDate) = '{$year->Year}' AND LastSubAgent != '-' AND (LastStatusHist = 2 OR LastStatusHist = 4) AND (Type = 1 OR Type = 4)")
                ->groupBy(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1), YEAR(LastShipoutDate), MONTH(LastShipoutDate)"))
                ->select(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1) as 'Channel', COUNT(SerialNumber) as 'Counter', YEAR(LastShipoutDate) as 'Year', MONTH(LastShipoutDate) as 'Month'"))->get();
                #4g
                // $act = DB::table('m_inventory')->whereRaw("LastShipoutDate IS NOT NULL AND LastSubAgent IS NOT NULL AND YEAR(LastShipoutDate) = '{$year->Year}' AND LastSubAgent != '-' AND (LastStatusHist = 2 OR LastStatusHist = 4) AND Type = 4")
                // ->groupBy(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1), YEAR(LastShipoutDate), MONTH(LastShipoutDate)"))
                // ->select(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1) as 'Channel', COUNT(SerialNumber) as 'Counter', YEAR(LastShipoutDate) as 'Year', MONTH(LastShipoutDate) as 'Month'"))->get();
                if (count($act_prod) > 0) {
                    foreach ($act_prod as $ivr) {
                        if (!isset($data2[$ivr->Channel]["ALL"]))
                            $data2[$ivr->Channel]["ALL"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        $data2[$ivr->Channel]["ALL"][($ivr->Month - 1)] = $ivr->Counter;
                    }
                }
                // if (count($act) > 0) {
                //     foreach ($act as $ivr) {
                //         if (!isset($data2[$ivr->Channel]["3G"]))
                //             $data2[$ivr->Channel]["3G"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                //         if (!isset($data2[$ivr->Channel]["4G"]))
                //             $data2[$ivr->Channel]["4G"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                //         $data2[$ivr->Channel]["4G"][($ivr->Month - 1)] = $ivr->Counter + $data2[$ivr->Channel]["3G"][($ivr->Month - 1)];
                //     }
                // }
                foreach ($data2 as $key => $abc) {
                    $name = $key;
                    
                    foreach ($abc as $key2 => $a) {
                        $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                        $writer->addRow($myArr); // add a row at a time
                    }
                }
            }
            $writer->close();
             #3g
            $act_prod = DB::table('m_inventory')->whereRaw("LastShipoutDate IS NOT NULL AND LastSubAgent IS NOT NULL AND YEAR(LastShipoutDate) = '{$year->Year}' AND LastSubAgent != '-' AND (LastStatusHist = 2 OR LastStatusHist = 4) AND Type = 1")
            ->groupBy(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1), YEAR(LastShipoutDate), MONTH(LastShipoutDate)"))
            ->select(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1) as 'Channel', COUNT(SerialNumber) as 'Counter', YEAR(LastShipoutDate) as 'Year', MONTH(LastShipoutDate) as 'Month'"))->get();
            #4g
            $act = DB::table('m_inventory')->whereRaw("LastShipoutDate IS NOT NULL AND LastSubAgent IS NOT NULL AND YEAR(LastShipoutDate) = '{$year->Year}' AND LastSubAgent != '-' AND (LastStatusHist = 2 OR LastStatusHist = 4) AND Type = 4")
            ->groupBy(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1), YEAR(LastShipoutDate), MONTH(LastShipoutDate)"))
            ->select(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1) as 'Channel', COUNT(SerialNumber) as 'Counter', YEAR(LastShipoutDate) as 'Year', MONTH(LastShipoutDate) as 'Month'"))->get();
            if (count($act_prod) > 0) {
                foreach ($act_prod as $ivr) {
                    if (!isset($data[$ivr->Channel]["3G"]))
                        $data[$ivr->Channel]["3G"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    $data[$ivr->Channel]["3G"][($ivr->Month - 1)] = $ivr->Counter;
                }
            }
            if (count($act) > 0) {
                foreach ($act as $ivr) {
                    if (!isset($data[$ivr->Channel]["4G"]))
                        $data[$ivr->Channel]["4G"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    $data[$ivr->Channel]["4G"][($ivr->Month - 1)] = $ivr->Counter;
                }
            }
            return $data;
        }
        #3g
        $act_prod = DB::table('m_inventory')->whereRaw("LastShipoutDate IS NOT NULL AND LastSubAgent IS NOT NULL AND YEAR(LastShipoutDate) = '{$year}' AND LastSubAgent != '-' AND (LastStatusHist = 2 OR LastStatusHist = 4) AND Type = 1")
            ->groupBy(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1), YEAR(LastShipoutDate), MONTH(LastShipoutDate)"))
            ->select(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1) as 'Channel', COUNT(SerialNumber) as 'Counter', YEAR(LastShipoutDate) as 'Year', MONTH(LastShipoutDate) as 'Month'"))->get();
        #4g
        $act = DB::table('m_inventory')->whereRaw("LastShipoutDate IS NOT NULL AND LastSubAgent IS NOT NULL AND YEAR(LastShipoutDate) = '{$year}' AND LastSubAgent != '-' AND (LastStatusHist = 2 OR LastStatusHist = 4) AND Type = 4")
        ->groupBy(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1), YEAR(LastShipoutDate), MONTH(LastShipoutDate)"))
        ->select(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1) as 'Channel', COUNT(SerialNumber) as 'Counter', YEAR(LastShipoutDate) as 'Year', MONTH(LastShipoutDate) as 'Month'"))->get();
        if (count($act_prod) > 0) {
            foreach ($act_prod as $ivr) {
                if (!isset($data[$ivr->Channel]["3G"]))
                    $data[$ivr->Channel]["3G"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $data[$ivr->Channel]["3G"][($ivr->Month - 1)] = $ivr->Counter;
            }
        }
        if (count($act) > 0) {
            foreach ($act as $ivr) {
                if (!isset($data[$ivr->Channel]["4G"]))
                    $data[$ivr->Channel]["4G"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $data[$ivr->Channel]["4G"][($ivr->Month - 1)] = $ivr->Counter;
            }
        }
        return $data;
    }


    static function getChannelShipoutVoc()
    {
        $year = Input::get('year');
        $type = '';
//        $type = '2';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data2 = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Channel", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
                #3g
                $act_prod = DB::table('m_inventory')->whereRaw("LastShipoutDate IS NOT NULL AND LastSubAgent IS NOT NULL AND YEAR(LastShipoutDate) = '{$year->Year}' AND LastSubAgent != '-' AND (LastStatusHist = 2 OR LastStatusHist = 4) AND (Type = 2 OR Type = 3)")
                ->groupBy(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1), YEAR(LastShipoutDate), MONTH(LastShipoutDate)"))
                ->select(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1) as 'Channel', COUNT(SerialNumber) as 'Counter', YEAR(LastShipoutDate) as 'Year', MONTH(LastShipoutDate) as 'Month'"))->get();
                #4g
                // $act = DB::table('m_inventory')->whereRaw("LastShipoutDate IS NOT NULL AND LastSubAgent IS NOT NULL AND YEAR(LastShipoutDate) = '{$year->Year}' AND LastSubAgent != '-' AND (LastStatusHist = 2 OR LastStatusHist = 4) AND Type = 4")
                // ->groupBy(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1), YEAR(LastShipoutDate), MONTH(LastShipoutDate)"))
                // ->select(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1) as 'Channel', COUNT(SerialNumber) as 'Counter', YEAR(LastShipoutDate) as 'Year', MONTH(LastShipoutDate) as 'Month'"))->get();
                if (count($act_prod) > 0) {
                    foreach ($act_prod as $ivr) {
                        if (!isset($data2[$ivr->Channel]["ALL"]))
                            $data2[$ivr->Channel]["ALL"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        $data2[$ivr->Channel]["ALL"][($ivr->Month - 1)] = $ivr->Counter;
                    }
                }
                // if (count($act) > 0) {
                //     foreach ($act as $ivr) {
                //         if (!isset($data2[$ivr->Channel]["3G"]))
                //             $data2[$ivr->Channel]["3G"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                //         if (!isset($data2[$ivr->Channel]["4G"]))
                //             $data2[$ivr->Channel]["4G"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                //         $data2[$ivr->Channel]["4G"][($ivr->Month - 1)] = $ivr->Counter + $data2[$ivr->Channel]["3G"][($ivr->Month - 1)];
                //     }
                // }
                foreach ($data2 as $key => $abc) {
                    $name = $key;
                    
                    foreach ($abc as $key2 => $a) {
                        $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                        $writer->addRow($myArr); // add a row at a time
                    }
                }
            }
            $writer->close();
             #evoc
            $act_prod = DB::table('m_inventory')->whereRaw("LastShipoutDate IS NOT NULL AND LastSubAgent IS NOT NULL AND YEAR(LastShipoutDate) = '{$year->Year}' AND LastSubAgent != '-' AND (LastStatusHist = 2 OR LastStatusHist = 4) AND Type = 2")
            ->groupBy(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1), YEAR(LastShipoutDate), MONTH(LastShipoutDate)"))
            ->select(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1) as 'Channel', COUNT(SerialNumber) as 'Counter', YEAR(LastShipoutDate) as 'Year', MONTH(LastShipoutDate) as 'Month'"))->get();
            #pvoc
            $act = DB::table('m_inventory')->whereRaw("LastShipoutDate IS NOT NULL AND LastSubAgent IS NOT NULL AND YEAR(LastShipoutDate) = '{$year->Year}' AND LastSubAgent != '-' AND (LastStatusHist = 2 OR LastStatusHist = 4) AND Type = 3")
            ->groupBy(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1), YEAR(LastShipoutDate), MONTH(LastShipoutDate)"))
            ->select(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1) as 'Channel', COUNT(SerialNumber) as 'Counter', YEAR(LastShipoutDate) as 'Year', MONTH(LastShipoutDate) as 'Month'"))->get();
            if (count($act_prod) > 0) {
                foreach ($act_prod as $ivr) {
                    if (!isset($data[$ivr->Channel]["eVoc"]))
                        $data[$ivr->Channel]["eVoc"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    $data[$ivr->Channel]["eVoc"][($ivr->Month - 1)] = $ivr->Counter;
                }
            }
            if (count($act) > 0) {
                foreach ($act as $ivr) {
                    if (!isset($data[$ivr->Channel]["pVoc"]))
                        $data[$ivr->Channel]["pVoc"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    $data[$ivr->Channel]["pVoc"][($ivr->Month - 1)] = $ivr->Counter;
                }
            }
            return $data;
        }
        #evoc
        $act_prod = DB::table('m_inventory')->whereRaw("LastShipoutDate IS NOT NULL AND LastSubAgent IS NOT NULL AND YEAR(LastShipoutDate) = '{$year}' AND LastSubAgent != '-' AND (LastStatusHist = 2 OR LastStatusHist = 4) AND Type = 2")
            ->groupBy(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1), YEAR(LastShipoutDate), MONTH(LastShipoutDate)"))
            ->select(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1) as 'Channel', COUNT(SerialNumber) as 'Counter', YEAR(LastShipoutDate) as 'Year', MONTH(LastShipoutDate) as 'Month'"))->get();
        #pvoc
        $act = DB::table('m_inventory')->whereRaw("LastShipoutDate IS NOT NULL AND LastSubAgent IS NOT NULL AND YEAR(LastShipoutDate) = '{$year}' AND LastSubAgent != '-' AND (LastStatusHist = 2 OR LastStatusHist = 4) AND Type = 3")
        ->groupBy(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1), YEAR(LastShipoutDate), MONTH(LastShipoutDate)"))
        ->select(DB::raw("SUBSTRING_INDEX(`LastSubAgent`, ' ', 1) as 'Channel', COUNT(SerialNumber) as 'Counter', YEAR(LastShipoutDate) as 'Year', MONTH(LastShipoutDate) as 'Month'"))->get();
        if (count($act_prod) > 0) {
            foreach ($act_prod as $ivr) {
                if (!isset($data[$ivr->Channel]["eVoc"]))
                    $data[$ivr->Channel]["eVoc"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $data[$ivr->Channel]["eVoc"][($ivr->Month - 1)] = $ivr->Counter;
            }
        }
        if (count($act) > 0) {
            foreach ($act as $ivr) {
                if (!isset($data[$ivr->Channel]["pVoc"]))
                    $data[$ivr->Channel]["pVoc"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $data[$ivr->Channel]["pVoc"][($ivr->Month - 1)] = $ivr->Counter;
            }
        }
        return $data;
    }

    static function getChannel()
    {
        $year = Input::get('year');
        $type = '';
//        $type = '2';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data2 = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $act_prod = DB::table('m_inventory as inv1')->whereRaw("inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year->Year}' AND hist1.SubAgent != '-' AND (hist1.Status = 2 OR hist1.Status = 4)")
                    ->join('m_productive as prod1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
                    ->join('m_historymovement as hist1', 'hist1.ID', '=', 'inv1.LastStatusID')
                    ->groupBy(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1), YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)"))
                    ->select(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1) as 'Channel', COUNT(DISTINCT prod1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->get();
                $act = DB::table('m_inventory as inv1')->whereRaw("inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year->Year}' AND hist1.SubAgent != '-' AND (hist1.Status = 2 OR hist1.Status = 4)")
                    ->join('m_historymovement as hist1', 'hist1.ID', '=', 'inv1.LastStatusID')
                    ->groupBy(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1), YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)"))
                    ->select(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1) as 'Channel', COUNT(inv1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->get();

                if (count($act) > 0) {
                    foreach ($act as $ivr) {
                        if (!isset($data2["Subscriber"][$ivr->Channel]))
                            $data2["Subscriber"][$ivr->Channel] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        $data2["Subscriber"][$ivr->Channel][($ivr->Month - 1)] = $ivr->Counter;
                    }
                }
                if (count($act_prod) > 0) {
                    foreach ($act_prod as $ivr) {
                        if (!isset($data2["Productive Subscriber"][$ivr->Channel]))
                            $data2["Productive Subscriber"][$ivr->Channel] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        $data2["Productive Subscriber"][$ivr->Channel][($ivr->Month - 1)] = $ivr->Counter;
                    }
                }
                if (count($act) > 0) {
                    foreach ($act as $ivr) {
                        if (!isset($data2["Not Productive Subscriber"][$ivr->Channel]))
                            $data2["Not Productive Subscriber"][$ivr->Channel] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        if (!isset($data2["Percentage Productive"][$ivr->Channel]))
                            $data2["Percentage Productive"][$ivr->Channel] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        $data2["Percentage Productive"][$ivr->Channel][($ivr->Month - 1)] = (float)($data2["Productive Subscriber"][$ivr->Channel][($ivr->Month - 1)] / $ivr->Counter) * 100;
                        $data2["Not Productive Subscriber"][$ivr->Channel][($ivr->Month - 1)] = $ivr->Counter - $data2["Productive Subscriber"][$ivr->Channel][($ivr->Month - 1)];
                    }
                }
                foreach ($data2 as $key => $abc) {
                    $name = $key;
                    $myArr = array($name);
                    $writer->addRow($myArr);
                    $myArr = array("Channel", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                    $writer->addRow($myArr); // add a row at a time
                    foreach ($abc as $key2 => $a) {
                        if ($name === 'Percentage Productive')
                            $myArr = array($key2, number_format($a[0], 2, '.', '') . '%', number_format($a[1], 2, '.', '') . '%', number_format($a[2], 2, '.', '') . '%',
                                number_format($a[3], 2, '.', '') . '%', number_format($a[4], 2, '.', '') . '%', number_format($a[5], 2, '.', '') . '%',
                                number_format($a[6], 2, '.', '') . '%',
                                number_format($a[7], 2, '.', '') . '%', number_format($a[8], 2, '.', '') . '%',
                                number_format($a[9], 2, '.', '') . '%', number_format($a[10], 2, '.', '') . '%', number_format($a[11], 2, '.', '') . '%');
                        else
                            $myArr = array($key2, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                        $writer->addRow($myArr); // add a row at a time
                    }
                }
            }
            $writer->close();
            return $data;
        }
        $act_prod = DB::table('m_inventory as inv1')->whereRaw("inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year}' AND hist1.SubAgent != '-' AND (hist1.Status = 2 OR hist1.Status = 4)")
            ->join('m_productive as prod1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
            ->join('m_historymovement as hist1', 'hist1.ID', '=', 'inv1.LastStatusID')
            ->groupBy(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1), YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)"))
            ->select(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1) as 'Channel', COUNT(DISTINCT prod1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->get();
        $act = DB::table('m_inventory as inv1')->whereRaw("inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year}' AND hist1.SubAgent != '-' AND (hist1.Status = 2 OR hist1.Status = 4)")
            ->join('m_historymovement as hist1', 'hist1.ID', '=', 'inv1.LastStatusID')
            ->groupBy(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1), YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)"))
            ->select(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1) as 'Channel', COUNT(inv1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->get();
        if (count($act_prod) > 0) {
            foreach ($act_prod as $ivr) {
                if (!isset($data[$ivr->Channel]["Productive Subscriber"]))
                    $data[$ivr->Channel]["Productive Subscriber"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $data[$ivr->Channel]["Productive Subscriber"][($ivr->Month - 1)] = $ivr->Counter;
            }
        }
        if (count($act) > 0) {
            foreach ($act as $ivr) {
                if (!isset($data[$ivr->Channel]["Not Productive Subscriber"]))
                    $data[$ivr->Channel]["Not Productive Subscriber"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $data[$ivr->Channel]["Not Productive Subscriber"][($ivr->Month - 1)] = $ivr->Counter - $data[$ivr->Channel]["Productive Subscriber"][($ivr->Month - 1)];
            }
        }
        return $data;
    }

    static function getChannelChurn()
    {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data2 = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time

                $act_prod = DB::table('m_inventory as inv1')->whereRaw("inv1.ChurnDate IS NOT NULL AND YEAR(inv1.ChurnDate) = '{$year->Year}' AND hist1.SubAgent != '-' AND hist1.Status = 2")
                    ->join('m_productive as prod1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
                    ->join('m_historymovement as hist1', 'hist1.ID', '=', 'inv1.LastStatusID')
                    ->groupBy(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1), YEAR(inv1.ChurnDate), MONTH(inv1.ChurnDate)"))
                    ->select(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1) as 'Channel', COUNT(DISTINCT prod1.MSISDN) as 'Counter', YEAR(inv1.ChurnDate) as 'Year', MONTH(inv1.ChurnDate) as 'Month'"))->get();
                $act = DB::table('m_inventory as inv1')->whereRaw("inv1.ChurnDate IS NOT NULL AND YEAR(inv1.ChurnDate) = '{$year->Year}' AND hist1.SubAgent != '-' AND hist1.Status = 2")
                    ->join('m_historymovement as hist1', 'hist1.ID', '=', 'inv1.LastStatusID')
                    ->groupBy(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1), YEAR(inv1.ChurnDate), MONTH(inv1.ChurnDate)"))
                    ->select(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1) as 'Channel', COUNT(inv1.MSISDN) as 'Counter', YEAR(inv1.ChurnDate) as 'Year', MONTH(inv1.ChurnDate) as 'Month'"))->get();

                if (count($act) > 0) {
                    foreach ($act as $ivr) {
                        if (!isset($data2["Churn"][$ivr->Channel]))
                            $data2["Churn"][$ivr->Channel] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        $data2["Churn"][$ivr->Channel][($ivr->Month - 1)] = $ivr->Counter;
                    }
                }
                if (count($act_prod) > 0) {
                    foreach ($act_prod as $ivr) {
                        if (!isset($data2["Productive Churn"][$ivr->Channel]))
                            $data2["Productive Churn"][$ivr->Channel] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        $data2["Productive Churn"][$ivr->Channel][($ivr->Month - 1)] = $ivr->Counter;
                    }
                }
                if (count($act) > 0) {
                    foreach ($act as $ivr) {
                        if (!isset($data2["Not Productive Churn"][$ivr->Channel]))
                            $data2["Not Productive Churn"][$ivr->Channel] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        if (!isset($data2["Percentage Churn"][$ivr->Channel]))
                            $data2["Percentage Churn"][$ivr->Channel] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        $data2["Percentage Churn"][$ivr->Channel][($ivr->Month - 1)] = (float)($data2["Productive Churn"][$ivr->Channel][($ivr->Month - 1)] / $ivr->Counter) * 100;
                        $data2["Not Productive Churn"][$ivr->Channel][($ivr->Month - 1)] = $ivr->Counter - $data2["Productive Churn"][$ivr->Channel][($ivr->Month - 1)];
                    }
                }
                foreach ($data2 as $key => $abc) {
                    $name = $key;
                    $myArr = array($name);
                    $writer->addRow($myArr);
                    $myArr = array("Channel", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                    $writer->addRow($myArr); // add a row at a time
                    foreach ($abc as $key2 => $a) {
                        if ($name === 'Percentage Churn')
                            $myArr = array($key2, number_format($a[0], 2, '.', '') . '%', number_format($a[1], 2, '.', '') . '%', number_format($a[2], 2, '.', '') . '%',
                                number_format($a[3], 2, '.', '') . '%', number_format($a[4], 2, '.', '') . '%', number_format($a[5], 2, '.', '') . '%'
                            , number_format($a[6], 2, '.', '') . '%',
                                number_format($a[7], 2, '.', '') . '%', number_format($a[8], 2, '.', '') . '%',
                                number_format($a[9], 2, '.', '') . '%', number_format($a[10], 2, '.', '') . '%', number_format($a[11], 2, '.', '') . '%');
                        else
                            $myArr = array($key2, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                        $writer->addRow($myArr); // add a row at a time
                    }
                }
            }
            $writer->close();
            return $data;
        }

        $act_prod = DB::table('m_inventory as inv1')->whereRaw("inv1.ChurnDate IS NOT NULL AND YEAR(inv1.ChurnDate) = '{$year}' AND hist1.SubAgent != '-' AND hist1.Status = 2")
            ->join('m_productive as prod1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
            ->join('m_historymovement as hist1', 'hist1.ID', '=', 'inv1.LastStatusID')
            ->groupBy(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1), YEAR(inv1.ChurnDate), MONTH(inv1.ChurnDate)"))
            ->select(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1) as 'Channel', COUNT(DISTINCT prod1.MSISDN) as 'Counter', YEAR(inv1.ChurnDate) as 'Year', MONTH(inv1.ChurnDate) as 'Month'"))->get();
        $act = DB::table('m_inventory as inv1')->whereRaw("inv1.ChurnDate IS NOT NULL AND YEAR(inv1.ChurnDate) = '{$year}' AND hist1.SubAgent != '-' AND hist1.Status = 2")
            ->join('m_historymovement as hist1', 'hist1.ID', '=', 'inv1.LastStatusID')
            ->groupBy(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1), YEAR(inv1.ChurnDate), MONTH(inv1.ChurnDate)"))
            ->select(DB::raw("SUBSTRING_INDEX(`SubAgent`, ' ', 1) as 'Channel', COUNT(inv1.MSISDN) as 'Counter', YEAR(inv1.ChurnDate) as 'Year', MONTH(inv1.ChurnDate) as 'Month'"))->get();
        if (count($act_prod) > 0) {
            foreach ($act_prod as $ivr) {
                if (!isset($data[$ivr->Channel]["Productive Churn"]))
                    $data[$ivr->Channel]["Productive Churn"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $data[$ivr->Channel]["Productive Churn"][($ivr->Month - 1)] = $ivr->Counter;
            }
        }
        if (count($act) > 0) {
            foreach ($act as $ivr) {
                if (!isset($data[$ivr->Channel]["Not Productive Churn"]))
                    $data[$ivr->Channel]["Not Productive Churn"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $data[$ivr->Channel]["Not Productive Churn"][($ivr->Month - 1)] = $ivr->Counter - $data[$ivr->Channel]["Productive Churn"][($ivr->Month - 1)];
            }
        }
        return $data;
    }

    static function getSubsriber()
    {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data["Productive Subscriber"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $data["Not Productive Subscriber"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];


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
                $data["Productive Subscriber"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $data["Not Productive Subscriber"] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                $all_ivr = DB::table('m_inventory as inv1')->whereRaw("inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year->Year}'")
                    ->groupBy(DB::raw('YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)'))
                    ->select(DB::raw("COUNT(inv1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->get();
                $act = DB::table('m_inventory as inv1')->whereRaw("inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year->Year}'")
                    ->join('m_productive as prod1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
                    ->groupBy(DB::raw('YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)'))
                    ->select(DB::raw("COUNT(DISTINCT prod1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->get();
                if (count($act) > 0) {
                    foreach ($act as $ivr) {
                        $data["Productive Subscriber"][($ivr->Month - 1)] = $ivr->Counter;
                    }
                }
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $data["Not Productive Subscriber"][($ivr->Month - 1)] = $ivr->Counter - $data["Productive Subscriber"][($ivr->Month - 1)];
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
            return $data;
        }
        $all_ivr = DB::table('m_inventory as inv1')->whereRaw("inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year}'")
            ->groupBy(DB::raw('YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)'))
            ->select(DB::raw("COUNT(inv1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->get();
        $act = DB::table('m_inventory as inv1')->whereRaw("inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year}'")
            ->join('m_productive as prod1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
            ->groupBy(DB::raw('YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)'))
            ->select(DB::raw("COUNT(DISTINCT prod1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->get();
        if (count($act) > 0) {
            foreach ($act as $ivr) {
                $data["Productive Subscriber"][($ivr->Month - 1)] = $ivr->Counter;
            }
        }
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $data["Not Productive Subscriber"][($ivr->Month - 1)] = $ivr->Counter - $data["Productive Subscriber"][($ivr->Month - 1)];
            }
        }
        return $data;
    }

    static function getRemainingWarehouse(){
        $data = [];
        $wh = DB::table('m_inventory as inv1')->whereRaw("inv1.LastStatusHist IN (0,1,3)")->select(DB::raw("DISTINCT inv1.LastWarehouse as 'Warehouse'"))->get();
        if ($wh != null && count($wh) > 0) {
            foreach ($wh as $key => $value) {
                # code...
                $data[] = $value->Warehouse;
            }
        }
        return $data;
    }

    static function getRemaining()
    {
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        
        $data = [];
        $returnData = [];
        $remainWH = [];
        $wh_temp = DB::table('m_inventory as inv1')->whereRaw("inv1.LastStatusHist IN (0,1,3)")->select(DB::raw("DISTINCT inv1.LastWarehouse as 'Warehouse'"))->get();
        if ($wh_temp != null && count($wh_temp) > 0) {
            foreach ($wh_temp as $key => $value) {
                # code...
                $remainWH[] = $value->Warehouse;
            }
        }
        $all_ivr = DB::table('m_inventory as inv1')->whereRaw("inv1.LastStatusHist IN (0,1,3)")
            ->groupBy(DB::raw('inv1.LastWarehouse, inv1.Type, inv1.VocValue'))
            ->select(DB::raw("COUNT(inv1.SerialNumber) as 'Counter', inv1.LastWarehouse as 'Warehouse', inv1.Type, inv1.VocValue as 'Value'"))->get();
        if ($all_ivr != null && count($all_ivr) > 0) {
            foreach ($all_ivr as $ivr) {
                $wh = $ivr->Warehouse;
                $type = $ivr->Type;
                $value = $ivr->Value;
                $typeString = "";
                switch ($type) {
                    case 1:
                        # SIM3G
                        $typeString = "SIM-3G";
                        break;
                    case 2:
                        # EVOC
                        $typeString = "EVOC-".($value?$value:"");
                        break;
                    case 3:
                        # PVOC
                        $typeString = "PVOC-".($value?$value:"");
                        break;
                    case 4:
                        $typeString = "SIM-4G";
                        # SIM4G
                        break;
                    default:
                        # code...
                        break;
                }
                if(!isset($data[$typeString])) $data[$typeString] =[];
                $data[$typeString][$wh] = $ivr->Counter ;
            }
        }

        $countWH = count($remainWH);
        foreach ($data as $key => $value) {
            if(!isset($returnData[$key])) $returnData[$key] = array_fill(0, $countWH, "0");
            foreach ($value as $key2 => $value2) {
                $found_idx = array_search($key2, $remainWH);
                $returnData[$key][$found_idx] = isset($data[$key][$key2])?$data[$key][$key2]:"0";
            }
            
        }
        
        if (true) {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            $arrKeyString = [];
            $arrWh = [];
            $arr_temp = [];
            $counter = 0;
            foreach ($data as $key => $value) {
                $arrKeyString[] = $key;
                $counter++;
            }
            $counter2 = 0;
            foreach ($data as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    #  number_format($a[2])
                    if(!isset($arr_temp[$key2])) $arr_temp[$key2] = array_fill(0, $counter, "0");;
                    $arr_temp[$key2][$counter2] = isset($data[$key][$key2])?number_format($data[$key][$key2]):"0";
                }
                $counter2++;
            }
            array_unshift($arrKeyString , 'Type');
            $writer->addRow($arrKeyString); // add a row at a time
            
            foreach ($arr_temp as $key => $value) {
                # code...
                $tmp = [];
                $tmp[] = $key;
                $tmp = array_merge($tmp, $value); 
                $writer->addRow($tmp);
            }
            $writer->close();
        }
        return $returnData;
    }

    static function getProductive()
    {
//        $check_msisdn =[];
//        $arr_msisdn =[];
//        $all_ivr = DB::table('m_productive')
//                        ->whereRaw("(`MO` > 0 OR `MT` > 0) AND `Internet` > 0 AND `Sms` = 0 AND `Month` LIKE '09' AND `Year` LIKE '2018' AND (`m_productive`.`MSISDN` IN (SELECT m_inventory.MSISDN FROM m_inventory))")
//                        ->select(DB::raw("MSISDN"))->get();
//        foreach ($all_ivr as $msisdn) {
//            $check_msisdn[] = $msisdn->MSISDN;
//        }
//        
//        $all_ivr = DB::table('m_productive')
//                        ->whereRaw("(`MO` > 0 OR `MT` > 0) AND `Internet` > 0 AND `Sms` = 0 AND `Month` LIKE '09' AND `Year` LIKE '2018'")
//                        ->select(DB::raw("MSISDN"))->get();
//        foreach ($all_ivr as $msisdn) {
//            $arr_msisdn[] = $msisdn->MSISDN;
//        }
//        $not_found = array_diff($arr_msisdn, $check_msisdn);
//        $not_found = implode(",", $not_found);
//        dd($not_found);

        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2018';
        $data = [];


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
                //each data
                $stats = 'no service';
                $all_ivr = DB::table('m_productive as prod1')
                    ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')->whereRaw("`prod1`.Year = '{$year->Year}'")
                    ->where('prod1.MO', '0')->where('prod1.MT', '0')->where('prod1.Internet', '0')->where('prod1.Sms', '0')
                    ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
                    ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
                if (count($all_ivr) > 0) {
                    if (!isset($data[$stats]))
                        $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    foreach ($all_ivr as $ivr)
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data[$stats][$i] = $ivr->Counter;
                            }
                        }
                }

                $stats = 'Voice only';
                $all_ivr = DB::table('m_productive as prod1')
                    ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
                    ->whereRaw("`prod1`.Year = '{$year->Year}' AND (prod1.MO > 0 OR prod1.MT > 0) AND prod1.Internet = 0 AND prod1.Sms = 0")
                    ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
                    ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
                if (count($all_ivr) > 0) {
                    if (!isset($data[$stats]))
                        $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    foreach ($all_ivr as $ivr)
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data[$stats][$i] = $ivr->Counter;
                            }
                        }
                }

                $stats = 'Internet only';
                $all_ivr = DB::table('m_productive as prod1')
                    ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
                    ->whereRaw("`prod1`.Year = '{$year->Year}' AND prod1.MO = 0 AND prod1.MT = 0 AND prod1.Internet > 0 AND prod1.Sms = 0")
                    ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
                    ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
                if (count($all_ivr) > 0) {
                    if (!isset($data[$stats]))
                        $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    foreach ($all_ivr as $ivr)
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data[$stats][$i] = $ivr->Counter;
                            }
                        }
                }

                $stats = 'Voice + Internet';
                $all_ivr = DB::table('m_productive as prod1')
                    ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
                    ->whereRaw("`prod1`.Year = '{$year->Year}' AND (prod1.MO > 0 OR prod1.MT > 0) AND prod1.Internet > 0 AND prod1.Sms = 0")
                    ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
                    ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
                if (count($all_ivr) > 0) {
                    if (!isset($data[$stats]))
                        $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    foreach ($all_ivr as $ivr)
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data[$stats][$i] = $ivr->Counter;
                            }
                        }
                }

                $stats = 'SMS only';
                $all_ivr = DB::table('m_productive as prod1')
                    ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
                    ->whereRaw("`prod1`.Year = '{$year->Year}' AND prod1.MO = 0 AND prod1.MT = 0 AND prod1.Internet = 0 AND prod1.Sms > 0")
                    ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
                    ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
                if (count($all_ivr) > 0) {
                    if (!isset($data[$stats]))
                        $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    foreach ($all_ivr as $ivr)
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data[$stats][$i] = $ivr->Counter;
                            }
                        }
                }

                $stats = 'Voice + SMS';
                $all_ivr = DB::table('m_productive as prod1')
                    ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
                    ->whereRaw("`prod1`.Year = '{$year->Year}' AND (prod1.MO > 0 OR prod1.MT > 0) AND prod1.Internet = 0 AND prod1.Sms > 0")
                    ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
                    ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
                if (count($all_ivr) > 0) {
                    if (!isset($data[$stats]))
                        $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    foreach ($all_ivr as $ivr)
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data[$stats][$i] = $ivr->Counter;
                            }
                        }
                }

                $stats = 'Internet + SMS';
                $all_ivr = DB::table('m_productive as prod1')
                    ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
                    ->whereRaw("`prod1`.Year = '{$year->Year}' AND prod1.MO = 0 AND prod1.MT = 0 AND prod1.Internet > 0 AND prod1.Sms > 0")
                    ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
                    ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
                if (count($all_ivr) > 0) {
                    if (!isset($data[$stats]))
                        $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    foreach ($all_ivr as $ivr)
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data[$stats][$i] = $ivr->Counter;
                            }
                        }
                }

                $stats = 'All';
                $all_ivr = DB::table('m_productive as prod1')
                    ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
                    ->whereRaw("`prod1`.Year = '{$year->Year}' AND (prod1.MO > 0 OR prod1.MT > 0) AND prod1.Internet > 0 AND prod1.Sms > 0")
                    ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
                    ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
                if (count($all_ivr) > 0) {
                    if (!isset($data[$stats]))
                        $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                    foreach ($all_ivr as $ivr)
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data[$stats][$i] = $ivr->Counter;
                            }
                        }
                }


//                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%services%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
//                if ($all_ivr != null) {
//                    foreach ($all_ivr as $all_ivr) {
//                        $stats = 'no service';
//                        $temp_stat = $ivr->Service;
//                        if (substr($temp_stat, 0, 1) == '1') {
//                            $stats = 'Voice only';
//                        } else if (substr($temp_stat, 0, 1) == '2') {
//                            $stats = 'Internet only';
//                        } else if (substr($temp_stat, 0, 1) == '3') {
//                            $stats = 'Voice + Internet';
//                        } else if (substr($temp_stat, 0, 1) == '5') {
//                            $stats = 'SMS only';
//                        } else if (substr($temp_stat, 0, 1) == '6') {
//                            $stats = 'Voice + SMS';
//                        } else if (substr($temp_stat, 0, 1) == '7') {
//                            $stats = 'Internet + SMS';
//                        } else if (substr($temp_stat, 0, 1) == '8') {
//                            $stats = 'All';
//                        }
//                        if (!isset($data[$stats]))
//                            $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//                        for ($i = 0; $i < 12; $i++) {
//                            if ($i == $ivr->Month - 1) {
//                                $data[$stats][$i] = $ivr->Counter;
//                            }
//                        }
//                    }
//                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
            return $data;
        }
//        //each data
        $stats = 'no service';
        $all_ivr = DB::table('m_productive as prod1')
            ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')->whereRaw("`prod1`.Year = '{$year}'")
            ->where('prod1.MO', '0')->where('prod1.MT', '0')->where('prod1.Internet', '0')->where('prod1.Sms', '0')
            ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
            ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
        if (count($all_ivr) > 0) {
            if (!isset($data[$stats]))
                $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach ($all_ivr as $ivr)
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data[$stats][$i] = $ivr->Counter;
                    }
                }
        }

        $stats = 'Voice only';
        $all_ivr = DB::table('m_productive as prod1')
            ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
            ->whereRaw("`prod1`.Year = '{$year}' AND (prod1.MO > 0 OR prod1.MT > 0) AND prod1.Internet = 0 AND prod1.Sms = 0")
            ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
            ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
        if (count($all_ivr) > 0) {
            if (!isset($data[$stats]))
                $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach ($all_ivr as $ivr)
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data[$stats][$i] = $ivr->Counter;
                    }
                }
        }

        $stats = 'Internet only';
        $all_ivr = DB::table('m_productive as prod1')
            ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
            ->whereRaw("`prod1`.Year = '{$year}' AND prod1.MO = 0 AND prod1.MT = 0 AND prod1.Internet > 0 AND prod1.Sms = 0")
            ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
            ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
        if (count($all_ivr) > 0) {
            if (!isset($data[$stats]))
                $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach ($all_ivr as $ivr)
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data[$stats][$i] = $ivr->Counter;
                    }
                }
        }

        $stats = 'Voice + Internet';
        $all_ivr = DB::table('m_productive as prod1')
            ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
            ->whereRaw("`prod1`.Year = '{$year}' AND (prod1.MO > 0 OR prod1.MT > 0) AND prod1.Internet > 0 AND prod1.Sms = 0")
            ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
            ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
        if (count($all_ivr) > 0) {
            if (!isset($data[$stats]))
                $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach ($all_ivr as $ivr)
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data[$stats][$i] = $ivr->Counter;
                    }
                }
        }

        $stats = 'SMS only';
        $all_ivr = DB::table('m_productive as prod1')
            ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
            ->whereRaw("`prod1`.Year = '{$year}' AND prod1.MO = 0 AND prod1.MT = 0 AND prod1.Internet = 0 AND prod1.Sms > 0")
            ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
            ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
        if (count($all_ivr) > 0) {
            if (!isset($data[$stats]))
                $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach ($all_ivr as $ivr)
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data[$stats][$i] = $ivr->Counter;
                    }
                }
        }

        $stats = 'Voice + SMS';
        $all_ivr = DB::table('m_productive as prod1')
            ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
            ->whereRaw("`prod1`.Year = '{$year}' AND (prod1.MO > 0 OR prod1.MT > 0) AND prod1.Internet = 0 AND prod1.Sms > 0")
            ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
            ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
        if (count($all_ivr) > 0) {
            if (!isset($data[$stats]))
                $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach ($all_ivr as $ivr)
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data[$stats][$i] = $ivr->Counter;
                    }
                }
        }

        $stats = 'Internet + SMS';
        $all_ivr = DB::table('m_productive as prod1')
            ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
            ->whereRaw("`prod1`.Year = '{$year}' AND prod1.MO = 0 AND prod1.MT = 0 AND prod1.Internet > 0 AND prod1.Sms > 0")
            ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
            ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
        if (count($all_ivr) > 0) {
            if (!isset($data[$stats]))
                $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach ($all_ivr as $ivr)
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data[$stats][$i] = $ivr->Counter;
                    }
                }
        }

        $stats = 'All';
        $all_ivr = DB::table('m_productive as prod1')
            ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
            ->whereRaw("`prod1`.Year = '{$year}' AND (prod1.MO > 0 OR prod1.MT > 0) AND prod1.Internet > 0 AND prod1.Sms > 0")
            ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
            ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month"))->get();
        if (count($all_ivr) > 0) {
            if (!isset($data[$stats]))
                $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach ($all_ivr as $ivr)
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data[$stats][$i] = $ivr->Counter;
                    }
                }
        }
        return $data;
    }

    static function getSumService()
    {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];

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
//                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%_sum%\'')->get();
//                
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }

                $all_ivr = DB::table('m_productive as prod1')
                    // ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
                    ->whereRaw("`prod1`.Year = '{$year->Year}'")
                    ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
                    ->select(DB::raw("SUM(`prod1`.MO) as 'mo_Counter',SUM(`prod1`.MT) as 'mt_Counter',SUM(`prod1`.Internet) as 'int_Counter',SUM(`prod1`.Sms) as 'sms_Counter', `prod1`.Year, `prod1`.Month"))->get();
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        if (!isset($data['MT (/1000 mins)']))
                            $data['MT (/1000 mins)'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        if (!isset($data['MO (/1000 mins)']))
                            $data['MO (/1000 mins)'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        if (!isset($data['Internet (TB)']))
                            $data['Internet (TB)'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        if (!isset($data['SMS (/1000 sms)']))
                            $data['SMS (/1000 sms)'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $data['MT (/1000 mins)'][$i] = round(ceil($ivr->mt_Counter / 60) / 1000, 1);
                                $data['MO (/1000 mins)'][$i] = round(ceil($ivr->mo_Counter / 60) / 1000, 1);
                                $data['Internet (TB)'][$i] = round($ivr->int_Counter / 1000, 1);
                                $data['SMS (/1000 sms)'][$i] = round($ivr->sms_Counter / 1000, 1);
                            }
                        }
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
            return $data;
        }
        $all_ivr = DB::table('m_productive as prod1')
            // ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
            ->whereRaw("`prod1`.Year = '{$year}'")
            ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month'))
            ->select(DB::raw("SUM(`prod1`.MO) as 'mo_Counter',SUM(`prod1`.MT) as 'mt_Counter',SUM(`prod1`.Internet) as 'int_Counter',SUM(`prod1`.Sms) as 'sms_Counter', `prod1`.Year, `prod1`.Month"))->get();
//        $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%_sum%\'')->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
//                $stats = '';
//                $temp_stat = $ivr->Status;
//                $temp_counter = $ivr->Counter;
//                if (explode('_', $temp_stat)[0] == 'mt') {
//                    $stats = 'MT (/1000 mins)';
//                    $temp_counter = round(ceil($temp_counter / 60) / 1000, 1);
//                } else if (explode('_', $temp_stat)[0] == 'mo') {
//                    $stats = 'MO (/1000 mins)';
//                    $temp_counter = round(ceil($temp_counter / 60) / 1000, 1);
//                } else if (explode('_', $temp_stat)[0] == 'internet') {
//                    $stats = 'Internet (TB)';
//                    $temp_counter = round($temp_counter / 1000, 1);
//                } else if (explode('_', $temp_stat)[0] == 'sms') {
//                    $stats = 'SMS (/1000 sms)';
//                    $temp_counter = round($temp_counter / 1000, 1);
//                }


                if (!isset($data['MT (/1000 mins)']))
                    $data['MT (/1000 mins)'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                if (!isset($data['MO (/1000 mins)']))
                    $data['MO (/1000 mins)'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                if (!isset($data['Internet (TB)']))
                    $data['Internet (TB)'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                if (!isset($data['SMS (/1000 sms)']))
                    $data['SMS (/1000 sms)'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $data['MT (/1000 mins)'][$i] = round(ceil($ivr->mt_Counter / 60) / 1000, 1);
                        $data['MO (/1000 mins)'][$i] = round(ceil($ivr->mo_Counter / 60) / 1000, 1);
                        $data['Internet (TB)'][$i] = round($ivr->int_Counter / 1000, 1);
                        $data['SMS (/1000 sms)'][$i] = round($ivr->sms_Counter / 1000, 1);
                    }
                }
            }
        }
        return $data;
    }

    static function getPayload()
    {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];

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
                $all_ivr = DB::table('m_productive as prod1')
            // ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
            ->whereRaw("`prod1`.Year = '{$year->Year}' AND prod1.Internet > 0")
            ->groupBy(DB::raw('`prod1`.Month'))
            ->select(DB::raw(" SUM(Internet) as 'Counter', `prod1`.Month"))->get();
                // $all_ivr = DB::select("SELECT SUM(Internet) as 'Counter', Month  FROM m_productive WHERE Year = '{$year->Year}' GROUP BY Month");
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
                    $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
            return $data;
        }
        $all_ivr = DB::table('m_productive as prod1')
        // ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
        ->whereRaw("`prod1`.Year = '{$year}' AND prod1.Internet > 0")
        ->groupBy(DB::raw('`prod1`.Month'))
        ->select(DB::raw(" SUM(Internet) as 'Counter', `prod1`.Month"))->get();
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

    static function getPayloadPerUser()
    {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];
        $sum_internet = [];
        $count_internet = [];


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
                $all_ivr = DB::select("SELECT SUM(Internet) as 'Counter', Month FROM m_productive WHERE Year = '{$year->Year}' GROUP BY Month");
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

                $internet_user = DB::table('m_productive as prod1')
                    ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
                    ->whereRaw("`prod1`.Year = '{$year->Year}' AND prod1.Internet > 0")
                    ->groupBy(DB::raw('`prod1`.Month'))
                    ->select(DB::raw("COUNT(`prod1`.MSISDN) as 'Counter', `prod1`.Month"))->get();
                $count_internet = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                if ($internet_user != null) {
                    foreach ($internet_user as $ivr) {
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $ivr->Month - 1) {
                                $count_internet[$i] += $ivr->Counter;
                            }
                        }
                    }
                }
                $data['PayLoad Per User'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($count_internet[$i] == 0) {
                        $data['PayLoad Per User'][$i] = 0;
                    } else {
                        $data['PayLoad Per User'][$i] = round($sum_internet['Internet (TB)'][$i] / $count_internet[$i], 2);
                    }
                }
                foreach ($data as $key => $a) {
                    $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
            return $data;
        }
        $all_ivr = DB::select("SELECT SUM(Internet) as 'Counter', Month FROM m_productive WHERE Year = '{$year}' GROUP BY Month");
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

        //$internet_user = Stats::where('Year', $year)->whereRaw('Status LIKE \'%services%\'')->get();

        $internet_user = DB::table('m_productive as prod1')
            ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')
            ->whereRaw("`prod1`.Year = '{$year}' AND prod1.Internet > 0")
            ->groupBy(DB::raw('`prod1`.Month'))
            ->select(DB::raw("COUNT(`prod1`.MSISDN) as 'Counter', `prod1`.Month"))->get();
        $count_internet = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        if ($internet_user != null) {
            foreach ($internet_user as $ivr) {
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $ivr->Month - 1) {
                        $count_internet[$i] += $ivr->Counter;
                    }
                }
            }
        }
        $data['PayLoad Per User'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        for ($i = 0; $i < 12; $i++) {
            if ($count_internet[$i] == 0) {
                $data['PayLoad Per User'][$i] = 0;
            } else {
                $data['PayLoad Per User'][$i] = round($sum_internet['Internet (TB)'][$i] / $count_internet[$i], 2);
            }
        }
        return $data;
    }

    static function getInternetVsNon()
    {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];


        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                $writer->addRow($myArr); // add a row at a time
//                $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%services%\'')->get();
                $all_ivr = DB::table('m_productive as prod1')
                    ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')->whereRaw("`prod1`.Year = '{$year->Year}'")
                    ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month, `prod1`.Service'))
                    ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month, `prod1`.Service"))->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
                $data = [];
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = 'Non-Internet';
                        $temp_stat = $ivr->Service;
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
                    $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
            return $data;
        }
//        $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%services%\'')->get();
        $all_ivr = DB::table('m_productive as prod1')
            ->join('m_inventory as inv1', 'prod1.MSISDN', '=', 'inv1.MSISDN')->whereRaw("`prod1`.Year = '{$year}'")
            ->groupBy(DB::raw('`prod1`.Year, `prod1`.Month, `prod1`.Service'))
            ->select(DB::raw("COUNT(DISTINCT `prod1`.MSISDN) as 'Counter', `prod1`.Year, `prod1`.Month, `prod1`.Service"))->get();
//        $all_act = Stats::where('Year', $year)->whereRaw('Status LIKE \'%Act%\'')->get();
//        if(!count($all_ivr)){
//            $data['000'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//            $data['001'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
//        }
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = 'Non-Internet';
                $temp_stat = $ivr->Service;
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

    static function getVouchers300TopUp()
    {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];
        //1 -> evoucher; 2 -> phvoucher
        $all_ivr = [];

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
                $all_ivr = DB::select("SELECT Count(SerialNumber) as 'Counter', MONTH(TopUpDate) as Month FROM m_inventory WHERE YEAR(TopUpDate) = '{$year->Year}' AND TopUpMSISDN IS NOT NULL AND `SerialNumber` LIKE '%KR0250%' GROUP BY MONTH(TopUpDate)");
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = 'eV300';
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
                //pvoc
                $all_ivr = DB::select("SELECT Count(SerialNumber) as 'Counter', MONTH(TopUpDate) as Month FROM m_inventory WHERE YEAR(TopUpDate) = '{$year->Year}' AND TopUpMSISDN IS NOT NULL AND `SerialNumber` LIKE '%KR1850%' GROUP BY MONTH(TopUpDate)");
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = 'pV300';
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
                
                //pvoc 499
                $all_ivr = DB::select("SELECT Count(SerialNumber) as 'Counter', MONTH(TopUpDate) as Month FROM m_inventory WHERE YEAR(TopUpDate) = '{$year->Year}' AND TopUpMSISDN IS NOT NULL AND `SerialNumber` LIKE '%KR0550%' GROUP BY MONTH(TopUpDate)");
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = 'pV499';
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
                    $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
            return $data;
        }
        // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
        //$all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%topup%\'')->get();
        //evoc
        $all_ivr = DB::select("SELECT Count(SerialNumber) as 'Counter', MONTH(TopUpDate) as Month FROM m_inventory WHERE YEAR(TopUpDate) = '{$year}' AND TopUpMSISDN IS NOT NULL AND `SerialNumber` LIKE '%KR0250%' GROUP BY MONTH(TopUpDate)");
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = 'eV300';
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
        //pvoc
        $all_ivr = DB::select("SELECT Count(SerialNumber) as 'Counter', MONTH(TopUpDate) as Month FROM m_inventory WHERE YEAR(TopUpDate) = '{$year}' AND TopUpMSISDN IS NOT NULL AND `SerialNumber` LIKE '%KR1850%' GROUP BY MONTH(TopUpDate)");
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = 'pV300';
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
        //pvoc 499
        $all_ivr = DB::select("SELECT Count(SerialNumber) as 'Counter', MONTH(TopUpDate) as Month FROM m_inventory WHERE YEAR(TopUpDate) = '{$year}' AND TopUpMSISDN IS NOT NULL AND `SerialNumber` LIKE '%KR0550%' GROUP BY MONTH(TopUpDate)");
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = 'pV499';
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

    static function getSubsriberTopUp()
    {
        $year = Input::get('year');
//        $year = '2017';
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');

        $data = [];
        //1 -> evoucher; 2 -> phvoucher
        $simtopup499 = [];
        $simtopup300 = [];
        $simtopup100 = [];
        $simtopup50 = [];

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
                $simtopup499 = [];
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
                $simtopup499 = DB::table('m_inventory')
                    ->whereRaw("TopUpMSISDN IS NOT NULL AND `SerialNumber` LIKE '%KR055%' AND YEAR(TopUpDate) = '{$year->Year}'")
                    ->select(DB::raw("COUNT(DISTINCT `TopUpMSISDN`) as 'Counter',MONTH(TopUpDate)  as 'month'"))->groupBy(DB::raw("MONTH(TopUpDate)"))->get();

                if ($simtopup499 != null) {
                    foreach ($simtopup499 as $sim) {
                        if (!isset($data['Voc499']))
                            $data['Voc499'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $sim->month - 1) {
                                $data['Voc499'][$i] += $sim->Counter;
                            }
                        }
                    }
                }
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
                    $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
            return $data;
        }
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

        $simtopup499 = DB::table('m_inventory')
                ->whereRaw("TopUpMSISDN IS NOT NULL AND `SerialNumber` LIKE '%KR055%' AND YEAR(TopUpDate) = '{$year}'")
                ->select(DB::raw("COUNT(DISTINCT `TopUpMSISDN`) as 'Counter',MONTH(TopUpDate)  as 'month'"))->groupBy(DB::raw("MONTH(TopUpDate)"))->get();

        if ($simtopup499 != null) {
            foreach ($simtopup499 as $sim) {
                if (!isset($data['Voc499']))
                    $data['Voc499'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $sim->month - 1) {
                        $data['Voc499'][$i] += $sim->Counter;
                    }
                }
            }
        }
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
        return $data;
    }

    static function getVouchersTopUp()
    {
        $year = Input::get('year');
    //    $year = '2016';
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');

        $data = [];
        //1 -> evoucher; 2 -> phvoucher
        $all_ivr = [];

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
                $all_ivr = DB::select("SELECT Count(SerialNumber) as 'Counter', MONTH(TopUpDate) as Month, SUBSTRING(SerialNumber, 1, 6) as 'vocType' FROM m_inventory WHERE YEAR(TopUpDate) = '{$year->Year}' AND TopUpMSISDN IS NOT NULL AND (Type = '2' OR Type = '3') GROUP BY MONTH(TopUpDate), SUBSTRING(SerialNumber, 1, 6)");
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = '';
                        $key = $ivr->vocType;
                        if (strtoupper($key) == 'KR0250')
                            $stats = 'eV300';
                        else if (strtoupper($key) == 'KR0150')
                            $stats = 'eV100';
                        else if (strtoupper($key) == 'KR0450' || strtoupper($key) == 'KR0950')
                            $stats = 'eV50';
                        else if (strtoupper($key) == 'KR0350')
                            $stats = 'phV100';
                        else if (strtoupper($key) == 'KR0550')
                            $stats = 'phV499';
                        else if (strtoupper($key) == 'KR1850')
                            $stats = 'phV300';
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
                
                // $all_ivr = Stats::where('Year', $year->Year)->whereRaw('Status LIKE \'%topup%\'')->get();
                // if ($all_ivr != null) {
                //     foreach ($all_ivr as $ivr) {
                //         $stats = '';
                //         $temp_stat = $ivr->Status;
                //         if (substr($temp_stat, 0, 1) == '1') {
                //             $stats = 'pV100';
                //         } else if (substr($temp_stat, 0, 1) == '2') {
                //             $stats = 'pV300';
                //         } else if (substr($temp_stat, 0, 1) == '3') {
                //             $stats = 'eV50';
                //         } else if (substr($temp_stat, 0, 1) == '4') {
                //             $stats = 'eV100';
                //         } else if (substr($temp_stat, 0, 1) == '5') {
                //             $stats = 'eV300';
                //         }
                //         if ($stats != '') {
                //             if (!isset($data[$stats]))
                //                 $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                //             for ($i = 0; $i < 12; $i++) {
                //                 if ($i == $ivr->Month - 1) {
                //                     $data[$stats][$i] += $ivr->Counter;
                //                 }
                //             }
                //         }
                //     }
                // }
                foreach ($data as $key => $a) {
                    $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
            return $data;
        }
        // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
        $all_ivr = DB::select("SELECT Count(SerialNumber) as 'Counter', MONTH(TopUpDate) as Month, SUBSTRING(SerialNumber, 1, 6) as 'vocType' FROM m_inventory WHERE YEAR(TopUpDate) = '{$year}' AND TopUpMSISDN IS NOT NULL AND (Type = '2' OR Type = '3') GROUP BY MONTH(TopUpDate), SUBSTRING(SerialNumber, 1, 6)");
        if ($all_ivr != null) {
            foreach ($all_ivr as $ivr) {
                $stats = '';
                $key = $ivr->vocType;
                if (strtoupper($key) == 'KR0250')
                    $stats = 'eV300';
                else if (strtoupper($key) == 'KR0150')
                    $stats = 'eV100';
                else if (strtoupper($key) == 'KR0450' || strtoupper($key) == 'KR0950')
                    $stats = 'eV50';
                else if (strtoupper($key) == 'KR0350')
                    $stats = 'phV100';
                else if (strtoupper($key) == 'KR0550')
                    $stats = 'phV499';
                else if (strtoupper($key) == 'KR1850')
                    $stats = 'phV300';
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
        // $all_ivr = Stats::where('Year', $year)->whereRaw('Status LIKE \'%topup%\'')->get();
        // if ($all_ivr != null) {
        //     foreach ($all_ivr as $ivr) {
        //         $stats = '';
        //         $temp_stat = $ivr->Status;
        //         if (substr($temp_stat, 0, 1) == '1') {
        //             $stats = 'pV100';
        //         } else if (substr($temp_stat, 0, 1) == '2') {
        //             $stats = 'pV300';
        //         } else if (substr($temp_stat, 0, 1) == '3') {
        //             $stats = 'eV50';
        //         } else if (substr($temp_stat, 0, 1) == '4') {
        //             $stats = 'eV100';
        //         } else if (substr($temp_stat, 0, 1) == '5') {
        //             $stats = 'eV300';
        //         }
        //         if ($stats != '') {
        //             if (!isset($data[$stats]))
        //                 $data[$stats] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        //             for ($i = 0; $i < 12; $i++) {
        //                 if ($i == $ivr->Month - 1) {
        //                     $data[$stats][$i] += $ivr->Counter;
        //                 }
        //             }
        //         }
        //     }
        // }


        return $data;
    }

    static function getShipoutVoc()
    {
        $year = Input::get('year');
//        $year = '2016';
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');

        $data = [];

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                $year = $year->Year;
                $shipoutevoc50 = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                    ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '2' AND m_inventory.LastStatusHist IN ('2','4') AND (m_inventory.SerialNumber LIKE '%KR0450%' OR m_inventory.SerialNumber LIKE '%KR095%')")
                    ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month', m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();
                $shipoutevoc100 = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                    ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '2' AND m_inventory.LastStatusHist IN ('2','4') AND m_inventory.SerialNumber LIKE '%KR0150%'")
                    ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month', m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();
                $shipoutevoc300 = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                    ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '2' AND m_inventory.LastStatusHist IN ('2','4') AND m_inventory.SerialNumber LIKE '%KR0250%'")
                    ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month', m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();


                $shipoutpvoc100 = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                    ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '3' AND m_inventory.LastStatusHist IN ('2','4') AND m_inventory.SerialNumber LIKE '%KR0350%'")
                    ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month', m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();
                $shipoutpvoc300 = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                    ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '3' AND m_inventory.LastStatusHist IN ('2','4') AND m_inventory.SerialNumber LIKE '%KR1850%'")
                    ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month', m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();
                $shipoutpvoc499 = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                    ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '3' AND m_inventory.LastStatusHist IN ('2','4') AND m_inventory.SerialNumber LIKE '%KR055%'")
                    ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month', m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();

                if ($shipoutevoc50 != null) {
                    foreach ($shipoutevoc50 as $voc) {
                        if (!isset($data[$voc->LastWarehouse]['eV50']))
                            $data[$voc->LastWarehouse]['eV50'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $voc->month - 1) {
                                $data[$voc->LastWarehouse]['eV50'][$i] += $voc->Counter;
                            }
                        }
                    }
                }
                if ($shipoutevoc100 != null) {
                    foreach ($shipoutevoc100 as $voc) {
                        if (!isset($data[$voc->LastWarehouse]['eV100']))
                            $data[$voc->LastWarehouse]['eV100'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $voc->month - 1) {
                                $data[$voc->LastWarehouse]['eV100'][$i] += $voc->Counter;
                            }
                        }
                    }
                }
                if ($shipoutevoc300 != null) {
                    foreach ($shipoutevoc300 as $voc) {
                        if (!isset($data[$voc->LastWarehouse]['eV300']))
                            $data[$voc->LastWarehouse]['eV300'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $voc->month - 1) {
                                $data[$voc->LastWarehouse]['eV300'][$i] += $voc->Counter;
                            }
                        }
                    }
                }
                if ($shipoutpvoc100 != null) {
                    foreach ($shipoutpvoc100 as $voc) {
                        if (!isset($data[$voc->LastWarehouse]['pV100']))
                            $data[$voc->LastWarehouse]['pV100'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $voc->month - 1) {
                                $data[$voc->LastWarehouse]['pV100'][$i] += $voc->Counter;
                            }
                        }
                    }
                }
                if ($shipoutpvoc300 != null) {
                    foreach ($shipoutpvoc300 as $voc) {
                        if (!isset($data[$voc->LastWarehouse]['pV300']))
                            $data[$voc->LastWarehouse]['pV300'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $voc->month - 1) {
                                $data[$voc->LastWarehouse]['pV300'][$i] += $voc->Counter;
                            }
                        }
                    }
                }
                if ($shipoutpvoc499 != null) {
                    foreach ($shipoutpvoc499 as $voc) {
                        if (!isset($data[$voc->LastWarehouse]['pV499']))
                            $data[$voc->LastWarehouse]['pV499'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $voc->month - 1) {
                                $data[$voc->LastWarehouse]['pV499'][$i] += $voc->Counter;
                            }
                        }
                    }
                }
                foreach ($data as $key => $abc) {
                    $name = $key;
                    $myArr = array($name);
                    $writer->addRow($myArr);
                    $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                    $writer->addRow($myArr); // add a row at a time
                    foreach ($abc as $key2 => $a) {
                        $myArr = array($key2, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]),
                            number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                        $writer->addRow($myArr); // add a row at a time
                    }
                }
            }
            $writer->close();
            return $data;
        }
        //1 -> evoucher; 2 -> phvoucher
        // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
        $shipoutevoc50 = DB::table('m_inventory')
            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
            ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '2' AND m_inventory.LastStatusHist IN ('2','4') AND (m_inventory.SerialNumber LIKE '%KR0450%' OR m_inventory.SerialNumber LIKE '%KR095%')")
            ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month', m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();
        $shipoutevoc100 = DB::table('m_inventory')
            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
            ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '2' AND m_inventory.LastStatusHist IN ('2','4') AND m_inventory.SerialNumber LIKE '%KR0150%'")
            ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month', m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();
        $shipoutevoc300 = DB::table('m_inventory')
            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
            ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '2' AND m_inventory.LastStatusHist IN ('2','4') AND m_inventory.SerialNumber LIKE '%KR0250%'")
            ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month', m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();


        $shipoutpvoc100 = DB::table('m_inventory')
            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
            ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '3' AND m_inventory.LastStatusHist IN ('2','4') AND m_inventory.SerialNumber LIKE '%KR0350%'")
            ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month', m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();
        $shipoutpvoc300 = DB::table('m_inventory')
            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
            ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '3' AND m_inventory.LastStatusHist IN ('2','4') AND m_inventory.SerialNumber LIKE '%KR1850%'")
            ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month', m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();
        $shipoutpvoc499 = DB::table('m_inventory')
            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
            ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '3' AND m_inventory.LastStatusHist IN ('2','4') AND m_inventory.SerialNumber LIKE '%KR055%'")
            ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month', m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();

        if ($shipoutevoc50 != null) {
            foreach ($shipoutevoc50 as $voc) {
                if (!isset($data[$voc->LastWarehouse]['eV50']))
                    $data[$voc->LastWarehouse]['eV50'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $voc->month - 1) {
                        $data[$voc->LastWarehouse]['eV50'][$i] += $voc->Counter;
                    }
                }
            }
        }
        if ($shipoutevoc100 != null) {
            foreach ($shipoutevoc100 as $voc) {
                if (!isset($data[$voc->LastWarehouse]['eV100']))
                    $data[$voc->LastWarehouse]['eV100'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $voc->month - 1) {
                        $data[$voc->LastWarehouse]['eV100'][$i] += $voc->Counter;
                    }
                }
            }
        }
        if ($shipoutevoc300 != null) {
            foreach ($shipoutevoc300 as $voc) {
                if (!isset($data[$voc->LastWarehouse]['eV300']))
                    $data[$voc->LastWarehouse]['eV300'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $voc->month - 1) {
                        $data[$voc->LastWarehouse]['eV300'][$i] += $voc->Counter;
                    }
                }
            }
        }
        if ($shipoutpvoc100 != null) {
            foreach ($shipoutpvoc100 as $voc) {
                if (!isset($data[$voc->LastWarehouse]['pV100']))
                    $data[$voc->LastWarehouse]['pV100'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $voc->month - 1) {
                        $data[$voc->LastWarehouse]['pV100'][$i] += $voc->Counter;
                    }
                }
            }
        }
        if ($shipoutpvoc300 != null) {
            foreach ($shipoutpvoc300 as $voc) {
                if (!isset($data[$voc->LastWarehouse]['pV300']))
                    $data[$voc->LastWarehouse]['pV300'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $voc->month - 1) {
                        $data[$voc->LastWarehouse]['pV300'][$i] += $voc->Counter;
                    }
                }
            }
        }
        if ($shipoutpvoc499 != null) {
            foreach ($shipoutpvoc499 as $voc) {
                if (!isset($data[$voc->LastWarehouse]['pV499']))
                    $data[$voc->LastWarehouse]['pV499'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $voc->month - 1) {
                        $data[$voc->LastWarehouse]['pV499'][$i] += $voc->Counter;
                    }
                }
            }
        }


        return $data;
    }

    static function getShipoutSim()
    {
        $year = Input::get('year');
//        $year = '2016';
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');

        $data = [];
        //1 -> evoucher; 2 -> phvoucher
        $shipout4g = [];
        $shipout3g = [];

        if ($type === '2') {
            $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
            $filePath = public_path() . "/data_chart.xlsx";
            $writer->openToFile($filePath);
            foreach (DB::table('r_stats')->select('Year')->orderBy('Year', 'ASC')->distinct()->get() as $year) {
                $data = [];
                $myArr = array($year->Year);
                $writer->addRow($myArr); // add a row at a time
                // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
                $year = $year->Year;
                $shipout4g = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                    ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '4' AND m_inventory.LastStatusHist IN ('2','4')")
                    ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month' , m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();
                $shipout3g = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                    ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '1' AND m_inventory.LastStatusHist IN ('2','4')")
                    ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month' , m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();

                if ($shipout4g != null) {
                    foreach ($shipout4g as $voc) {
                        if (!isset($data[$voc->LastWarehouse]['SIM 4G']))
                            $data[$voc->LastWarehouse]['SIM 4G'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $voc->month - 1) {
                                $data[$voc->LastWarehouse]['SIM 4G'][$i] += $voc->Counter;
                            }
                        }
                    }
                }
                if ($shipout3g != null) {
                    foreach ($shipout3g as $voc) {
                        if (!isset($data[$voc->LastWarehouse]['SIM 3G']))
                            $data[$voc->LastWarehouse]['SIM 3G'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                        for ($i = 0; $i < 12; $i++) {
                            if ($i == $voc->month - 1) {
                                $data[$voc->LastWarehouse]['SIM 3G'][$i] += $voc->Counter;
                            }
                        }
                    }
                }
                foreach ($data as $key => $abc) {
                    $name = $key;
                    $myArr = array($name);
                    $writer->addRow($myArr);
                    $myArr = array("Type", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                    $writer->addRow($myArr); // add a row at a time
                    foreach ($abc as $key2 => $a) {
                        $myArr = array($key2, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]),
                            number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                        $writer->addRow($myArr); // add a row at a time
                    }
                }
            }
            $writer->close();
            return $data;
        }
        // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
        $shipout4g = DB::table('m_inventory')
            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
            ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '4' AND m_inventory.LastStatusHist IN ('2','4')")
            ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month' , m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();
        $shipout3g = DB::table('m_inventory')
            ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
            ->whereRaw("m_historymovement.Date IS NOT NULL AND YEAR(m_historymovement.Date) = '{$year}' AND m_inventory.Type = '1' AND m_inventory.LastStatusHist IN ('2','4')")
            ->select(DB::raw("COUNT(m_inventory.`SerialNumber`) as 'Counter', MONTH(m_historymovement.Date) as 'month' , m_inventory.LastWarehouse"))->groupBy(DB::raw("MONTH(m_historymovement.Date), m_inventory.LastWarehouse"))->get();

        if ($shipout4g != null) {
            foreach ($shipout4g as $voc) {
                if (!isset($data[$voc->LastWarehouse]['SIM 4G']))
                    $data[$voc->LastWarehouse]['SIM 4G'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $voc->month - 1) {
                        $data[$voc->LastWarehouse]['SIM 4G'][$i] += $voc->Counter;
                    }
                }
            }
        }
        if ($shipout3g != null) {
            foreach ($shipout3g as $voc) {
                if (!isset($data[$voc->LastWarehouse]['SIM 3G']))
                    $data[$voc->LastWarehouse]['SIM 3G'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                for ($i = 0; $i < 12; $i++) {
                    if ($i == $voc->month - 1) {
                        $data[$voc->LastWarehouse]['SIM 3G'][$i] += $voc->Counter;
                    }
                }
            }
        }


        return $data;
    }

    static function geteVouchersTopUp()
    {
        $year = Input::get('year');
//        $year = '2016';
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');

        $data = [];
        //1 -> evoucher; 2 -> phvoucher
        $all_ivr = [];

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
                $all_ivr = DB::select("SELECT Count(SerialNumber) as 'Counter', MONTH(TopUpDate) as Month, SUBSTRING(SerialNumber, 1, 6) as 'vocType' FROM m_inventory WHERE YEAR(TopUpDate) = '{$year->Year}' AND TopUpMSISDN IS NOT NULL AND (Type = '2' OR Type = '3') GROUP BY MONTH(TopUpDate), SUBSTRING(SerialNumber, 1, 6)");
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = '';
                        $key = $ivr->vocType;
                        if (strtoupper($key) == 'KR0250')
                            $stats = 'eV300';
                        else if (strtoupper($key) == 'KR0150')
                            $stats = 'eV100';
                        else if (strtoupper($key) == 'KR0450' || strtoupper($key) == 'KR0950')
                            $stats = 'eV50';
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
                    $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
            return $data;
        }
        // 1-ph100, 2-ph300, 3-ev50, 4-ev100, 5-ev300
        $all_ivr = DB::select("SELECT Count(SerialNumber) as 'Counter', MONTH(TopUpDate) as Month, SUBSTRING(SerialNumber, 1, 6) as 'vocType' FROM m_inventory WHERE YEAR(TopUpDate) = '{$year}' AND TopUpMSISDN IS NOT NULL AND (Type = '2' OR Type = '3') GROUP BY MONTH(TopUpDate), SUBSTRING(SerialNumber, 1, 6)");
                if ($all_ivr != null) {
                    foreach ($all_ivr as $ivr) {
                        $stats = '';
                        $key = $ivr->vocType;
                        if (strtoupper($key) == 'KR0250')
                            $stats = 'eV300';
                        else if (strtoupper($key) == 'KR0150')
                            $stats = 'eV100';
                        else if (strtoupper($key) == 'KR0450' || strtoupper($key) == 'KR0950')
                            $stats = 'eV50';
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

    static function getMSISDNTopUp()
    {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2017';
        $data = [];

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
                    $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
            return $data;
        }
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

    static function getChurnDetail()
    {
        $year = Input::get('year');
        $type = '';
        if (Input::get('type'))
            $type = Input::get('type');
//        $year = '2018';
//        $type = '';
        $data = [];
        $data['Churn'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $data['Active MSISDN'] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

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
                $all_ivr = DB::table('m_inventory as inv1')->whereRaw("inv1.ChurnDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year->Year}'")
                ->groupBy(DB::raw('YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)'))
                ->select(DB::raw("COUNT(DISTINCT inv1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->get();
                $all_act = DB::table('m_inventory as inv1')->whereRaw("inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year->Year}'")->groupBy(DB::raw('YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)'))
                ->select(DB::raw("COUNT(DISTINCT inv1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->get();
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
                    $myArr = array($key, number_format($a[0]), number_format($a[1]), number_format($a[2]), number_format($a[3]), number_format($a[4]), number_format($a[5]), number_format($a[6]), number_format($a[7]), number_format($a[8]), number_format($a[9]), number_format($a[10]), number_format($a[11]));
                    $writer->addRow($myArr); // add a row at a time
                }
            }
            $writer->close();
            return $data;
        }
        //1 -> evoucher; 2 -> phvoucher
        $all_ivr = DB::table('m_inventory as inv1')->whereRaw("inv1.ChurnDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year}'")
                ->groupBy(DB::raw('YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)'))
                ->select(DB::raw("COUNT(DISTINCT inv1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->get();
                $all_act = DB::table('m_inventory as inv1')->whereRaw("inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year}'")->groupBy(DB::raw('YEAR(inv1.ActivationDate), MONTH(inv1.ActivationDate)'))
                ->select(DB::raw("COUNT(DISTINCT inv1.MSISDN) as 'Counter', YEAR(inv1.ActivationDate) as 'Year', MONTH(inv1.ActivationDate) as 'Month'"))->get();
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

    static function postShipin()
    {
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

        $for_raw = "('{$sn}',0,0,0,'{$id_counter}','TELIN TAIWAN','{$type}','{$msisdn}',NULL,NULL,'TAIWAN STAR',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'shipin from uncatagorized',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
        DB::insert("INSERT INTO m_inventory VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE SerialNumber=SerialNumber;");

        $for_raw = "('{$id_counter}','{$sn}','-','TELIN TAIWAN',0,CONCAT(CURDATE(),'/SI/TST001'),NULL,'0','{$id_counter}',0,CURDATE(),'shipin from uncatagorized',CURDATE(),CURDATE(),'" . Auth::user()->ID . "','" . Auth::user()->ID . "')";
        DB::insert("INSERT INTO m_historymovement VALUES " . $for_raw . " ON DUPLICATE KEY UPDATE ID=ID;");
    }

    static function postRemark()
    {
        $sn = Input::get('sn');
        $remark = Input::get('new_remark');
//        $sn = 'FM155012310699003306';
//        $remark = 'unfound from churn file2';
        DB::Update("UPDATE `m_uncatagorized` SET `Remark` = '{$remark}' WHERE `SerialNumber` LIKE '{$sn}'");
    }

    static function postMissing()
    {
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

    static function changeFB()
    {
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

    static function postConsStat()
    {
        Session::put('conses', Input::get('cs'));
    }

    static function postNewAgent()
    {
        Session::put('NewAgent', Input::get('agent'));
    }

    static function postNewWh()
    {
        Session::put('NewWarehouse', Input::get('wh'));
    }

    static function postFormSeries()
    {
        Session::put('FormSeries', Input::get('fs'));
        Session::put('FormSeriesInv', Input::get('fs'));
    }

    static function postWarehouse()
    {
        Session::put('WarehouseInv', Input::get('wh'));
    }

    static function postST()
    {
        Session::put('ShipouttoInv', Input::get('st'));
    }

    static function delST()
    {
        Session::forget('ShipouttoInv');
    }

    static function postSemuaSN()
    {
        $sn = Input::get('sn');
        $sn = explode(",", $sn);
        Session::put('SemuaSN', $sn);
    }

static function exportExcel($filter)
    {
        ini_set('memory_limit', '6000M');
        $invs = '';
        $filter = explode(',,,', $filter);
        $typesym = '>=';
        $type = '0';
        $filenames = 'all';
        $statussym = '>=';
        $conso = '';
        $status = array('0', '1', '2', '3', '4');
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
            $status = array($filter[1]);
            if ($filter[1] == '0') {
                $filenames .= '_shipin';
            } else if ($filter[1] == '1') {
                $filenames .= '_return';
            } else if ($filter[1] == '2') {
//                $statussym = 'IN';
                $status = array('2', '4');
                $filenames .= '_shipout_cons';
            } else if ($filter[1] == '3') {
                $filenames .= '_warehouse';
            } else if ($filter[1] == '4') {
                $filenames .= '_consignment';
            }
        }

        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/inventory_" . $filenames . ".xlsx";
        $writer->openToFile($filePath);
        $myArr = array("SERIAL NUMBER", "MSISDN", "MSISDN TSEL", "BATCH", "TYPE", "LAST STATUS", "SHIPOUT TO", "SUBAGENT", "FORM SERIES", "LAST WAREHOUSE", "WAREHOUSE DATE", "SHIPOUT DATE", "SHIPOUT PRICE", "SHIPIN DATE", "SHIPIN PRICE", "REMARK", "ACTIVATION/ TOPUP DATE");
        $writer->addRow($myArr); // add a row at a time

        if ($fs == '') {
            $invs = DB::table('m_inventory as inv1')
                ->join('m_historymovement', 'inv1.LastStatusID', '=', 'm_historymovement.ID')
                ->where('inv1.Type', $typesym, $type)
                ->whereIn('m_historymovement.Status', $status)->select(DB::raw('inv1.SerialNumber, inv1.MSISDN, inv1.Type,inv1.ActivationDate,inv1.TopUpDate, m_historymovement.Status,'
                    . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                    . ' inv1.LastSubAgent as "SubAgent", '
                    . 'inv1.LastShipoutNumber as "ShipoutNumber", '
                    . 'inv1.MSISDN_TSEL as "Tsel", '
                    . 'inv1.BATCH as "Batch", '
                    . 'inv1.LastWarehouseDate as "WarehouseDate", '
                    . 'inv1.LastShipoutDate as "ShipoutDate", '
                    . 'inv1.LastShipoutPrice as "ShipoutPrice", '
                    . 'inv1.LastShipinPrice as "ShipinPrice", '
                    . 'inv1.LastShipinDate as "ShipinDate"'))->get();

            if ($wh != '') {
                $invs = DB::table('m_inventory as inv1')
                    ->join('m_historymovement', 'inv1.LastStatusID', '=', 'm_historymovement.ID')
                    ->where('inv1.Type', $typesym, $type)->where('inv1.LastWarehouse', 'LIKE', '%' . $wh . '%')
                    ->whereIn('m_historymovement.Status', $status)->select(DB::raw('inv1.SerialNumber, inv1.MSISDN,inv1.ActivationDate,inv1.TopUpDate, inv1.Type, m_historymovement.Status,'
                        . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                        . ' inv1.LastSubAgent as "SubAgent", '
                        . 'inv1.LastShipoutNumber as "ShipoutNumber", '
                        . 'inv1.MSISDN_TSEL as "Tsel", '
                        . 'inv1.BATCH as "Batch", '
                        . 'inv1.LastWarehouseDate as "WarehouseDate", '
                        . 'inv1.LastShipoutDate as "ShipoutDate", '
                        . 'inv1.LastShipoutPrice as "ShipoutPrice", '
                        . 'inv1.LastShipinPrice as "ShipinPrice", '
                        . 'inv1.LastShipinDate as "ShipinDate"'))->get();
                if ($st != '') {
                    $invs = DB::table('m_inventory as inv1')
                        ->join('m_historymovement', 'inv1.LastStatusID', '=', 'm_historymovement.ID')
                        ->where('inv1.Type', $typesym, $type)->where('inv1.LastWarehouse', 'LIKE', '%' . $wh . '%')
                        ->where('m_historymovement.SubAgent', 'LIKE', '%' . $st . '%')
                        ->whereIn('m_historymovement.Status', $status)->select(DB::raw('inv1.SerialNumber, inv1.MSISDN,inv1.ActivationDate,inv1.TopUpDate, inv1.Type, m_historymovement.Status,'
                            . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                            . ' inv1.LastSubAgent as "SubAgent", '
                            . 'inv1.LastShipoutNumber as "ShipoutNumber", '
                            . 'inv1.MSISDN_TSEL as "Tsel", '
                            . 'inv1.BATCH as "Batch", '
                            . 'inv1.LastWarehouseDate as "WarehouseDate", '
                            . 'inv1.LastShipoutDate as "ShipoutDate", '
                            . 'inv1.LastShipoutPrice as "ShipoutPrice", '
                            . 'inv1.LastShipinPrice as "ShipinPrice", '
                            . 'inv1.LastShipinDate as "ShipinDate"'))->get();
                }
            } else {
                if ($st != '') {
                    $invs = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->where('m_inventory.Type', $typesym, $type)
                        ->where('m_historymovement.SubAgent', 'LIKE', '%' . $st . '%')
                        ->whereIn('m_historymovement.Status', $status)->select(DB::raw('inv1.SerialNumber, inv1.MSISDN,inv1.ActivationDate,inv1.TopUpDate, inv1.Type, m_historymovement.Status,'
                            . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                            . ' inv1.LastSubAgent as "SubAgent", '
                            . 'inv1.LastShipoutNumber as "ShipoutNumber", '
                            . 'inv1.MSISDN_TSEL as "Tsel", '
                            . 'inv1.BATCH as "Batch", '
                            . 'inv1.LastWarehouseDate as "WarehouseDate", '
                            . 'inv1.LastShipoutDate as "ShipoutDate", '
                            . 'inv1.LastShipoutPrice as "ShipoutPrice", '
                            . 'inv1.LastShipinPrice as "ShipinPrice", '
                            . '(SELECT Date FROM m_historymovement WHERE Status = "0" AND m_historymovement.SN = inv1.SerialNumber ORDER BY m_historymovement.ID DESC LIMIT 1) as "ShipinDate"'))->get();
                }
            }
        } else if ($fs != '') {
            $invs = DB::table('m_inventory as inv1')
                ->join('m_historymovement', 'inv1.SerialNumber', '=', 'm_historymovement.SN')
                ->where('inv1.Type', $typesym, $type)
                ->whereIn('m_historymovement.Status', $status)
                ->where('m_historymovement.ShipoutNumber', 'like', '%' . $fs . '%')->select(DB::raw('inv1.SerialNumber, inv1.MSISDN,inv1.ActivationDate,inv1.TopUpDate, inv1.Type, m_historymovement.Status,'
                    . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                    . ' inv1.LastSubAgent as "SubAgent", '
                    . 'inv1.LastShipoutNumber as "ShipoutNumber", '
                    . 'inv1.MSISDN_TSEL as "Tsel", '
                    . 'inv1.BATCH as "Batch", '
                    . 'inv1.LastWarehouseDate as "WarehouseDate", '
                    . 'inv1.LastShipoutDate as "ShipoutDate", '
                    . 'inv1.LastShipoutPrice as "ShipoutPrice", '
                    . 'inv1.LastShipinPrice as "ShipinPrice", '
                    . 'inv1.LastShipinDate as "ShipinDate"'))->get();
            if ($wh != '') {
                $invs = DB::table('m_inventory as inv1')
                    ->join('m_historymovement', 'inv1.SerialNumber', '=', 'm_historymovement.SN')
                    ->where('inv1.Type', $typesym, $type)
                    ->whereIn('m_historymovement.Status', $status)->where('inv1.LastWarehouse', 'LIKE', '%' . $wh . '%')
                    ->where('m_historymovement.ShipoutNumber', 'like', '%' . $fs . '%')->select(DB::raw('inv1.SerialNumber, inv1.MSISDN, inv1.Type, m_historymovement.Status,'
                        . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                        . ' inv1.LastSubAgent as "SubAgent", '
                        . 'inv1.LastShipoutNumber as "ShipoutNumber", '
                        . 'inv1.MSISDN_TSEL as "Tsel", '
                        . 'inv1.BATCH as "Batch", '
                        . 'inv1.LastWarehouseDate as "WarehouseDate", '
                        . 'inv1.LastShipoutDate as "ShipoutDate", '
                        . 'inv1.LastShipoutPrice as "ShipoutPrice", '
                        . 'inv1.LastShipinPrice as "ShipinPrice", '
                        . 'inv1.LastShipinDate as "ShipinDate"'))->get();
                if ($st != '') {
                    $invs = DB::table('m_inventory as inv1')
                        ->join('m_historymovement', 'inv1.SerialNumber', '=', 'm_historymovement.SN')
                        ->where('inv1.Type', $typesym, $type)
                        ->whereIn('m_historymovement.Status', $status)->where('inv1.LastWarehouse', 'LIKE', '%' . $wh . '%')
                        ->where('m_historymovement.SubAgent', 'LIKE', '%' . $st . '%')
                        ->where('m_historymovement.ShipoutNumber', 'like', '%' . $fs . '%')->select(DB::raw('inv1.SerialNumber, inv1.MSISDN,inv1.ActivationDate,inv1.TopUpDate, inv1.Type, m_historymovement.Status,'
                            . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                            . ' inv1.LastSubAgent as "SubAgent", '
                            . 'inv1.LastShipoutNumber as "ShipoutNumber", '
                            . 'inv1.MSISDN_TSEL as "Tsel", '
                            . 'inv1.BATCH as "Batch", '
                            . 'inv1.LastWarehouseDate as "WarehouseDate", '
                            . 'inv1.LastShipoutDate as "ShipoutDate", '
                            . 'inv1.LastShipoutPrice as "ShipoutPrice", '
                            . 'inv1.LastShipinPrice as "ShipinPrice", '
                            . 'inv1.LastShipinDate as "ShipinDate"'))->get();
                }
            } else {
                if ($st != '') {
                    $invs = DB::table('m_inventory as inv1')
                        ->join('m_historymovement', 'inv1.SerialNumber', '=', 'm_historymovement.SN')
                        ->where('inv1.Type', $typesym, $type)
                        ->whereIn('m_historymovement.Status', $status)
                        ->where('m_historymovement.SubAgent', 'LIKE', '%' . $st . '%')
                        ->where('m_historymovement.ShipoutNumber', 'like', '%' . $fs . '%')->select(DB::raw('inv1.SerialNumber, inv1.MSISDN,inv1.ActivationDate,inv1.TopUpDate, inv1.Type, m_historymovement.Status,'
                            . ' inv1.LastStatusHist,inv1.LastWarehouse, m_historymovement.Remark,'
                            . ' inv1.LastSubAgent as "SubAgent", '
                            . 'inv1.LastShipoutNumber as "ShipoutNumber", '
                            . 'inv1.MSISDN_TSEL as "Tsel", '
                            . 'inv1.BATCH as "Batch", '
                            . 'inv1.LastWarehouseDate as "WarehouseDate", '
                            . 'inv1.LastShipoutDate as "ShipoutDate", '
                            . 'inv1.LastShipoutPrice as "ShipoutPrice", '
                            . 'inv1.LastShipinPrice as "ShipinPrice", '
                            . 'inv1.LastShipinDate as "ShipinDate"'))->get();
                }
            }
        }
        foreach ($invs as $inv) {
            $type = 'SIM 3G';
            $lastDate = $inv->ActivationDate;
            if ($inv->Type == 2) {
                $lastDate = $inv->TopUpDate;
                $type = 'eVoucher';
            } else if ($inv->Type == 3) {
                $lastDate = $inv->TopUpDate;
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
                $shipoutdt = $inv->ShipoutDate;
                $status = 'Consignment';
            }

            $shipout = '';
            $agent = '';
            $subagent = '';
            $tempcount = 0;
            if(substr($inv->SubAgent, 0, 1) === ' '){
                
            }
            if ($inv->SubAgent != '') {
                $shipout = explode(' ', ltrim($inv->SubAgent,' '));
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
            $myArr = array($inv->SerialNumber, $inv->MSISDN, $inv->Tsel, $inv->Batch, $type, $status, $agent, $inv->SubAgent, $inv->ShipoutNumber, $inv->LastWarehouse, $inv->WarehouseDate, $shipoutdt, $shipoutprice, $shipindt, $inv->ShipinPrice, $inv->Remark, $lastDate);
            $writer->addRow($myArr);
        }
        $writer->close();
        return "/inventory_" . $filenames . ".xlsx";
    }

    static function postDashboard()
    {
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

    static function exportExcelWeeklyDashboard()
    {
        $date = Input::get("argyear");
//        $date = "2018-08-02";
        $year = explode("-", $date)[0];
        $month = explode("-", $date)[1];
        $day = explode("-", $date)[2];


        if (substr($month, 0, 1) === "0") {
            $month = substr($month, 1, 1);
        }

        if ($day !== '1')
            $day = $day - 1;
        else {
            $month = $month - 1;
            if ($month % 2 == 1) //ganjil
                $day = 31;
            else if ($month == 2)
                $day = 28;
            else
                $day = 30;
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
        $all_ivr = Inventory::whereRaw("ChurnDate IS NOT NULL AND YEAR(ChurnDate) LIKE '{$last_year}' AND MONTH(ChurnDate) LIKE '{$last_month}' AND DAY(ChurnDate) >= '1' AND DAY(ChurnDate) <= '{$day}'")->select(DB::raw("COUNT(MSISDN) as Counter"))->get();
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
        $all_ivr = Inventory::whereRaw("ActivationDate IS NOT NULL AND YEAR(ActivationDate) LIKE '{$last_year}' AND MONTH(ActivationDate) LIKE '{$last_month}' AND DAY(ActivationDate) >= '1' AND DAY(ActivationDate) <= '{$day}'")->select(DB::raw("COUNT(MSISDN) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['act'][1] = $all_ivr[0]->Counter;
        else {
            $data['act'][1] = 1;
        }

        //total process
        if ($data['churn'][1] != 0)
            $data['churn'][2] = round((($data['churn'][0] - $data['churn'][1]) / $data['churn'][1]) * 100, 2);
        else
            $data['churn'][2] = round((($data['churn'][0] - $data['churn'][1]) / 1) * 100, 2);
        if ($data['act'][1] != 0)
            $data['act'][2] = round((($data['act'][0] - $data['act'][1]) / $data['act'][1]) * 100, 2);
        else
            $data['act'][2] = round((($data['act'][0] - $data['act'][1]) / 1) * 100, 2);

        $data["net"][0] = $data['act'][0] - $data['churn'][0];
        $data["net"][1] = $data['act'][1] - $data['churn'][1];
        if ($data['net'][1] !== 0)
            $data['net'][2] = round((($data['net'][0] - $data['net'][1]) / $data['net'][1]) * 100, 2);
        else
            $data['net'][2] = round((($data['net'][0] - $data['net'][1]) / 1) * 100, 2);


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

        $all_ivr = Inventory::whereRaw("TopUpDate IS NOT NULL AND YEAR(TopUpDate) LIKE '{$last_year}' AND MONTH(TopUpDate) LIKE '{$last_month}' AND DAY(TopUpDate) >= '1' AND DAY(TopUpDate) <= '{$day}' AND (`SerialNumber` LIKE '%KR1850%')")->select(DB::raw("COUNT(SerialNumber) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['PH300'][1] = $all_ivr[0]->Counter;

        $all_ivr = Inventory::whereRaw("TopUpDate IS NOT NULL AND YEAR(TopUpDate) LIKE '{$last_year}' AND MONTH(TopUpDate) LIKE '{$last_month}' AND DAY(TopUpDate) >= '1' AND DAY(TopUpDate) <= '{$day}' AND (`SerialNumber` LIKE '%KR0250%')")->select(DB::raw("COUNT(SerialNumber) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['E300'][1] = $all_ivr[0]->Counter;

        //total process
        if ($data['PH300'][1] != 0)
            $data['PH300'][2] = round((($data['PH300'][0] - $data['PH300'][1]) / $data['PH300'][1]) * 100, 2);
        else
            $data['PH300'][2] = round((($data['PH300'][0] - $data['PH300'][1]) / 1) * 100, 2);
        if ($data['E300'][1] != 0)
            $data['E300'][2] = round((($data['E300'][0] - $data['E300'][1]) / $data['E300'][1]) * 100, 2);
        else
            $data['E300'][2] = round((($data['E300'][0] - $data['E300'][1]) / 1) * 100, 2);

        $data["300NT"][0] = $data['PH300'][0] + $data['E300'][0];
        $data["300NT"][1] = $data['PH300'][1] + $data['E300'][1];
        if ($data['300NT'][1] != 0)
            $data['300NT'][2] = round((($data['300NT'][0] - $data['300NT'][1]) / $data['300NT'][1]) * 100, 2);
        else
            $data['300NT'][2] = round((($data['300NT'][0] - $data['300NT'][1]) / 1) * 100, 2);


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
        $data['90DAY'][0] = 1;
        $data['1GB'][1] = 1;
        $data['2GB'][1] = 1;
        $data['30DAY'][1] = 1;
        $data['90DAY'][1] = 1;

        $all_ivr = DB::table("m_ivr")->whereRaw("Date IS NOT NULL AND YEAR(Date) LIKE '{$year}' AND MONTH(Date) LIKE "
            . "'{$month}' AND DAY(Date) >= '1' AND DAY(Date) <= '{$day}' AND (PurchaseAmount LIKE '180' OR PurchaseAmount LIKE '360' OR PurchaseAmount LIKE '540')")
            ->select(DB::raw("COUNT(MSISDN_) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['1GB'][0] = $all_ivr[0]->Counter;

        $all_ivr = DB::table("m_ivr")->whereRaw("Date IS NOT NULL AND YEAR(Date) LIKE '{$year}' AND MONTH(Date) LIKE "
            . "'{$month}' AND DAY(Date) >= '1' AND DAY(Date) <= '{$day}' AND (PurchaseAmount LIKE '300' OR PurchaseAmount LIKE '600' OR PurchaseAmount LIKE '900')")
            ->select(DB::raw("COUNT(MSISDN_) as Counter"))->get();

        if (count($all_ivr) > 0)
            $data['2GB'][0] = $all_ivr[0]->Counter;

        $all_ivr = DB::table("m_ivr")->whereRaw("Date IS NOT NULL AND YEAR(Date) LIKE '{$year}' AND MONTH(Date) LIKE "
            . "'{$month}' AND DAY(Date) >= '1' AND DAY(Date) <= '{$day}' AND PurchaseAmount > 300 AND PurchaseAmount != 360 AND PurchaseAmount != 540 AND PurchaseAmount != 600 AND PurchaseAmount != 900 AND PurchaseAmount != 1199")
            ->select(DB::raw("COUNT(MSISDN_) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['30DAY'][0] = $all_ivr[0]->Counter;

        $all_ivr = DB::table("m_ivr")->whereRaw("Date IS NOT NULL AND YEAR(Date) LIKE '{$year}' AND MONTH(Date) LIKE "
            . "'{$month}' AND DAY(Date) >= '1' AND DAY(Date) <= '{$day}' AND PurchaseAmount LIKE '1199'")
            ->select(DB::raw("COUNT(MSISDN_) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['90DAY'][0] = $all_ivr[0]->Counter;

        $all_ivr = DB::table("m_ivr")->whereRaw("Date IS NOT NULL AND YEAR(Date) LIKE '{$last_year}' AND MONTH(Date) LIKE "
            . "'{$last_month}' AND DAY(Date) >= '1' AND DAY(Date) <= '{$day}' AND (PurchaseAmount LIKE '180' OR PurchaseAmount LIKE '360' OR PurchaseAmount LIKE '540')")
            ->select(DB::raw("COUNT(MSISDN_) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['1GB'][1] = $all_ivr[0]->Counter;

        $all_ivr = DB::table("m_ivr")->whereRaw("Date IS NOT NULL AND YEAR(Date) LIKE '{$last_year}' AND MONTH(Date) LIKE "
            . "'{$last_month}' AND DAY(Date) >= '1' AND DAY(Date) <= '{$day}' AND (PurchaseAmount LIKE '300' OR PurchaseAmount LIKE '600' OR PurchaseAmount LIKE '900')")
            ->select(DB::raw("COUNT(MSISDN_) as Counter"))->get();

        if (count($all_ivr) > 0)
            $data['2GB'][1] = $all_ivr[0]->Counter;

        $all_ivr = DB::table("m_ivr")->whereRaw("Date IS NOT NULL AND YEAR(Date) LIKE '{$last_year}' AND MONTH(Date) LIKE "
            . "'{$last_month}' AND DAY(Date) >= '1' AND DAY(Date) <= '{$day}' AND PurchaseAmount > 300 AND PurchaseAmount != 360 AND PurchaseAmount != 540 AND PurchaseAmount != 600 AND PurchaseAmount != 1199 AND PurchaseAmount != 900")
            ->select(DB::raw("COUNT(MSISDN_) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['30DAY'][1] = $all_ivr[0]->Counter;

        $all_ivr = DB::table("m_ivr")->whereRaw("Date IS NOT NULL AND YEAR(Date) LIKE '{$last_year}' AND MONTH(Date) LIKE "
            . "'{$last_month}' AND DAY(Date) >= '1' AND DAY(Date) <= '{$day}' AND PurchaseAmount LIKE '1199'")
            ->select(DB::raw("COUNT(MSISDN_) as Counter"))->get();
        if (count($all_ivr) > 0)
            $data['90DAY'][1] = $all_ivr[0]->Counter;

        //total process
        if ($data['1GB'][1] != 0)
            $data['1GB'][2] = round((($data['1GB'][0] - $data['1GB'][1]) / $data['1GB'][1]) * 100, 2);
        else
            $data['1GB'][2] = round((($data['1GB'][0] - $data['1GB'][1]) / 1) * 100, 2);
        if ($data['2GB'][1] != 0)
            $data['2GB'][2] = round((($data['2GB'][0] - $data['2GB'][1]) / $data['2GB'][1]) * 100, 2);
        else
            $data['2GB'][2] = round((($data['2GB'][0] - $data['2GB'][1]) / 1) * 100, 2);
        if ($data['30DAY'][1] != 0)
            $data['30DAY'][2] = round((($data['30DAY'][0] - $data['30DAY'][1]) / $data['30DAY'][1]) * 100, 2);
        else
            $data['30DAY'][2] = round((($data['30DAY'][0] - $data['30DAY'][1]) / 1) * 100, 2);

        if ($data['90DAY'][1] != 0)
            $data['90DAY'][2] = round((($data['90DAY'][0] - $data['90DAY'][1]) / $data['90DAY'][1]) * 100, 2);
        else
            $data['90DAY'][2] = round((($data['90DAY'][0] - $data['90DAY'][1]) / 1) * 100, 2);

        $data["INTERNET"][0] = $data['1GB'][0] + $data['2GB'][0] + $data['30DAY'][0]+ $data['90DAY'][0];
        $data["INTERNET"][1] = $data['1GB'][1] + $data['2GB'][1] + $data['30DAY'][1]+ $data['90DAY'][1];

        if ($data['INTERNET'][1] != 0)
            $data['INTERNET'][2] = round((($data['INTERNET'][0] - $data['INTERNET'][1]) / $data['INTERNET'][1]) * 100, 2);
        else
            $data['INTERNET'][2] = round((($data['INTERNET'][0] - $data['INTERNET'][1]) / 1) * 100, 2);

        $myArr = array("INTERNET", "SUBS", number_format($data["INTERNET"][0]), number_format($data["INTERNET"][1]), $data["INTERNET"][2] . '%');
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("1GB", "SUBS", number_format($data["1GB"][0]), number_format($data["1GB"][1]), $data["1GB"][2] . '%');
        $writer->addRow($myArr);
        $myArr = array("2GB", "SUBS", number_format($data["2GB"][0]), number_format($data["2GB"][1]), $data["2GB"][2] . '%');
        $writer->addRow($myArr);
        $myArr = array("30 DAYS", "SUBS", number_format($data["30DAY"][0]), number_format($data["30DAY"][1]), $data["30DAY"][2] . '%');
        $writer->addRow($myArr);
        $myArr = array("90 DAYS", "SUBS", number_format($data["90DAY"][0]), number_format($data["90DAY"][1]), $data["90DAY"][2] . '%');
        $writer->addRow($myArr);
        $writer->addRow(['']);

        $tempmonth = $month;
        $tempday = $day;
        if (strlen($month) === 1) {
            $tempmonth = "0" . $month;
        }
        $all_ivr = DB::select("SELECT SUM(prod1.MO) as 'mo', SUM(prod1.MT) as 'mt',SUM(prod1.Internet) as 'internet',SUM(prod1.Sms) as 'sms' FROM m_productive prod1 " // INNER JOIN m_inventory as inv1 ON prod1.MSISDN = inv1.MSISDN
            . "WHERE prod1.Day >= 1 AND prod1.Day <= {$tempday} AND prod1.Month LIKE '{$tempmonth}' AND prod1.Year LIKE '{$year}'");
//        $all_ivr = Stats::where('Year', $year)->where('Month', $tempmonth)->whereRaw('Status LIKE \'%_sum%\'')->get();
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
            $temp_counter = round(ceil($all_ivr[0]->mt / 60), 1);
            $data['MT'][0] = $temp_counter;
            $temp_counter = round(ceil($all_ivr[0]->mo / 60), 1);
            $data['MO'][0] = $temp_counter;
            $temp_counter = round($all_ivr[0]->internet, 1);
            $data['IT'][0] = $temp_counter;
            $temp_counter = round($all_ivr[0]->sms, 1);
            $data['SMS'][0] = $temp_counter;
//            foreach ($all_ivr as $ivr) {
//                $temp_stat = $ivr->Status;
//                $temp_counter = $ivr->Counter;
//                if (explode('_', $temp_stat)[0] == 'mt') {
//                    $temp_counter = round(ceil($temp_counter / 60), 1);
//                    $data['MT'][0] = $temp_counter;
//                } else if (explode('_', $temp_stat)[0] == 'mo') {
//                    $temp_counter = round(ceil($temp_counter / 60), 1);
//                    $data['MO'][0] = $temp_counter;
//                } else if (explode('_', $temp_stat)[0] == 'internet') {
//                    $temp_counter = round($temp_counter, 1);
//                    $data['IT'][0] = $temp_counter;
//                } else if (explode('_', $temp_stat)[0] == 'sms') {
//                    $temp_counter = round($temp_counter, 1);
//                    $data['SMS'][0] = $temp_counter;
//                }
//            }
        }
        $tempmonth = $last_month;
        if (strlen($last_month) === 1) {
            $tempmonth = "0" . $last_month;
        }
        $all_ivr = DB::select("SELECT SUM(prod1.MO) as 'mo', SUM(prod1.MT) as 'mt',SUM(prod1.Internet) as 'internet',SUM(prod1.Sms) as 'sms' FROM m_productive prod1 INNER JOIN m_inventory as inv1 ON prod1.MSISDN = inv1.MSISDN "
            . "WHERE prod1.Day >= 1 AND prod1.Day <= {$tempday} AND prod1.Month LIKE '{$tempmonth}' AND prod1.Year LIKE '{$last_year}'");
//        $all_ivr = Stats::where('Year', $year)->where('Month', $tempmonth)->whereRaw('Status LIKE \'%_sum%\'')->get();
        if ($all_ivr != null) {
            $temp_counter = round(ceil($all_ivr[0]->mt / 60), 1);
            $data['MT'][1] = $temp_counter;
            $temp_counter = round(ceil($all_ivr[0]->mo / 60), 1);
            $data['MO'][1] = $temp_counter;
            $temp_counter = round($all_ivr[0]->internet, 1);
            $data['IT'][1] = $temp_counter;
            $temp_counter = round($all_ivr[0]->sms, 1);
            $data['SMS'][1] = $temp_counter;
//            foreach ($all_ivr as $ivr) {
//                $temp_stat = $ivr->Status;
//                $temp_counter = $ivr->Counter;
//                if (explode('_', $temp_stat)[0] == 'mt') {
//                    $temp_counter = round(ceil($temp_counter / 60), 1);
//                    $data['MT'][1] = $temp_counter;
//                } else if (explode('_', $temp_stat)[0] == 'mo') {
//                    $temp_counter = round(ceil($temp_counter / 60), 1);
//                    $data['MO'][1] = $temp_counter;
//                } else if (explode('_', $temp_stat)[0] == 'internet') {
//                    $temp_counter = round($temp_counter, 1);
//                    $data['IT'][1] = $temp_counter;
//                } else if (explode('_', $temp_stat)[0] == 'sms') {
//                    $temp_counter = round($temp_counter, 1);
//                    $data['SMS'][1] = $temp_counter;
//                }
//            }
        }

        //total process
        if ($data['MT'][1] != 0)
            $data['MT'][2] = round((($data['MT'][0] - $data['MT'][1]) / $data['MT'][1]) * 100, 2);
        else
            $data['MT'][2] = round((($data['MT'][0] - $data['MT'][1]) / 1) * 100, 2);
        if ($data['MO'][1] != 0)
            $data['MO'][2] = round((($data['MO'][0] - $data['MO'][1]) / $data['MO'][1]) * 100, 2);
        else
            $data['MO'][2] = round((($data['MO'][0] - $data['MO'][1]) / 1) * 100, 2);
        if ($data['IT'][1] != 0)
            $data['IT'][2] = round((($data['IT'][0] - $data['IT'][1]) / $data['IT'][1]) * 100, 2);
        else
            $data['IT'][2] = round((($data['IT'][0] - $data['IT'][1]) / 1) * 100, 2);
        if ($data['SMS'][1] != 0)
            $data['SMS'][2] = round((($data['SMS'][0] - $data['SMS'][1]) / $data['SMS'][1]) * 100, 2);
        else
            $data['SMS'][2] = round((($data['SMS'][0] - $data['SMS'][1]) / 1) * 100, 2);

        $data["MVNO_CALL"][0] = $data['MT'][0] + $data['MO'][0];
        $data["MVNO_CALL"][1] = $data['MT'][1] + $data['MO'][1];
        if ($data['MVNO_CALL'][1] != 0)
            $data['MVNO_CALL'][2] = round((($data['MVNO_CALL'][0] - $data['MVNO_CALL'][1]) / $data['MVNO_CALL'][1]) * 100, 2);
        else
            $data['MVNO_CALL'][2] = round((($data['MVNO_CALL'][0] - $data['MVNO_CALL'][1]) / 1) * 100, 2);

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

    static function exportExcelSIM1Dashboard()
    {
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

    static function exportExcelDashboard()
    {
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

    static function postShipoutDashboard()
    {
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

    static function postShipinDashboard()
    {
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

    static function postUsageDashboard()
    {
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

    static function postUserFilterActive()
    {
        $state = Input::get("argstate");
        Session::put('UserFilterAct', $state);
    }

    static function postUserFilterv300()
    {
        $state = Input::get("argstate");
        Session::put('UserFilterv300', $state);
    }

    static function postUserFilterv100()
    {
        $state = Input::get("argstate");
        Session::put('UserFilterv100', $state);
    }

    static function postUserFilterService()
    {
        $state = Input::get("argstate");
        Session::put('UserFilterService', $state);
    }

    static function postUserResetFilter()
    {
        Session::put('UserFilterAct', 0);
        Session::put('UserFilterv300', 0);
        Session::put('UserFilterv100', 0);
        Session::put('UserFilterService', 0);
    }

    static function exportExcelSubAgentDashboard()
    {
        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/subagent_report.xlsx";
        $writer->openToFile($filePath);
        $year = Input::GET('argyear');
//        $year = '2017';
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
            ->whereRaw("hist1.SubAgent != '-' AND (hist1.Status = 2 OR hist1.Status = 4) AND inv1.Type IN ('1','4') AND inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year}'")
            ->groupBy(DB::raw('hist1.SubAgent, MONTH(inv1.ActivationDate), YEAR(inv1.ActivationDate)'))
            ->select(DB::raw("hist1.SubAgent, COUNT(inv1.MSISDN) as 'count', MONTH(inv1.ActivationDate) as 'month', YEAR(inv1.ActivationDate) as 'year'"
            ))->get();
        $topup = DB::table('m_inventory as inv1')
            ->join('m_historymovement as hist1', 'inv1.LastStatusID', '=', 'hist1.ID')
            ->join('m_inventory as inv2', 'inv2.TopUpMSISDN', '=', 'inv1.MSISDN')
            ->whereRaw("hist1.SubAgent != '-' AND (hist1.Status = 2 OR hist1.Status = 4) AND inv1.Type IN ('1','4') AND inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year}'")
            ->groupBy(DB::raw('hist1.SubAgent, MONTH(inv1.ActivationDate), YEAR(inv1.ActivationDate)'))
            ->select(DB::raw("hist1.SubAgent"
                . ", COUNT(DISTINCT inv2.TopUpMSISDN) as 'count'"
                . ", MONTH(inv1.ActivationDate) as 'month', YEAR(inv1.ActivationDate) as 'year'"
            ))->get();
        $prod = DB::table('m_inventory as inv1')
            ->join('m_historymovement as hist1', 'inv1.LastStatusID', '=', 'hist1.ID')
            ->join('m_productive as prod1', 'inv1.MSISDN', '=', 'prod1.MSISDN')
            ->whereRaw("hist1.SubAgent != '-' AND (hist1.Status = 2 OR hist1.Status = 4) AND inv1.Type IN ('1','4') AND inv1.ActivationDate IS NOT NULL AND YEAR(inv1.ActivationDate) = '{$year}'")
            ->groupBy(DB::raw("hist1.SubAgent, MONTH(inv1.ActivationDate), YEAR(inv1.ActivationDate)"))
            ->select(DB::raw("hist1.SubAgent, COUNT(DISTINCT prod1.MSISDN) as 'count', MONTH(inv1.ActivationDate) as 'month', YEAR(inv1.ActivationDate) as 'year'"
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

    static function exportExcelUserDashboard()
    {
        ini_set('memory_limit', '3000M');
        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/user_report_allyears.xlsx";
        $writer->openToFile($filePath);
//
        $myArr = array("All User  Reporting");
        $writer->addRow($myArr); // add a row at a time
        $myArr = array("MSISDN", "Name", "Activation Date", "Activation Store", "Shipout to", "Sub Agent", "Churn Date", "Voc 300 TopUp", "Voc 100 TopUp", "Voc 50 TopUp", "Last Top Up Date", "Service Usage", "Last Service Usage Date");
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
            ->whereRaw('inv1.ActivationName IS NOT NULL AND inv1.ActivationDate IS NOT NULL' . $raw_where)
            ->select(DB::raw("inv1.`ActivationDate`,inv1.`ActivationName`,inv1.`MSISDN`,inv1.`ChurnDate`,inv1.`ActivationStore`"
                . ",(SELECT COUNT(inv2.`SerialNumber`) FROM `m_inventory` as inv2 WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN` AND (inv2.`SerialNumber` LIKE '%KR0250%' OR inv2.`SerialNumber` LIKE '%KR1850%')) as 'Voc300'"
                . ",(SELECT COUNT(inv2.`SerialNumber`) FROM `m_inventory` as inv2 WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN` AND (inv2.`SerialNumber` LIKE '%KR0150%' OR inv2.`SerialNumber` LIKE '%KR0350%')) as 'Voc100'"
                . ",(SELECT COUNT(inv2.`SerialNumber`) FROM `m_inventory` as inv2 WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN` AND (inv2.`SerialNumber` LIKE '%KR0450%' OR inv2.`SerialNumber` LIKE '%KR095%')) as 'Voc50'"
                . ",(SELECT inv2.`TopUpDate` FROM `m_inventory` as inv2  WHERE inv2.`TopUpMSISDN` = inv1.`MSISDN` AND (inv2.`SerialNumber` LIKE '%KR0250%' OR inv2.`SerialNumber` LIKE '%KR1850%') AND (YEAR(inv2.`TopUpDate`) <= YEAR(inv1.ChurnDate) OR (MONTH(inv2.`TopUpDate`) <= MONTH(inv1.ChurnDate) AND YEAR(inv2.`TopUpDate`) = YEAR(inv1.ChurnDate)) OR inv1.ChurnDate IS NULL) AND (YEAR(inv2.`TopUpDate`) >= YEAR(inv1.ActivationDate) OR (MONTH(inv2.`TopUpDate`) >= MONTH(inv1.ActivationDate) AND YEAR(inv2.`TopUpDate`) = YEAR(inv1.ActivationDate))) ORDER BY inv2.`TopUpDate` DESC LIMIT 1) as 'LastDatePurchasedVoucher' "
                . ",(SELECT prod.`Service` FROM `m_productive` as prod  WHERE prod.`MSISDN` = inv1.`MSISDN` AND (prod.`Year` <= YEAR(inv1.ChurnDate) OR (prod.`Month` <= MONTH(inv1.ChurnDate) AND prod.`Year` = YEAR(inv1.ChurnDate)) OR inv1.ChurnDate IS NULL) AND (prod.`Year` >= YEAR(inv1.ActivationDate) OR (prod.`Month` >= MONTH(inv1.ActivationDate) AND prod.`Year` = YEAR(inv1.ActivationDate))) ORDER BY CONCAT(prod.`Month`,prod.`Year`) DESC LIMIT 1) as 'ServiceUsed' "
                . ",(SELECT CONCAT(prod.`Day`,prod.`Month`,prod.`Year`) FROM `m_productive` as prod  WHERE prod.`MSISDN` = inv1.`MSISDN` AND (prod.`Year` <= YEAR(inv1.ChurnDate) OR (prod.`Month` <= MONTH(inv1.ChurnDate) AND prod.`Year` = YEAR(inv1.ChurnDate)) OR inv1.ChurnDate IS NULL) AND (prod.`Year` >= YEAR(inv1.ActivationDate) OR (prod.`Month` >= MONTH(inv1.ActivationDate) AND prod.`Year` = YEAR(inv1.ActivationDate))) ORDER BY prod.`Year` DESC, prod.`Month` DESC, prod.`Day` DESC LIMIT 1) as 'LastDateUsedService'"
                . ",(SELECT SubAgent FROM `m_historymovement` as hist  WHERE hist.`SN` = inv1.`SerialNumber` AND hist.Status IN ('2','4') ORDER BY hist.ID DESC LIMIT 1) as 'Shipoutto'"
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

            $shipout = '';
            $agent = '';
            if ($data->Shipoutto != '') {
                $shipout = explode(' ', $data->Shipoutto);
            }
            if ($shipout != '') {
                $agent = $shipout[0];
            }

            $myArr = array($data->MSISDN, $data->ActivationName, $data->ActivationDate, $data->ActivationStore, $agent, $data->Shipoutto, $data->ChurnDate, number_format($data->Voc300), number_format($data->Voc100), number_format($data->Voc50), $data->LastDatePurchasedVoucher, $stats, $data->LastDateUsedService);
            $writer->addRow($myArr); // add a row at a time
        }
        $writer->close();
        return '/user_report_allyears.xlsx';
    }

    static function exportExcelShipoutDashboard()
    {
        $year = Input::get("argyear");
//        $year = "2017";
        $writer = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX); // for XLSX files
        $filePath = public_path() . "/shippout_report_" . $year . ".xlsx";
        $writer->openToFile($filePath);

        $myArr = array("All Channel Reporting");
        $writer->addRow($myArr); // add a row at a time
//        foreach (DB::table('m_historymovement')->select(DB::raw('YEAR(Date) as year'))->where('Status', 2)->orderBy('year', 'DESC')->distinct()->get() as $year) {
//            $year = $year->year;
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
            if ($channel->channel != '-' && $channel->channel != ' ' && $channel->channel != '') {
                $simshipout = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                    ->whereRaw('m_inventory.Type IN ("1")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                    ->where('m_historymovement.SubAgent', 'LIKE', $channel->channel . '%')
                    ->whereRaw('m_historymovement.Status IN ("2","4")')->where('m_historymovement.Deleted', '0')
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
            if ($channel->channel != '-' && $channel->channel != ' ' && $channel->channel != '') {
                $simshipout = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                    ->whereRaw('m_inventory.Type IN ("4")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                    ->where('m_historymovement.SubAgent', 'LIKE', $channel->channel . '%')
                    ->whereRaw('m_historymovement.Status IN ("2","4")')->where('m_historymovement.Deleted', '0')
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
            if ($channel->channel != '-' && $channel->channel != ' ' && $channel->channel != '') {
                $vocshipout = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                    ->whereRaw('m_inventory.Type IN ("2","3")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                    ->whereRaw('m_historymovement.Status IN ("2","4")')->where('m_historymovement.Deleted', '0')->where('m_inventory.SerialNumber', 'LIKE', "%KR0250%")
                    ->where('m_historymovement.SubAgent', 'LIKE', $channel->channel . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
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
            if ($channel->channel != '-' && $channel->channel != ' ' && $channel->channel != '') {
                $vocshipout = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                    ->whereRaw('m_inventory.Type IN ("2","3")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                    ->whereRaw('m_historymovement.Status IN ("2","4")')->where('m_historymovement.Deleted', '0')->where('m_inventory.SerialNumber', "LIKE", "%KR0150%")
                    ->where('m_historymovement.SubAgent', 'LIKE', $channel->channel . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
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
            if ($channel->channel != '-' && $channel->channel != ' ' && $channel->channel != '') {
                $vocshipout = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                    ->whereRaw('m_inventory.Type IN ("2","3")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                    ->whereRaw('m_historymovement.Status IN ("2","4")')->where('m_historymovement.Deleted', '0')->where('m_inventory.SerialNumber', "LIKE", "%KR0450%")
                    ->where('m_historymovement.SubAgent', 'LIKE', $channel->channel . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
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
            if ($channel->channel != '-' && $channel->channel != ' ' && $channel->channel != '') {
                $vocshipout = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                    ->whereRaw('m_inventory.Type IN ("2","3")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                    ->whereRaw('m_historymovement.Status IN ("2","4")')->where('m_historymovement.Deleted', '0')->where('m_inventory.SerialNumber', "LIKE", "%KR0350%")
                    ->where('m_historymovement.SubAgent', 'LIKE', $channel->channel . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
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
            if ($channel->channel != '-' && $channel->channel != ' ' && $channel->channel != '') {
                $vocshipout = DB::table('m_inventory')
                    ->join('m_historymovement', 'm_inventory.SerialNumber', '=', 'm_historymovement.SN')
                    ->whereRaw('m_inventory.Type IN ("2","3")')->whereRaw('YEAR(m_historymovement.Date) = ' . $year)
                    ->whereRaw('m_historymovement.Status IN ("2","4")')->where('m_historymovement.Deleted', '0')->where('m_inventory.SerialNumber', "LIKE", "%KR1850%")
                    ->where('m_historymovement.SubAgent', 'LIKE', $channel->channel . '%')->groupBy(DB::raw('MONTH(m_historymovement.Date)'))
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
//        }
        $writer->close();
        return "/shippout_report_" . $year . ".xlsx";
    }

    static function exportExcelShipinDashboard()
    {
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
                 else if (strtoupper($key) == 'KR0550')
                    $header = 'phV499';
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
                else if (strtoupper($key) == 'KR0550')
                       $header = 'phV499';
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

    static function exportExcelUsageDashboard()
    {
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
                else if (strtoupper($key) == 'KR0550')
                       $header = 'phV499';
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
                else if (strtoupper($key) == 'KR0550')
                       $header = 'phV499';
                if (!isset($data[$header])) {
                    $data[$header] = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
                }
                $data[$header][$datas->month - 1] = $datas->counter;
            }
            foreach ($data as $key => $val) {
                for ($i = 0; $i < 12; $i++) {
                    $totalvoc[$i] += $val[$i];
                }
                $myArr = array($key, number_format($val[0]), number_format($val[1]), number_format($val[2]), number_format($val[3]), number_format($val[4]), number_format($val[5]), number_format($val[6]), number_format($val[7]), number_format($val[8]), number_format($val[9]), number_format($val[10]), number_format($val[11]), number_format(array_sum($val)));;
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

    static function exportExcel2($filter)
    {
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

    static function getPDFShipout()
    {
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
        $wh = '';
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
                            src:url("fonts/traditional.ttf");
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
                        <div style="width:430px; height:20px;float:left; display: inline-block;"></div> 
                        <div style="width:200px; height:20px;float:left; display: inline-block;">倉 庫 別:' . $wh . ' (紅白電訊)</div>
                    </div>
                    <div style="width:102%; text-align:center;height:20px; border-left: 1px solid;  border-right: 1px solid; border-bottom: 1px solid;">
                        <div style="width:100px; height:20px;float:left; display: inline-block; border-right: 1px solid;">產品編號</div>
                        <div style="width:300px; height:20px;float:left; display: inline-block; border-right: 1px solid;">產品名稱</div>
                        <div style="width:70px; height:20px;float:left; display: inline-block; border-right: 1px solid;">數 量</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block; border-right: 1px solid;">訂價/單價</div>
                        <div style="width:115px; height:20px;float:left; display: inline-block;">合計</div>
                    </div>';
//        Session::get('fabiao')
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
                    
                </body>
            </html>';
        return PDF::load($html, 'F4', 'portrait')->show(Session::get('sn'));
    }

    static function getPDFCons()
    {
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
                    if (count($firstid) > 0)
                        $first[$temp_count] = $firstid->SerialNumber;
                    else
                        $first[$temp_count] = ' ';
                    $lastid = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                        ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '1')
                        ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                        ->orderBy('m_inventory.SerialNumber', 'desc')
                        ->first();
                    if (count($firstid) > 0)
                        $last[$temp_count] = $lastid->SerialNumber;
                    else
                        $last[$temp_count] = ' ';
//                    $last[$temp_count] = $lastid->SerialNumber;
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
                    if (count($firstid) > 0)
                        $first[$temp_count] = $firstid->SerialNumber;
                    else
                        $first[$temp_count] = ' ';
                    $lastid = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                        ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '2')
                        ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                        ->orderBy('m_inventory.SerialNumber', 'desc')
                        ->first();
                    if (count($firstid) > 0)
                        $last[$temp_count] = $lastid->SerialNumber;
                    else
                        $last[$temp_count] = ' ';
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
                    if (count($firstid) > 0)
                        $first[$temp_count] = $firstid->SerialNumber;
                    else
                        $first[$temp_count] = ' ';
                    $lastid = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                        ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '3')
                        ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                        ->orderBy('m_inventory.SerialNumber', 'desc')
                        ->first();
                    if (count($firstid) > 0)
                        $last[$temp_count] = $lastid->SerialNumber;
                    else
                        $last[$temp_count] = ' ';
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
                    if (count($firstid) > 0)
                        $first[$temp_count] = $firstid->SerialNumber;
                    else
                        $first[$temp_count] = ' ';
                    $lastid = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->where('m_historymovement.Status', '!=', '2')->where('m_historymovement.ShipoutNumber', 'LIKE', '%' . Session::get('snCons') . '%')
                        ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '4')
                        ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                        ->orderBy('m_inventory.SerialNumber', 'desc')
                        ->first();
                    if (count($firstid) > 0)
                        $last[$temp_count] = $lastid->SerialNumber;
                    else
                        $last[$temp_count] = ' ';
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
                            src:url("fonts/traditional.ttf");
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
                        <div style="width:430px; height:20px;float:left; display: inline-block;"></div>
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
                $html .= '';
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
        $html .= '<div style="width:102%; height:20px; border-left: 1px solid;  border-right: 1px solid; border-top: 1px solid;">
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
                    
                    
                </body>
            </html>';
        return PDF::load($html, 'F4', 'portrait')->show(Session::get('sn'));
    }

    static function getPDFInv()
    {
        $type = ['', '', '', ''];
        $count = ['', '', '', ''];
        $first = ['', '', '', ''];
        $last = ['', '', '', ''];
        $temp_count = 0;
        $subtotal = 0;
        $date_item = '';
        $remark_item = '';
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
            $remark_item = $inv_item->Remark;
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
                            src:url("fonts/traditional.ttf");
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
                        <div style="width:430px; height:20px;float:left; display: inline-block;"></div>
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
                        <div style="width:377px; height:20px;float:left; display: inline-block; border-right: 1px solid;">'.$remark_item.'</div>
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
                    
                </body>
            </html>';
        return PDF::load($html, 'F4', 'portrait')->show(Session::get('sn'));
    }

    static function getPDFReturn()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sn = Input::get('sn');
            $date = Input::get('date');
            $arr = Input::get('array_SN');
            $rem = Input::get('remark');

            Session::put('sn_ret', $sn);
            Session::put('date_ret', $date);
            Session::put('arr_ret', $arr);
            Session::put('arr_rem', $rem);

            return $arr;
        }
        $type = ['', '', '', ''];
        $count = ['', '', '', ''];
        $first = ['', '', '', ''];
        $last = ['', '', '', ''];
        $temp_count = 0;
        $subtotal = 0;
        $wh = '';
        $status = ['2','4'];
        if (Session::has('arr_ret')) {
            $arr_sn = Session::get('arr_ret');
            $arr_sn = explode(',', $arr_sn);
//            $arr_sn = str_replace(',', "','", $arr_sn);
            $alltype = DB::table('m_inventory')
                ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                ->whereIn('m_historymovement.Status', $status)
                ->where('m_inventory.Missing', '0')->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                ->select('m_inventory.Type')
                ->distinct()->get();
            $wh = DB::table('m_inventory')
                ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                ->whereIn('m_historymovement.Status', $status)
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
                        ->whereIn('m_historymovement.Status', $status)
                        ->where('m_inventory.Missing', '0')
                        ->where('m_inventory.Type', '1')->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                        ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                        ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->whereIn('m_historymovement.Status', $status)
                        ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '1')
                        ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                        ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                        ->orderBy('m_inventory.SerialNumber', 'asc')
                        ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->whereIn('m_historymovement.Status', $status)
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
                        ->whereIn('m_historymovement.Status', $status)
                        ->where('m_inventory.Missing', '0')
                        ->where('m_inventory.Type', '2')
                        ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                        ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                        ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->whereIn('m_historymovement.Status', $status)
                        ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '2')
                        ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                        ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                        ->orderBy('m_inventory.SerialNumber', 'asc')
                        ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->whereIn('m_historymovement.Status', $status)
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
                        ->whereIn('m_historymovement.Status', $status)
                        ->where('m_inventory.Missing', '0')
                        ->where('m_inventory.Type', '3')
                        ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                        ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                        ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->whereIn('m_historymovement.Status', $status)
                        ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '3')
                        ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                        ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                        ->orderBy('m_inventory.SerialNumber', 'asc')
                        ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->whereIn('m_historymovement.Status', $status)
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
                        ->whereIn('m_historymovement.Status', $status)
                        ->where('m_inventory.Missing', '0')
                        ->where('m_inventory.Type', '4')
                        ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                        ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                        ->count();
                    $count[$temp_count] = $counters;
                    $firstid = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->whereIn('m_historymovement.Status', $status)
                        ->where('m_inventory.Missing', '0')->where('m_inventory.Type', '4')
                        ->whereIn('m_inventory.SerialNumber', $arr_sn)->orWhereIn('m_inventory.MSISDN', $arr_sn)
                        ->select('m_inventory.SerialNumber', 'm_inventory.Type')
                        ->orderBy('m_inventory.SerialNumber', 'asc')
                        ->first();
                    $first[$temp_count] = $firstid->SerialNumber;
                    $lastid = DB::table('m_inventory')
                        ->join('m_historymovement', 'm_inventory.LastStatusID', '=', 'm_historymovement.ID')
                        ->whereIn('m_historymovement.Status', $status)
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
//        if (Session::get('price') == 0) {
//            $wh = 'TELIN TAIWAN';
//        }
        $html = '
            <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    <style>
                        @font-face {
                            font-family:traditional;
                            src:url("fonts/traditional.ttf");
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
                        <div style="width:430px; height:20px;float:left; display: inline-block;"></div>
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
                        <div style="width:377px; height:20px;float:left; display: inline-block; border-right: 1px solid;">' . Session::get('arr_rem') . '</div>
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
                    
                    
                </body>
            </html>';
        return PDF::load($html, 'F4', 'portrait')->show(Session::get('sn'));
    }

    static function postAvail()
    {
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

    static function getSN($msi)
    {
        return Inventory::where('MSISDN', $msi)->first()->SerialNumber;
    }

    static function getShipout()
    {
        $lasthist = History::where('SN', 'like', '%' . Input::get('sn') . '%')->where('Status', '2')->orWhere('Status', '4')->orderBy('ID', 'desc')->first()->SubAgent;
        return $lasthist;
    }

    static function getFS()
    {
        $lasthist['FS'] = DB::table('m_historymovement')->select('ShipoutNumber')->distinct()->get();
        $lasthist['WH'] = DB::table('m_historymovement')->select('Warehouse')->distinct()->get();
        Session::forget('FormSeriesInv');
        Session::forget('WarehouseInv');
        Session::forget('ShipouttoInv');
        return $lasthist;
    }

    static function postFS()
    {
        $sns = Input::get('sns');
        $lasthist['FS'] = DB::table('m_historymovement')->where('SN', 'LIKE', '%' . $sns . '%')->select('ShipoutNumber')->distinct()->get();
        $lasthist['WH'] = DB::table('m_historymovement')->select('Warehouse')->distinct()->get();
        Session::forget('FormSeriesInv');
        Session::forget('WarehouseInv');
        Session::forget('ShipouttoInv');
        return $lasthist;
    }

    static function getForm()
    {
        $lastnum = History::where('ShipoutNumber', 'like', '%' . Input::get('sn') . '%')->orderBy('ID', 'desc')->first();
        if ($lastnum != null) {
            $lastnum = $lastnum->ShipoutNumber;
            $lastnum = substr($lastnum, -3, 3);
        } else {
            $lastnum = 0;
        }
        $lastnum++;
        $lastnum = sprintf("%'03d", $lastnum);
        return $lastnum;
    }

    static function inventoryDataBackup($filter)
    {
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
                    'formatter' => function ($d, $row) {
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
                    'formatter' => function ($d, $row) {
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
                array('db' => 'SerialNumber', 'dt' => 10, 'formatter' => function ($d, $row) {
                    $data = Inventory::find($d);
                    if ($data->Missing == 0) {
                        $hist = History::find($data->LastStatusID);
                        $disa = '';
                        if ($hist->Status == 2 || Auth::user()->Position > 1) {
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

            //require(URL::asset('ssp.class.php'));
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
        } else {
            $columns = array(
                array('db' => 'SerialNumber', 'dt' => 0),
                array(
                    'db' => 'Type',
                    'dt' => 1,
                    'formatter' => function ($d, $row) {
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
                    'formatter' => function ($d, $row) {
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
                array('db' => 'SerialNumber', 'dt' => 10, 'formatter' => function ($d, $row) {
                    $data = Inventory::find($d);
                    if ($data->Missing == 0) {
                        $hist = History::find($data->LastStatusID);
                        $disa = '';
                        if ($hist->Status == 2 || Auth::user()->Position > 1) {
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

            //require(URL::asset('ssp.class.php'));
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

    static function inventoryDataBackupUncat()
    {
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

        //require(URL::asset('ssp.class.php'));
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "";
        $join = '';
        echo json_encode(
            SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

    static function inventoryDataBackupAnomalies()
    {
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

        //require(URL::asset('ssp.class.php'));
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "";
        $join = '';
        echo json_encode(
            SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

    static function delInv()
    {
        Session::forget('temp_inv_start');
        Session::forget('temp_inv_end');
        Session::forget('temp_inv_price');
        Session::forget('temp_inv_arr');
        Session::forget('temp_inv_qty');
    }

    static function addInv()
    {
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

    static function inventoryDataBackupOut($id)
    {
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
                'formatter' => function ($d, $row) {
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
                'formatter' => function ($d, $row) {
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
            array('db' => 'SerialNumber', 'dt' => 7, 'formatter' => function ($d, $row) {
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

        //require(URL::asset('ssp.class.php'));
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "m_inventory.`SerialNumber` IN (" . Session::get('temp_inv_arr') . ")";
        $extraCondition .= " && m_historymovement.Status " . $string_temp;
        $extraCondition .= " && m_inventory.Missing " . $string_miss;
        $join = ' INNER JOIN m_historymovement on m_historymovement.ID = m_inventory.LastStatusID';

        echo json_encode(
            SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

    static function inventoryDataBackupDashboard()
    {
        $table = 'r_shipout_subagent';
        $primaryKey = 'r_shipout_subagent`.`SubAgent';
        $columns = array(
            array('db' => 'SubAgent', 'dt' => 0),
            array('db' => '1Shipout', 'dt' => 1, 'formatter' => function ($d, $row) {
                return number_format($d);
            }
            ),
            array('db' => '1Active', 'dt' => 2, 'formatter' => function ($d, $row) {
                return number_format($d);
            }),
            array('db' => '1ApfReturn', 'dt' => 3, 'formatter' => function ($d, $row) {
                return number_format($d);
            }),
            array('db' => '2Shipout', 'dt' => 4, 'formatter' => function ($d, $row) {
                return number_format($d);
            }),
            array('db' => '2Active', 'dt' => 5, 'formatter' => function ($d, $row) {
                return number_format($d);
            }),
            array('db' => '2ApfReturn', 'dt' => 6, 'formatter' => function ($d, $row) {
                return number_format($d);
            }),
            array('db' => '3Shipout', 'dt' => 7, 'formatter' => function ($d, $row) {
                return number_format($d);
            }),
            array('db' => '3Active', 'dt' => 8, 'formatter' => function ($d, $row) {
                return number_format($d);
            }),
            array('db' => '3ApfReturn', 'dt' => 9, 'formatter' => function ($d, $row) {
                return number_format($d);
            })
        );

        $sql_details = getConnection();

        //require(URL::asset('ssp.class.php'));
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "";
        $join = '';

        echo json_encode(
            SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

    static function inventoryDataBackupCons($id)
    {
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
                $hist = History::where('SN', $inv->SerialNumber)->where('LastStatus', 4)->where('Status', 4)->orderBy('ID', 'desc')->first();
                if ($hist != null)
                    $series = $hist->ShipoutNumber;
            }
            if ($serial == '0') {
                $serial = '';
            } else {
                $hist = History::where('SN', 'like', '%' . $serial . '%')->where('LastStatus', 4)->where('Status', 4)->orderBy('ID', 'desc')->first();
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
        if ($series == '' || $series == null) {
            $series = "THISISRANDOMSTRING";
        }
        Session::put('snCons', $series);
        $table = 'm_inventory';
        $primaryKey = 'm_inventory`.`SerialNumber';
        $columns = array(
            array('db' => 'SerialNumber', 'dt' => 0),
            array(
                'db' => 'Type',
                'dt' => 1,
                'formatter' => function ($d, $row) {
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
                'formatter' => function ($d, $row) {
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
            array('db' => 'SerialNumber', 'dt' => 6, 'formatter' => function ($d, $row) {
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
        //require(URL::asset('ssp.class.php'));
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "m_historymovement.Status " . $string_temp;
        $extraCondition .= " && m_historymovement.ShipoutNumber LIKE '%" . $series . "%'";
        $extraCondition .= " && m_inventory.Missing " . $string_miss;
        $join = ' INNER JOIN m_historymovement on m_historymovement.ID = m_inventory.LastStatusID';

        echo json_encode(
            SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

    static function inventoryDataBackupWare($id)
    {
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
                'formatter' => function ($d, $row) {
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
                'formatter' => function ($d, $row) {
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

        //require(URL::asset('ssp.class.php'));
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "m_inventory.`SerialNumber` >= '" . $startid . "' && " . "m_inventory.`SerialNumber` <= '" . $endid . "'";
        $extraCondition .= " && m_historymovement.Status " . $string_temp;
        $join = ' INNER JOIN m_historymovement on m_historymovement.ID = m_inventory.LastStatusID';

        echo json_encode(
            SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

    static function inventoryDataBackupReturn($id)
    {
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
                'formatter' => function ($d, $row) {
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
                'formatter' => function ($d, $row) {
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

        //require(URL::asset('ssp.class.php'));
//        $ID_CLIENT_VALUE = Auth::user()->CompanyInternalID;
        $extraCondition = "(m_inventory.`SerialNumber` IN ('" . $array . "')";
        $extraCondition .= " OR m_inventory.`MSISDN` IN ('" . $array . "'))";
        $extraCondition .= " AND m_historymovement.Status IN ('" . $string_temp . "')";
        $join = ' INNER JOIN m_historymovement on m_historymovement.ID = m_inventory.LastStatusID';

        echo json_encode(
            SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $extraCondition, $join));
    }

}
