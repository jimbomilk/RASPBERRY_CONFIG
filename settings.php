<?php
// Defining variables: load system persistent state.
    //$c = 'c:';
    $c='';

    // Strict kiosk URL.
    $iwkStrictModeURL = trim(file_get_contents($c."/iwk/iwk.strictKioskBaseURL"));
    if (!$iwkStrictModeURL || $iwkStrictModeURL=="http://www.binaryemotions.com") $iwkStrictModeURL = "http://";

    // Refresh timeout.
    $iwkRefreshTimeout = file_get_contents("/iwk/iwk.refreshTimeout");
    $iwkRefreshTimeout = (int)($iwkRefreshTimeout/60/1000);

    // Screen Location
    $iwkScreenLocation = (int)file_get_contents("/iwk/iwk.screenLocation");

    // Reload timeout.
    $iwkPageReloadTimeout = (int)file_get_contents("/iwk/iwk.strictKioskReloadPageTimer");

    // Video rotation info.
    $iwkVideoRotationHow = file_get_contents("/iwk/iwk.screenRotate");
    if (!$iwkVideoRotationHow || trim($iwkVideoRotationHow)=="normal") $iwkVideoRotationNormal = "checked";
    if (trim($iwkVideoRotationHow)=="left") $iwkVideoRotationLeft = "checked";
    if (trim($iwkVideoRotationHow)=="right") $iwkVideoRotationRight = "checked";
    if (trim($iwkVideoRotationHow)=="inverted") $iwkVideoRotationReverse = "checked";

    // Virtual keyboard.
    $iwkVirtualKeyboardFileOn = "";
    $iwkVirtualKeyboardFileOff = "";
    if (file_exists("/iwk/iwk.virtualKeyboardDisplay")) $iwkVirtualKeyboardFileOn = "checked";
    else $iwkVirtualKeyboardFileOff = "checked";

    // Proxy.
    $iwkAppProxy = trim(file_get_contents("/iwk/iwk.applicationProxy"));

    // System halt time.
    /*if (file_exists("/iwk/iwk.systemHaltAt")) $iwkSystemHaltContent = trim(file_get_contents("/iwk/iwk.systemHaltAt"));

    if ($iwkSystemHaltContent) $iwkSystemHaltHour = $iwkSystemHaltContent;
    else $iwkSystemHaltHour = 0;*/

    // Disable mouse/keyboard input.
    $iwkDisabledInput = "";
    if (file_exists("/iwk/iwk.systemDisableInput")) $iwkDisabledInput = "checked";

    // Add token (MAC address) at the end of the target URL.
    $iwkAddToken = "";
    if (file_exists("/iwk/iwk.strictKioskAddMacAddress")) $iwkAddToken = "checked";

    // Is password disabled?
    $unlockPwdHashSaved = trim(file_get_contents("/iwk/iwk.adminPasswd"));
    if ($unlockPwdHashSaved==trim(md5("no-passwd"))) $passwordDisabled = true;
    else $passwordDisabled = false;

