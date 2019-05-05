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


    if(mysqli_query($conn, "insert into data values (null, 2, 2, '$ss', '$rcv', '$r')"))
    {
            echo "<script>alert('Data Inserted Successfully, Start Simulating!');</script>";
    }
    else
    {
        echo "<script>alert('Could not insert data');</script>";
    }

}

if(isset($_POST['cr']))
{
  

    $q = mysqli_query($conn, "select * from server where app_read = 0 ");
    
    $num = mysqli_num_rows($q);
    
    $q2 = mysqli_query($conn, "select * from data");
    
    $row = mysqli_fetch_array($q2);

    $rwnd = $row[4] - $num;

    $cwnd = $row[2];

    $ack  = $row[1];


    if($cwnd > $rwnd)
    {
        echo "<script>alert('Flow Control - Receiver buffer can not contain all packets.');</script>";
    }
    else
    {
        $i_ack = $ack;

        for($i = 0; $i < $cwnd; $i++)
        {

            $q3 = mysqli_query($conn, "select * from file where id = '$i_ack'");

            $row3 = mysqli_fetch_array($q3);
            $message  = $row3[1];
            $seq = $row3[0];
            if(mysqli_query($conn, "insert into router values (null, '$message', '$seq')"))
            {

            }
            else
            {
                 echo "<script>alert('error while Inserting');</script>";
            }

            $i_ack++;


        } 
    }


}


if(isset($_POST['transfer']))
{
  

    $q = mysqli_query($conn, "select * from server where app_read = 0 ");
    
    $num = mysqli_num_rows($q);
    
    $q2 = mysqli_query($conn, "select * from data");
    
    $row = mysqli_fetch_array($q2);

    $rwnd = $row[4] - $num;

    $cwnd = $row[2];

    $ack  = $row[1];


    
        $i_ack = $ack;

        for($i = 0; $i < $cwnd; $i++)
        {

            $q3 = mysqli_query($conn, "select * from file where id = '$i_ack'");

            $row3 = mysqli_fetch_array($q3);
            $message  = $row3[1];
            $seq = $row3[0];
            if(mysqli_query($conn, "insert into client values (null, '$message', '$seq')"))
            {

            }
            else
            {
                 echo "<script>alert('error while Inserting');</script>";
            }

            $i_ack++;


        } 
    


}

if(isset($_POST['rs']))
{
   $q =  mysqli_query($conn, "select * from router");

   while($row = mysqli_fetch_array($q))
   {
        $message = $row[1];
        $q2 = mysqli_query($conn, "select seq from server where id = (select MAX(id) from server where seq != 'NA')");

        if(mysqli_num_rows($q2) == 0)
        {
            $seq = $row[2];
        }
        else
        {
            $row2 = mysqli_fetch_array($q2);

            $qq = mysqli_query($conn, "select * from temp");
            $roww = mysqli_fetch_array($qq);
            if($roww[0]!=0)
            {
                $seq = $row[2] +  $roww[0] + 1;
            }
            else
            { 
                $seq = $row2[0]  + 1;

            }  
        }
       

        $idd = $row[0];
        if(mysqli_query($conn, "insert into server values(null, '$message', '$seq',0, 'Success' )"))
        {
            if(mysqli_query($conn,"delete from router where id = '$idd'"))
            {

            }
            else{
                echo "<script>alert('error while deleting');</script>";
            }
        }  
        else{
            echo "<script>alert('error while Inserting');</script>";
        } 

      
   }

        $q2 = mysqli_query($conn, "select seq from server where id = (select MAX(id) from server where seq != 'NA')");

        if(mysqli_num_rows($q2) == 0)
        {
            $seq = $row[2];
        }
        else
        {
             $row2 = mysqli_fetch_array($q2);

        $seq = $row2[0];
        }
       
       $ack = $seq + 1;

       $q3 = mysqli_query($conn, "select * from data");
       $row3 = mysqli_fetch_array($q3);

        if($cwnd>=$row3[3])
        {
              $cwnd = $row3[2] +1;
        }
        else
        {
              $cwnd = $row3[2] * 2;
        }
      

       if(mysqli_query($conn, "update data set ack = '$ack', cwnd = '$cwnd' "))
       {

       }
       else
       {
        echo "<script>alert('error while updating');</script>";
       }

             mysqli_query($conn,"update temp set ack = 0");
             mysqli_query($conn, "insert into graph values (null,'$cwnd')");







}


