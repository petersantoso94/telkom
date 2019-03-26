<?php

function getConnection() {
    return array(
        'user' => 'telkom',
        'pass' => 'akatsuki7',
        'db' => 'telkom',
        'host' => 'database'
    );
}

// My common functions
function myEscapeStringData($dataArray) {
//implode
    $tampString = '"' . implode('", "', $dataArray) . '"';
    $tampString = str_replace(',","', '!#44!","', $tampString);
    $tampString = str_replace(',"}"', '!#44!"}"', $tampString);
    $tampString = str_replace(',"', '!#4344!', $tampString);
    $tampString = str_replace(",", "!#44!", $tampString);
    $tampString = str_replace('!#4344!', ',"', $tampString);
    $tampString = str_replace("\/", "!#47!", $tampString);
    $tampString = str_replace('\"', "!#34!", $tampString);
    $tampString = str_replace("'", "!#39!", $tampString);
    return $tampString;
}

function myEscapeStringDataLevel($dataArray) {
//implode
    $tampString = '"{' . implode(',', $dataArray) . '}"';
    $tampString = str_replace(',","', '!#44!","', $tampString);
    $tampString = str_replace(',"}"', '!#44!"}"', $tampString);
    $tampString = str_replace(',"', '!#4344!', $tampString);
    $tampString = str_replace(",", "!#44!", $tampString);
    $tampString = str_replace('!#4344!', ',"', $tampString);
    $tampString = str_replace("/", "!#47!", $tampString);
    $tampString = str_replace('"{"', '!#1111!', $tampString);
    $tampString = str_replace('"}"', '!#2222!', $tampString);
    $tampString = str_replace('":"', '!#3333!', $tampString);
    $tampString = str_replace('","', '!#4444!', $tampString);
    $tampString = str_replace('"', "!#34!", $tampString);
    $tampString = str_replace('!#1111!', '"{"', $tampString);
    $tampString = str_replace('!#2222!', '"}"', $tampString);
    $tampString = str_replace('!#3333!', '":"', $tampString);
    $tampString = str_replace('!#4444!', '","', $tampString);
    $tampString = str_replace("'", "!#39!", $tampString);
    return $tampString;
}

function myEncryptNumeric($text) {
    $text = crypt($text, '$1$g$');
    $text = str_replace("~", "21", $text);
    $text = str_replace("`", "22", $text);
    $text = str_replace("!", "23", $text);
    $text = str_replace("@", "24", $text);
    $text = str_replace("#", "25", $text);
    $text = str_replace("$", "26", $text);
    $text = str_replace("%", "27", $text);
    $text = str_replace("^", "28", $text);
    $text = str_replace("&", "29", $text);
    $text = str_replace("*", "33", $text);
    $text = str_replace("(", "43", $text);
    $text = str_replace(")", "53", $text);
    $text = str_replace("-", "63", $text);
    $text = str_replace("_", "73", $text);
    $text = str_replace("+", "83", $text);
    $text = str_replace("=", "93", $text);
    $text = str_replace("{", "30", $text);
    $text = str_replace("[", "31", $text);
    $text = str_replace("]", "32", $text);
    $text = str_replace("}", "34", $text);
    $text = str_replace("|", "35", $text);
    $text = str_replace(";", "36", $text);
    $text = str_replace(":", "37", $text);
    $text = str_replace('"', "38", $text);
    $text = str_replace("'", "39", $text);
    $text = str_replace("<", "40", $text);
    $text = str_replace(">", "41", $text);
    $text = str_replace(",", "42", $text);
    $text = str_replace(".", "44", $text);
    $text = str_replace("?", "45", $text);
    $text = str_replace("/", "46", $text);
    return $text;
}

