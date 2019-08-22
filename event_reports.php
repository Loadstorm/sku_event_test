<?php
$accessError = 'There was a problem accessing the events log.';
$readError = 'There was a problem storing the event logs';

function showError($err){
    print '<pre>';
    print $err;
    print '</pre>';
    print '<footer></footer></body></html>';
    exit();

}

function getEventsJSON($file){

    if(file_exists($file))
    {
        $result = file_get_contents($file);
        return $result;
    }
    else{
        return false;
    }
}

/*******************************************************
SKUNinja-sample:  event-reports
Steven Hefner
8/19/2019
*/

//required classes
require_once($_SERVER['DOCUMENT_ROOT'] . '/examples/sku_report/sku_event_report/event.php');


?>
<!DOCTYPE html>
<html lang="en">
<head>


    <!--- Basic Page Needs
    ================================================== -->
    <title>Steven Hefner - SKUNinja Example Event Log Report</title>
    <meta name="description" content="SKUNinja Example Event Log Report">
    <meta name="author" content="Steven Hefner">

    <!-- Mobile Specific Metas
   ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- CSS
   ================================================== -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/cb2811c94a.js"></script>
    <link rel="stylesheet" href="css/base.css">

    <!-- Scripts
   ================================================== -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

    <!-- Social Media Stuff
   ================================================== -->
    <meta property="og:type" content="Example">
    <meta property="og:title" content="SKU Ninja Example Work">
    <meta property="og:description" content="SKU Ninja Example Work">
    <meta property="og:url" content="https://www.facebook.com/loadstormstudios">
    <meta property="og:image" content="http://portfolio.loadstormstudios.com/favicon.png">
    <meta property="og:site_name" content="SKU Ninja Example Work">
    <meta property="fb:app_id" content="">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="SKU Ninja Example Work">
    <meta name="twitter:description" content="SKU Ninja Example Work">
    <meta name="twitter:url" content="https://twitter.com/TheOUGuy">
    <meta name="twitter:image" content="http://portfolio.loadstormstudios.com/favicon.png">
    <meta name="twitter:site" content="Loadstorm Studios">

</head>
<body class="container">
<?php
//JSON file
$logs = 'SKUNinja-sample-logs.json';
$eventLogs = new SplObjectStorage();

// Get events from JSON file
//from a file in this case
$logsJSON = getcwd() . '/'. $logs;

$eventsJson = getEventsJSON($logsJSON);

if($eventsJson === false){
    //import failed so show an error and finish loading page
    showError($accessError);
}

$eventsArr = json_decode($eventsJson, JSON_PRETTY_PRINT);

foreach ($eventsArr as $log) {
    $event = new Event();
    foreach ($log as $field => $value) {
        switch($field) {
            case 'id':
                $event->setId($value);
                break;
            case 'created':
                $stamp = explode(" ", $value);
                $event->setCreDate($stamp[0]);
                $event->setCreTime($stamp[1]);
                break;
            case 'user_id':
                $event->setUserId($value);
                break;
            case 'ip':
                $event->setIp($value);
                break;
            case 'type':
                $event->setType($value);
                break;
            case 'subject':
                $event->setSubject($value);
                break;
            case 'body':
                $event->setBody($value);
                break;
        }
    }
    $eventLogs->attach($event);
}

?>
<div id="popup" class="popup" onclick="toggle();">
    <span class="popuptext" id="details"></span>
</div>
<div class="container-fluid header">
    <h1>Event Log Report</h1>
