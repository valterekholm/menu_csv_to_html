<?php
// Open the CSV file
$filename = "csv.txt";
//$filename = "csv2.txt";

$PRICECOL = 2; //0-bazed

$isget = 0;
if(!empty($_GET)){
    if(!empty($_GET["filename"])){
        $filename = $_GET["filename"];
        $isget = true;
    }
}

$stream = fopen($filename, "r");




?>

<html>
    <head>
        <style>
            body, table{
                font-variant-caps: small-caps;
            }
            table, th, td{
                border: 1px solid gray;
            }
            /*För paranteser i menys rubrik*/
            h2 span{
                font-size: 80%;
            }
            .infotext{
                color:red;
            }
        </style>
    </head>
    <body>

<?php

if($isget) echo $filename . "<br>";

$lines = array();
$file = fopen($filename,"r");
while($row = fgets($file)){

    $lines[]=$row;
}
fclose($file);

// Extract rows and columns
$count = 0;
$trs = array();
$rows = array();//rows with defininitions
$trcount = 0;
while (($row = fgetcsv($stream,5000,';')) !== false) {
    if(row_is_header($count, $lines)){
        $drow = array();
        //TODO: "strip"
        if(strstr($row[0],"(")){
            if(strstr($row[0],"/")){
                $drow ["h2"] =  str_replace(')', ')</span>', str_replace('(', '<span>(', str_replace('*', '', $row[0])));
            }
            else{
                $drow ["h2"] =  str_replace('*', '', $row[0]);
            }
        }
        else{
            $drow ["h2"] =  str_replace('*', '', $row[0]);
        }
        
        //$drow ["h2"] =  str_replace(')', ')</span>', str_replace('(', '<span>(', str_replace('*', '', $row[0])));
        $rows[]=$drow;
        $trcount = 0;
    }
    else if(row_is_subheader($count, $lines)){
        $drow = array();
        $drow["p"] = str_replace('*','',$row[0]);
        $rows[]=$drow;
        $trcount = 0;
    }
    else if(!empty($row)){
        $drow = array();
        $drow[$trcount] = get_as_trx2($row);
        $trcount++;
        $rows[]=$drow;
    }
    $count++;
}

// Close the CSV file
fclose($stream);

//echo "<table>$trs</table>";

$tc = 0;
$endtable = false;
$starttable = false;
$lastrow = "";

foreach($rows as $row){
    $is_info = 0;
    if(empty($row)){
        continue;
    }

    if(array_key_exists('h2',$row)){
        if($endtable){
            echo "</table>" . "\n";
            $endtable = 0;
        }
        echo "<".array_key_first($row).">".$row[array_key_first($row)]."</".array_key_first($row).">" . "\n";
        $is_info = 1;
        $starttable = true;
    }
    else if(array_key_exists('p',$row)){
        echo "<".array_key_first($row).">".$row[array_key_first($row)]."</".array_key_first($row).">" . "\n";
        $is_info = 1;
        $starttable = true;
    }
    else if(is_array($row)){
        if($starttable){
            echo "<table>" . "\n";
            $starttable = 0;    
        }
        $is_info = 0;


        foreach($row as $k => $v){
            echo $v;
            //echo $k . ":" . $v . "<br>";
        }
        $tc++;
        $endtable = 1;
    }
    $lastrow = $row;
}
if($endtable){
    echo "</table>" . "\n";
}

function row_is_info($row){
    $ret = false;
    if(array_key_exists('h2',$row) || array_key_exists('p',$row)){
        $ret = true;
    }
    return $ret;

}
function row_is_table($row){

    $ret = false;
    if(is_array($row[0])){
        $ret = true;
    }
    return $ret;
}

function print_as_tr($data){
    echo "<tr>";
    $count = 0;
    foreach ($data as $col) {
        echo "<td>".format_col($col, $count)."</td>";
        $count++;
    }
    echo "</tr>";
}

//print last element on a new row
function print_as_trx2($data){
    echo "<tr>";
    $len = count($data);
    $count = 0;
    $firstRowLen = $len-1;
    foreach ($data as $col) {
        if($count == $firstRowLen) echo "<td colspan=$firstRowLen>$col</td>";
        else echo "<td>" . format_col($col, $count) . "</td>";
        $count++;

        if($count==$len-1){
            echo "</tr><tr>";
        }
    }
    echo "</tr>\n";
}



function get_as_trx2_($data){
    if(empty($data[0])){
        return "";
    }
    $ret = "<tr>";
    $len = count($data);
    $count = 0;
    $firstRowLen = $len-1;
    $isNewRow = false;
    $moreThanOneCount = 0;
    foreach ($data as $col) {
        if($count == $firstRowLen) $ret .= "<td colspan=$firstRowLen>$col</td>";
        else $ret .= "<td>" . format_col($col, $count) . "</td>";
        $count++;

        if($count==3){
            if($moreThanOneCount>0){
                $tr_ = "<tr class='red_bg'>";
            }
            else{
                $tr_ = "<tr>";

            }
            $ret .= "</tr>$tr_";
            $isNewRow = true;
            $moreThanOneCount++;
        }
    }
    $ret .= "</tr>\n";

    return $ret;
}

//TODO: gör så att infotext bara kan komma på en fjärde "kolumn"
//TODO: gör så att priskolumn även kan vara kolumn 2
function get_as_trx2($data){
    if(empty($data[0])){
        return "";
    }
    $ret = "<tr>";
    $len = count($data);
    $count = 0;
    $firstRowLen = $len-1;
    $isNewRow = false;
    $moreThanOneCount = 0;
    $infoTextPosition0i = 3;
    foreach ($data as $col) {
        if($count == $infoTextPosition0i) $ret .= "<td colspan=$firstRowLen class='infotext'>$col</td>";
        else $ret .= "<td>" . format_col($col, $count) . "</td>";

        if($count == 0 && !is_numeric($col)){
            //shift row to ignore dish number
            //to get prices showing with added symbols
            //assumes whole table have same formatting

            $count++;
            $infoTextPosition0i++;
        }
        if($count==$infoTextPosition0i){
            //definiera ny rads eventuella attribut
            if($moreThanOneCount>0){
                $tr_ = "<tr class='red_bg'>";
            }
            else{
                $tr_ = "<tr>";

            }
            $ret .= "</tr>$tr_";
            $isNewRow = true;
            $moreThanOneCount++;
        }
        $count++;


    }
    $ret .= "</tr>\n";

    return $ret;
}

function print_as_menu_divs($data){
    echo "<div class='menu_row'>";
    $count = 0;
    foreach ($data as $col) {
        echo "<div>" . format_col($col, $count). "</div>";
        $count++;
    }
    echo "</div>";
}

function print_as_divs_table($data){
    echo "<tr><td>";
    $count = 0;
    foreach ($data as $col) {
        echo "<div>". format_col($col,$count) . "</div>";
        $count++;
    }
    echo "</td></tr>";
}


function row_is_header($index, $data){
    if(substr ($data[$index], 0, 2) == "**"){
        return true;
    }
    return false;
}

function row_is_subheader($index, $data){
    if(substr ($data[$index], 0, 1) == "*"){
        if(substr($data[$index], 1, 1) != "*"){
            return true;
        }
    }
    return false;
}

function print_col($data, $index){
    echo $data;
    if($index == $PRICECOL){
        echo ":-";
    }
}

function format_col($data, $index){
    $ret = "";
    $ret .= $data;
    if($index == 2){
        $ret .= ":-";
    }
    return $ret;
}

?>