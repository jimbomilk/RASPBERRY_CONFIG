<?php

class Utils
{
    // ***************************************************************************************************************************
    // PUBLIC METHODS
    // ***************************************************************************************************************************

    // ;

    // ***************************************************************************************************************************
    // PUBLIC STATIC METHODS
    // ***************************************************************************************************************************

    public static function writeFile(/* String */ $file,/* String */ $content,/* String */ $permissions) /* Boolean */
    {
        if ($fp = fopen($file,"w"))
            {
            fwrite($fp,$content);
            fclose($fp);

            if ($permissions="777")
                {
                shell_exec("sudo chmod 777 ".$file);
                }

            return true;
            }

        else return false;
    }


    public static function addFile(/* String */ $file,/* String */ $content,/* String */ $permissions) /* Boolean */
    {
        if ($fp = fopen($file,"a+"))
        {
            fwrite($fp,$content);
            fclose($fp);

            if ($permissions="777")
            {
                shell_exec("sudo chmod 777 ".$file);
            }

            return true;
        }

        else return false;
    }
}

?>
