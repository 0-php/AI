x = ('var_a=Math,b=a.random,c=document.g'+
'etElementById("c"),e=c.getContext("2d")'+
',f=[],g=b()*		 16777216<<0,h,i'+
',j,k,l=(g		  &16711680)>>16'+
',m=(g&65		    280)>>8,n=g&'+
'255,o,p		    ;c.width=320'+
';c.height=320;		    e.shadowBlur'+
'=10;for /* */	      (p=   0;p<64;p++)f'+
'.push(b(		    )*16777216<<'+
'0);func		    tion_q(d){fo'+
'r(d=d.t oStr		  ing(16);d.leng'+
'th<6;)			d = " 0"+d;retur'+
'n"#"+		      d } d ocument.body'+
'.sty		     l e .b a c kground='+
'q(g);		    setInterval(function'+
'(){e.		 c l e a r R e c t ( 0 ,'+
'0,320,		3 2 0 ) ; f o r ( p = 0 '+
';p<64;	    p + + ) { h = f [ p ] ; i = '+
'l-((h&	  1 6 7 1 1 6 8 0 ) > > 1 6 ) ;j'+
"=m-((h&65 2 8 0 ) > > 8 ) ; k = n - ( h"+
'&255);o=a.sqrt(i*i+j*j+k*k)/443;if(b()<'+
"o)h=f[p]=(f[b()*64<<0]&16773120|f[b()*6"+
'4<<0]&4095)^1<<(b()*24<<0);e.fillStyle='+
"q(h);e.fillRect(1+p%8<<5,1+p/8<<5,32,32"+
')}},100);e.shadowColor="#000";').replace(
/\s/g,"").replace(/_/g,' ');eval(x);
/*evolution in 999b by 29a.ch**/
