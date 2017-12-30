<?php

// Is password disabled?
/*$unlockPwdHashSaved = trim(file_get_contents("/iwk/iwk.adminPasswd"));
if ($unlockPwdHashSaved==trim(md5("no-passwd"))) */$passwordDisabled = true;
//else $passwordDisabled = false;

// ************************************************************************************************ ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Addmeetoo Digital Signage - settings page</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="Author" content="JMGCG">

    <link type="text/css" href="Styles/standard.css" rel="stylesheet">
    <link type="text/css" href="JavaScript/dojo/dijit/themes/tundra/tundra.css" rel="stylesheet">
    <link type="text/css" href="JavaScript/dojo/dijit/themes/soria/soria.css" rel="stylesheet">
    <link type="text/css" href="JavaScript/dojo/dojox/grid/resources/tundraGrid.css" rel="stylesheet">

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
        dojo.require("dojo.data.ItemFileReadStore");
        dojo.require("dojo.data.ItemFileWriteStore");
        dojo.require("dijit.form.ComboBox");
        dojo.require("dijit.TitlePane");
        dojo.require("dijit.layout.ContentPane");
        dojo.require("dijit.layout.TabContainer");
        dojo.require("dijit.form.FilteringSelect");
        dojo.require("dijit.form.Form");
        dojo.require("dojox.grid.DataGrid");
        dojo.require("dojox.grid.cells.dijit");
        dojo.require("dojox.layout.FloatingPane");

        // Grid layout.
        layoutWifiNetworks = [
                         {name:'Identificador', headerStyles:'text-align:center;', field:'ssid', styles:';', width:'40%'},
                         {name:'Dirección', headerStyles:'text-align:center;', field:'bssid', styles:';', width:'30%'},
                         {name:'Frecuencia', headerStyles:'text-align:center;', field:'rate', styles:';'},
                         {name:'Seguridad', headerStyles:'text-align:center;', field:'security', styles:';'},
                         {name:'Señal', headerStyles:'text-align:center;', field:'signal', styles:';'}
                         ];

        dojo.addOnLoad(function()
            {
            dojo.connect(dijit.byId('wifiNetworksGrid'), "onClick", function(item)
                {
                if (item.grid)
                    {
                    var valItem = item.grid.selection.getSelected()[0];
                    document.getElementById('netSSID').value = valItem.ssid;
                    document.getElementById('netSecurity').value = valItem.security;
                    }
                });

            // Initialize network with selected method (if none: DHCP).
            networkInit();
            });

        // JSON store retrivals.
        function __setStore(gridType)
            {
            if (gridType=="wifiNetworks") var uri = "backend.php?target=network&action=wifi-list";
            else return true;

            var store = new dojo.data.ItemFileWriteStore
                ({
                url: uri,
                urlPreventCache: true
                });

            return store;
            }

        function networkInit()
            {
            waitingState("on");

            dojo.xhr("GET", {
                url: "backend.php?target=network&action=init",
                preventCache: true,
                load: function(data,args)
                    {
                    viewNetworkInfo();
                    },
                timeout: 32000,
                error: function(error,args)
                    {
                    viewNetworkInfo();
                    }
                });
            }

        function networkSet(/* String */ _action, /* String */ ip, /* String */ mask, /* String */ gateway, /* String */ dns, /* String */ ssid, /* String */ password, /* String */ security)
        {

            waitingState("on");
            document.getElementById('networkLog').innerHTML = "Por favor espere, iniciando conexión...";


            dojo.xhr("GET", {
                url: "backend.php?target=network&action="+_action+"&netIP="+ip+"&netMask="+mask+"&netGateway="+gateway+"&netDNS="+dns+"&netSSID="+encodeURIComponent(ssid)+"&netPassword="+encodeURIComponent(password)+"&netSecurity="+security+"&unlockPwd="+encodeURIComponent(unlockpwd),
                preventCache: true,
                load: function(data,args)
                    {
                    viewNetworkInfo();
                    },
                timeout: 12000,
                error: function(error,args)
                    {
                    viewNetworkInfo();
                    }
                });
        }

        function viewNetworkInfo()
            {
            dojo.xhr("GET", {
                url: "backend.php?target=network&action=info",
                preventCache: true,
                load: function(data,args)
                {
                    if (data!="") {
                        document.getElementById('networkLog').innerHTML = data;

                        // Autostart on Internet connection OK, go to (other) settings page.
                        if (Utils.strpos(data, "conectada") > 0 || Utils.strpos(data, "running") > 0) {
                            document.getElementById('networkTabLeft').style.display = "none";
                            window.setTimeout("document.location.href='settings.php';", 8000);

                        }
                        else {
                            //wifiNetworkList('');
                            waitingState("off");
                        }
                    }
                },
                timeout: 10000,
                error: function(error,args) { ; }
                });
            }

        function wifiNetworkList()
            {
            jsonStoreWifiNetworks = __setStore("wifiNetworks");
            dijit.byId('wifiNetworksGrid').setStore(jsonStoreWifiNetworks);
            }

        function waitingState(/* String */ action)
            {
            if (action=="on")
                {
                document.getElementById('networkTabLeft').style.visibility = "hidden";
                document.body.style.cursor = "wait";
                }
            else
                {
                document.getElementById('networkTabLeft').style.visibility = "visible";
                document.body.style.cursor = "default";
                }
            }
    </script>