// ************************************************************************************************ ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Addmeetoo - settings</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="Author" content="ing. Marco Buratto, ing.marcoburatto@gmail.com">
    <meta name="Comment" content="This is a dirty no-framework, js-in-pages quick and fast coding style.">

    <link type="text/css" href="Styles/standard.css" rel="stylesheet">
    <link type="text/css" href="JavaScript/dojo/dijit/themes/soria/soria.css" rel="stylesheet">
    <link type="text/css" href="JavaScript/dojo/dojox/grid/resources/soriaGrid.css" rel="stylesheet">

    <style>
        @font-face {
            font-family: 'Economica';
            font-style: normal;
            font-weight: 700;
            src: local('Economica Bold'), local('Economica-Bold'), url(Styles/WebFonts/economica_2.woff) format('woff');
        }
    </style>

    <script type="text/javascript" src="JavaScript/Utils.object.js"></script>
    <script type="text/javascript" src="JavaScript/dojo/dojo/dojo.js" djConfig="isDebug:false, parseOnLoad:true"></script>

    <script type="text/javascript">
       dojo.require("dojo.parser");
       dojo.require("dojo.date.locale");
       dojo.require("dijit.TitlePane");
       dojo.require("dijit.layout.ContentPane");
       dojo.require("dijit.layout.TabContainer");
       dojo.require("dojox.layout.FloatingPane");

        dojo.addOnLoad(function()
            {
            // Start kiosk mode in seconds if no user interaction detected.
            document.getElementById('KioskModeTabContent2').style.display = "none";
            document.getElementById('KioskModeTabContent1').innerHTML = "<p class='title'>Start kiosk mode in 15 seconds if no user interaction detected.<br><br><a href='#.' onClick='showAdmin();'>Click here for settings modify</a>.</p>";

            tOut = window.setTimeout("kioskModeSet(true);",15000);
            });

        function showAdmin()
            {
            clearTimeout(tOut);

            document.getElementById('KioskModeTabContent2').style.display = "block";
            document.getElementById('KioskModeTabContent1').style.display = "none";
            }

        // XHR calls and others.
        function localeSet(/* String */ lang)
            {


            dojo.xhr("GET", {
                url: "backend.php?target=locale&lang="+lang,
                preventCache: true,
                load: function(data,args)
                    {
                    if (data!="")
                        alert(data);
                    },
                timeout: 30000,
                error: function(error,args)
                    {
                    alert("Cannot change language.");
                    }
                });
            }

        function getAvailableResolutions()
            {
            dojo.xhr("GET", {
                url: "backend.php?target=video&action=getResolutions",
                preventCache: true,
                load: function(data,args)
                    {
                    if (data!="")
                        alert(data);
                    },
                timeout: 30000,
                error: function(error,args) { ; }
                });
            }

        function changeResolution()
            {
            var resolutionW = parseInt(document.getElementById('resolutionW').value);
            var resolutionH = parseInt(document.getElementById('resolutionH').value);

            if ((resolutionW>0) && (resolutionW>0))
                {


                dojo.xhr("GET", {
                    url: "backend.php?target=video&action=changeResolution&w="+resolutionW+"&h="+resolutionH,
                    preventCache: true,
                    load: function(data,args)
                        {
                        if (data!="")
                            console.log(data);
                        },
                    timeout: 30000,
                    error: function(error,args) { ; }
                    });
                }
            }

        function soundSet()
            {


            dojo.xhr("GET", {
                url: "backend.php?target=sound,
                preventCache: true,
                load: function(data,args)
                    {
                    if (data!="")
                        console.log(data);
                    },
                timeout: 30000,
                error: function(error,args) { ; }
                });
            }

        function modifyPassword(/* Boolean */ dontuse)
            {


            if (!dontuse) var newAdminPassword = prompt("Insert new password"); // prompt for password.
            else var newAdminPassword = "no-passwd";

            if (newAdminPassword)
                {
                dojo.xhr("GET", {
                    url: "backend.php?target=modifyPasword",
                    preventCache: true,
                    load: function(data,args)
                        {
                        if (data!="")
                            {
                            alert(data);
                            document.location.reload();
                            }
                        },
                    timeout: 30000,
                    error: function(error,args) { ; }
                    });
                }
            }

        function makeBrowserHomePersistent(/* Boolean */ doPersist)
            {
            if (doPersist)
                {
                var toggle = "on";
                if (!confirm("Are you sure you want to replace default Raspberry Digital Signage Chrome settings with this browser snapshot? "))
                    return false;
                }
            else
                {
                var toggle = "off";
                if (!confirm("Replace your own Chrome settings with default ones? "))
                    return false;
                }

            if (confirm("PLEASE WAIT UNTIL SYSTEM RESTARTS, OPERATION MAY TAKE MINUTES."))
                {


                dojo.xhr("GET", {
                    url: "backend.php?target=browser&action=makeHomePersistent&toggle="+toggle,
                    preventCache: true,
                    load: function(data,args)
                        {
                        if (data!="")
                            console.log(data);
                        },
                    timeout: 30000,
                    error: function(error,args) { ; }
                    });
                }
            }

        function videoRotationSet()
            {


            if (!confirm("This will reboot system now, continue? ")) return true;

            if (document.getElementById('videoRotationRadioNormal').checked) var rotation = "normal";
            if (document.getElementById('videoRotationRadioLeft').checked) var rotation = "left";
            if (document.getElementById('videoRotationRadioRight').checked) var rotation = "right";
            if (document.getElementById('videoRotationRadioReverse').checked) var rotation = "inverted";

            dojo.xhr("GET", {
                url: "backend.php?target=video&action=rotate&rotation="+rotation,
                preventCache: true,
                load: function(data,args)
                    {
                    if (data!="")
                        console.log(data);
                    },
                timeout: 30000,
                error: function(error,args) { ; }
                });
            }

       function setLocation()
       {
           var location = document.getElementById('skmScreenLocation').value;

           dojo.xhr("GET", {
               url: "backend.php?target=location&screenLocation="+location,
               preventCache: true,
               load: function(data,args)
               {
                   if (data!="")
                       console.log(data);
               },
               timeout: 30000,
               error: function(error,args) { ; }
           });
       }


       function kioskModeSet(/* Boolean */ directBoot)
            {

            var skmURL = document.getElementById('skmPage').value;
            var skmTimeout = document.getElementById('skmInactivityBeforeReload').value;
            var skmPageTimeout = document.getElementById('skmForcePageReload').value;
            var iwkScreenblankingTime = 0; // future use.
            var skmSystemHalt = document.getElementById('skmSystemHalt').value;
            var skmCacheURL = false; //document.getElementById('skmCacheURL').checked;
            var skmVirtualKeyboard = document.getElementById('virtualKeyboardSetOn').checked;
            var skmProxy = document.getElementById('skmHTTPProxy').value;
            var skmDisableAllInput = document.getElementById('skmDisableAllInput').checked;
            var skmAddMacAddress = document.getElementById('skmAddMacAddress').checked;

            var skmScreenLocation = document.getElementById('skmScreenLocation').value;

            if (directBoot) var directStart = "y";
            else var directStart = "n";

            if (skmCacheURL) var skmCache = "y";
            else var skmCache = "n";

            if (skmVirtualKeyboard) var skmVirtualKeyboard = "on";
            else var skmVirtualKeyboard = "off";

            if (skmDisableAllInput) var skmDisableAllInput = "on";
            else var skmDisableAllInput = "off";

            if (skmAddMacAddress) var skmAddMacAddress = "on";
            else var skmAddMacAddress = "off";

            var uri = "backend.php?target=strictKiosk&url="+encodeURIComponent(skmURL)+"&token="+skmAddMacAddress+"&directStart="+directStart+"&timeout="+skmTimeout+"&cache="+skmCache+"&keyboard="+skmVirtualKeyboard+"&pageReload="+skmPageTimeout+"&haltAt="+skmSystemHalt+"&blankingTime="+iwkScreenblankingTime+"&disableInput="+skmDisableAllInput+"&proxy="+encodeURIComponent(skmProxy);

            dojo.xhr("GET", {
                url: uri,
                preventCache: true,
                load: function(data,args)
                    {
                        alert(data);
                    if (data!="")
                        console.log(data);
                    },
                timeout: 30000,
                error: function(error,args) { alert(error); }
                });
            }

        function viewSystemInformations()
            {
            dojo.xhr("GET", {
                url: "backend.php?target=system-infos",
                preventCache: true,
                load: function(data,args)
                    {
                    if (data!="")
                        document.getElementById('systemLog').innerHTML = data;
                    },
                timeout: 30000,
                error: function(error,args) { ; }
                });
            }
    </script>
