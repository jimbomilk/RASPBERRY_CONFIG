<?php
// Lazy loading auto-inclusions.
function __autoload($className)
    {
    @include "Server/".$className.".class.php";
    @include $className;
    }

$target = strip_tags(trim($_GET['target']));
$action = strip_tags(trim($_GET['action']));
Utils::addFile("/iwk/error.log","*** BACKEND *** target:".$target.", action:".$action,"777");

// **************************************************************************************************************************

switch ($target)
{

    case "locale":
            $language = strip_tags(trim($_GET['lang']));
            if (!Locale::setLocale($language))
            {
            echo "Error setting language.";
            }

        break;


    case "network":
        $action = strip_tags(trim($_GET['action']));


        if ($action=="wifi-list")
        {
            $json = new Services_JSON();
            $jsonString = "{identifier:'bssid',label:'bssid',items:[ ";

            $wiFiArray = Network::wifiNetworksList();
            while (list(,$v) = @each($wiFiArray)) $jsonString .= $json->encode($v).",";

            header("Content-Type: text/html; charset=UTF-8");
            echo substr($jsonString,0,strlen($jsonString)-1)." ]}";
        }
        else if ($action=="view-network-hardware")
        {
            $netHardware = Network::listAllInterfaces();
            print_r($netHardware);
        }
        else if ($action=="info")
        {
            echo str_replace("\n","<br>",Network::getNetworkInformations());
        }
        else if ($action=="init")
        {
            Network::initNetwork();
        }
    break;

    case "connect":
        $action = strip_tags(trim($_GET['action']));

        $netIP = strip_tags(trim($_GET['netIP']));
        $netMask = strip_tags(trim($_GET['netMask']));
        $netGateway = strip_tags(trim($_GET['netGateway']));
        $netDNS = strip_tags(trim($_GET['netDNS']));

        $netSSID = strip_tags(trim($_GET['netSSID']));
        $netPassword = strip_tags(trim($_GET['netPassword']));
        $netSecurity = strip_tags(trim($_GET['netSecurity']));


        Network::setNetwork($action,$netIP,$netMask,$netGateway,$netDNS,$netSSID,$netPassword,$netSecurity);

    break;


    case "video":
        $action = strip_tags(trim($_GET['action']));
        if ($action=="getResolutions")
            {
            echo Misc::getAvailableResolutions();
            }

        if ($action=="changeResolution")
            {
            Misc::changeResolution((int)$_GET['w'],(int)$_GET['h']);
            }

        if ($action=="rotate")
            {
            Misc::videoRotate(strip_tags(trim($_GET['rotation'])));
            }
    break;


    case "sound":
        Misc::setSound();
    break;


    case "browser":
        $action = strip_tags(trim($_GET['action']));
        $toggle = strip_tags(trim($_GET['toggle']));


        if ($action=="makeHomePersistent")
            Misc::makeBrowserHomePersistent($toggle);

    break;


    case "strictKiosk":
        Misc::setStrickKioskMode($_GET['url'],$_GET['token'],$_GET['timeout'],$_GET['cache'],$_GET['keyboard'],$_GET['pageReload'],$_GET['haltAt'],$_GET['blankingTime'],$_GET['disableInput'],$_GET['proxy']);
    break;

    case "location":
        if (Misc::changeScreenLocation(strip_tags(trim($_GET['screenLocation'])))) echo "Código actualizado.";
        else echo "Error al actualizar el código.";
    break;


    case "system-infos":
        Misc::viewSystemInformations();
    break;

}

?>