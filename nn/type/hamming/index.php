<?php
echo "<!DOCTYPE html>
<!-- saved from url=(0051)http://home.agh.edu.pl/~vlsi/AI/hamming_en/sim.html -->
<html>
<head>
	<title>Hamming Network</title>
	<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
	<link rel='stylesheet' href='style.css'>
	<script src='hamming.js'></script>
	<script>
	
 if (document.images)
 {
  pic1on= new Image(16,16);
  pic1on.src='img/black.gif';  
  pic2on= new Image(16,16);
  pic2on.src='img/black.gif';
  pic3on= new Image(16,16);
  pic3on.src='img/black.gif';  
  pic4on= new Image(16,16);
  pic4on.src='img/black.gif';  
  pic5on= new Image(16,16);
  pic5on.src='img/black.gif';  
  pic6on= new Image(16,16);
  pic6on.src='img/black.gif';  
  pic7on= new Image(16,16);
  pic7on.src='img/black.gif';  
  pic8on= new Image(16,16);
  pic8on.src='img/black.gif';  
  pic9on= new Image(16,16);
  pic9on.src='img/black.gif';  
  pic10on= new Image(16,16);
  pic10on.src='img/black.gif';  
  pic11on= new Image(16,16);
  pic11on.src='img/black.gif';  
  pic12on= new Image(16,16);
  pic12on.src='img/black.gif';  
  pic13on= new Image(16,16);
  pic13on.src='img/black.gif';  
  pic14on= new Image(16,16);
  pic14on.src='img/black.gif';  
  pic15on= new Image(16,16);
  pic15on.src='img/black.gif';  

  pic1off= new Image(16,16);
  pic1off.src='img/white.gif';
  pic2off= new Image(16,16);
  pic2off.src='img/white.gif';
  pic3off= new Image(16,16);
  pic3off.src='img/white.gif';
  pic4off= new Image(16,16);
  pic4off.src='img/white.gif';
  pic5off= new Image(16,16);
  pic5off.src='img/white.gif';
  pic6off= new Image(16,16);
  pic6off.src='img/white.gif';
  pic7off= new Image(16,16);
  pic7off.src='img/white.gif';
  pic8off= new Image(16,16);
  pic8off.src='img/white.gif';
  pic9off= new Image(16,16);
  pic9off.src='img/white.gif';
  pic10off= new Image(16,16);
  pic10off.src='img/white.gif';
  pic11off= new Image(16,16);
  pic11off.src='img/white.gif';
  pic12off= new Image(16,16);
  pic12off.src='img/white.gif';
  pic13off= new Image(16,16);
  pic13off.src='img/white.gif';
  pic14off= new Image(16,16);
  pic14off.src='img/white.gif';
  pic15off= new Image(16,16);
  pic15off.src='img/white.gif';
  // tu sie zaczyna...
  picgr1g0= new Image(16,16);
  picgr1g0.src='img/grey0.gif';
  picgr1g1= new Image(16,16);
  picgr1g1.src='img/grey1.gif';
  picgr1g2= new Image(16,16);
  picgr1g2.src='img/grey2.gif';
  picgr1g3= new Image(16,16);
  picgr1g3.src='img/grey3.gif';
  picgr1g4= new Image(16,16);
  picgr1g4.src='img/grey4.gif';

  picgr2g0= new Image(16,16);
  picgr2g0.src='img/grey0.gif';
  picgr2g1= new Image(16,16);
  picgr2g1.src='img/grey1.gif';
  picgr2g2= new Image(16,16);
  picgr2g2.src='img/grey2.gif';
  picgr2g3= new Image(16,16);
  picgr2g3.src='img/grey3.gif';
  picgr2g4= new Image(16,16);
  picgr2g4.src='img/grey4.gif';

  picgr3g0= new Image(16,16);
  picgr3g0.src='img/grey0.gif';
  picgr3g1= new Image(16,16);
  picgr3g1.src='img/grey1.gif';
  picgr3g2= new Image(16,16);
  picgr3g2.src='img/grey2.gif';
  picgr3g3= new Image(16,16);
  picgr3g3.src='img/grey3.gif';
  picgr3g4= new Image(16,16);
  picgr3g4.src='img/grey4.gif';

  picgr4g0= new Image(16,16);
  picgr4g0.src='img/grey0.gif';
  picgr4g1= new Image(16,16);
  picgr4g1.src='img/grey1.gif';
  picgr4g2= new Image(16,16);
  picgr4g2.src='img/grey2.gif';
  picgr4g3= new Image(16,16);
  picgr4g3.src='img/grey3.gif';
  picgr4g4= new Image(16,16);
  picgr4g4.src='img/grey4.gif';

  picgr5g0= new Image(16,16);
  picgr5g0.src='img/grey0.gif';
  picgr5g1= new Image(16,16);
  picgr5g1.src='img/grey1.gif';
  picgr5g2= new Image(16,16);
  picgr5g2.src='img/grey2.gif';
  picgr5g3= new Image(16,16);
  picgr5g3.src='img/grey3.gif';
  picgr5g4= new Image(16,16);
  picgr5g4.src='img/grey4.gif';

  picgr6g0= new Image(16,16);
  picgr6g0.src='img/grey0.gif';
  picgr6g1= new Image(16,16);
  picgr6g1.src='img/grey1.gif';
  picgr6g2= new Image(16,16);
  picgr6g2.src='img/grey2.gif';
  picgr6g3= new Image(16,16);
  picgr6g3.src='img/grey3.gif';
  picgr6g4= new Image(16,16);
  picgr6g4.src='img/grey4.gif';

  picgr7g0= new Image(16,16);
  picgr7g0.src='img/grey0.gif';
  picgr7g1= new Image(16,16);
  picgr7g1.src='img/grey1.gif';
  picgr7g2= new Image(16,16);
  picgr7g2.src='img/grey2.gif';
  picgr7g3= new Image(16,16);
  picgr7g3.src='img/grey3.gif';
  picgr7g4= new Image(16,16);
  picgr7g4.src='img/grey4.gif';

  picgr8g0= new Image(16,16);
  picgr8g0.src='img/grey0.gif';
  picgr8g1= new Image(16,16);
  picgr8g1.src='img/grey1.gif';
  picgr8g2= new Image(16,16);
  picgr8g2.src='img/grey2.gif';
  picgr8g3= new Image(16,16);
  picgr8g3.src='img/grey3.gif';
  picgr8g4= new Image(16,16);
  picgr8g4.src='img/grey4.gif';

  picgr9g0= new Image(16,16);
  picgr9g0.src='img/grey0.gif';
  picgr9g1= new Image(16,16);
  picgr9g1.src='img/grey1.gif';
  picgr9g2= new Image(16,16);
  picgr9g2.src='img/grey2.gif';
  picgr9g3= new Image(16,16);
  picgr9g3.src='img/grey3.gif';
  picgr9g4= new Image(16,16);
  picgr9g4.src='img/grey4.gif';

  picgr10g0= new Image(16,16);
  picgr10g0.src='img/grey0.gif';
  picgr10g1= new Image(16,16);
  picgr10g1.src='img/grey1.gif';
  picgr10g2= new Image(16,16);
  picgr10g2.src='img/grey2.gif';
  picgr10g3= new Image(16,16);
  picgr10g3.src='img/grey3.gif';
  picgr10g4= new Image(16,16);
  picgr10g4.src='img/grey4.gif';

  picgr11g0= new Image(16,16);
  picgr11g0.src='img/grey0.gif';
  picgr11g1= new Image(16,16);
  picgr11g1.src='img/grey1.gif';
  picgr11g2= new Image(16,16);
  picgr11g2.src='img/grey2.gif';
  picgr11g3= new Image(16,16);
  picgr11g3.src='img/grey3.gif';
  picgr11g4= new Image(16,16);
  picgr11g4.src='img/grey4.gif';

  picgr12g0= new Image(16,16);
  picgr12g0.src='img/grey0.gif';
  picgr12g1= new Image(16,16);
  picgr12g1.src='img/grey1.gif';
  picgr12g2= new Image(16,16);
  picgr12g2.src='img/grey2.gif';
  picgr12g3= new Image(16,16);
  picgr12g3.src='img/grey3.gif';
  picgr12g4= new Image(16,16);
  picgr12g4.src='img/grey4.gif';

  picgr13g0= new Image(16,16);
  picgr13g0.src='img/grey0.gif';
  picgr13g1= new Image(16,16);
  picgr13g1.src='img/grey1.gif';
  picgr13g2= new Image(16,16);
  picgr13g2.src='img/grey2.gif';
  picgr13g3= new Image(16,16);
  picgr13g3.src='img/grey3.gif';
  picgr13g4= new Image(16,16);
  picgr13g4.src='img/grey4.gif';

  picgr14g0= new Image(16,16);
  picgr14g0.src='img/grey0.gif';
  picgr14g1= new Image(16,16);
  picgr14g1.src='img/grey1.gif';
  picgr14g2= new Image(16,16);
  picgr14g2.src='img/grey2.gif';
  picgr14g3= new Image(16,16);
  picgr14g3.src='img/grey3.gif';
  picgr14g4= new Image(16,16);
  picgr14g4.src='img/grey4.gif';

  picgr15g0= new Image(16,16);
  picgr15g0.src='img/grey0.gif';
  picgr15g1= new Image(16,16);
  picgr15g1.src='img/grey1.gif';
  picgr15g2= new Image(16,16);
  picgr15g2.src='img/grey2.gif';
  picgr15g3= new Image(16,16);
  picgr15g3.src='img/grey3.gif';
  picgr15g4= new Image(16,16);
  picgr15g4.src='img/grey4.gif';
 }
