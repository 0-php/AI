 temp = 0;
 iterations = 0;
 input = new Array(8);
 input_str = new Array(8);
 output = new Array(8);
 output_str = new Array(8);
 char=new Array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
 char_str=new Array("","","","","","","","","","","","","","","");
 char_noise=new Array(0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0);
 char_noise_str=new Array("","","","","","","","","","","","","","","");
 noise_generated = 0;
 str = "";
 old_output = new Array(0,0,0,0,0,0,0,0);
 weight_first = new Array(15);
 nr0 = 0.28867513459481;
 nr1 = 0.44721359549996;
 nr2 = 0.30151134457776;
 nr3 = 0.31622776601684;
 nr4 = 0.33333333333333;
 nr5 = 0.30151134457776;
 nr6 = 0.28867513459481;
 nr7 = 0.37796447300923;
 weight_first[0] = new Array(nr0, nr0, nr0, nr0, 0, nr0, nr0, 0, nr0, nr0, 0, nr0, nr0, nr0, nr0);
 weight_first[1] = new Array(0, nr1, 0, 0, nr1, 0, 0, nr1, 0, 0, nr1, 0, 0, nr1, 0);
 weight_first[2] = new Array(nr2, nr2, nr2, 0, 0, nr2, nr2, nr2, nr2, nr2, 0, 0, nr2, nr2, nr2);
 weight_first[3] = new Array(nr3, nr3, nr3, 0, 0, nr3, 0, nr3, nr3, 0, 0, nr3, nr3, nr3, nr3);
 weight_first[4] = new Array(nr4, 0, nr4, nr4, 0, nr4, nr4, nr4, nr4, 0, 0, nr4, 0, 0, nr4);
 weight_first[5] = new Array(nr5, nr5, nr5, nr5, 0, 0, nr5, nr5, nr5, 0, 0, nr5, nr5, nr5, nr5);
 weight_first[6] = new Array(nr6, nr6, nr6, nr6, 0, 0, nr6, nr6, nr6, nr6, 0, nr6, nr6, nr6, nr6);
 weight_first[7] = new Array(nr7, nr7, nr7, 0, 0, nr7, 0, 0, nr7, 0, 0, nr7, 0, 0, nr7);

 if (document.images)
 {
  pic1on= new Image(16,16);
  pic1on.src="img/black.gif";  
  pic2on= new Image(16,16);
  pic2on.src="img/black.gif";
  pic3on= new Image(16,16);
  pic3on.src="img/black.gif";  
  pic4on= new Image(16,16);
  pic4on.src="img/black.gif";  
  pic5on= new Image(16,16);
  pic5on.src="img/black.gif";  
  pic6on= new Image(16,16);
  pic6on.src="img/black.gif";  
  pic7on= new Image(16,16);
  pic7on.src="img/black.gif";  
  pic8on= new Image(16,16);
  pic8on.src="img/black.gif";  
  pic9on= new Image(16,16);
  pic9on.src="img/black.gif";  
  pic10on= new Image(16,16);
  pic10on.src="img/black.gif";  
  pic11on= new Image(16,16);
  pic11on.src="img/black.gif";  
  pic12on= new Image(16,16);
  pic12on.src="img/black.gif";  
  pic13on= new Image(16,16);
  pic13on.src="img/black.gif";  
  pic14on= new Image(16,16);
  pic14on.src="img/black.gif";  
  pic15on= new Image(16,16);
  pic15on.src="img/black.gif";  

  pic1off= new Image(16,16);
  pic1off.src="img/white.gif";
  pic2off= new Image(16,16);
  pic2off.src="img/white.gif";
  pic3off= new Image(16,16);
  pic3off.src="img/white.gif";
  pic4off= new Image(16,16);
  pic4off.src="img/white.gif";
  pic5off= new Image(16,16);
  pic5off.src="img/white.gif";
  pic6off= new Image(16,16);
  pic6off.src="img/white.gif";
  pic7off= new Image(16,16);
  pic7off.src="img/white.gif";
  pic8off= new Image(16,16);
  pic8off.src="img/white.gif";
  pic9off= new Image(16,16);
  pic9off.src="img/white.gif";
  pic10off= new Image(16,16);
  pic10off.src="img/white.gif";
  pic11off= new Image(16,16);
  pic11off.src="img/white.gif";
  pic12off= new Image(16,16);
  pic12off.src="img/white.gif";
  pic13off= new Image(16,16);
  pic13off.src="img/white.gif";
  pic14off= new Image(16,16);
  pic14off.src="img/white.gif";
  pic15off= new Image(16,16);
  pic15off.src="img/white.gif";
  // tu sie zaczyna...
  picgr1g0= new Image(16,16);
  picgr1g0.src="img/grey0.gif";
  picgr1g1= new Image(16,16);
  picgr1g1.src="img/grey1.gif";
  picgr1g2= new Image(16,16);
  picgr1g2.src="img/grey2.gif";
  picgr1g3= new Image(16,16);
  picgr1g3.src="img/grey3.gif";
  picgr1g4= new Image(16,16);
  picgr1g4.src="img/grey4.gif";

  picgr2g0= new Image(16,16);
  picgr2g0.src="img/grey0.gif";
  picgr2g1= new Image(16,16);
  picgr2g1.src="img/grey1.gif";
  picgr2g2= new Image(16,16);
  picgr2g2.src="img/grey2.gif";
  picgr2g3= new Image(16,16);
  picgr2g3.src="img/grey3.gif";
  picgr2g4= new Image(16,16);
  picgr2g4.src="img/grey4.gif";

  picgr3g0= new Image(16,16);
  picgr3g0.src="img/grey0.gif";
  picgr3g1= new Image(16,16);
  picgr3g1.src="img/grey1.gif";
  picgr3g2= new Image(16,16);
  picgr3g2.src="img/grey2.gif";
  picgr3g3= new Image(16,16);
  picgr3g3.src="img/grey3.gif";
  picgr3g4= new Image(16,16);
  picgr3g4.src="img/grey4.gif";

  picgr4g0= new Image(16,16);
  picgr4g0.src="img/grey0.gif";
  picgr4g1= new Image(16,16);
  picgr4g1.src="img/grey1.gif";
  picgr4g2= new Image(16,16);
  picgr4g2.src="img/grey2.gif";
  picgr4g3= new Image(16,16);
  picgr4g3.src="img/grey3.gif";
  picgr4g4= new Image(16,16);
  picgr4g4.src="img/grey4.gif";

  picgr5g0= new Image(16,16);
  picgr5g0.src="img/grey0.gif";
  picgr5g1= new Image(16,16);
  picgr5g1.src="img/grey1.gif";
  picgr5g2= new Image(16,16);
  picgr5g2.src="img/grey2.gif";
  picgr5g3= new Image(16,16);
  picgr5g3.src="img/grey3.gif";
  picgr5g4= new Image(16,16);
  picgr5g4.src="img/grey4.gif";

  picgr6g0= new Image(16,16);
  picgr6g0.src="img/grey0.gif";
  picgr6g1= new Image(16,16);
  picgr6g1.src="img/grey1.gif";
  picgr6g2= new Image(16,16);
  picgr6g2.src="img/grey2.gif";
  picgr6g3= new Image(16,16);
  picgr6g3.src="img/grey3.gif";
  picgr6g4= new Image(16,16);
  picgr6g4.src="img/grey4.gif";

  picgr7g0= new Image(16,16);
  picgr7g0.src="img/grey0.gif";
  picgr7g1= new Image(16,16);
  picgr7g1.src="img/grey1.gif";
  picgr7g2= new Image(16,16);
  picgr7g2.src="img/grey2.gif";
  picgr7g3= new Image(16,16);
  picgr7g3.src="img/grey3.gif";
  picgr7g4= new Image(16,16);
  picgr7g4.src="img/grey4.gif";

  picgr8g0= new Image(16,16);
  picgr8g0.src="img/grey0.gif";
  picgr8g1= new Image(16,16);
  picgr8g1.src="img/grey1.gif";
  picgr8g2= new Image(16,16);
  picgr8g2.src="img/grey2.gif";
  picgr8g3= new Image(16,16);
  picgr8g3.src="img/grey3.gif";
  picgr8g4= new Image(16,16);
  picgr8g4.src="img/grey4.gif";

  picgr9g0= new Image(16,16);
  picgr9g0.src="img/grey0.gif";
  picgr9g1= new Image(16,16);
  picgr9g1.src="img/grey1.gif";
  picgr9g2= new Image(16,16);
  picgr9g2.src="img/grey2.gif";
  picgr9g3= new Image(16,16);
  picgr9g3.src="img/grey3.gif";
  picgr9g4= new Image(16,16);
  picgr9g4.src="img/grey4.gif";

  picgr10g0= new Image(16,16);
  picgr10g0.src="img/grey0.gif";
  picgr10g1= new Image(16,16);
  picgr10g1.src="img/grey1.gif";
  picgr10g2= new Image(16,16);
  picgr10g2.src="img/grey2.gif";
  picgr10g3= new Image(16,16);
  picgr10g3.src="img/grey3.gif";
  picgr10g4= new Image(16,16);
  picgr10g4.src="img/grey4.gif";

  picgr11g0= new Image(16,16);
  picgr11g0.src="img/grey0.gif";
  picgr11g1= new Image(16,16);
  picgr11g1.src="img/grey1.gif";
  picgr11g2= new Image(16,16);
  picgr11g2.src="img/grey2.gif";
  picgr11g3= new Image(16,16);
  picgr11g3.src="img/grey3.gif";
  picgr11g4= new Image(16,16);
  picgr11g4.src="img/grey4.gif";

  picgr12g0= new Image(16,16);
  picgr12g0.src="img/grey0.gif";
  picgr12g1= new Image(16,16);
  picgr12g1.src="img/grey1.gif";
  picgr12g2= new Image(16,16);
  picgr12g2.src="img/grey2.gif";
  picgr12g3= new Image(16,16);
  picgr12g3.src="img/grey3.gif";
  picgr12g4= new Image(16,16);
  picgr12g4.src="img/grey4.gif";

  picgr13g0= new Image(16,16);
  picgr13g0.src="img/grey0.gif";
  picgr13g1= new Image(16,16);
  picgr13g1.src="img/grey1.gif";
  picgr13g2= new Image(16,16);
  picgr13g2.src="img/grey2.gif";
  picgr13g3= new Image(16,16);
  picgr13g3.src="img/grey3.gif";
  picgr13g4= new Image(16,16);
  picgr13g4.src="img/grey4.gif";

  picgr14g0= new Image(16,16);
  picgr14g0.src="img/grey0.gif";
  picgr14g1= new Image(16,16);
  picgr14g1.src="img/grey1.gif";
  picgr14g2= new Image(16,16);
  picgr14g2.src="img/grey2.gif";
  picgr14g3= new Image(16,16);
  picgr14g3.src="img/grey3.gif";
  picgr14g4= new Image(16,16);
  picgr14g4.src="img/grey4.gif";

  picgr15g0= new Image(16,16);
  picgr15g0.src="img/grey0.gif";
  picgr15g1= new Image(16,16);
  picgr15g1.src="img/grey1.gif";
  picgr15g2= new Image(16,16);
  picgr15g2.src="img/grey2.gif";
  picgr15g3= new Image(16,16);
  picgr15g3.src="img/grey3.gif";
  picgr15g4= new Image(16,16);
  picgr15g4.src="img/grey4.gif";
 }

