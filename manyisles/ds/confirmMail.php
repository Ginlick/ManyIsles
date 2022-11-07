<?php
require_once($_SERVER['DOCUMENT_ROOT']."/Server-Side/db_accounts.php");

$hashedId = $_GET["id"];
$id = openssl_decrypt ($id, "aes-256-ctr", "Me234bauA&/", 0, "12gah522d:8efj5a");

if ($userrow = $conn->query(sprintf("SELECT emailConfirmed FROM accountsTable WHERE id='%s';", $id))) {
            while ($row = $userrow->fetch_row()) {
                    $conn->query(sprintf("UPDATE accountsTable SET emailConfirmed = true WHERE id= '%s'", $id));
            } 
}

$conn->close();

header("Location:checkout1.php";)
?>
