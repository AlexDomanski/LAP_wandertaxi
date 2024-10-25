<?php
require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/db.inc.php");

$conn = dbConnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        td,tr, table{ 
            border: solid 1px black;
            border-collapse: collapse;
        }

        div{
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>
    <h1>Fahrplan:</h1>
    <div>
    <?php
        $sql = "
            SELECT 
                tbl_routen.IDRoute,
                tbl_routen.Route,
                tbl_routen.Beschreibung
            FROM tbl_routen
        ";

        $routen = dbQuery($conn, $sql);
        while($route = $routen->fetch_object()){
            echo('<table>');
            echo('<thead><tr><td colspan="100">'. $route->Route . ' (<i>' . $route->Beschreibung . '</i>)</td></tr></thead><tbody>');
            $sql = "
                SELECT
                    tbl_routentage.FIDWochentag_von,
                    tbl_routentage.FIDWochentag_von,
                    table1.Kurzzeichen AS Anfangtag,
                    table2.Kurzzeichen AS Endetag
                FROM tbl_routentage
                JOIN
                    tbl_wochentage table1 ON FIDWochentag_von = table1.IDWochentag
                JOIN
                    tbl_wochentage table2 ON FIDWochentag_bis = table2.IDWochentag
                WHERE( tbl_routentage.FIDRoute =". $route->IDRoute .")
            ";

            
            echo('<tr>');
            echo('<td> </td>');
            $intervale = dbQuery($conn, $sql);
            $quantityOfintervals = 0;
            while($interval = $intervale->fetch_object()){
                $tage = '';
                $quantityOfintervals += 1;
                if($interval->Anfangtag == $interval->Endetag){
                    $tage .= $interval->Anfangtag;
                }
                else{
                    $tage .= $interval->Anfangtag;
                    $tage .= ' - ';
                    $tage .= $interval->Endetag;
                }
                echo('<td>'. $tage .'</td>');
            }
            echo('</tr>');

            echo('<tr>');
                echo('<td><b>Ort</b></td>');
                for ($i=0; $i < $quantityOfintervals; $i++) { 
                    echo('<td><b>ab/an</b></td>');
                }
            echo('</tr>');

            $sql = "
                SELECT 
                    tbl_routenfuehrung.IDRoutenfuehrung,
                    tbl_routenfuehrung.isEndstation,
                    tbl_stationen.Station
                FROM tbl_routenfuehrung
                INNER JOIN tbl_stationen ON tbl_stationen.IDStation=tbl_routenfuehrung.FIDStation
                WHERE( tbl_routenfuehrung.FIDRoute =" . $route->IDRoute .")
                ORDER BY tbl_routenfuehrung.Reihenfolge 
            ";
            $routenfuehrungen = dbQuery($conn,$sql);
            while($routenfuehrung = $routenfuehrungen->fetch_object()){
                echo('<tr>');
                    if ($routenfuehrung->isEndstation == 1) {
                        echo('<td><b>' . $routenfuehrung->Station . '</b></td>');
                    }
                    else{
                        echo('<td>' . $routenfuehrung->Station . '</td>');
                    }

                    $sql = "
                        SELECT
                        tbl_routentage_routenfuehrung.Uhrzeit
                        FROM tbl_routentage_routenfuehrung
                        WHERE( FIDRoutenfuehrung=". $routenfuehrung->IDRoutenfuehrung.")
                    ";
                    $uhrzeiten = dbQuery($conn, $sql);
                    while ($uhrzeit = $uhrzeiten->fetch_object()) {
                        $time = new DateTime($uhrzeit->Uhrzeit);
                        echo('<td><b>' . $time->format('H:i') . '</b></td>');
                    }

                echo('</tr>');
            }

            echo('</tbody></table>');
        }
    ?>
    </div>
    <h3><a href="stationsuche.php">Stationsuche</a></h3>
</body>
</html>