</script>
</head> 
<body>
 <a name='top'></a>
 <div class='title'><div class='authors'></div></div>
 <div class='project'>
	<div class='project_shadow'>Hamming Neural Network - classifier</div>
	<div class='project_text'>Hamming Neural Network - classifier</div>
 </div>
 <div class='header'>
  <a href='http://home.agh.edu.pl/~vlsi/AI/hamming_en/index.html' class='xref-m'>Theory</a>
  </div>
 <div class='contents'>
 <!-- form start -->
 <p class='descr2'>To simulate the Hamming Network, you should enter the symbol into the left table, by clicking appropriate squares and turning them black.</p>
 <p class='descr2'>Then you should add some noise to examined symbol, by choosing it from the list in the middle. You can see the symbol with noise in the right table.</p>
 <p class='descr2'>Simulation starts when you click the <i>&#8216;Next step&#8217;</i> button. Every next click on it orders the network to do the next iteration. To start the simulation from the beginning click the <i>&#8216;Clear all&#8217;</i> button.</p>
 <p class='descr2'>If your screen resolution is set to 1024x728, it may be convenient to go into the full screen mode, by pressing &lt;F11&gt;.</p><br><hr size='1'>
 <form name='iterations' method='POST'>
 <table border='1' cellspacing='0' cellpadding='0' class='tcenter'>
  <tbody><tr>
   <td colspan='2'><p class='descr'>Input symbol<br>without noise</p></td>
   <td></td>
   <td colspan='2'><p class='descr'>Input symbol<br>with noise</p></td>
  </tr>
  <tr>
   <!-- For clicks -->
   <td>
    <table border='1' cellspacing='1' cellpadding='1'>
     <tbody><tr>";
