<style>
.diff td{
  vertical-align : top;
  white-space    : pre;
  white-space    : pre-wrap;
  font-family    : monospace;
}
.diff{
	width:100%;
}
.diffDeleted{
    background:pink;
}
.diffInserted{
    background:lightgreen;
}
</style>

<?php 

$old_txt = $_SERVER['DOCUMENT_ROOT'].'/'.$_GET['oldfile'];
$new_txt = $_SERVER['DOCUMENT_ROOT'].'/'.$_GET['file'];
require_once 'src/class.Diff.php';
// compare two files line by line
echo Diff::toTable(Diff::compareFiles($old_txt, $new_txt));

?>