</div>
<table class="event-display">
    <thead>
        <tr>
            <td id="date">
                Date<span class="fas fa-sort-down" style="padding-left: 10px;"></span>
            </td>
            <td id="time">
                Time<span class="fas fa-sort-down" style="padding-left: 10px;"></span>
            </td>
            <td id="subject">
                Event Subject<span class="fas fa-sort-down" style="padding-left: 10px;"></span>
            </td>
        </tr>
    </thead>
    <?php

    //loop thru all events
    $eventLogs->rewind();
    while($eventLogs->valid())
    {
        //set Javascript
        $event = new Event();
        $event = $eventLogs->current();
        $level = $event->getType();
        $date = $event->getCreDate();
        $time = $event->getCreTime();
        $body = $event->getBody();
        $linkKey = $eventLogs->key();
        $event->setLink($linkKey);

        //get body results
        $len = strlen($body);
        $err = strpos($body, "\nUser: Array\n");

        $errD = $body;
        $errData = "";
        $errA = "";
        $fullArray = array();

        $errMessage = "";
        //is this an array or string?
        $arrayCheck = strpos($errD, "Array");
        if($arrayCheck === false)
        {
            //a string
            if($body === null)
            {
                $errMessage = "N/A";
            }
            else {
                $errMessage = preg_replace( "/\r|\n/", "", $body);
                //echo $errMessage . "<br/><br/>";
            }
            $event->setErrMessage($errMessage);
        }
        else
        {
            $errMessage = substr($body, 0, $err);
            $event->setErrMessage($errMessage);
            $errA = substr($body, $err+15);
            $errA = preg_replace( "/\r|\n/","", $errA);
            $errA = str_replace("(", "", $errA);
            $errA = str_replace(")", "", $errA);
            $e = strpos($errA, "[Group] => Array");
            $a = $e + 16;
            $d = strlen($errA);
            $d = $d - 8;
            $errD = substr($errA, $a, $d);
            $errA = substr($errA, 0, $e);

            $a = substr_count($errA, "[");
            $b = strpos($errA, "]");

            for($i = 0; $i < $a; $i++){
                if($i == 0) {
                    $key = "id";
                }
                else
                {
                    $key = substr($errA, 1, $b-1);
                }

                $errA = substr($errA, $b+3);
                if($key === "company_id") {
                    $c = strpos($errA, "[");
                    $x = strpos($errA, "]");
                    $value = substr($errA, $c+1, $x-3);

                    $errA = substr($errA, $x+1);
                    $value = addslashes($value);

                    $key = preg_replace( "/\r|\n/","",trim($key));
                    $value = preg_replace( "/\r|\n/","",trim($value));
                    $key = str_replace(" ", "&nbsp;", $key);
                    $value = str_replace(" ", "&nbsp;", $value);
                    $fullArray[$key] = $value;

                    $b = strpos($errA, "]");
                    $b = $b +2;
                }
                else{
                    $c = strpos($errA, "[");
                    $value = substr($errA, 1,$c-1);
                    $errA = substr($errA, $c);

                    $key = preg_replace( "/\r|\n/","",trim($key));
                    $value = preg_replace( "/\r|\n/","",trim($value));
                    $key = str_replace(" ", "&nbsp;", $key);
                    $value = str_replace(" ", "&nbsp;", $value);


                    $fullArray[$key] = $value;
                    $b = strpos($errA, "]");
                }
                //echo $errA . "<br/><br/>";
            }
            $a = substr_count($errD, "[");
            $b = strpos($errD, "]");
            for($i = 0; $i < $a; $i++){
                if($i == 0) {
                    $key = "gid";
                }
                else
                {
                    $key = "g" . substr($errD, 1, $b-1);
                }
                $errD = substr($errD, $b+3);

                $c = strpos($errD, "[");
                $value = substr($errD, 1,$c-1);
                $errD = substr($errD, $c);

                $key = preg_replace( "/\r|\n/","",trim($key));
                $value = preg_replace( "/\r|\n/","",trim($value));
                $key = str_replace(" ", "&nbsp;", $key);
                $value = str_replace(" ", "&nbsp;", $value);

                $fullArray[$key] = $value;
                $b = strpos($errD, "]");
            }
        }

//        $z = 0;
//        echo "Event Start <br/><br/>";
//        foreach($fullArray as $key => $value){
//            echo $key . " - " . $value . "<br/><br/>";
//            $z++;
//        }

        $event->setBodyData($fullArray);

        if($level === '1'){
            if($arrayCheck === false)
            {
                ?>
                    <tr class="normal-cond" onclick="showInfo('<?= $date; ?>','<?= $time; ?>','<?= $linkKey; ?>')">
                <?php
            }
            else
            {
                ?>
                <tr class="normal-cond" onclick="showInfo('<?= $date; ?>','<?= $time; ?>','<?= $linkKey; ?>')">
                <?php
            }

            //echo '<td class="normal-icon">';
        }
        elseif($level === '2'){
            if($arrayCheck === false)
            {
                ?>
                    <tr class="warning-cond" onclick="showInfo('<?= $date; ?>','<?= $time; ?>','<?= $linkKey; ?>')">
                <?php
            }
            else
            {
                ?>
                <tr class="warning-cond" onclick="showInfo('<?= $date; ?>','<?= $time; ?>','<?= $linkKey; ?>')">
                <?php
            }

            //echo '<td class="warning-icon">';
        }
        elseif($level === '3'){
            if($arrayCheck === false)
            {
                ?>
                    <tr class="error-cond" onclick="showInfo('<?= $date; ?>','<?= $time; ?>','<?= $linkKey; ?>')">
                <?php
            }
            else
            {
                $errData = implode(",", $fullArray);
                $jsonOut = json_encode($fullArray, JSON_FORCE_OBJECT);
                $test = "blah,blah,blah";
                ?>
                    <tr class="error-cond" onclick="showInfo('<?= $date; ?>','<?= $time; ?>','<?= $linkKey; ?>')">
                <?php

            }

            //echo '<td class="error-icon">';
        }
    ?>

        <td>
            <?= trim($event->getCreDate()); ?>
        </td>
        <td>
            <?= trim($event->getCreTime()); ?>
        </td>
        <td>
            <?php
                if($level === "1"){
                    echo '<span class="eventSubject-Normal fas fa-check"></span>';
                }
                elseif($level === "2"){
                    echo '<span class="eventSubject-Warning fas fa-exclamation"></span>';
                }
                elseif($level === "3"){
                    echo '<span class="eventSubject-Error fas fa-times"></span>';
                }
                else
                {
                    //Do nothing
                }
                    echo trim($event->getSubject());
            ?>
        </td>
    </tr>
        <?php
        $eventLogs->next();
    }
    ?>
