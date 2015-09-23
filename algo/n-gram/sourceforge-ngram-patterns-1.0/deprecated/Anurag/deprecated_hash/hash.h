#ifndef _HASH_H
#define _HASH_H

#include<stdio.h>
#include<stdlib.h>
#include<string.h>
#include"network.h"

int *final_arr_1;
int *final_arr_2;
int *final_arr_3;
int *final_arr_4;
int  flag[94],
     flag1[94],
     flag2[94],
     flag3[94],
     flag4[94];



void init_hash_arr(void);
int find_index(char *, node **);
void build_hash_arrays(char *, int i);

#endif
