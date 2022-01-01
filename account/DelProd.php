<?php

require_once($_SERVER['DOCUMENT_ROOT']."/dl/global/engine.php");
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/fileManager.php");
$dl = new dlengine();
$writingNew = true;
$dl->partner();
$filing = new fileEngine($dl->user->user);

$id = 0;
$id = substr(preg_replace("/[^0-9]/", "", $_GET['id']), 0, 55);
$dl->checkOwner($id);

$filing->delete($id, "461");
$filing->delete($id, "462");

$query = "UPDATE products SET status = 'deleted' WHERE id = $id";
if ($dl->dlconn->query($query)) {
  $dl->go("Publish?i=proddel", "p");
}
$dl->go("Product?id=$id&i=delfail", "p");

?>