for($i=1; $i<=15; $i++){
	echo "<td class='tcenter'><a href='javascript:wb(".$i.")' class='simple'><img src='img/white.gif' name='pic".$i."' border='0' alt='*'></a></td>";
	if($i % 3 == 0)
		echo "</tr><tr>";
}
echo "</tr>
    </tbody></table>
   </td>
   <!-- Show what you clicked -->
   <td width='56' class='tcenter' style='padding-left: 10px'>
    <table border='1' cellspacing='1' cellpadding='1'>
     <tbody><tr>";
for($i=1;$i<=15;$i++){
	echo "<td class='tnoise'><input type='text' class='inoise' name='nonoise".$i."' size='1'></td>";
	if($i % 3 == 0)
		echo "</tr><tr>";
}
echo "</tr>
    </tbody></table>
   </td>
   
   <td width='250' class='tcenter'>
     <select name='jaki_noise' onchange='generateNoise()'>
      <option value='5' selected=''>Choose noise</option>
      <option value='0'>no noise</option>
      <option value='1'>small noise (0-0,25)</option>
      <option value='2'>medium noise (0-0,33)</option>
      <option value='3'>strong noise (0-0,5)</option>
     </select><br>
   </td>
   <!-- Here you show shades of gray -->
   <td>
    <table border='1' cellspacing='1' cellpadding='1'>
     <tbody>
	 <tr>";
