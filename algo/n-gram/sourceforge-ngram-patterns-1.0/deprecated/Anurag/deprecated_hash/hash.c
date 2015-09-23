#include "hash.h"

void init_hash_arr(void)
{
int j;
final_arr_1 = (int*)calloc(94, sizeof(int));
final_arr_2 = (int*)calloc(8836, sizeof(int));
final_arr_3 = (int*)calloc(830584, sizeof(int));
final_arr_4 = (int*)calloc(78074896, sizeof(int));


for(j=0; j<94; j++)
{

 flag[j] = 0;
 flag1[j]=0;
 flag2[j] = 0;
 flag3[j] = 0;
 flag4[j] = 0;
}

}



void build_hash_arrays(char *strx, int i)
{
//printf("%s\n", strx);


static int  a = 0,
    b = 0,
    c = 0,
    d = 0,
    e = 0,
    f = 0,
    g = 0,
    h = 0,
    count1 = 0,
    count2 = 0,
    count3 = 0,
    count4 = 0,
    count5 = 0;
    int j = 0, k = 0, l = 0, m = 0, n = 0, z = 0;


 for(j = 0; j<=93; j++)
 {
  if((int)strx[0] == (j+33))
  {
   if(flag[j] == 0)
   {
    final_arr_1[j]=i;
//    printf("\n\n\nStarting Point for %c at %d location is %d", (char)(j+33), flag[j]+1, i);
    a = j + 33;
    b = i;
    count1++;
    flag[j] = 1;
    for(z=0; z<94; z++)
    {
     flag1[z]=0;
    }
   }
   else
   {
    for(k = 0; k<=93; k++)
    {
     if((int)strx[1] == (k+33))
     {
      if(flag1[k] == 0)
      {
       final_arr_2[94*j + k]= i-b;
//       printf("\nOffset for %c at %d location with %c as first letter is %d", (char)(k+33), flag1[k]+2,(char)(a), i-b);
       count2++;
       c = k + 33;
       d = i;
       flag1[k] = 1;
       for(z=0; z<94; z++)
       {
        flag2[z]=0;
       }
      }
      else
      {
       for(l = 0; l<=93; l++)
       {
        if((int)strx[2] == (l+33))
        {
         if(flag2[l] == 0)
         {
          final_arr_3[94*(94*j + k)+l] = i-d;
//         printf("\nOffset for %c at %d location with %c as first letter and %c as second letter is %d",(char)(l+33), flag2[l]+3, (char)(a), (char)(c), i-d);

          count3++;
          e=l+33;
          f=i;
          flag2[l] = 1;
          for(z=0; z<94; z++)
          {
           flag3[z]=0;
          }
         }
         else
         {
          for(m=0; m<=93; m++)
          {
           if((int)strx[3] == (m+33))
           {
            if(flag3[m] == 0)
            {
             final_arr_4[94*(94*(94*j + k)+l) + m] = i-f;
  //           printf("\nOffset for %c at %d location with %c as first letter and %c as second letter and %c as third letter is %d", (char)(m+33), flag3[m]+4, (char)(a), (char)(c), (char)(e), i-f);
             count4++;
             flag3[m] = 1;
            }
           }
          }
         }
        }
       }
      }
     }
    }
   }
  }
 }

}


















int find_index(char *str, node **vocab_array)
{
static int i, j, start;
static int a1 = 0, b1 = 0, c1 = 0, d1 = 0;
static int a2 = 0, b2 = 0, c2 = 0, d2 = 0;

/*
//printf("%s %d %d %d %d\n", str, str[0], str[1], str[2], str[3]);
for(i=0; i<100; i++)
{
	printf("%s\n", vocab_array[i]->token);
}
exit(1);
*/

a1 = (int)str[0];
a2 = a1 - 33;
if(a1 >=33 && a1 <= 126)
{
 if(!strcmp(str,vocab_array[final_arr_1[a2]]->token))
        return final_arr_1[a2];
 else
 {
  b1 = (int)str[1];
  b2 = 94*a2 + b1 - 33;
  if(b1 >= 33 && b1 <= 126)
  {
   if(!strcmp(str,vocab_array[final_arr_1[a2]+final_arr_2[b2]]->token))
        return (final_arr_1[a2]+final_arr_2[b2]);
   else
   {
    c1 = (int)str[2];
    c2 = 94*b2 + c1 - 33;
    if(c1 >=33 && c1 <= 126)
    {
     if(!strcmp(str,vocab_array[final_arr_1[a2]+final_arr_2[b2]+final_arr_3[c2]]->token))
                return (final_arr_1[a2]+final_arr_2[b2]+final_arr_3[c2]);
     else
     {
      d1 = (int)str[3];
      d2 = 94*c2 + d1 - 33;
      if(d1 >=33 && d1 <= 126)
      {
       if(!strcmp(str,vocab_array[final_arr_1[a2]+final_arr_2[b2]+final_arr_3[c2]+final_arr_4[d2]]->token))
                return (final_arr_1[a2]+final_arr_2[b2]+final_arr_3[c2]+final_arr_4[d2]);
       else
       {
        start = final_arr_1[a2]+final_arr_2[b2]+final_arr_3[c2]+final_arr_4[d2];
        for(i=start; i<13588391; i++)
        {
         if(!strcmp(str,vocab_array[i]->token))
           return i;
        }
       }
      }
      else
      {
       start = final_arr_1[a2]+final_arr_2[b2]+final_arr_3[c2];
       for(i=start; i<13588391; i++)
       {
                if(!strcmp(str,vocab_array[i]->token))
                        return i;
       }

      }
     }
    }
    else
    {
     start = final_arr_1[a2]+final_arr_2[b2];
     for(i=start; i<13588391; i++)
     {
     if(!strcmp(str,vocab_array[i]->token))
       return i;
     }
    }
   }
  }
  else
  {
   start = final_arr_1[a2];
   for(i=start; i<13588391; i++)
   {
      if(!strcmp(str,vocab_array[i]->token))
         return i;
   }
  }
 }
}
else
{
        start = 13565324;
        for(i=start; i<13588391; i++)
        {
                if(!strcmp(str,vocab_array[i]->token))
                        return i;
        }
}
}

