<?php
namespace Dompdf;
require_once 'dompdf/autoload.inc.php';
session_start();
ob_start();
require_once('includes/configpdo.php');
error_reporting(0);
?>

<html>
<style>
body {
  padding: 4px;
  text-align: center;
}

table {
  width: 100%;
  margin: 10px auto;
  table-layout: auto;
}

.fixed {
  table-layout: fixed;
}

table,
td,
th {
  border-collapse: collapse;
}

th,
td {
  padding: 1px;
  border: solid 1px;
  text-align: center;
}


</style>
<?php $rollid=$_SESSION['rollid'];
$classid=$_SESSION['classid'];
$qery = "SELECT   tblstudents.StudentName,tblstudents.RoolId,tblstudents.Dob,tblstudents.StudentEmail,tblstudents.Status,tblclasses.ClassName,tblclasses.ClassNameNumeric,tblclasses.Section from tblstudents join tblclasses on tblclasses.id=tblstudents.ClassId where tblstudents.RoolId=? and tblstudents.ClassId=?";
$stmt21 = $mysqli->prepare($qery);
$stmt21->bind_param("ss",$rollid,$classid);
$stmt21->execute();
                 $res1=$stmt21->get_result();
                 $cnt=1;
                   while($result=$res1->fetch_object())
                  {  ?>
<p><b>Student Name :</b> <?php echo htmlentities($result->StudentName);?></p>
<p><b>Student Rool Id :</b> <?php echo htmlentities($result->RollId);?>
<p><b>Student Class:</b> <?php echo htmlentities($result->ClassName);?>(<?php echo htmlentities($result->Section);?>)
<?php }

    ?>
 <table class="table table-inverse" border="1">
                      
                                                <table class="table table-hover table-bordered">
                                                <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Subject</th>    
                                                            <th>Marks</th>
                                                        </tr>
                                               </thead>
  


                                                  
                                                  <tbody>
<?php                                              
// Code for result
 $query ="select tblstudent.StudentName,tblstudent.RoolId,tblstudent.ClassId,tblresult.marks,tblresult.SubjectId,tblsubjects.SubjectName from (select tblstudent.StudentName,tblstudent.RoolId,tblstudent.ClassId,tblresult.marks,tblresult.SubjectId from tblstudents as tblstudent join  tblresult as tblreslt on tblresult.StudentName=tblstudent.Studentname) as t join tblsubjects on tblsubjects.Subjectname=tblresult.SubjectId where (tblstudent.RoolId=? and tblstudent.ClassId=?)";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ss",$rollid,$classid);
$stmt->execute();
                 $res=$stmt->get_result();
                 $cnt=1;
                   while($row=$res->fetch_object())
                  {

    ?>

                                                    <tr>
                                                <td ><?php echo htmlentities($cnt);?></td>
                                                      <td><?php echo htmlentities($row->SubjectName);?></td>
                                                      <td><?php echo htmlentities($totalmarks=$row->marks);?></td>
                                                    </tr>
<?php 
$totlcount+=$totalmarks;
$cnt++;}
?>
<tr>
                                                <th scope="row" colspan="2">Total Marks</th>
<td><b><?php echo htmlentities($totlcount); ?></b> out of <b><?php echo htmlentities($outof=($cnt-1)*100); ?></b></td>
                                                        </tr>
<tr>
                                                <th scope="row" colspan="2">Percentage</th>           
                                                            <td><b><?php echo  htmlentities($totlcount*(100)/$outof); ?> %</b></td>
                                                             </tr>

                            </tbody>
                        </table>
                    </div>
</html>

<?php
$html = ob_get_clean();
$dompdf = new DOMPDF();
$dompdf->setPaper('A4', 'landscape');
$dompdf->load_html($html);
$dompdf->render();
//dompdf->stream("",array("Attachment" => false));
$dompdf->stream("result.pdf");
?>