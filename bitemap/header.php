<?php session_start();
$log = 0;
if(!empty($_SESSION['user'])){
  $user = $_SESSION['user'];
  $log = 1;
}
?>
<div class="header">
        <video autoplay loop class="background-video" muted plays-inline>
            <source src="image/background.mp4" type="video/mp4">
        </video>
        <nav>
        <h1 class="logt">BITE<span>MAP</span></h1>
            <ul class="nav-links">
                <li><a href="http://localhost/project/bitemap.php">HOME</a></li>
                <li><a href="#">OUR FEATURES</a></li>
                <li><a href="#">RESERVATION</a></li>
                <li><a href="#">OFFERS</a></li>
                <li><a href="localhost/project/signuphome.php">ABOUT US</a></li>
                
            


        <?php if($log):?>
    
        <li><a href="#">BLOG</a></li>
        <li><a href="localhost/project/signuphome.php">INBOX</a></li>
        <li><a href="localhost/project/signuphome.php">##</a></li>
      </ul>
        <button type="submit" class ="navbutton"><a href="http://localhost/project/fuctions/logout.php">LOG OUT</a></button>
        
        <?php else: ?>
        <button type="submit"class ="navbutton"><a href="http://localhost/project/login.php">LOG IN</a></button>
                <button type="submit"class ="navbutton"><a href="http://localhost/project/signuphome.php">SIGN UP</a></button>            
        <?php endif;?>
        </span>
        </nav>
