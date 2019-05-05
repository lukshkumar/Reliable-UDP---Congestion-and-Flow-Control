<head>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php 

require "conn_cn.php";

if(isset($_POST['submit']))
{
    $r = $_POST['rbuffer'];
    $rcv = $_POST['rcvbuffer'];
    $ss = $_POST['ssthresh'];


    if(mysqli_query($conn, "insert into data values (null, 1, 1, '$ss', '$rcv', '$r')"))
    {
            echo "<script>alert('Data Inserted Successfully, Start Simulating!');</script>";
    }
    else
    {
        echo "<script>alert('Could not insert data');</script>";
    }

}

include "header_cn.html";
?>


            <!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        
                    </div>

                                <!--
                                <div class="row">
                        <div class="col-md-6 col-sm-6">
                            <div class="card card-box">

                                <div class="card-head">
                                    <header>Counter (No of Packet Sent) : </header>
                                     <table class = "table table-striped table-bordered table-hover">

                                         <?php
                                        /*
                                        $q7 = mysqli_query($conn, "select count(*) from packet");
                                    
                                        while($row7 = mysqli_fetch_array($q7))
                                        {
                                            echo "<tr>
                                       
                                            <td class='text-centered'>".$row7[0]."</td>
                                            
                                       ";  
                                       
                                        }

                                        */


                                       ?>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                -->
                  <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="card card-box">

                                <div class="card-head">
                                    <header>Buffer Sizes</header>
                                     <form action="#" method ="POST">
                                        
                                        Router Buffer Size:   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="number" name ="rbuffer"><br>
                                        Receiver Buffer Size: &nbsp;&nbsp;<input type="number" name ="rcvbuffer"><br>
                                        Slow Start Threshold: <input type="number" name ="ssthresh"><br>
                                        
                                        <input type = 'submit' value ='Submit' name = 'submit' class='btn btn-info'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                     </form>
                                </div>
                            </div>
                        </div>
                    </div>


                         
                         


							
							





                       













      