for($i=1; $i<=15; $i++){
	echo "<td class='tcenter'><img src='img/white.gif' name='picgr".$i."' border='0' alt='*'></td>";
	if($i % 3 == 0)
		echo "</tr><tr>";
}
echo "</tr>
    </tbody></table>
   </td>
   <td width='56' class='tcenter' style='padding-left: 10px'>
    <table border='1' cellspacing='1' cellpadding='1'>
     <tbody><tr>";

for($i=1;$i<=15;$i++){
	echo "<td class='tnoise'><input type='text' class='inoise' name='noise".$i."' size='1'></td>";
	if($i % 3 == 0)
		echo "</tr><tr>";
}
echo "</tr>
    </tbody></table>
   </td>
  </tr>
 </tbody></table>
 <p class='center'>
  <a href='javascript: void 0' onclick='maxnet()' class='button'>Next step</a>
 </p>
 <br><a name='start'><br>
  <table width='100%'>
   <tbody><tr>
    <td>
     <table border='0' cellspacing='0' cellpadding='0'>
      <tbody><tr>
       <td width='25'></td>";
for($i=1;$i<=15;$i++){
	echo "<td width='50' class='tcenter'><input name='input".dechex($i)."' size='1'></td>";
}
echo "</tr>     
      <tr>
       <td width='25' height='243'></td>";
for($i=1;$i<=15;$i++){
	echo "<td width='50' height='243' style='background: url(img/net0".dechex($i).".gif)' class='tcenter'>&nbsp;</td>";
}
echo "</tr>
     </tbody></table>
     <table border='0' cellspacing='0' cellpadding='0' width='921'>
      <tbody><tr>
       <td width='100' height='328' valign='top' style='background: url(img/net11.gif)' class='tcenter'><input type='text' name='warstwa1' size='1' value='-'></td>
       <td width='100' height='328' valign='top' style='background: url(img/net12.gif)' class='tcenter'><input type='text' name='warstwa2' size='1' value='M'></td>
       <td width='100' height='328' valign='top' style='background: url(img/net13.gif)' class='tcenter'><input type='text' name='warstwa3' size='1' value='I'></td>
       <td width='100' height='328' valign='top' style='background: url(img/net14.gif)' class='tcenter'><input type='text' name='warstwa4' size='1' value='D'></td>
       <td width='100' height='328' valign='top' style='background: url(img/net15.gif)' class='tcenter'><input type='text' name='warstwa5' size='1' value='D'></td>
       <td width='100' height='328' valign='top' style='background: url(img/net16.gif)' class='tcenter'><input type='text' name='warstwa6' size='1' value='L'></td>
       <td width='100' height='328' valign='top' style='background: url(img/net17.gif)' class='tcenter'><input type='text' name='warstwa7' size='1' value='E'></td>
       <td width='100' height='328' valign='top' style='background: url(img/net18.gif)' class='tcenter'><input type='text' name='warstwa8' size='1' value='-'></td>
       <td width='121' height='328' style='background: url(img/net19.gif)'></td>
      </tr>
      <tr>
       <td width='100' class='tcenter'><input type='text' name='output1' size='1' value='-'></td>
       <td width='100' class='tcenter'><input type='text' name='output2' size='1' value='O'></td>
       <td width='100' class='tcenter'><input type='text' name='output3' size='1' value='U'></td>
       <td width='100' class='tcenter'><input type='text' name='output4' size='1' value='T'></td>
       <td width='100' class='tcenter'><input type='text' name='output5' size='1' value='P'></td>
       <td width='100' class='tcenter'><input type='text' name='output6' size='1' value='U'></td>
       <td width='100' class='tcenter'><input type='text' name='output7' size='1' value='T'></td>
       <td width='100' class='tcenter'><input type='text' name='output8' size='1' value='-'></td>
      </tr>
     </tbody></table>
    </td>
   </tr>
  </tbody></table>
  <br>
 
 </a></form></div><a name='start'>
 </a><div class='footer0'><a name='start'>
  </a><a href='http://home.agh.edu.pl/~vlsi/AI/hamming_en/index.html' class='xref-f'>Theory</a>
 </div>
 <div class='footer'>
  <a href='http://home.agh.edu.pl/~vlsi/AI/hamming_en/sim.html#top' class='xref-f'>To the top</a>
 </div>
</body>
</html>";

?>