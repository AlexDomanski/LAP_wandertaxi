<?php
    require("includes/config.inc.php");
    require("includes/common.inc.php");
    require("includes/db.inc.php");

    date_default_timezone_set("Europe/Vienna");
    
    $conn = dbConnect();
    ta($_POST);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/common.css">
</head>
<body>
    <h1>Stationsuche</h1>

    <form method="post">
    <label>
        Station:</br>
        <select name="stationen">
            <?php
                $sql = "
                    SELECT
                        tbl_stationen.Station
                    FROM
                        tbl_stationen
                ";
                $stationen = dbQuery($conn, $sql);
                while($stat = $stationen->fetch_object())
                {
                    echo('<option value='.$stat->Station.'>'.$stat->Station.'</option>');
                }
            ?>
        </select>
    </label>
    <label>
        <br/>Tag</br>
        <select name="day" value="<?= (new DateTime())->format('l'); ?>">
            <?php
                $sql = "
                SELECT
                    tbl_wochentage.Wochentag
                FROM
                    tbl_wochentage
                ";
                $wochentage = dbQuery($conn, $sql);
                while($tag = $wochentage->fetch_object())
                {
                    echo('<option value='.$tag->Wochentag.'>'.$tag->Wochentag.'</option>');
                }
            ?>
        </select>
    </label>
    <label>
        <br/>Uhrzeit</br>
        <input type="time" name="time" value="<?= date("H:i") ?>">
    </label>

    </br>
    <button type="submit">SEND</button>
    </form>

    <?php
        $day = (count($_POST)>0) ? $_POST['day'] : (new DateTime())->format('l');
    
        $sql = "
            SELECT
                tbl_wochentage.IDWochentag,
                tbl_wochentage.Wochentag
            FROM
                tbl_wochentage
            WHERE(
                tbl_wochentage.Wochentag='". $day."'
            )
        ";

        $tagen = dbQuery($conn,$sql);
        while ($tag = $tagen->fetch_object()) {
            $sql = "
                SELECT
                    tbl_routentage_routenfuehrung.Uhrzeit,
                    tbl_routen.Route,
                    tbl_routenfuehrung.isEndstation
                FROM
                    tbl_routentage_routenfuehrung
                JOIN tbl_routentage ON tbl_routentage.IDRoutentag = tbl_routentage_routenfuehrung.FIDRoutentag
                JOIN tbl_routen ON tbl_routen.IDRoute = tbl_routentage.FIDRoute
                JOIN tbl_routenfuehrung ON tbl_routenfuehrung.IDRoutenfuehrung = tbl_routentage_routenfuehrung.FIDRoutenfuehrung
                JOIN tbl_stationen ON tbl_stationen.IDStation = tbl_routenfuehrung.FIDStation
                WHERE(
                    tbl_routentage.FIDWochentag_von <= ". $tag->IDWochentag." 
                    AND tbl_routentage.FIDWochentag_bis >= ". $tag->IDWochentag."
                    AND tbl_routentage_routenfuehrung.Uhrzeit >= TIME('". $_POST['time']."') 
                    AND tbl_stationen.Station ='".$_POST['stationen']."'
                )
            ";

            $varianten = dbQuery($conn, $sql);
            if ($varianten->num_rows > 0)
            {
                echo('<p class="success">Zu folgenden Zeiten können Sie an diesem Tag mit dem Wandertaxi fahren:</p>');
                echo('<ul>');
                while ($variant = $varianten->fetch_object()) {
                    $endstat = '';
                    if($variant->isEndstation){$endstat = " (Endstation)";}
                    echo('<li>' .$variant->Uhrzeit. ' auf ' .$variant->Route . $endstat .'</li>');
                }
                echo('</ul>');
            }
            else{
                echo('<p class="error">An diesem Tag fährt das Wandertaxi leider nicht mehr zu dieser Station</p>');
            }
        }
        
    ?>
    <p><a href="routen.php">Zurück zum Fahrplan</a></p>
</body>
</html>