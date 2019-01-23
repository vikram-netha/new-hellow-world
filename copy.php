<?php
set_time_limit(0);
if(!@copy('http://www.foxandlee.com.au/fox-09-10.zip','foxlee.zip'))
{
    $errors= error_get_last();
    echo "COPY ERROR: ".$errors['type'];
    echo "<br />\n".$errors['message'];
} else {
    echo "File copied from remote!";
}
?>
