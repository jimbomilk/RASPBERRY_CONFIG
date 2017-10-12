<?php
// Defining variables: load system persistent state.

    // Strict kiosk URL.
    $iwkStrictModeURL = trim(file_get_contents("/iwk/iwk.strictKioskBaseURL"));
    if (!$iwkStrictModeURL || $iwkStrictModeURL=="http://www.binaryemotions.com") $iwkStrictModeURL = "http://";

    // Refresh timeout.
    $iwkRefreshTimeout = file_get_contents("/iwk/iwk.refreshTimeout");
    $iwkRefreshTimeout = (int)($iwkRefreshTimeout/60/1000);

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
    if (file_exists("/iwk/iwk.systemHaltAt")) $iwkSystemHaltContent = trim(file_get_contents("/iwk/iwk.systemHaltAt"));

    if ($iwkSystemHaltContent) $iwkSystemHaltHour = $iwkSystemHaltContent;
    else $iwkSystemHaltHour = 0;

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
    <title>Raspberry Digital Signage - settings page</title>
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
            var unlockpwd = document.getElementById('adminPassword').value;
            if (!unlockpwd) 
                {
                alert("In order to change settings, please enter unlock password. ");
                document.getElementById('languageSelector').selectedIndex = 0;
                return false;
                }

            dojo.xhr("GET", {
                url: "backend.php?target=locale&lang="+lang+"&unlockPwd="+encodeURIComponent(unlockpwd),
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
                var unlockpwd = document.getElementById('adminPassword').value;
                if (!unlockpwd)
                    {
                    alert("In order to change settings, please enter unlock password. ");
                    return false;
                    }

                dojo.xhr("GET", {
                    url: "backend.php?target=video&action=changeResolution&w="+resolutionW+"&h="+resolutionH+"&unlockPwd="+encodeURIComponent(unlockpwd),
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
            var unlockpwd = document.getElementById('adminPassword').value;
            if (!unlockpwd)
                {
                alert("In order to change settings, please enter unlock password. ");
                return false;
                }

            dojo.xhr("GET", {
                url: "backend.php?target=sound&unlockPwd="+encodeURIComponent(unlockpwd),
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
            var unlockpwd = document.getElementById('adminPassword').value;
            if (!unlockpwd)
                {
                alert("In order to change password, please enter the current admin password. ");
                return false;
                }

            if (!dontuse) var newAdminPassword = prompt("Insert new password"); // prompt for password.
            else var newAdminPassword = "no-passwd";

            if (newAdminPassword)
                {
                dojo.xhr("GET", {
                    url: "backend.php?target=modifyPasword&newPassword="+encodeURIComponent(newAdminPassword)+"&unlockPwd="+encodeURIComponent(unlockpwd),
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
                var unlockpwd = document.getElementById('adminPassword').value;
                if (!unlockpwd)
                    {
                    alert("Please enter admin password, first. ");
                    return false;
                    }

                dojo.xhr("GET", {
                    url: "backend.php?target=browser&action=makeHomePersistent&toggle="+toggle+"&unlockPwd="+unlockpwd,
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
            var unlockpwd = document.getElementById('adminPassword').value;
            if (!unlockpwd)
                {
                alert("In order to change settings, please enter unlock password. ");
                return false;
                }

            if (!confirm("This will reboot system now, continue? ")) return true;

            if (document.getElementById('videoRotationRadioNormal').checked) var rotation = "normal";
            if (document.getElementById('videoRotationRadioLeft').checked) var rotation = "left";
            if (document.getElementById('videoRotationRadioRight').checked) var rotation = "right";
            if (document.getElementById('videoRotationRadioReverse').checked) var rotation = "inverted";

            dojo.xhr("GET", {
                url: "backend.php?target=video&action=rotate&rotation="+rotation+"&unlockPwd="+encodeURIComponent(unlockpwd),
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
            var unlockpwd = document.getElementById('adminPassword').value;
            if (!directBoot)
                {
                if (!unlockpwd)
                    {
                    alert("In order to change settings, please enter unlock password. ");
                    return false;
                    }
                }

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

            var uri = "backend.php?target=strictKiosk&url="+encodeURIComponent(skmURL)+"&token="+skmAddMacAddress+"&directStart="+directStart+"&timeout="+skmTimeout+"&cache="+skmCache+"&keyboard="+skmVirtualKeyboard+"&pageReload="+skmPageTimeout+"&haltAt="+skmSystemHalt+"&blankingTime="+iwkScreenblankingTime+"&disableInput="+skmDisableAllInput+"&proxy="+encodeURIComponent(skmProxy)+"&unlockPwd="+encodeURIComponent(unlockpwd);

            dojo.xhr("GET", {
                url: uri,
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

<body class="soria" style="height:92%;">
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
                                 <div class="title">
                                     <img src="Images/Button-empathy-icon.png" style="vertical-align:middle; padding-right:10px;">
                                     <strong>Locale and keyboard</strong>
                                 </div>
                                 <div style="margin-bottom:20px;">
                                     <p>The following settings will change the browser locale and keymap used. This admin interface will remain English only.</p>
                                     <p>
                                         Set system <strong>localization</strong>: &nbsp;
                                         <select id="languageSelector" onChange="if (this.options[this.selectedIndex].value) localeSet(this.options[this.selectedIndex].value);" style="width:130px;">
                                             <option value=""> Select your locale...</option>
                                             <option value="cn"> Chinese (use Google Input Tools)</option>
                                             <option value="cz"> Czech</option>
                                             <option value="dk"> Danish</option>
                                             <option value="gb"> English (GB)</option>
                                             <option value="us"> English (US)</option>
                                             <option value="fr"> French</option>
                                             <option value="de"> German</option>
                                             <option value="it"> Italian</option>
                                             <option value="jp"> Japanese (use Google Input Tools)</option>
                                             <option value="br"> Portuguese (BR)</option>
                                             <option value="pt"> Portuguese (PT)</option>
                                             <option value="ru"> Russian</option>
                                             <option value="es"> Spanish (default)</option>
                                             <option value="sk"> Slovak</option>
                                         </select>
                                     </p>
                                 </div>

                                 <!-- Video -->
                                 <div class="title">
                                     <img src="Images/Window-remote-desktop-icon.png" style="vertical-align:middle; padding-right:10px;">
                                     <strong>Video</strong>
                                 </div>
                                 <div style="margin-bottom:20px;">
                                     <p style="margin-top:10px;">
                                         <strong>Screen resolution</strong>: <a href="#." onClick="getAvailableResolutions();">show available</a> &nbsp; | &nbsp;
                                         Select resolution: <input type="text" id="resolutionW" style="width:40px;"> x <input type="text" id="resolutionH" style="width:40px;"> &nbsp;
                                         <a href="#." onClick="changeResolution();">save and use</a>. RDS displays at maximum resolution possible. Change only if the resulting resolution is incorrect.
                                     </p>
                                     <p style="margin-top:10px;">
                                         <strong>Video rotation</strong>:
                                         <input type="radio" name="videoRotationRadio" id="videoRotationRadioNormal" style="width:15px;" <?php echo $iwkVideoRotationNormal;?>> normal &nbsp; | &nbsp;
                                         <input type="radio" name="videoRotationRadio" id="videoRotationRadioLeft" style="width:15px;" <?php echo $iwkVideoRotationLeft;?>> left &nbsp; | &nbsp;
                                         <input type="radio" name="videoRotationRadio" id="videoRotationRadioRight" style="width:15px;" <?php echo $iwkVideoRotationRight;?>> right &nbsp; | &nbsp;
                                         <input type="radio" name="videoRotationRadio" id="videoRotationRadioReverse" style="width:15px;" <?php echo $iwkVideoRotationReverse;?>> reverse &nbsp; | &nbsp;
                                         <a href="#." onClick="videoRotationSet();">apply rotation</a> (setting will be applied after a reboot).
                                      </p>
                                 </div>

                                 <!-- Sound -->
                                 <div class="title">
                                     <img src="Images/Document-music-playlist-icon.png" style="vertical-align:middle; padding-right:10px;">
                                     <strong>Sound</strong>
                                 </div>
                                 <div style="margin-bottom:20px;">
                                     <p>Set <strong>sound volume</strong>: <a href="#." onClick="soundSet();">open mixer</a>.</p>
                                 </div>

                                 <!-- Browser -->
                                 <div class="title">
                                     <img src="Images/Internet-icon.png" style="vertical-align:middle; padding-right:10px;">
                                     <strong>Chrome settings</strong>
                                 </div>
                                 <div style="margin-bottom:20px;">
                                     <p>Fullscreen kiosk mode is a restricted Chrome fullscreen view. In order to add extensions or settings to the Chrome browser itself, you can do it right here and then persist.</p>
                                     <p>Make all Chromium settings <a href="#." onClick="makeBrowserHomePersistent(true);">persistent</a>: all <strong>modifies you bring to Chrome will be saved</strong> &nbsp; | &nbsp; toggle to <a href="#." onClick="makeBrowserHomePersistent(false);">default</a> again.</p>
                                 </div>

                                 <!-- Digital Signage -->
                                 <div class="title">
                                     <img src="Images/Internet-icon.png" style="vertical-align:middle; padding-right:10px;">
                                     <strong>Fullscreen page (kiosk mode) settings</strong>
                                 </div>

                                 <div style="margin-top:10px;">
                                     <table>
                                         <tr>
                                             <td>Display the following <strong>URL</strong>:</td>
                                             <td>&nbsp;</td>
                                             <td><input id="skmPage" type="text" value="<?php echo $iwkStrictModeURL;?>" style="width:200px;"> &nbsp; | &nbsp; insert an Internet or LAN URL. See site FAQ to host web resources inside the R-Pi SD card. </td>
                                         </tr>
                                         <tr>
                                             <td>Add MAC address at the end of the URL:</td>
                                             <td>&nbsp;</td>
                                             <td><input id="skmAddMacAddress" type="checkbox" style="width:15px;" <?php echo $iwkAddToken;?>> <span style="margin-left:187px;"> &nbsp; | &nbsp; this allows multiple deploys pointing just one target/server URL (<a title="For example: http://yourserver.com?id=mac-address. It is up to your local/remote server logic to return the appropriate content.">?</a>)</span>.</td>
                                         </tr>
                                         <tr>
                                             <td><strong>Reset all browser data</strong> after user inactivity of:</td>
                                             <td>&nbsp;</td>
                                             <td><input id="skmInactivityBeforeReload" type="text" value="<?php echo $iwkRefreshTimeout;?>" style="width:200px;"> &nbsp; | &nbsp; values in minutes. Zero value (0) means never reset. <span style="color:#D00;">(Full version)</span>.</td>
                                         </tr>
                                         <tr>
                                             <td>Force <strong>reloading of web page content</strong> every:</td>
                                             <td>&nbsp;</td>
                                             <td><input id="skmForcePageReload" type="text" value="<?php echo $iwkPageReloadTimeout;?>" style="width:200px;"> &nbsp; | &nbsp; values in seconds. Zero value (0) means never reload. Min. value is 5s (<a title="Please note that some sites (as Google services for example) refuse to work within frames, so they cannot be forced to refresh.">?</a>). <span style="color:#D00;">(Full version)</span>.</td>
                                         </tr>
                                         <tr>
                                             <td>Completely <strong>disable mouse/keyboard</strong> input:</td>
                                             <td>&nbsp;</td>
                                             <td><input id="skmDisableAllInput" type="checkbox" style="width:15px;" <?php echo $iwkDisabledInput;?>> <span style="margin-left:187px;"> &nbsp; | &nbsp; setting will be applied after 2 miutes of kiosk display.</span></td>
                                         </tr>
                                         <tr>
                                             <td>Set on-screen <strong>virtual keyboard</strong>, US layout:</td>
                                             <td>&nbsp;</td>
                                             <td>
                                                 <input type="radio" name="virtualKeyboardRadio" id="virtualKeyboardSetOff" style="width:15px;" <?php echo $iwkVirtualKeyboardFileOff;?>> Off &nbsp;&nbsp;&nbsp;
                                                 <input type="radio" name="virtualKeyboardRadio" id="virtualKeyboardSetOn" style="width:15px;" <?php echo $iwkVirtualKeyboardFileOn;?>> On
                                             </td>
                                         </tr>
                                         <tr>
                                             <td>Set application-level <strong>HTTP proxy</strong> URL:</td>
                                             <td>&nbsp;</td>
                                             <td><input id="skmHTTPProxy" type="text" value="<?php echo $iwkAppProxy;?>" style="width:200px;"> &nbsp; | &nbsp; do not use http://; always specify port. Example: 192.168.1.100:8080 <span style="color:#D00;">(Full version)</span>.</td>
                                         </tr>
                                         <tr>
                                             <td><strong>Halt</strong> system every day at:</td>
                                             <td>&nbsp;</td>
                                             <td><input id="skmSystemHalt" type="text" value="<?php echo $iwkSystemHaltHour;?>" style="width:200px;"> &nbsp; | &nbsp; UTC 24h time; format: hh:mm. Zero value (0) means never halt. <span style="color:#D00;">(Full version)</span>.</td>
                                         </tr>

                                     </table>
                                 </div>


                             </div>

                             <p style="margin-top:20px; margin-bottom:10px; height:26px; padding-top:10px; text-align:center;">
                                 <a href="#." onClick="kioskModeSet(false); document.getelementById('kmsStart').style.display='none';"><span id="kmsStart" class="title" style="font-size:30px;">Start ADDMEETOO</span></a>
                             </p>
                         </div>
                     </div>
                 </div>
             </div>




        </div>

        <div style="padding-top:10px; color:black;display: none">
            <strong>&raquo; Unlock settings modify</strong>: <input id="adminPassword" type="password" value="<?php if ($passwordDisabled) echo "no-passwd";?>" style="padding-left:5px; height:20px; width:105px;" <?php if ($passwordDisabled) echo "disabled";?>> &nbsp; | &nbsp; only with admin password you can change system settings | <a href="#." onClick="modifyPassword(false);">change password</a> | <a href="#." onClick="modifyPassword(true);">do not use password</a>, anyone can change settings.
        </div>
    </div>

</body>
</html>