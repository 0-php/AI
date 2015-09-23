//**********************************************************************************************

/* Copyright (C) 2007 Flamengo team.
   This file is part of the Fun with N-gram-patterns.
         
   N-gram-patterns is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   N-gram-patterns is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program. If not, see <http://www.gnu.org/licenses/>.

*/

/*==========================================print_path.h============================================
Aurthor: Anurag Jain
Course: CS 8621
Time: Fall 2007
================================================================================================*/

//=========================================Include Files=========================================
#include"network.h"
#include"mpi.h"
#ifndef PRINT_PATH_H
#define PRINT_PATH_H
//=========================================Global Variables=======================================

// Length till which the edges need to be printed
static int lengthx; 
// Length of the path incoming into the queried token
static int length1=-1; 
// Length of the path outgoing from the queried token
static int length2=-1;	
// Index of the queried token in the vocab array
static int search_index; 
// Records the status of message passing
static MPI_Status status;
//=====================================Function Declarations========================================
/* Note: Function print_length_1 makes a call to the function print_length_2 which helps us print the incoming 
		 nodes followed by the outgoing nodes with the queried token in-between.*/		 

// Prints the incoming as well as the outgoing nodes for the queried token  
void print_query(node **, char*, int, int, int, double); 

// Prints the incoming nodes and frequencies into the queried token 
void print_length_1(int, int, char*, double); 

// Prints the nodes nodes and frequencies into the queried token
void print_length_2(int, int, char*, double); 

// Finds out the number of lines in the input file
int getInputFileLength(FILE *);

// Collects incoming nodes from processors
void collect_incoming_nodes(int );

// Collects outgoing nodes from processors
void collect_outgoing_nodes(int );

// Processor id
static int id;

// Number Of Processors
static int p;

// Word Pointer
static node** word;

// Unigram Size
static int uSize;

// Associative Cut
static double associativeCut;
//****************************************************************************************************
#endif
