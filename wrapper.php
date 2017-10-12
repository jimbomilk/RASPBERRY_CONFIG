<?php

$url = file_get_contents("/iwk/iwk.strictKioskURL");

$pageRefreshTime = (int)file_get_contents("/iwk/iwk.strictKioskReloadPageTimer")*1000; // ms.
if ($pageRefreshTime<5000) $pageRefreshTime = 5000; // min 5 secs. refresh.

// ************************************************************************************************ ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <script type="text/javascript">
        window.setInterval("document.getElementById('r').src='<?php echo $url;?>';",<?php echo $pageRefreshTime;?>);
    </script>

    <frameset cols="0,*" frameborder="0">
        <frame id="l">
        <frame id="r" src="<?php echo $url;?>">
    </frameset>
</html>