</table>

<footer>

</footer>

<script language="JavaScript">
    function showInfo(date, time, link){
        //process body info
        //alert(link);
        let curEvent = new Array();
        let event = new Array();
        let events = new Array();
        <?php
            $currEvent = new Event();
            $eventLogs->rewind();
            while($eventLogs->valid())
            {
                $event = $eventLogs->current();
                $date = $event->getCreDate();
                $time = $event->getCreTime();
                $body = $event->getBodyData();
                $errMessage = $event->getErrMessage();
                $linkKey = $event->getLink();
                ?>
                    event.push("<?= $linkKey ?>");
                    event.push("<?= $date ?>");
                    event.push("<?= $time ?>");
                    event.push("<?= addslashes($body) ?>");
                    event.push("<?= addslashes($errMessage) ?>");
                    events.push(event);
                    //alert("Link: " + "<?= $linkKey ?>" + " Date: " + "<?= $date ?>" + " Time: " + "<?= $time ?>" + " Body: " + "<?= addslashes($body) ?>" + " Message: " + "<?= addslashes($errMessage) ?>");
                <?php
                $eventLogs->next();
            }
        ?>

        for(let i=0;i < events.length; i++)
        {
            let event = events[i];
            let eLink = event[0];
            if(eLink == link)
            {
                for(let x = 0; x < event[link].length; x++){
                    curEvent.push(event[link][x]);
                }
            }
        }

        let body = "<h1>Event Report for " + curEvent[1] + " @ " + curEvent[2] + "</h1><br/>";
        if(curEvent[3] === "")
        {
            body = body + "Nothing to report";
            let details = document.getElementById("details");
            details.innerHTML = body;
            details.classList.toggle("show");
        }
        else {
            body = "<h1>Event Report for " + curEvent[1] + " @ " + curEvent[2] + "</h1><br/>";

            //Event Params
            // id
            // name
            // company_id
            // [password_token]
            // email
            // email_verified
            // email_token
            // email_token_expires
            // tos
            // active
            // last_login
            // last_action
            // is_admin
            // created
            // modified
            // group_id
            // 1
            // gid
            // gname

            let bodyValues = new Array();
            for(let d=0;d < curEvent[3].length;d++)
            {
                bodyValues.push(curEvent[3][d]);
                alert(bodyValues[d]);
            }

            body = body + curEvent[4] + "<br/><br/>";
            body = body + "Id: " + bodyValues[0] + "<br/>";
            body = body + "Name: " + bodyValues[1] + "<br/>";
            body = body + "Company Id: " + bodyValues[2] + "," + bodyValues[3] + "<br/>";
            body = body + "Password Token: " +bodyValues[4] + "<br/>";
            body = body + "Email: " + bodyValues[5] + "<br/>";
            body = body + "Email Verified? " + bodyValues[6] + "<br/>";
            body = body + "Email Token: " + bodyValues[7] + "<br/>";
            body = body + "Email Token Expires: " + bodyValues[8] + "<br/>";
            body = body + "TOS: " + bodyValues[9] + "<br/>";
            body = body + "Active: " + bodyValues[10] + "<br/>";
            body = body + "Last login: " + bodyValues[11] + "<br/>";
            body = body + "Last Action: " + bodyValues[12] + "<br/>";
            body = body + "Created: " + bodyValues[13] + "<br/>";
            body = body + "Modified: " + bodyValues[14] + "<br/><br/>";
            body = body + "Group Id = " + bodyValues[15] + "<br/>";
            body = body + "Group Info<br/><br/>";

            body = body + "Id = " + bodyValues[16] + "<br/>";
            body = body + "Group Name = " + bodyValues[17] + "<br/>";

            let details = document.getElementById("details");
            details.innerHTML = body;
            details.classList.toggle("show");
        }
    }