/* =================================================================================== */
function standards_input()
{
 squares_sum = 0.0;
 for(i=0; i<15; i++)
 {
  squares_sum = squares_sum + input[i]*input[i];
 }
 for(i=0; i<15; i++)
 {
  input[i] = Math.sqrt((input[i]*input[i]/squares_sum));
 }
}

/* =================================================================================== */
function standards_output()
{
 squares_sum = 0.0;
 for(i=0; i<8; i++)
 {
  squares_sum = squares_sum + output[i]*output[i];
 }
 for(i=0; i<8; i++)
 {
  output[i] = Math.sqrt((output[i]*output[i])/squares_sum);
 }
}

/* =================================================================================== */
function maxnet()
{
 if (noise_generated == 1) {
  if (iterations == 0)
  {
   input = char_noise;
   standards_input();
   for(i=0; i<8; i++) {
    output[i] = 0;
    for(j=0; j<15; j++) {
     output[i] = output[i] + input[j]*weight_first[i][j];
    }
    for(k=0; k<15; k++) {
     str = input[k] + " ";
     if (str == "0 ") str = "0.00";
     if (str == "1 ") str = "1.00";
     str = str.slice(0,4);
     input_str[k] = str;
    }
    document.forms.iterations.input1.value=input_str[0];
    document.forms.iterations.input2.value=input_str[1];
    document.forms.iterations.input3.value=input_str[2];
    document.forms.iterations.input4.value=input_str[3];
    document.forms.iterations.input5.value=input_str[4];
    document.forms.iterations.input6.value=input_str[5];
    document.forms.iterations.input7.value=input_str[6];
    document.forms.iterations.input8.value=input_str[7];
    document.forms.iterations.input9.value=input_str[8];
    document.forms.iterations.inputa.value=input_str[9];
    document.forms.iterations.inputb.value=input_str[10];
    document.forms.iterations.inputc.value=input_str[11];
    document.forms.iterations.inputd.value=input_str[12];
    document.forms.iterations.inpute.value=input_str[13];
    document.forms.iterations.inputf.value=input_str[14];
   }
   standards_output();
   for(i=0; i<8; i++)
   {
    if (output[i]<0) output[i] = 0;
   }
   for(k=0; k<8; k++)
   {
    str = output[k] + " ";
    str = str.slice(0,4);
    output_str[k] = str;
   }
   document.forms.iterations.warstwa1.value=output_str[0];
   document.forms.iterations.warstwa2.value=output_str[1];
   document.forms.iterations.warstwa3.value=output_str[2];
   document.forms.iterations.warstwa4.value=output_str[3];
   document.forms.iterations.warstwa5.value=output_str[4];
   document.forms.iterations.warstwa6.value=output_str[5];
   document.forms.iterations.warstwa7.value=output_str[6];
   document.forms.iterations.warstwa8.value=output_str[7];
   old_output = output;
  }
  else
  {
   suma = (old_output[0]+ old_output[1]+ old_output[2]+ old_output[3]+ old_output[4]+ old_output[5]+ old_output[6]+ old_output[7]);
   output[0] = suma/(-8)+old_output[0];
   output[1] = suma/(-8)+old_output[1];
   output[2] = suma/(-8)+old_output[2];
   output[3] = suma/(-8)+old_output[3];
   output[4] = suma/(-8)+old_output[4];
   output[5] = suma/(-8)+old_output[5];
   output[6] = suma/(-8)+old_output[6];
   output[7] = suma/(-8)+old_output[7];
   for(i=0; i<8; i++)
   {
    if (output[i]<0.05) output[i] = 0;
    if (output[i]>0.96) output[i] = 1;
   }
   standards_output();
   old_output = output;
  }
  for(i=0; i<8; i++)
  {
   str = output[i] + " ";
   str = str.slice(0,4);
   output_str[i] = str;
  }
  document.forms.iterations.wyjscie1.value=output_str[0];
  document.forms.iterations.wyjscie2.value=output_str[1];
  document.forms.iterations.wyjscie3.value=output_str[2];
  document.forms.iterations.wyjscie4.value=output_str[3];
  document.forms.iterations.wyjscie5.value=output_str[4];
  document.forms.iterations.wyjscie6.value=output_str[5];
  document.forms.iterations.wyjscie7.value=output_str[6];
  document.forms.iterations.wyjscie8.value=output_str[7];
  if (iterations == 1) {
   document.forms.iterations.warstwa1.value=0;
   document.forms.iterations.warstwa2.value=0;
   document.forms.iterations.warstwa3.value=0;
   document.forms.iterations.warstwa4.value=0;
   document.forms.iterations.warstwa5.value=0;
   document.forms.iterations.warstwa6.value=0;
   document.forms.iterations.warstwa7.value=0;
   document.forms.iterations.warstwa8.value=0;
  }
  checkResult();
  iterations++;
 }
 else
 {
  alert("You should choose a noise first.");
 }
}
/* =================================================================================== */
function wb(imgNumber)
{
 if (iterations == 0) {
  if (document.images) {
   if (char[imgNumber-1]==0)  {
    imgOn=eval("pic"+imgNumber+"on.src");
    document["pic"+imgNumber].src=imgOn;
    char[imgNumber-1]=1;
   }
   else {
    imgOff=eval("pic"+imgNumber+"off.src");
    document["pic"+imgNumber].src=imgOff;
    char[imgNumber-1]=0;
   }
  }
  for(i=0; i<15; i++)
  {
   str = char[i] + " ";
   if (str == "0 ") str = "0.00";
   if (str == "1 ") str = "1.00";
   str = str.slice(0,4);
   char_str[i] = str;
  }
  document.forms.iterations.nonoise1.value=char_str[0];
  document.forms.iterations.nonoise2.value=char_str[1];
  document.forms.iterations.nonoise3.value=char_str[2];
  document.forms.iterations.nonoise4.value=char_str[3];
  document.forms.iterations.nonoise5.value=char_str[4];
  document.forms.iterations.nonoise6.value=char_str[5];
  document.forms.iterations.nonoise7.value=char_str[6];
  document.forms.iterations.nonoise8.value=char_str[7];
  document.forms.iterations.nonoise9.value=char_str[8];
  document.forms.iterations.nonoise10.value=char_str[9];
  document.forms.iterations.nonoise11.value=char_str[10];
  document.forms.iterations.nonoise12.value=char_str[11];
  document.forms.iterations.nonoise13.value=char_str[12];
  document.forms.iterations.nonoise14.value=char_str[13];
  document.forms.iterations.nonoise15.value=char_str[14];  
 }
}
/* =================================================================================== */
function start(){
 iterations = 0;
 char=(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
 output = (0,0,0,0,0,0,0,0);
}
/* =================================================================================== */
function clearValues() {
// document.iterations.reset();
 start();
 return true;
}
/* =================================================================================== */
function generateNoise() {
 if (iterations == 0) {
  for(i=0; i<15; i++)
  {
   switch (document.forms.iterations.jaki_noise.value) {
    case "0": char_noise[i] = char[i]; break;
    case "1": if (char[i] == 0) char_noise[i] = char[i] + Math.random() / 4; else char_noise[i] = char[i] - Math.random() / 4; break;
    case "2": if (char[i] == 0) char_noise[i] = char[i] + Math.random() / 3; else char_noise[i] = char[i] - Math.random() / 3; break;
    case "3": if (char[i] == 0) char_noise[i] = char[i] + Math.random() / 2; else char_noise[i] = char[i] - Math.random() / 2; break;
   }
   str = char_noise[i] + " ";
   if (str == "0 ") str = "0.00";
   if (str == "1 ") str = "1.00";
   str = str.slice(0,4);
   char_noise_str[i] = str;
  }
  document.forms.iterations.noise1.value=char_noise_str[0];
  document.forms.iterations.noise2.value=char_noise_str[1];
  document.forms.iterations.noise3.value=char_noise_str[2];
  document.forms.iterations.noise4.value=char_noise_str[3];
  document.forms.iterations.noise5.value=char_noise_str[4];
  document.forms.iterations.noise6.value=char_noise_str[5];
  document.forms.iterations.noise7.value=char_noise_str[6];
  document.forms.iterations.noise8.value=char_noise_str[7];
  document.forms.iterations.noise9.value=char_noise_str[8];
  document.forms.iterations.noise10.value=char_noise_str[9];
  document.forms.iterations.noise11.value=char_noise_str[10];
  document.forms.iterations.noise12.value=char_noise_str[11];
  document.forms.iterations.noise13.value=char_noise_str[12];
  document.forms.iterations.noise14.value=char_noise_str[13];
  document.forms.iterations.noise15.value=char_noise_str[14];
 }
 // odcienie szarosci

 if (iterations == 0) {
  for (img=1; img<16; img++) {
   if (document.images) {
    if (char_noise[img-1]<0.125)  {
     imgTmp=eval("picgr"+img+"g0.src");
     document["picgr"+img].src=imgTmp;
    }
    else if (char_noise[img-1]<0.375){
     imgTmp=eval("picgr"+img+"g1.src");
     document["picgr"+img].src=imgTmp;
    }
    else if (char_noise[img-1]<0.625){
     imgTmp=eval("picgr"+img+"g2.src");
     document["picgr"+img].src=imgTmp;
    }
    else if (char_noise[img-1]<0.875){
     imgTmp=eval("picgr"+img+"g3.src");
     document["picgr"+img].src=imgTmp;
    }
    else {
     imgTmp=eval("picgr"+img+"g4.src");
     document["picgr"+img].src=imgTmp;
    }
   }
  }
 }
 noise_generated = 1;
}
/* =================================================================================== */
function checkResult() {
 nok = 0;
 sum = 0;
 for (i=0; i<8; i++) {
  if (!((output[i] == 0) || (output[i] == 1))) nok++;
  if (output[i] == 1) sum++;
 }
 if ((nok == 0) && (sum == 1)) {
  if (output[0] == 1) alert("The Network's answer is: 0");
  if (output[1] == 1) alert("The Network's answer is: 1");
  if (output[2] == 1) alert("The Network's answer is: 2");
  if (output[3] == 1) alert("The Network's answer is: 3");
  if (output[4] == 1) alert("The Network's answer is: 4");
  if (output[5] == 1) alert("The Network's answer is: 5");
  if (output[6] == 1) alert("The Network's answer is: 6");
  if (output[7] == 1) alert("The Network's answer is: 7");
 }
}
