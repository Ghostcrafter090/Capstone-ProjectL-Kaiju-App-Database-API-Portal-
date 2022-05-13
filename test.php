<?php
echo "<form action=\"interface/index.php?comm=get&userid=random&sessionid=". $_GET['sessionid']. "&table=Pet\" method=\"POST\">";
?>
    <input type="text" name="data">
    <input type="submit" name="submit">
</form>

<?php
echo date("Y-m-d H:m:s");