</script>

<script language="JavaScript">
    function sortTable(table, col, reverse) {
        let tb = table.tBodies[0], // use `<tbody>` to ignore `<thead>` and `<tfoot>` rows
            tr = Array.prototype.slice.call(tb.rows, 0), // put rows into array
            i;
        reverse = -((+reverse) || -1);
        tr = tr.sort(function (a, b) { // sort rows
            return reverse // `-1 *` if want opposite order
                * (a.cells[col].textContent.trim() // using `.textContent.trim()` for test
                        .localeCompare(b.cells[col].textContent.trim())
                );
        });
        for(i = 0; i < tr.length; ++i) tb.appendChild(tr[i]); // append each row in order
        if(col.contains('fa-sort-up'))
        {
            document.getElementById(col.id).classList.remove('fa-sort-up');
            document.getElementById(col.id).classList.add('fa-sort-down');
        }
        else {
            document.getElementById(col.id).classList.remove('fa-sort-down');
            document.getElementById(col.id).classList.add('fa-sort-up');
        }
    }

    function makeSortable(table) {
        let th = table.tHead, i;
        th && (th = th.rows[0]) && (th = th.cells);
        if (th) i = th.length;
        else return; // if no `<thead>` then do nothing
        while (--i >= 0) (function (i) {
            let dir = 1;
            th[i].addEventListener('click', function () {sortTable(table, i, (dir = 1 - dir))});
        }(i));
    }

    function makeAllSortable(parent) {
        parent = parent || document.body;
        let t = parent.getElementsByTagName('table'), i = t.length;
        while (--i >= 0) makeSortable(t[i]);
    }

    window.onload = function () {
        makeAllSortable();
    }
</script>

<script language="JavaScript">
    function toggle() {
        let element = document.getElementById("details");
        element.classList.toggle("show");
    }
</script>
</body>
</html>


