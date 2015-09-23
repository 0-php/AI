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
 old_output = new Array();
 weight_first = new Array();
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
  document.forms.iterations.output1.value=output_str[0];
  document.forms.iterations.output2.value=output_str[1];
  document.forms.iterations.output3.value=output_str[2];
  document.forms.iterations.output4.value=output_str[3];
  document.forms.iterations.output5.value=output_str[4];
  document.forms.iterations.output6.value=output_str[5];
  document.forms.iterations.output7.value=output_str[6];
  document.forms.iterations.output8.value=output_str[7];
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
  console.info(output);
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