if(isset($_POST['lose']))
{
   $q =  mysqli_query($conn, "select * from router");

   $count  = 0;
   $check = 0;
   $temp  = 0;
   $counter = 0;

   while($row = mysqli_fetch_array($q))
   {
        $message = $row[1];
        $q2 = mysqli_query($conn, "select seq from server where id = (select MAX(id) from server where seq != 'NA')");

        if(mysqli_num_rows($q2) == 0)
        {
            $seq = $row[2];
        }
        else
        {
             $row2 = mysqli_fetch_array($q2);

        if($check == 1)
        {
            $seq = $temp;
            $counter++;
        }
        else
        {
            $qqq = mysqli_query($conn, "select * from data");
            $rowww = mysqli_fetch_array($qqq);

            $seq = $rowww[1];  
            $temp = $seq;  
        }
        
        }
       

        $idd = $row[0];

        if($count == 1)
        {
            if(mysqli_query($conn, "insert into server values(null, '$message', '$seq',0, 'Success')"))
            {
                if(mysqli_query($conn,"delete from router where id = '$idd'"))
                {

                }
                else{
                    echo "<script>alert('error while deleting');</script>";
                }
            }  
            else{
                echo "<script>alert('error while Inserting');</script>";
            } 
        }
        else{
            if(mysqli_query($conn, "insert into server values(null, '', 'NA',0, 'Packet Lost')"))
            {
                if(mysqli_query($conn,"delete from router where id = '$idd'"))
                {

                }
                else{
                    echo "<script>alert('error while deleting');</script>";
                }
            }  
            else{
                echo "<script>alert('error while Inserting');</script>";
            } 
           
                $count = 1;
                $check = 1;

        }   

      
   }

        $q2 = mysqli_query($conn, "select seq from server where id = (select MAX(id) from server)");

        if(mysqli_num_rows($q2) == 0)
        {
            $seq = $row[2];
        }
        else
        {
             $row2 = mysqli_fetch_array($q2);

        $seq = $row2[0];
        }
       
       $ack = $temp;

       $cwnd = 1;

       if(mysqli_query($conn, "update data set ack = '$ack', cwnd = '$cwnd' "))
       {

       }
       else
       {
        echo "<script>alert('error while updating');</script>";
       }


       mysqli_query($conn,"update temp set ack = '$counter'");
       mysqli_query($conn, "insert into graph values (null,'$cwnd')");





}



if(isset($_POST['timeout']))
{

    echo "<script>alert('Packet will be retransmitted after 5 seconds of time internal')</script>";
    sleep(5);
   $q =  mysqli_query($conn, "select * from router");

   $count  = 0;
   $check = 0;
   $temp  = 0;
   $counter = 0;

   while($row = mysqli_fetch_array($q))
   {
        $message = $row[1];
        $q2 = mysqli_query($conn, "select seq from server where id = (select MAX(id) from server where seq != 'NA')");

        if(mysqli_num_rows($q2) == 0)
        {
            $seq = $row[2];
        }
        else
        {
             $row2 = mysqli_fetch_array($q2);

        if($check == 1)
        {
            $seq = $temp;
            $counter++;
        }
        else
        {
            $seq = $row2[0]  + 1;  
            $temp = $seq;  
        }
        
        }
       

        $idd = $row[0];

        if($count == 1)
        {
            if(mysqli_query($conn, "insert into server values(null, '$message', '$seq',0, 'Success')"))
            {
                if(mysqli_query($conn,"delete from router where id = '$idd'"))
                {

                }
                else{
                    echo "<script>alert('error while deleting');</script>";
                }
            }  
            else{
                echo "<script>alert('error while Inserting');</script>";
            } 
        }
        else{
            if(mysqli_query($conn, "insert into server values(null, '', 'NA',0, 'Packet Lost')"))
            {
                if(mysqli_query($conn,"delete from router where id = '$idd'"))
                {

                }
                else{
                    echo "<script>alert('error while deleting');</script>";
                }
            }  
            else{
                echo "<script>alert('error while Inserting');</script>";
            } 
           
                $count = 1;
                $check = 1;

        }   

      
   }

        $q2 = mysqli_query($conn, "select seq from server where id = (select MAX(id) from server)");

        if(mysqli_num_rows($q2) == 0)
        {
            $seq = $row[2];
        }
        else
        {
             $row2 = mysqli_fetch_array($q2);

        $seq = $row2[0];
        }
       
       $ack = $temp;

       $cwnd = 1;

       if(mysqli_query($conn, "update data set ack = '$ack', cwnd = '$cwnd' "))
       {

       }
       else
       {
        echo "<script>alert('error while updating');</script>";
       }


       mysqli_query($conn,"update temp set ack = '$counter'");
       mysqli_query($conn, "insert into graph values (null,'$cwnd')");





}