function myCheckIsEmpty($text) {
    $textSplit = explode(';', $text);
    $countCek = 0;
    foreach ($textSplit as $data) {
        $count = 0;
        if ($data == 'Company') {
            $count += Company::where('InternalID', "<>", '-1')->count();
        }
        if ($data == 'Coa1') {
            $count += Coa1::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'Coa') {
            $count += Coa::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'Slip') {
            $count += Slip::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'Department') {
            $count += Department::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'DepartmentDefault') {
            $count += Department::where('CompanyInternalID', Auth::user()->CompanyInternalID)->where('Default', '1')->count();
        }
        if ($data == 'Coa5') {
            $count += Coa5::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'Currency') {
            $count += Currency::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'GroupDepreciation') {
            $count += GroupDepreciation::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'Depreciation') {
            $count += DepreciationHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'InventoryType') {
            $count += InventoryType::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'Inventory') {
            $count += Inventory::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'InventoryUom') {
            $count += InventoryUom::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'Warehouse') {
            $count += Warehouse::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'Customer') {
            $count += Coa6::where('CompanyInternalID', Auth::user()->CompanyInternalID)->where('Type', 'c')->count();
        }
        if ($data == 'Supplier') {
            $count += Coa6::where('CompanyInternalID', Auth::user()->CompanyInternalID)->where('Type', 's')->count();
        }
        if ($data == 'Sales') {
            $count += SalesHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'SalesOrder') {
            $count += SalesOrderHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'SalesReturn') {
            $count += SalesReturnHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'Purchase') {
            $count += PurchaseHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'PurchaseOrder') {
            $count += PurchaseOrderHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'PurchaseReturn') {
            $count += PurchaseReturnHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'Journal') {
            $count += JournalHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'SalesPurchase') {
            $count += SalesHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
            $count += PurchaseHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'Default') {
            $count += Default_s::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'DefaultCurrency') {
            $count += Currency::where('CompanyInternalID', Auth::user()->CompanyInternalID)->where('Default', 1)->count();
        }
        if ($data == 'Variety') {
            $count += Variety::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'Brand') {
            $count += Brand::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'Group') {
            $count += Group::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($data == 'Quotation') {
            $count += QuotationHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
        }
        if ($count > 0) {
            $countCek++;
        }
    }
    if ($countCek == count($textSplit)) {
        return false;
    } else {
        return true;
    }
}

function convertNol($numeric) {
    if ($numeric == 0) {
        return '-';
    }
    return $numeric;
}

function checkSalesAdd($idSO) {
    /*
      $salesOrderHeader = SalesOrderHeader::find($idSO);
      $tampJum = 0;
      $detailSales = $salesOrderHeader->SalesOrderDetail;
      foreach ($detailSales as $data) {
      $sumSalesReturn = SalesReturnHeader::getSumReturnOrder($data->InventoryInternalID, $data->SalesOrderInternalID, $data->InternalID);
      if ($sumSalesReturn == '') {
      $sumSalesReturn = '0';
      }
      $tampJum+= ($data->Qty) - SalesAddHeader::getSumSales($data->InventoryInternalID, $data->SalesOrderInternalID, $data->InternalID) + $sumSalesReturn;
      }
      if ($tampJum > 0) {
      return TRUE;
      }
      return FALSE;
     * 
     */



    $salesOrderHeader = SalesOrderHeader::find($idSO);
    $tampJum = 0;
    $detailSales = $salesOrderHeader->SalesOrderDetail;
    //sudah benar
    foreach ($detailSales as $data) {
        if ($data->SalesOrderParcelInternalID == 0) {
            $sumSalesReturn = SalesReturnHeader::getSumReturnOrder($data->InventoryInternalID, $data->SalesOrderInternalID, $data->InternalID);
            if ($sumSalesReturn == '') {
                $sumSalesReturn = '0';
            }
            $tampJum+= ($data->Qty) - SalesAddHeader::getSumSales($data->InventoryInternalID, $data->SalesOrderInternalID, $data->InternalID) + $sumSalesReturn;
        }
    }
    //parcel
    foreach (SalesOrderParcel::where("SalesOrderInternalID", $salesOrderHeader->InternalID)->where("CompanyInternalID", Auth::user()->Company->InternalID)->get() as $data) {
        $sumSalesReturn = SalesReturnHeader::getSumReturnOrderParcel($data->ParcelInternalID, $data->SalesOrderInternalID, $data->InternalID);
        if ($sumSalesReturn == '') {
            $sumSalesReturn = '0';
        }
        $tampJum+= ($data->Qty) - SalesAddHeader::getSumSalesParcel($data->ParcelInternalID, $data->SalesOrderInternalID, $data->InternalID) + $sumSalesReturn;
    }
    if ($tampJum > 0) {
        return TRUE;
    }
    return FALSE;
}

function checkSalesReturn($idSI) {
//    $salesHeader = SalesHeader::find($idSI);
//    $tampJum = 0;
//    $detailReturn = $salesHeader->SalesDetail;
//    foreach ($detailReturn as $data) {
//        $tampJum+= ($data->Qty) - SalesReturnHeader::getSumReturn($data->InventoryInternalID, $salesHeader->SalesID, $data->InternalID);
//    }
//    if ($tampJum > 0) {
//        return TRUE;
//    }
//    return FALSE;
    $salesHeader = SalesHeader::find($idSI);
    $tampJum = 0;
    $detailReturn = $salesHeader->SalesDetail;
    foreach ($detailReturn as $data) {
        if ($data->SalesParcelInternalID == 0) {
            $tampJum+= ($data->Qty) - SalesReturnHeader::getSumReturn($data->InventoryInternalID, $salesHeader->SalesID, $data->InternalID);
        }
    }
    foreach (SalesAddParcel::where("SalesInternalID", $salesHeader->InternalID)->where("CompanyInternalID", Auth::user()->Company->InternalID)->get() as $data) {
        $tampJum+= ($data->Qty) - SalesReturnHeader::getSumReturnParcel($data->ParcelInternalID, $salesHeader->SalesHeaderID, $data->InternalID);
    }
    if ($tampJum > 0) {
        return TRUE;
    }
    return FALSE;
}

function checkPurchaseAdd($idPO) {
    $purchaseOrderHeader = PurchaseOrderHeader::find($idPO);
    $tampJum = 0;
    $detailPurchase = $purchaseOrderHeader->PurchaseOrderDetail;
    foreach ($detailPurchase as $data) {
        $sumPurchaseReturn = PurchaseReturnHeader::getSumReturnOrder($data->InventoryInternalID, $data->PurchaseOrderInternalID, $data->InternalID);
        if ($sumPurchaseReturn == '') {
            $sumPurchaseReturn = '0';
        }
        $tampJum+= ($data->Qty) - PurchaseAddHeader::getSum($data->InventoryInternalID, $data->PurchaseOrderInternalID, $data->InternalID) + $sumPurchaseReturn;
    }
    if ($tampJum > 0) {
        return TRUE;
    }
    return FALSE;
}

function checkQuotation($idQuotation) {
//jika quotationnya sudah ada di tabel mn maka tidak keluar di pilihan
    $tampJum = 0;
    $countQuotationSales = QuotationSales::where("QuotationInternalID", $idQuotation)->count();
    $tampJum = $countQuotationSales;
    if ($tampJum == 0) {
        return TRUE;
    }
    return FALSE;
}

function checkPurchaseReturn($idPI) {
    $purchaseHeader = PurchaseHeader::find($idPI);
    $tampJum = 0;
    $detailReturn = $purchaseHeader->PurchaseDetail;
    foreach ($detailReturn as $data) {
        $tampJum+= ($data->Qty) - PurchaseReturnHeader::getSumReturn($data->InventoryInternalID, $purchaseHeader->PurchaseID, $data->InternalID);
    }
    if ($tampJum > 0) {
        return TRUE;
    }
    return FALSE;
}

function countMemorySuperAdmin($company) {
    $purchaseO = PurchaseOrderHeader::where('CompanyInternalID', $company)->count();
    $purchaseI = PurchaseHeader::where('CompanyInternalID', $company)->count();
    $purchaseR = PurchaseReturnHeader::where('CompanyInternalID', $company)->count();
    $salesO = SalesOrderHeader::where('CompanyInternalID', $company)->count();
    $salesI = SalesHeader::where('CompanyInternalID', $company)->count();
    $salesR = SalesReturnHeader::where('CompanyInternalID', $company)->count();
//Transaction
    $totalTransaction = $purchaseO + $purchaseI + $purchaseR + $salesO + $salesI + $salesR;
    $totalTransaction *= 20;

    $user = User::where('CompanyInternalID', $company)->where('Status', 1)->count();
//user
    $totalUser = $user * 10000;
//Total
    $inventory = Inventory::where('CompanyInternalID', Auth::user()->CompanyInternalID)->where('Picture', '!=', '')->count();
    $totalInventory = $inventory * 120;
    $total = ($totalTransaction + $totalUser + $totalInventory) / 1000;

    return $total . 'Mb';
}

function countMemory() {
    $purchaseO = PurchaseOrderHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
    $purchaseI = PurchaseHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
    $purchaseR = PurchaseReturnHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
    $salesO = SalesOrderHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
    $salesI = SalesHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
    $salesR = SalesReturnHeader::where('CompanyInternalID', Auth::user()->CompanyInternalID)->count();
//Transaction
    $totalTransaction = $purchaseO + $purchaseI + $purchaseR + $salesO + $salesI + $salesR;
    $totalTransaction *= 20;

    $user = User::where('CompanyInternalID', Auth::user()->CompanyInternalID)->where('Status', 1)->count();
//user
    $totalUser = $user * 10000;

    $inventory = Inventory::where('CompanyInternalID', Auth::user()->CompanyInternalID)->where('Picture', '!=', '')->count();
    $totalInventory = $inventory * 120;
//Total
    $total = ($totalTransaction + $totalUser + $totalInventory) / 1000;

    return $total;
}

function checkModul($idModul) {
    $modul = Modul::where('ModulID', $idModul)->pluck('InternalID');
    if (!Auth::check()) {
        return false;
    }
    $package = Auth::user()->Company->PackageInternalID;
    $result = PackageDetail::where('PackageInternalID', $package)
                    ->where('ModulInternalID', $modul)->count();
    if ($result == 0) {
        return false;
    }
    return true;
}

function checkMatrix($idMatrix) {
    $matrix = Matrix::where('MatrixID', $idMatrix)->pluck('InternalID');
    $result = UserDetail::where('UserInternalID', Auth::user()->InternalID)
                    ->where('MatrixInternalID', $matrix)->count();
    if ($result == 0) {
        return false;
    }
    return true;
}

function checkTypeMatrix($type) {
    $result = UserDetail::join('m_matrix', 'm_matrix.InternalID', '=', 'MatrixInternalID')
                    ->where('UserInternalID', Auth::user()->InternalID)
                    ->where('Type', $type)->count();
    if ($result == 0) {
        return false;
    }
    return true;
}

function myEncryptEmail($text) {
    $text = str_replace("=", "EEE93PPP", $text);
    $text = str_replace("?", "CCC45TTT", $text);
    $text = str_replace("/", "EEE46CCC", $text);
    return $text;
}

function myDecryptEmail($text) {
    $text = str_replace("EEE93PPP", "=", $text);
    $text = str_replace("CCC45TTT", "?", $text);
    $text = str_replace("EEE46CCC", "/", $text);
    return $text;
}

function findAlfaNumeric($cari, $arr, $rand) {
    $ke = -1;
    for ($i = 0; $i < count($arr); $i++) {
        if ($cari == $arr[$i]) {
            $ke = $i;
        }
    }
    if ($ke == -1) {
        return '-1';
    }
    $index = 0;
    if (($ke + $rand) > 75) {
        $index = ($ke + $rand) - 76;
    } else {
        $index = $ke + $rand;
    }
    return $index;
}

function myEncryptJavaScript($text, $rand) {
// $text berupa array
    $arr = array(
        'h', '!', '3', 'z', 'a', 'g', '8', '%', '9', 'k',
        'y', '@', 'b', 'f', '-', 'o', 'v', 'q', 'd', '7',
        '0', 'i', '^', '6', '#', '5', 'c', 'j', '*', 'e',
        '&', '(', 'm', 'l', '4', ')', '=', 'p', 'u', '_',
        's', '2', 't', '+', 'r', '$', 'x', 'w', 'n', '1',
        'H', 'Z', 'A', 'G', 'K', 'Y', 'B', 'F', 'O', 'V',
        'Q', 'D', 'I', 'C', 'J', 'E', 'M', 'L', 'P', 'U',
        'S', 'T', 'R', 'X', 'W', 'N'
    );
    $hasil = "";
    for ($i = 0; $i < count($text); $i++) {
        if ($i < (count($text) - 1)) {
            $hasil .= $text[$i] . ',';
        } else {
            $hasil .= $text[$i];
        }
    }

    $kal = "";
    for ($i = 0; $i < strlen($hasil); $i++) {
        $ke = findAlfaNumeric($hasil[$i], $arr, $rand);
        if ($ke != '-1') {
            $kal .= $arr[$ke];
        } else {
            $kal .= $hasil[$i];
        }
    }
    return '[' . $kal . ']';
}

function myEncryptJavaScriptText($text, $rand) {
// $text berupa string
    $arr = array(
        'h', '!', '3', 'z', 'a', 'g', '8', '%', '9', 'k',
        'y', '@', 'b', 'f', '-', 'o', 'v', 'q', 'd', '7',
        '0', 'i', '^', '6', '#', '5', 'c', 'j', '*', 'e',
        '&', '(', 'm', 'l', '4', ')', '=', 'p', 'u', '_',
        's', '2', 't', '+', 'r', '$', 'x', 'w', 'n', '1',
        'H', 'Z', 'A', 'G', 'K', 'Y', 'B', 'F', 'O', 'V',
        'Q', 'D', 'I', 'C', 'J', 'E', 'M', 'L', 'P', 'U',
        'S', 'T', 'R', 'X', 'W', 'N'
    );

    $kal = "";
    for ($i = 0; $i < strlen($text); $i++) {
        $ke = findAlfaNumeric($text[$i], $arr, $rand);
        if ($ke != '-1') {
            $kal .= $arr[$ke];
        } else {
            $kal .= $text[$i];
        }
    }
    return $kal;
}

function splitSearchValue($text) {
    $tamp = "%";
    for ($i = 0; $i < strlen($text); $i++) {
        if ($text[$i] == " ")
            $tamp.= "%";
        else
            $tamp.= $text[$i];
    }
    return $tamp . "%";
}

function getEndStockInventory($InventoryInternalID) {
    $html = "";
    $totalPieces = 0;
    $tampUom = '';
    $initialStockTamp = Inventory::find($InventoryInternalID)->InitialQuantity;
    foreach (InventoryUom::where('InventoryInternalID', $InventoryInternalID)->get() as $data) {
        $pembelian = PurchaseDetail::
                join('t_purchase_header', 't_purchase_header.InternalID', '=', 't_purchase_detail.PurchaseInternalID')
                ->where('t_purchase_header.CompanyInternalID', Auth::user()->Company->InternalID)
                ->where('t_purchase_detail.InventoryInternalID', $data->InventoryInternalID)
                ->where('t_purchase_detail.UomInternalID', $data->UomInternalID)
                ->sum('Qty');
        $penjualan = SalesDetail::
                join('t_sales_header', 't_sales_header.InternalID', '=', 't_sales_detail.SalesInternalID')
                ->where('t_sales_header.CompanyInternalID', Auth::user()->Company->InternalID)
                ->where('t_sales_detail.InventoryInternalID', $data->InventoryInternalID)
                ->where('t_sales_detail.UomInternalID', $data->UomInternalID)
                ->sum('Qty');
        $Rpembelian = PurchaseReturnDetail::
                join('t_purchasereturn_header', 't_purchasereturn_header.InternalID', '=', 't_purchasereturn_detail.PurchaseReturnInternalID')
                ->where('t_purchasereturn_header.CompanyInternalID', Auth::user()->Company->InternalID)
                ->where('t_purchasereturn_detail.InventoryInternalID', $data->InventoryInternalID)
                ->where('t_purchasereturn_detail.UomInternalID', $data->UomInternalID)
                ->sum('Qty');
        $Rpenjualan = SalesReturnDetail::
                join('t_salesreturn_header', 't_salesreturn_header.InternalID', '=', 't_salesreturn_detail.SalesReturnInternalID')
                ->where('t_salesreturn_header.CompanyInternalID', Auth::user()->Company->InternalID)
                ->where('t_salesreturn_detail.InventoryInternalID', $data->InventoryInternalID)
                ->where('t_salesreturn_detail.UomInternalID', $data->UomInternalID)
                ->sum('Qty');
        $Min = MemoInDetail::
                join('t_memoin_header', 't_memoin_header.InternalID', '=', 't_memoin_detail.MemoInInternalID')
                ->where('t_memoin_header.CompanyInternalID', Auth::user()->Company->InternalID)
                ->where('t_memoin_detail.InventoryInternalID', $data->InventoryInternalID)
                ->where('t_memoin_detail.UomInternalID', $data->UomInternalID)
                ->sum('Qty');
        $Mout = MemoOutDetail::
                join('t_memoout_header', 't_memoout_header.InternalID', '=', 't_memoout_detail.MemoOutInternalID')
                ->where('t_memoout_header.CompanyInternalID', Auth::user()->Company->InternalID)
                ->where('t_memoout_detail.InventoryInternalID', $data->InventoryInternalID)
                ->where('t_memoout_detail.UomInternalID', $data->UomInternalID)
                ->sum('Qty');
        $Tin = TransferDetail::
                join('t_transfer_header', 't_transfer_header.InternalID', '=', 't_transfer_detail.TransferInternalID')
                ->where('t_transfer_header.CompanyInternalID', Auth::user()->Company->InternalID)
                ->where('t_transfer_detail.InventoryInternalID', $data->InventoryInternalID)
                ->where('t_transfer_detail.UomInternalID', $data->UomInternalID)
                ->sum('Qty');
        $Tout = TransferDetail::
                join('t_transfer_header', 't_transfer_header.InternalID', '=', 't_transfer_detail.TransferInternalID')
                ->where('t_transfer_header.CompanyInternalID', Auth::user()->Company->InternalID)
                ->where('t_transfer_detail.InventoryInternalID', $data->InventoryInternalID)
                ->where('t_transfer_detail.UomInternalID', $data->UomInternalID)
                ->sum('Qty');
        $ConvIn = Convertion::
                join('m_inventory_uom', 'm_inventory_uom.InternalID', '=', 'h_convertion.InventoryUomInternalID2')
                ->where('m_inventory_uom.CompanyInternalID', Auth::user()->Company->InternalID)
                ->where('h_convertion.InventoryUomInternalID2', $data->InternalID)
                ->sum('QuantityResult');
        $ConvOut = Convertion::
                join('m_inventory_uom', 'm_inventory_uom.InternalID', '=', 'h_convertion.InventoryUomInternalID1')
                ->where('m_inventory_uom.CompanyInternalID', Auth::user()->Company->InternalID)
                ->where('h_convertion.InventoryUomInternalID1', $data->InternalID)
                ->sum('Quantity');

        $initialStock = $initialStockTamp;
        if ($data->Default == 0) {
            $initialStock = 0;
        }
        $endStock = $initialStock + $pembelian + $Rpenjualan - $penjualan - $Rpembelian + $Min - $Mout + $Tin - $Tout + $ConvIn - $ConvOut;
        $totalPieces += $endStock * $data->Value;
    }
    $UomInternalID = InventoryUom::where("CompanyInternalID", Auth::user()->Company->InternalID)->where("InventoryInternalID", $InventoryInternalID)->where("Default", 1)->first()->UomInternalID;
    $tampUom = Uom::find($UomInternalID)->UomID;
    return $totalPieces . ' ' . $tampUom;
}

function getLastPriceThisInventory($InventoryInternalID) {
    $purchaseDetail = PurchaseAddDetail::join("t_purchase_header", "t_purchase_header.InternalID", "=", "t_purchase_detail.PurchaseInternalID")
                    ->where("t_purchase_header.CompanyInternalID", Auth::user()->Company->InternalID)
                    ->where("t_purchase_detail.InventoryInternalID", $InventoryInternalID)
                    ->orderBy("t_purchase_header.PurchaseDate", "DESC")->first();
    if (count($purchaseDetail) > 0) {
        return number_format($purchaseDetail->Price, 2, '.', ',') . " (" . Uom::find($purchaseDetail->UomInternalID)->UomID . ")";
    }
    return 0;
}

function getPriceRangeInventory($inventoryInternalID, $uomInternalID, $qty) {
    $value = 0;
    $result = 0;
    $price = 0;
    $value = InventoryUom::where("CompanyInternalID", Auth::user()->Company->InternalID)
                    ->where("InventoryInternalID", $inventoryInternalID)
                    ->where("UomInternalID", $uomInternalID)->first()->Value;
    $result = $value * $qty;

    $inventory = Inventory::find($inventoryInternalID);

    if ($result <= $inventory->SmallQty) {
        $price = $inventory->SmallValue;
    } else if ($result <= $inventory->MediumQty) {
        $price = $inventory->MediumValue;
    } else {
        $price = $inventory->BigValue;
    }

    return $price;
}

function getStockInventorySOHelper($InternalID) {
    $tooltip = "";
    $inventorySimilarity = InventorySimilarity::where("CompanyInternalID", Auth::user()->Company->InternalID)->where("InventoryInternalID", $InternalID)->get();

    if (count($inventorySimilarity) > 0) {
        foreach ($inventorySimilarity as $data) {
            $tooltip .= $data->Inventory->InventoryID . ' ' . $data->Inventory->InventoryName . ' <br/> ';
        }
    } else {
        $tooltip = "-";
    }
    return getEndStockInventory($InternalID) . '---;---' . $tooltip;
}

function getInformationPriceRangeInventory($inventoryInternalID) {
    $inventory = Inventory::find($inventoryInternalID);
    $html = "";
    $html .= "Small Qty : " . $inventory->SmallQty . ' - Rp. ' . number_format($inventory->SmallValue, 2, '.', ',') . "<br/>";
    $html .= "Medium Qty : " . $inventory->MediumQty . ' - Rp. ' . number_format($inventory->MediumValue, 2, '.', ',') . "<br/>";
    $html .= "Big Qty : " . $inventory->BigQty . ' - Rp. ' . number_format($inventory->BigValue, 2, '.', ',') . "<br/>";
    return $html;
}

function getHPPValueInventory($inventoryInternalID) {
    $date = date("d-m-Y", strtotime(date("y-m-d")));
    $arrDate = explode("-", $date);
    $bulan = $arrDate[1];
    $tahun = $arrDate[2];
    $qtyPurchase = PurchaseHeader::qtyInventory($inventoryInternalID, $bulan, $tahun);
    $qtyPurchaseR = PurchaseReturnHeader::qtyInventory($inventoryInternalID, $bulan, $tahun);
    $qtyMemoIn = MemoInHeader::qtyInventory($inventoryInternalID, $bulan, $tahun);
    $purchase = PurchaseHeader::valueInventory($inventoryInternalID, $bulan, $tahun);
    $purchaseR = PurchaseReturnHeader::valueInventory($inventoryInternalID, $bulan, $tahun);
    $memoIn = MemoInHeader::valueInventory($inventoryInternalID, $bulan, $tahun);
    $valueBefore = InventoryValue::valueInventoryBefore($inventoryInternalID, $bulan, $tahun);
    $qtyBefore = InventoryValue::qtyInventoryBefore($inventoryInternalID, $bulan, $tahun);

    $data = Inventory::find($inventoryInternalID);
    $dataInitialQuantity = $data->InitialQuantity;
    $dataInitialValue = $data->InitialValue;
    if (InventoryValue::where('InventoryInternalID', $data->InternalID)
                    ->where('Month', $bulan)
                    ->where('Year', $tahun)->count() > 0) {
        $dataInitialQuantity = 0;
        $dataInitialValue = 0;
    }
    $qtyDividen = $dataInitialQuantity + $qtyBefore + $qtyPurchase - $qtyPurchaseR + $qtyMemoIn;
    if ($qtyDividen == 0) {
        $average = 0;
    } else {
        $average = (($dataInitialValue * $dataInitialQuantity) + ($valueBefore * $qtyBefore) + $purchase - $purchaseR + $memoIn) / $qtyDividen;
    }
    return number_format($average, 2, '.', ',');
}

function cekGantiValue($inventoryInternalID, $uomInternalID) {
    $where = ['InventoryInternalID' => $inventoryInternalID, 'UomInternalID' => $uomInternalID];

    $cekQuotation1 = QuotationDetail::where($where)->count();
    $cekQuotation2 = QuotationParcel::Where(DB::raw("ParcelInternalID IN (SELECT InternalID FROM m_parcel_inventory WHERE "
                            . "InventoryInternalID = $inventoryInternalID AND UomInternalID = $uomInternalID)"))->count();

    $cekSales1 = SalesOrderDetail::where($where)->count();
    $cekSales2 = SalesOrderParcel::Where(DB::raw("ParcelInternalID IN (SELECT InternalID FROM m_parcel_inventory WHERE "
                            . "InventoryInternalID = $inventoryInternalID AND UomInternalID = $uomInternalID)"))->count();

    $cekPurchase = PurchaseOrderDetail::where($where)->count();
//    print_r($cekSales1);
//    exit();
    //transfotmation sudah di memo in/out
    $cekMemoIn = MemoInDetail::where($where)->count();
    $cekMemoOut = MemoOutDetail::where($where)->count();
    $cekTransfer = TransferDetail::where($where)->count();

//    $cekConvertion = Convertion::where();

    $convertion1 = Convertion::join("m_inventory_uom", "m_inventory_uom.InternalID", "=", "h_convertion.InventoryUomInternalID1")->where($where)->count();
    $convertion2 = Convertion::join("m_inventory_uom", "m_inventory_uom.InternalID", "=", "h_convertion.InventoryUomInternalID2")->where($where)->count();

    if ($cekMemoIn > 0 || $cekMemoOut > 0 || $cekPurchase > 0 || $cekSales1 > 0 || $cekSales2 > 0 || $cekQuotation1 > 0 || $cekQuotation2 > 0 || $cekTransfer > 0 || $convertion1 > 0 || $convertion2 > 0) {
        return 'false';
    } else {
        return 'true';
    }
}

function setTampInventory($inventoryInternalID) {
    $inventory = Inventory::find($inventoryInternalID);
    $inventory->TampLastPrice = getLastPriceThisInventory($inventory->InternalID);
    $inventory->TampStock = getEndStockInventory($inventory->InternalID);
    $inventory->TampHPP = getHPPValueInventory($inventory->InternalID);
    $inventory->save();
}
?>