</head>

<body class="tundra" style="height:92%;">
    <div id="header" style="height: 100px"></div>
    <div id="content" style="height:auto;">
        <div style="height:100%;">
             <!-- DIGITAL SIGNAGE TAB -->
             <div dojoType="dijit.layout.ContentPane" href="" title="<strong>Digital signage</strong>" refreshOnShow="false" style="padding:15px;">
                 <div id="KioskModeTabContent1"></div>
                 <div id="KioskModeTabContent2">
                     <div>

                         <div style="margin-top:20px;">
                             <div>
                                 <!-- Language -->
                                 <div>Ip: <?php
                                     $command="/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'";
                                     $localIP = exec ($command);
                                     echo $localIP; ?>
                                 </div>
                                 <div class="title">
                                     <img src="Images/Button-empathy-icon.png" style="vertical-align:middle; padding-right:10px;">
                                     <strong>Localización</strong>
                                 </div>
                                 <div style="margin-bottom:20px;">
                                     <p>
                                         Selecciona el <strong>idioma</strong> del sistema: &nbsp;
                                         <select id="languageSelector" onChange="if (this.options[this.selectedIndex].value) localeSet(this.options[this.selectedIndex].value);" style="width:130px;">
                                             <option value=""> Selecciona tu idioma...</option>
                                             <option value="cn"> Chinese (use Google Input Tools)</option>
                                             <option value="us"> English</option>
                                             <option value="fr"> French</option>
                                             <option value="de"> German</option>
                                             <option value="it"> Italian</option>
                                             <option value="jp"> Japanese</option>
                                             <option value="pt"> Portuguese</option>
                                             <option value="ru"> Russian</option>
                                             <option value="es"> Spanish (default)</option>
                                        </select>
                                     </p>


                                 </div>

                                 <div class="title">
                                     <img src="Images/Button-empathy-icon.png" style="vertical-align:middle; padding-right:10px;">
                                     <strong>Código de pantalla</strong>
                                 </div>
                                 <div style="margin-bottom:20px;">
                                     <p>
                                         <input type="number" name="screenLocation" id="skmScreenLocation" style="width:140px;" value="<?php echo $iwkScreenLocation;?>">
                                         <a href="#." onClick="setLocation();">salvar</a>.
                                 </div>

                                         <!-- Video -->
                                 <div class="title">
                                     <img src="Images/Window-remote-desktop-icon.png" style="vertical-align:middle; padding-right:10px;">
                                     <strong>Video</strong>
                                 </div>
                                 <div style="margin-bottom:20px;">
                                     <p style="margin-top:10px;">
                                         <strong>Resolucion de pantalla</strong>: <a href="#." onClick="getAvailableResolutions();">mostrar disponibles</a> &nbsp; | &nbsp;
                                         Elegir resolución: <input type="text" id="resolutionW" style="width:40px;"> x <input type="text" id="resolutionH" style="width:40px;"> &nbsp;
                                         <a href="#." onClick="changeResolution();">salvar</a>. Resolución de imagen al máximo, cambiar sólo si la resolución mostrada es incorrecta.
                                     </p>
                                     <p style="margin-top:10px;">
                                         <strong>Rotacion de video</strong>:
                                         <input type="radio" name="videoRotationRadio" id="videoRotationRadioNormal" style="width:15px;" <?php echo $iwkVideoRotationNormal;?>> normal &nbsp; | &nbsp;
                                         <input type="radio" name="videoRotationRadio" id="videoRotationRadioLeft" style="width:15px;" <?php echo $iwkVideoRotationLeft;?>> izquierda &nbsp; | &nbsp;
                                         <input type="radio" name="videoRotationRadio" id="videoRotationRadioRight" style="width:15px;" <?php echo $iwkVideoRotationRight;?>> derecha &nbsp; | &nbsp;
                                         <input type="radio" name="videoRotationRadio" id="videoRotationRadioReverse" style="width:15px;" <?php echo $iwkVideoRotationReverse;?>> inversa &nbsp; | &nbsp;
                                         <a href="#." onClick="videoRotationSet();">aplicar rotación</a> (los cambios aparecerán tras reiniciar).
                                      </p>
                                 </div>

                                 <!-- Sound -->
                                 <div class="title">
                                     <img src="Images/Document-music-playlist-icon.png" style="vertical-align:middle; padding-right:10px;">
                                     <strong>Sonido</strong>
                                 </div>
                                 <div style="margin-bottom:20px;">
                                     <p><strong>Volumen</strong>: <a href="#." onClick="soundSet();">open mixer</a>.</p>
                                 </div>

                                 <!-- Browser -->
                                 <!--
                                 <div class="title">
                                     <img src="Images/Internet-icon.png" style="vertical-align:middle; padding-right:10px;">
                                     <strong>Chrome settings</strong>
                                 </div>
                                 <div style="margin-bottom:20px;">
                                     <p>Fullscreen kiosk mode is a restricted Chrome fullscreen view. In order to add extensions or settings to the Chrome browser itself, you can do it right here and then persist.</p>
                                     <p>Make all Chromium settings <a href="#." onClick="makeBrowserHomePersistent(true);">persistent</a>: all <strong>modifies you bring to Chrome will be saved</strong> &nbsp; | &nbsp; toggle to <a href="#." onClick="makeBrowserHomePersistent(false);">default</a> again.</p>
                                 </div>
                                    -->

                                 <!-- Digital Signage -->
                                <div style="display: none">
                                    <input id="skmPage" type="text" value="<?php echo $iwkStrictModeURL;?>" style="width:200px;"> &nbsp; | &nbsp;
                                    <input id="skmAddMacAddress" type="checkbox" style="width:15px;" <?php echo $iwkAddToken;?>> <span style="margin-left:187px;"> &nbsp; | &nbsp; this allows multiple deploys pointing just one target/server URL </span>
                                    <input id="skmInactivityBeforeReload" type="text" value="<?php echo $iwkRefreshTimeout;?>" style="width:200px;"> &nbsp; | &nbsp; values in minutes. Zero value (0) means never reset.
                                    <input id="skmForcePageReload" type="text" value="<?php echo $iwkPageReloadTimeout;?>" style="width:200px;"> &nbsp; | &nbsp; values in seconds. Zero value (0) means never reload. Min. value is 5s.
                                    <input id="skmDisableAllInput" type="checkbox" style="width:15px;" <?php echo $iwkDisabledInput;?>> <span style="margin-left:187px;"> &nbsp; | &nbsp; setting will be applied after 2 miutes of kiosk display.</span>
                                    <input type="radio" name="virtualKeyboardRadio" id="virtualKeyboardSetOff" style="width:15px;" <?php echo $iwkVirtualKeyboardFileOff;?>> Off &nbsp;&nbsp;&nbsp;
                                    <input type="radio" name="virtualKeyboardRadio" id="virtualKeyboardSetOn" style="width:15px;" <?php echo $iwkVirtualKeyboardFileOn;?>> On
                                    <input id="skmHTTPProxy" type="text" value="<?php echo $iwkAppProxy;?>" style="width:200px;"> &nbsp; | &nbsp; do not use http://; always specify port. Example: 192.168.1.100:8080
                                    <input id="skmSystemHalt" type="text" value="<?php echo $iwkSystemHaltHour;?>" style="width:200px;"> &nbsp; | &nbsp; Formato UTC 24h: hh:mm. Valor cero(0) significa nunca.
                                </div>

                             <p style="margin-top:20px; margin-bottom:10px; height:26px; padding-top:10px; text-align:center;">
                                 <a href="#." onClick="kioskModeSet(false); document.getelementById('kmsStart').style.display='none';"><span id="kmsStart" class="title" style="font-size:30px;">INICIAR ADDMEETOO</span></a>
                             </p>
                         </div>
                     </div>
                 </div>
             </div>

        </div>

    </div>

</body>
</html>