if(isset($_POST['move']))
{
    $q = mysqli_query($conn, "select min(id) from server where seq != 'NA' and app_read = 0");
    $row = mysqli_fetch_array($q);
    $idd = $row[0];

    if(mysqli_query($conn, "update server set app_read = 1 where id = '$idd'"))
    {

    }
    else
    {
        echo "<script>alert('error while updating');</script>";
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
                        <div class=" col-md-offset-1 col-md-3 col-sm-3">
                            <div class="card card-box">

                                <div class="card-head">
                                    <header></header>
                                    <img src='client.jpg' width = 200 height = 150>
                                    <?php

                                    $q = mysqli_query($conn, "select count(*) from client");
                                    $row = mysqli_fetch_array($q);
                                   echo ' <br>Buffer:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
                                   for($i = 0;$i<$row[0]; $i++)
                                   {
                                    echo '<span class="glyphicon glyphicon-stop" aria-hidden="true"></span>
                                   ';
                                   }
                                   
                                    ?>
                                    
                                </div>
                            </div>
                        </div>
                         <div class="col-md-3 col-sm-3">
                            <div class="card card-box">

                                <div class="card-head">
                                    <header></header>
                                    <img src='router.jpg' width = 200 height = 150>
                                    <?php

                                    $q = mysqli_query($conn, "select count(*) from router");
                                    $row = mysqli_fetch_array($q);
                                   echo ' <br>Buffer:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
                                   for($i = 0;$i<$row[0]; $i++)
                                   {
                                    echo '<span class="glyphicon glyphicon-stop" aria-hidden="true"></span>
                                   ';
                                   }
                                   
                                    ?>
                                     
                                </div>
                            </div>
                        </div>
                         <div class="col-md-3 col-sm-3">
                            <div class="card card-box">

                                <div class="card-head">
                                    <header></header>
                                    <img src='server.png' width = 200 height = 150>
                                    <?php

                                    $q = mysqli_query($conn, "select count(*) from server where app_read = 0 ");
                                    $row = mysqli_fetch_array($q);
                                   echo ' <br>Buffer:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ';
                                   for($i = 0;$i<$row[0]; $i++)
                                   {
                                    echo '<span class="glyphicon glyphicon-stop" aria-hidden="true"></span>
                                   ';
                                   }
                                   
                                    ?>
                                </div>
                            </div>
                        </div>
                    


                    </div>

                                 <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="card card-box">

                                <div class="card-head">
                                    <header>Controls</header>
                                     <form action="#" method ="POST">
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type = 'submit' value ='Transfer' name = 'transfer' class='btn '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                  
                                        <input type = 'submit' value ='Client To Router' name = 'cr' class='btn btn-success'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    
                                 
                                        <input type = 'submit' value ='Router To Server' name = 'rs' class='btn btn-warning'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                   
                                        <input type = 'submit' value ='Three Duplicate Acknowledgement' name = 'lose' class='btn btn-info'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                                         <input type = 'submit' value ='TimeOut' name = 'timeout' class='btn btn-danger'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                     </form>
                                </div>
                            </div>
                        </div>
                    </div>
                                <div class="row">
                        <div class="col-md-4 col-sm-4">
                            <div class="card card-box">
                                <div class="card-head">
                                    <header>Client</header>
                                     <table class = "table table-striped table-bordered table-hover">
                                        <tr>
                                            <th class="text-centered">
                                           S.No                                     </th>
                                            <th class="text-centered">
                                            Message                                      </th>
                                        <th class="text-centered">
                                        Sequence
                                        </th>
                                       
                                                                                  
                                        </tr>
                                       <?php

                                        $q = mysqli_query($conn, "select * from client ");
                                        $i = 1;
                                        while($row = mysqli_fetch_array($q))
                                        {
                                            echo "<tr>
                                       
                                            <td class='text-centered'>".$i."</td>
                                            <td class='text-centered'>".$row[1]."</td>
                                            <td class='text-centered'>".$row[2]."</td>    
                                            
                                       ";
                                           
                                            
                                           
                                            $i++;
                                        }


                                       ?>

                                    </table>
                                  

                                </div>
                                
                            </div>
                        </div>


                        <div class="col-md-4 col-sm-4">
                            <div class="card card-box">
                                <div class="card-head">
                                    <header>Router Buffer</header>
                                     <table class = "table table-striped table-bordered table-hover">
                                        <tr>
                                            <th class="text-centered">
                                                S.No   
                                            </th>
                                            <th class="text-centered">
                                            Message                                      </th>
                                        <th class="text-centered">
                                        Sequence
                                        </th>
                                   
                                                                                  
                                        </tr>
                                         <?php

                                        $q = mysqli_query($conn, "select * from router ");
                                        $i = 1;
                                        while($row = mysqli_fetch_array($q))
                                        {
                                            echo "<tr>
                                       
                                            <td class='text-centered'>".$i."</td>
                                            <td class='text-centered'>".$row[1]."</td>
                                            <td class='text-centered'>".$row[2]."</td>    
                                            
                                       ";
                                           
                                            
                                           
                                            $i++;
                                        }


                                       ?>

                                    </table>
                                   

                                    

                                </div>
                                
                            </div>
                        </div>





                        <div class="col-md-4 col-sm-4">
                            <div class="card card-box">
                                <div class="card-head">
                                    <header>Server 


                                    </header>
                                     <table class = "table  table-bordered table-hover">
                                        <tr>
                                            <th class="text-centered">
                                           S.No                                    </th>
                                            <th class="text-centered">
                                            Message                                     </th>
                                        <th class="text-centered">
                                        Sequence
                                        </th>
                                   
                                        <th class="text-centered">
                                        Status
                                        </th>
                                                                                  
                                        </tr>
                                          <?php

                                        $q = mysqli_query($conn, "select * from server ");
                                        $i = 1;
                                        while($row = mysqli_fetch_array($q))
                                        {
                                            if($row[3] == '1')
                                            {
                                            echo "<tr style ='background-color:pink;'>
                                       
                                            <td class='text-centered'>".$i."</td>
                                            <td class='text-centered'>".$row[1]."</td>
                                            <td class='text-centered'>".$row[2]."</td>    
                                             <td class='text-centered'>".$row[4]."</td> 
                                            
                                       ";
                                            }
                                            else
                                            {
                                                echo "<tr>
                                       
                                            <td class='text-centered'>".$i."</td>
                                            <td class='text-centered'>".$row[1]."</td>
                                            <td class='text-centered'>".$row[2]."</td>    
                                             <td class='text-centered'>".$row[4]."</td> 
                                            
                                       ";

                                            }
                                           
                                            
                                           
                                            $i++;
                                        }


                                       ?>


                                        <form action="#" method ="POST">
                                       
                                        <input type = 'submit' value ='Move to Application Layer' name = 'move' class='btn btn-info'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                  
                                       
                                     </form>


                                    </table>
                                 
                                    

                                </div>
                                
                            </div>
                        </div>

                        <?php

                        $q =mysqli_query($conn, "select * from graph");
 $i = 1;
 $chart_data = '';
while($row = mysqli_fetch_array($q))
{
 $chart_data .= "{ number:'".$i."', profit:".$row[1]."}, ";
 $i++;
}

$chart_data = substr($chart_data, 0, -2);
?>



 <head>

  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
  
 </head>
 <body>

  <br /><br />
  <div class="container" style="width:900px;">
   <h2 align="center">Congestion Control Line Graph</h2>
 
   <br /><br />
   <div id="chart"></div>
  </div>
 </body>
</html>

<script>
Morris.Line({
 element : 'chart',
 data:[<?php echo $chart_data; ?>],
 xkey:'number',
 ykeys:['profit'],
 labels:['Congestion Window Size'],
 xlabels: 'RTT',
 hideHover:'auto',
 stacked:true,
 parseTime: false
});
</script>


							
							





                       













      