</head>

<body class="tundra" style="height:92%;">
    <div id="content" style="height:100%;">
         <div dojoType="dijit.layout.TabContainer" style="height:100%;">
             <!-- NETWORK TAB -->
             <div dojoType="dijit.layout.ContentPane" href="" title="<strong>Configuración de red</strong>" refreshOnShow="false" style="padding:15px;">
                 <div id="networkLog" class="title">
                     Espere por favor, inicializando red...
                 </div>

                 <div id="networkTabLeft">
                     <!-- Wired network settings - DHCP/Static -->
                     <div class="title">
                         <img src="Images/Window-remote-desktop-icon.png" style="vertical-align:middle; padding-right:10px;">
                         <strong>Red por cable</strong>
                     </div>
                     <div style="margin-bottom:10px;">
                         <p>Configura la<strong> red por cable</strong>:</p>
                         <p>
                             <ul>
                                 <li>
                                     <a href="#." onClick="networkInit();">reiniciar RED</a> - si no detecta redes WIFI intenta inicializar la red nuevamente.
                                 </li>
                             </ul>
                             <ul>
                                 <li>
                                     <a href="#." onClick="document.getElementById('staticIpTable').style.display='block';">utilizar IP estática</a>.
                                 </li>
                             </ul>
                         </p>

                         <div id="staticIpTable" style="display:none;">
                             <div style="padding-left:16px;">
                                 <table style="text-align:right;">
                                     <tr>
                                         <td>IP: </td>
                                         <td><input id="netIP" type="text"></td>
                                     </tr>
                                     <tr>
                                         <td>Mask: </td>
                                         <td><input id="netMask" type="text" value="255.255.255.0"></td>
                                     </tr>
                                     <tr>
                                         <td>Gateway: </td>
                                         <td><input id="netGateway" type="text"></td>
                                     </tr>
                                     <tr>
                                         <td>DNS: </td>
                                         <td><input id="netDNS" type="text" value="8.8.8.8"></td>
                                     </tr>
                                 </table>
                             </div>
                             <p>
                                 <ul style="padding-left:63px;">
                                     <li><a href="#." onClick="if (document.getElementById('netIP').value!='' && document.getElementById('netMask').value!='' && document.getElementById('netGateway').value!='' && document.getElementById('netDNS').value!='') { networkSet('static',document.getElementById('netIP').value,document.getElementById('netMask').value,document.getElementById('netGateway').value,document.getElementById('netDNS').value,'','',''); } else alert('Incomplete data. ');">use the above parameters</a>.</li>
                                 </ul>
                             </p>
                          </div>
                     </div>

                     <!-- Wireless network settings -->
                     <div class="title">
                         <img src="Images/Internet-icon.png" style="vertical-align:middle; padding-right:10px;">
                         <strong>Redes WIFI (802.11) detectadas</strong>
                     </div>
                     <div style="margin-bottom:10px;">
                         <ul>
                             <li>
                                 <a href="#." onClick="wifiNetworkList('');">Lista de redes WIFI</a>:
                             </li>
                         </ul>
                         <div dojoType="dojox.grid.DataGrid" jsid="wifiNetworksGrid" id="wifiNetworksGrid" query="{ bssid: '*' }" rowsPerPage="5" style="width:95%; height:220px; margin-top:5px;" structure="layoutWifiNetworks"></div>

                         <p>Por favor seleccione su red e introduzca su clave:</p>
                         <p style="margin-top:20px;">
                             <input id="netSSID" type="text" style="width:150px;" readonly> &nbsp;
                             <input id="netSecurity" type="text" style="width:150px;" readonly> &nbsp;
                             <input id="netPassword" type="text" style="width:250px;"> &nbsp; | &nbsp;
                             <a href="#." onClick="if (document.getElementById('netSSID').value!='') { networkSet('wifi','','','','',document.getElementById('netSSID').value,document.getElementById('netPassword').value,document.getElementById('netSecurity').value); }">conectar</a>
                         </p>
                     </div>
                 </div>
             </div>
        </div>


    </div>
</body>
</html>
